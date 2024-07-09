<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2023 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

namespace WEM\SmartgearBundle\Backend\Component\Blog;

use Contao\CoreBundle\Csrf\ContaoCsrfTokenManager;
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
    public const MODE_RESET = 'check_reset';

    protected string $type = 'component';

    protected string $module = 'blog';

    protected string $icon = 'exclamation-triangle';

    protected string $title = 'Blog';

    protected ContaoCsrfTokenManager $contaoCsrfTokenManager;

    public function __construct(
        TranslatorInterface        $translator,
        ConfigurationManager       $configurationManager,
        ConfigurationStepManager   $configurationStepManager,
        protected ResetStepManager $resetStepManager,
        Dashboard                  $dashboard
    ) {
        $this->contaoCsrfTokenManager = System::getContainer()->getParameter('contao.csrf.token_manager');
        parent::__construct($configurationManager, $configurationStepManager, $dashboard, $translator);
    }

    public function processAjaxRequest(): void
    {
        try {
            switch (Input::post('action')) {
                case 'blogPresetAdd':
                    try {
                        $generalConfigurationStep = System::getContainer()->get('smartgear.backend.component.blog.configuration_step.general');
                        $newPresetIndex = $generalConfigurationStep->presetAdd();
                        $arrResponse['status'] = 'success';
                        $arrResponse['msg'] = $GLOBALS['TL_LANG']['WEMSG']['BLOG']['BLOCK']['blogNewsConfigAddAjaxMessageSuccess'];
                        $arrResponse['index'] = $newPresetIndex;
                    } catch (Exception $e) {
                        $arrResponse['status'] = 'error';
                        $arrResponse['msg'] = $GLOBALS['TL_LANG']['WEMSG']['BLOG']['BLOCK']['blogNewsConfigAddAjaxMessageError'].$e->getMessage();
                        $arrResponse['output'] = $e->getMessage();
                    }

                break;
                case 'blogPresetGet':
                    try {
                        $generalConfigurationStep = System::getContainer()->get('smartgear.backend.component.blog.configuration_step.general');
                        $config = $generalConfigurationStep->presetGet((int) Input::post('id'));
                        $arrResponse['status'] = 'success';
                        $arrResponse['msg'] = $GLOBALS['TL_LANG']['WEMSG']['BLOG']['BLOCK']['blogNewsConfigGetAjaxMessageSuccess'];
                        $arrResponse['config'] = null !== $config ? $config->export() : null;
                    } catch (Exception $e) {
                        $arrResponse['status'] = 'error';
                        $arrResponse['msg'] = $GLOBALS['TL_LANG']['WEMSG']['BLOG']['BLOCK']['blogNewsConfigGetAjaxMessageError'].$e->getMessage();
                        // $arrResponse['output'] = $e->getMessage();
                    }

                break;
                case 'reset_mode':
                    $this->setMode(self::MODE_RESET);
                    $this->resetStepManager->goToStep(0);
                    $arrResponse = ['status' => 'success', 'msg' => '', 'callbacks' => [$this->callback('refreshBlock')]];
                break;
                case 'reset_mode_check_cancel':
                case 'back_to_dashboard':
                    $this->setMode(self::MODE_DASHBOARD);
                    $content = $this->parse();
                    $arrResponse = ['status' => 'success', 'msg' => '', 'callbacks' => [$this->callback('replaceBlockContent', [$content])]];
                break;
                default:
                    parent::processAjaxRequest();
                break;
            }
        } catch (Exception $exception) {
            $arrResponse = ['status' => 'error', 'msg' => $exception->getMessage(), 'trace' => $exception->getTrace()];
        }

        // Add Request Token to JSON answer and return
        $arrResponse['rt'] = $this->contaoCsrfTokenManager->getDefaultTokenValue();
        echo json_encode($arrResponse);
        exit;
    }

    public function isInstalled(): bool
    {
        $config = $this->configurationManager->load();

        return (bool) $config->getSgBlog()->getSgInstallComplete();
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
        match ($this->getMode()) {
            self::MODE_RESET => $this->resetStepManager->goToNextStep(),
            default => parent::goToNextStep(),
        };
    }

    protected function goToPreviousStep(): void
    {
        match ($this->getMode()) {
            self::MODE_RESET => $this->resetStepManager->goToPreviousStep(),
            default => parent::goToPreviousStep(),
        };
    }

    protected function goToStep(int $stepIndex): void
    {
        match ($this->getMode()) {
            self::MODE_RESET => $this->resetStepManager->goToStep($stepIndex),
            default => parent::goToStep($stepIndex),
        };
    }

    protected function finish(): array
    {
        $i = $this->getMode();
        if ($i === self::MODE_RESET) {
            $this->resetStepManager->finish();
            $messageParameters = Util::messagesToToastrCallbacksParameters($this->resetStepManager->getCurrentStep()->getMessages());
            foreach ($messageParameters as $singleMessageParameters) {
                $callbacks[] = $this->callback('toastrDisplay', $singleMessageParameters);
            }

            $callbacks[] = $this->callback('refreshBlock');
            $this->setMode(self::MODE_INSTALL);
            $this->configurationStepManager->setCurrentStepIndex(0);
            $arrResponse = ['status' => 'success', 'msg' => '', 'callbacks' => $callbacks];
        } else {
            return parent::finish();
        }

        return $arrResponse;
    }

    protected function save(): void
    {
        match ($this->getMode()) {
            self::MODE_RESET => $this->resetStepManager->save(),
            default => parent::save(),
        };
    }

    protected function parseSteps(): ?string
    {
        $i = $this->getMode();
        if ($i === self::MODE_RESET) {
            return $this->configurationStepManager->parseSteps();
        } else {
            parent::parseSteps();
        }

        return null;
    }
}
