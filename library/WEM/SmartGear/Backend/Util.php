<?php

/**
 * SMARTGEAR for Contao Open Source CMS
 *
 * Copyright (c) 2015-2018 Web ex Machina
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */

namespace WEM\SmartGear\Backend;

use Exception;

/**
 * Back end module "smartgear".
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Util
{
	/**
	 * Store the path to the config file
	 * @var String
	 */
	protected static $strConfigPath = "system/modules/wem-contao-smartgear/assets/config/smartgear.json";

	/**
	 * Find and Create an Object, depending on type and module
	 * @param  [String] $strType   [Type / Folder]
	 * @param  [String] $strModule [Class / File]
	 * @return [Object]            [Object of the class]
	 */
	public static function findAndCreateObject($strType, $strModule = ''){
		try{
			// If module is missing, try to explode strType
			if('' === $strModule && false != strpos($strType, '_')){
				$arrObject = explode('_', $strType);
				$strType = $arrObject[0];
				$strModule = $arrObject[1];
			}

			// Parse the classname
			$strClass = sprintf("WEM\SmartGear\Backend\%s\%s", ucfirst($strType), ucfirst($strModule));

			// Throw error if class doesn't exists
			if(!class_exists($strClass))
				throw new Exception(sprintf("Unknown class %s", $strClass));

			// Create the object
			$objModule = new $strClass;

			// And return
			return $objModule;
		}
		catch(Exception $e){
			throw $e;
		}
	}

	/**
	 * Get Smartgear Config
	 * @param  [String] $strKey [Config key wanted]
	 * @return [Mixed] 			[Config value]
	 */
	public static function loadSmartgearConfig(){
		try{
			$objFiles = \Files::getInstance();
			$objFile = $objFiles->fopen(static::$strConfigPath, "a");
			$arrConfig = [];

			// Get the config file
			if($strConfig = file_get_contents(static::$strConfigPath))
				$arrConfig = (array)json_decode($strConfig);

			// And return the entire config, updated
			return $arrConfig;
		}
		catch(Exception $e){
			throw $e;
		}
	}
	
	/**
	 * Update Contao Config
	 * @param  [Array] $arrVars [Key/Value Array]
	 */
	public static function updateConfig($arrVars){
		try{
			$objFiles = \Files::getInstance();
			if(!file_exists(static::$strConfigPath)){
				$objFiles->mkdir(str_replace("/smartgear.json", "", static::$strConfigPath));
				$objFiles->fopen(static::$strConfigPath, "wb");
			}
			$strConfig = file_get_contents(static::$strConfigPath);
			$arrConfig = [];

			// Decode the config
			if($strConfig)
				$arrConfig = (array)json_decode($strConfig);
			
			// Update the config
			foreach($arrVars as $strKey => $varValue){
				// Make sure arrays are converted in varValues (for blob compatibility)
				if(is_array($varValue))
					$varValue = serialize($varValue);

				// And update the global array
				$arrConfig[$strKey] = $varValue;
			}

			// Open and update the config file
			$objFile = $objFiles->fopen(static::$strConfigPath, "w");
			$objFiles->fputs($objFile, json_encode($arrConfig, JSON_PRETTY_PRINT));
			
			// And return the entire config, updated
			return $arrConfig;
		}
		catch(Exception $e){
			throw $e;
		}
	}

	/**
	 * Shortcut for page creation
	 */
	public static function createPage($strTitle, $intPid = 0, $arrData = []){
		$arrConfig = static::loadSmartgearConfig();
		if(0 === $intPid)
			$intPid = $arrConfig["sgInstallRootPage"];

		// Create the page
		$objPage = new \PageModel();
		$objPage->tstamp = time();
		$objPage->pid = $intPid;
		$objPage->sorting = (\PageModel::countBy("pid", $intPid) + 1) * 128;
		$objPage->title = $strTitle;
		$objPage->alias = \StringUtil::generateAlias($objPage->title);
		$objPage->type = "regular";
		$objPage->pageTitle = $strTitle;
		$objPage->robots = "index,follow";
		$objPage->sitemap = "map_default";
		$objPage->published = 1;

		// Now we get the default values, get the arrData table
		if(!empty($arrData))
			foreach($arrData as $k=>$v)
				$objPage->$k = $v;
		
		$objPage->save();

		// Return the model
		return $objPage;
	}

	/**
	 * Shortcut for article creation
	 */
	public static function createArticle($objPage, $arrData = []){
		// Create the article
		$objArticle = new \ArticleModel();
		$objArticle->tstamp = time();
		$objArticle->pid = $objPage->id;
		$objArticle->sorting = (\ArticleModel::countBy("pid", $objPage->id) + 1) * 128;
		$objArticle->title = $objPage->title;
		$objArticle->alias = $objPage->alias;
		$objArticle->author = 1;
		$objArticle->inColumn = "main";
		$objArticle->published = 1;

		// Now we get the default values, get the arrData table
		if(!empty($arrData))
			foreach($arrData as $k=>$v)
				$objArticle->$k = $v;

		$objArticle->save();

		// Return the model
		return $objArticle;
	}

	/**
	 * Shortcut for content creation
	 */
	public static function createContent($objArticle, $arrData = []){
		// Create the content
		$objContent = new \ContentModel();
		$objContent->tstamp = time();
		$objContent->pid = $objArticle->id;
		$objContent->ptable = "tl_article";
		$objContent->sorting = (\ContentModel::countPublishedByPidAndTable($objArticle->id, "tl_article") + 1) * 128;
		$objContent->type = "text";

		// Now we get the default values, get the arrData table
		if(!empty($arrData))
			foreach($arrData as $k=>$v)
				$objContent->$k = $v;

		$objContent->save();

		// Return the model
		return $objContent;
	}

	/**
	 * Shortcut for page w/ modules creations
	 */
	public static function createPageWithModules($strTitle, $arrModules, $intPid = 0){
		$arrConfig = static::loadSmartgearConfig();
		if(0 === $intPid)
			$intPid = $arrConfig["sgInstallRootPage"];
		
		// Create the page
		$objPage = static::createPage($strTitle, $intPid);

		// Create the article
		$objArticle = static::createArticle($objPage);

		// Create the contents
		foreach($arrModules as $intModule)
			$objContent = static::createContent($objArticle, ["type"=>"module", "module"=>$intModule]);

		// Return the page ID
		return $objPage->id;
	}

	/**
	 * Shortcut for page w/ texts creations
	 */
	public static function createPageWithText($strTitle, $strText, $intPid = 0, $arrHl = null){
		$arrConfig = static::loadSmartgearConfig();
		if(0 === $intPid)
			$intPid = $arrConfig["sgInstallRootPage"];
		
		// Create the page
		$objPage = static::createPage($strTitle, $intPid);

		// Create the article
		$objArticle = static::createArticle($objPage);

		// Create the content
		$objContent = static::createContent($objArticle, ["text"=>$strText, "headline"=>$arrHl]);

		// Return the page ID
		return $objPage->id;
	}
}