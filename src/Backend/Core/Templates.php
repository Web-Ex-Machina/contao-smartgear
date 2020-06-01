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

namespace WEM\SmartgearBundle\Backend\Core;

use Exception;
use WEM\SmartgearBundle\Backend\Block;
use WEM\SmartgearBundle\Backend\BlockInterface;
use WEM\SmartgearBundle\Backend\Util;

/**
 * Back end module "smartgear".
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Templates extends Block implements BlockInterface
{
    /**
     * Module dependancies.
     *
     * @var array
     */
    protected $require = ['core_core'];

    /**
     * Smartgear files folder.
     *
     * @var string
     */
    protected $strBasePath = 'web/bundles/wemsmartgear/contao_files';

    /**
     * Should be updated.
     *
     * @var bool
     */
    protected $blnShouldBeUpdated = false;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->type = 'core';
        $this->module = 'templates';
        $this->icon = 'exclamation-triangle';
        $this->title = 'Smartgear | Core | Fichiers';

        $this->objFiles = \Files::getInstance();

        parent::__construct();
    }

    /**
     * Check Smartgear Status.
     *
     * @return [String] [Template of the module check status]
     */
    public function getStatus()
    {
        $this->messages[] = ['class' => 'tl_info', 'text' => 'Cette section permet d\'importer les fichiers Contao utilisés par Smartgear.'];
        $this->shouldBeUpdated();

        // Check the install status
        if (1 === $this->sgConfig['sgInstallFiles']) {
            $this->status = 1;

            // Compare the source folder and the existing one to check if an update should be done
            if ($this->blnShouldBeUpdated) {
                $this->messages[] = ['class' => 'tl_new', 'text' => 'Il y a une différence entre les fichiers. Mettez à jour via le bouton ci-dessous'];
                $this->actions[] = ['action' => 'reset', 'label' => 'Mettre à jour les fichiers Contao'];
            } else {
                $this->messages[] = ['class' => 'tl_confirm', 'text' => 'Les fichiers Contao sont à jour.'];
                $this->actions[] = ['action' => 'reset', 'label' => 'Réinitialiser les fichiers Contao'];
            }

            $this->actions[] = ['action' => 'remove', 'label' => 'Supprimer les fichiers Contao'];
        } else {
            $this->actions[] = ['action' => 'install', 'label' => 'Importer les fichiers Contao'];
            $this->status = 0;
        }
    }

    /**
     * Install Contao files.
     */
    public function install()
    {
        try {
            $this->rcopy($this->strBasePath, '');
            $this->logs[] = ['status' => 'tl_confirm', 'msg' => 'Les fichiers Contao ont été importés.'];

            // Update config
            Util::updateConfig(['sgInstallFiles' => 1]);

            // And return an explicit status with some instructions
            return [
                'toastr' => [
                    'status' => 'success', 'msg' => "L'installation des fichiers Contao a été effectuée avec succès.",
                ], 'callbacks' => [
                    0 => [
                        'method' => 'refreshBlock', 'args' => ['block-'.$this->type.'-'.$this->module],
                    ],
                ],
            ];
        } catch (Exception $e) {
            $this->remove();
            throw $e;
        }
    }

    /**
     * Remove Contao templates.
     */
    public function remove()
    {
        try {
            //$this->objFiles->rrdir("templates/smartgear");
            $this->logs[] = ['status' => 'tl_confirm', 'msg' => 'Les fichiers Contao ont été supprimés.'];

            // Update config
            Util::updateConfig(['sgInstallFiles' => 0]);

            // And return an explicit status with some instructions
            return [
                'toastr' => [
                    'status' => 'success', 'msg' => 'La désinstallation des fichiers Contao a été effectuée avec succès.',
                ], 'callbacks' => [
                    0 => [
                        'method' => 'refreshBlock', 'args' => ['block-'.$this->type.'-'.$this->module],
                    ],
                ],
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Calculate the difference between Contao templates Source Folder and the installed one.
     */
    protected function shouldBeUpdated(): void
    {
        try {
            clearstatcache();
            $this->blnShouldBeUpdated = false;
            $this->checkFolderDifferences($this->strBasePath);
        } catch (Exception $e) {
            $this->blnShouldBeUpdated = true;
        }
    }

    protected function checkFolderDifferences($strPath): void
    {
        try {
            $strBasePath = TL_ROOT.'/'.$strPath;
            $arrFiles = scandir($strBasePath);

            foreach ($arrFiles as $f) {
                if ('.' === $f || '..' === $f) {
                    continue;
                }

                $strFilePath = $strPath.'/'.$f;

                if (is_dir(TL_ROOT.'/'.$strFilePath)) {
                    $this->checkFolderDifferences($strFilePath);
                } else {
                    $this->checkIfFilesDifferent($strFilePath);
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    protected function checkIfFilesDifferent($strPackageFilePath): void
    {
        try {
            $objPackageFile = new \File($strPackageFilePath);
            $objLocalFile = new \File(str_replace($this->strBasePath.'/', '', $strPackageFilePath));

            if (!$objLocalFile || !$objLocalFile->exists()) {
                $this->messages[] = ['class' => 'tl_new', 'text' => 'Fichier à importer : '.$objLocalFile->path];
                $this->blnShouldBeUpdated = true;
            } elseif ($objLocalFile->hash !== $objPackageFile->hash) {
                $this->messages[] = ['class' => 'tl_new', 'text' => 'Fichier à mettre à jour : '.$objLocalFile->path];
                $this->blnShouldBeUpdated = true;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Recursively copy a directory
     * 
     *
     * @param string $strSource      The source file or folder
     * @param string $strDestination The new file or folder path
     */
    protected function rcopy($strSource, $strDestination)
    {
        $arrFiles = scan(\System::getContainer()->getParameter('kernel.project_dir') . '/' . $strSource, true);

        foreach ($arrFiles as $strFile)
        {
            if (is_dir(\System::getContainer()->getParameter('kernel.project_dir') . '/' . $strSource . '/' . $strFile))
            {
                $this->rcopy($strSource . '/' . $strFile, $strDestination . '/' . $strFile);
            }
            else
            {
                $this->objFiles->copy($strSource . '/' . $strFile, $strDestination . '/' . $strFile);
            }
        }
    }
}
