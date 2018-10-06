<?php

/**
 * SMARTGEAR for Contao Open Source CMS
 *
 * Copyright (c) 2015-2018 Web ex Machina
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */

namespace WEM\SmartGear\Backend\Module;

use \Exception;

use WEM\SmartGear\Backend\Block;
use WEM\SmartGear\Backend\BlockInterface;
use WEM\SmartGear\Backend\Util;

/**
 * Back end module "smartgear".
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Locations extends Block implements BlockInterface
{
	/**
	 * Module dependancies
	 * @var Array
	 */
	protected $require = ["core_core", "core_rsce"];
	
	/**
	 * Constructor
	 */
	public function __construct(){
		$this->type = "module";
		$this->module = "locations";
		$this->icon = "cogs";
		$this->title = "SmartGear | Module | Locations";

		parent::__construct();
	}

	/**
	 * Check Module Status
	 * @return [String] [Template of the module check status]
	 */
	public function getStatus(){
		if(!isset($this->bundles['wem-contao-locations'])){
			$this->messages[] = ['class' => 'tl_error', 'text' => 'Le module Locations n\'est pas installé. Veuillez utiliser le <a href="{{env::/}}/contao-manager.phar.php" title="Contao Manager" target="_blank">Contao Manager</a> pour cela.'];

			$this->status = 0;
		}
		else if(!$this->sgConfig['sgLocations'] || 0 === \WEM\Location\Model\Map::countById($this->sgConfig['sgLocationsMap'])){
			$this->messages[] = ['class' => 'tl_info', 'text' => 'Le module est installé, mais pas configuré.'];
			$this->actions[] = ['action'=>'install', 'label'=>'Installer'];

			$this->status = 0;
		}
		else{
			$this->messages[] = ['class' => 'tl_confirm', 'text' => 'Le module est installé et configuré.'];
			$href = 'contao?do=wem-maps&act=edit&id='.$this->sgConfig["sgLocationsMap"].'&rt='.\RequestToken::get();
			$this->actions[] = ['v'=>2, 'tag'=>'a', 'text'=>'Configurer', 'attrs'=>['href'=>$href, 'title'=>'Configurer', 'class'=>'openContaoModal']];
			$this->actions[] = ['action'=>'reset', 'label'=>'Réinitialiser'];
			$this->actions[] = ['action'=>'remove', 'label'=>'Supprimer'];

			$this->status = 1;
		}
	}

	/**
	 * Setup the module
	 */
	public function install(){
		// Create the map
		$objMap = new \WEM\Location\Model\Map();
		$objMap->tstamp = time();
		$objMap->title = "Emplacements";
		$objMap->save();

		// Create the module
		$objModule = new \ModuleModel();
		$objModule->tstamp = time();
		$objModule->pid = $this->sgConfig["sgInstallTheme"];
		$objModule->name = "Emplacements";
		$objModule->type = "wem_display_map";
		$objModule->wem_location_map = $objMap->id;
		$objModule->save();

		// Create the list page
		$intPage = Util::createPageWithModules("Emplacements", [$objModule->id]);
		
		// And save stuff in config
		Util::updateConfig([
			"sgLocations"=>1
			,"sgLocationsMap"=>$objMap->id
			,"sgLocationsModule"=>$objModule->id
			,"sgLocationsPage"=>$intPage
		]);

		// And return an explicit status with some instructions
		return [
			"toastr" => [
				"status"=>"success"
				,"msg"=>"La configuration du module a été effectuée avec succès."
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
	 * Remove the module
	 */
	public function remove(){
		if($objModule = \ModuleModel::findByPk($this->sgConfig["sgLocationsModule"]))
			$objModule->delete();
		if($objPage = \PageModel::findByPk($this->sgConfig["sgLocationsPage"]))
			$objPage->delete();
		if($objMap = \WEM\Location\Model\Map::findByPk($this->sgConfig["sgLocationsMap"]))
			$objMap->delete();

		Util::updateConfig([
			"sgLocations"=>''
			,"sgLocationsMap"=>''
			,"sgLocationsModule"=>''
			,"sgLocationsPage"=>''
		]);

		// And return an explicit status with some instructions
		return [
			"toastr" => [
				"status"=>"success"
				,"msg"=>"La suppression du module a été effectuée avec succès."
			]
			,"callbacks" => [
				0 => [
					"method" => "refreshBlock"
					,"args"	 => ["block-".$this->type."-".$this->module]
				]
			]
		];
	}
}