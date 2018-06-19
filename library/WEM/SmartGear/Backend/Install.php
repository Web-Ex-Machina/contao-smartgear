<?php

/**
 * SMARTGEAR for Contao Open Source CMS
 *
 * Copyright (c) 2015-2018 Web ex Machina
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */

namespace WEM\SmartGear\Backend;

use Exception;
use Contao\BackendModule;
use Contao\Config;
use Contao\Environment;
use Contao\Files;
use Contao\Input;
use Contao\Message;
use Contao\RequestToken;
use Contao\StringUtil;

/**
 * Back end module "smartgear".
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Install extends BackendModule
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'be_wem_sg_install';

	/**
	 * Logs
	 * @var array
	 */
	protected $arrLogs = array();

	/**
	 * Generate the module
	 *
	 * @throws Exception
	 */
	protected function compile()
	{
		// Back button
		$this->Template->backButtonHref = Environment::get('request');
		$this->Template->backButtonTitle = StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']);
		$this->Template->backButtonButton = $GLOBALS['TL_LANG']['MSC']['backBT'];

		// Add WEM styles to template
		$GLOBALS['TL_CSS'][] = 'system/modules/wem-contao-smartgear/assets/backend/wemsg.css';

		// Catch the setup submit
		if(Input::post("FORM_SUBMIT") == "tl_wem_sg_install")
		{
			try
			{
				switch(Input::post("sg_step")){
					case "setup":
						// Launch setup function
						$this->installSmartgear();
					break;

					case "repair":
						// In both cases, we need to trash everything linked to Smartgear
						$this->deleteSmartgear();

						// And if reset, relaunch the setup function
						if(Input::post("action") == "reset")
							$this->installSmartgear();
					break;

					case "rsce":
						if(Input::post("action") == "reimport"){
							$this->processRSCE('delete');
							$this->processRSCE('install');
						}
					break;

					case "module":
						$strClass = "WEM\SmartGear\Backend\Module\\".ucfirst(Input::post('sg_module'));
						if(!class_exists($strClass))
							throw new Exception(sprintf("Classe inconnue : %s", $strClass));
						$objModule = new $strClass();
						if(!method_exists($objModule, Input::post('action')))
							throw new Exception(sprintf("Méthode %s inconnue dans la classe %s", Input::post('action'), get_class($objModule)));
						call_user_func([$objModule, Input::post('action')]);
					break;

					default:
						throw new Exception(sprintf("Action inconnue : %s", Input::post("sg_step")));
				}

				$this->Template->logs = $arrLogs;
			}
			catch(Exception $e)
			{
				Message::addError($e->getResponse());
			}
		}

		// Check if we already completed the Smartgear setup
		if(Config::get('sgInstallComplete')){
			$this->Template->isSetupComplete = true;
			$bundles = \System::getContainer()->getParameter('kernel.bundles');

			// Find News Module
			$arrModules["blog"] = array();
			if(!isset($bundles['ContaoNewsBundle'])){
				$arrModules["blog"]["status"] = 0;
				$arrModules["blog"]["class"] = 'tl_error';
				$arrModules["blog"]["msg"] = 'Le blog n\'est pas installé. Veuillez utiliser le <a href="{{env::/}}/contao-manager.phar.php" title="Contao Manager" target="_blank">Contao Manager</a> pour cela.';
			} else if(!Config::get('sgBlogInstall') || 0 === \NewsArchiveModel::countById(Config::get('sgBlogNewsArchive'))){
				$arrModules["blog"]["status"] = 1;
				$arrModules["blog"]["class"] = 'tl_info';
				$arrModules["blog"]["msg"] = 'Le blog est installé, mais pas configuré.';
				$arrModules["blog"]["actions"][] = ['action'=>'install', 'label'=>'Installer'];
			} else {
				$arrModules["blog"]["status"] = 2;
				$arrModules["blog"]["class"] = 'tl_confirm';
				$arrModules["blog"]["msg"] = 'Le blog est installé et configuré.';
				$arrModules["blog"]["actions"][] = ['action'=>'reset', 'label'=>'Réinitialiser'];
				$arrModules["blog"]["actions"][] = ['action'=>'remove', 'label'=>'Supprimer'];
			}
			
		}

		// Send msc data to template
		$this->Template->request = Environment::get('request');
		$this->Template->token = RequestToken::get();
		$this->Template->websiteTitle = Config::get("websiteTitle");
		$this->Template->modules = $arrModules;
	}

	/**
	 * Process to the setup
	 */
	protected function installSmartgear(){
		try
		{
			// Prepare the log array
			$this->arrLogs[] = ["status"=>"tl_info", "msg"=>"Début de la déinstallation"];

			// Store the default config
			Config::persist("websiteTitle", Input::post('websiteTitle'));
			Config::persist("dateFormat", "d/m/Y");
			Config::persist("timeFormat", "H:i");
			Config::persist("datimFormat", "d/m/Y à H:i");
			Config::persist("timeZone", "Europe/Paris");
			Config::persist("adminEmail", "contact@webexmachina.fr");
			Config::persist("characterSet", "utf-8");
			Config::persist("useAutoItem", 1);
			Config::persist("privacyAnonymizeIp", 1);
			Config::persist("privacyAnonymizeGA", 1);
			Config::persist("gdMaxImgWidth", 5000);
			Config::persist("gdMaxImgHeight", 5000);
			Config::persist("maxFileSize", 20971520);
			$this->arrLogs[] = ["status"=>"tl_confirm", "msg"=>"Configuration importée"];

			// Create templates and rsce folders and Move all Smartgear files in this one
			$this->processRSCE('install');

			// Check app folders and check if there is all Jeff stuff loaded
			if(!file_exists("files/app/build/css/framway.css") || !file_exists("files/app/build/css/vendor.css") || !file_exists("files/app/build/js/framway.js") || !file_exists("files/app/build/js/vendor.js"))
				throw new Exception("Des fichiers Framway sont manquants !");
			$this->arrLogs[] = ["status"=>"tl_confirm", "msg"=>"Les fichiers SmartGear ont été trouvés (framway.css, framway.js, vendor.css, vendor.js)"];

			// Check if there is another themes and warn the user
			if(\ThemeModel::countAll() > 0)
				$this->arrLogs[] = ["status"=>"tl_info", "msg"=>"Attention, il existe d'autres thèmes potentiellement utilisés sur ce Contao"];

			// Create the Smartgear main theme
			$objTheme = new \ThemeModel();
			$objTheme->tstamp = time();
			$objTheme->name = "Smargear";
			$objTheme->author = "Web ex Machina";
			$objTheme->templates = "templates/smartgear";
			$objTheme->save();
			Config::persist("sgInstallTheme", $objTheme->id);
			$this->arrLogs[] = ["status"=>"tl_confirm", "msg"=>sprintf("Le thème %s a été créé et sera utilisé pour la suite de la configuration", $objTheme->name)];

			// Create the Smartgear main modules
			$arrLayoutModules = array();
			$arrModules = array();

			// Header - Logo
			$objModule = new \ModuleModel();
			$objModule->pid = $objTheme->id;
			$objModule->tstamp = time();
			$objModule->type = "html";
			$objModule->name = "HEADER - LOGO";
			$objModule->html = '
				<div id="logo">
					<a class="logo__container" href="{{env::url}}">
						<img src="files/app_v1/build/img/logo_placeholder.png" title="Your logo goes here">
					</a>
				</div>
			';
			$objModule->save();
			$arrLayoutModules[] = ["mod"=>$objModule->id, "col"=>"header", "enable"=>"1"];
			$arrModules[] = $objModule->id;

			// Header - Nav
			$objModule = new \ModuleModel();
			$objModule->pid = $objTheme->id;
			$objModule->tstamp = time();
			$objModule->type = "navigation";
			$objModule->name = "HEADER - NAV";
			$objModule->showLevel = 3;
			$objModule->navigationTpl = "nav_default";
			$objModule->save();
			$arrLayoutModules[] = ["mod"=>$objModule->id, "col"=>"header", "enable"=>"1"];
			$arrModules[] = $objModule->id;

			// Main - Articles
			$arrLayoutModules[] = ["mod"=>0, "col"=>"main", "enable"=>"1"];

			// Footer
			$objModule = new \ModuleModel();
			$objModule->pid = $objTheme->id;
			$objModule->tstamp = time();
			$objModule->type = "html";
			$objModule->name = "FOOTER";
			$objModule->html = '<div class="footer__copyright">© {{date::Y}} '.Config::get('websiteTitle').'</div>';
			$objModule->save();
			$arrLayoutModules[] = ["mod"=>$objModule->id, "col"=>"footer", "enable"=>"1"];
			$arrModules[] = $objModule->id;

			Config::persist("sgInstallModules", serialize($arrModules));
			$this->arrLogs[] = ["status"=>"tl_confirm", "msg"=>sprintf("Les modules principaux ont été créés", $objTheme->name)];

			// Create the Smartgear main layout
			$arrCssFiles = array();
			$arrJsFiles = array();
			$objFile = \FilesModel::findOneByPath("files/app/build/css/framway.css");
			$arrCssFiles[] = $objFile->uuid;
			$objFile = \FilesModel::findOneByPath("files/app/build/css/vendor.css");
			$arrCssFiles[] = $objFile->uuid;
			$objFile = \FilesModel::findOneByPath("files/app/build/js/framway.js");
			$arrJsFiles[] = $objFile->uuid;
			$objFile = \FilesModel::findOneByPath("files/app/build/js/vendor.js");
			$arrJsFiles[] = $objFile->uuid;

			$objLayout = new \LayoutModel();
			$objLayout->pid = $objTheme->id;
			$objLayout->name = "Page Standard";
			$objLayout->rows = "3rw";
			$objLayout->cols = "1cl";
			$objLayout->framework = '';
			$objLayout->stylesheet = '';
			$objLayout->external = serialize($arrCssFiles);
			$objLayout->orderExt = serialize($arrCssFiles);
			$objLayout->loadingOrder = "external_first";
			$objLayout->combineScripts = 1;
			$objLayout->doctype = "html5";
			$objLayout->template = "fe_page";
			$objLayout->viewport = "width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=0";
			$objLayout->externalJs = serialize($arrJsFiles);
			$objLayout->orderExtJs = serialize($arrJsFiles);
			$objLayout->modules = serialize($arrLayoutModules);
			$objLayout->save();
			Config::persist("sgInstallLayout", $objLayout->id);
			$this->arrLogs[] = ["status"=>"tl_confirm", "msg"=>sprintf("Le layout %s a été créé et sera utilisé pour la suite de la configuration", $objLayout->name)];

			// Create the default user group
			$objUserGroup = new \UserGroupModel();
			$objUserGroup->tstamp = time();
			$objUserGroup->name = "Administrateurs";
			$objUserGroup->save();
			Config::persist("sgInstallUserGroup", $objUserGroup->id);
			$this->arrLogs[] = ["status"=>"tl_confirm", "msg"=>sprintf("Le groupe d'utilisateurs %s a été créé", $objUserGroup->name)];

			// Add a default user to the user group
			$objUser = new \UserModel();
			$objUser->tstamp = time();
			$objUser->dateAdded = time();
			$objUser->username = "webmaster";
			$objUser->name = "Webmaster";
			$objUser->email = "to@fill.fr";
			$objUser->language = "fr";
			$objUser->backendTheme = "flexible";
			$objUser->fullscreen = 1;
			$objUser->uploader = "DropZone";
			$objUser->pwChange = 1;
			$objUser->save();
			Config::persist("sgInstallUser", $objUser->id);
			$this->arrLogs[] = ["status"=>"tl_confirm", "msg"=>sprintf("L'utilisateur %s a été créé", $objUser->name)];

			// Generate a root page with the stuff previously created
			$objRootPage = new \PageModel();
			$objRootPage->pid = 0;
			$objRootPage->sorting = (\PageModel::countByPid(0) + 1) * 128;
			$objRootPage->tstamp = time();
			$objRootPage->title = Config::get('websiteTitle');
			$objRootPage->alias = StringUtil::generateAlias($objRootPage->title);
			$objRootPage->type = "root";
			$objRootPage->language = "fr";
			$objRootPage->fallback = 1;
			$objRootPage->createSitemap = 1;
			$objRootPage->sitemapName = "sitemap-".$objRootPage->alias;
			$objRootPage->useSSL = 1;
			$objRootPage->includeLayout = 1;
			$objRootPage->layout = $objLayout->id;
			$objRootPage->includeChmod = 1;
			$objRootPage->cuser = $objUser->id;
			$objRootPage->cgroup = $objUserGroup->id;
			$objRootPage->chmod = 'a:12:{i:0;s:2:"u1";i:1;s:2:"u2";i:2;s:2:"u3";i:3;s:2:"u4";i:4;s:2:"u5";i:5;s:2:"u6";i:6;s:2:"g1";i:7;s:2:"g2";i:8;s:2:"g3";i:9;s:2:"g4";i:10;s:2:"g5";i:11;s:2:"g6";}';
			$objRootPage->published = 1;
			$objRootPage->save();
			Config::persist("sgInstallRootPage", $objRootPage->id);
			$this->arrLogs[] = ["status"=>"tl_confirm", "msg"=>sprintf("Le site Internet %s a été créé", $objRootPage->title)];

			// Generate a gateway in the Notification Center
			$objGateway = new \NotificationCenter\Model\Gateway();
			$objGateway->tstamp = time();
			$objGateway->title = "Email de service - Smartgear";
			$objGateway->type = "email";
			$objGateway->save();
			Config::persist("sgInstallNcGateway", $objGateway->id);
			$this->arrLogs[] = ["status"=>"tl_confirm", "msg"=>sprintf("La passerelle (Notification Center) %s a été créée", $objGateway->title)];

			// Finally, notify in Config that the install is complete :)
			Config::persist("sgInstallComplete", 1);
			$this->arrLogs[] = ["status"=>"tl_confirm", "msg"=>"Installation terminée"];
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}

	/**
	 * Delete all the Smartgear setup
	 */
	protected function deleteSmartgear(){
		try
		{
			// Delete the Gateway
			if($objGateway = \NotificationCenter\Model\Gateway::findByPk(Config::get('sgInstallNcGateway'))){
				if($objGateway->delete()){
					Config::remove("sgInstallNcGateway");
					$this->arrLogs[] = ["status"=>"tl_confirm", "msg"=>sprintf("La passerelle (Notification Center) %s a été supprimée", $objGateway->title)];
				}
			}

			// Delete the root page
			if($objRootPage = \PageModel::findByPk(Config::get('sgInstallRootPage'))){
				if($objRootPage->delete()){
					Config::remove("sgInstallRootPage");
					$this->arrLogs[] = ["status"=>"tl_confirm", "msg"=>sprintf("Le site Internet %s a été supprimé", $objRootPage->title)];
				}
			}

			// Delete the user
			if($objUser = \UserModel::findByPk(Config::get('sgInstallUser'))){
				if($objUser->delete()){
					Config::remove("sgInstallUser");
					$this->arrLogs[] = ["status"=>"tl_confirm", "msg"=>sprintf("L'utilisateur %s a été supprimé", $objUser->name)];
				}
			}

			// Delete the user group
			if($objUserGroup = \UserModel::findByPk(Config::get('sgInstallUserGroup'))){
				if($objUserGroup->delete()){
					Config::remove("sgInstallUserGroup");
					$this->arrLogs[] = ["status"=>"tl_confirm", "msg"=>sprintf("Le groupe d'utilisateur %s a été supprimé", $objUserGroup->name)];
				}
			}

			// Delete the layout
			if($objLayout = \LayoutModel::findByPk(Config::get('sgInstallLayout'))){
				if($objLayout->delete()){
					Config::remove("sgInstallLayout");
					$this->arrLogs[] = ["status"=>"tl_confirm", "msg"=>sprintf("Le squelette %s a été supprimé", $objLayout->name)];
				}
			}

			// Delete the modules
			$arrModules = deserialize(Config::get('sgInstallModules'));
			if(is_array($arrModules) && !empty($arrModules)){
				foreach($arrModules as $intKey => $intModule){
					if($objModule = \ModuleModel::findByPk($intModule)){
						if($objModule->delete()){
							unset($arrModules[$intKey]);
							$this->arrLogs[] = ["status"=>"tl_confirm", "msg"=>sprintf("Le module %s a été supprimé", $objModule->name)];
						}
					}
				}

				// Clear the config
				if(empty($arrModules))
					Config::remove("sgInstallModules");
				else
					Config::persist(serialize($arrModules));
			}

			// Delete the theme
			if($objTheme = \ThemeModel::findByPk(Config::get('sgInstallTheme'))){
				if($objTheme->delete()){
					Config::remove("sgInstallTheme");
					$this->arrLogs[] = ["status"=>"tl_confirm", "msg"=>sprintf("Le thème %s a été supprimé", $objTheme->name)];
				}
			}
			
			// Delete Smartgear files
			$this->processRSCE('delete');

			// Finally, reset the config
			Config::remove("sgInstallComplete");
			$this->arrLogs[] = ["status"=>"tl_confirm", "msg"=>"Désinstallation terminée"];
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}

	/**
	 * Process RSCE elements
	 * @param  [String] $strMode [Install or Delete]
	 */
	protected function processRSCE($strMode = 'install')
	{
		try
		{
			$objFiles = Files::getInstance();

			// Create templates and rsce folders and Move all Smartgear files in this one
			if($strMode == 'install'){		
				$objFiles->rcopy("system/modules/wem-contao-smartgear/assets/templates_files", "templates/smartgear");
				$objFiles->rcopy("system/modules/wem-contao-smartgear/assets/rsce_files", "templates/rsce");
				$this->arrLogs[] = ["status"=>"tl_confirm", "msg"=>"Les templates SmartGear ont été importés (templates et rsce)"];
			}
			else if($strMode == 'delete'){
				$objFiles->rrdir("templates/smartgear");
				$objFiles->rrdir("templates/rsce");
				$this->arrLogs[] = ["status"=>"tl_confirm", "msg"=>"Les templates SmartGear ont été supprimés (templates et rsce)"];
			}
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}
}
