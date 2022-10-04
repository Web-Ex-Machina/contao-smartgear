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

namespace WEM\SmartgearBundle\Backend\Component\Events;

use Contao\ArticleModel;
use Contao\CalendarEventsModel;
use Contao\CalendarModel;
use Contao\ContentModel;
use Contao\FilesModel;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\UserGroupModel;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Backend\Resetter as BackendResetter;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\UserGroupModelUtil;
use WEM\SmartgearBundle\Config\Component\Events\Events as EventsConfig;
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
        // reset everything except what we wanted to keep
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var EventsConfig */
        $eventsConfig = $config->getSgEvents();
        if (!$eventsConfig) {
            return;
        }
        $this->resetUserGroupSettings();
        $archiveTimestamp = time();

        switch ($mode) {
            case EventsConfig::ARCHIVE_MODE_ARCHIVE:
                $objFolder = new \Contao\Folder($eventsConfig->getSgEventsFolder());
                if ($objFolder) {
                    $objFolder->renameTo(sprintf('files/archives/events-%s', (string) $archiveTimestamp));
                }

                $objCalendar = CalendarModel::findById($eventsConfig->getSgCalendar());
                if ($objCalendar) {
                    $objCalendar->title = sprintf('%s (Archive-%s)', $objCalendar->title, (string) $archiveTimestamp);
                    $objCalendar->save();
                }

                $objPage = PageModel::findById($eventsConfig->getSgPage());
                if ($objPage) {
                    $objPage->published = false;
                    $objPage->save();
                }

                foreach ($eventsConfig->getContaoArticlesIds() as $id) {
                    $objArticle = ArticleModel::findByPk($id);
                    if ($objArticle) {
                        $objArticle->published = false;
                        $objArticle->title = sprintf('%s (Archive-%s)', $objArticle->title, (string) $archiveTimestamp);
                        $objArticle->save();
                    }
                }

                foreach ($eventsConfig->getContaoModulesIds() as $id) {
                    $objModule = ModuleModel::findByPk($id);
                    if ($objModule) {
                        $objModule->published = false;
                        $objModule->title = sprintf('%s (Archive-%s)', $objModule->title, (string) $archiveTimestamp);
                        $objModule->save();
                    }
                }
            break;
            case EventsConfig::ARCHIVE_MODE_KEEP:
            break;
            case EventsConfig::ARCHIVE_MODE_DELETE:
                $objFolder = new \Contao\Folder($eventsConfig->getSgEventsFolder());
                if ($objFolder) {
                    $objFolder->delete();
                }

                $calendarEvents = CalendarEventsModel::findByPid($eventsConfig->getSgCalendar());
                if ($calendarEvents) {
                    while ($calendarEvents->next()) {
                        $calendarEvents->delete();
                    }
                }

                $objCalendar = CalendarModel::findById($eventsConfig->getSgCalendar());
                if ($objCalendar) {
                    $objCalendar->delete();
                }

                $objPage = PageModel::findById($eventsConfig->getSgPage());
                if ($objPage) {
                    $objPage->delete();
                }

                foreach ($eventsConfig->getContaoArticlesIds() as $id) {
                    $objArticle = ArticleModel::findByPk($id);
                    if ($objArticle) {
                        $objArticle->delete();
                    }
                }

                foreach ($eventsConfig->getContaoContentsIds() as $id) {
                    $objContent = ContentModel::findByPk($id);
                    if ($objContent) {
                        $objContent->delete();
                    }
                }

                foreach ($eventsConfig->getContaoModulesIds() as $id) {
                    $objModule = ModuleModel::findByPk($id);
                    if ($objModule) {
                        $objModule->delete();
                    }
                }
            break;
            default:
                throw new \InvalidArgumentException($this->translator->trans('WEMSG.EVENTS.RESET.deleteModeUnknown', [], 'contao_default'));
            break;
        }

        $eventsConfig->setSgArchived(true)
            ->setSgArchivedMode($mode)
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

        $this->resetUserGroup(UserGroupModel::findOneById($config->getSgUserGroupRedactors()), $eventsConfig);
        $this->resetUserGroup(UserGroupModel::findOneById($config->getSgUserGroupAdministrators()), $eventsConfig);
    }

    protected function resetUserGroup(UserGroupModel $objUserGroup, EventsConfig $eventsConfig): void
    {
        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);
        $userGroupManipulator
            ->removeSmartgearPermissions([SmartgearPermissions::EVENTS_EXPERT])
            ->removeAllowedModules(['calendar'])
            ->removeAllowedCalendar([$eventsConfig->getSgCalendar()])
            ->removeAllowedFieldsByPrefixes(['tl_calendar_events::'])
            ->removeAllowedPagemounts($eventsConfig->getContaoPagesIds())
            ->removeAllowedModules(Module::getTypesByIds($eventsConfig->getContaoModulesIds()))
        ;

        $objFolder = FilesModel::findByPath($eventsConfig->getSgEventsFolder());
        if ($objFolder) {
            $userGroupManipulator->removeAllowedFilemounts([$objFolder->uuid]);
        }

        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->calendarp = null;
        $objUserGroup->save();
    }
}
