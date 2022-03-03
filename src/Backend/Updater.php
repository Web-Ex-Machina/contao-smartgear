<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2020 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

namespace WEM\SmartgearBundle\Backend;

use Exception;

/**
 * Back end module "smartgear".
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Updater
{
    /**
     * Updates to apply.
     *
     * @var [Array]
     */
    public $updates = [];

    /**
     * Constructor.
     *
     * @param bool $checkForUpdates [Directly call the update checker]
     */
    public function __construct($checkForUpdates = true)
    {
        $this->sgVersion = $this->getPackageVersion();
        $this->conf = Util::loadSmartgearConfig();

        if ($checkForUpdates) {
            $this->shouldBeUpdated();
        }
    }

    /**
     * Specific getter.
     */
    public function __get($strKey)
    {
        return $this->$strKey;
    }

    /**
     * Return package Smartgear version.
     *
     * @return [Float] Smartgear version
     */
    public function getPackageVersion()
    {
        $packages = json_decode(file_get_contents(TL_ROOT.'/vendor/composer/installed.json'));

        foreach ($packages->packages as $p) {
            $p = (array) $p;
            if ('webexmachina/contao-smartgear' === $p['name']) {
                $this->sgVersion = $p['version'];
            }
        }

        return $this->sgVersion;
    }

    /**
     * Return current Smartgear version.
     *
     * @return [Float] Smartgear version
     */
    public function getCurrentVersion()
    {
        return $this->conf['sgVersion'] ?: null;
    }

    /**
     * Play the functions wanted in params.
     *
     * @param string $update Update to call
     *
     * @return [Boolean] True/False depending of the update status
     */
    public function runUpdate($update)
    {
        try {
            // Check if the update called exists
            if (!method_exists($this, $update)) {
                throw new \Exception(sprintf('Update %s introuvable !', $update));
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
     * Generate the update link.
     *
     * @param string $update [<description>]
     *
     * @return string - Return the link of the update wanted
     */
    public function getUpdateLink($update)
    {
        try {
            return sprintf('contao?do=smartgear&sgUpdate=%s', $update);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Compare current Smartgear install version with what it should be.
     */
    public function shouldBeUpdated()
    {
        try {
            // Clear the current updates array to avoid doublons
            $this->updates = [];

            // If no version setup, just call the first
            if (!$this->getCurrentVersion()) {
                $this->updates[] = 'to050';
                return !empty($this->updates);
            }

            // We need to compare current version with the package one
            $arrPackageVersion = explode('.', $this->getPackageVersion());
            $arrCurrentVersion = explode('.', $this->getCurrentVersion());

            // Check if the system needs an update
            if ($arrCurrentVersion[2] >= $arrPackageVersion[2]
                && $arrCurrentVersion[1] >= $arrPackageVersion[1]
                && $arrCurrentVersion[0] >= $arrPackageVersion[0]
            ) {
                return !empty($this->updates);
            }

            // Now we will find out what is the function to call
            // The point here is not to found the latest update
            // we need to find the closest update to the current version
            // so at each update, the system will detect the next one
            $x = $arrCurrentVersion[0];
            $y = $arrCurrentVersion[1];

            // 1st we want to apply all the fix updates of the current major/minor version
            // We assume that 50 is a good enough limit to the fix version
            // or we must stop doing whatever we are doing.
            $maxZ = ($x === $arrPackageVersion[0] && $y === $arrPackageVersion[1]) ? $arrPackageVersion[2] : 50;
            for ($z = $arrCurrentVersion[2] + 1; $z <= $maxZ; ++$z) {
                // Format the function name
                $strFunction = sprintf('to%s%s%s', $x, $y, $z);

                // If it exists, add it to the list of updates to play
                if (method_exists($this, $strFunction)) {
                    $this->updates[] = $strFunction;
                }
            }

            // Now we have done this, we must redo this, but for minor updates
            // If we are already on the same major
            // maxY will be the current package minor
            // Else, limit that count to 20
            $maxY = ($x === $arrPackageVersion[0]) ? $arrPackageVersion[1] : 20;
            for ($y = $arrCurrentVersion[1] + 1; $y <= $maxY; ++$y) {
                // And check again for fix updates
                // Here too, check if the current X & Y are equal to the package X & Y
                $maxZ = ($x === $arrPackageVersion[0] && $y === $arrPackageVersion[1]) ? $arrPackageVersion[2] : 50;
                for ($z = 0; $z <= $maxZ; ++$z) {
                    // Format the function name
                    $strFunction = sprintf('to%s%s%s', $x, $y, $z);

                    // If it exists, add it to the list of updates to play
                    if (method_exists($this, $strFunction)) {
                        $this->updates[] = $strFunction;
                    }
                }
            }

            // And finally, do this for major versions
            // This time, we don't have to go the 50, because we know the last major version for sure
            for ($x = $arrCurrentVersion[0] + 1; $x <= $arrPackageVersion[0]; ++$x) {
                // Same for minor
                for ($y = 0; $y <= $arrPackageVersion[1]; ++$y) {
                    // Same for fix
                    for ($z = 0; $z <= $arrPackageVersion[2]; ++$z) {
                        // Format the function name
                        $strFunction = sprintf('to%s%s%s', $x, $y, $z);

                        // If it exists, add it to the list of updates to play
                        if (method_exists($this, $strFunction)) {
                            $this->updates[] = $strFunction;
                        }
                    }
                }
            }

            // At the end, always add the generic version update
            if ($this->sgVersion !== $this->getCurrentVersion()) {
                $this->updates[] = 'updateCurrentVersionToPackageVersion';
            }

            return !empty($this->updates);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Standardize config to Smartgear 0.5
     * Just add the version to the current config.
     */
    public function to050(): void
    {
        try {
            Util::updateConfig(['sgVersion' => '0.5.0']);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Update to Smartgear 0.6.
     */
    public function to060(): void
    {
        try {
            // Update version
            Util::updateConfig(['sgVersion' => '0.6.0']);

            // Update timers in Contao config
            \Config::persist('undoPeriod', 7776000);
            \Config::persist('versionPeriod', 7776000);
            \Config::persist('logPeriod', 7776000);

            // Create the Smartgear theme template folder and update the ThemeModel
            $objFiles = \Files::getInstance();
            $objFiles->mkdir(sprintf('templates/%s', \StringUtil::generateAlias($this->conf['websiteTitle'])));
            $objTheme = \ThemeModel::findByPk($this->conf['sgInstallTheme']);
            $objTheme->templates = sprintf('templates/%s', \StringUtil::generateAlias($this->conf['websiteTitle']));
            $objTheme->save();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Generic function who will just update the current version to package version
     * Useful when there is no updates to play but we still need to update the config version.
     */
    public function updateCurrentVersionToPackageVersion(): void
    {
        try {
            Util::updateConfig(['sgVersion' => $this->getPackageVersion()]);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
