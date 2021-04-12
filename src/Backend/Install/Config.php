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
class Config extends BlockInstall implements BlockInstallInterface
{
	/**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->type = 'install';
        $this->module = 'config';
        $this->icon = 'exclamation-triangle';
        $this->title = 'Smartgear | Installation | Configuration';
        $this->class = 'content full block-install';
        $this->status = 0;

        // Load default values
        $this->loadDefaultValues();

        // Check values
        $this->checkValue('websiteTitle');
        $this->checkValue('dateFormat');
        $this->checkValue('timeFormat');
        $this->checkValue('datimFormat');
        $this->checkValue('adminEmail');
        $this->checkValue('ownerTitle');
        $this->checkValue('ownerStatus');
        $this->checkValue('ownerSIRET');
        $this->checkValue('ownerAddress');
        $this->checkValue('ownerEmail');
        $this->checkValue('ownerDomain');
        $this->checkValue('ownerHost');

        // Add block fields
        $this->addTextField('websiteTitle', 'Nom du site', $this->websiteTitle, true, 'w50');
        $this->addTextField('dateFormat', 'Format de la date', $this->dateFormat, true, 'w50');
        $this->addTextField('timeFormat', 'Format de l\'heure', $this->timeFormat, true, 'w50');
        $this->addTextField('datimFormat', 'Format de la date et de l\'heure', $this->datimFormat, true, 'w50');
        $this->addTextField('adminEmail', 'Adresse email de l\'admin', $this->adminEmail, true, 'w50');
        $this->addTextField('ownerTitle', 'Nom du propriétaire du site', $this->ownerTitle, true, 'w50');
        $this->addTextField('ownerStatus', 'Statut du propriétaire', $this->ownerStatus, true, 'w50');
        $this->addTextField('ownerSIRET', 'SIRET du propriétaire', $this->ownerSIRET, true, 'w50');
        $this->addTextField('ownerAddress', 'Adresse du propriétaire', $this->ownerAddress, true, 'w50');
        $this->addTextField('ownerEmail', 'Email du propriétaire', $this->ownerEmail, true, 'w50');
        $this->addTextField('ownerDomain', 'Domaine du propriétaire', $this->ownerDomain, true, 'w50');
        $this->addTextField('ownerHost', 'Hébergeur', $this->ownerHost, true, 'w50');

        // Add generic button
        $this->addAction('apply', 'Vérifier la config');

        // Get step status
        $this->getStatus();
    }

    protected function loadDefaultValues()
    {
        $this->websiteTitle = "Smartgear";
        $this->dateFormat = 'd/m/Y';
        $this->timeFormat = 'H:i';
        $this->datimFormat = 'd/m/Y à H:i';
        $this->timeZone = 'Europe/Paris';
        $this->adminEmail = 'contact@webexmachina.fr';
        $this->characterSet = 'utf-8';
        $this->useAutoItem = 1;
        $this->privacyAnonymizeIp = 1;
        $this->privacyAnonymizeGA = 1;
        $this->gdMaxImgWidth = 5000;
        $this->gdMaxImgHeight = 5000;
        $this->maxFileSize = 20971520;
        $this->undoPeriod = 7776000; // 3 months
        $this->versionPeriod = 7776000; // 3 months
        $this->logPeriod = 7776000; // 3 months
        $this->allowedTags = '<script><iframe><a><abbr><acronym><address><area><article><aside><audio><b><bdi><bdo><big><blockquote><br><base><button><canvas><caption><cite><code><col><colgroup><data><datalist><dataset><dd><del><dfn><div><dl><dt><em><fieldset><figcaption><figure><footer><form><h1><h2><h3><h4><h5><h6><header><hgroup><hr><i><img><input><ins><kbd><keygen><label><legend><li><link><map><mark><menu><nav><object><ol><optgroup><option><output><p><param><picture><pre><q><s><samp><section><select><small><source><span><strong><style><sub><sup><table><tbody><td><textarea><tfoot><th><thead><time><tr><tt><u><ul><var><video><wbr>';
        $this->ownerDomain = \Environment::get('base');
        $this->ownerHost = 'INFOMANIAK - 25 Eugène-Marziano 1227 Les Acacias - GENÈVE - Suisse';
    }

    /**
     * Check Framway Status.
     *
     * @return [String] [Template of the module check status]
     */
    public function getStatus()
    {
        if (!$this->hasErrors()) {
            $this->addConfirm('La configuration est valide !');
            $this->addAction('next', 'Suivant');

            $this->status = 1;
        } else {
            $this->addAction('process', 'Sauvegarder la configuration');
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