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

namespace WEM\SmartgearBundle\Backend\Module\FormDataManager;

use Contao\FrontendTemplate;
use Contao\Input;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Backend\Block as BackendBlock;
use WEM\SmartgearBundle\Classes\Backend\ConfigurationStepManager;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\Util;

class Block extends BackendBlock
{
    public const MODE_RESET = 'check_reset';
    protected $type = 'module';
    protected $module = 'form_data_manager';
    protected $icon = 'exclamation-triangle';
    protected $title = 'FormDataManager';

    protected $resetStepManager;

    public function __construct(
        TranslatorInterface $translator,
        ConfigurationManager $configurationManager,
        ConfigurationStepManager $configurationStepManager,
        ResetStepManager $resetStepManager,
        Dashboard $dashboard
    ) {
        parent::__construct($configurationManager, $configurationStepManager, $dashboard, $translator);
        $this->resetStepManager = $resetStepManager;
    }

    public function processAjaxRequest(): void
    {
        try {
            switch (Input::post('action')) {
                case 'reset_mode':
                    $this->setMode(self::MODE_RESET);
                    $this->resetStepManager->goToStep(0);
                    $arrResponse = ['status' => 'success', 'msg' => '', 'callbacks' => [$this->callback('refreshBlock')]];
                break;
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

        // Add Request Token to JSON answer and return
        $arrResponse['rt'] = \Contao\RequestToken::get();
        echo json_encode($arrResponse);
        exit;
    }

    public function isInstalled(): bool
    {
        $config = $this->configurationManager->load();

        return (bool) $config->getSgFormDataManager()->getSgInstallComplete();
    }

    protected function parseDependingOnMode(FrontendTemplate $objTemplate): FrontendTemplate
    {
        switch ($this->getMode()) {
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
