<?php

/**
 * SMARTGEAR for Contao Open Source CMS
 *
 * Copyright (c) 2015-2018 Web ex Machina
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */

namespace WEM\SmartGear;

use Contao\Database;
use Contao\Folder;
use Contao\Files;
use Contao\File;

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
    	$objFiles = Files::getInstance();

        // Copy all the files from the assets folder
    	$objFiles->rcopy('system/modules/wem-contao-smartgear/assets/rsce_files', 'templates/rsce');
    }
}