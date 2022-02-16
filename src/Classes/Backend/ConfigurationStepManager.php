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

use Contao\System;
use WEM\SmartgearBundle\Classes\Config\Manager as ConfigurationManager;

class ConfigurationStepManager
{
    /** @var array */
    protected $steps = [];
    /** @var string */
    protected $module = '';
    /** @var ConfigurationManager [description] */
    protected $configurationManager;

    public function __construct(
        ConfigurationManager $configurationManager,
        string $module,
        array $steps
    ) {
        $this->configurationManager = $configurationManager;
        $this->module = $module;
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
        $objTemplate->actions = [];

        return $objTemplate->parse();
    }

    public function setCurrentStep(): void
    {
    }

    public function getCurrentStep()
    {
        // @todo : manage that correctly
        return $this->steps[0];
    }
}
