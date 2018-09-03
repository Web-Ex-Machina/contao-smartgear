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
class Calendar extends Block implements BlockInterface
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
		$this->type = "module";
		$this->module = "calendar";
		$this->icon = "cogs";
		$this->title = "SmartGear | Module | Événements";

		parent::__construct();
	}

	/**
	 * Check Module Status
	 * @return [String] [Template of the module check status]
	 */
	public function getStatus(){
		if(!isset($this->bundles['ContaoCalendarBundle'])){
			$this->messages[] = ['class' => 'tl_error', 'text' => 'Le module Événements n\'est pas installé. Veuillez utiliser le <a href="{{env::/}}/contao-manager.phar.php" title="Contao Manager" target="_blank">Contao Manager</a> pour cela.'];
			$this->status = 0;
		}
		else if(!$this->sgConfig['sgCalendarInstall'] || 0 === \CalendarModel::countById($this->sgConfig['sgCalendar'])){
			$this->messages[] = ['class' => 'tl_info', 'text' => 'Le module Événements est installé, mais pas configuré.'];
			$this->actions[] = ['action'=>'install', 'label'=>'Installer'];
			$this->status = 0;
		}
		else{
			$this->messages[] = ['class' => 'tl_confirm', 'text' => 'Le module Événements est installé et configuré.'];
			$this->actions[] = ['action'=>'reset', 'label'=>'Réinitialiser'];
			$this->actions[] = ['action'=>'remove', 'label'=>'Supprimer'];
			$this->status = 1;
		}
	}

	/**
	 * Setup the module
	 */
	public function install(){
		
		// Create the archive
		$objCalendar = new \CalendarModel();
		$objCalendar->tstamp = time();
		$objCalendar->title = "Événements";
		$objCalendar->save();

		// Create the reader module
		$objReaderModule = new \ModuleModel();
		$objReaderModule->tstamp = time();
		$objReaderModule->pid = $this->sgConfig["sgInstallTheme"];
		$objReaderModule->name = "Événements - Reader";
		$objReaderModule->type = "eventreader";
		$objReaderModule->cal_calendar = serialize([0=>$objCalendar->id]);
		$objReaderModule->cal_template = 'event_full';
		$objReaderModule->imgSize = serialize([0=>1000,1=>"",2=>"proportional"]);
		$objReaderModule->save();

		// Create the list modules
		$objListModule = new \ModuleModel();
		$objListModule->tstamp = time();
		$objListModule->pid = $this->sgConfig["sgInstallTheme"];
		$objListModule->name = "Événements - List - Upcoming";
		$objListModule->type = "eventlist";
		$objListModule->cal_calendar = serialize([0=>$objCalendar->id]);
		$objListModule->cal_format = "next_cur_month";
		$objListModule->cal_noSpan = "";
		$objListModule->cal_order = "ascending";
		$objListModule->cal_readerModule = $objReaderModule->id;
		$objListModule->cal_limit = 0;
		$objListModule->cal_ignoreDynamic = "";
		$objListModule->cal_hideRunning = "";
		$objListModule->cal_template = 'event_list';
		$objListModule->perPage = 15;
		$objListModule->imgSize = serialize([0=>1000,1=>"",2=>"proportional"]);
		$objListModule->save();

		$objListModulePassed = new \ModuleModel();
		$objListModulePassed->tstamp = time();
		$objListModulePassed->pid = $this->sgConfig["sgInstallTheme"];
		$objListModulePassed->name = "Événements - List - Passed";
		$objListModulePassed->type = "eventlist";
		$objListModulePassed->cal_calendar = serialize([0=>$objCalendar->id]);
		$objListModulePassed->cal_format = "past_all";
		$objListModulePassed->cal_noSpan = "";
		$objListModulePassed->cal_order = "descending";
		$objListModulePassed->cal_readerModule = $objReaderModule->id;
		$objListModulePassed->cal_limit = 0;
		$objListModulePassed->cal_ignoreDynamic = "";
		$objListModulePassed->cal_hideRunning = "";
		$objListModulePassed->cal_template = 'event_list';
		$objListModulePassed->perPage = 15;
		$objListModulePassed->imgSize = serialize([0=>1000,1=>"",2=>"proportional"]);
		$objListModulePassed->save();

		// Create the page with the modules
		$intPage = Util::createPageWithModules("Événements", [$objListModule->id, $objListModulePassed->id, $objReaderModule->id]);

		// Update the archive jumpTo
		$objCalendar->jumpTo = $intPage;
		$objCalendar->save();
		
		// And save stuff in config
		Util::updateConfig([
			"sgCalendarInstall"=>1
			,"sgCalendar"=>$objCalendar->id
			,"sgCalendarModuleList"=>$objListModule->id
			,"sgCalendarModuleListPassed"=>$objListModulePassed->id
			,"sgCalendarModuleReader"=>$objReaderModule->id
			,"sgCalendarPageList"=>$objPage->id
			,"sgCalendarPageReader"=>$objPage->id
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
		if($objArchive = \CalendarModel::findByPk($this->sgConfig["sgCalendar"]))
			$objArchive->delete();
		if($objModule = \ModuleModel::findByPk($this->sgConfig["sgCalendarModuleList"]))
			$objModule->delete();
		if($objModule = \ModuleModel::findByPk($this->sgConfig["sgCalendarModuleListPassed"]))
			$objModule->delete();
		if($objModule = \ModuleModel::findByPk($this->sgConfig["sgCalendarModuleReader"]))
			$objModule->delete();
		if($objPage = \PageModel::findByPk($this->sgConfig["sgCalendarPageList"]))
			$objPage->delete();
		if($objPage = \PageModel::findByPk($this->sgConfig["sgCalendarPageReader"]))
			$objPage->delete();

		Util::updateConfig([
			"sgCalendarInstall"=>''
			,"sgCalendar"=>''
			,"sgCalendarModuleList"=>''
			,"sgCalendarModuleListPassed"=>''
			,"sgCalendarModuleReader"=>''
			,"sgCalendarPageList"=>''
			,"sgCalendarPageReader"=>''
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