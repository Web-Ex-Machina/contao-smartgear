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

namespace WEM\SmartgearBundle\Backend\Component\Core;

use Contao\FrontendTemplate;
use Contao\Input;
use Contao\System;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Backend\Block as BackendBlock;
use WEM\SmartgearBundle\Classes\Backend\ConfigurationStepManager;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\Util;

class Block extends BackendBlock
{
    public const MODE_CHECK_PROD = 'check_prod';
    public const MODE_RESET = 'check_reset';
    protected $type = 'component';
    protected $module = 'core';
    protected $icon = 'exclamation-triangle';
    protected $title = 'Core';

    protected $resetStepManager;

    public function __construct(
        ConfigurationManager $configurationManager,
        ConfigurationStepManager $configurationStepManager,
        ResetStepManager $resetStepManager,
        Dashboard $dashboard,
        TranslatorInterface $translator
    ) {
        parent::__construct($configurationManager, $configurationStepManager, $dashboard, $translator);
        $this->resetStepManager = $resetStepManager;
    }

    public function processAjaxRequest(): void
    {
        try {
            switch (Input::post('action')) {
                case 'framwayRetrieval':
                    $framwayRetrievalStep = System::getContainer()->get('smartgear.backend.module.core.configuration_step.framway_retrieval');
                    $res = $framwayRetrievalStep->framwayRetrieve();
                    $arrResponse['status'] = 'success';
                    $arrResponse['msg'] = $GLOBALS['TL_LANG']['WEMSG']['CORE']['BLOCK']['framwayRetrievalAjaxMessageSuccess'];
                    $arrResponse['output'] = $res;
                break;
                case 'framwayInstall':
                    try {
                        $framwayRetrievalStep = System::getContainer()->get('smartgear.backend.module.core.configuration_step.framway_retrieval');
                        $res = $framwayRetrievalStep->framwayInstall();
                        $arrResponse['status'] = 'success';
                        $arrResponse['msg'] = $GLOBALS['TL_LANG']['WEMSG']['CORE']['BLOCK']['framwayInstallAjaxMessageSuccess'];
                        $arrResponse['output'] = $res;
                    } catch (Exception $e) {
                        $arrResponse['status'] = 'error';
                        $arrResponse['msg'] = $GLOBALS['TL_LANG']['WEMSG']['CORE']['BLOCK']['framwayInstallAjaxMessageError'];
                        $arrResponse['output'] = $e->getMessage();
                    }
                break;
                case 'framwayInitialize':
                    try {
                        $framwayRetrievalStep = System::getContainer()->get('smartgear.backend.module.core.configuration_step.framway_retrieval');
                        $res = $framwayRetrievalStep->framwayInitialize();
                        $arrResponse['status'] = 'success';
                        $arrResponse['msg'] = $GLOBALS['TL_LANG']['WEMSG']['CORE']['BLOCK']['framwayInitialiazeAjaxMessageSuccess'];
                        $arrResponse['output'] = $res;
                    } catch (Exception $e) {
                        $arrResponse['status'] = 'error';
                        $arrResponse['msg'] = $GLOBALS['TL_LANG']['WEMSG']['CORE']['BLOCK']['framwayInitialiazeAjaxMessageError'];
                        $arrResponse['output'] = $e->getMessage();
                    }
                break;
                case 'framwayBuild':
                    try {
                        $framwayRetrievalStep = System::getContainer()->get('smartgear.backend.module.core.configuration_step.framway_retrieval');
                        $res = $framwayRetrievalStep->framwayBuild();
                        $arrResponse['status'] = 'success';
                        $arrResponse['msg'] = $GLOBALS['TL_LANG']['WEMSG']['CORE']['BLOCK']['framwayBuildAjaxMessageSuccess'];
                        $arrResponse['output'] = $res;
                    } catch (Exception $e) {
                        $arrResponse['status'] = 'error';
                        $arrResponse['msg'] = $GLOBALS['TL_LANG']['WEMSG']['CORE']['BLOCK']['framwayBuildAjaxMessageError'];
                        $arrResponse['output'] = $e->getMessage();
                    }
                break;
                case 'framwayThemeAdd':
                    try {
                        $framwayConfigurationStep = System::getContainer()->get('smartgear.backend.module.core.configuration_step.framway_configuration');
                        $res = $framwayConfigurationStep->framwayThemeAdd();
                        $arrResponse['status'] = 'success';
                        $arrResponse['msg'] = $GLOBALS['TL_LANG']['WEMSG']['CORE']['BLOCK']['framwayThemeAddAjaxMessageSuccess'];
                        $arrResponse['output'] = $res;
                    } catch (Exception $e) {
                        $arrResponse['status'] = 'error';
                        $arrResponse['msg'] = $GLOBALS['TL_LANG']['WEMSG']['CORE']['BLOCK']['framwayThemeAddAjaxMessageError'].$e->getMessage();
                        $arrResponse['output'] = $e->getMessage();
                    }
                break;
                case 'dev_mode':
                    $this->dashboard->enableDevMode();
                    $arrResponse = ['status' => 'success', 'msg' => $GLOBALS['TL_LANG']['WEMSG']['CORE']['BLOCK']['enableDevModeAjaxMessageSuccess'], 'callbacks' => [$this->callback('refreshBlock')]];
                break;
                case 'prod_mode':
                    $this->dashboard->enableProdMode();
                    $arrResponse = ['status' => 'success', 'msg' => $GLOBALS['TL_LANG']['WEMSG']['CORE']['BLOCK']['enableProdModeAjaxMessageSuccess'], 'callbacks' => [$this->callback('refreshBlock')]];
                break;
                case 'prod_mode_check':
                    $this->setMode(self::MODE_CHECK_PROD);
                    $content = $this->parse();
                    $arrResponse = ['status' => 'success', 'msg' => '', 'callbacks' => [$this->callback('replaceBlockContent', [$content])]];
                break;
                case 'reset_mode':
                    $this->setMode(self::MODE_RESET);
                    $this->resetStepManager->goToStep(0);
                    $arrResponse = ['status' => 'success', 'msg' => '', 'callbacks' => [$this->callback('refreshBlock')]];
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
                $objTemplate->actions = $this->formatActions($this->dashboard->getActions());
            break;
            case self::MODE_RESET:
                $objTemplate->steps = $this->resetStepManager->parseSteps();
                $objTemplate->content = $this->resetStepManager->parse();
            break;
            default:
                $objTemplate = parent::parseDependingOnMode($objTemplate);
            break;
        }

        return $objTemplate;
    }

    protected function goToNextStep(): void
    {
        switch ($this->getMode()) {
            case self::MODE_RESET:
                $this->resetStepManager->goToNextStep();
            break;
            default:
                parent::goToNextStep();
            break;
        }
    }

    protected function goToPreviousStep(): void
    {
        switch ($this->getMode()) {
            case self::MODE_RESET:
                $this->resetStepManager->goToPreviousStep();
            break;
            default:
                parent::goToPreviousStep();
            break;
        }
    }

    protected function goToStep(int $stepIndex): void
    {
        switch ($this->getMode()) {
            case self::MODE_RESET:
                $this->resetStepManager->goToStep($stepIndex);
            break;
            default:
                parent::goToStep($stepIndex);
            break;
        }
    }

    protected function finish(): array
    {
        switch ($this->getMode()) {
            case self::MODE_RESET:
                $this->resetStepManager->finish();
                $messageParameters = Util::messagesToToastrCallbacksParameters($this->resetStepManager->getCurrentStep()->getMessages());
                foreach ($messageParameters as $singleMessageParameters) {
                    $callbacks[] = $this->callback('toastrDisplay', $singleMessageParameters);
                }

                $callbacks[] = $this->callback('refreshBlock');
                $this->setMode(self::MODE_INSTALL);
                $this->configurationStepManager->setCurrentStepIndex(0);
                $arrResponse = ['status' => 'success', 'msg' => '', 'callbacks' => $callbacks];
            break;
            default:
                return parent::finish();
            break;
        }

        return $arrResponse;
    }

    protected function save(): void
    {
        switch ($this->getMode()) {
            case self::MODE_RESET:
                $this->resetStepManager->save();
            break;
            default:
                parent::save();
            break;
        }
    }

    protected function parseSteps()
    {
        switch ($this->getMode()) {
            case self::MODE_RESET:
                return $this->configurationStepManager->parseSteps();
            break;
            default:
                parent::parseSteps();
            break;
        }
    }
}
