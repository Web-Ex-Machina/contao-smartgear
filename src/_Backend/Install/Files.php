<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2022 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

namespace WEM\SmartgearBundle\Backend\Install;

use Exception;
use WEM\SmartgearBundle\Backend\BlockInstall;
use WEM\SmartgearBundle\Backend\BlockInstallInterface;
use WEM\SmartgearBundle\Backend\Util;

/**
 * Back end module "smartgear".
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Files extends BlockInstall implements BlockInstallInterface
{
    /**
     * Smartgear files location.
     *
     * @var string
     */
    protected $sgFolderPath = 'public/bundles/wemsmartgear/contao_files';

    /**
     * App default location.
     *
     * @var string
     */
    protected $appFolderPath = 'app';

    /**
     * Assets default location.
     *
     * @var string
     */
    protected $assetsFolderPath = 'assets';

    /**
     * Framway default location.
     *
     * @var string
     */
    protected $framwayFolderPath = 'files/app';

    /**
     * Framway default theme.
     *
     * @var string
     */
    protected $framwayTheme = 'smartgear';

    /**
     * Vendor default location.
     *
     * @var string
     */
    protected $vendorFolderPath = 'files/vendor';

    /**
     * Template default location.
     *
     * @var string
     */
    protected $templatesFolderPath = 'templates';

    /**
     * Missing files.
     *
     * @var array
     */
    protected $arrFilesToAdd = [];

    /**
     * Files not to date.
     *
     * @var array
     */
    protected $arrFilesToUpdate = [];

    /**
     * Files to remove.
     *
     * @var array
     */
    protected $arrFilesToDelete = [];

    /**
     * Folders to unprotect.
     *
     * @var array
     */
    protected $arrFoldersToUnprotect = [];

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->type = 'install';
        $this->module = 'files';
        $this->icon = 'exclamation-triangle';
        $this->title = 'Smartgear | Installation | Files';
        $this->class = 'content full block-install';
        $this->status = 0;

        $this->objFiles = \Files::getInstance();
        $this->strRootDir = \System::getContainer()->getParameter('kernel.project_dir');

        // Load config
        $this->checkValue('framwayFolderPath');
        $this->checkValue('framwayTheme');
        // $this->checkValue('vendorFolderPath');

        // Add block intro
        $this->addInfo('Sélectionnez l\'emplacement du Framway et stockez sa configuration.');

        // Add block fields
        $this->addTextField('framwayFolderPath', 'Chemin vers la racine du framway', $this->framwayFolderPath, true, 'w50');
        $this->addTextField('framwayTheme', 'Nom du thème framway', $this->framwayTheme, true, 'w50');
        // $this->addTextField('vendorFolderPath', 'Chemin vers le répertoire des librairies', $this->vendorFolderPath, true);

        // Check App files
        $this->checkAppFiles();

        // Check Assets files
        $this->checkAssetsFiles();

        // Check Framway files
        $this->checkFramwayFiles();

        // Check Templates files
        $this->checkTemplatesFiles();

        // Check vendor files
        $this->checkVendorFiles();

        // Add generic button
        $this->addAction('check', 'Vérifier les fichiers');

        // Get step status
        $this->getStatus();
    }

    /**
     * Check Framway Status.
     *
     * @return [String] [Template of the module check status]
     */
    public function getStatus()
    {
        if (!empty($this->arrFilesToAdd)) {
            foreach ($this->arrFilesToAdd as $p) {
                $this->addError(sprintf('Le fichier %s doit être importé', $p));
            }
        }

        if (!empty($this->arrFilesToUpdate)) {
            foreach ($this->arrFilesToUpdate as $p) {
                $this->addError(sprintf('Le fichier %s doit être mis à jour', $p));
            }
        }

        if (!empty($this->arrFilesToDelete)) {
            foreach ($this->arrFilesToDelete as $p) {
                $this->addError(sprintf('Le fichier %s doit être supprimé', $p));
            }
        }

        if (!empty($this->arrFoldersToUnprotect)) {
            foreach ($this->arrFoldersToUnprotect as $p) {
                $this->addError(sprintf('Le dossier %s doit être accessible', $p));
            }
        }

        if ($this->hasErrors() || $this->hasUpdates()) {
            $this->addAction('process', 'Importer les fichiers Smartgear');
        }

        if (!$this->hasErrors()) {
            $this->addConfirm('Les fichiers ont été trouvés, sont à jour et sont accessibles !');
            $this->addAction('next', 'Suivant');

            $this->status = 1;
        }
    }

    /**
     * Check if Framway & Vendor files exists and if we can access to them.
     */
    public function check()
    {
        try {
            if ($this->hasErrors()) {
                return [
                    'toastr' => $this->callback('toastr', ['error', 'Fichiers manquants']),
                    'callbacks' => [$this->callback('refreshBlock')],
                ];
            }

            if ($this->hasUpdates()) {
                return [
                    'toastr' => $this->callback('toastr', ['info', 'Des fichiers sont à mettre à jour']),
                    'callbacks' => [$this->callback('refreshBlock')],
                ];
            }

            // And return an explicit status with some instructions
            return [
                'toastr' => $this->callback('toastr', ['success', 'Les fichiers ont été trouvés, sont à jour et sont accessibles !']),
                'callbacks' => [$this->callback('refreshBlock')],
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function next()
    {
        try {
            if ($this->hasErrors()) {
                return [
                    'toastr' => $this->callback('toastr', ['error', 'Fichiers manquants']), 'callbacks' => [$this->callback('refreshBlock')],
                ];
            }

            // If everything ok, store the new config
            if ($this->framwayFolderPath !== $this->sgConfig['framwayFolderPath']) {
                $this->sgConfig['framwayFolderPath'] = $this->framwayFolderPath;
            }

            if ($this->framwayTheme !== $this->sgConfig['framwayTheme']) {
                $this->sgConfig['framwayTheme'] = $this->framwayTheme;
            }

            /*if ($this->vendorFolderPath !== $this->sgConfig['vendorFolderPath']) {
                $this->sgConfig['vendorFolderPath'] = $this->vendorFolderPath;
            }*/

            Util::updateConfig($this->sgConfig);

            // And return an explicit status with some instructions
            return [
                'toastr' => $this->callback('toastr', ['success', 'Les fichiers ont été trouvés, sont à jour et sont accessibles !']), 'callbacks' => [$this->callback('loadNextStep')],
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function process()
    {
        try {
            if (!empty($this->arrFilesToAdd)) {
                foreach ($this->arrFilesToAdd as $p) {
                    $objFile = new \File($p);
                    $objFile->copyTo(str_replace($this->sgFolderPath.'/', '', $p));
                }
            }

            if (!empty($this->arrFilesToUpdate)) {
                foreach ($this->arrFilesToUpdate as $p) {
                    $objFileA = new \File($p); // Source file
                    $objFileB = new \File(str_replace($this->sgFolderPath.'/', '', $p)); // App file

                    $objFileB->truncate();
                    $objFileB->write($objFileA->getContent());

                    $objFileB->close();
                }
            }

            if (!empty($this->arrFilesToDelete)) {
                foreach ($this->arrFilesToDelete as $p) {
                    $objFile = new \File($p);
                    $objFile->delete();
                }
            }

            if (!empty($this->arrFoldersToUnprotect)) {
                foreach ($this->arrFoldersToUnprotect as $p) {
                    $objFolder = new \Folder($p);
                    $objFolder->unprotect();
                }
            }

            // And return an explicit status with some instructions
            return [
                'toastr' => $this->callback('toastr', ['info', 'Import des fichiers terminé']),
                'callbacks' => [$this->callback('refreshBlock')],
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }

    protected function checkAppFiles(): void
    {
        $this->checkFiles($this->getAppFilesPath());
    }

    protected function checkAssetsFiles(): void
    {
        $this->checkFiles($this->getAssetsFilesPath());
    }

    protected function checkFramwayFiles(): void
    {
        $objFile = new \Folder($this->framwayFolderPath);
        if (!$objFile->isUnprotected()) {
            $arrFoldersToUnprotect[] = $this->framwayFolderPath;
        }

        foreach ($this->getFramwayFilesPath() as $p) {
            $objFile = new \File($this->framwayFolderPath.'/'.$p);

            if (!$objFile->exists()) {
                $this->arrFilesToAdd[] = $p;
            }
        }
    }

    protected function checkTemplatesFiles(): void
    {
        $this->checkFiles($this->getTemplatesFilesPath());
    }

    protected function checkVendorFiles(): void
    {
        $objFile = new \Folder($this->vendorFolderPath);
        if (!$objFile->isUnprotected()) {
            $arrFoldersToUnprotect[] = $this->vendorFolderPath;
        }

        $this->checkFiles($this->getVendorFilesPath());
    }

    protected function getAppFilesPath()
    {
        return $this->getFiles($this->sgFolderPath.'/'.$this->appFolderPath, true);
    }

    protected function getAssetsFilesPath()
    {
        return $this->getFiles($this->sgFolderPath.'/'.$this->assetsFolderPath, true);
    }

    protected function getFramwayFilesPath()
    {
        return [
            'build/css/framway.css',
            'build/css/vendor.css',
            'build/js/framway.js',
            'build/js/vendor.js',
            'src/themes/'.$this->framwayTheme.'/_config.scss',
            'src/themes/'.$this->framwayTheme.'/_smartgear.scss',
        ];
    }

    protected function getTemplatesFilesPath()
    {
        return $this->getFiles($this->sgFolderPath.'/'.$this->templatesFolderPath, true);
    }

    protected function getVendorFilesPath()
    {
        return $this->getFiles($this->sgFolderPath.'/'.$this->vendorFolderPath, true);
    }

    protected function checkFiles($arrFiles): void
    {
        foreach ($arrFiles as $p) {
            $objFile = new \File($p);
            $objFileApp = new \File(str_replace($this->sgFolderPath.'/', '', $p));

            if (!$objFileApp->exists() && $objFile->exists()) {
                $this->arrFilesToAdd[] = $objFile->path;
            } elseif ($objFileApp->exists() && !$objFile->exists()) {
                $this->arrFilesToDelete[] = $objFileApp->path;
            } elseif ($this->checkIfFilesAreDifferent($objFileApp, $objFile)) {
                $this->arrFilesToUpdate[] = $objFile->path;
            }
        }
    }

    protected function getFiles($strFolder, $blnGetSubFolders = true)
    {
        try {
            $strBasePath = $this->strRootDir.'/'.$strFolder;
            $arrFiles = scandir($strBasePath);
            $arrPaths = [];

            foreach ($arrFiles as $f) {
                if ('.' === $f || '..' === $f) {
                    continue;
                }

                $isFolder = is_dir($strBasePath.'/'.$f);

                if ($blnGetSubFolders && $isFolder) {
                    $arrPaths = array_merge($arrPaths, $this->getFiles($strFolder.'/'.$f, $blnGetSubFolders));
                } elseif (!$isFolder) {
                    $arrPaths[] = $strFolder.'/'.$f;
                }
            }

            return $arrPaths;
        } catch (Exception $e) {
            throw $e;
        }
    }

    protected function checkIfFilesAreDifferent($objFileA, $objFileB)
    {
        try {
            if (!$objFileA->exists()) {
                return true;
            }
            if (!$objFileB->exists()) {
                return true;
            }
            if ($objFileA->hash !== $objFileB->hash) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
