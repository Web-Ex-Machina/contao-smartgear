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

namespace WEM\SmartgearBundle\Backend\Component\Events\ResetStep;

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
use WEM\SmartgearBundle\Config\Component\Events\Events as EventsConfig;
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

        $this->title = $this->translator->trans('WEMSG.EVENTS.RESET.title', [], 'contao_default');

        $resetOptions = [
            [
                'value' => EventsConfig::ARCHIVE_MODE_ARCHIVE,
                'label' => $this->translator->trans('WEMSG.EVENTS.RESET.deleteModeArchiveLabel', [], 'contao_default'),
            ],
            [
                'value' => EventsConfig::ARCHIVE_MODE_KEEP,
                'label' => $this->translator->trans('WEMSG.EVENTS.RESET.deleteModeKeepLabel', [], 'contao_default'),
            ],
            [
                'value' => EventsConfig::ARCHIVE_MODE_DELETE,
                'label' => $this->translator->trans('WEMSG.EVENTS.RESET.deleteModeDeleteLabel', [], 'contao_default'),
            ],
        ];

        $this->addSelectField('deleteMode', $this->translator->trans('WEMSG.EVENTS.RESET.deleteModeLabel', [], 'contao_default'), $resetOptions, EventsConfig::ARCHIVE_MODE_ARCHIVE, true);
    }

    public function isStepValid(): bool
    {
        // check if the step is correct
        if (!\in_array(Input::post('deleteMode'), EventsConfig::ARCHIVE_MODES_ALLOWED, true)) {
            throw new \InvalidArgumentException($this->translator->trans('WEMSG.EVENTS.RESET.deleteModeUnknown', [], 'contao_default'));
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
        /** @var EventsConfig */
        $eventsConfig = $config->getSgEvents();
        $archiveTimestamp = time();

        switch ($deleteMode) {
            case EventsConfig::ARCHIVE_MODE_ARCHIVE:
                $objFolder = new \Contao\Folder($presetConfig->getSgNewsFolder());
                $objNewsArchive = NewsArchiveModel::findById($eventsConfig->getSgNewsArchive());

                $objFolder->renameTo(sprintf('files/archives/news-%s', (string) $archiveTimestamp));
                $objNewsArchive->title = sprintf('%s (Archive-%s)', $objNewsArchive->title, (string) $archiveTimestamp);
                $objNewsArchive->save();

            break;
            case EventsConfig::ARCHIVE_MODE_KEEP:
            break;
            case EventsConfig::ARCHIVE_MODE_DELETE:
                $objFolder = new \Contao\Folder($presetConfig->getSgNewsFolder());
                $objNewsArchive = NewsArchiveModel::findById($eventsConfig->getSgNewsArchive());

                $objFolder->delete();
                $objNewsArchive->delete();
            break;
            default:
                throw new \InvalidArgumentException($this->translator->trans('WEMSG.EVENTS.RESET.deleteModeUnknown', [], 'contao_default'));
            break;
        }

        $objPage = PageModel::findById($eventsConfig->getSgPage());
        $objPage->published = false;
        $objPage->save();

        $eventsConfig->setSgArchived(true)
            ->setSgArchivedMode($deleteMode)
            ->setSgArchivedAt($archiveTimestamp)
        ;

        $config->setSgEvents($eventsConfig);

        $this->configurationManager->save($config);
    }

    protected function resetUserGroupSettings(): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var EventsConfig */
        $eventsConfig = $config->getSgEvents();

        $objUserGroup = UserGroupModel::findByName($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['UsergroupWebmastersName']);
        if (!$objUserGroup) {
            throw new Exception(sprintf('Unable to find the user group "%s"', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['UsergroupWebmastersName']));
        }
        $objUserGroup = $this->resetUserGroupSmartgearPermissions($objUserGroup);
        $objUserGroup = $this->resetUserGroupAllowedModules($objUserGroup);
        $objUserGroup = $this->resetUserGroupAllowedNewsArchive($objUserGroup, $eventsConfig);
        $objUserGroup = $this->resetUserGroupAllowedDirectory($objUserGroup, $eventsConfig);
        $objUserGroup = $this->resetUserGroupAllowedFields($objUserGroup);
        $objUserGroup->save();

        $objUserGroup = UserGroupModel::findByName($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['UsergroupAdministratorsName']);
        if (!$objUserGroup) {
            throw new Exception(sprintf('Unable to find the user group "%s"', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['UsergroupAdministratorsName']));
        }
        $objUserGroup = $this->resetUserGroupSmartgearPermissions($objUserGroup);
        $objUserGroup = $this->resetUserGroupAllowedModules($objUserGroup);
        $objUserGroup = $this->resetUserGroupAllowedNewsArchive($objUserGroup, $eventsConfig);
        $objUserGroup = $this->resetUserGroupAllowedDirectory($objUserGroup, $eventsConfig);
        $objUserGroup = $this->resetUserGroupAllowedFields($objUserGroup);
        $objUserGroup->save();
    }

    protected function resetUserGroupSmartgearPermissions(UserGroupModel $objUserGroup): UserGroupModel
    {
        return UserGroupModelUtil::removeSmartgearPermissions($objUserGroup, [SmartgearPermissions::EVENTS_EXPERT]);
    }

    protected function resetUserGroupAllowedModules(UserGroupModel $objUserGroup): UserGroupModel
    {
        return UserGroupModelUtil::removeAllowedModules($objUserGroup, ['news']);
    }

    protected function resetUserGroupAllowedNewsArchive(UserGroupModel $objUserGroup, EventsConfig $eventsConfig): UserGroupModel
    {
        $objUserGroup = UserGroupModelUtil::removeAllowedNewsArchive($objUserGroup, [$eventsConfig->getSgNewsArchive()]);
        $objUserGroup->newp = null;

        return $objUserGroup;
    }

    protected function resetUserGroupAllowedDirectory(UserGroupModel $objUserGroup, EventsConfig $eventsConfig): UserGroupModel
    {
        // add allowed directory
        $objFolder = FilesModel::findByPath($eventsConfig->getCurrentPreset()->getSgNewsFolder());
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
