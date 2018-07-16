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
use Contao\CalendarModel;
use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\FrontendTemplate;

/**
 * Back end module "smartgear".
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Calendar extends Module implements ModuleInterface
{
	/**
	 * Check Module Status
	 * @return [String] [Template of the module check status]
	 */
	public function checkStatus($strTemplate = 'be_wem_sg_module'){
		try{
			$objTemplate = new FrontendTemplate($strTemplate);
			$objTemplate->title = "SmartGear | Module | Agenda";
			$objTemplate->module = "calendar";
			$objTemplate->request = \Environment::get('request');
			$objTemplate->token = \RequestToken::get();
			$arrActions = array();
			$bundles = \System::getContainer()->getParameter('kernel.bundles');

			if(!isset($bundles['ContaoCalendarBundle'])){
				$objTemplate->msgClass = 'tl_error';
				$objTemplate->msgText = 'L\'agenda n\'est pas installé. Veuillez utiliser le <a href="{{env::/}}/contao-manager.phar.php" title="Contao Manager" target="_blank">Contao Manager</a> pour cela.';
			} else if(!Config::get('sgCalendarInstall') || 0 === \CalendarModel::countById(Config::get('sgCalendar'))){
				$objTemplate->msgClass = 'tl_info';
				$objTemplate->msgText = 'L\'agenda est installé, mais pas configuré.';
				$arrActions[] = ['action'=>'install', 'label'=>'Installer'];
			} else {
				$objTemplate->msgClass = 'tl_confirm';
				$objTemplate->msgText = 'L\'agenda est installé et configuré.';
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

		return $objTemplate->parse();
	}

	/**
	 * Setup the module
	 */
	public function install(){
		
		// Create the archive
		$objCalendar = new CalendarModel();
		$objCalendar->tstamp = time();
		$objCalendar->title = "Agenda";
		$objCalendar->save();

		// Create the reader module
		$objReaderModule = new ModuleModel();
		$objReaderModule->tstamp = time();
		$objReaderModule->pid = Config::get("sgInstallTheme");
		$objReaderModule->name = "Agenda - Reader";
		$objReaderModule->type = "eventreader";
		$objReaderModule->cal_calendar = serialize([0=>$objCalendar->id]);
		$objReaderModule->cal_template = 'event_full';
		$objReaderModule->imgSize = serialize([0=>1000,1=>"",2=>"proportional"]);
		$objReaderModule->save();

		// Create the list modules
		$objListModule = new ModuleModel();
		$objListModule->tstamp = time();
		$objListModule->pid = Config::get("sgInstallTheme");
		$objListModule->name = "Agenda - List - Upcoming";
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

		$objListModulePassed = new ModuleModel();
		$objListModulePassed->tstamp = time();
		$objListModulePassed->pid = Config::get("sgInstallTheme");
		$objListModulePassed->name = "Agenda - List - Passed";
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

		// Create the list page
		$objPage = new PageModel();
		$objPage->tstamp = time();
		$objPage->pid = Config::get("sgInstallRootPage");
		$objPage->sorting = (PageModel::countBy("pid", Config::get("sgInstallRootPage")) + 1) * 128;
		$objPage->title = "Agenda";
		$objPage->alias = \StringUtil::generateAlias($objPage->title);
		$objPage->type = "regular";
		$objPage->pageTitle = "Agenda";
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

		$objContent = new ContentModel();
		$objContent->tstamp = time();
		$objContent->pid = $objArticle->id;
		$objContent->ptable = "tl_article";
		$objContent->sorting = 256;
		$objContent->type = "module";
		$objContent->module = $objListModulePassed->id;
		$objContent->save();

		// Create the content
		$objContent = new ContentModel();
		$objContent->tstamp = time();
		$objContent->pid = $objArticle->id;
		$objContent->ptable = "tl_article";
		$objContent->sorting = 384;
		$objContent->type = "module";
		$objContent->module = $objReaderModule->id;
		$objContent->save();

		// Update the archive jumpTo
		$objCalendar->jumpTo = $objPage->id;
		$objCalendar->save();
		
		// And save stuff in config
		$this->updateConfig([
			"sgCalendarInstall"=>1
			,"sgCalendar"=>$objCalendar->id
			,"sgCalendarModuleList"=>$objListModule->id
			,"sgCalendarModuleListPassed"=>$objListModulePassed->id
			,"sgCalendarModuleReader"=>$objReaderModule->id
			,"sgCalendarPageList"=>$objPage->id
			,"sgCalendarPageReader"=>$objPage->id
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
		if($objArchive = CalendarModel::findByPk(Config::get("sgCalendar")))
			$objArchive->delete();
		if($objModule = ModuleModel::findByPk(Config::get("sgCalendarModuleList")))
			$objModule->delete();
		if($objModule = ModuleModel::findByPk(Config::get("sgCalendarModuleListPassed")))
			$objModule->delete();
		if($objModule = ModuleModel::findByPk(Config::get("sgCalendarModuleReader")))
			$objModule->delete();
		if($objPage = PageModel::findByPk(Config::get("sgCalendarPageList")))
			$objPage->delete();
		if($objPage = PageModel::findByPk(Config::get("sgCalendarPageReader")))
			$objPage->delete();

		$this->updateConfig([
			"sgCalendarInstall"=>''
			,"sgCalendar"=>''
			,"sgCalendarModuleList"=>''
			,"sgCalendarModuleListPassed"=>''
			,"sgCalendarModuleReader"=>''
			,"sgCalendarPageList"=>''
			,"sgCalendarPageReader"=>''
		]);
	}
}