<?php

/**
 * SMARTGEAR for Contao Open Source CMS
 *
 * Copyright (c) 2015-2018 Web ex Machina
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */

namespace WEM\SmartGear\Backend\Module;

use Exception;
use Contao\Config;
use Contao\PageModel;
use Contao\ArticleModel;
use Contao\ContentModel;

/**
 * Back end module "smartgear".
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
abstract class Module
{
	/**
	 * Update Contao Config
	 * @param  [Array] $arrVars [Key/Value Array]
	 */
	public function updateConfig($arrVars){
		foreach($arrVars as $strKey => $varValue)
			Config::persist($strKey, $varValue);
	}

	/**
	 * Shortcut for page w/ modules creations
	 */
	public function createPageWithModule($strTitle, $intModule, $intPid = 0){
		if(0 === $intPid)
			$intPid = Config::get("sgInstallRootPage");
		
		// Create the page
		$objPage = new PageModel();
		$objPage->tstamp = time();
		$objPage->pid = $intPid;
		$objPage->sorting = (PageModel::countBy("pid", $intPid) + 1) * 128;
		$objPage->title = $strTitle;
		$objPage->alias = \StringUtil::generateAlias($objPage->title);
		$objPage->type = "regular";
		$objPage->pageTitle = $strTitle;
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
		$objContent->module = $intModule;
		$objContent->save();

		// Return the page ID
		return $objPage->id;
	}

	/**
	 * Shortcut for page w/ texts creations
	 */
	public function createPageWithText($strTitle, $strText, $intPid = 0, $arrHl = null){
		if(0 === $intPid)
			$intPid = Config::get("sgInstallRootPage");
		
		// Create the page
		$objPage = new PageModel();
		$objPage->tstamp = time();
		$objPage->pid = $intPid;
		$objPage->sorting = (PageModel::countBy("pid", $intPid) + 1) * 128;
		$objPage->title = $strTitle;
		$objPage->alias = \StringUtil::generateAlias($objPage->title);
		$objPage->type = "regular";
		$objPage->pageTitle = $strTitle;
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
		$objContent->type = "text";
		$objContent->text = $strText;

		if($arrHl)
			$objContent->headline = serialize($arrHl);

		$objContent->save();

		// Return the page ID
		return $objPage->id;
	}
}