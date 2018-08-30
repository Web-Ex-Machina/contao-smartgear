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
class Blog extends Block implements BlockInterface
{
	/**
	 * Check Module Status
	 * @return [String] [Template of the module check status]
	 */
	public function getStatus(){
		/*try{
			$objTemplate = new FrontendTemplate($strTemplate);
			$objTemplate->title = "SmartGear | Module | Blog";
			$objTemplate->module = "blog";
			$objTemplate->request = \Environment::get('request');
			$objTemplate->token = \RequestToken::get();
			$arrActions = array();

			if(!isset($this->bundles['ContaoNewsBundle'])){
				$objTemplate->msgClass = 'tl_error';
				$objTemplate->msgText = 'Le blog n\'est pas installé. Veuillez utiliser le <a href="{{env::/}}/contao-manager.phar.php" title="Contao Manager" target="_blank">Contao Manager</a> pour cela.';
			} else if(!Config::get('sgBlogInstall') || 0 === \NewsArchiveModel::countById(Config::get('sgBlogNewsArchive'))){
				$objTemplate->msgClass = 'tl_info';
				$objTemplate->msgText = 'Le blog est installé, mais pas configuré.';
				$arrActions[] = ['action'=>'install', 'label'=>'Installer'];
			} else {
				$objTemplate->msgClass = 'tl_confirm';
				$objTemplate->msgText = 'Le blog est installé et configuré.';
				$arrActions[] = ['action'=>'reset', 'label'=>'Réinitialiser'];
				$arrActions[] = ['action'=>'remove', 'label'=>'Supprimer'];
			}

			$objTemplate->actions = $arrActions;
		}
		catch(Exception $e){
			$objTemplate->isError = true;
			$objTemplate->error = $e->getMessage();
			$objTemplate->trace = $e->getTrace();
		}

		return $objTemplate->parse();*/
	}

	/**
	 * Setup the module
	 */
	public function install(){
		
		// Create the archive
		$objArchive = new NewsArchiveModel();
		$objArchive->tstamp = time();
		$objArchive->title = "Blog";
		$objArchive->save();

		// Create the list module
		$objListModule = new ModuleModel();
		$objListModule->tstamp = time();
		$objListModule->pid = Config::get("sgInstallTheme");
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
		$objReaderModule = new ModuleModel();
		$objReaderModule->tstamp = time();
		$objReaderModule->pid = Config::get("sgInstallTheme");
		$objReaderModule->name = "Blog - Reader";
		$objReaderModule->type = "newsreader";
		$objReaderModule->news_archives = serialize([0=>$objArchive->id]);
		$objReaderModule->news_metaFields = serialize([0=>'date']);
		$objReaderModule->news_template = 'news_full';
		$objReaderModule->save();

		// Create the list page
		$objPage = new PageModel();
		$objPage->tstamp = time();
		$objPage->pid = Config::get("sgInstallRootPage");
		$objPage->sorting = (PageModel::countBy("pid", Config::get("sgInstallRootPage")) + 1) * 128;
		$objPage->title = "Blog";
		$objPage->alias = \StringUtil::generateAlias($objPage->title);
		$objPage->type = "regular";
		$objPage->pageTitle = "Blog";
		$objPage->robots = "index,follow";
		$objPage->sitemap = "map_default";
		$objPage->published = 1;
		$objPage->save();

		// Create the article
		$objArticle = new ArticleModel();
		$objArticle->tstamp = time();
		$objArticle->pid = $objPage->id;
		$objArticle->sorting = 128;
		$objArticle->title = $objPage->title;
		$objArticle->alias = $objPage->alias;
		$objArticle->author = Config::get("sgInstallUser");
		$objArticle->inColumn = "main";
		$objArticle->published = 1;
		$objArticle->save();

		// Create the content
		$objContent = new ContentModel();
		$objContent->tstamp = time();
		$objContent->pid = $objArticle->id;
		$objContent->ptable = "tl_article";
		$objContent->sorting = 128;
		$objContent->type = "module";
		$objContent->module = $objListModule->id;
		$objContent->save();

		// Create the content
		$objContent = new ContentModel();
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
		$this->updateConfig([
			"sgBlogInstall"=>1
			,"sgBlogNewsArchive"=>$objArchive->id
			,"sgBlogModuleList"=>$objListModule->id
			,"sgBlogModuleReader"=>$objReaderModule->id
			,"sgBlogPageList"=>$objPage->id
			,"sgBlogPageReader"=>$objPage->id
		]);
	}

	/**
	 * Reset the module
	 */
	public function reset(){
		$this->remove();
		$this->install();
	}

	/**
	 * Remove the module
	 */
	public function remove(){
		if($objArchive = NewsArchiveModel::findByPk(Config::get("sgBlogNewsArchive")))
			$objArchive->delete();
		if($objModule = ModuleModel::findByPk(Config::get("sgBlogModuleList")))
			$objModule->delete();
		if($objModule = ModuleModel::findByPk(Config::get("sgBlogModuleReader")))
			$objModule->delete();
		if($objPage = PageModel::findByPk(Config::get("sgBlogPageList")))
			$objPage->delete();
		if($objPage = PageModel::findByPk(Config::get("sgBlogPageReader")))
			$objPage->delete();

		$this->updateConfig([
			"sgBlogInstall"=>''
			,"sgBlogNewsArchive"=>''
			,"sgBlogModuleList"=>''
			,"sgBlogModuleReader"=>''
			,"sgBlogPageList"=>''
			,"sgBlogPageReader"=>''
		]);
	}
}