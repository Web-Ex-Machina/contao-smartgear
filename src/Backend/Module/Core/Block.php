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

use Contao\FrontendTemplate;
use Contao\Input;
use Contao\System;
use Exception;
use WEM\SmartgearBundle\Classes\Backend\Block as BackendBlock;
use WEM\SmartgearBundle\Classes\Util;

class Block extends BackendBlock
{
    public const MODE_CHECK_PROD = 'check_prod';
    public const MODE_RESET_PROD = 'check_reset';
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
                case 'dev_mode':
                    $this->dashboard->enableDevMode();
                    $arrResponse = ['status' => 'success', 'msg' => 'Le mode "développement" a bien été activé', 'callbacks' => [$this->callback('refreshBlock')]];
                break;
                case 'prod_mode':
                    $this->dashboard->enableProdMode();
                    $arrResponse = ['status' => 'success', 'msg' => 'Le mode "production" a bien été activé', 'callbacks' => [$this->callback('refreshBlock')]];
                break;
                case 'prod_mode_check':
                    $this->setMode(self::MODE_CHECK_PROD);
                    $content = $this->parse();
                    $arrResponse = ['status' => 'success', 'msg' => '', 'callbacks' => [$this->callback('replaceBlockContent', [$content])]];
                break;
                case 'reset_mode_check':
                    $this->setMode(self::MODE_RESET_PROD);
                    $content = $this->parse();
                    $arrResponse = ['status' => 'success', 'msg' => '', 'callbacks' => [$this->callback('replaceBlockContent', [$content])]];
                break;
                case 'prod_mode_check_cancel':
                case 'reset_mode_check_cancel':
                    $this->setMode(self::MODE_DASHBOARD);
                    $content = $this->parse();
                    $arrResponse = ['status' => 'success', 'msg' => '', 'callbacks' => [$this->callback('replaceBlockContent', [$content])]];
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

    protected function parseDependingOnMode(FrontendTemplate $objTemplate): FrontendTemplate
    {
        switch ($this->getMode()) {
            case self::MODE_CHECK_PROD:
                $objTemplate->content = $this->dashboard->checkProdMode();
                $objTemplate->logs = $this->dashboard->getLogs();
                $objTemplate->messages = $this->dashboard->getMessages();
                $objTemplate->actions = Util::formatActions($this->dashboard->getActions());
            break;
            case self::MODE_RESET_PROD:
                $objTemplate->content = $this->dashboard->checkReset();
                $objTemplate->logs = $this->dashboard->getLogs();
                $objTemplate->messages = $this->dashboard->getMessages();
                $objTemplate->actions = Util::formatActions($this->dashboard->getActions());
            break;
            default:
                $objTemplate = parent::parseDependingOnMode($objTemplate);
            break;
        }

        return $objTemplate;
    }
}
