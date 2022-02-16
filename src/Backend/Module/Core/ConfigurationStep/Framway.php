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

namespace WEM\SmartgearBundle\Backend\Module\Core\ConfigurationStep;

use Contao\Input;
use WEM\SmartgearBundle\Classes\Backend\ConfigurationStep;
use WEM\SmartgearBundle\Classes\Config\Manager as ConfigurationManager;

class Framway extends ConfigurationStep
{
    /** @var ConfigurationManager */
    protected $configurationManager;

    public function __construct(
        string $module,
        string $type,
        ConfigurationManager $configurationManager
    ) {
        parent::__construct($module, $type);
        $this->title = 'Framway';
        $this->configurationManager = $configurationManager;
        $this->addTextField('titi', 'The titiiiiiii !!!');
    }

    public function isStepValid(): bool
    {
        // check if the step is correct
        if (!empty(Input::post('titi'))) {
            return true;
        }

        return false;
    }

    public function do(): void
    {
        // do what is meant to be done in this step
    }
}
