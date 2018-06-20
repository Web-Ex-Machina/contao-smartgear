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
}