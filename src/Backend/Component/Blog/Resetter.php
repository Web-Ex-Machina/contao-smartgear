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

use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\FilesModel;
use Contao\ModuleModel;
use Contao\NewsArchiveModel;
use Contao\NewsModel;
use Contao\PageModel;
use Contao\UserGroupModel;
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
                if ($objFolder) {
                    $objFolder->renameTo(sprintf('files/archives/news-%s', (string) $archiveTimestamp));
                }

                $objNewsArchive = NewsArchiveModel::findById($blogConfig->getSgNewsArchive());
                if ($objNewsArchive) {
                    $objNewsArchive->title = sprintf('%s (Archive-%s)', $objNewsArchive->title, (string) $archiveTimestamp);
                    $objNewsArchive->save();
                }

                $objPage = PageModel::findById($blogConfig->getSgPage());
                if ($objPage) {
                    $objPage->published = false;
                    $objPage->title = sprintf('%s (Archive-%s)', $objPage->title, (string) $archiveTimestamp);
                    $objPage->save();
                }

                foreach ($blogConfig->getContaoArticlesIds() as $id) {
                    $objArticle = ArticleModel::findByPk($id);
                    if ($objArticle) {
                        $objArticle->published = false;
                        $objArticle->title = sprintf('%s (Archive-%s)', $objArticle->title, (string) $archiveTimestamp);
                        $objArticle->save();
                    }
                }

                foreach ($blogConfig->getContaoModulesIds() as $id) {
                    $objModule = ModuleModel::findByPk($id);
                    if ($objModule) {
                        $objModule->published = false;
                        $objModule->title = sprintf('%s (Archive-%s)', $objModule->title, (string) $archiveTimestamp);
                        $objModule->save();
                    }
                }
            break;
            case BlogConfig::ARCHIVE_MODE_KEEP:
            break;
            case BlogConfig::ARCHIVE_MODE_DELETE:
                $objFolder = new \Contao\Folder($presetConfig->getSgNewsFolder());
                if ($objFolder) {
                    $objFolder->delete();
                }

                $news = NewsModel::findByPid($blogConfig->getSgNewsArchive());
                if ($news) {
                    while ($news->next()) {
                        $news->delete();
                    }
                }
                $objNewsArchive = NewsArchiveModel::findById($blogConfig->getSgNewsArchive());
                if ($objNewsArchive) {
                    $objNewsArchive->delete();
                }

                $objPage = PageModel::findById($blogConfig->getSgPage());
                if ($objPage) {
                    $objPage->delete();
                }

                foreach ($blogConfig->getContaoArticlesIds() as $id) {
                    $objArticle = ArticleModel::findByPk($id);
                    if ($objArticle) {
                        $objArticle->delete();
                    }
                }

                foreach ($blogConfig->getContaoContentsIds() as $id) {
                    $objContent = ContentModel::findByPk($id);
                    if ($objContent) {
                        $objContent->delete();
                    }
                }

                foreach ($blogConfig->getContaoModulesIds() as $id) {
                    $objModule = ModuleModel::findByPk($id);
                    if ($objModule) {
                        $objModule->delete();
                    }
                }
            break;
            default:
                throw new \InvalidArgumentException($this->translator->trans('WEMSG.BLOG.RESET.deleteModeUnknown', [], 'contao_default'));
            break;
        }

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
        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);
        $userGroupManipulator
            ->removeSmartgearPermissions([SmartgearPermissions::BLOG_EXPERT])
            // ->removeAllowedModules(['news'])
            ->removeAllowedNewsArchive([$blogConfig->getSgNewsArchive()])
            ->removeAllowedFieldsByPrefixes(['tl_news::'])
            ->removeAllowedPagemounts($blogConfig->getContaoPagesIds())
            ->removeAllowedModules(Module::getTypesByIds($blogConfig->getContaoModulesIds()))
        ;

        $objFolder = FilesModel::findByPath($blogConfig->getCurrentPreset()->getSgNewsFolder());
        if ($objFolder) {
            $userGroupManipulator->removeAllowedFilemounts([$objFolder->uuid]);
        }

        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->newp = null;
        $objUserGroup->save();
    }
}
