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
use Contao\Controller;
use Contao\Environment;
use Contao\FrontendTemplate;
use Contao\RequestToken;

/**
 * Back end module "smartgear".
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Block extends Controller
{
	/**
	 * Construct the block object
	 */
	public function __construct(){
		// Load the bundles, since we will need them in every block
		$this->bundles = \System::getContainer()->getParameter('kernel.bundles');

		// Load the Smartgear config, we will need it
		$this->sgConfig = Util::loadSmartgearConfig();

		// Instance some vars
		$this->strTemplate = 'be_wem_sg_install_block_default';
		$this->logs = [];
		$this->messages = [];
		$this->fields = [];
		$this->actions = [];

		$this->getStatus();
	}

	/**
	 * Reset the module
	 */
	public function reset(){
		$this->remove();
		$this->install();

		// And return an explicit status with some instructions
		return [
			"toastr" => [
				"status"=>"success"
				,"msg"=>"Réinitialisation effectuée avec succès."
			]
			,"callbacks" => [
				0 => [
					"method" => "refreshBlock"
					,"args"	 => ["block-".$this->type."-".$this->module]
				]
			]
		];
	}

	/**
	 * Parse and return the block as HTML
	 * @return [String] [Block HTML]
	 */
	public function parse(){
		// Create the block template and add some general vars
		$objTemplate = new FrontendTemplate($this->strTemplate);
		$objTemplate->request = Environment::get('request');
		$objTemplate->token = RequestToken::get();
		$objTemplate->type = $this->type;
		$objTemplate->module = $this->module;
		$objTemplate->title = $this->title;
		$objTemplate->icon = $this->icon;

		// Check if we need other modules and if yes, check if it's okay
		$blnCanManage = true;
		if($this->require){
			$arrMissingModules = [];
			foreach($this->require as $strModule){
				$objModule = Util::findAndCreateObject($strModule);
				$objModule->getStatus();

				if(1 != $objModule->status){
					$arrMissingModules[] = $strModule;
				}

				if(!empty($arrMissingModules)){
					$this->messages = [];
					$this->messages[] = [
						'class' => 'tl_error'
						,'text' => sprintf(
							'Vous ne pouvez pas gérer ce module tant que les dépendances suivantes ne seront pas résolues : %s'
							,implode(', ', $arrMissingModules)
						)
					];
					$blnCanManage = false;
				}
			}
		}

		// Always add messages	
		$objTemplate->messages = $this->messages;

		// Add actions only if we can manage the module
		if($blnCanManage){
			$objTemplate->fields = $this->fields;
			$objTemplate->actions = $this->actions;
			$objTemplate->logs = $this->logs;
		}

		// And return the template, parsed.
		return $objTemplate->parse();
	}
}