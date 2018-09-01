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
		}
		else if(!$this->sgConfig['sgBlogInstall'] || 0 === \NewsArchiveModel::countById($this->sgConfig['sgBlogNewsArchive'])){
			$this->messages[] = ['class' => 'tl_info', 'text' => 'Le blog est installé, mais pas configuré.'];
			$this->actions[] = ['action'=>'install', 'label'=>'Installer'];
		}
		else{
			$this->messages[] = ['class' => 'tl_confirm', 'text' => 'Le blog est installé et configuré.'];
			$this->actions[] = ['action'=>'reset', 'label'=>'Réinitialiser'];
			$this->actions[] = ['action'=>'remove', 'label'=>'Supprimer'];
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
		$objPage = new \PageModel();
		$objPage->tstamp = time();
		$objPage->pid = $this->sgConfig["sgInstallRootPage"];
		$objPage->sorting = (\PageModel::countBy("pid", $this->sgConfig["sgInstallRootPage"]) + 1) * 128;
		$objPage->title = "Blog";
		$objPage->alias = \StringUtil::generateAlias($objPage->title);
		$objPage->type = "regular";
		$objPage->pageTitle = "Blog";
		$objPage->robots = "index,follow";
		$objPage->sitemap = "map_default";
		$objPage->published = 1;
		$objPage->save();

		// Create the article
		$objArticle = new \ArticleModel();
		$objArticle->tstamp = time();
		$objArticle->pid = $objPage->id;
		$objArticle->sorting = 128;
		$objArticle->title = $objPage->title;
		$objArticle->alias = $objPage->alias;
		$objArticle->author = $this->sgConfig["sgInstallUser"];
		$objArticle->inColumn = "main";
		$objArticle->published = 1;
		$objArticle->save();

		// Create the content
		$objContent = new \ContentModel();
		$objContent->tstamp = time();
		$objContent->pid = $objArticle->id;
		$objContent->ptable = "tl_article";
		$objContent->sorting = 128;
		$objContent->type = "module";
		$objContent->module = $objListModule->id;
		$objContent->save();

		// Create the content
		$objContent = new \ContentModel();
		$objContent->tstamp = time();
		$objContent->pid = $objArticle->id;
		$objContent->ptable = "tl_article";
		$objContent->sorting = 128;
		$objContent->type = "module";
		$objContent->module = $objReaderModule->id;
		$objContent->save();

		// Update the archive jumpTo
		$objArchive->jumpTo = $objPage->id;
		$objArchive->save();
		
		// And save stuff in config
		Util::updateConfig([
			"sgBlogInstall"=>1
			,"sgBlogNewsArchive"=>$objArchive->id
			,"sgBlogModuleList"=>$objListModule->id
			,"sgBlogModuleReader"=>$objReaderModule->id
			,"sgBlogPageList"=>$objPage->id
			,"sgBlogPageReader"=>$objPage->id
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