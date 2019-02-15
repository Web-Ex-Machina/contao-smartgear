<?php

/**
 * SMARTGEAR for Contao Open Source CMS
 *
 * Copyright (c) 2015-2019 Web ex Machina
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
class Updater
{
	protected static $strVersion = "0.2.0";

	/**
	 * Compare current Smartgear install version with what it should be
	 * @return [type] [description]
	 */
	public static function shouldBeUpdated(){
		try{
			$conf = Util::loadSmartgearConfig();

			if(!$conf['version'])
				return true;

			// We need to compare current version with the package one
			$arrPackageVersion = explode(".", static::$strVersion);
			$arrCurrentVersion = explode(".", $conf['version']);

			// Now we will find out what are the process to call
			if($arrCurrentVersion[0] < $arrPackageVersion[0])
				return true;
			else if($arrCurrentVersion[1] < $arrPackageVersion[1])
				return true;
			else if($arrCurrentVersion[2] < $arrPackageVersion[2])
				return true;
			else
				return false;
		}
		catch(Exception $e){
			throw $e;
		}	
	}
}