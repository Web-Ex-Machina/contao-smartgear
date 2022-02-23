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

    public function processAjaxRequest(): void
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
                        $arrResponse['msg'] = 'Une erreur est survenue lors de l\'installation du framway';
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
                        $arrResponse['msg'] = 'Une erreur est survenue lors de l\'initialisation du framway';
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
                        $arrResponse['msg'] = 'Une erreur est survenue lors du build du framway';
                        $arrResponse['output'] = $e->getMessage();
                    }
                break;
                case 'framwayThemeAdd':
                    try {
                        $framwayConfigurationStep = System::getContainer()->get('smartgear.backend.module.core.configuration_step.framway_configuration');
                        $res = $framwayConfigurationStep->framwayThemeAdd();
                        $arrResponse['status'] = 'success';
                        $arrResponse['msg'] = 'Le thème a été ajouté au framway avec succès';
                        $arrResponse['output'] = $res;
                    } catch (Exception $e) {
                        $arrResponse['status'] = 'error';
                        $arrResponse['msg'] = 'Une erreur est survenue lors de l\'ajout du thème au framway : '.$e->getMessage();
                        $arrResponse['output'] = $e->getMessage();
                    }
                break;
                case 'configure':
                    $this->setMode(self::MODE_CONFIGURE);
                    $this->configurationStepManager->goToStep(0);
                    $arrResponse = ['status' => 'success', 'msg' => '', 'callbacks' => [$this->callback('refreshBlock')]];
                break;
                default:
                    parent::processAjaxRequest();
                break;
            }
        } catch (Exception $e) {
            $arrResponse = ['status' => 'error', 'msg' => $e->getMessage(), 'trace' => $e->getTrace()];
        }

        // return $arrResponse;
        // Add Request Token to JSON answer and return
        $arrResponse['rt'] = \RequestToken::get();
        echo json_encode($arrResponse);
        exit;
    }
}
