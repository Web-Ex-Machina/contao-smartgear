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

namespace WEM\SmartGear\Backend;

use Exception;

/**
 * Back end module "smartgear".
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Updater
{
    /**
     * Constructor
     *
     * @param Boolean $shouldBeUpdated [Directly call the update checker]
     */
    public function __construct($shouldBeUpdated = true)
    {
        $this->sgVersion = "0.5.0";
        $this->conf = Util::loadSmartgearConfig();
        
        if ($shouldBeUpdated) {
            $this->shouldBeUpdated();
        }
    }

    /**
     * Return package Smartgear version
     *
     * @return [Float] Smartgear version
     */
    public function getPackageVersion()
    {
        return $this->sgVersion;
    }

    /**
     * Return current Smartgear version
     *
     * @return [Float] Smartgear version
     */
    public function getCurrentVersion()
    {
        return $this->conf['sgVersion'];
    }

    /**
     * Play the functions wanted in params
     *
     * @param String $update Update to call
     *
     * @return [Boolean] True/False depending of the update status
     */
    public function runUpdate($update)
    {
        try {
            // Check if the update called exists
            if (!method_exists($this, $update)) {
                throw new \Exception(sprintf("Update %s introuvable !", $update));
            }

            // Run the update
            $this->{$update}();

            // Either we are here and everything was great
            // either an exception was sent and it's been catched and thrown
            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Generate the update link
     *
     * @param String $update [<description>]
     *
     * @return String - Return the link of the update wanted
     */
    public function getUpdateLink($update)
    {
        try {
            return sprintf("contao?do=smartgear&sgUpdate=%s", $update);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Compare current Smartgear install version with what it should be
     *
     * @return void - Return either the function to call either false
     */
    public function shouldBeUpdated()
    {
        try {
            // If no version setup, just call the first
            if (!$this->getCurrentVersion()) {
                $this->update = "to050";
            }

            // We need to compare current version with the package one
            $arrPackageVersion = explode(".", $this->getPackageVersion());
            $arrCurrentVersion = explode(".", $this->getCurrentVersion());

            // Check if the system needs an update
            if ($arrCurrentVersion[2] >= $arrPackageVersion[2]
                || $arrCurrentVersion[1] >= $arrPackageVersion[1]
                || $arrCurrentVersion[0] >= $arrPackageVersion[0]
            ) {
                $this->update = false;
            }

            // Now we will find out what is the function to call
            // The point here is not to found the latest update
            // we need to find the closest update to the current version
            // so at each update, the system will detect the next one
            $strFunction = 'to';
            // @todo : well, find a way to do this ? :D
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Standardize config to Smartgear 0.5
     * Just add the version to the current config
     *
     * @return void
     */
    public function to050()
    {
        try {
            Util::updateConfig(["sgVersion"=>"0.5.0"]);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
