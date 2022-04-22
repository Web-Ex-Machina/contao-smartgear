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
use WEM\SmartgearBundle\Classes\Backend\ConfigurationStep;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Config\Core as CoreConfig;
use WEM\UtilsBundle\Classes\Files as WEMFiles;
use WEM\UtilsBundle\Classes\StringUtil as WEMStringUtil;

class Website extends ConfigurationStep
{
    /** @var ConfigurationManager */
    protected $configurationManager;

    protected $strTemplate = 'be_wem_sg_install_block_configuration_step_core_website';

    public function __construct(
        string $module,
        string $type,
        ConfigurationManager $configurationManager
    ) {
        parent::__construct($module, $type);
        $this->configurationManager = $configurationManager;
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
        $this->addTextField('sgOwnerRegion', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerRegion'], $config->getSgOwnerRegion(), true);
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

        if (empty(Input::post('sgOwnerRegion'))) {
            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerRegionEmpty']);
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
        if (!empty($_FILES)) {
            $objFileLogo = $this->uploadLogo();
            $this->updateModuleConfigurationLogo($objFileLogo);
        }

        $themeId = $this->createTheme();
        $this->updateModuleConfigurationTheme($themeId);
        $modules = $this->createModules($themeId);
        $this->updateModuleConfigurationModules($modules);
        $layouts = $this->createLayouts($themeId, $modules);
        $groups = $this->createUserGroups();
        $users = $this->createUsers($groups);
        $pages = $this->createPages($layouts, $groups, $users, $modules);
        $modules = array_merge($this->createModules2($themeId, $pages), $modules);
        $this->createNotificationGateways();
    }

    protected function createClientFilesFolders(): void
    {
        $clientFilesFolder = new Folder(CoreConfig::DEFAULT_CLIENT_FILES_FOLDER);
        $clientLogosFolder = new Folder(CoreConfig::DEFAULT_CLIENT_LOGOS_FOLDER);
    }

    protected function createTheme()
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        // Create the Smartgear main theme
        $objTheme = ModuleModel::findOneByName('Smartgear '.$config->getSgWebsiteTitle()) ?? new ThemeModel();
        $objTheme->tstamp = time();
        $objTheme->name = 'Smartgear '.$config->getSgWebsiteTitle();
        $objTheme->author = 'Web ex Machina';
        $objTheme->templates = sprintf('templates/%s', WEMStringUtil::generateAlias($config->getSgWebsiteTitle()));
        $objTheme->save();

        return (int) $objTheme->id;
    }

    protected function createModules(int $themeId): array
    {
        $modules = [];
        // Header - Logo

        $objHeaderModule = ModuleModel::findOneByName('HEADER') ?? new ModuleModel();
        $objHeaderModule->pid = $themeId;
        $objHeaderModule->tstamp = time();
        $objHeaderModule->type = 'wem_sg_header';
        $objHeaderModule->name = 'HEADER';
        // $objHeaderModule->wem_sg_header_preset = 'classic';
        // $objHeaderModule->wem_sg_header_sticky = 1;
        // $objHeaderModule->wem_sg_navigation = 'classic';
        // $objHeaderModule->wem_sg_header_logo = $objLogoModel->uuid;
        // $objHeaderModule->wem_sg_header_logo_size = 'a:3:{i:0;s:0:"";i:1;s:2:"75";i:2;s:12:"proportional";}';
        // $objHeaderModule->wem_sg_header_logo_alt = 'Logo '.$this->sgConfig['websiteTitle'];
        $objHeaderModule->save();
        $modules[$objHeaderModule->type] = $objHeaderModule;

        // Breadcrumb
        $objBreadcrumbModule = ModuleModel::findOneByName($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['ModuleBreadcrumbName']) ?? new ModuleModel();
        $objBreadcrumbModule->pid = $themeId;
        $objBreadcrumbModule->tstamp = time();
        $objBreadcrumbModule->type = 'breadcrumb';
        $objBreadcrumbModule->name = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['ModuleBreadcrumbName'];
        $objBreadcrumbModule->save();
        $modules[$objBreadcrumbModule->type] = $objBreadcrumbModule;

        // Main - Articles
        // $arrLayoutModules[] = ['mod' => 0, 'col' => 'main', 'enable' => '1'];

        // Footer
        $objFooterModule = ModuleModel::findOneByName('FOOTER') ?? new ModuleModel();
        $objFooterModule->pid = $themeId;
        $objFooterModule->tstamp = time();
        $objFooterModule->type = 'wem_sg_footer';
        $objFooterModule->name = 'FOOTER';
        $objFooterModule->html = file_get_contents(TL_ROOT.'/public/bundles/wemsmartgear/examples/footer_1.html');
        $objFooterModule->save();
        $modules[$objFooterModule->type] = $objFooterModule;

        $objSitemapModule = ModuleModel::findOneByName($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['ModuleSitemapName']) ?? new ModuleModel();
        $objSitemapModule->pid = $themeId;
        $objSitemapModule->tstamp = time();
        $objSitemapModule->type = 'sitemap';
        $objSitemapModule->name = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['ModuleSitemapName'];
        $objSitemapModule->save();
        $modules[$objSitemapModule->type] = $objSitemapModule;

        return $modules;
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
        $script = file_get_contents(TL_ROOT.'/public/bundles/wemsmartgear/examples/code_javascript_personnalise_1.js');
        $script = str_replace('{{config.googleFonts}}', "'".implode("','", $config->getSgGoogleFonts())."'", $script);
        $script = str_replace('{{config.framway.path}}', $config->getSgFramwayPath(), $script);

        $head = file_get_contents(TL_ROOT.'/public/bundles/wemsmartgear/examples/balises_supplementaires_1.js');
        $head = str_replace('{{config.framway.path}}', $config->getSgFramwayPath(), $head);

        // $arrCssFiles = [];
        // $arrJsFiles = [];
        // $objFile = FilesModel::findOneByPath($config->getSgFramwayPath().'/css/vendor.css');
        // $arrCssFiles[] = $objFile->uuid;
        // $objFile = FilesModel::findOneByPath($config->getSgFramwayPath().'/css/framway.css');
        // $arrCssFiles[] = $objFile->uuid;
        // $objFile = FilesModel::findOneByPath($config->getSgFramwayPath().'/js/vendor.js');
        // $arrJsFiles[] = $objFile->uuid;
        // $objFile = FilesModel::findOneByPath($config->getSgFramwayPath().'/js/framway.js');
        // $arrJsFiles[] = $objFile->uuid;

        $objLayout = LayoutModel::findOneByName($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['LayoutStandardName']) ?? new LayoutModel();
        $objLayout->pid = $themeId;
        $objLayout->name = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['LayoutStandardName'];
        $objLayout->rows = '3rw';
        $objLayout->cols = '1cl';
        // $objLayout->external = serialize($arrCssFiles);
        $objLayout->loadingOrder = 'external_first';
        $objLayout->combineScripts = 1;
        $objLayout->viewport = 'width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=0';
        // $objLayout->externalJs = serialize($arrJsFiles);
        $objLayout->modules = serialize($arrLayoutModules);
        $objLayout->template = 'fe_page';
        // $objLayout->webfonts = '';
        $objLayout->head = $head;
        $objLayout->script = $script;
        $objLayout->save();

        $layouts['standard'] = $objLayout;

        $objLayout = LayoutModel::findOneByName($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['LayoutStandardFullwidthName']) ?? new LayoutModel();
        $objLayout->pid = $themeId;
        $objLayout->name = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['LayoutStandardFullwidthName'];
        $objLayout->rows = '3rw';
        $objLayout->cols = '1cl';
        // $objLayout->external = serialize($arrCssFiles);
        $objLayout->loadingOrder = 'external_first';
        $objLayout->combineScripts = 1;
        $objLayout->viewport = 'width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=0';
        // $objLayout->externalJs = serialize($arrJsFiles);
        $objLayout->modules = serialize($arrLayoutModules);
        $objLayout->template = 'fe_page_full';
        // $objLayout->webfonts = '';
        $objLayout->head = $head;
        $objLayout->script = $script;
        $objLayout->save();

        $layouts['fullwidth'] = $objLayout;

        return $layouts;
    }

    protected function createUserGroups(): array
    {
        $userGroups = [];

        $objUserGroup = UserGroupModel::findOneByName($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['UsergroupAdministratorsName']) ?? new UserGroupModel();
        $objUserGroup->tstamp = time();
        $objUserGroup->name = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['UsergroupAdministratorsName'];
        $objUserGroup->modules = serialize(['page', 'article', 'form', 'files', 'nc_notifications', 'user', 'log', 'maintenance']);
        // $objUserGroup->pagemounts = '';
        // $objUserGroup->alpty = 'a:3:{i:0;s:7:"regular";i:1;s:7:"forward";i:2;s:8:"redirect";}';
        // $objUserGroup->filemounts = 'a:1:{i:0;s:16:"'.$objMediaFolder->getModel()->uuid.'";}';
        // $objUserGroup->fop = 'a:4:{i:0;s:2:"f1";i:1;s:2:"f2";i:2;s:2:"f3";i:3;s:2:"f4";}';
        // $objUserGroup->imageSizes = 'a:3:{i:0;s:12:"proportional";i:1;s:3:"box";i:2;s:4:"crop";}';
        $objUserGroup->alexf = Util::addPermissions($this->getCorePermissions());
        $objUserGroup->elements = serialize([
            'headline',
            'text',
            'html',
            'table',
            'rsce_listIcons',
            'rsce_quote',
            'accordionStart',
            'accordionStop',
            'sliderStart',
            'sliderStop',
            'hyperlink',
            'image',
            'player',
            'youtube',
            'vimeo',
            'downloads',
            'module',
            'template',
            'rsce_timeline',
            'grid-start',
            'grid-stop',
            'rsce_accordionFW',
            'rsce_block-img',
            'rsce_counterFW',
            'rsce_foldingbox',
            'rsce_gridGallery',
            'rsce_heroFW',
            'rsce_heroFWStart',
            'rsce_heroFWStop',
            'rsce_notations',
            'rsce_priceCards',
            'rsce_sliderFW',
            'rsce_tabs',
            'rsce_testimonials',
        ]);
        $objUserGroup->save();
        $userGroups['administrators'] = $objUserGroup;

        $objUserGroup = UserGroupModel::findOneByName($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['UsergroupRedactorsName']) ?? new UserGroupModel();
        $objUserGroup->tstamp = time();
        $objUserGroup->name = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['UsergroupRedactorsName'];
        $objUserGroup->modules = 'a:2:{i:0;s:4:"article";i:1;s:5:"files";}';
        $objUserGroup->modules = serialize(['article', 'files']);
        $objUserGroup->save();
        $userGroups['redactors'] = $objUserGroup;

        return $userGroups;
    }

    protected function createUsers(array $groups): array
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        $objUser = UserModel::findOneByName($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['UserWebmasterName']) ?? new UserModel();
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
        $objUser->groups = serialize([0 => $groups['administrators']->id]);
        $objUser->inherit = 'group';
        $objUser->save();

        return ['webmaster' => $objUser];
    }

    protected function createPageRoot(array $layouts, array $groups, array $users): PageModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        $page = PageModel::findOneBy('title', $config->getSgwebsiteTitle());

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
        $page = PageModel::findOneBy('title', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PageHomeTitle']);
        $page = Util::createPage($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PageHomeTitle'], $rootPage->id, array_merge([
            'sorting' => 128,
            'alias' => '/',
            'sitemap' => 'default',
            'hide' => 1,
        ], null !== $page ? ['id' => $page->id] : []));
        $objArticle = ArticleModel::findByPid($page->id) ?? Util::createArticle($page);

        return $page;
    }

    protected function createPage404(array $modules, PageModel $rootPage): PageModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        $page = PageModel::findOneBy('title', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['Page404Title']);
        $page = Util::createPage($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['Page404Title'], $rootPage->id, array_merge([
            'sorting' => 256,
            'sitemap' => 'default',
            'hide' => 1,
        ], null !== $page ? ['id' => $page->id] : []));
        $objArticle = ArticleModel::findOneBy(['pid = ?', 'alias = ?'], [$page->id, $page->alias]) ?? Util::createArticle($page);
        $content = ContentModel::findByPid($objArticle->id);
        if ($content) {
            while ($content->next()) {
                $content->current()->delete();
            }
        }
        $objContent = Util::createContent($objArticle, [
            'headline' => serialize(['unit' => 'h1', 'value' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['Page404Headline']]), 'text' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['Page404Text'],
        ]);
        $objContent = Util::createContent($objArticle, [
            'type' => 'module', 'module' => $modules['sitemap']->id,
        ]);

        return $page;
    }

    protected function createPageLegalNotice(PageModel $rootPage): PageModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        $page = PageModel::findOneBy('title', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PageLegalNoticeTitle']);
        $page = Util::createPage($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PageLegalNoticeTitle'], $rootPage->id, array_merge([
            'sorting' => 386,
            'sitemap' => 'default',
            'description' => sprintf($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PageLegalNoticeDescription'], $config->getSgWebsiteTitle()),
            'hide' => 1,
        ], null !== $page ? ['id' => $page->id] : []));
        $objArticle = ArticleModel::findOneBy(['pid = ?', 'alias = ?'], [$page->id, $page->alias]) ?? Util::createArticle($page);
        $content = ContentModel::findByPid($objArticle->id);
        if ($content) {
            while ($content->next()) {
                $content->current()->delete();
            }
        }
        $strText = file_get_contents(TL_ROOT.'/public/bundles/wemsmartgear/examples/legal-notices_1.html');
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
        $objContent = Util::createContent($objArticle, [
            'headline' => serialize(['unit' => 'h1', 'value' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PageLegalNoticeHeadline']]), 'text' => $strHtml,
        ]);

        return $page;
    }

    protected function createPagePrivacyPolitics(PageModel $rootPage): PageModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        $page = PageModel::findOneBy('title', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PagePrivacyPoliticsTitle']);
        $page = Util::createPage($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PagePrivacyPoliticsTitle'], $rootPage->id, array_merge([
            'sorting' => 512,
            'sitemap' => 'default',
            'description' => sprintf($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PagePrivacyPoliticsDescription'], $config->getSgWebsiteTitle()),
            'hide' => 1,
        ], null !== $page ? ['id' => $page->id] : []));
        $objArticle = ArticleModel::findOneBy(['pid = ?', 'alias = ?'], [$page->id, $page->alias]) ?? Util::createArticle($page);
        $content = ContentModel::findByPid($objArticle->id);
        if ($content) {
            while ($content->next()) {
                $content->current()->delete();
            }
        }
        $strText = file_get_contents(TL_ROOT.'/public/bundles/wemsmartgear/examples/privacy_1.html');
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
                $config->getSgOwnerDomain().'/'.$page->alias.'.html',
                date('d/m/Y'),
                $config->getSgOwnerEmail() ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled']
            );
        }
        $objContent = Util::createContent($objArticle, [
            'text' => $strHtml,
        ]);

        return $page;
    }

    protected function createPageSitemap(array $modules, PageModel $rootPage): PageModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        $page = PageModel::findOneBy('title', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PageSitemapTitle']);
        $page = Util::createPage($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PageSitemapTitle'], $rootPage->id, array_merge([
            'sorting' => 640,
            'sitemap' => 'default',
            'description' => sprintf($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PageSitemapDescription'], $config->getSgWebsiteTitle()),
            'hide' => 1,
        ], null !== $page ? ['id' => $page->id] : []));

        $objArticle = ArticleModel::findOneBy(['pid = ?', 'alias = ?'], [$page->id, $page->alias]) ?? Util::createArticle($page);
        $content = ContentModel::findByPid($objArticle->id);
        if ($content) {
            while ($content->next()) {
                $content->current()->delete();
            }
        }
        $objContent = Util::createContent($objArticle, [
            'type' => 'module', 'module' => $modules['sitemap']->id,
        ]);

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

    protected function createModules2(int $themeId, array $pages): void
    {
        $objCustomNavModule = ModuleModel::findOneByName($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['ModuleFooterNavName']) ?? new ModuleModel();
        $objCustomNavModule->pid = $themeId;
        $objCustomNavModule->tstamp = time();
        $objCustomNavModule->type = 'customnav';
        $objCustomNavModule->name = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['ModuleFooterNavName'];
        $objCustomNavModule->pages = [$pages['legal_notice']->id, $pages['privacy_politics']->id, $pages['sitemap']->id];
        $objCustomNavModule->navigationTpl = 'nav_default';
        $objCustomNavModule->save();
        $modules[$objCustomNavModule->type] = $objCustomNavModule;
    }

    protected function createNotificationGateways(): void
    {
        $objGateway = \NotificationCenter\Model\Gateway::findOneBy('title', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['NotificationGatewayEmailSmartgearTitle']) ?? new \NotificationCenter\Model\Gateway();
        $objGateway->tstamp = time();
        $objGateway->title = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['NotificationGatewayEmailSmartgearTitle'];
        $objGateway->type = 'email';
        $objGateway->save();
    }

    protected function uploadLogo(): File
    {
        $fm = Files::getInstance();
        $logoFolder = new Folder(CoreConfig::DEFAULT_CLIENT_LOGOS_FOLDER);
        if (!$fm->move_uploaded_file($_FILES['sgWebsiteLogo']['tmp_name'], CoreConfig::DEFAULT_CLIENT_LOGOS_FOLDER.$_FILES['sgWebsiteLogo']['name'])) {
            throw new Exception(sprintf('Unable to upload logo to "%s".', CoreConfig::DEFAULT_CLIENT_LOGOS_FOLDER.$_FILES['sgWebsiteLogo']['name']));
        }
        $objFile = new File(CoreConfig::DEFAULT_CLIENT_LOGOS_FOLDER.$_FILES['sgWebsiteLogo']['name']);

        return $objFile;
    }

    protected function updateModuleConfiguration(): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        $fonts = explode(',', Input::post('sgGoogleFonts'));
        foreach ($fonts as $key => $value) {
            $fonts[$key] = trim($value);
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

        $config->setSgTheme((string) $themeId);

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
        ];
    }
}
