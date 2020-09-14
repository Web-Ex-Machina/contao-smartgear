<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2020 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

namespace WEM\SmartgearBundle\Backend\Core;

use Exception;
use WEM\SmartgearBundle\Backend\Block;
use WEM\SmartgearBundle\Backend\BlockInterface;
use WEM\SmartgearBundle\Backend\Util;

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
    public function reset(): void
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
                    if ($v !== $this->sgConfig[$k]) {
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
                    'name' => $objThemes->name, 'selected' => ($this->sgConfig['sgInstallTheme'] === $objThemes->id) ? true : false,
                ];
            }
        }
        $objTemplate->themes = $arrThemes;

        $objModules = \ModuleModel::findAll();
        if ($objModules) {
            $arrConfigModules = deserialize($this->sgConfig['sgInstallModules']);
            while ($objModules->next()) {
                $arrModules[$objModules->id] = [
                    'name' => $objModules->name, 'selected' => (\in_array($objModules->id, $arrConfigModules, true)) ? true : false,
                ];
            }
        }
        $objTemplate->modules = $arrModules;

        $objLayouts = \LayoutModel::findAll();
        $arrLayouts = [];
        if ($objLayouts) {
            while ($objLayouts->next()) {
                $arrLayouts[$objLayouts->id] = [
                    'name' => $objLayouts->name, 'selected' => ($this->sgConfig['sgInstallLayout'] === $objLayouts->id) ? true : false,
                ];
            }
        }
        $objTemplate->layouts = $arrLayouts;

        $objUserGroups = \UserGroupModel::findAll();
        $arrUserGroups = [];
        if ($objUserGroups) {
            while ($objUserGroups->next()) {
                $arrUserGroups[$objUserGroups->id] = [
                    'name' => $objUserGroups->name, 'selected' => ($this->sgConfig['sgInstallUserGroup'] === $objUserGroups->id) ? true : false,
                ];
            }
        }
        $objTemplate->usergroups = $arrUserGroups;

        $objRootPages = \PageModel::findByPid(0);
        $arrRootPages = [];
        if ($objRootPages) {
            while ($objRootPages->next()) {
                $arrRootPages[$objRootPages->id] = [
                    'name' => $objRootPages->title, 'selected' => ($this->sgConfig['sgInstallRootPage'] === $objRootPages->id) ? true : false,
                ];
            }
        }
        $objTemplate->rootpages = $arrRootPages;

        $objNcGateways = \NotificationCenter\Model\Gateway::findAll();
        $arrNcGateways = [];
        if ($objNcGateways) {
            while ($objNcGateways->next()) {
                $arrNcGateways[$objNcGateways->id] = [
                    'name' => $objNcGateways->title, 'selected' => ($this->sgConfig['sgInstallNcGateway'] === $objNcGateways->id) ? true : false,
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
            $objFiles = \Files::getInstance();

            // Prepare the log array
            $this->logs[] = ['status' => 'tl_info', 'msg' => "Début de l'installation"];

            // Create framway paths for later
            $rp = \System::getContainer()->getParameter('kernel.project_dir').'/';
            $fp = \Input::post('framwayPath');
            $mp = 'files/media';
            $vp = 'files/vendor';
            $fbp = $fp.'/build';
            $ftp = $fp.'/src/themes';
            $fttp = \Input::post('framwayTheme');

            // Check if Framway Folder exists
            if (!file_exists($rp.$fp)) {
                throw new Exception(sprintf('Le dossier Framway indiqué (%s) n\'existe pas', $fp));
            }

            // Check if Framway themes folder exists
            if (!file_exists($rp.$ftp)) {
                throw new Exception(sprintf('Le dossier du thème Framway indiqué (%s) n\'existe pas', $ftp));
            }

            // Check if the Framway Theme Folder exists
            if (!file_exists($rp.$fttp)) {
                throw new Exception(sprintf('Le dossier du thème Framway indiqué (%s) n\'existe pas', $fttp));
            }

            // Copy the vendors into the filesystem
            $objFiles->rcopy('web/bundles/wemsmartgear/vendor', 'files/vendor');

            // Check app folders and check if there is all Framway stuff loaded
            if (
                !file_exists($rp.$fbp.'/css/framway.css')
                || !file_exists($rp.$fbp.'/css/vendor.css')
                || !file_exists($rp.'/files/vendor/outdatedbrowser/outdatedbrowser.min.css')
                || !file_exists($rp.$fbp.'/js/framway.js')
                || !file_exists($rp.$fbp.'/js/vendor.js')
            ) {
                throw new Exception('Des fichiers sont manquants.');
            }
            $this->logs[] = ['status' => 'tl_confirm', 'msg' => 'Les fichiers Smartgear ont été trouvés (outdatedbrowser.min.css, framway.css, framway.js, vendor.css, vendor.js)'];

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

            // Make sure framway path is public
            $objFramwayFolder = new \Folder($fp);
            $objFramwayFolder->unprotect();

            // Make sure Contao knows the framway files
            \Dbafs::addResource($fp);

            // Import the folders
            $objFiles->rcopy('web/bundles/wemsmartgear/templates_files', 'templates');
            $objFiles->rcopy('web/bundles/wemsmartgear/templates_app', 'app');

            // Create the theme template folder
            $objFiles->mkdir(sprintf('templates/%s', \StringUtil::generateAlias($arrConfig['websiteTitle'])));

            // Copy package themes into framway folder
            $objFolder = new \Folder($ftp);
            $objFiles->rcopy('web/bundles/wemsmartgear/themes_framway', $ftp);
            $objFolder->unprotect();

            // Unprotect vendor folder
            $objFolder = new \Folder($vp);
            $objFolder->unprotect();
            \Dbafs::addResource($vp);

            // Check if there is another themes and warn the user
            if (\ThemeModel::countAll() > 0) {
                $this->logs[] = ['status' => 'tl_info', 'msg' => "Attention, il existe d'autres thèmes potentiellement utilisés sur ce Contao"];
            }

            // Create smartgear medias folder
            $objMediaFolder = new \Folder($mp);
            $objMediaFolder->unprotect();
            \Dbafs::addResource($mp);

            // Import the logo into files/medias/logos folder
            if (\Input::post('websiteLogo')) {
                $objFolder = new \Folder('files/medias/logos');
                $objLogo = Util::base64ToImage(\Input::post('websiteLogo'), 'files/medias/logos', 'logo');
                $objLogoModel = $objLogo->getModel();
            } else {
                $objLogoModel = \FilesModel::findOneByPath($fbp.'/img/logo_placeholder.png');
            }

            // Set up some config vars
            $this->sgConfig['websiteTitle'] = \Input::post('websiteTitle');
            $this->sgConfig['framwayPath'] = $fp;
            $this->sgConfig['framwayTheme'] = $fttp;
            $this->sgConfig['websiteLogo'] = $objLogoModel->path;
            $this->sgConfig['ownerTitle'] = \Input::post('ownerTitle');
            $this->sgConfig['ownerStatus'] = \Input::post('ownerStatus');
            $this->sgConfig['ownerSIRET'] = \Input::post('ownerSIRET');
            $this->sgConfig['ownerAddress'] = \Input::post('ownerAddress');
            $this->sgConfig['ownerEmail'] = \Input::post('ownerEmail');
            $this->sgConfig['ownerDomain'] = \Input::post('ownerDomain') ?: \Environment::get('base');
            $this->sgConfig['ownerHost'] = \Input::post('ownerHost');
            $this->logs[] = ['status' => 'tl_confirm', 'msg' => 'Configuration importée'];

            // Create the Smartgear main theme
            $objTheme = new \ThemeModel();
            $objTheme->tstamp = time();
            $objTheme->name = 'Smartgear';
            $objTheme->author = 'Web ex Machina';
            $objTheme->templates = sprintf('templates/%s', \StringUtil::generateAlias($arrConfig['websiteTitle']));
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
            $objModule->html = file_get_contents(TL_ROOT.'/web/bundles/wemsmartgear/examples/footer_1.html');
            $objModule->save();
            $arrLayoutModules[] = ['mod' => $objModule->id, 'col' => 'footer', 'enable' => '1'];
            $arrModules[] = $objModule->id;

            // Store & log module creation
            $this->sgConfig['sgInstallModules'] = serialize($arrModules);
            $this->logs[] = ['status' => 'tl_confirm', 'msg' => sprintf('Les modules principaux ont été créés', $objTheme->name)];

            // Create the Smartgear main layout
            $arrCssFiles = [];
            $arrJsFiles = [];
            $objFile = \FilesModel::findOneByPath('files/vendor/outdatedbrowser/outdatedbrowser.min.css');
            $arrCssFiles[] = $objFile->uuid;
            $objFile = \FilesModel::findOneByPath($fbp.'/css/vendor.css');
            $arrCssFiles[] = $objFile->uuid;
            $objFile = \FilesModel::findOneByPath($fbp.'/css/framway.css');
            $arrCssFiles[] = $objFile->uuid;
            $objFile = \FilesModel::findOneByPath($fbp.'/js/vendor.js');
            $arrJsFiles[] = $objFile->uuid;
            $objFile = \FilesModel::findOneByPath($fbp.'/js/framway.js');
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
            $objLayout->head = file_get_contents(TL_ROOT.'/web/bundles/wemsmartgear/examples/balises_supplementaires_1.js');
            $objLayout->script = file_get_contents(TL_ROOT.'/web/bundles/wemsmartgear/examples/code_javascript_personnalise_1.js');
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
            $objLayoutWithoutHeaderAndFooter->head = file_get_contents(TL_ROOT.'/web/bundles/wemsmartgear/examples/balises_supplementaires_1.js');
            $objLayoutWithoutHeaderAndFooter->script = file_get_contents(TL_ROOT.'/web/bundles/wemsmartgear/examples/code_javascript_personnalise_1.js');
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
            $objUserGroup->alexf = Util::addPermissions($this->getCorePermissions());
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

            // Create a 404 page, with a sitemap after
            $obj404Page = Util::createPage('Erreur 404 - Page non trouvée', $objRootPage->id, ['type' => 'error_404']);
            $objArticle = Util::createArticle($obj404Page);
            $objContent = Util::createContent($objArticle, [
                'headline' => serialize(['unit' => 'h1', 'value' => 'Page non trouvée !']), 'text' => "<p>La page demandée n'existe pas. Vous pouvez consulter le plan du site ci-dessous pour poursuivre votre navigation.</p>",
            ]);
            $objContent = Util::createContent($objArticle, [
                'type' => 'module', 'module' => $objModule->id,
            ]);

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
            $strText = file_get_contents(TL_ROOT.'/web/bundles/wemsmartgear/examples/legal-notices_1.html');
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
                    $this->sgConfig['ownerDomain'] ?: 'NR',
                    $this->sgConfig['ownerDomain'] ?: 'NR',
                    $this->sgConfig['ownerTitle'] ?: 'NR',
                    $this->sgConfig['ownerStatus'] ?: 'NR',
                    $this->sgConfig['ownerSIRET'] ?: 'NR',
                    $this->sgConfig['ownerAddress'] ?: 'NR',
                    $this->sgConfig['ownerEmail'] ?: 'NR',
                    $this->sgConfig['ownerHost'] ?: 'NR'
                );
            }

            $objContent = Util::createContent($objArticle, [
                'headline' => serialize(['unit' => 'h1', 'value' => 'Mentions légales']), 'text' => $strHtml,
            ]);

            // Create a Privacy Page
            $objPage = Util::createPage('Confidentialité', $objRootPage->id, ['hide' => 1]);
            $arrPageMounts[] = $objPage->id;
            $objArticle = Util::createArticle($objPage);
            $strText = file_get_contents(TL_ROOT.'/web/bundles/wemsmartgear/examples/privacy_1.html');
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
                    $this->sgConfig['ownerTitle'] ?: 'NR',
                    $this->sgConfig['ownerAddress'] ?: 'NR',
                    $this->sgConfig['ownerSIRET'] ?: 'NR',
                    $this->sgConfig['ownerDomain'].'/'.$objPage->alias.'.html',
                    date('d/m/Y'),
                    $this->sgConfig['ownerEmail'] ?: 'NR'
                );
            }
            $objContent = Util::createContent($objArticle, [
                'text' => $strHtml,
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
            $this->sgConfig['sgVersion'] = Util::getPackageVersion('webexmachina/contao-smartgear');
            $this->sgConfig['sgInstallComplete'] = 1;
            Util::updateConfig($this->sgConfig);

            // Refresh Cache
            Util::executeCmd('cache:warmup');

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
                        if (\is_array($arrModules) && \in_array($objModules->id, $arrModules, true)) {
                            unset($arrModules[array_search($objModules->id, $arrModules, true)]);
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

            // Call the https redirection rewriting
            $this->addHttpsRedirectToHtaccess();

            // Finally, reset the config
            $this->sgConfig['sgInstallComplete'] = '';
            Util::updateConfig($this->sgConfig);
            $this->logs[] = ['status' => 'tl_confirm', 'msg' => 'Désinstallation terminée'];

            // Refresh Cache
            Util::executeCmd('cache:warmup');

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

            // Make sure all the SG config is purged (since every table will be truncated)
            Util::resetConfig();

            $objDb = \Database::getInstance();
            $arrSkipTables = ['tl_user', 'tl_files'];
            foreach ($objDb->listTables() as $strTable) {
                // Do not delete user table
                if (\in_array($strTable, $arrSkipTables, true)) {
                    continue;
                }
                $objDb->prepare('TRUNCATE TABLE '.$strTable)->execute();
            }

            // Restore Contao default config
            \Config::remove('websiteTitle');
            \Config::remove('dateFormat');
            \Config::remove('timeFormat');
            \Config::remove('datimFormat');
            \Config::remove('timeZone');
            \Config::remove('adminEmail');
            \Config::remove('characterSet');
            \Config::remove('useAutoItem');
            \Config::remove('privacyAnonymizeIp');
            \Config::remove('privacyAnonymizeGA');
            \Config::remove('gdMaxImgWidth');
            \Config::remove('gdMaxImgHeight');
            \Config::remove('maxFileSize');
            \Config::remove('undoPeriod');
            \Config::remove('versionPeriod');
            \Config::remove('logPeriod');
            \Config::remove('allowedTags');

            // Clear user files and folders
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

    /**
     * Add "Redirect to https" in htaccess.
     *
     * @return [void]
     */
    protected function addHttpsRedirectToHtaccess()
    {
        try {
            // 1st: open htaccess
            $strContent = file_get_contents(TL_ROOT.'/web/.htaccess');

            // 2nd: check if we already have the https redirect
            if (false !== strpos($strContent, 'RewriteCond %{HTTPS} off')) {
                return;
            }

            // 3rd: Add the redirect to https directives after RewriteEngine On
            $str = 'RewriteEngine On'."\n";
            $str2 = '    RewriteCond %{HTTPS} off'."\n".'    RewriteRule .* https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]'."\n";
            $strContent = substr_replace(
                $strContent,
                $str2,
                strpos($strContent, $str) + \strlen($str),
                0
            );

            // 4th: write in the htaccess
            $f = fopen(TL_ROOT.'/web/.htaccess', 'w+');
            fwrite($f, $strContent);
            fclose($f);
        } catch (\Exception $e) {
            throw $e;
        }
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
