<?php

/**
 * SMARTGEAR for Contao Open Source CMS
 *
 * Copyright (c) 2015-2018 Web ex Machina
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */

namespace WEM\SmartGear\Backend\Addon;

use \Exception;

use WEM\SmartGear\Backend\Block;
use WEM\SmartGear\Backend\BlockInterface;
use WEM\SmartGear\Backend\Util;

/**
 * Back end module "smartgear".
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Seo extends Block implements BlockInterface
{
	/**
	 * Module dependancies
	 * @var Array
	 */
	protected $require = ["core_core"];
	
	/**
	 * Constructor
	 */
	public function __construct(){
		$this->type = "addon";
		$this->module = "seo";
		$this->icon = "puzzle-piece";
		$this->title = "SmartGear | Extension | SEO";

		parent::__construct();
	}

	/**
	 * Check Module Status
	 * @return [String] [Template of the module check status]
	 */
	public function getStatus(){
		if(!isset($this->bundles['wem-contao-seo'])){
			$this->messages[] = ['class' => 'tl_error', 'text' => 'L\'extension n\'est pas installé. Veuillez utiliser le <a href="{{env::/}}/contao-manager.phar.php" title="Contao Manager" target="_blank">Contao Manager</a> pour cela.'];

			$this->status = 0;
		}
		else{
			$this->messages[] = ['class' => 'tl_confirm', 'text' => 'L\'extension est installé et configuré.'];
			$this->status = 1;
		}
	}

	/**
	 * Setup the module
	 */
	public function install(){
		
	}

	/**
	 * Remove the module
	 */
	public function remove(){
		
	}
}