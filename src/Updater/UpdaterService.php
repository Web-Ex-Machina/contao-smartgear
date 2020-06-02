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

namespace WEM\SmartgearBundle\Updater;

use Ausi\SlugGenerator\SlugGeneratorInterface;
use Contao\CoreBundle\Framework\ContaoFramework;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RequestStack;
use WEM\SmartgearBundle\Backend\Util;

class UpdaterService
{
    /**
     * @var array
     */
    public $logs;

    /**
     * @var ContaoFramework
     */
    private $framework;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var SlugGeneratorInterface
     */
    private $slugGenerator;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @internal Do not inherit from this class; decorate the "smartgear.backupmanager" service instead
     */
    public function __construct(ContaoFramework $framework, Filesystem $filesystem, SlugGeneratorInterface $slugGenerator, RequestStack $requestStack)
    {
        $this->framework = $framework;
        $this->filesystem = $filesystem;
        $this->slugGenerator = $slugGenerator;
        $this->requestStack = $requestStack;

        $this->sgVersion = Util::getPackageVersion('webexmachina/contao-smartgear');
        $this->conf = Util::loadSmartgearConfig();

        $this->logs = [];
    }

    /**
     * Play the functions wanted in params.
     *
     * @param string $update         | Update to call
     * @param bool   $doBackupBefore | Do a backup before update
     *
     * @return [Boolean] True/False depending of the update status
     */
    public function runUpdate($update, $doBackupBefore = true)
    {
        try {
            // Check if the update called exists
            if (!method_exists($this, $update)) {
                throw new \Exception(sprintf('Update %s introuvable !', $update));
            }

            if ($doBackupBefore) {
                $objService = \System::getContainer()->get('smartgear.backend.backupservice');

                // Retrieve and list all the files to save
                $strDir = TL_ROOT.'/web/bundles/wemsmartgear/contao_files';
                $files = Util::getFileList($strDir);

                foreach ($files as &$f) {
                    $f = str_replace($strDir.'/', '', $f);
                }

                $objService->save($files, ['name' => 'sgbackup_'.$update.'_']);
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
     * Compare current Smartgear install version with what it should be.
     * Also checks if there is updates to run.
     */
    public function shouldBeUpdated()
    {
        try {
            // Clear the current updates array to avoid doublons
            $this->updates = [];

            // If no version setup, just call the first
            if (!Util::getCurrentVersion()) {
                $this->updates[] = 'to050';

                return !empty($this->updates);
            }

            // We need to compare current version with the package one
            $arrPackageVersion = explode('.', Util::getPackageVersion('webexmachina/contao-smartgear'));
            $arrCurrentVersion = explode('.', Util::getCurrentVersion());

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
            if ($this->sgVersion !== Util::getCurrentVersion()) {
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

            if (!$objTheme) {
                return;
            }

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
            Util::updateConfig(['sgVersion' => Util::getPackageVersion('webexmachina/contao-smartgear')]);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
