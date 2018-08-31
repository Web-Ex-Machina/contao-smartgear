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
use Contao\Database;
use Contao\Environment;
use Contao\Files;
use Contao\Input;
use Contao\Message;
use Contao\RequestToken;
use Contao\StringUtil;
use Contao\FrontendTemplate;

use WEM\SmartGear\Backend\Module as ModulePath;

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
	 * Module basepath
	 * @var string
	 */
	protected $strBasePath = 'system/modules/wem-contao-smartgear';

	/**
	 * Available modules
	 * @var array
	 */
	protected $modules = [
		"core" => ["core", "rsce"]
		,"module" => ["blog", "calendar", "forms", "faq", "newsletter"]
	];

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
		$GLOBALS['TL_CSS'][] = $this->strBasePath.'/assets/backend/wemsg.css';

		// Check if we already completed the Smartgear setup
		if(Config::get('sgInstallComplete')){
			$this->Template->isSetupComplete = true;
			
			foreach($this->modules as $type => $blocks){
				foreach($blocks as $block){
					$objModule = Util::findAndCreateObject($type, $block);
					$arrBlocks[$type][] = $objModule->parse();
				}
			}
			
		}

		// Send msc data to template
		$this->Template->request = Environment::get('request');
		$this->Template->token = RequestToken::get();
		$this->Template->websiteTitle = Config::get("websiteTitle");
		$this->Template->blocks = $arrBlocks;
	}

	/**
	 * Process AJAX actions
	 */
	public function processAjaxRequest($strAction){

		// Catch AJAX Requests
		if(Input::post('TL_WEM_AJAX') && 'be_smartgear' == Input::post('wem_module')){
			try{
				switch($strAction){
					case 'refreshBlock':
						if(!Input::post('type') || !Input::post('module'))
							throw new Exception("Missing arguments type or/and module");

						$objModule = Util::findAndCreateObject(Input::post('type'), Input::post('module'));
						echo $objModule->parse(); die;
					break;
					default:
						throw new Exception(sprintf("Unknown AJAX action %s", $strAction));
				}
			}
			catch(Exception $e){
				$arrResponse = ["status"=>"error", "msg"=>$e->getMessage(), "trace"=>$e->getTrace()];
			}

			// Add Request Token to JSON answer and return
			$arrResponse["rt"] = RequestToken::get();
			echo json_encode($arrResponse);
			die;
		}
	}
}
