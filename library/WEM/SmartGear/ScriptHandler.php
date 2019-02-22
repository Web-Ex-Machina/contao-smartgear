<?php

/**
 * SMARTGEAR for Contao Open Source CMS
 *
 * Copyright (c) 2015-2019 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
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
        // Make sure to update the files
        static::rUnlink('templates/rsce');

        // Copy all the files from the assets folder
        static::rCopy('system/modules/wem-contao-smartgear/assets/rsce_files', 'templates/rsce');
    }

    /**
     * Recursively copy a directory
     *
     * @param string $strSource      The source file or folder
     * @param string $strDestination The new file or folder path
     */
    public static function rCopy($strSource, $strDestination)
    {
        if (!file_exists($strDestination)) {
            mkdir($strDestination);
        }

        $arrFiles = scandir($strSource);

        foreach ($arrFiles as $strFile) {
            if ($strFile == '.' || $strFile == '..') {
                continue;
            }
            
            if (is_dir($strSource . '/' . $strFile)) {
                static::rCopy($strSource . '/' . $strFile, $strDestination . '/' . $strFile);
            } else {
                copy($strSource . '/' . $strFile, $strDestination . '/' . $strFile);
            }
        }
    }

    /**
     * Recursively delete a directory
     *
     * @param string $strSource      The source file or folder
     * @param string $strDestination The new file or folder path
     */
    public static function rUnlink($strSource)
    {
        $arrFiles = scandir($strSource);

        foreach ($arrFiles as $strFile) {
            if ($strFile == '.' || $strFile == '..') {
                continue;
            }
            
            if (is_dir($strSource . '/' . $strFile)) {
                static::rUnlink($strSource . '/' . $strFile);
            } else {
                unlink($strSource . '/' . $strFile);
            }
        }
    }
}
