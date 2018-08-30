<?php

/**
 * SMARTGEAR for Contao Open Source CMS
 *
 * Copyright (c) 2015-2018 Web ex Machina
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */

namespace WEM\SmartGear\Backend\Core;

use \Exception;
use Contao\Config;
use Contao\PageModel;
use Contao\ModuleModel;
use Contao\NewsArchiveModel;
use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\FrontendTemplate;

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
	 * Constructor
	 */
	public function __construct(){
		$this->type = "core";
		$this->module = "core";
		$this->icon = "exclamation-triangle";

		parent::__construct();
	}

	/**
	 * Check Smartgear Status
	 * @return [String] [Template of the module check status]
	 */
	public function getStatus(){
		// Check if Smartgear is install
		if(!Config::get('sgInstallComplete')){
			$this->title = "Smartgear | Core | Installation";
			$this->messages[] = ["text"=>"Avant de faire quoique ce soit l'ami, tu vas devoir installer quelques trucs de base. Pas de soucis, on gère tout ça pour toi. Voilà ce qui est prévu :"];
			$this->messages[] = ["text"=>"<ul>
			<li>Modification de la configuration (tailles des images, limite d'upload...)</li>
			<li>Création des répertoires des modèles, avec import de ceux qu'on a modifier</li>
			<li>Vérification des répertoires des fichiers (pour l'app et pour le client). On doit bien avoir le framway d'installé dans files/app avant la prochaine étape.</li>
			<li>Création du thème Smartgear</li>
			<li>Création des squelettes Smartgear</li>
			<li>Création d'un groupe d'utilisateurs par défaut. Les droits seront probablement à ajuster.</li>
			<li>Création de la racine de site, avec le titre configuré dans le champ ci-dessous.</li>
			<li>Création d'une passerelle de notification par défaut (Email de service). Pensez à configurer le SMTP si besoin.</li>
		</ul>"];
			$this->messages[] = ["class"=>"tl_info", "text"=>"A noter que tout cela sera prochainement découpé en étapes, pour permettre de configurer chaque module plus précisément."];

			$this->fields[] = ['name'=>'websiteTitle', 'value'=>Config::get('websiteTitle'), 'label'=>'Titre du site internet', 'help'=>'Saisir le titre du site internet'];
			$this->actions[] = ['action'=>'setup', 'label'=>'Installer Smartgear'];

		} else {
			$this->title = "Smartgear | Core | Réparation - Désinstallation";

			$this->messages[] = ['class' => 'tl_error', 'text' => 'Vous pouvez réparer ou réinitialiser la configuration Smartgear établie. Veuillez prendre note que cela supprimera tous les éléments liés aux thèmes, squelettes, modules associés !'];
			$this->messages[] = ['class' => 'tl_error', 'text' => 'Vous pouvez également réinitialiser la totalité des données Contao. Tous les fichiers et toutes les données seront supprimés.'];

			$this->actions[] = ['action'=>'reset', 'label'=>'Réinitialiser Smartgear', 'attributes'=>'onclick="if(!confirm(\'Voulez-vous vraiment réinitialiser Smartgear ?\'))return false;Backend.getScrollOffset()"'];
			$this->actions[] = ['action'=>'delete', 'label'=>'Supprimer Smartgear', 'attributes'=>'onclick="if(!confirm(\'Voulez-vous vraiment supprimer Smartgear ?\'))return false;Backend.getScrollOffset()"'];
			$this->actions[] = ['action'=>'truncate', 'label'=>'Réinitialiser Contao', 'attributes'=>'onclick="if(!confirm(\'Voulez-vous vraiment réinitialiser Contao ?\'))return false;Backend.getScrollOffset()"'];
		}
	}

	/**
	 * Install Smartgear
	 */
	public function install(){
		try{
			// Prepare the log array
			$this->logs[] = ["status"=>"tl_info", "msg"=>"Début de l'installation"];

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
			$this->logs[] = ["status"=>"tl_confirm", "msg"=>"Configuration importée"];

			// Create templates and rsce folders and Move all Smartgear files in this one
			$this->processRSCE('install');

			// Check app folders and check if there is all Jeff stuff loaded
			if(!file_exists("files/app/build/css/framway.css") || !file_exists("files/app/build/css/vendor.css") || !file_exists("files/app/build/js/framway.js") || !file_exists("files/app/build/js/vendor.js"))
				throw new Exception("Des fichiers Framway sont manquants !");
			$this->logs[] = ["status"=>"tl_confirm", "msg"=>"Les fichiers Smartgear ont été trouvés (framway.css, framway.js, vendor.css, vendor.js)"];

			// Check if there is another themes and warn the user
			if(\ThemeModel::countAll() > 0)
				$this->logs[] = ["status"=>"tl_info", "msg"=>"Attention, il existe d'autres thèmes potentiellement utilisés sur ce Contao"];

			// Create the Smartgear main theme
			$objTheme = new \ThemeModel();
			$objTheme->tstamp = time();
			$objTheme->name = "Smargear";
			$objTheme->author = "Web ex Machina";
			$objTheme->templates = "templates/smartgear";
			$objTheme->save();
			Config::persist("sgInstallTheme", $objTheme->id);
			$this->logs[] = ["status"=>"tl_confirm", "msg"=>sprintf("Le thème %s a été créé et sera utilisé pour la suite de la configuration", $objTheme->name)];

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
			$this->logs[] = ["status"=>"tl_confirm", "msg"=>sprintf("Les modules principaux ont été créés", $objTheme->name)];

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
			$this->logs[] = ["status"=>"tl_confirm", "msg"=>sprintf("Le layout %s a été créé et sera utilisé pour la suite de la configuration", $objLayout->name)];

			// Create the default user group
			$objUserGroup = new \UserGroupModel();
			$objUserGroup->tstamp = time();
			$objUserGroup->name = "Administrateurs";
			$objUserGroup->save();
			Config::persist("sgInstallUserGroup", $objUserGroup->id);
			$this->logs[] = ["status"=>"tl_confirm", "msg"=>sprintf("Le groupe d'utilisateurs %s a été créé", $objUserGroup->name)];

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
			$this->logs[] = ["status"=>"tl_confirm", "msg"=>sprintf("L'utilisateur %s a été créé", $objUser->name)];

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
			$objRootPage->sitemapName = substr("sitemap-".$objRootPage->alias, 0, 30);
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
			$this->logs[] = ["status"=>"tl_confirm", "msg"=>sprintf("Le site Internet %s a été créé", $objRootPage->title)];

			// Generate a gateway in the Notification Center
			$objGateway = new \NotificationCenter\Model\Gateway();
			$objGateway->tstamp = time();
			$objGateway->title = "Email de service - Smartgear";
			$objGateway->type = "email";
			$objGateway->save();
			Config::persist("sgInstallNcGateway", $objGateway->id);
			$this->logs[] = ["status"=>"tl_confirm", "msg"=>sprintf("La passerelle (Notification Center) %s a été créée", $objGateway->title)];

			// Finally, notify in Config that the install is complete :)
			Config::persist("sgInstallComplete", 1);
			$this->logs[] = ["status"=>"tl_confirm", "msg"=>"Installation terminée"];
		}
		catch(Exception $e){
			$this->remove();
			throw $e;
		}
	}	

	/**
	 * Remove Smartgear
	 */
	public function remove(){
		try{
			// Delete the Gateway
			if($objGateway = \NotificationCenter\Model\Gateway::findByPk(Config::get('sgInstallNcGateway'))){
				if($objGateway->delete()){
					Config::remove("sgInstallNcGateway");
					$this->logs[] = ["status"=>"tl_confirm", "msg"=>sprintf("La passerelle (Notification Center) %s a été supprimée", $objGateway->title)];
				}
			}

			// Delete the root page
			if($objRootPage = \PageModel::findByPk(Config::get('sgInstallRootPage'))){
				if($objRootPage->delete()){
					Config::remove("sgInstallRootPage");
					$this->logs[] = ["status"=>"tl_confirm", "msg"=>sprintf("Le site Internet %s a été supprimé", $objRootPage->title)];
				}
			}

			// Delete the user
			if($objUser = \UserModel::findByPk(Config::get('sgInstallUser'))){
				if($objUser->delete()){
					Config::remove("sgInstallUser");
					$this->logs[] = ["status"=>"tl_confirm", "msg"=>sprintf("L'utilisateur %s a été supprimé", $objUser->name)];
				}
			}

			// Delete the user group
			if($objUserGroup = \UserModel::findByPk(Config::get('sgInstallUserGroup'))){
				if($objUserGroup->delete()){
					Config::remove("sgInstallUserGroup");
					$this->logs[] = ["status"=>"tl_confirm", "msg"=>sprintf("Le groupe d'utilisateur %s a été supprimé", $objUserGroup->name)];
				}
			}

			// Delete the layout
			if($objLayout = \LayoutModel::findByPk(Config::get('sgInstallLayout'))){
				if($objLayout->delete()){
					Config::remove("sgInstallLayout");
					$this->logs[] = ["status"=>"tl_confirm", "msg"=>sprintf("Le squelette %s a été supprimé", $objLayout->name)];
				}
			}

			// Delete the modules
			$arrModules = deserialize(Config::get('sgInstallModules'));
			if(is_array($arrModules) && !empty($arrModules)){
				foreach($arrModules as $intKey => $intModule){
					if($objModule = \ModuleModel::findByPk($intModule)){
						if($objModule->delete()){
							unset($arrModules[$intKey]);
							$this->logs[] = ["status"=>"tl_confirm", "msg"=>sprintf("Le module %s a été supprimé", $objModule->name)];
						}
					}
				}

				// Clear the config
				if(empty($arrModules))
					Config::remove("sgInstallModules");
				else
					Config::persist("sgInstallModules", serialize($arrModules));
			}

			// Delete the theme
			if($objTheme = \ThemeModel::findByPk(Config::get('sgInstallTheme'))){
				if($objTheme->delete()){
					Config::remove("sgInstallTheme");
					$this->logs[] = ["status"=>"tl_confirm", "msg"=>sprintf("Le thème %s a été supprimé", $objTheme->name)];
				}
			}
			
			// Delete Smartgear files
			$this->processRSCE('delete');

			// Finally, reset the config
			Config::remove("sgInstallComplete");
			$this->logs[] = ["status"=>"tl_confirm", "msg"=>"Désinstallation terminée"];
		}
		catch(Exception $e){
			throw $e;
		}
	}

	/**
	 * Reset Contao install by Truncating everything
	 */
	public function resetContao(){
		try{
			// Clear Smartgear install before, if exists (useful for config vars)
			$this->remove();

			$objDb = Database::getInstance();
			foreach($objDb->listTables() as $strTable)
				$objDb->prepare("TRUNCATE TABLE ".$strTable)->execute();

			$objFiles = Files::getInstance();
			$objFiles->rrdir("files");
			$objFiles->rrdir("templates");

			$this->logs[] = ["status"=>"tl_confirm", "msg"=>"Contao a été réinitialisé"];
		}
		catch(Exception $e){
			throw $e;
		}
	}
}