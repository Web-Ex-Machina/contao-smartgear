<?php

/**
 * SMARTGEAR for Contao Open Source CMS
 *
 * Copyright (c) 2015-2018 Web ex Machina
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */

namespace WEM\SmartGear;

use Composer\Script\Event;

/**
 * Handle SmartGear Install/Update shortcuts 
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class ScriptHandler
{
	/**
     * Move RSCE files into a folder the plugin can read
     *
     * @param Event $event
     */
    public static function initialize(Event $event)
    {
        // Copy all the files from the assets folder
    	static::rcopy('system/modules/wem-contao-smartgear/assets/rsce_files', 'templates/rsce');
    }

    /**
	 * Recursively copy a directory
	 *
	 * @param string $strSource      The source file or folder
	 * @param string $strDestination The new file or folder path
	 */
	public static function rcopy($strSource, $strDestination)
	{
		//$strSource = getcwd().'/'.$strSource;
		//$strDestination = getcwd().'/'.$strDestination;

		if(!file_exists($strDestination))
			mkdir($strDestination);

		$arrFiles = scandir($strSource);

		foreach ($arrFiles as $strFile)
		{
			if($strFile == '.' || $strFile == '..')
				continue;
			
			if (is_dir($strSource . '/' . $strFile))
				static::rcopy($strSource . '/' . $strFile, $strDestination . '/' . $strFile);
			else
				copy($strSource . '/' . $strFile, $strDestination . '/' . $strFile);
		}
	}
}