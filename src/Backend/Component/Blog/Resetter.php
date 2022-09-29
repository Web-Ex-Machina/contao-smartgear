<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2022 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

namespace WEM\SmartgearBundle\Backend\Component\Blog;

use Contao\FilesModel;
use Contao\NewsArchiveModel;
use Contao\PageModel;
use Contao\UserGroupModel;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Backend\Resetter as BackendResetter;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\UserGroupModelUtil;
use WEM\SmartgearBundle\Config\Component\Blog\Blog as BlogConfig;
use WEM\SmartgearBundle\Model\Module;
use WEM\SmartgearBundle\Security\SmartgearPermissions;

class Resetter extends BackendResetter
{
    /** @var string */
    protected $module = '';
    /** @var string */
    protected $type = '';
    /** @var ConfigurationManager */
    protected $configurationManager;
    /** @var TranslatorInterface */
    protected $translator;

    /**
     * Generic array of logs.
     *
     * @var array
     */
    protected $logs = [];

    public function __construct(
        ConfigurationManager $configurationManager,
        TranslatorInterface $translator,
        string $module,
        string $type
    ) {
        parent::__construct($configurationManager, $translator, $module, $type);
    }

    public function reset(string $mode): void
    {
        $this->resetUserGroupSettings();
        // reset everything except what we wanted to keep
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var BlogConfig */
        $blogConfig = $config->getSgBlog();
        /** @var BlogPresetConfig */
        $presetConfig = $blogConfig->getCurrentPreset();
        $archiveTimestamp = time();

        switch ($mode) {
            case BlogConfig::ARCHIVE_MODE_ARCHIVE:
                $objFolder = new \Contao\Folder($presetConfig->getSgNewsFolder());
                $objNewsArchive = NewsArchiveModel::findById($blogConfig->getSgNewsArchive());

                $objFolder->renameTo(sprintf('files/archives/news-%s', (string) $archiveTimestamp));
                $objNewsArchive->title = sprintf('%s (Archive-%s)', $objNewsArchive->title, (string) $archiveTimestamp);
                $objNewsArchive->save();

            break;
            case BlogConfig::ARCHIVE_MODE_KEEP:
            break;
            case BlogConfig::ARCHIVE_MODE_DELETE:
                $objFolder = new \Contao\Folder($presetConfig->getSgNewsFolder());
                $objNewsArchive = NewsArchiveModel::findById($blogConfig->getSgNewsArchive());

                $objFolder->delete();
                $objNewsArchive->delete();
            break;
            default:
                throw new \InvalidArgumentException($this->translator->trans('WEMSG.BLOG.RESET.deleteModeUnknown', [], 'contao_default'));
            break;
        }

        $objPage = PageModel::findById($blogConfig->getSgPage());
        $objPage->published = false;
        $objPage->save();

        $blogConfig->setSgArchived(true)
            ->setSgArchivedMode($mode)
            ->setSgArchivedAt($archiveTimestamp)
        ;

        $config->setSgBlog($blogConfig);

        $this->configurationManager->save($config);
    }

    protected function resetUserGroupSettings(): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var BlogConfig */
        $blogConfig = $config->getSgBlog();

        $this->resetUserGroup(UserGroupModel::findOneById($config->getSgUserGroupWebmasters()), $blogConfig);
        $this->resetUserGroup(UserGroupModel::findOneById($config->getSgUserGroupAdministrators()), $blogConfig);
    }

    protected function resetUserGroup(UserGroupModel $objUserGroup, BlogConfig $blogConfig): void
    {
        $objFolder = FilesModel::findByPath($blogConfig->getCurrentPreset()->getSgNewsFolder());
        if (!$objFolder) {
            throw new Exception('Unable to find the folder');
        }

        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);
        $userGroupManipulator
            ->removeSmartgearPermissions([SmartgearPermissions::BLOG_EXPERT])
            // ->removeAllowedModules(['news'])
            ->removeAllowedNewsArchive([$blogConfig->getSgNewsArchive()])
            ->removeAllowedFilemounts([$objFolder->uuid])
            ->removeAllowedFieldsByPrefixes(['tl_news::'])
            ->removeAllowedPagemounts($blogConfig->getContaoPagesIds())
            ->removeAllowedModules(Module::getTypesByIds($blogConfig->getContaoModulesIds()))
        ;

        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->newp = null;
        $objUserGroup->save();
    }
}
