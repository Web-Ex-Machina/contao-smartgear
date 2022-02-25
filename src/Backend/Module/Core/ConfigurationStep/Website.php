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
use WEM\SmartgearBundle\Classes\Config\Manager as ConfigurationManager;
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
        $this->title = 'Informations';
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        $countries = [];
        foreach (\Contao\System::getCountries() as $shortName => $longName) {
            $countries[] = ['value' => $shortName, 'label' => $longName];
        }
        $this->addFileField('sgWebsiteLogo', 'Logo du site web', empty($config->getSgOwnerLogo()));
        $this->addTextField('sgWebsiteTitle', 'Titre du site web', !empty($config->getSgOwnerName()) ? $config->getSgOwnerName() : $config->getSgWebsiteTitle(), true);
        $this->addTextField('sgOwnerStatus', 'Statut', $config->getSgOwnerStatus(), true);
        $this->addTextField('sgOwnerSiret', 'SIRET', $config->getSgOwnerSiret(), true);
        $this->addTextField('sgOwnerStreet', 'Adresse', $config->getSgOwnerStreet(), true);
        $this->addTextField('sgOwnerPostal', 'Code postal', $config->getSgOwnerPostal(), true);
        $this->addTextField('sgOwnerCity', 'Ville', $config->getSgOwnerCity(), true);
        $this->addTextField('sgOwnerRegion', 'Region', $config->getSgOwnerRegion(), true);
        $this->addSelectField('sgOwnerCountry', 'Pays', $countries, !empty($config->getSgOwnerCountry()) ? $config->getSgOwnerCountry() : 'fr', true);
        $this->addTextField('sgOwnerEmail', 'Email', $config->getSgOwnerEmail(), true);
        $this->addTextField('sgOwnerDomain', 'Domaine', !empty($config->getSgOwnerDomain()) ? $config->getSgOwnerDomain() : \Contao\Environment::get('base'), true);
        $this->addTextField('sgOwnerHost', 'Nom et adresse de l\'hébergeur', $config->getSgOwnerHost(), true);
        $this->addTextField('sgOwnerDpoName', 'Nom du DPO', $config->getSgOwnerDpoName(), true);
        $this->addTextField('sgOwnerDpoEmail', 'Email du DPO', $config->getSgOwnerDpoEmail(), true);
        $this->addTextField('sgGoogleFonts', 'Google Fonts', implode(',', $config->getSgGoogleFonts()), false, '', 'text', '', 'Liste des noms de polices Google Fonts, séparées par des virgules');
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
        if (empty(Input::post('sgWebsiteTitle'))) {
            throw new Exception('Le titre du site web n\'est pas renseigné.');
        }

        if (empty(Input::post('sgOwnerStatus'))) {
            throw new Exception('Le statut n\'est pas renseigné.');
        }

        if (empty(Input::post('sgOwnerSiret'))) {
            throw new Exception('Le numéro de SIRET n\'est pas renseigné.');
        }

        if (empty(Input::post('sgOwnerStreet'))) {
            throw new Exception('La rue n\'est pas renseignée.');
        }

        if (empty(Input::post('sgOwnerPostal'))) {
            throw new Exception('Le code postal n\'est pas renseigné.');
        }

        if (empty(Input::post('sgOwnerCity'))) {
            throw new Exception('La ville n\'est pas renseignée.');
        }

        if (empty(Input::post('sgOwnerRegion'))) {
            throw new Exception('La région n\'est pas renseignée.');
        }

        if (empty(Input::post('sgOwnerCountry'))) {
            throw new Exception('Le pays n\'est pas renseigné.');
        }

        if (empty(Input::post('sgOwnerEmail'))) {
            throw new Exception('L\'adresse email de l\'administrateur n\'est pas renseignée.');
        }

        if (empty(Input::post('sgOwnerDomain'))) {
            throw new Exception('Le domaine n\'est pas renseigné.');
        }

        if (empty(Input::post('sgOwnerHost'))) {
            throw new Exception('Les informations de l\'hébergeur ne sont pas renseignées.');
        }

        if (empty(Input::post('sgOwnerDpoName'))) {
            throw new Exception('Le nom du DPO n\'est pas renseigné.');
        }

        if (empty(Input::post('sgOwnerDpoEmail'))) {
            throw new Exception('L\'adresse email du DPO n\'est pas renseignée.');
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
        $pages = $this->createPages($layouts, $groups, $users);
        $this->createModules2($themeId, $pages);
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
        // $objFooterModule->html = file_get_contents(TL_ROOT.'/web/bundles/wemsmartgear/examples/footer_1.html');
        $objFooterModule->save();
        $modules[$objFooterModule->type] = $objFooterModule;

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
        $script = file_get_contents(TL_ROOT.'/web/bundles/wemsmartgear/examples/code_javascript_personnalise_1.js');
        $script = str_replace('{{config.googleFonts}}', "'".implode("','", $config->getSgGoogleFonts())."'", $script);
        $script = str_replace('{{config.framway.path}}', $config->getSgFramwayPath(), $script);

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
        // $objLayout->template = 'fe_page';
        // $objLayout->webfonts = '';
        // $objLayout->head = file_get_contents(TL_ROOT.'/web/bundles/wemsmartgear/examples/balises_supplementaires_1.js');
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
        // $objLayout->head = file_get_contents(TL_ROOT.'/web/bundles/wemsmartgear/examples/balises_supplementaires_1.js');
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

    protected function createPages(array $layouts, array $groups, array $users): array
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        $pages = [];

        $page = PageModel::findOneBy('title', $config->getSgwebsiteTitle());
        $pages['root'] = Util::createPage($config->getSgwebsiteTitle(), 0, array_merge([
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

        $page = PageModel::findOneBy('title', 'Accueil');
        $pages['home'] = Util::createPage('Accueil', $pages['root']->id, array_merge([
            'sorting' => 128,
            'alias' => '/',
            'sitemap' => 'default',
            'hide' => 1,
        ], null !== $page ? ['id' => $page->id] : []));

        $page = PageModel::findOneBy('title', 'Erreur 404 - Page non trouvée');
        $pages['404'] = Util::createPage('Erreur 404 - Page non trouvée', $pages['root']->id, array_merge([
            'sorting' => 256,
            'sitemap' => 'default',
            'hide' => 1,
        ], null !== $page ? ['id' => $page->id] : []));

        $page = PageModel::findOneBy('title', 'Mentions légales');
        $pages['legal_notice'] = Util::createPage('Mentions légales', $pages['root']->id, array_merge([
            'sorting' => 386,
            'sitemap' => 'default',
            'description' => 'Mentions légales de '.$config->getSgWebsiteTitle(),
            'hide' => 1,
        ], null !== $page ? ['id' => $page->id] : []));

        $page = PageModel::findOneBy('title', 'Confidentialité');
        $pages['privacy_politics'] = Util::createPage('Confidentialité', $pages['root']->id, array_merge([
            'sorting' => 512,
            'sitemap' => 'default',
            'description' => 'Politique de confidentialité de '.$config->getSgWebsiteTitle(),
            'hide' => 1,
        ], null !== $page ? ['id' => $page->id] : []));

        $page = PageModel::findOneBy('title', 'Plan du site');
        $pages['sitemap'] = Util::createPage('Plan du site', $pages['root']->id, array_merge([
            'sorting' => 640,
            'sitemap' => 'default',
            'description' => 'Plan du site de '.$config->getSgWebsiteTitle(),
            'hide' => 1,
        ], null !== $page ? ['id' => $page->id] : []));

        // $objSamplerPage = Util::createPage('Sampler', $pages['root']->id, []);

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

        $objSitemapModule = ModuleModel::findOneByName('Plan du site') ?? new ModuleModel();
        $objSitemapModule->pid = $themeId;
        $objSitemapModule->tstamp = time();
        $objSitemapModule->type = 'sitemap';
        $objSitemapModule->name = 'Plan du site';
        $objSitemapModule->save();
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

        $config->setSgWebsiteTitle(Input::post('sgWebsiteTitle'));
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
