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
	}

	/**
	 * Parse and return the block as HTML
	 * @return [String] [Block HTML]
	 */
	public function parse(){
		$objTemplate = new FrontendTemplate($this->strTemplate);
		$objTemplate->request = Environment::get('request');
		$objTemplate->token = RequestToken::get();
		$objTemplate->type = $this->type;
		$objTemplate->module = $this->module;
		$objTemplate->title = $this->title;
		$objTemplate->icon = $this->icon;
		$objTemplate->messages = $this->messages;
		$objTemplate->fields = $this->fields;
		$objTemplate->actions = $this->actions;
		$objTemplate->logs = $this->logs;

		return $objTemplate->parse();
	}
}