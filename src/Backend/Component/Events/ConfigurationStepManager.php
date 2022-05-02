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

namespace WEM\SmartgearBundle\Backend\Component\Events;

use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Backend\ConfigurationStepManager as ConfigurationStepManagerBase;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;

class ConfigurationStepManager extends ConfigurationStepManagerBase
{
    /** @var ConfigurationManager */
    protected $configurationManager;

    public function __construct(
        ConfigurationManager $configurationManager,
        TranslatorInterface $translator,
        string $module,
        string $type,
        string $stepSessionKey,
        array $steps
    ) {
        parent::__construct($configurationManager, $translator, $module, $type, $stepSessionKey, $steps);
    }

    public function setInstallAsComplete(): void
    {
        $config = $this->configurationManager->load();
        $blogConfig = $config->getSgEvents();
        $blogConfig->setSgInstallComplete(true);
        $config->setSgEvents($blogConfig);
        $this->configurationManager->save($config);
    }
}
