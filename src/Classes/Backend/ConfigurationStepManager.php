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

namespace WEM\SmartgearBundle\Classes\Backend;

use Contao\FrontendTemplate;
use Contao\System;
use Exception;
use WEM\SmartgearBundle\Classes\Backend\ConfigurationStep as ConfigurationStep;
use WEM\SmartgearBundle\Classes\Config\Manager as ConfigurationManager;
use WEM\SmartgearBundle\Classes\Util;

class ConfigurationStepManager
{
    /** @var array */
    protected $steps = [];
    /** @var string */
    protected $module = '';
    /** @var string */
    protected $type = '';
    /** @var ConfigurationManager [description] */
    protected $configurationManager;
    /** @var array */
    protected $actions = [];

    protected $strStepsTemplate = 'be_wem_sg_install_steps';

    public function __construct(
        ConfigurationManager $configurationManager,
        string $module,
        string $type,
        array $steps
    ) {
        $this->configurationManager = $configurationManager;
        $this->module = $module;
        $this->type = $type;
        $this->steps = $steps;
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
        $this->actions = [
            ['action' => 'previous', 'label' => 'Précédent'],
            // ['action' => 'reset', 'label' => 'Réinitialiser'],
            // ['action' => 'save', 'label' => 'Sauvegarder'],
            ['action' => 'next', 'label' => 'Suivant'],
        ];

        $objTemplate->actions = Util::formatActions($this->actions);

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
                'label' => $step->getTitle(),
                // 'href' => '',
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
            throw new Exception('Formulaire invalide');
        }
        $this->getCurrentStep()->do();
        $this->setCurrentStepIndex($this->getNextStepIndex());
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
        $this->objSession->set($this->getInstallStepSessionKey(), $index);
    }

    public function getCurrentStepIndex(): int
    {
        $index = $this->objSession->get($this->getInstallStepSessionKey()) ?? 0;
        if (0 > $index) {
            $index = 0;
        }

        return $index;
    }

    public function getNextStepIndex(): int
    {
        $index = $this->getCurrentStepIndex() + 1;

        if ($index > \count($this->steps) - 1 || 0 > $index) {
            throw new Exception('Next step is out of bounds');
        }

        return $index;
    }

    public function getPreviousStepIndex(): int
    {
        $index = $this->getCurrentStepIndex() - 1;

        if ($index > \count($this->steps) - 1 || 0 > $index) {
            throw new Exception('Previous step is out of bounds');
        }

        return $index;
    }

    public function getCurrentStep(): ConfigurationStep
    {
        return $this->steps[$this->getCurrentStepIndex()];
    }

    public function getActions(): array
    {
        return $this->actions;
    }

    public function getInstallStepSessionKey(): string
    {
        return 'sg_'.$this->module.'_install_step';
    }
}
