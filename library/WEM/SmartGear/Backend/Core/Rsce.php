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
class Rsce extends Block implements BlockInterface
{
	/**
	 * Constructor
	 */
	public function __construct(){
		$this->type = "core";
		$this->module = "rsce";
		$this->icon = "exclamation-triangle";
		$this->title = "Smartgear | Core | RSCE";
		parent::__construct();
	}

	/**
	 * Check Smartgear Status
	 * @return [String] [Template of the module check status]
	 */
	public function getStatus(){
		$this->messages[] = ['class' => 'tl_info', 'text' => 'Cette section permet de réinitialiser, de rafraichir ou de supprimer les éléments personnalisés RSCE utilisés par Smartgear.'];
		$this->actions[] = ['action'=>'reimport', 'label'=>'Réinitialiser Smartgear'];
	}

	/**
	 * Install Smartgear
	 */
	public function install(){
		try{
			$objFiles = Files::getInstance();
			$objFiles->rcopy($this->strBasePath."/assets/templates_files", "templates/smartgear");
			$objFiles->rcopy($this->strBasePath."/assets/rsce_files", "templates/rsce");
			$this->logs[] = ["status"=>"tl_confirm", "msg"=>"Les templates Smartgear ont été importés (templates et rsce)"];
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
			$objFiles = Files::getInstance();
			$objFiles->rrdir("templates/smartgear");
			$objFiles->rrdir("templates/rsce");
			$this->logs[] = ["status"=>"tl_confirm", "msg"=>"Les templates Smartgear ont été supprimés (templates et rsce)"];
		}
		catch(Exception $e){
			throw $e;
		}
	}
}