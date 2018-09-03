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

use WEM\SmartGear\Backend\Module as ModulePath;

/**
 * Back end module "smartgear".
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Install extends \BackendModule
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
		,"module" => ["blog", "calendar", "faq", "forms", "newsletter"]
	];

	/**
	 * Generate the module
	 *
	 * @throws Exception
	 */
	protected function compile()
	{
		// Back button
		$this->Template->backButtonHref = \Environment::get('request');
		$this->Template->backButtonTitle = \StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']);
		$this->Template->backButtonButton = $GLOBALS['TL_LANG']['MSC']['backBT'];

		// Add WEM styles to template
		$GLOBALS['TL_CSS'][] = $this->strBasePath.'/assets/backend/wemsg.css';

		// Parse Smartgear components
		foreach($this->modules as $type => $blocks){
			foreach($blocks as $block){
				$objModule = Util::findAndCreateObject($type, $block);
				$arrBlocks[$type][] = $objModule->parse();
			}
		}

		// Send msc data to template
		$this->Template->request = \Environment::get('request');
		$this->Template->token = \RequestToken::get();
		$this->Template->websiteTitle = \Config::get("websiteTitle");
		$this->Template->blocks = $arrBlocks;
	}

	/**
	 * Process AJAX actions
	 */
	public function processAjaxRequest($strAction){

		// Catch AJAX Requests
		if(\Input::post('TL_WEM_AJAX') && 'be_smartgear' == \Input::post('wem_module')){
			try{
				// Check if we get all the params we need first
				if(!\Input::post('type') || !\Input::post('module') || !\Input::post('action'))
					throw new Exception("Missing one or several arguments : type/module/action");
				
				$objModule = Util::findAndCreateObject(\Input::post('type'), \Input::post('module'));

				// Check if the method asked exists
				if(!method_exists($objModule, $strAction))
					throw new Exception(sprintf("Unknown method %s in Class %s", $strAction, get_class($objModule)));

				// Just make sure we return a response in the asked format, if no format sent, we assume it's JSON.
				if("html" == \Input::post('format')){
					echo $objModule->$strAction();
					die;
				}

				// Launch the action and store the result
				$arrResponse = $objModule->$strAction();
				$arrResponse["logs"] = $objModule->logs;			
			}
			catch(Exception $e){
				$arrResponse = ["status"=>"error", "msg"=>$e->getMessage(), "trace"=>$e->getTrace()];
			}

			// Add Request Token to JSON answer and return
			$arrResponse["rt"] = \RequestToken::get();
			echo json_encode($arrResponse);
			die;
		}
	}
}
