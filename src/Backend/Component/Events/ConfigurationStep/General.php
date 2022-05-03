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

namespace WEM\SmartgearBundle\Backend\Component\Events\ConfigurationStep;

use Contao\ArticleModel;
use Contao\CalendarModel;
use Contao\ContentModel;
use Contao\FilesModel;
use Contao\Input;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\UserGroupModel;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Backend\ConfigurationStep;
use WEM\SmartgearBundle\Classes\Command\Util as CommandUtil;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\DirectoriesSynchronizer;
use WEM\SmartgearBundle\Classes\UserGroupModelUtil;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Config\Component\Events\Events as EventsConfig;
use WEM\SmartgearBundle\Config\Core as CoreConfig;
use WEM\SmartgearBundle\Security\SmartgearPermissions;

class General extends ConfigurationStep
{
    /** @var TranslatorInterface */
    protected $translator;
    /** @var ConfigurationManager */
    protected $configurationManager;
    /** @var CommandUtil */
    protected $commandUtil;
    /** @var DirectoriesSynchronizer */
    protected $leafletDirectorySynchronizer;

    public function __construct(
        string $module,
        string $type,
        TranslatorInterface $translator,
        ConfigurationManager $configurationManager,
        CommandUtil $commandUtil,
        DirectoriesSynchronizer $leafletDirectorySynchronizer
    ) {
        parent::__construct($module, $type);
        $this->translator = $translator;
        $this->configurationManager = $configurationManager;
        $this->commandUtil = $commandUtil;
        $this->leafletDirectorySynchronizer = $leafletDirectorySynchronizer;

        $this->title = $this->translator->trans('WEMSG.EVENTS.INSTALL_GENERAL.title', [], 'contao_default');
        /** @var EventsConfig */
        $config = $this->configurationManager->load()->getSgEvents();

        $this->addTextField('calendarTitle', $this->translator->trans('WEMSG.EVENTS.INSTALL_GENERAL.calendarTitle', [], 'contao_default'), $config->getSgCalendarTitle(), true);

        $this->addTextField('eventsListPerPage', $this->translator->trans('WEMSG.EVENTS.INSTALL_GENERAL.eventsListPerPage', [], 'contao_default'), (string) $config->getSgEventsListPerPage(), false, '', 'number');

        $this->addTextField('pageTitle', $this->translator->trans('WEMSG.EVENTS.INSTALL_GENERAL.pageTitle', [], 'contao_default'), $config->getSgPageTitle(), true);

        $this->addSimpleFileTree('eventsFolder', $this->translator->trans('WEMSG.EVENTS.INSTALL_GENERAL.eventsFolder', [], 'contao_default'), $config->getSgEventsFolder(), true, false, '', $this->translator->trans('WEMSG.EVENTS.INSTALL_GENERAL.eventsFolderHelp', [], 'contao_default'), ['multiple' => false, 'isGallery' => false,
            'isDownloads' => false,
            'files' => false, ]);

        $this->addCheckboxField('expertMode', $this->translator->trans('WEMSG.EVENTS.INSTALL_GENERAL.expertMode', [], 'contao_default'), '1', EventsConfig::MODE_EXPERT === $config->getSgMode());
    }

    public function isStepValid(): bool
    {
        // check if the step is correct
        if (null === Input::post('calendarTitle', null)) {
            throw new Exception($this->translator->trans('WEMSG.EVENTS.INSTALL_GENERAL.calendarTitleMissing', [], 'contao_default'));
        }
        if (null === Input::post('eventsListPerPage', null)) {
            throw new Exception($this->translator->trans('WEMSG.EVENTS.INSTALL_GENERAL.eventsListPerPageMissing', [], 'contao_default'));
        }
        if (0 > (int) Input::post('eventsListPerPage')) {
            throw new Exception($this->translator->trans('WEMSG.EVENTS.INSTALL_GENERAL.eventsListPerPageTooLow', [], 'contao_default'));
        }
        if (null === Input::post('pageTitle', null)) {
            throw new Exception($this->translator->trans('WEMSG.EVENTS.INSTALL_GENERAL.pageTitleMissing', [], 'contao_default'));
        }
        if (null === Input::post('eventsFolder', null)) {
            throw new Exception($this->translator->trans('WEMSG.EVENTS.INSTALL_GENERAL.eventsFolderMissing', [], 'contao_default'));
        }

        return true;
    }

    public function do(): void
    {
        // do what is meant to be done in this step
        $this->updateModuleConfiguration();

        $this->createFolder();
        $page = $this->createPage();
        $article = $this->createArticle($page);
        $calendar = $this->createCalendarFeed($page);
        $modules = $this->createModules($page, $calendar);
        $this->fillArticle($page, $article, $modules);
        $this->importLeaflet();
        $this->updateModuleConfigurationAfterGenerations($page, $calendar, $modules);
        $this->updateUserGroups((bool) Input::post('expertMode', false));
        $this->commandUtil->executeCmdPHP('cache:clear');
    }

    protected function updateModuleConfiguration(): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var EventsConfig */
        $eventsConfig = $config->getSgEvents();

        $eventsConfig
            ->setSgMode(Input::post('expertMode') ? EventsConfig::MODE_EXPERT : EventsConfig::MODE_SIMPLE)
            ->setSgCalendarTitle(Input::post('calendarTitle'))
            ->setSgEventsListPerPage((int) Input::post('eventsListPerPage'))
            ->setSgPageTitle(Input::post('pageTitle'))
            ->setSgEventsFolder(Input::post('eventsFolder'))
            ->setSgArchived(false)
            ->setSgArchivedMode(EventsConfig::ARCHIVE_MODE_EMPTY)
            ->setSgArchivedAt(0)
        ;
        $config->setSgEvents($eventsConfig);

        $this->configurationManager->save($config);
    }

    protected function createFolder(): void
    {
        $objFolder = new \Contao\Folder(Input::post('eventsFolder', null));
        $objFolder->unprotect();
    }

    protected function createPage(): PageModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var EventsConfig */
        $eventsConfig = $config->getSgEvents();

        $rootPage = PageModel::findById($config->getSgRootPage());

        $page = PageModel::findById($eventsConfig->getSgPage());

        return Util::createPage($eventsConfig->getSgPageTitle(), 0, array_merge([
            'pid' => $rootPage->id,
            'sorting' => ((int) $rootPage->sorting) + 128,
            'layout' => $rootPage->layout,
            'title' => $eventsConfig->getSgPageTitle(),
            'robots' => 'index,follow',
            'type' => 'regular',
            'published' => 1,
        ], null !== $page ? ['id' => $page->id] : []));
    }

    protected function createArticle(PageModel $page): ArticleModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var EventsConfig */
        $eventsConfig = $config->getSgEvents();

        $article = ArticleModel::findOneByPid($page->id) ?? new ArticleModel();
        $article->pid = $page->id;
        $article->sorting = 128;
        $article->title = $eventsConfig->getSgPageTitle();
        $article->alias = $page->alias;
        $article->author = 1;
        $article->inColumn = 'main';
        $article->published = 1;
        $article->tstamp = time();

        $article->save();

        return $article;
    }

    protected function createCalendarFeed(PageModel $page): CalendarModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var EventsConfig */
        $eventsConfig = $config->getSgEvents();

        $objUserGroupAdministrators = UserGroupModel::findOneById($config->getSgUserGroupAdministrators());
        $objUserGroupWebmasters = UserGroupModel::findOneById($config->getSgUserGroupWebmasters());

        $calendar = CalendarModel::findById($eventsConfig->getSgCalendar()) ?? new CalendarModel();
        $calendar->title = $eventsConfig->getSgCalendarTitle();
        $calendar->jumpTo = $page->id;
        $calendar->groups = serialize([$objUserGroupAdministrators->id, $objUserGroupWebmasters->id]);
        $calendar->tstamp = time();
        $calendar->save();

        return $calendar;
    }

    protected function createModules(PageModel $page, CalendarModel $calendar): array
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var EventsConfig */
        $eventsConfig = $config->getSgEvents();

        $moduleReader = new ModuleModel();
        $moduleList = new ModuleModel();

        if (null !== $eventsConfig->getSgModuleReader()) {
            $moduleReaderOld = ModuleModel::findById($eventsConfig->getSgModuleReader());
            if ($moduleReaderOld) {
                $moduleReaderOld->delete();
            }
            $moduleReader->id = $eventsConfig->getSgModuleReader();
        }
        $moduleReader->name = $page->title.' - Reader';
        $moduleReader->pid = $config->getSgTheme();
        $moduleReader->type = 'eventreader';
        $moduleReader->cal_calendar = serialize([$calendar->id]);
        $moduleReader->imgSize = serialize([0 => '1200', 1 => '0', 2 => \Contao\Image\ResizeConfiguration::MODE_PROPORTIONAL]);
        $moduleReader->tstamp = time();
        $moduleReader->save();

        if (null !== $eventsConfig->getSgModuleList()) {
            $moduleListOld = ModuleModel::findById($eventsConfig->getSgModuleList());
            if ($moduleListOld) {
                $moduleListOld->delete();
            }
            $moduleList->id = $eventsConfig->getSgModuleList();
        }
        $moduleList->name = $page->title.' - List';
        $moduleList->pid = $config->getSgTheme();
        $moduleList->type = 'eventlist';
        $moduleList->cal_calendar = serialize([$calendar->id]);
        $moduleList->numberOfItems = 0;
        $moduleList->cal_format = 'next_365';
        $moduleList->cal_order = 'order_date_desc';
        $moduleList->cal_readerModule = $moduleReader->id;
        $moduleList->perPage = $eventsConfig->getSgEventsListPerPage();
        $moduleList->imgSize = serialize([0 => '480', 1 => '0', 2 => \Contao\Image\ResizeConfiguration::MODE_PROPORTIONAL]);

        $moduleList->tstamp = time();

        $moduleList->save();

        return ['reader' => $moduleReader, 'list' => $moduleList];
    }

    protected function fillArticle(PageModel $page, ArticleModel $article, array $modules): void
    {
        $headline = ContentModel::findOneBy(['pid = ?', 'ptable = ?', 'type = ?'], [$article->id, 'tl_article', 'headline']) ?? new ContentModel();

        $list = ContentModel::findOneBy(['pid = ?', 'ptable = ?', 'type = ?', 'module = ?'], [$article->id, 'tl_article', 'module', $modules['list']->id]) ?? new ContentModel();

        $headline->type = 'headline';
        $headline->pid = $article->id;
        $headline->ptable = 'tl_article';
        $headline->headline = serialize(['unit' => 'h1', 'value' => $page->title]);
        $headline->cssID = 'sep-bottom';
        $headline->tstamp = time();
        $headline->save();

        $list->type = 'module';
        $list->pid = $article->id;
        $list->ptable = 'tl_article';
        $list->module = $modules['list']->id;
        $list->tstamp = time();
        $list->save();

        $article->save();
    }

    protected function importLeaflet(): void
    {
        $this->leafletDirectorySynchronizer->synchronize(true);
    }

    protected function updateModuleConfigurationAfterGenerations(PageModel $page, CalendarModel $calendar, array $modules): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var EventsConfig */
        $eventsConfig = $config->getSgEvents();

        $eventsConfig
            ->setSgPage((int) $page->id)
            ->setSgCalendar((int) $calendar->id)
            ->setSgModuleReader((int) $modules['reader']->id)
            ->setSgModuleList((int) $modules['list']->id)
        ;

        $config->setSgEvents($eventsConfig);

        $this->configurationManager->save($config);
    }

    protected function updateUserGroups(bool $expertMode): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var EventsConfig */
        $eventsConfig = $config->getSgEvents();

        // retrieve the webmaster's group and update the permissions

        $objUserGroup = UserGroupModel::findOneById($config->getSgUserGroupWebmasters());
        $objUserGroup = $this->updateUserGroupSmartgearPermissions($objUserGroup, $expertMode);
        $objUserGroup = $this->updateUserGroupAllowedModules($objUserGroup);
        $objUserGroup = $this->updateUserGroupAllowedNewsArchive($objUserGroup, $eventsConfig);
        $objUserGroup = $this->updateUserGroupAllowedDirectory($objUserGroup, $eventsConfig);
        $objUserGroup = $this->updateUserGroupAllowedFields($objUserGroup);
        $objUserGroup->save();

        $objUserGroup = UserGroupModel::findOneById($config->getSgUserGroupAdministrators());
        $objUserGroup = $this->updateUserGroupSmartgearPermissions($objUserGroup, true);
        $objUserGroup = $this->updateUserGroupAllowedModules($objUserGroup);
        $objUserGroup = $this->updateUserGroupAllowedNewsArchive($objUserGroup, $eventsConfig);
        $objUserGroup = $this->updateUserGroupAllowedDirectory($objUserGroup, $eventsConfig);
        $objUserGroup = $this->updateUserGroupAllowedFields($objUserGroup);
        $objUserGroup->save();
    }

    protected function updateUserGroupSmartgearPermissions(UserGroupModel $objUserGroup, bool $expertMode): UserGroupModel
    {
        return $expertMode ? UserGroupModelUtil::addSmartgearPermissions($objUserGroup, [SmartgearPermissions::EVENTS_EXPERT]) : $objUserGroup;
    }

    protected function updateUserGroupAllowedModules(UserGroupModel $objUserGroup): UserGroupModel
    {
        return UserGroupModelUtil::addAllowedModules($objUserGroup, ['calendar']);
    }

    protected function updateUserGroupAllowedNewsArchive(UserGroupModel $objUserGroup, EventsConfig $eventsConfig): UserGroupModel
    {
        $objUserGroup = UserGroupModelUtil::addAllowedCalendar($objUserGroup, [$eventsConfig->getSgCalendar()]);
        $objUserGroup->newp = serialize(['create', 'delete']);

        return $objUserGroup;
    }

    protected function updateUserGroupAllowedDirectory(UserGroupModel $objUserGroup, EventsConfig $eventsConfig): UserGroupModel
    {
        // add allowed directory
        $objFolder = FilesModel::findByPath($eventsConfig->getSgEventsFolder());
        if (!$objFolder) {
            throw new Exception('Unable to find the folder');
        }

        return UserGroupModelUtil::addAllowedFilemounts($objUserGroup, [$objFolder->uuid]);
    }

    protected function updateUserGroupAllowedFields(UserGroupModel $objUserGroup): UserGroupModel
    {
        return UserGroupModelUtil::addAllowedFieldsByTables($objUserGroup, ['tl_calendar_events']);
    }
}