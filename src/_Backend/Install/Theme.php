<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2021 Web ex Machina
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
class Theme extends BlockInstall implements BlockInstallInterface
{
	/**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->type = 'install';
        $this->module = 'theme';
        $this->icon = 'exclamation-triangle';
        $this->title = 'Smartgear | Installation | Thème';
        $this->class = 'content full block-install';
        $this->status = 0;

        // Load config
        if (\Input::post('framwayFolderPath')) {
            $this->framwayFolderPath = \Input::post('framwayFolderPath');
            $this->objSession->set('sg_install_framwayFolderPath', $this->framwayFolderPath);
        } elseif ($this->objSession->get('sg_install_framwayFolderPath')) {
            $this->framwayFolderPath = $this->objSession->get('sg_install_framwayFolderPath');
        } elseif ($this->sgConfig['framwayFolderPath']) {
            $this->framwayFolderPath = $this->sgConfig['framwayFolderPath'];
        }

        if (\Input::post('framwayTheme')) {
            $this->framwayTheme = \Input::post('framwayTheme');
            $this->objSession->set('sg_install_framwayTheme', $this->framwayTheme);
        } elseif ($this->objSession->get('sg_install_framwayTheme')) {
            $this->framwayTheme = $this->objSession->get('sg_install_framwayTheme');
        } elseif ($this->sgConfig['framwayTheme']) {
            $this->framwayTheme = $this->sgConfig['framwayTheme'];
        }

        /*if (\Input::post('vendorFolderPath')) {
            $this->vendorFolderPath = \Input::post('vendorFolderPath');
            $this->objSession->set('sg_install_vendorFolderPath', $this->vendorFolderPath);
        } elseif ($this->objSession->get('sg_install_vendorFolderPath')) {
            $this->vendorFolderPath = $this->objSession->get('sg_install_vendorFolderPath');
        } elseif ($this->sgConfig['vendorFolderPath']) {
            $this->vendorFolderPath = $this->sgConfig['vendorFolderPath'];
        }*/

        // Add block fields
        $this->addTextField('framwayFolderPath', 'Chemin vers la racine du framway', $this->framwayFolderPath, true);
        $this->addTextField('framwayTheme', 'Nom du thème framway', $this->framwayTheme, true);
        // $this->addTextField('vendorFolderPath', 'Chemin vers le répertoire des librairies', $this->vendorFolderPath, true);

        // Add generic button
        $this->addAction('apply', 'Vérifier les fichiers');

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
        if (!$this->hasErrors()) {
            $this->addConfirm('Les fichiers ont été trouvés, sont à jour et sont accessibles !');
            $this->addAction('next', 'Suivant');

            $this->status = 1;
        } else {
            $this->addAction('process', 'Importer les fichiers Smartgear');
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
            

            // And return an explicit status with some instructions
            return [
                'toastr' => $this->callback('toastr', ['success', 'Les fichiers ont été trouvés, sont à jour et sont accessibles !']), 'callbacks' => [$this->callback('check')],
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }
}