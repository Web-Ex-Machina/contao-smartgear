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
class Blog extends Block implements BlockInterface
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
		$this->module = "blog";
		$this->icon = "cogs";
		$this->title = "SmartGear | Module | Blog";

		parent::__construct();
	}

	/**
	 * Check Module Status
	 * @return [String] [Template of the module check status]
	 */
	public function getStatus(){
		if(!isset($this->bundles['ContaoNewsBundle'])){
			$this->messages[] = ['class' => 'tl_error', 'text' => 'Le blog n\'est pas installé. Veuillez utiliser le <a href="{{env::/}}/contao-manager.phar.php" title="Contao Manager" target="_blank">Contao Manager</a> pour cela.'];

			$this->status = 0;
		}
		else if(!$this->sgConfig['sgBlogInstall'] || 0 === \NewsArchiveModel::countById($this->sgConfig['sgBlogNewsArchive'])){
			$this->messages[] = ['class' => 'tl_info', 'text' => 'Le blog est installé, mais pas configuré.'];
			$this->actions[] = ['action'=>'install', 'label'=>'Installer'];

			$this->status = 0;
		}
		else{
			$this->messages[] = ['class' => 'tl_confirm', 'text' => 'Le blog est installé et configuré.'];
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
		$objArchive = new \NewsArchiveModel();
		$objArchive->tstamp = time();
		$objArchive->title = "Blog";
		$objArchive->save();

		// Create the list module
		$objListModule = new \ModuleModel();
		$objListModule->tstamp = time();
		$objListModule->pid = $this->sgConfig["sgInstallTheme"];
		$objListModule->name = "Blog - List";
		$objListModule->type = "newslist";
		$objListModule->news_archives = serialize([0=>$objArchive->id]);
		$objListModule->numberOfItems = 0;
		$objListModule->news_featured = "all_items";
		$objListModule->perPage = 15;
		$objListModule->news_template = 'news_latest';
		$objListModule->skipFirst = 0;
		$objListModule->news_metaFields = serialize([0=>'date']);
		$objListModule->news_order = 'order_date_desc';
		$objListModule->save();

		// Create the reader module
		$objReaderModule = new \ModuleModel();
		$objReaderModule->tstamp = time();
		$objReaderModule->pid = $this->sgConfig["sgInstallTheme"];
		$objReaderModule->name = "Blog - Reader";
		$objReaderModule->type = "newsreader";
		$objReaderModule->news_archives = serialize([0=>$objArchive->id]);
		$objReaderModule->news_metaFields = serialize([0=>'date']);
		$objReaderModule->news_template = 'news_full';
		$objReaderModule->save();

		// Create the list page
		$intPage = Util::createPageWithModules("Blog", [$objListModule->id, $objReaderModule->id]);

		// Update the archive jumpTo
		$objArchive->jumpTo = $intPage;
		$objArchive->save();
		
		// And save stuff in config
		Util::updateConfig([
			"sgBlogInstall"=>1
			,"sgBlogNewsArchive"=>$objArchive->id
			,"sgBlogModuleList"=>$objListModule->id
			,"sgBlogModuleReader"=>$objReaderModule->id
			,"sgBlogPageList"=>$intPage
			,"sgBlogPageReader"=>$intPage
		]);

		// And return an explicit status with some instructions
		return [
			"toastr" => [
				"status"=>"success"
				,"msg"=>"La configuration du blog a été effectuée avec succès."
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
		if($objArchive = \NewsArchiveModel::findByPk($this->sgConfig["sgBlogNewsArchive"]))
			$objArchive->delete();
		if($objModule = \ModuleModel::findByPk($this->sgConfig["sgBlogModuleList"]))
			$objModule->delete();
		if($objModule = \ModuleModel::findByPk($this->sgConfig["sgBlogModuleReader"]))
			$objModule->delete();
		if($objPage = \PageModel::findByPk($this->sgConfig["sgBlogPageList"]))
			$objPage->delete();
		if($objPage = \PageModel::findByPk($this->sgConfig["sgBlogPageReader"]))
			$objPage->delete();

		Util::updateConfig([
			"sgBlogInstall"=>''
			,"sgBlogNewsArchive"=>''
			,"sgBlogModuleList"=>''
			,"sgBlogModuleReader"=>''
			,"sgBlogPageList"=>''
			,"sgBlogPageReader"=>''
		]);

		// And return an explicit status with some instructions
		return [
			"toastr" => [
				"status"=>"success"
				,"msg"=>"La suppression du blog a été effectuée avec succès."
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