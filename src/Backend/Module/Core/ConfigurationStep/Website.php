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

namespace WEM\SmartgearBundle\Backend\Module\Core\ConfigurationStep;

use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\File;
use Contao\Files;
use Contao\FilesModel;
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
        $objBreadcrumbModule = ModuleModel::findOneByName('Fil d\'ariane') ?? new ModuleModel();
        $objBreadcrumbModule->pid = $themeId;
        $objBreadcrumbModule->tstamp = time();
        $objBreadcrumbModule->type = 'breadcrumb';
        $objBreadcrumbModule->name = "Fil d'ariane";
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

        $objSitemapModule = ModuleModel::findOneByName('Plan du site') ?? new ModuleModel();
        $objSitemapModule->pid = $themeId;
        $objSitemapModule->tstamp = time();
        $objSitemapModule->type = 'sitemap';
        $objSitemapModule->name = 'Plan du site';
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

        $objLayout = LayoutModel::findOneByName('Page Standard') ?? new LayoutModel();
        $objLayout->pid = $themeId;
        $objLayout->name = 'Page Standard';
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

        $objLayout = LayoutModel::findOneByName('Page Standard - Fullwidth') ?? new LayoutModel();
        $objLayout->pid = $themeId;
        $objLayout->name = 'Page Standard - Fullwidth';
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

        $objUserGroup = UserGroupModel::findOneByName(CoreConfig::DEFAULT_USER_GROUP_ADMIN_NAME) ?? new UserGroupModel();
        $objUserGroup->tstamp = time();
        $objUserGroup->name = CoreConfig::DEFAULT_USER_GROUP_ADMIN_NAME;
        $objUserGroup->modules = 'a:8:{i:0;s:4:"page";i:1;s:7:"article";i:2;s:4:"form";i:3;s:5:"files";i:4;s:16:"nc_notifications";i:5;s:4:"user";i:6;s:3:"log";i:7;s:11:"maintenance";}';
        // $objUserGroup->pagemounts = '';
        // $objUserGroup->alpty = 'a:3:{i:0;s:7:"regular";i:1;s:7:"forward";i:2;s:8:"redirect";}';
        // $objUserGroup->filemounts = 'a:1:{i:0;s:16:"'.$objMediaFolder->getModel()->uuid.'";}';
        // $objUserGroup->fop = 'a:4:{i:0;s:2:"f1";i:1;s:2:"f2";i:2;s:2:"f3";i:3;s:2:"f4";}';
        // $objUserGroup->imageSizes = 'a:3:{i:0;s:12:"proportional";i:1;s:3:"box";i:2;s:4:"crop";}';
        // $objUserGroup->alexf = Util::addPermissions($this->getCorePermissions());
        $objUserGroup->save();
        $userGroups['administrators'] = $objUserGroup;

        $objUserGroup = UserGroupModel::findOneByName('Redacteurs') ?? new UserGroupModel();
        $objUserGroup->tstamp = time();
        $objUserGroup->name = 'Redacteurs';
        $objUserGroup->modules = 'a:8:{i:0;s:7:"article";i:1;s:5:"files";}';
        $objUserGroup->save();
        $userGroups['redactors'] = $objUserGroup;

        return $userGroups;
    }

    protected function createUsers(array $groups): array
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        $objUser = UserModel::findOneByName('Webmaster') ?? new UserModel();
        $objUser->tstamp = time();
        $objUser->dateAdded = time();
        $objUser->username = CoreConfig::DEFAULT_USER_USERNAME;
        $objUser->name = 'Webmaster';
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
        $page = PageModel::findOneBy('title', 'Accueil');
        $page = Util::createPage('Accueil', $rootPage->id, array_merge([
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
        $page = PageModel::findOneBy('title', 'Erreur 404 - Page non trouvée');
        $page = Util::createPage('Erreur 404 - Page non trouvée', $rootPage->id, array_merge([
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
            'headline' => serialize(['unit' => 'h1', 'value' => 'Page non trouvée !']), 'text' => "<p>La page demandée n'existe pas. Vous pouvez consulter le plan du site ci-dessous pour poursuivre votre navigation.</p>",
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

        $page = PageModel::findOneBy('title', 'Mentions légales');
        $page = Util::createPage('Mentions légales', $rootPage->id, array_merge([
            'sorting' => 386,
            'sitemap' => 'default',
            'description' => 'Mentions légales de '.$config->getSgWebsiteTitle(),
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
        $strHtml = '<p>A remplir</p>';
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
                    $config->getSgOwnerDomain() ?: 'NR',
                    str_replace('https://', '', $config->getSgOwnerDomain()) ?: 'NR',
                    $config->getSgOwnerName() ?: 'NR',
                    $config->getSgOwnerStatus() ?: 'NR',
                    $config->getSgOwnerSIRET() ?: 'NR',
                    $config->getSgOwnerStreet().' '.$config->getSgOwnerPostal().' '.$config->getSgOwnerCity().' '.$config->getSgOwnerRegion().' '.$config->getSgOwnerCountry() ?: 'NR',
                    $config->getSgOwnerEmail() ?: 'NR',
                    $config->getSgOwnerHost() ?: 'NR'
                );
        }
        $objContent = Util::createContent($objArticle, [
            'headline' => serialize(['unit' => 'h1', 'value' => 'Mentions légales']), 'text' => $strHtml,
        ]);

        return $page;
    }

    protected function createPagePrivacyPolitics(PageModel $rootPage): PageModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        $page = PageModel::findOneBy('title', 'Confidentialité');
        $page = Util::createPage('Confidentialité', $rootPage->id, array_merge([
            'sorting' => 512,
            'sitemap' => 'default',
            'description' => 'Politique de confidentialité de '.$config->getSgWebsiteTitle(),
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
        $strHtml = '<p>A remplir</p>';
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
                $config->getSgOwnerName() ?: 'NR',
                $config->getSgOwnerStreet().' '.$config->getSgOwnerPostal().' '.$config->getSgOwnerCity().' '.$config->getSgOwnerRegion().' '.$config->getSgOwnerCountry() ?: 'NR',
                $config->getSgOwnerSIRET() ?: 'NR',
                $config->getSgOwnerDomain().'/'.$page->alias.'.html',
                date('d/m/Y'),
                $config->getSgOwnerEmail() ?: 'NR'
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

        $page = PageModel::findOneBy('title', 'Plan du site');
        $page = Util::createPage('Plan du site', $rootPage->id, array_merge([
            'sorting' => 640,
            'sitemap' => 'default',
            'description' => 'Plan du site de '.$config->getSgWebsiteTitle(),
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
        $objCustomNavModule = ModuleModel::findOneByName('Nav - footer') ?? new ModuleModel();
        $objCustomNavModule->pid = $themeId;
        $objCustomNavModule->tstamp = time();
        $objCustomNavModule->type = 'customnav';
        $objCustomNavModule->name = 'Nav - footer';
        $objCustomNavModule->pages = [$pages['legal_notice']->id, $pages['privacy_politics']->id, $pages['sitemap']->id];
        $objCustomNavModule->navigationTpl = 'nav_default';
        $objCustomNavModule->save();
        $modules[$objCustomNavModule->type] = $objCustomNavModule;
    }

    protected function createNotificationGateways(): void
    {
        $objGateway = \NotificationCenter\Model\Gateway::findOneBy('title', 'Email de service - Smartgear') ?? new \NotificationCenter\Model\Gateway();
        $objGateway->tstamp = time();
        $objGateway->title = 'Email de service - Smartgear';
        $objGateway->type = 'email';
        $objGateway->save();
    }

    protected function uploadLogo(): File
    {
        $fm = Files::getInstance();
        $fm->move_uploaded_file($_FILES['sgWebsiteLogo']['tmp_name'], 'files/'.$_FILES['sgWebsiteLogo']['name']);
        $objFile = new File('files/'.$_FILES['sgWebsiteLogo']['name']);

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
            $formattedModules[] = [$objModule->type => $objModule->id];
        }

        $config->setSgModules($formattedModules);

        $this->configurationManager->save($config);
    }
}
