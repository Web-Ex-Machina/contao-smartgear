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
	 * Module dependancies
	 * @var Array
	 */
	protected $require = ["core_core"];

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
		$this->messages[] = ['class' => 'tl_info', 'text' => 'Cette section permet d\'importer les éléments personnalisés RSCE utilisés par Smartgear.'];
		
		if(1 === $this->sgConfig["sgInstallRsce"]){
			$this->actions[] = ['action'=>'reset', 'label'=>'Réinitialiser les fichiers RSCE'];
			$this->actions[] = ['action'=>'remove', 'label'=>'Supprimer les fichiers RSCE'];
			$this->status = 1;
		}
		else{
			$this->actions[] = ['action'=>'install', 'label'=>'Importer les fichiers RSCE'];
			$this->status = 0;
		}
	}

	/**
	 * Install Smartgear
	 */
	public function install(){
		try{
			$objFolder = new \Folder("templates/smartgear");
			$objFolder = new \Folder("templates/rsce");

			$objFiles = \Files::getInstance();
			$objFiles->rcopy("system/modules/wem-contao-smartgear/assets/templates_files", "templates/smartgear");
			$objFiles->rcopy("system/modules/wem-contao-smartgear/assets/rsce_files", "templates/rsce");
			$this->logs[] = ["status"=>"tl_confirm", "msg"=>"Les templates Smartgear ont été importés (templates et rsce)"];

			// Update config
			Util::updateConfig(["sgInstallRsce"=>1]);

			// And return an explicit status with some instructions
			return [
				"toastr" => [
					"status"=>"success"
					,"msg"=>"L'installation des templates Smartgear a été effectuée avec succès."
				]
				,"callbacks" => [
					0 => [
						"method" => "refreshBlock"
						,"args"	 => ["block-".$this->type."-".$this->module]
					]
				]
			];
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
			$objFiles = \Files::getInstance();
			$objFiles->rrdir("templates/smartgear");
			$objFiles->rrdir("templates/rsce");
			$this->logs[] = ["status"=>"tl_confirm", "msg"=>"Les templates Smartgear ont été supprimés (templates et rsce)"];

			// Update config
			Util::updateConfig(["sgInstallRsce"=>0]);

			// And return an explicit status with some instructions
			return [
				"toastr" => [
					"status"=>"success"
					,"msg"=>"La désinstallation des templates Smartgear a été effectuée avec succès."
				]
				,"callbacks" => [
					0 => [
						"method" => "refreshBlock"
						,"args"	 => ["block-".$this->type."-".$this->module]
					]
				]
			];
		}
		catch(Exception $e){
			throw $e;
		}
	}
}