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

use Contao\CalendarModel;
use Contao\FilesModel;
use Contao\Input;
use Contao\PageModel;
use Contao\UserGroupModel;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Backend\AbstractStep;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\UserGroupModelUtil;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Config\Component\Events\Events as EventsConfig;
use WEM\SmartgearBundle\Model\Module;
use WEM\SmartgearBundle\Security\SmartgearPermissions;

class General extends AbstractStep
{
    /** @var ConfigurationManager */
    protected $configurationManager;

    protected $strTemplate = 'be_wem_sg_install_block_reset_step_events_general';

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
                $objFolder = new \Contao\Folder($eventsConfig->getSgEventsFolder());
                $objCalendar = CalendarModel::findById($eventsConfig->getSgCalendar());

                $objFolder->renameTo(sprintf('files/archives/events-%s', (string) $archiveTimestamp));
                $objCalendar->title = sprintf('%s (Archive-%s)', $objCalendar->title, (string) $archiveTimestamp);
                $objCalendar->save();

            break;
            case EventsConfig::ARCHIVE_MODE_KEEP:
            break;
            case EventsConfig::ARCHIVE_MODE_DELETE:
                $objFolder = new \Contao\Folder($eventsConfig->getSgEventsFolder());
                $objCalendar = CalendarModel::findById($eventsConfig->getSgCalendar());

                $objFolder->delete();
                $objCalendar->delete();
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

        $this->resetUserGroup(UserGroupModel::findOneById($config->getSgUserGroupWebmasters()), $eventsConfig);
        $this->resetUserGroup(UserGroupModel::findOneById($config->getSgUserGroupAdministrators()), $eventsConfig);
    }

    protected function resetUserGroup(UserGroupModel $objUserGroup, EventsConfig $eventsConfig): void
    {
        $objFolder = FilesModel::findByPath($eventsConfig->getSgEventsFolder());
        if (!$objFolder) {
            throw new Exception('Unable to find the folder');
        }

        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);
        $userGroupManipulator
            ->removeSmartgearPermissions([SmartgearPermissions::EVENTS_EXPERT])
            ->removeAllowedModules(['calendar'])
            ->removeAllowedCalendar([$eventsConfig->getSgCalendar()])
            ->removeAllowedFilemounts([$objFolder->uuid])
            ->removeAllowedFieldsByPrefixes(['tl_calendar_events::'])
            ->removeAllowedPagemounts($eventsConfig->getContaoPagesIds())
            ->removeAllowedModules(Module::getTypesByIds($eventsConfig->getContaoModulesIds()))
        ;

        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->calendarp = null;
        $objUserGroup->save();
    }
}
