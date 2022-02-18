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
use WEM\SmartgearBundle\Classes\Config\ManagerFramway as ConfigurationManagerFramway;
use WEM\SmartgearBundle\Config\Framway;

class FramwayConfiguration extends ConfigurationStep
{
    /** @var ConfigurationManager */
    protected $configurationManager;
    /** @var ConfigurationManagerFramway */
    protected $configurationManagerFramway;

    public function __construct(
        string $module,
        string $type,
        ConfigurationManager $configurationManager,
        ConfigurationManagerFramway $configurationManagerFramway
    ) {
        parent::__construct($module, $type);
        $this->title = 'Framway | Configuration';
        $this->configurationManager = $configurationManager;
        $this->configurationManagerFramway = $configurationManagerFramway;

        $config = $this->configurationManager->load();
        $framwayConfig = $this->configurationManagerFramway->load();

        $arrThemes = [];
        $arrComponents = [];

        if ($handle = opendir($config->getSgFramwayPath().\DIRECTORY_SEPARATOR.'src/themes')) {
            while (false !== ($entry = readdir($handle))) {
                if ('.' !== $entry && '..' !== $entry) {
                    $arrThemes[] = ['label' => $entry, 'value' => $entry];
                }
            }
            closedir($handle);
        }

        if ($handle = opendir($config->getSgFramwayPath().\DIRECTORY_SEPARATOR.'src/components')) {
            while (false !== ($entry = readdir($handle))) {
                if ('.' !== $entry && '..' !== $entry) {
                    $arrComponents[] = ['label' => $entry, 'value' => $entry];
                }
            }
            closedir($handle);
        }

        $arrFontAwesome = [
            [
                'label' => 'None', 'value' => Framway::USE_FA_NONE,
            ],
            [
                'label' => 'kit Smartgear', 'value' => Framway::USE_FA_FREE,
            ],
            [
                'label' => 'all', 'value' => Framway::USE_FA_PRO,
            ],
        ];

        $this->addSelectField('themes', 'Thèmes', $arrThemes, $framwayConfig->getThemes(), false, true);
        $this->addSelectField('components', 'Composants', $arrComponents, $framwayConfig->getComponents(), true, true);
        $this->addSelectField('fontawesome', 'Configuration Font-Awesome', $arrFontAwesome, $framwayConfig->getUseFA(), false);
    }

    public function isStepValid(): bool
    {
        // check if the step is correct
        // if (empty(Input::post('themes'))) {
        //     return false;
        // }
        if (empty(Input::post('components'))) {
            return false;
        }
        // if (empty(Input::post('fontawesome'))) {
        //     return false;
        // }

        return true;
    }

    public function do(): void
    {
        // do what is meant to be done in this step
        $framwayConfig = $this->configurationManagerFramway->load();
        $framwayConfig->setThemes(Input::post('themes') ?? []);
        $framwayConfig->setComponents(Input::post('components'));
        $framwayConfig->setUseFA(Input::post('fontawesome') ?? Framway::USE_FA_NONE);
        $this->configurationManagerFramway->save($framwayConfig);
    }
}
