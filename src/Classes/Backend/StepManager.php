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

namespace WEM\SmartgearBundle\Classes\Backend;

use Contao\FrontendTemplate;
use Contao\System;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Backend\AbstractStep as Step;

class StepManager
{
    use Traits\ActionsTrait;
    /** @var TranslatorInterface */
    protected $translator;
    /** @var array */
    protected $steps = [];
    /** @var string */
    protected $module = '';
    /** @var string */
    protected $type = '';
    /** @var string */
    protected $stepSessionKey = '';

    protected $strStepsTemplate = 'be_wem_sg_install_steps';
    protected $objSession;

    public function __construct(
        TranslatorInterface $translator,
        string $module,
        string $type,
        string $stepSessionKey,
        array $steps
    ) {
        $this->translator = $translator;
        $this->module = $module;
        $this->type = $type;
        $this->steps = $steps;
        $this->stepSessionKey = $stepSessionKey;
        // Init session
        $this->objSession = System::getContainer()->get('session');
    }

    public function parse()
    {
        // get the current step
        // call its "getFilledTemplate" method
        // add some actions (previous / reset / save / next )
        // and render it
        $currentStep = $this->getCurrentStep();

        $objTemplate = $currentStep->getFilledTemplate();

        $this->fillActions();
        $objTemplate->actions = $this->formatActions($this->actions);

        return $objTemplate->parse();
    }

    public function parseSteps()
    {
        $objTemplate = new FrontendTemplate($this->strStepsTemplate);

        $arrSteps = [];
        foreach ($this->steps as $index => $step) {
            $arrSteps[] = [
                'active' => $index === $this->getCurrentStepIndex(),
                'number' => $index + 1,
                'index' => $index,
                'label' => $step->getTitle(),
                // 'href' => ''
                'type' => $this->type,
                'name' => $this->module,
            ];
        }
        $objTemplate->steps = $arrSteps;

        return $objTemplate->parse();
    }

    public function goToNextStep(): void
    {
        // get the step manager
        // check the form validity
        // do what the step does
        // go to next step
        if (!$this->getCurrentStep()->isStepValid()) {
            throw new Exception($this->translator->trans('WEM.SMARTGEAR.DEFAULT.InvalidForm', [], 'contao_default'));
        }
        $this->getCurrentStep()->do();
        $this->setCurrentStepIndex($this->getNextStepIndex());
    }

    public function finish(): void
    {
        $this->save();
    }

    public function save(): void
    {
        if (!$this->getCurrentStep()->isStepValid()) {
            throw new Exception($this->translator->trans('WEM.SMARTGEAR.DEFAULT.InvalidForm', [], 'contao_default'));
        }
        $this->getCurrentStep()->do();
    }

    public function goToStep(int $stepIndex): void
    {
        $this->setCurrentStepIndex($stepIndex);
    }

    public function goToPreviousStep(): void
    {
        // if (!$this->getCurrentStep()->isStepValid()) {
        //     throw new Exception('Formulaire invalide');
        // }
        // $this->getCurrentStep()->do();
        $this->setCurrentStepIndex($this->getPreviousStepIndex());
    }

    public function setCurrentStepIndex(int $index): void
    {
        $this->objSession->set($this->getStepSessionKey(), $index);
    }

    public function getCurrentStepIndex(): int
    {
        $index = $this->objSession->get($this->getStepSessionKey()) ?? 0;
        if (0 > $index) {
            $index = 0;
        }

        return $index;
    }

    public function getNextStepIndex(): int
    {
        $index = $this->getCurrentStepIndex() + 1;

        if ($index > \count($this->steps) - 1 || 0 > $index) {
            throw new Exception($this->translator->trans('WEMSG.STEPMANAGER.ERRORS.nextStepIsOutOfBounds', [], 'contao_default'));
        }

        return $index;
    }

    public function getPreviousStepIndex(): int
    {
        $index = $this->getCurrentStepIndex() - 1;

        if ($index > \count($this->steps) - 1 || 0 > $index) {
            throw new Exception($this->translator->trans('WEMSG.STEPMANAGER.ERRORS.previousStepIsOutOfBounds', [], 'contao_default'));
        }

        return $index;
    }

    public function getCurrentStep(): Step
    {
        return $this->steps[$this->getCurrentStepIndex()];
    }

    public function getStepSessionKey(): string
    {
        return $this->stepSessionKey;
    }

    public function setMode(string $mode): self
    {
        $this->mode = $mode;

        return $this;
    }

    protected function fillActions(): void
    {
        if (0 !== $this->getCurrentStepIndex()) {
            $this->actions[] = ['action' => 'previous', 'label' => $this->translator->trans('WEM.SMARTGEAR.DEFAULT.PreviousStep', [], 'contao_default')];
        }

        if ($this->getCurrentStepIndex() < \count($this->steps) - 1) {
            $this->actions[] = ['action' => 'next', 'label' => $this->translator->trans('WEM.SMARTGEAR.DEFAULT.NextStep', [], 'contao_default')];
        } else {
            $this->actions[] = ['action' => 'finish', 'label' => $this->translator->trans('WEM.SMARTGEAR.DEFAULT.Finish', [], 'contao_default')];
        }
    }
}
