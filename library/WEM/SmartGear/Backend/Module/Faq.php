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
use Contao\Files;
use Contao\PageModel;
use Contao\ModuleModel;
use Contao\FaqCategoryModel;
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
class Faq extends Block implements BlockInterface
{
	/**
	 * Constructor
	 */
	public function __construct(){
		$this->type = "module";
		$this->module = "faq";
		$this->icon = "cogs";
		$this->title = "SmartGear | Module | FAQ";

		parent::__construct();
	}

	/**
	 * Check Module Status
	 * @return [String] [Template of the module check status]
	 */
	public function getStatus(){
		if(!isset($this->bundles['ContaoFaqBundle'])){
			$this->messages[] = ['class' => 'tl_error', 'text' => 'Le module FAQ n\'est pas installé. Veuillez utiliser le <a href="{{env::/}}/contao-manager.phar.php" title="Contao Manager" target="_blank">Contao Manager</a> pour cela.'];
		}
		else if(!Config::get('sgFAQInstall') || 0 === \FaqCategoryModel::countById(Config::get('sgFAQ'))){
			$this->messages[] = ['class' => 'tl_info', 'text' => 'Le module FAQ est installé, mais pas configuré.'];
			$this->actions[] = ['action'=>'install', 'label'=>'Installer'];
		}
		else{
			$this->messages[] = ['class' => 'tl_confirm', 'text' => 'Le module FAQ est installé et configuré.'];
			$this->actions[] = ['action'=>'reset', 'label'=>'Réinitialiser'];
			$this->actions[] = ['action'=>'remove', 'label'=>'Supprimer'];
		}
	}

	/**
	 * Setup the module
	 */
	public function install(){
		// Make sure the template is here before doing anything
		if(!file_exists("templates/smartgear/mod_faqpage.html5")){
			$objFiles = Files::getInstance();
			$objFiles->copy("system/modules/wem-contao-smartgear/assets/templates_files/mod_faqpage.html5", "templates/smartgear/mod_faqpage.html5");
		}

		// Create the archive
		$objFAQ = new FaqCategoryModel();
		$objFAQ->tstamp = time();
		$objFAQ->title = "FAQ";
		$objFAQ->headline = "FAQ";
		$objFAQ->save();

		// Create the reader module
		$objModule = new ModuleModel();
		$objModule->tstamp = time();
		$objModule->pid = Config::get("sgInstallTheme");
		$objModule->name = "FAQ";
		$objModule->type = "faqpage";
		$objModule->faq_categories = serialize([0=>$objFAQ->id]);
		$objModule->customTpl = 'mod_faqpage';
		$objModule->save();

		// Create the page
		$objPage = new PageModel();
		$objPage->tstamp = time();
		$objPage->pid = Config::get("sgInstallRootPage");
		$objPage->sorting = (PageModel::countBy("pid", Config::get("sgInstallRootPage")) + 1) * 128;
		$objPage->title = "FAQ";
		$objPage->alias = \StringUtil::generateAlias($objPage->title);
		$objPage->type = "regular";
		$objPage->pageTitle = "FAQ";
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
		$objContent->module = $objModule->id;
		$objContent->save();
		
		// And save stuff in config
		$this->updateConfig([
			"sgFAQInstall"=>1
			,"sgFAQ"=>$objFAQ->id
			,"sgFAQModule"=>$objModule->id
			,"sgFAQPage"=>$objPage->id
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
		if($objFAQ = FaqCategoryModel::findByPk(Config::get("sgFAQ")))
			$objFAQ->delete();
		if($objModule = ModuleModel::findByPk(Config::get("sgFAQModule")))
			$objModule->delete();
		if($objPage = PageModel::findByPk(Config::get("sgFAQPage")))
			$objPage->delete();

		$this->updateConfig([
			"sgFAQInstall"=>''
			,"sgFAQ"=>''
			,"sgFAQModule"=>''
			,"sgFAQPage"=>''
		]);
	}
}