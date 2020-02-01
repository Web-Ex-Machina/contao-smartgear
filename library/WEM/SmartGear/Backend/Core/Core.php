<?php

/**
 * SMARTGEAR for Contao Open Source CMS.
 *
 * Copyright (c) 2015-2019 Web ex Machina
 *
 * @category ContaoBundle
 *
 * @author   Web ex Machina <contact@webexmachina.fr>
 *
 * @see     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

namespace WEM\SmartGear\Backend\Core;

use Exception;
use WEM\SmartGear\Backend\Block;
use WEM\SmartGear\Backend\BlockInterface;
use WEM\SmartGear\Backend\Util;

/**
 * Back end module "smartgear".
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Core extends Block implements BlockInterface
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->type = 'core';
        $this->module = 'core';
        $this->icon = 'exclamation-triangle';

        parent::__construct();
    }

    /**
     * Check Smartgear Status.
     *
     * @return [String] [Template of the module check status]
     */
    public function getStatus()
    {
        // Check if Smartgear is install
        if (!$this->sgConfig['sgInstallComplete']) {
            // Use a specific template for install
            $this->strTemplate = 'be_wem_sg_install_block_core_core';

            $this->actions[] = ['action' => 'install', 'label' => 'Installer Smartgear'];
            $this->actions[] = ['action' => 'resetContao', 'label' => 'Réinitialiser Contao', 'attributes' => 'onclick="if(!confirm(\'Voulez-vous vraiment réinitialiser Contao ?\'))return false;Backend.getScrollOffset()"'];

            $this->status = 0;
        } else {
            $this->title = 'Smartgear | Core | Configuration';

            $this->messages[] = ['class' => 'tl_error', 'text' => 'Vous pouvez réparer ou réinitialiser la configuration Smartgear établie. Veuillez prendre note que cela supprimera tous les éléments liés aux thèmes, squelettes, modules associés !'];
            $this->messages[] = ['class' => 'tl_error', 'text' => 'Vous pouvez également réinitialiser la totalité des données Contao. Tous les fichiers et toutes les données seront supprimés.'];

            $href = sprintf(
                'contao?do=smartgear&act=modal&type=%s&module=%s&function=%s&popup=1&rt=%s',
                $this->type,
                $this->module,
                'configure',
                \RequestToken::get()
            );
            $this->actions[] = ['v' => 2, 'tag' => 'a', 'text' => 'Configurer', 'attrs' => ['href' => $href, 'title' => 'Configurer Smartgear', 'class' => 'openSmartgearModal', 'data-title' => 'Configurer Smartgear']];
            $this->actions[] = ['action' => 'reset', 'label' => 'Réinitialiser', 'attributes' => 'onclick="if(!confirm(\'Voulez-vous vraiment réinitialiser Smartgear ?\'))return false;Backend.getScrollOffset()"'];
            $this->actions[] = ['action' => 'remove', 'label' => 'Supprimer', 'attributes' => 'onclick="if(!confirm(\'Voulez-vous vraiment supprimer Smartgear ?\'))return false;Backend.getScrollOffset()"'];
            $this->actions[] = ['action' => 'resetContao', 'label' => 'Réinitialiser Contao', 'attributes' => 'onclick="if(!confirm(\'Voulez-vous vraiment réinitialiser Contao ?\'))return false;Backend.getScrollOffset()"'];

            $this->status = 1;
        }
    }

    /**
     * Reset Smartgear.
     *
     * @todo Handle the Logo reset (the install POST value is a b64 so either we match that or we create a rule in install to handle b64 or path)
     */
    public function reset()
    {
        \Input::setPost('websiteTitle', $this->sgConfig['websiteTitle']);
        \Input::setPost('framwayPath', $this->sgConfig['framwayPath']);
        \Input::setPost('framwayTheme', $this->sgConfig['framwayTheme']);
        parent::reset();
    }

    /**
     * Display & Update Smartgear config.
     *
     * @todo Add the logo input file
     */
    public function configure()
    {
        if (\Input::post('TL_WEM_AJAX')) {
            try {
                if (!\Input::post('config') || empty(\Input::post('config'))) {
                    throw new Exception('No data sent');
                }

                $blnUpdate = false;
                foreach (\Input::post('config') as $k => $v) {
                    if ($v != $this->sgConfig[$k]) {
                        $this->sgConfig[$k] = $v;
                        $blnUpdate = true;
                    }
                }

                if ($blnUpdate) {
                    Util::updateConfig($this->sgConfig);

                    $arrResponse = [
                        'toastr' => [
                            'status' => 'success', 'msg' => 'Configuration sauvegardée',
                        ],
                    ];
                } else {
                    $arrResponse = [
                        'toastr' => [
                            'status' => 'info', 'msg' => 'Pas de changements détectés',
                        ],
                    ];
                }
            } catch (Exception $e) {
                $arrResponse = ['status' => 'error', 'msg' => $e->getMessage(), 'trace' => $e->getTrace()];
            }

            // Add Request Token to JSON answer and return
            $arrResponse['rt'] = \RequestToken::get();
            echo json_encode($arrResponse);
            die;
        }

        $objTemplate = new \FrontendTemplate('be_wem_sg_install_modal_core_configure');
        $objTemplate->config = $this->sgConfig;

        $objThemes = \ThemeModel::findAll();
        $arrThemes = [];
        if ($objThemes) {
            while ($objThemes->next()) {
                $arrThemes[$objThemes->id] = [
                    'name' => $objThemes->name, 'selected' => ($this->sgConfig['sgInstallTheme'] == $objThemes->id) ? true : false,
                ];
            }
        }
        $objTemplate->themes = $arrThemes;

        $objModules = \ModuleModel::findAll();
        if ($objModules) {
            $arrConfigModules = deserialize($this->sgConfig['sgInstallModules']);
            while ($objModules->next()) {
                $arrModules[$objModules->id] = [
                    'name' => $objModules->name, 'selected' => (in_array($objModules->id, $arrConfigModules)) ? true : false,
                ];
            }
        }
        $objTemplate->modules = $arrModules;

        $objLayouts = \LayoutModel::findAll();
        $arrLayouts = [];
        if ($objLayouts) {
            while ($objLayouts->next()) {
                $arrLayouts[$objLayouts->id] = [
                    'name' => $objLayouts->name, 'selected' => ($this->sgConfig['sgInstallLayout'] == $objLayouts->id) ? true : false,
                ];
            }
        }
        $objTemplate->layouts = $arrLayouts;

        $objUserGroups = \UserGroupModel::findAll();
        $arrUserGroups = [];
        if ($objUserGroups) {
            while ($objUserGroups->next()) {
                $arrUserGroups[$objUserGroups->id] = [
                    'name' => $objUserGroups->name, 'selected' => ($this->sgConfig['sgInstallUserGroup'] == $objUserGroups->id) ? true : false,
                ];
            }
        }
        $objTemplate->usergroups = $arrUserGroups;

        $objRootPages = \PageModel::findByPid(0);
        $arrRootPages = [];
        if ($objRootPages) {
            while ($objRootPages->next()) {
                $arrRootPages[$objRootPages->id] = [
                    'name' => $objRootPages->title, 'selected' => ($this->sgConfig['sgInstallRootPage'] == $objRootPages->id) ? true : false,
                ];
            }
        }
        $objTemplate->rootpages = $arrRootPages;

        $objNcGateways = \NotificationCenter\Model\Gateway::findAll();
        $arrNcGateways = [];
        if ($objNcGateways) {
            while ($objNcGateways->next()) {
                $arrNcGateways[$objNcGateways->id] = [
                    'name' => $objNcGateways->title, 'selected' => ($this->sgConfig['sgInstallNcGateway'] == $objNcGateways->id) ? true : false,
                ];
            }
        }
        $objTemplate->ncgateways = $arrNcGateways;

        return $objTemplate;
    }

    /**
     * Install Smartgear.
     */
    public function install()
    {
        try {
            // Prepare the log array
            $this->logs[] = ['status' => 'tl_info', 'msg' => "Début de l'installation"];

            // Store the default config
            $arrConfig['websiteTitle'] = \Input::post('websiteTitle');
            $arrConfig['dateFormat'] = 'd/m/Y';
            $arrConfig['timeFormat'] = 'H:i';
            $arrConfig['datimFormat'] = 'd/m/Y à H:i';
            $arrConfig['timeZone'] = 'Europe/Paris';
            $arrConfig['adminEmail'] = 'contact@webexmachina.fr';
            $arrConfig['characterSet'] = 'utf-8';
            $arrConfig['useAutoItem'] = 1;
            $arrConfig['privacyAnonymizeIp'] = 1;
            $arrConfig['privacyAnonymizeGA'] = 1;
            $arrConfig['gdMaxImgWidth'] = 5000;
            $arrConfig['gdMaxImgHeight'] = 5000;
            $arrConfig['maxFileSize'] = 20971520;
            $arrConfig['undoPeriod'] = 7776000; // 3 months
            $arrConfig['versionPeriod'] = 7776000; // 3 months
            $arrConfig['logPeriod'] = 7776000; // 3 months
            $arrConfig['allowedTags'] = '<script><iframe><a><abbr><acronym><address><area><article><aside><audio><b><bdi><bdo><big><blockquote><br><base><button><canvas><caption><cite><code><col><colgroup><data><datalist><dataset><dd><del><dfn><div><dl><dt><em><fieldset><figcaption><figure><footer><form><h1><h2><h3><h4><h5><h6><header><hgroup><hr><i><img><input><ins><kbd><keygen><label><legend><li><link><map><mark><menu><nav><object><ol><optgroup><option><output><p><param><picture><pre><q><s><samp><section><select><small><source><span><strong><style><sub><sup><table><tbody><td><textarea><tfoot><th><thead><time><tr><tt><u><ul><var><video><wbr>';

            // Update Contao Config
            foreach ($arrConfig as $k => $v) {
                \Config::persist($k, $v);
            }

            // Standardize Framway path (make sure we are at the framway root)
            $strFramwayPath = str_replace('/build', '', \Input::post('framwayPath'));
            $strFramwayPathBuild = $strFramwayPath.'/build';
            $strFramwayPathThemes = $strFramwayPath.'/src/themes';

            // Make sure Contao knows the framway build files
            \Dbafs::addResource($strFramwayPathBuild);

            // Check app folders and check if there is all Framway stuff loaded
            if (!file_exists(TL_ROOT.'/'.$strFramwayPathBuild.'/css/framway.css') || !file_exists(TL_ROOT.'/'.$strFramwayPathBuild.'/css/vendor.css') || !file_exists(TL_ROOT.'/'.$strFramwayPathBuild.'/js/framway.js') || !file_exists(TL_ROOT.'/'.$strFramwayPathBuild.'/js/vendor.js')) {
                throw new Exception('Des fichiers Framway sont manquants !');
            }
            $this->logs[] = ['status' => 'tl_confirm', 'msg' => 'Les fichiers Smartgear ont été trouvés (framway.css, framway.js, vendor.css, vendor.js)'];

            // Make sure framway path is public
            $objFramwayFolder = new \Folder($strFramwayPath);
            $objFramwayFolder->unprotect();

            // Import the folders
            $objFiles = \Files::getInstance();
            $objFiles->rcopy('system/modules/wem-contao-smartgear/assets/templates_files', 'templates');
            $objFiles->rcopy('system/modules/wem-contao-smartgear/assets/templates_app', 'app');
            $objFiles->rcopy('system/modules/wem-contao-smartgear/assets/vendor', 'files/vendor');

            // Copy package themes into framway folder
            $objFolder = new \Folder($strFramwayPathThemes);
            $objFiles->rcopy('system/modules/wem-contao-smartgear/assets/themes_framway', $strFramwayPathThemes);
            $objFolder->unprotect();

            // Unprotect vendor folder
            $objFolder = new \Folder('files/vendor');
            $objFolder->unprotect();

            // Check if there is another themes and warn the user
            if (\ThemeModel::countAll() > 0) {
                $this->logs[] = ['status' => 'tl_info', 'msg' => "Attention, il existe d'autres thèmes potentiellement utilisés sur ce Contao"];
            }

            // Create smartgear medias folder
            $objMediaFolder = new \Folder('files/medias');
            $objMediaFolder->unprotect();

            // Import the logo into files/medias/logos folder
            if (\Input::post('websiteLogo')) {
                $objFolder = new \Folder('files/medias/logos');
                $objLogo = Util::base64ToImage(\Input::post('websiteLogo'), 'files/medias/logos', 'logo');
                $objLogoModel = $objLogo->getModel();
            } else {
                $objLogoModel = \FilesModel::findOneByPath($strFramwayPathBuild.'/img/logo_placeholder.png');
            }

            // Set up some config vars
            $this->sgConfig['websiteTitle'] = \Input::post('websiteTitle');
            $this->sgConfig['framwayPath'] = $strFramwayPath;
            $this->sgConfig['framwayTheme'] = \Input::post('framwayTheme');
            $this->sgConfig['websiteLogo'] = $objLogoModel->path;
            $this->logs[] = ['status' => 'tl_confirm', 'msg' => 'Configuration importée'];

            // Create the Smartgear main theme
            $objTheme = new \ThemeModel();
            $objTheme->tstamp = time();
            $objTheme->name = 'Smartgear';
            $objTheme->author = 'Web ex Machina';
            $objTheme->templates = 'templates/smartgear';
            $objTheme->save();
            $this->sgConfig['sgInstallTheme'] = $objTheme->id;
            $this->logs[] = ['status' => 'tl_confirm', 'msg' => sprintf('Le thème %s a été créé et sera utilisé pour la suite de la configuration', $objTheme->name)];

            // Create the Smartgear main modules
            $arrLayoutModules = [];
            $arrModules = [];

            // Header - Logo
            $objModule = new \ModuleModel();
            $objModule->pid = $objTheme->id;
            $objModule->tstamp = time();
            $objModule->type = 'wem_sg_header';
            $objModule->name = 'HEADER';
            $objModule->wem_sg_header_preset = 'classic';
            $objModule->wem_sg_header_sticky = 1;
            $objModule->wem_sg_navigation = 'classic';
            $objModule->wem_sg_header_logo = $objLogoModel->uuid;
            $objModule->wem_sg_header_logo_size = 'a:3:{i:0;s:0:"";i:1;s:2:"75";i:2;s:12:"proportional";}';
            $objModule->wem_sg_header_logo_alt = 'Logo '.$this->sgConfig['websiteTitle'];
            $objModule->save();
            $arrLayoutModules[] = ['mod' => $objModule->id, 'col' => 'header', 'enable' => '1'];
            $arrModules[] = $objModule->id;

            // Breadcrumb
            $objModule = new \ModuleModel();
            $objModule->pid = $objTheme->id;
            $objModule->tstamp = time();
            $objModule->type = 'breadcrumb';
            $objModule->name = "Fil d'ariane";
            $objModule->save();
            $arrLayoutModules[] = ['mod' => $objModule->id, 'col' => 'main', 'enable' => '1'];
            $arrModules[] = $objModule->id;

            // Main - Articles
            $arrLayoutModules[] = ['mod' => 0, 'col' => 'main', 'enable' => '1'];

            // Footer
            $objModule = new \ModuleModel();
            $objModule->pid = $objTheme->id;
            $objModule->tstamp = time();
            $objModule->type = 'html';
            $objModule->name = 'FOOTER';
            $objModule->html = file_get_contents('system/modules/wem-contao-smartgear/assets/examples/footer_1.html');
            $objModule->save();
            $arrLayoutModules[] = ['mod' => $objModule->id, 'col' => 'footer', 'enable' => '1'];
            $arrModules[] = $objModule->id;

            // Store & log module creation
            $this->sgConfig['sgInstallModules'] = serialize($arrModules);
            $this->logs[] = ['status' => 'tl_confirm', 'msg' => sprintf('Les modules principaux ont été créés', $objTheme->name)];

            // Create the Smartgear main layout
            $arrCssFiles = [];
            $arrJsFiles = [];
            $objFile = \FilesModel::findOneByPath($strFramwayPathBuild.'/css/vendor.css');
            $arrCssFiles[] = $objFile->uuid;
            $objFile = \FilesModel::findOneByPath($strFramwayPathBuild.'/css/framway.css');
            $arrCssFiles[] = $objFile->uuid;
            $objFile = \FilesModel::findOneByPath('files/vendor/outdatedbrowser/outdatedbrowser.min.css');
            $arrCssFiles[] = $objFile->uuid;
            $objFile = \FilesModel::findOneByPath($strFramwayPathBuild.'/js/vendor.js');
            $arrJsFiles[] = $objFile->uuid;
            $objFile = \FilesModel::findOneByPath($strFramwayPathBuild.'/js/framway.js');
            $arrJsFiles[] = $objFile->uuid;

            $objLayout = new \LayoutModel();
            $objLayout->pid = $objTheme->id;
            $objLayout->name = 'Page Standard';
            $objLayout->rows = '3rw';
            $objLayout->cols = '1cl';
            $objLayout->framework = '';
            $objLayout->stylesheet = '';
            $objLayout->external = serialize($arrCssFiles);
            $objLayout->orderExt = serialize($arrCssFiles);
            $objLayout->loadingOrder = 'external_first';
            $objLayout->combineScripts = 1;
            $objLayout->doctype = 'html5';
            $objLayout->template = 'fe_page';
            $objLayout->viewport = 'width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=0';
            $objLayout->externalJs = serialize($arrJsFiles);
            $objLayout->orderExtJs = serialize($arrJsFiles);
            $objLayout->modules = serialize($arrLayoutModules);
            $objLayout->head = file_get_contents('system/modules/wem-contao-smartgear/assets/examples/balises_supplementaires_1.js');
            $objLayout->script = file_get_contents('system/modules/wem-contao-smartgear/assets/examples/code_javascript_personnalise_1.js');
            $objLayout->save();
            $this->sgConfig['sgInstallLayout'] = $objLayout->id;
            $this->logs[] = ['status' => 'tl_confirm', 'msg' => sprintf('Le layout %s a été créé et sera utilisé pour la suite de la configuration', $objLayout->name)];

            // Add a layout without header and guidelines
            $objLayoutWithoutHeaderAndFooter = new \LayoutModel();
            $objLayoutWithoutHeaderAndFooter->pid = $objTheme->id;
            $objLayoutWithoutHeaderAndFooter->name = 'Page sans header/footer';
            $objLayoutWithoutHeaderAndFooter->rows = '1rw';
            $objLayoutWithoutHeaderAndFooter->cols = '1cl';
            $objLayoutWithoutHeaderAndFooter->framework = '';
            $objLayoutWithoutHeaderAndFooter->stylesheet = '';
            $objLayoutWithoutHeaderAndFooter->external = serialize($arrCssFiles);
            $objLayoutWithoutHeaderAndFooter->orderExt = serialize($arrCssFiles);
            $objLayoutWithoutHeaderAndFooter->loadingOrder = 'external_first';
            $objLayoutWithoutHeaderAndFooter->combineScripts = 1;
            $objLayoutWithoutHeaderAndFooter->doctype = 'html5';
            $objLayoutWithoutHeaderAndFooter->template = 'fe_page';
            $objLayoutWithoutHeaderAndFooter->viewport = 'width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=0';
            $objLayoutWithoutHeaderAndFooter->externalJs = serialize($arrJsFiles);
            $objLayoutWithoutHeaderAndFooter->orderExtJs = serialize($arrJsFiles);
            $objLayoutWithoutHeaderAndFooter->modules = 'a:1:{i:0;a:3:{s:3:"mod";s:1:"0";s:3:"col";s:4:"main";s:6:"enable";s:1:"1";}}';
            $objLayoutWithoutHeaderAndFooter->head = file_get_contents('system/modules/wem-contao-smartgear/assets/examples/balises_supplementaires_1.js');
            $objLayoutWithoutHeaderAndFooter->script = file_get_contents('system/modules/wem-contao-smartgear/assets/examples/code_javascript_personnalise_1.js');
            $objLayoutWithoutHeaderAndFooter->save();
            $this->logs[] = ['status' => 'tl_confirm', 'msg' => sprintf('Le layout %s a été créé et sera utilisé pour la suite de la configuration', $objLayout->name)];

            // Create the default user group
            $objUserGroup = new \UserGroupModel();
            $objUserGroup->tstamp = time();
            $objUserGroup->name = 'Administrateurs';
            $objUserGroup->modules = 'a:8:{i:0;s:4:"page";i:1;s:7:"article";i:2;s:4:"form";i:3;s:5:"files";i:4;s:16:"nc_notifications";i:5;s:4:"user";i:6;s:3:"log";i:7;s:11:"maintenance";}';
            $arrPageMounts = [];
            $objUserGroup->pagemounts = '';
            $objUserGroup->alpty = 'a:3:{i:0;s:7:"regular";i:1;s:7:"forward";i:2;s:8:"redirect";}';
            $objUserGroup->filemounts = 'a:1:{i:0;s:16:"'.$objMediaFolder->getModel()->uuid.'";}';
            $objUserGroup->fop = 'a:4:{i:0;s:2:"f1";i:1;s:2:"f2";i:2;s:2:"f3";i:3;s:2:"f4";}';
            $objUserGroup->imageSizes = 'a:3:{i:0;s:12:"proportional";i:1;s:3:"box";i:2;s:4:"crop";}';
            $objUserGroup->alexf = 'a:156:{i:0;s:17:"tl_article::title";i:1;s:17:"tl_article::alias";i:2;s:17:"tl_article::cssID";i:3;s:21:"tl_article::published";i:4;s:17:"tl_article::start";i:5;s:16:"tl_article::stop";i:6;s:16:"tl_content::type";i:7;s:20:"tl_content::headline";i:8;s:16:"tl_content::text";i:9;s:20:"tl_content::addImage";i:10;s:25:"tl_content::overwriteMeta";i:11;s:21:"tl_content::singleSRC";i:12;s:15:"tl_content::alt";i:13;s:22:"tl_content::imageTitle";i:14;s:16:"tl_content::size";i:15;s:23:"tl_content::imagemargin";i:16;s:20:"tl_content::imageUrl";i:17;s:20:"tl_content::fullsize";i:18;s:19:"tl_content::caption";i:19;s:20:"tl_content::floating";i:20;s:16:"tl_content::html";i:21;s:20:"tl_content::listtype";i:22;s:21:"tl_content::listitems";i:23;s:22:"tl_content::tableitems";i:24;s:19:"tl_content::summary";i:25;s:17:"tl_content::thead";i:26;s:17:"tl_content::tfoot";i:27;s:17:"tl_content::tleft";i:28;s:20:"tl_content::sortable";i:29;s:21:"tl_content::sortIndex";i:30;s:21:"tl_content::sortOrder";i:31;s:23:"tl_content::mooHeadline";i:32;s:20:"tl_content::mooStyle";i:33;s:22:"tl_content::mooClasses";i:34;s:21:"tl_content::highlight";i:35;s:16:"tl_content::code";i:36;s:15:"tl_content::url";i:37;s:18:"tl_content::target";i:38;s:25:"tl_content::overwriteLink";i:39;s:21:"tl_content::titleText";i:40;s:21:"tl_content::linkTitle";i:41;s:17:"tl_content::embed";i:42;s:15:"tl_content::rel";i:43;s:20:"tl_content::useImage";i:44;s:20:"tl_content::multiSRC";i:45;s:22:"tl_content::useHomeDir";i:46;s:18:"tl_content::perRow";i:47;s:19:"tl_content::perPage";i:48;s:25:"tl_content::numberOfItems";i:49;s:18:"tl_content::sortBy";i:50;s:22:"tl_content::metaIgnore";i:51;s:22:"tl_content::galleryTpl";i:52;s:21:"tl_content::customTpl";i:53;s:21:"tl_content::playerSRC";i:54;s:19:"tl_content::youtube";i:55;s:17:"tl_content::vimeo";i:56;s:21:"tl_content::posterSRC";i:57;s:22:"tl_content::playerSize";i:58;s:25:"tl_content::playerOptions";i:59;s:23:"tl_content::playerStart";i:60;s:22:"tl_content::playerStop";i:61;s:25:"tl_content::playerCaption";i:62;s:24:"tl_content::playerAspect";i:63;s:25:"tl_content::playerPreload";i:64;s:23:"tl_content::playerColor";i:65;s:26:"tl_content::youtubeOptions";i:66;s:24:"tl_content::vimeoOptions";i:67;s:23:"tl_content::sliderDelay";i:68;s:23:"tl_content::sliderSpeed";i:69;s:28:"tl_content::sliderStartSlide";i:70;s:28:"tl_content::sliderContinuous";i:71;s:20:"tl_content::cteAlias";i:72;s:24:"tl_content::articleAlias";i:73;s:19:"tl_content::article";i:74;s:16:"tl_content::form";i:75;s:18:"tl_content::module";i:76;s:21:"tl_content::protected";i:77;s:18:"tl_content::groups";i:78;s:18:"tl_content::guests";i:79;s:17:"tl_content::cssID";i:80;s:21:"tl_content::invisible";i:81;s:17:"tl_content::start";i:82;s:16:"tl_content::stop";i:83;s:21:"tl_content::rsce_data";i:84;s:23:"tl_content::grid_preset";i:85;s:26:"tl_content::grid_row_class";i:86;s:21:"tl_content::grid_rows";i:87;s:21:"tl_content::grid_cols";i:88;s:22:"tl_content::grid_items";i:89;s:24:"tl_nc_language::language";i:90;s:24:"tl_nc_language::fallback";i:91;s:26:"tl_nc_language::recipients";i:92;s:33:"tl_nc_language::attachment_tokens";i:93;s:27:"tl_nc_language::attachments";i:94;s:36:"tl_nc_language::attachment_templates";i:95;s:33:"tl_nc_language::email_sender_name";i:96;s:36:"tl_nc_language::email_sender_address";i:97;s:34:"tl_nc_language::email_recipient_cc";i:98;s:35:"tl_nc_language::email_recipient_bcc";i:99;s:29:"tl_nc_language::email_replyTo";i:100;s:29:"tl_nc_language::email_subject";i:101;s:26:"tl_nc_language::email_mode";i:102;s:26:"tl_nc_language::email_text";i:103;s:26:"tl_nc_language::email_html";i:104;s:37:"tl_nc_language::email_external_images";i:105;s:25:"tl_nc_language::file_name";i:106;s:33:"tl_nc_language::file_storage_mode";i:107;s:28:"tl_nc_language::file_content";i:108;s:14:"tl_page::title";i:109;s:14:"tl_page::alias";i:110;s:13:"tl_page::type";i:111;s:18:"tl_page::pageTitle";i:112;s:17:"tl_page::language";i:113;s:15:"tl_page::robots";i:114;s:20:"tl_page::description";i:115;s:17:"tl_page::redirect";i:116;s:15:"tl_page::jumpTo";i:117;s:21:"tl_page::redirectBack";i:118;s:12:"tl_page::url";i:119;s:15:"tl_page::target";i:120;s:17:"tl_page::noSearch";i:121;s:16:"tl_page::sitemap";i:122;s:13:"tl_page::hide";i:123;s:18:"tl_page::published";i:124;s:14:"tl_page::start";i:125;s:13:"tl_page::stop";i:126;s:17:"tl_user::username";i:127;s:13:"tl_user::name";i:128;s:14:"tl_user::email";i:129;s:17:"tl_user::language";i:130;s:21:"tl_user::backendTheme";i:131;s:19:"tl_user::fullscreen";i:132;s:17:"tl_user::uploader";i:133;s:17:"tl_user::showHelp";i:134;s:19:"tl_user::thumbnails";i:135;s:15:"tl_user::useRTE";i:136;s:14:"tl_user::useCE";i:137;s:17:"tl_user::password";i:138;s:17:"tl_user::pwChange";i:139;s:14:"tl_user::admin";i:140;s:15:"tl_user::groups";i:141;s:16:"tl_user::inherit";i:142;s:16:"tl_user::modules";i:143;s:15:"tl_user::themes";i:144;s:19:"tl_user::pagemounts";i:145;s:14:"tl_user::alpty";i:146;s:19:"tl_user::filemounts";i:147;s:12:"tl_user::fop";i:148;s:19:"tl_user::imageSizes";i:149;s:14:"tl_user::forms";i:150;s:14:"tl_user::formp";i:151;s:12:"tl_user::amg";i:152;s:16:"tl_user::disable";i:153;s:14:"tl_user::start";i:154;s:13:"tl_user::stop";i:155;s:16:"tl_user::session";}';
            $objUserGroup->save();
            $this->sgConfig['sgInstallUserGroup'] = $objUserGroup->id;
            $this->logs[] = ['status' => 'tl_confirm', 'msg' => sprintf("Le groupe d'utilisateurs %s a été créé", $objUserGroup->name)];

            // Add a default user to the user group
            if (0 === \UserModel::countBy('username', 'webmaster')) {
                $objUser = new \UserModel();
                $objUser->tstamp = time();
                $objUser->dateAdded = time();
                $objUser->username = 'webmaster';
                $objUser->name = 'Webmaster';
                $objUser->email = 'to@fill.fr';
                $objUser->language = 'fr';
                $objUser->backendTheme = 'flexible';
                $objUser->fullscreen = 1;
                $objUser->showHelp = 1;
                $objUser->thumbnails = 1;
                $objUser->useRTE = 1;
                $objUser->useCE = 1;
                $objUser->uploader = 'DropZone';
                $objUser->password = \Encryption::hash('webmaster');
                $objUser->pwChange = 1;
                $objUser->groups = serialize([0 => $objUserGroup->id]);
                $objUser->inherit = 'group';
                $objUser->save();
                $this->sgConfig['sgInstallUser'] = $objUser->id;
                $this->logs[] = ['status' => 'tl_confirm', 'msg' => sprintf("L'utilisateur %s a été créé", $objUser->name)];
            }

            // Create the Web ex Machina Admins
            if (0 === \UserModel::countBy('email', 'julien@webexmachina.fr')) {
                $objUser = new \UserModel();
                $objUser->tstamp = time();
                $objUser->dateAdded = time();
                $objUser->username = 'jthirion';
                $objUser->name = 'Julien - Web ex Machina';
                $objUser->email = 'julien@webexmachina.fr';
                $objUser->language = 'fr';
                $objUser->backendTheme = 'flexible';
                $objUser->fullscreen = 1;
                $objUser->showHelp = 1;
                $objUser->thumbnails = 1;
                $objUser->useRTE = 1;
                $objUser->useCE = 1;
                $objUser->admin = 1;
                $objUser->uploader = 'DropZone';
                $objUser->password = \Encryption::hash('webexmachina');
                $objUser->pwChange = 1;
                $objUser->save();
            }

            if (0 === \UserModel::countBy('email', 'quentin@webexmachina.fr')) {
                $objUser = new \UserModel();
                $objUser->tstamp = time();
                $objUser->dateAdded = time();
                $objUser->username = 'qvansteene';
                $objUser->name = 'Quentin - Web ex Machina';
                $objUser->email = 'quentin@webexmachina.fr';
                $objUser->language = 'fr';
                $objUser->backendTheme = 'flexible';
                $objUser->fullscreen = 1;
                $objUser->showHelp = 1;
                $objUser->thumbnails = 1;
                $objUser->useRTE = 1;
                $objUser->useCE = 1;
                $objUser->admin = 1;
                $objUser->uploader = 'DropZone';
                $objUser->password = \Encryption::hash('webexmachina');
                $objUser->pwChange = 1;
                $objUser->save();
            }

            // Generate a root page with the stuff previously created
            $websiteTitleAlias = \StringUtil::generateAlias($this->sgConfig['websiteTitle']);
            $objRootPage = Util::createPage($this->sgConfig['websiteTitle'], 0, [
                'pid' => 0, 'type' => 'root', 'language' => 'fr', 'fallback' => 1, 'createSitemap' => 1, 'sitemapName' => substr('sitemap-'.$websiteTitleAlias, 0, 30), 'useSSL' => 1, 'includeLayout' => 1, 'layout' => $objLayout->id, 'includeChmod' => 1, 'cuser' => $this->sgConfig['sgInstallUser'], 'cgroup' => $objUserGroup->id, 'chmod' => 'a:12:{i:0;s:2:"u1";i:1;s:2:"u2";i:2;s:2:"u3";i:3;s:2:"u4";i:4;s:2:"u5";i:5;s:2:"u6";i:6;s:2:"g1";i:7;s:2:"g2";i:8;s:2:"g3";i:9;s:2:"g4";i:10;s:2:"g5";i:11;s:2:"g6";}',
            ]);
            $arrPageMounts[] = $objRootPage->id;
            $this->sgConfig['sgInstallRootPage'] = $objRootPage->id;
            $this->logs[] = ['status' => 'tl_confirm', 'msg' => sprintf('Le site Internet %s a été créé', $objRootPage->title)];

            // Generate a gateway in the Notification Center
            $objGateway = new \NotificationCenter\Model\Gateway();
            $objGateway->tstamp = time();
            $objGateway->title = 'Email de service - Smartgear';
            $objGateway->type = 'email';
            $objGateway->save();
            $this->sgConfig['sgInstallNcGateway'] = $objGateway->id;
            $this->logs[] = ['status' => 'tl_confirm', 'msg' => sprintf('La passerelle (Notification Center) %s a été créée', $objGateway->title)];

            // Create a homepage
            $objHomePage = Util::createPage('Accueil', $objRootPage->id, ['alias' => '/']);
            $arrPageMounts[] = $objHomePage->id;
            $objArticle = Util::createArticle($objHomePage);

            // Create a 404 page, with a sitemap after
            $obj404Page = Util::createPage('Erreur 404 - Page non trouvée', $objRootPage->id, ['type' => 'error_404']);
            $objArticle = Util::createArticle($obj404Page);
            $objContent = Util::createContent($objArticle, [
                'headline' => serialize(['unit' => 'h1', 'value' => 'Page non trouvée !']), 'text' => "<p>La page demandée n'existe pas. Vous pouvez consulter le plan du site ci-dessous pour poursuivre votre navigation.</p>",
            ]);
            $objContent = Util::createContent($objArticle, [
                'type' => 'module', 'module' => $objModule->id,
            ]);

            // Create a module Sitemap
            $objModule = new \ModuleModel();
            $objModule->pid = $objTheme->id;
            $objModule->tstamp = time();
            $objModule->name = 'Plan du site';
            $objModule->type = 'sitemap';
            $objModule->headline = serialize(['unit' => 'h1', 'value' => 'Plan du site']);
            $objModule->rootPage = $objRootPage->id;
            $objModule->save();
            $arrModules[] = $objModule->id;

            // Create a page with the sitemap
            $objSitemapPage = Util::createPage('Plan du site', $objRootPage->id, ['hide' => 1]);
            $objArticle = Util::createArticle($objSitemapPage);
            $objContent = Util::createContent($objArticle, [
                'type' => 'module', 'module' => $objModule->id,
            ]);

            // Create a Legal Notices Page
            $objPage = Util::createPage('Mentions légales', $objRootPage->id, ['hide' => 1]);
            $arrPageMounts[] = $objPage->id;
            $objArticle = Util::createArticle($objPage);
            $objContent = Util::createContent($objArticle, [
                'headline' => serialize(['unit' => 'h1', 'value' => 'Mentions légales']), 'text' => '<p>A remplir</p>',
            ]);

            // Create a Privacy Page
            $objPage = Util::createPage('Confidentialité', $objRootPage->id, ['hide' => 1]);
            $arrPageMounts[] = $objPage->id;
            $objArticle = Util::createArticle($objPage);
            $objContent = Util::createContent($objArticle, [
                'headline' => serialize(['unit' => 'h1', 'value' => 'Confidentialité']), 'text' => '<p>A remplir</p>',
            ]);

            // Create a Guidelines Page
            $objPage = Util::createPage('Guidelines', $objRootPage->id, [
                'includeLayout' => 1, 'layout' => $objLayoutWithoutHeaderAndFooter->id, 'hide' => 1,
            ]);
            $arrPageMounts[] = $objPage->id;
            $objArticle = Util::createArticle($objPage, ['cssID' => serialize([0 => 'guideline'])]);

            // Add the generated pages to the user group
            $objUserGroup->pagemounts = serialize($arrPageMounts);
            $objUserGroup->save();

            // Create a robots.txt file with a Disallow
            /*if (!file_exists(TL_ROOT."/web/robots.txt")) {
                $objFile = $objFiles->fopen("web/robots.txt", "w");
                $objFiles->fputs($objFile, "User-agent: *"."\n"."Disallow: /");
            }*/

            // Finally, notify in Config that the install is complete :)
            $this->logs[] = ['status' => 'tl_confirm', 'msg' => 'Installation terminée'];

            // Update Config
            $this->sgConfig['sgInstallComplete'] = 1;
            Util::updateConfig($this->sgConfig);

            // And return an explicit status with some instructions
            return [
                'toastr' => [
                    'status' => 'success', 'msg' => "L'installation de Smartgear est terminée avec succès !",
                ], 'callbacks' => [
                    0 => [
                        'method' => 'refreshAllBlocks',
                    ],
                ],
            ];
        } catch (Exception $e) {
            $this->remove();
            throw $e;
        }
    }

    /**
     * Remove Smartgear.
     */
    public function remove()
    {
        try {
            // Delete the Gateway
            if ($objGateway = \NotificationCenter\Model\Gateway::findByPk($this->sgConfig['sgInstallNcGateway'])) {
                if ($objGateway->delete()) {
                    $this->sgConfig['sgInstallNcGateway'] = '';
                    $this->logs[] = ['status' => 'tl_confirm', 'msg' => sprintf('La passerelle (Notification Center) %s a été supprimée', $objGateway->title)];
                }
            }

            // Delete the root page
            if ($objRootPage = \PageModel::findByPk($this->sgConfig['sgInstallRootPage'])) {
                if ($objRootPage->delete()) {
                    $this->sgConfig['sgInstallRootPage'] = '';
                    $this->logs[] = ['status' => 'tl_confirm', 'msg' => sprintf('Le site Internet %s a été supprimé', $objRootPage->title)];
                }
            }

            // Delete the user
            if ($objUser = \UserModel::findByPk($this->sgConfig['sgInstallUser'])) {
                if ($objUser->delete()) {
                    $this->sgConfig['sgInstallUser'] = '';
                    $this->logs[] = ['status' => 'tl_confirm', 'msg' => sprintf("L'utilisateur %s a été supprimé", $objUser->name)];
                }
            }

            // Delete the user group
            if ($objUserGroup = \UserGroupModel::findByPk($this->sgConfig['sgInstallUserGroup'])) {
                if ($objUserGroup->delete()) {
                    $this->sgConfig['sgInstallUserGroup'] = '';
                    $this->logs[] = ['status' => 'tl_confirm', 'msg' => sprintf("Le groupe d'utilisateur %s a été supprimé", $objUserGroup->name)];
                }
            }

            // Delete the layout
            $objLayouts = \LayoutModel::findByPid($this->sgConfig['sgInstallTheme']);
            if ($objLayouts && 0 < $objLayouts->count()) {
                while ($objLayouts->next()) {
                    $objLayouts->delete();
                }

                $this->sgConfig['sgInstallLayout'] = '';
                $this->logs[] = ['status' => 'tl_confirm', 'msg' => sprintf('Le squelette %s a été supprimé', $objLayout->name)];
            }

            // Delete the modules
            $objModules = \ModuleModel::findByPid($this->sgConfig['sgInstallTheme']);
            $arrModules = deserialize($this->sgConfig['sgInstallModules']);
            if ($objModules && 0 < $objModules->count()) {
                while ($objModules->next()) {
                    if ($objModules->delete()) {
                        if (is_array($arrModules) && in_array($objModules->id, $arrModules)) {
                            unset($arrModules[array_search($objModules->id, $arrModules)]);
                        }
                        $this->logs[] = ['status' => 'tl_confirm', 'msg' => sprintf('Le module %s a été supprimé', $objModules->name)];
                    }
                }

                // Clear the config
                if (empty($arrModules)) {
                    $this->sgConfig['sgInstallModules'] = '';
                } else {
                    $this->sgConfig['sgInstallModules'] = serialize($arrModules);
                }
            }

            // Delete the theme
            if ($objTheme = \ThemeModel::findByPk($this->sgConfig['sgInstallTheme'])) {
                if ($objTheme->delete()) {
                    $this->sgConfig['sgInstallTheme'] = '';
                    $this->logs[] = ['status' => 'tl_confirm', 'msg' => sprintf('Le thème %s a été supprimé', $objTheme->name)];
                }
            }

            // Delete the templates folder
            $objFiles = \Files::getInstance();
            if (file_exists(TL_ROOT.'/templates/smartgear')) {
                $objFiles->rrdir('templates/smartgear');
            }

            // Delete the robots file
            /*if (file_exists(TL_ROOT."/web/robots.txt")) {
                $objFiles->delete("web/robots.txt");
            }*/

            // Finally, reset the config
            $this->sgConfig['sgInstallComplete'] = '';
            Util::updateConfig($this->sgConfig);
            $this->logs[] = ['status' => 'tl_confirm', 'msg' => 'Désinstallation terminée'];

            // And return an explicit status with some instructions
            return [
                'toastr' => [
                    'status' => 'success', 'msg' => 'La désinstallation de Smartgear a été effectuée avec succès.',
                ], 'callbacks' => [
                    0 => [
                        'method' => 'refreshAllBlocks',
                    ],
                ],
            ];
        } catch (Exception $e) {
            Util::updateConfig($this->sgConfig);
            throw $e;
        }
    }

    /**
     * Reset Contao install by Truncating everything.
     *
     * @todo Plan to build a "hard" & a "soft" reset. The hard should truncate data & files where as the soft should truncate only data (current behaviour)
     */
    public function resetContao()
    {
        try {
            // Clear Smartgear install before, if exists (useful for config vars)
            $this->remove();

            $objDb = \Database::getInstance();
            $arrSkipTables = ['tl_user', 'tl_files'];
            foreach ($objDb->listTables() as $strTable) {
                // Do not delete user table
                if (in_array($strTable, $arrSkipTables)) {
                    continue;
                }
                $objDb->prepare('TRUNCATE TABLE '.$strTable)->execute();
            }

            $objFiles = \Files::getInstance();
            $objFiles->rrdir('templates', true);
            $objFiles->rrdir('app/Resources');

            $this->logs[] = ['status' => 'tl_confirm', 'msg' => 'Contao a été réinitialisé'];

            // And return an explicit status with some instructions
            return [
                'toastr' => [
                    'status' => 'success', 'msg' => 'La réinitialisation de Contao a été effectuée avec succès.',
                ], 'callbacks' => [
                    0 => [
                        'method' => 'refreshAllBlocks',
                    ],
                ],
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }
}
