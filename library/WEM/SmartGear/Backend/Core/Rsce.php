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
		
		// Check the install status
		if(1 === $this->sgConfig["sgInstallRsce"]){
			
			$this->status = 1;

			// Compare the source folder and the existing one to check if an update should be done
			if($this->shouldBeUpdated()){
				$this->messages[] = ['class' => 'tl_new', 'text' => 'Il y a une différence entre les deux répertoires. Mettez à jour via le bouton ci-dessous'];
				$this->actions[] = ['action'=>'reset', 'label'=>'Mettre à jour les fichiers RSCE'];
			}
			else{
				$this->messages[] = ['class' => 'tl_confirm', 'text' => 'Les éléments RSCE sont à jour.'];
				$this->actions[] = ['action'=>'reset', 'label'=>'Réinitialiser les fichiers RSCE'];
			}

			$this->actions[] = ['action'=>'remove', 'label'=>'Supprimer les fichiers RSCE'];
		
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
			$objFiles = \Files::getInstance();
			$objFiles->rcopy("system/modules/wem-contao-smartgear/assets/rsce_files", "templates/rsce");
			$this->logs[] = ["status"=>"tl_confirm", "msg"=>"Les templates RSCE ont été importés."];

			// Update config
			Util::updateConfig(["sgInstallRsce"=>1]);

			// And return an explicit status with some instructions
			return [
				"toastr" => [
					"status"=>"success"
					,"msg"=>"L'installation des templates RSCE a été effectuée avec succès."
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
			$objFiles->rrdir("templates/rsce");
			$this->logs[] = ["status"=>"tl_confirm", "msg"=>"Les templates RSCE ont été supprimés."];

			// Update config
			Util::updateConfig(["sgInstallRsce"=>0]);

			// And return an explicit status with some instructions
			return [
				"toastr" => [
					"status"=>"success"
					,"msg"=>"La désinstallation des templates RSCE a été effectuée avec succès."
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

	/**
	 * Calculate the difference between RSCE Source Folder and Smartgear installed one
	 * @return [Boolean] [True means should be updated. Crazy uh ?]
	 */
	protected function shouldBeUpdated(){
		clearstatcache();
		$arrSrcFolder = scandir(TL_ROOT."/system/modules/wem-contao-smartgear/assets/rsce_files");
		$arrFolder = scandir(TL_ROOT."/templates/rsce");

		return !empty(array_diff($arrSrcFolder, $arrFolder)) || !empty(array_diff($arrFolder, $arrSrcFolder));
	}
}