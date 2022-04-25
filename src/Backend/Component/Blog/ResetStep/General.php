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

namespace WEM\SmartgearBundle\Backend\Component\Blog\ResetStep;

use Contao\FilesModel;
use Contao\Input;
use Contao\NewsArchiveModel;
use Contao\PageModel;
use Contao\UserGroupModel;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Backend\AbstractStep;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\UserGroupModelUtil;
use WEM\SmartgearBundle\Config\Component\Blog\Blog as BlogConfig;
use WEM\SmartgearBundle\Config\Component\Blog\Preset as BlogPresetConfig;
use WEM\SmartgearBundle\Config\Core as CoreConfig;
use WEM\SmartgearBundle\Security\SmartgearPermissions;

class General extends AbstractStep
{
    /** @var ConfigurationManager */
    protected $configurationManager;

    protected $strTemplate = 'be_wem_sg_install_block_reset_step_blog_general';

    public function __construct(
        string $module,
        string $type,
        TranslatorInterface $translator,
        ConfigurationManager $configurationManager
    ) {
        parent::__construct($module, $type);
        $this->translator = $translator;
        $this->configurationManager = $configurationManager;

        $this->title = $this->translator->trans('WEMSG.BLOG.RESET.title', [], 'contao_default');

        $resetOptions = [
            [
                'value' => BlogConfig::ARCHIVE_MODE_ARCHIVE,
                'label' => $this->translator->trans('WEMSG.BLOG.RESET.deleteModeArchiveLabel', [], 'contao_default'),
            ],
            [
                'value' => BlogConfig::ARCHIVE_MODE_KEEP,
                'label' => $this->translator->trans('WEMSG.BLOG.RESET.deleteModeKeepLabel', [], 'contao_default'),
            ],
            [
                'value' => BlogConfig::ARCHIVE_MODE_DELETE,
                'label' => $this->translator->trans('WEMSG.BLOG.RESET.deleteModeDeleteLabel', [], 'contao_default'),
            ],
        ];

        $this->addSelectField('deleteMode', $this->translator->trans('WEMSG.BLOG.RESET.deleteModeLabel', [], 'contao_default'), $resetOptions, BlogConfig::ARCHIVE_MODE_ARCHIVE, true);
    }

    public function isStepValid(): bool
    {
        // check if the step is correct
        if (!\in_array(Input::post('deleteMode'), BlogConfig::ARCHIVE_MODES_ALLOWED, true)) {
            throw new \InvalidArgumentException($this->translator->trans('WEMSG.BLOG.RESET.deleteModeUnknown', [], 'contao_default'));
        }

        return true;
    }

    public function do(): void
    {
        // do what is meant to be done in this step
        $this->resetUserGroupSettings();
        $this->reset(Input::post('deleteMode'));
    }

    protected function reset(string $deleteMode): void
    {
        // reset everything except what we wanted to keep
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var BlogConfig */
        $blogConfig = $config->getSgBlog();
        /** @var BlogPresetConfig */
        $presetConfig = $blogConfig->getCurrentPreset();
        $archiveTimestamp = time();

        switch ($deleteMode) {
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
            ->setSgArchivedMode($deleteMode)
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

        $objUserGroup = UserGroupModel::findByName($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['UsergroupWebmastersName']);
        if (!$objUserGroup) {
            throw new Exception(sprintf('Unable to find the user group "%s"', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['UsergroupWebmastersName']));
        }
        $objUserGroup = $this->resetUserGroupSmartgearPermissions($objUserGroup);
        $objUserGroup = $this->resetUserGroupAllowedModules($objUserGroup);
        $objUserGroup = $this->resetUserGroupAllowedNewsArchive($objUserGroup, $blogConfig);
        $objUserGroup = $this->resetUserGroupAllowedDirectory($objUserGroup, $blogConfig);
        $objUserGroup = $this->resetUserGroupAllowedFields($objUserGroup);
        $objUserGroup->save();

        $objUserGroup = UserGroupModel::findByName($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['UsergroupAdministratorsName']);
        if (!$objUserGroup) {
            throw new Exception(sprintf('Unable to find the user group "%s"', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['UsergroupAdministratorsName']));
        }
        $objUserGroup = $this->resetUserGroupSmartgearPermissions($objUserGroup);
        $objUserGroup = $this->resetUserGroupAllowedModules($objUserGroup);
        $objUserGroup = $this->resetUserGroupAllowedNewsArchive($objUserGroup, $blogConfig);
        $objUserGroup = $this->resetUserGroupAllowedDirectory($objUserGroup, $blogConfig);
        $objUserGroup = $this->resetUserGroupAllowedFields($objUserGroup);
        $objUserGroup->save();
    }

    protected function resetUserGroupSmartgearPermissions(UserGroupModel $objUserGroup): UserGroupModel
    {
        return UserGroupModelUtil::removeSmartgearPermissions($objUserGroup, [SmartgearPermissions::BLOG_EXPERT]);
    }

    protected function resetUserGroupAllowedModules(UserGroupModel $objUserGroup): UserGroupModel
    {
        return UserGroupModelUtil::removeAllowedModules($objUserGroup, ['news']);
    }

    protected function resetUserGroupAllowedNewsArchive(UserGroupModel $objUserGroup, BlogConfig $blogConfig): UserGroupModel
    {
        $objUserGroup = UserGroupModelUtil::removeAllowedNewsArchive($objUserGroup, [$blogConfig->getSgNewsArchive()]);
        $objUserGroup->newp = null;

        return $objUserGroup;
    }

    protected function resetUserGroupAllowedDirectory(UserGroupModel $objUserGroup, BlogConfig $blogConfig): UserGroupModel
    {
        // add allowed directory
        $objFolder = FilesModel::findByPath($blogConfig->getCurrentPreset()->getSgNewsFolder());
        if (!$objFolder) {
            throw new Exception('Unable to find the folder');
        }

        return UserGroupModelUtil::removeAllowedFilemounts($objUserGroup, [$objFolder->uuid]);
    }

    protected function resetUserGroupAllowedFields(UserGroupModel $objUserGroup): UserGroupModel
    {
        return UserGroupModelUtil::removeAllowedFieldsByPrefixes($objUserGroup, ['tl_news::']);
    }
}
