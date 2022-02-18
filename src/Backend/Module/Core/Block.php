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

namespace WEM\SmartgearBundle\Backend\Module\Core;

use Contao\Input;
use Contao\System;
use Exception;
use WEM\SmartgearBundle\Classes\Backend\Block as BackendBlock;

class Block extends BackendBlock
{
    protected $type = 'core';
    protected $module = 'core';
    protected $icon = 'exclamation-triangle';
    protected $title = 'Core';

    public function processAjaxRequest()
    {
        try {
            switch (Input::post('action')) {
                case 'framwayRetrieval':
                    $framwayRetrievalStep = System::getContainer()->get('smartgear.backend.module.core.configuration_step.framway_retrieval');
                    $res = $framwayRetrievalStep->framwayRetrieve();
                    $arrResponse['status'] = 'success';
                    $arrResponse['msg'] = 'Le framway a été récupéré avec succès';
                    $arrResponse['output'] = $res;
                break;
                case 'framwayInstall':
                    try {
                        $framwayRetrievalStep = System::getContainer()->get('smartgear.backend.module.core.configuration_step.framway_retrieval');
                        $res = $framwayRetrievalStep->framwayInstall();
                        $arrResponse['status'] = 'success';
                        $arrResponse['msg'] = 'Le framway a été installé avec succès';
                        $arrResponse['output'] = $res;
                    } catch (Exception $e) {
                        $arrResponse['status'] = 'error';
                        $arrResponse['msg'] = 'Une erreur est surevenue lors de l\'installation du framway';
                        $arrResponse['output'] = $e->getMessage();
                    }
                break;
                case 'framwayInitialize':
                    try {
                        $framwayRetrievalStep = System::getContainer()->get('smartgear.backend.module.core.configuration_step.framway_retrieval');
                        $res = $framwayRetrievalStep->framwayInitialize();
                        $arrResponse['status'] = 'success';
                        $arrResponse['msg'] = 'Le framway a été initialisé avec succès';
                        $arrResponse['output'] = $res;
                    } catch (Exception $e) {
                        $arrResponse['status'] = 'error';
                        $arrResponse['msg'] = 'Une erreur est surevenue lors de l\'initialisation du framway';
                        $arrResponse['output'] = $e->getMessage();
                    }
                break;
                case 'framwayBuild':
                    try {
                        $framwayRetrievalStep = System::getContainer()->get('smartgear.backend.module.core.configuration_step.framway_retrieval');
                        $res = $framwayRetrievalStep->framwayBuild();
                        $arrResponse['status'] = 'success';
                        $arrResponse['msg'] = 'Le framway a été build avec succès';
                        $arrResponse['output'] = $res;
                    } catch (Exception $e) {
                        $arrResponse['status'] = 'error';
                        $arrResponse['msg'] = 'Une erreur est surevenue lors du build du framway';
                        $arrResponse['output'] = $e->getMessage();
                    }
                break;
                case 'nothing':
                    // $framwayRetrievalStep = System::getContainer()->get('smartgear.backend.module.core.configuration_step.framway_retrieval');
                    // $res = $framwayRetrievalStep->retrieve();
                    $arrResponse['status'] = 'success';
                    $arrResponse['msg'] = 'Le framway a été récupéré avec succès';
                break;
                default:
                    return parent::processAjaxRequest();
                break;
            }
        } catch (Exception $e) {
            $arrResponse = ['status' => 'error', 'msg' => $e->getMessage(), 'trace' => $e->getTrace()];
        }

        return $arrResponse;
    }
}
