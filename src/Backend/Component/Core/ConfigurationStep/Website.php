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

namespace WEM\SmartgearBundle\Backend\Component\Core\ConfigurationStep;

use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\File;
use Contao\Files;
use Contao\FilesModel;
use Contao\Folder;
use Contao\FrontendTemplate;
use Contao\Input;
use Contao\LayoutModel;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\ThemeModel;
use Contao\UserGroupModel;
use Contao\UserModel;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Backend\ConfigurationStep;
use WEM\SmartgearBundle\Classes\Command\Util as CommandUtil;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\Migration\Result as MigrationResult;
use WEM\SmartgearBundle\Classes\UserGroupModelUtil;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Model\Module;
use WEM\SmartgearBundle\Security\SmartgearPermissions;
use WEM\SmartgearBundle\Update\UpdateManager;
use WEM\UtilsBundle\Classes\Files as WEMFiles;
use WEM\UtilsBundle\Classes\StringUtil as WEMStringUtil;

class Website extends ConfigurationStep
{
    /** @var TranslatorInterface */
    protected $translator;
    /** @var ConfigurationManager */
    protected $configurationManager;
    /** @var UpdateManager */
    protected $updateManager;
    /** @var CommandUtil */
    protected $commandUtil;
    /** array */
    protected $userGroupUpdaters;
    /** array */
    protected $userGroupWebmasterOldPermissions = [];

    protected $strTemplate = 'be_wem_sg_install_block_configuration_step_core_website';

    public function __construct(
        string $module,
        string $type,
        TranslatorInterface $translator,
        ConfigurationManager $configurationManager,
        UpdateManager $updateManager,
        CommandUtil $commandUtil,
        array $userGroupUpdaters
    ) {
        parent::__construct($module, $type);
        $this->translator = $translator;
        $this->configurationManager = $configurationManager;
        $this->updateManager = $updateManager;
        $this->commandUtil = $commandUtil;
        $this->userGroupUpdaters = $userGroupUpdaters;
        $this->title = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['Title'];
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        $countries = [];
        foreach (\Contao\System::getCountries() as $shortName => $longName) {
            $countries[] = ['value' => $longName, 'label' => $longName];
        }
        $this->addFileField('sgWebsiteLogo', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgWebsiteLogo'], empty($config->getSgOwnerLogo()));
        $this->addTextField('sgOwnerName', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerName'], !empty($config->getSgOwnerName()) ? $config->getSgOwnerName() : $config->getSgWebsiteTitle(), true);
        $this->addTextField('sgOwnerStatus', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerStatus'], $config->getSgOwnerStatus(), true);
        $this->addTextField('sgOwnerSiret', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerSiret'], $config->getSgOwnerSiret(), true);
        $this->addTextField('sgOwnerStreet', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerStreet'], $config->getSgOwnerStreet(), true);
        $this->addTextField('sgOwnerPostal', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerPostal'], $config->getSgOwnerPostal(), true);
        $this->addTextField('sgOwnerCity', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerCity'], $config->getSgOwnerCity(), true);
        $this->addTextField('sgOwnerRegion', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerRegion'], $config->getSgOwnerRegion(), false);
        $this->addSelectField('sgOwnerCountry', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerCountry'], $countries, !empty($config->getSgOwnerCountry()) ? $config->getSgOwnerCountry() : 'France', true);
        $this->addTextField('sgOwnerEmail', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerEmail'], $config->getSgOwnerEmail(), true);
        $this->addTextField('sgOwnerDomain', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerDomain'], !empty($config->getSgOwnerDomain()) ? $config->getSgOwnerDomain() : \Contao\Environment::get('base'), true);
        $this->addTextField('sgOwnerHost', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerHost'], $config->getSgOwnerHost(), true);
        $this->addTextField('sgOwnerDpoName', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerDpoName'], $config->getSgOwnerDpoName(), true);
        $this->addTextField('sgOwnerDpoEmail', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerDpoEmail'], $config->getSgOwnerDpoEmail(), true);
        $this->addTextField('sgGoogleFonts', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgGoogleFonts'], implode(',', $config->getSgGoogleFonts()), false, '', 'text', '', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgGoogleFontsHelp']);
    }

    public function getFilledTemplate(): FrontendTemplate
    {
        // to render the step
        $objTemplate = parent::getFilledTemplate();
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        if (!empty($config->getSgOwnerLogo())) {
            $objTemplate->logo = WEMFiles::imageToBase64(new File($config->getSgOwnerLogo()));
        } else {
            $objTemplate->logo = '';
        }
        // And return the template, parsed.
        return $objTemplate;
    }

    public function isStepValid(): bool
    {
        // check if the step is correct
        if (empty(Input::post('sgOwnerName'))) {
            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerNameEmpty']);
        }

        if (empty(Input::post('sgOwnerStatus'))) {
            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerStatusEmpty']);
        }

        if (empty(Input::post('sgOwnerSiret'))) {
            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerSiretEmpty']);
        }

        if (empty(Input::post('sgOwnerStreet'))) {
            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerStreetEmpty']);
        }

        if (empty(Input::post('sgOwnerPostal'))) {
            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerPostalEmpty']);
        }

        if (empty(Input::post('sgOwnerCity'))) {
            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerCityEmpty']);
        }

        if (empty(Input::post('sgOwnerCountry'))) {
            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerCountryEmpty']);
        }

        if (empty(Input::post('sgOwnerEmail'))) {
            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerEmailEmpty']);
        }

        if (empty(Input::post('sgOwnerDomain'))) {
            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerDomainEmpty']);
        }

        if (empty(Input::post('sgOwnerHost'))) {
            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerHostEmpty']);
        }

        if (empty(Input::post('sgOwnerDpoName'))) {
            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerDpoNameEmpty']);
        }

        if (empty(Input::post('sgOwnerDpoEmail'))) {
            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerDpoEmailEmpty']);
        }

        return true;
    }

    public function do(): void
    {
        // do what is meant to be done in this step
        $this->updateModuleConfiguration();
        $this->createClientFilesFolders();
        $this->createClientTemplatesFolder();
        if (!empty($_FILES)) {
            $objFileLogo = $this->uploadLogo();
            $this->updateModuleConfigurationLogo($objFileLogo);
        }

        $themeId = $this->createTheme();
        $this->updateModuleConfigurationTheme($themeId);

        $modules = $this->createModules($themeId);

        $layouts = $this->createLayouts($themeId, $modules);
        $this->updateModuleConfigurationLayouts($layouts);

        $groups = $this->createUserGroups();
        $users = $this->createUsers($groups);
        $this->updateModuleConfigurationUserAndGroups($users, $groups);

        $pages = $this->createPages($layouts, $groups, $users, $modules);
        $this->updateModuleConfigurationPages($pages);

        $articles = $this->createArticles($pages);
        $this->updateModuleConfigurationArticles($articles);

        $this->updateLayouts($layouts, $pages);

        $contents = $this->createContents($pages, $articles, $modules);
        $this->updateModuleConfigurationContents($contents);

        $modules = array_merge($this->createModules2($themeId, $pages), $modules);
        $this->updateModuleConfigurationModules($modules);

        $notificationGateways = $this->createNotificationGateways();
        $this->updateModuleConfigurationNotificationGateways($notificationGateways);
        $this->updateUserGroups();

        $this->commandUtil->executeCmdPHP('cache:clear');
        $this->commandUtil->executeCmdPHP('contao:symlinks');

        $this->launchMigrations();
    }

    protected function launchMigrations(): void
    {
        $updateResult = $this->updateManager->update();
        if ($updateResult->isSuccess()) {
            $this->addConfirm($this->translator->trans('WEMSG.UPDATEMANAGER.RESULT.success', [], 'contao_default'), $this->module);
        } else {
            $this->addError($this->translator->trans('WEMSG.UPDATEMANAGER.RESULT.fail', [], 'contao_default'), $this->module);
        }

        foreach ($updateResult->getResults() as $singleMigrationResult) {
            switch ($singleMigrationResult->getResult()->getStatus()) {
                case MigrationResult::STATUS_NOT_EXCUTED_YET:
                    $this->addInfo(sprintf('%s : %s', $singleMigrationResult->getName(), $this->translator->trans('WEMSG.UPDATEMANAGER.SINGLEMIGRATIONRESULT.statusNotexecutedyet', [], 'contao_default')), $this->module);
                break;
                case MigrationResult::STATUS_SHOULD_RUN:
                    $this->addInfo(sprintf('%s : %s', $singleMigrationResult->getName(), $this->translator->trans('WEMSG.UPDATEMANAGER.SINGLEMIGRATIONRESULT.statusShouldrun', [], 'contao_default')), $this->module);
                break;
                case MigrationResult::STATUS_SKIPPED:
                    $this->addInfo(sprintf('%s : %s', $singleMigrationResult->getName(), $this->translator->trans('WEMSG.UPDATEMANAGER.SINGLEMIGRATIONRESULT.statusSkipped', [], 'contao_default')), $this->module);
                break;
                case MigrationResult::STATUS_FAIL:
                    $this->addError(sprintf('%s : %s', $singleMigrationResult->getName(), $this->translator->trans('WEMSG.UPDATEMANAGER.SINGLEMIGRATIONRESULT.statusFail', [], 'contao_default')), $this->module);
                break;
                case MigrationResult::STATUS_SUCCESS:
                    $this->addConfirm(sprintf('%s : %s', $singleMigrationResult->getName(), $this->translator->trans('WEMSG.UPDATEMANAGER.SINGLEMIGRATIONRESULT.statusSuccess', [], 'contao_default')), $this->module);
                break;
            }
        }
    }

    protected function createClientFilesFolders(): void
    {
        $clientFilesFolder = new Folder(CoreConfig::DEFAULT_CLIENT_FILES_FOLDER);
        $clientFilesFolder->unprotect();
        $clientLogosFolder = new Folder(CoreConfig::DEFAULT_CLIENT_LOGOS_FOLDER);
        $clientLogosFolder->unprotect();
    }

    protected function createClientTemplatesFolder(): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        $clientTemplatesFolder = new Folder('templates'.\DIRECTORY_SEPARATOR.WEMStringUtil::generateAlias($config->getSgWebsiteTitle()));
    }

    protected function createTheme()
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        // Create the Smartgear main theme
        $objTheme = ThemeModel::findOneById($config->getSgTheme()) ?? new ThemeModel();
        $objTheme->tstamp = time();
        $objTheme->name = 'Smartgear '.$config->getSgWebsiteTitle();
        $objTheme->author = 'Web ex Machina';
        $objTheme->templates = sprintf('templates/%s', WEMStringUtil::generateAlias($config->getSgWebsiteTitle()));
        $objTheme->save();

        return (int) $objTheme->id;
    }

    protected function createModules(int $themeId): array
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        $registeredModules = $this->getConfigModulesAsFormattedArray();
        $modules = [];
        // Navigation
        $objNavMain = \array_key_exists('navigation', $registeredModules)
                            ? ModuleModel::findOneById($registeredModules['navigation']) ?? new ModuleModel()
                            : new ModuleModel()
                            ;
        $objNavMain->pid = $themeId;
        $objNavMain->tstamp = time();
        $objNavMain->type = 'navigation';
        $objNavMain->name = 'Nav - main';
        $objNavMain->save();
        $modules[$objNavMain->type] = $objNavMain;

        // Header
        $objHeaderModule = \array_key_exists('wem_sg_header', $registeredModules)
                            ? ModuleModel::findOneById($registeredModules['wem_sg_header']) ?? new ModuleModel()
                            : new ModuleModel()
                            ;
        $objHeaderModule->pid = $themeId;
        $objHeaderModule->tstamp = time();
        $objHeaderModule->type = 'wem_sg_header';
        $objHeaderModule->name = 'HEADER';
        if (!empty($config->getSgOwnerLogo())) {
            $objFileModel = FilesModel::findByPath($config->getSgOwnerLogo());
            if ($objFileModel) {
                $objHeaderModule->singleSRC = $objFileModel->uuid;
            }
        }
        $objHeaderModule->imgSize = 'a:3:{i:0;s:0:"";i:1;s:3:"100";i:2;s:12:"proportional";}';
        $objHeaderModule->wem_sg_header_sticky = 1;
        $objHeaderModule->wem_sg_header_nav_module = $objNavMain->id;
        $objHeaderModule->wem_sg_header_alt = 'Logo '.$this->sgConfig['websiteTitle'];
        $objHeaderModule->wem_sg_header_search_parameter = 'keywords';
        $objHeaderModule->wem_sg_header_nav_position = 'right';
        $objHeaderModule->wem_sg_header_panel_position = 'right';
        $objHeaderModule->save();
        $modules[$objHeaderModule->type] = $objHeaderModule;

        // Breadcrumb
        $objBreadcrumbModule = \array_key_exists('breadcrumb', $registeredModules)
                            ? ModuleModel::findOneById($registeredModules['breadcrumb']) ?? new ModuleModel()
                            : new ModuleModel()
                            ;
        $objBreadcrumbModule->pid = $themeId;
        $objBreadcrumbModule->tstamp = time();
        $objBreadcrumbModule->type = 'breadcrumb';
        $objBreadcrumbModule->name = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['ModuleBreadcrumbName'];
        $objBreadcrumbModule->save();
        $modules[$objBreadcrumbModule->type] = $objBreadcrumbModule;

        // Footer
        $objFooterModule = \array_key_exists('wem_sg_footer', $registeredModules)
                            ? ModuleModel::findOneById($registeredModules['wem_sg_footer']) ?? new ModuleModel()
                            : new ModuleModel()
                            ;
        $objFooterModule->pid = $themeId;
        $objFooterModule->tstamp = time();
        $objFooterModule->type = 'wem_sg_footer';
        $objFooterModule->name = 'FOOTER';
        $objFooterModule->html = file_get_contents(Util::getPublicOrWebDirectory().'/bundles/wemsmartgear/examples/footer_1.html');
        $objFooterModule->save();
        $modules[$objFooterModule->type] = $objFooterModule;

        // Sitemap
        $objSitemapModule = \array_key_exists('sitemap', $registeredModules)
                            ? ModuleModel::findOneById($registeredModules['sitemap']) ?? new ModuleModel()
                            : new ModuleModel()
                            ;
        $objSitemapModule->pid = $themeId;
        $objSitemapModule->tstamp = time();
        $objSitemapModule->type = 'sitemap';
        $objSitemapModule->name = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['ModuleSitemapName'];
        $objSitemapModule->save();
        $modules[$objSitemapModule->type] = $objSitemapModule;

        // Social link
        $objSocialLinkModule = \array_key_exists('wem_sg_social_link', $registeredModules)
                            ? ModuleModel::findOneById($registeredModules['wem_sg_social_link']) ?? new ModuleModel()
                            : new ModuleModel()
                            ;
        $objSocialLinkModule->pid = $themeId;
        $objSocialLinkModule->tstamp = time();
        $objSocialLinkModule->type = 'wem_sg_social_link';
        $objSocialLinkModule->name = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['ModuleSocialLinkName'];
        $objSocialLinkModule->save();
        $modules[$objSocialLinkModule->type] = $objSocialLinkModule;

        // Social Link Categories
        $objSocialLinkCategoriesModule = \array_key_exists('wem_sg_social_link_config_categories', $registeredModules)
                            ? ModuleModel::findOneById($registeredModules['wem_sg_social_link_config_categories']) ?? new ModuleModel()
                            : new ModuleModel()
                            ;
        $objSocialLinkCategoriesModule->pid = $themeId;
        $objSocialLinkCategoriesModule->tstamp = time();
        $objSocialLinkCategoriesModule->type = 'wem_sg_social_link_config_categories';
        $objSocialLinkCategoriesModule->name = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['ModuleSocialLinkConfigCategoriesName'];
        $objSocialLinkCategoriesModule->save();
        $modules[$objSocialLinkCategoriesModule->type] = $objSocialLinkCategoriesModule;

        // Personal Data Manager
        $objPDMModule = \array_key_exists('wem_personaldatamanager', $registeredModules)
                            ? ModuleModel::findOneById($registeredModules['wem_personaldatamanager']) ?? new ModuleModel()
                            : new ModuleModel()
                            ;

        $objPDMModule->name = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['modulePersonalDataManagerName'];
        $objPDMModule->pid = $themeId;
        $objPDMModule->type = 'wem_personaldatamanager';
        $objPDMModule->tstamp = time();
        $objPDMModule->save();
        $modules[$objPDMModule->type] = $objPDMModule;

        return $modules;
    }

    protected function updateLayouts(array $layouts, array $pages): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        $objLayout = LayoutModel::findOneById($config->getSgLayoutStandard());
        $objLayout->head = str_replace('{{config.core.page.privacy.url}}', $pages['privacy_politics']->getAbsoluteUrl(), $objLayout->head);
        $objLayout->save();

        $objLayout = LayoutModel::findOneById($config->getSgLayoutFullwidth());
        $objLayout->head = str_replace('{{config.core.page.privacy.url}}', $pages['privacy_politics']->getAbsoluteUrl(), $objLayout->head);
        $objLayout->save();
    }

    protected function createLayouts(int $themeId, array $modules): array
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        $layouts = [];

        $arrLayoutModules = [
            ['mod' => $modules['wem_sg_header']->id, 'col' => 'header', 'enable' => '1'],
            ['mod' => $modules['breadcrumb']->id, 'col' => 'main', 'enable' => '1'],
            ['mod' => 0, 'col' => 'main', 'enable' => '1'],
            ['mod' => $modules['wem_sg_footer']->id, 'col' => 'footer', 'enable' => '1'],
        ];
        $script = file_get_contents(Util::getPublicOrWebDirectory().'/bundles/wemsmartgear/examples/code_javascript_personnalise_1.js');
        if (\count($config->getSgGoogleFonts()) > 0) {
            $script = str_replace('{{config.googleFonts}}', "'".implode("','", $config->getSgGoogleFonts())."'", $script);
        } else {
            $script = preg_replace('/\/\/ -- GFONT(.*)\/\/ -- \/GFONT/s', '', $script);
        }

        $script = str_replace('{{config.framway.path}}', $config->getSgFramwayPath(), $script);
        switch ($config->getSgAnalytics()) {
            case CoreConfig::ANALYTICS_SYSTEM_NONE:
                $script = preg_replace('/\/\/ -- GTAG(.*)\/\/ -- \/GTAG/s', '', $script);
                $script = preg_replace('/\/\/ -- MATOMO(.*)\/\/ -- \/MATOMO/s', '', $script);
            break;
            case CoreConfig::ANALYTICS_SYSTEM_GOOGLE:
                $script = str_replace('{{config.analytics.google.id}}', $config->getSgAnalyticsGoogleId(), $script);
                $script = preg_replace('/\/\/ -- MATOMO(.*)\/\/ -- \/MATOMO/s', '', $script);
            break;
            case CoreConfig::ANALYTICS_SYSTEM_MATOMO:
                $script = str_replace('{{config.analytics.matomo.host}}', $config->getSgAnalyticsMatomoHost(), $script);
                $script = str_replace('{{config.analytics.matomo.id}}', $config->getSgAnalyticsMatomoId(), $script);
                $script = preg_replace('/\/\/ -- GTAG(.*)\/\/ -- \/GTAG/s', '', $script);
            break;
        }

        $head = file_get_contents(Util::getPublicOrWebDirectory().'/bundles/wemsmartgear/examples/balises_supplementaires_1.js');
        $head = str_replace('{{config.framway.path}}', $config->getSgFramwayPath(), $head);

        $objLayout = null !== $config->getSgLayoutStandard()
            ? LayoutModel::findOneById($config->getSgLayoutStandard()) ?? new LayoutModel()
            : new LayoutModel();
        $objLayout->pid = $themeId;
        $objLayout->name = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['LayoutStandardName'];
        $objLayout->rows = '3rw';
        $objLayout->cols = '1cl';
        $objLayout->loadingOrder = 'external_first';
        $objLayout->combineScripts = 1;
        $objLayout->viewport = 'width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=0';
        $objLayout->modules = serialize($arrLayoutModules);
        $objLayout->template = 'fe_page';
        $objLayout->webfonts = $config->getSgGoogleFonts();
        $objLayout->head = $head;
        $objLayout->script = $script;
        $objLayout->framework = serialize([]);
        $objLayout->tstamp = time();
        $objLayout->save();

        $layouts['standard'] = $objLayout;

        $objLayout = null !== $config->getSgLayoutFullwidth()
            ? LayoutModel::findOneById($config->getSgLayoutFullwidth()) ?? new LayoutModel()
            : new LayoutModel();
        $objLayout->pid = $themeId;
        $objLayout->name = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['LayoutStandardFullwidthName'];
        $objLayout->rows = '3rw';
        $objLayout->cols = '1cl';
        $objLayout->loadingOrder = 'external_first';
        $objLayout->combineScripts = 1;
        $objLayout->viewport = 'width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=0';
        $objLayout->modules = serialize($arrLayoutModules);
        $objLayout->template = 'fe_page_full';
        $objLayout->webfonts = $config->getSgGoogleFonts();
        $objLayout->head = $head;
        $objLayout->script = $script;
        $objLayout->framework = serialize([]);
        $objLayout->tstamp = time();
        $objLayout->save();

        $layouts['fullwidth'] = $objLayout;

        return $layouts;
    }

    protected function createUserGroups(): array
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        $objFolderClientFiles = FilesModel::findByPath(CoreConfig::DEFAULT_CLIENT_FILES_FOLDER);
        $objFolderClientLogos = FilesModel::findByPath(CoreConfig::DEFAULT_CLIENT_LOGOS_FOLDER);

        $userGroups = [];
        if (null !== $config->getSgUserGroupAdministrators()) {
            $objUserGroup = UserGroupModel::findOneById($config->getSgUserGroupAdministrators()) ?? new UserGroupModel();
        } else {
            $objUserGroup = new UserGroupModel();
        }
        $objUserGroup->tstamp = time();
        $objUserGroup->name = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['UsergroupAdministratorsName'];
        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);
        $userGroupManipulator
            ->addAllowedModules(['page', 'article', 'form', 'files', 'nc_notifications', 'user', 'log', 'maintenance', 'wem_sg_social_link'])
            ->addAllowedFields($this->getCorePermissions())
            ->addAllowedImageSizes(['proportional'])
            ->addAllowedFilemounts([$objFolderClientFiles->uuid, $objFolderClientLogos->uuid])
            ->addAllowedFileOperationPermissions(['f1', 'f2', 'f3', 'f4'])
            ->addAllowedElements([
                'headline',
                'text',
                'html',
                'table',
                'rsce_listIcons',
                'rsce_quote',
                'accordionStart',
                'accordionStop',
                'hyperlink',
                'image',
                'player',
                'youtube',
                'vimeo',
                'downloads',
                'module',
                'rsce_timeline',
                'grid-start',
                'grid-stop',
                'rsce_accordion',
                'rsce_counter',
                'rsce_hero',
                'rsce_heroStart',
                'rsce_heroStop',
                'rsce_ratings',
                'rsce_priceCards',
                'rsce_slider',
                'rsce_tabs',
                'rsce_testimonials',
                'rsce_pdfViewer',
                'rsce_blockCard',
                'form',
            ])
            ->addAllowedFormFields(array_keys($GLOBALS['TL_FFL']))
            ->addAllowedFieldsByTables(['tl_form', 'tl_form_field'])
            ->addAllowedFormPermissions(['create', 'delete'])
        ;
        $objUserGroup->modules = serialize(['page', 'article', 'form', 'files', 'nc_notifications', 'user', 'log', 'maintenance', 'wem_sg_social_link', 'wem_sg_social_link_config_categories']);
        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->save();
        $userGroups['administrators'] = $objUserGroup;

        if (null !== $config->getSgUserGroupAdministrators()) {
            $objUserGroup = UserGroupModel::findOneById($config->getSgUserGroupRedactors()) ?? new UserGroupModel();
            $this->userGroupWebmasterOldPermissions = null !== $objUserGroup->smartgear_permissions
            ? unserialize($objUserGroup->smartgear_permissions)
            : [];
        } else {
            $objUserGroup = new UserGroupModel();
        }

        $objUserGroup->tstamp = time();
        $objUserGroup->name = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['UsergroupRedactorsName'];
        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);
        $userGroupManipulator
            ->addAllowedModules(['article', 'files', 'form', 'wem_sg_social_link'])
            ->addAllowedFields($this->getCorePermissions())
            ->addAllowedImageSizes(['proportional'])
            ->addAllowedFilemounts([$objFolderClientFiles->uuid, $objFolderClientLogos->uuid])
            ->addAllowedFileOperationPermissions(['f1', 'f2', 'f3', 'f4'])
            ->addAllowedElements([
                'headline',
                'text',
                'html',
                'table',
                'rsce_listIcons',
                'rsce_quote',
                'accordionStart',
                'accordionStop',
                'hyperlink',
                'image',
                'player',
                'youtube',
                'vimeo',
                'downloads',
                'module',
                'rsce_timeline',
                'grid-start',
                'grid-stop',
                'rsce_accordion',
                'rsce_counter',
                'rsce_hero',
                'rsce_heroStart',
                'rsce_heroStop',
                'rsce_ratings',
                'rsce_priceCards',
                'rsce_slider',
                'rsce_tabs',
                'rsce_testimonials',
                'rsce_pdfViewer',
                'rsce_blockCard',
                'form',
            ])
            ->addAllowedFormFields(array_keys($GLOBALS['TL_FFL']))
            ->addAllowedFieldsByTables(['tl_form', 'tl_form_field'])
            ->addAllowedFormPermissions(['create', 'delete'])
        ;
        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->save();
        $userGroups['redactors'] = $objUserGroup;

        return $userGroups;
    }

    protected function createUsers(array $groups): array
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        $objUser = null !== $config->getSgUserWebmaster()
                    ? UserModel::findOneById($config->getSgUserWebmaster()) ?? new UserModel()
                    : new UserModel();
        $objUser->tstamp = time();
        $objUser->dateAdded = time();
        $objUser->username = CoreConfig::DEFAULT_USER_USERNAME;
        $objUser->name = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['UserWebmasterName'];
        $objUser->email = $config->getSgOwnerEmail();
        $objUser->language = 'fr';
        $objUser->backendTheme = 'flexible';
        $objUser->fullscreen = 1;
        $objUser->showHelp = 1;
        $objUser->thumbnails = 1;
        $objUser->useRTE = 1;
        $objUser->useCE = 1;
        $objUser->uploader = 'DropZone';
        // $objUser->password = \Contao\Encryption::hash('webmaster');
        $objUser->password = password_hash('webmaster', \PASSWORD_DEFAULT);
        $objUser->pwChange = 1;
        $objUser->groups = serialize([0 => $groups['redactors']->id]);
        $objUser->inherit = 'group';
        $objUser->save();

        return ['webmaster' => $objUser];
    }

    protected function createPageRoot(array $layouts, array $groups, array $users): PageModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        if (null !== $config->getSgPageRoot()) {
            $page = PageModel::findById($config->getSgPageRoot());
        } else {
            $page = PageModel::findOneBy('title', $config->getSgwebsiteTitle());
        }

        return Util::createPage($config->getSgwebsiteTitle(), 0, array_merge([
            'sorting' => 128,
            'type' => 'root',
            'language' => 'fr',
            'fallback' => 1,
            'adminEmail' => $config->getSgOwnerEmail(),
            'createSitemap' => 1,
            'sitemapName' => 'sitemap',
            'useSSL' => 1,
            'includeLayout' => 1,
            'layout' => $layouts['standard']->id,
            'includeChmod' => 1,
            'cuser' => $users['webmaster']->id,
            'cgroup' => $groups['administrators']->id,
            'chmod' => CoreConfig::DEFAULT_ROOTPAGE_CHMOD,
        ], null !== $page ? ['id' => $page->id] : []));
    }

    protected function createPageHome(PageModel $rootPage): PageModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        $page = PageModel::findOneById($config->getSgPageHome());
        $page = Util::createPage($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PageHomeTitle'], $rootPage->id, array_merge([
            'sorting' => 128,
            'alias' => 'index',
            'sitemap' => 'default',
            'hide' => 1,
        ], null !== $page ? ['id' => $page->id] : []));

        return $page;
    }

    protected function createPage404(array $modules, PageModel $rootPage): PageModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        $page = PageModel::findOneById($config->getSgPage404());
        $page = Util::createPage($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['Page404Title'], $rootPage->id, array_merge([
            'sorting' => 256,
            'sitemap' => 'default',
            'hide' => 1,
            'type' => 'error_404',
        ], null !== $page ? ['id' => $page->id] : []));

        return $page;
    }

    protected function createPageLegalNotice(PageModel $rootPage): PageModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        $page = PageModel::findOneById($config->getSgPageLegalNotice());
        $page = Util::createPage($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PageLegalNoticeTitle'], $rootPage->id, array_merge([
            'sorting' => 386,
            'sitemap' => 'default',
            'description' => sprintf($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PageLegalNoticeDescription'], $config->getSgWebsiteTitle()),
            'hide' => 1,
        ], null !== $page ? ['id' => $page->id] : []));

        return $page;
    }

    protected function createPagePrivacyPolitics(PageModel $rootPage): PageModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        $page = PageModel::findOneById($config->getSgPagePrivacyPolitics());
        $page = Util::createPage($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PagePrivacyPoliticsTitle'], $rootPage->id, array_merge([
            'sorting' => 512,
            'sitemap' => 'default',
            'description' => sprintf($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PagePrivacyPoliticsDescription'], $config->getSgWebsiteTitle()),
            'hide' => 1,
        ], null !== $page ? ['id' => $page->id] : []));

        return $page;
    }

    protected function createPageSitemap(array $modules, PageModel $rootPage): PageModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        $page = PageModel::findOneById($config->getSgPageSitemap());
        $page = Util::createPage($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PageSitemapTitle'], $rootPage->id, array_merge([
            'sorting' => 640,
            'sitemap' => 'default',
            'description' => sprintf($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PageSitemapDescription'], $config->getSgWebsiteTitle()),
            'hide' => 1,
        ], null !== $page ? ['id' => $page->id] : []));

        return $page;
    }

    protected function createPages(array $layouts, array $groups, array $users, array $modules): array
    {
        $pages = [];
        $pages['root'] = $this->createPageRoot($layouts, $groups, $users, $modules);
        $pages['home'] = $this->createPageHome($pages['root']);
        $pages['404'] = $this->createPage404($modules, $pages['root']);
        $pages['legal_notice'] = $this->createPageLegalNotice($pages['root']);
        $pages['privacy_politics'] = $this->createPagePrivacyPolitics($pages['root']);
        $pages['sitemap'] = $this->createPageSitemap($modules, $pages['root']);

        return $pages;
    }

    protected function createArticleHome(PageModel $page): ArticleModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        return ArticleModel::findOneById($config->getSgArticleHome()) ?? Util::createArticle($page);
    }

    protected function createArticle404(PageModel $page): ArticleModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        return ArticleModel::findOneById($config->getSgArticle404()) ?? Util::createArticle($page);
    }

    protected function createArticleLegalNotice(PageModel $page): ArticleModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        return ArticleModel::findOneById($config->getSgArticleLegalNotice()) ?? Util::createArticle($page);
    }

    protected function createArticlePrivacyPolitics(PageModel $page): ArticleModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        return ArticleModel::findOneById($config->getSgArticlePrivacyPolitics()) ?? Util::createArticle($page);
    }

    protected function createArticleSitemap(PageModel $page): ArticleModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        return ArticleModel::findOneById($config->getSgArticleSitemap()) ?? Util::createArticle($page);
    }

    protected function createArticles(array $pages): array
    {
        $articles = [];
        $articles['home'] = $this->createArticleHome($pages['home']);
        $articles['404'] = $this->createArticle404($pages['404']);
        $articles['legal_notice'] = $this->createArticleLegalNotice($pages['legal_notice']);
        $articles['privacy_politics'] = $this->createArticlePrivacyPolitics($pages['privacy_politics']);
        $articles['sitemap'] = $this->createArticleSitemap($pages['sitemap']);

        return $articles;
    }

    protected function createContent404(ArticleModel $article, array $modules): array
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        $content = ContentModel::findById($config->getSgContent404Headline());
        $contents['headline'] = Util::createContent($article, array_merge([
            'headline' => serialize(['unit' => 'h1', 'value' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['Page404Headline']]), 'text' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['Page404Text'],
        ], ['id' => null !== $content ? $content->id : null]));

        $content = ContentModel::findById($config->getSgContent404Sitemap());
        $contents['sitemap'] = Util::createContent($article, array_merge([
            'type' => 'module', 'module' => $modules['sitemap']->id,
        ], ['id' => null !== $content ? $content->id : null]));

        return $contents;
    }

    protected function createContentLegalNotice(ArticleModel $article, array $modules): ContentModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        $content = ContentModel::findById($config->getSgContentLegalNotice());

        $strText = file_get_contents(Util::getPublicOrWebDirectory().'/bundles/wemsmartgear/examples/legal-notices_1.html');
        $strHtml = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PageLegalNoticeTextDefault'];
        if ($strText) {
            /**
             * 1: URL du site entière
             * 2: URL du site sans https://
             * 3: Nom de l'entreprise
             * 4: Statut de l'entreprise
             * 5: Siret de l'entreprise
             * 6: Adresse du siège de l'entreprise
             * 7: Adresse mail de l'entreprise
             * 8: Nom & Adresse de l'hébergeur.
             */
            $strHtml = sprintf(
                    $strText,
                    $config->getSgOwnerDomain() ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled'],
                    str_replace('https://', '', $config->getSgOwnerDomain()) ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled'],
                    $config->getSgOwnerName() ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled'],
                    $config->getSgOwnerStatus() ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled'],
                    $config->getSgOwnerSIRET() ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled'],
                    $config->getSgOwnerStreet().' '.$config->getSgOwnerPostal().' '.$config->getSgOwnerCity().' '.$config->getSgOwnerRegion().' '.$config->getSgOwnerCountry() ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled'],
                    $config->getSgOwnerEmail() ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled'],
                    $config->getSgOwnerHost() ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled']
                );
        }
        $objContent = Util::createContent($article, array_merge([
            'headline' => serialize(['unit' => 'h1', 'value' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PageLegalNoticeHeadline']]), 'text' => $strHtml,
        ], ['id' => null !== $content ? $content->id : null]));

        return $objContent;
    }

    protected function createContentPrivacyPolitics(PageModel $page, ArticleModel $article, array $modules): ContentModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        $content = ContentModel::findById($config->getSgContentPrivacyPolitics());

        $strText = file_get_contents(Util::getPublicOrWebDirectory().'/bundles/wemsmartgear/examples/privacy_1.html');
        $strHtml = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PagePrivacyPoliticsTextDefault'];
        if ($strText) {
            /**
             * 1: Nom de la boite
             * 2: Adresse
             * 3: SIRET
             * 4: URL de la page confidentialité
             * 5: Date
             * 6: Contact email.
             */
            $strHtml = sprintf(
                $strText,
                $config->getSgOwnerName() ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled'],
                $config->getSgOwnerStreet().' '.$config->getSgOwnerPostal().' '.$config->getSgOwnerCity().' '.$config->getSgOwnerRegion().' '.$config->getSgOwnerCountry() ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled'],
                $config->getSgOwnerSIRET() ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled'],
                $page->getAbsoluteUrl(),
                date('d/m/Y'),
                $config->getSgOwnerEmail() ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled']
            );
        }
        $objContent = Util::createContent($article, array_merge([
            'text' => $strHtml,
        ], ['id' => null !== $content ? $content->id : null]));

        return $objContent;
    }

    protected function createContentSitemap(ArticleModel $article, array $modules): ContentModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        $content = ContentModel::findById($config->getSgContentSitemap());

        return Util::createContent($article, array_merge([
            'type' => 'module', 'module' => $modules['sitemap']->id,
        ], ['id' => null !== $content ? $content->id : null]));
    }

    protected function createContents(array $pages, array $articles, array $modules): array
    {
        $contents = [];
        $contents['404'] = $this->createContent404($articles['404'], $modules);
        $contents['legal_notice'] = $this->createContentLegalNotice($articles['legal_notice'], $modules);
        $contents['privacy_politics'] = $this->createContentPrivacyPolitics($pages['privacy_politics'], $articles['privacy_politics'], $modules);
        $contents['sitemap'] = $this->createContentSitemap($articles['sitemap'], $modules);

        return $contents;
    }

    protected function createModules2(int $themeId, array $pages): array
    {
        $registeredModules = $this->getConfigModulesAsFormattedArray();
        $modules = [];
        // Header - Logo

        $objCustomNavModule = \array_key_exists('customnav', $registeredModules)
                            ? ModuleModel::findOneById($registeredModules['customnav']) ?? new ModuleModel()
                            : new ModuleModel()
                            ;
        $objCustomNavModule->pid = $themeId;
        $objCustomNavModule->tstamp = time();
        $objCustomNavModule->type = 'customnav';
        $objCustomNavModule->name = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['ModuleFooterNavName'];
        $objCustomNavModule->pages = [$pages['legal_notice']->id, $pages['privacy_politics']->id, $pages['sitemap']->id];
        $objCustomNavModule->navigationTpl = 'nav_default';
        $objCustomNavModule->save();
        $modules[$objCustomNavModule->type] = $objCustomNavModule;

        return $modules;
    }

    protected function createNotificationGateways(): array
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        $nc = [];
        $objGateway = null !== $config->getSgNotificationGatewayEmail()
            ? \NotificationCenter\Model\Gateway::findOneById($config->getSgNotificationGatewayEmail()) ?? new \NotificationCenter\Model\Gateway()
            : new \NotificationCenter\Model\Gateway();
        $objGateway->tstamp = time();
        $objGateway->title = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['NotificationGatewayEmailSmartgearTitle'];
        $objGateway->type = 'email';
        $objGateway->save();

        $nc['email'] = $objGateway;

        return $nc;
    }

    protected function uploadLogo(): File
    {
        $fm = Files::getInstance();
        $logoFolder = new Folder(CoreConfig::DEFAULT_CLIENT_LOGOS_FOLDER);
        if (!$fm->move_uploaded_file($_FILES['sgWebsiteLogo']['tmp_name'], CoreConfig::DEFAULT_CLIENT_LOGOS_FOLDER.\DIRECTORY_SEPARATOR.$_FILES['sgWebsiteLogo']['name'].'_tmp')) {
            throw new Exception(sprintf('Unable to upload logo to "%s".', CoreConfig::DEFAULT_CLIENT_LOGOS_FOLDER.\DIRECTORY_SEPARATOR.$_FILES['sgWebsiteLogo']['name'].'_tmp'));
        }
        $objFile = new File(CoreConfig::DEFAULT_CLIENT_LOGOS_FOLDER.\DIRECTORY_SEPARATOR.$_FILES['sgWebsiteLogo']['name'].'_tmp');
        $objFile->renameTo(CoreConfig::DEFAULT_CLIENT_LOGOS_FOLDER.\DIRECTORY_SEPARATOR.$_FILES['sgWebsiteLogo']['name']);

        return $objFile;
    }

    protected function updateModuleConfiguration(): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        $fonts = [];
        if (!empty(Input::post('sgGoogleFonts'))) {
            $fonts = explode(',', Input::post('sgGoogleFonts'));
            foreach ($fonts as $key => $value) {
                $fonts[$key] = trim($value);
            }
        }

        $config->setSgOwnerName(Input::post('sgOwnerName'));
        $config->setSgOwnerStatus(Input::post('sgOwnerStatus'));
        $config->setSgOwnerSiret(Input::post('sgOwnerSiret'));
        $config->setSgOwnerStreet(Input::post('sgOwnerStreet'));
        $config->setSgOwnerPostal(Input::post('sgOwnerPostal'));
        $config->setSgOwnerCity(Input::post('sgOwnerCity'));
        $config->setSgOwnerRegion(Input::post('sgOwnerRegion'));
        $config->setSgOwnerCountry(Input::post('sgOwnerCountry'));
        $config->setSgOwnerEmail(Input::post('sgOwnerEmail'));
        $config->setSgOwnerDomain(Input::post('sgOwnerDomain'));
        $config->setSgOwnerHost(Input::post('sgOwnerHost'));
        $config->setSgOwnerDpoName(Input::post('sgOwnerDpoName'));
        $config->setSgOwnerDpoEmail(Input::post('sgOwnerDpoEmail'));
        $config->setSgGoogleFonts($fonts);

        $this->configurationManager->save($config);
    }

    protected function updateModuleConfigurationLogo(File $objFileLogo): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        $config->setSgOwnerLogo($objFileLogo->path);

        $this->configurationManager->save($config);
    }

    protected function updateModuleConfigurationTheme(int $themeId): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        $config->setSgTheme($themeId);

        $this->configurationManager->save($config);
    }

    protected function updateModuleConfigurationModules(array $modules): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        $formattedModules = [];
        foreach ($modules as $key => $objModule) {
            $formattedModules[] = ['type' => $objModule->type, 'id' => $objModule->id];
        }

        $config->setSgModules($formattedModules);

        $this->configurationManager->save($config);
    }

    protected function updateModuleConfigurationUserAndGroups(array $users, array $groups): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        $config->setSgUserWebmaster((int) $users['webmaster']->id);
        $config->setSgUserGroupRedactors((int) $groups['redactors']->id);
        $config->setSgUserGroupAdministrators((int) $groups['administrators']->id);

        $this->configurationManager->save($config);
    }

    protected function updateModuleConfigurationPages(array $pages): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        $config->setSgPageRoot((int) $pages['root']->id);
        $config->setSgPageHome((int) $pages['home']->id);
        $config->setSgPage404((int) $pages['404']->id);
        $config->setSgPageLegalNotice((int) $pages['legal_notice']->id);
        $config->setSgPagePrivacyPolitics((int) $pages['privacy_politics']->id);
        $config->setSgPageSitemap((int) $pages['sitemap']->id);

        $this->configurationManager->save($config);
    }

    protected function updateModuleConfigurationArticles(array $articles): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        $config->setSgArticleHome((int) $articles['home']->id);
        $config->setSgArticle404((int) $articles['404']->id);
        $config->setSgArticleLegalNotice((int) $articles['legal_notice']->id);
        $config->setSgArticlePrivacyPolitics((int) $articles['privacy_politics']->id);
        $config->setSgArticleSitemap((int) $articles['sitemap']->id);

        $this->configurationManager->save($config);
    }

    protected function updateModuleConfigurationContents(array $contents): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        $config->setSgContent404Headline((int) $contents['404']['headline']->id);
        $config->setSgContent404Sitemap((int) $contents['404']['sitemap']->id);
        $config->setSgContentLegalNotice((int) $contents['legal_notice']->id);
        $config->setSgContentPrivacyPolitics((int) $contents['privacy_politics']->id);
        $config->setSgContentSitemap((int) $contents['sitemap']->id);

        $this->configurationManager->save($config);
    }

    protected function updateModuleConfigurationLayouts(array $layouts): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        $config->setSgLayoutStandard((int) $layouts['standard']->id);
        $config->setSgLayoutFullwidth((int) $layouts['fullwidth']->id);

        $this->configurationManager->save($config);
    }

    protected function updateModuleConfigurationNotificationGateways(array $nc): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        $config->setSgNotificationGatewayEmail((int) $nc['email']->id);

        $this->configurationManager->save($config);
    }

    protected function getConfigModulesAsFormattedArray(): array
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        $registeredModules = [];
        $registeredModulesRaw = $config->getSgModules();
        foreach ($registeredModulesRaw as $registeredModuleRaw) {
            $registeredModules[$registeredModuleRaw->type] = (int) $registeredModuleRaw->id;
        }

        return $registeredModules;
    }

    /**
     * Return permissions concerned by this component.
     *
     * @return array
     */
    protected function getCorePermissions()
    {
        return [
            0 => 'tl_article::title',
            1 => 'tl_article::alias',
            2 => 'tl_article::cssID',
            3 => 'tl_article::published',
            4 => 'tl_article::start',
            5 => 'tl_article::stop',
            6 => 'tl_content::type',
            7 => 'tl_content::headline',
            8 => 'tl_content::text',
            9 => 'tl_content::addImage',
            10 => 'tl_content::overwriteMeta',
            11 => 'tl_content::singleSRC',
            12 => 'tl_content::alt',
            13 => 'tl_content::imageTitle',
            14 => 'tl_content::size',
            15 => 'tl_content::imagemargin',
            16 => 'tl_content::imageUrl',
            17 => 'tl_content::fullsize',
            18 => 'tl_content::caption',
            19 => 'tl_content::floating',
            20 => 'tl_content::html',
            21 => 'tl_content::listtype',
            22 => 'tl_content::listitems',
            23 => 'tl_content::tableitems',
            24 => 'tl_content::summary',
            25 => 'tl_content::thead',
            26 => 'tl_content::tfoot',
            27 => 'tl_content::tleft',
            28 => 'tl_content::sortable',
            29 => 'tl_content::sortIndex',
            30 => 'tl_content::sortOrder',
            31 => 'tl_content::mooHeadline',
            32 => 'tl_content::mooStyle',
            33 => 'tl_content::mooClasses',
            34 => 'tl_content::highlight',
            35 => 'tl_content::code',
            36 => 'tl_content::url',
            37 => 'tl_content::target',
            38 => 'tl_content::overwriteLink',
            39 => 'tl_content::titleText',
            40 => 'tl_content::linkTitle',
            41 => 'tl_content::embed',
            42 => 'tl_content::rel',
            43 => 'tl_content::useImage',
            44 => 'tl_content::multiSRC',
            45 => 'tl_content::useHomeDir',
            46 => 'tl_content::perRow',
            47 => 'tl_content::perPage',
            48 => 'tl_content::numberOfItems',
            49 => 'tl_content::sortBy',
            50 => 'tl_content::metaIgnore',
            51 => 'tl_content::galleryTpl',
            52 => 'tl_content::customTpl',
            53 => 'tl_content::playerSRC',
            54 => 'tl_content::youtube',
            55 => 'tl_content::vimeo',
            56 => 'tl_content::posterSRC',
            57 => 'tl_content::playerSize',
            58 => 'tl_content::playerOptions',
            59 => 'tl_content::playerStart',
            60 => 'tl_content::playerStop',
            61 => 'tl_content::playerCaption',
            62 => 'tl_content::playerAspect',
            63 => 'tl_content::playerPreload',
            64 => 'tl_content::playerColor',
            65 => 'tl_content::youtubeOptions',
            66 => 'tl_content::vimeoOptions',
            67 => 'tl_content::sliderDelay',
            68 => 'tl_content::sliderSpeed',
            69 => 'tl_content::sliderStartSlide',
            70 => 'tl_content::sliderContinuous',
            71 => 'tl_content::cteAlias',
            72 => 'tl_content::articleAlias',
            73 => 'tl_content::article',
            74 => 'tl_content::form',
            75 => 'tl_content::module',
            76 => 'tl_content::protected',
            77 => 'tl_content::groups',
            78 => 'tl_content::guests',
            79 => 'tl_content::cssID',
            80 => 'tl_content::invisible',
            81 => 'tl_content::start',
            82 => 'tl_content::stop',
            83 => 'tl_content::rsce_data',
            84 => 'tl_content::grid_preset',
            85 => 'tl_content::grid_row_class',
            86 => 'tl_content::grid_rows',
            87 => 'tl_content::grid_cols',
            88 => 'tl_content::grid_items',
            89 => 'tl_nc_language::language',
            90 => 'tl_nc_language::fallback',
            91 => 'tl_nc_language::recipients',
            92 => 'tl_nc_language::attachment_tokens',
            93 => 'tl_nc_language::attachments',
            94 => 'tl_nc_language::attachment_templates',
            95 => 'tl_nc_language::email_sender_name',
            96 => 'tl_nc_language::email_sender_address',
            97 => 'tl_nc_language::email_recipient_cc',
            98 => 'tl_nc_language::email_recipient_bcc',
            99 => 'tl_nc_language::email_replyTo',
            100 => 'tl_nc_language::email_subject',
            101 => 'tl_nc_language::email_mode',
            102 => 'tl_nc_language::email_text',
            103 => 'tl_nc_language::email_html',
            104 => 'tl_nc_language::email_external_images',
            105 => 'tl_nc_language::file_name',
            106 => 'tl_nc_language::file_storage_mode',
            107 => 'tl_nc_language::file_content',
            108 => 'tl_page::title',
            109 => 'tl_page::alias',
            110 => 'tl_page::type',
            111 => 'tl_page::pageTitle',
            112 => 'tl_page::language',
            113 => 'tl_page::robots',
            114 => 'tl_page::description',
            115 => 'tl_page::redirect',
            116 => 'tl_page::jumpTo',
            117 => 'tl_page::redirectBack',
            118 => 'tl_page::url',
            119 => 'tl_page::target',
            120 => 'tl_page::noSearch',
            121 => 'tl_page::sitemap',
            122 => 'tl_page::hide',
            123 => 'tl_page::published',
            124 => 'tl_page::start',
            125 => 'tl_page::stop',
            126 => 'tl_user::username',
            127 => 'tl_user::name',
            128 => 'tl_user::email',
            129 => 'tl_user::language',
            130 => 'tl_user::backendTheme',
            131 => 'tl_user::fullscreen',
            132 => 'tl_user::uploader',
            133 => 'tl_user::showHelp',
            134 => 'tl_user::thumbnails',
            135 => 'tl_user::useRTE',
            136 => 'tl_user::useCE',
            137 => 'tl_user::password',
            138 => 'tl_user::pwChange',
            139 => 'tl_user::admin',
            140 => 'tl_user::groups',
            141 => 'tl_user::inherit',
            142 => 'tl_user::modules',
            143 => 'tl_user::themes',
            144 => 'tl_user::pagemounts',
            145 => 'tl_user::alpty',
            146 => 'tl_user::filemounts',
            147 => 'tl_user::fop',
            148 => 'tl_user::imageSizes',
            149 => 'tl_user::forms',
            150 => 'tl_user::formp',
            151 => 'tl_user::amg',
            152 => 'tl_user::disable',
            153 => 'tl_user::start',
            154 => 'tl_user::stop',
            155 => 'tl_user::session',
            156 => 'tl_content::grid_gap',
            157 => 'tl_article::styleManager',
            158 => 'tl_content::styleManager',
        ];
    }

    protected function updateUserGroups(): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        $this->updateUserGroup(UserGroupModel::findOneById($config->getSgUserGroupRedactors()), $config);
        $this->updateUserGroup(UserGroupModel::findOneById($config->getSgUserGroupAdministrators()), $config);

        foreach ($this->userGroupUpdaters as $submoduleStep) {
            $submodule = $submoduleStep->getModule();
            $submoduleConfig = $config->getSubmoduleConfig($submodule);
            if ($submoduleConfig->getSgInstallComplete()) {
                if ('blog' === $submodule) {
                    $submoduleStep->updateUserGroups(\in_array(SmartgearPermissions::BLOG_EXPERT, $this->userGroupWebmasterOldPermissions, true));
                } elseif ('events' === $submodule) {
                    $submoduleStep->updateUserGroups(\in_array(SmartgearPermissions::EVENTS_EXPERT, $this->userGroupWebmasterOldPermissions, true));
                } elseif ('extranet' === $submodule) {
                    $objModules = Module::findItems(['id' => $submoduleConfig->getContaoModulesIds()]);
                    $modules = [];
                    if ($objModules) {
                        while ($objModules->next()) {
                            $modules[] = $objModules->current();
                        }
                    }
                    $submoduleStep->updateUserGroups($modules);
                } else {
                    $submoduleStep->updateUserGroups();
                }
            }
        }
    }

    protected function updateUserGroup(UserGroupModel $objUserGroup, CoreConfig $config): void
    {
        $objFolderClient = FilesModel::findByPath(CoreConfig::DEFAULT_CLIENT_FILES_FOLDER);
        if (!$objFolderClient) {
            throw new Exception('Unable to find the folder');
        }
        $objFolderLogos = FilesModel::findByPath(CoreConfig::DEFAULT_CLIENT_LOGOS_FOLDER);
        if (!$objFolderLogos) {
            throw new Exception('Unable to find the folder');
        }

        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);
        $userGroupManipulator
            ->addAllowedFilemounts($config->getContaoFoldersIds())
            ->addAllowedPagemounts($config->getContaoPagesIds())
            // ->addAllowedModules(Module::getTypesByIds($config->getContaoModulesIds()))
        ;

        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->save();
    }
}
