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

use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;

class ConfigurationStepManager extends StepManager
{
    public const MODE_INSTALL = 'install';
    public const MODE_CONFIGURE = 'configure';
    /** @var ConfigurationManager */
    protected $configurationManager;
    /** @var string */
    protected $mode = '';

    public function __construct(
        ConfigurationManager $configurationManager,
        string $module,
        string $type,
        string $stepSessionKey,
        array $steps
    ) {
        parent::__construct($module, $type, $stepSessionKey, $steps);
        $this->configurationManager = $configurationManager;
    }

    public function finish(): void
    {
        parent::finish();
        $this->setInstallAsComplete();
    }

    public function setMode(string $mode): self
    {
        $this->mode = $mode;

        return $this;
    }

    public function setInstallAsComplete(): void
    {
        $config = $this->configurationManager->load();
        $config->setSgInstallComplete(true);
        $this->configurationManager->save($config);
    }

    public function getCurrentStep(): ConfigurationStep
    {
        return parent::getCurrentStep();
    }

    protected function fillActions(): void
    {
        if (0 !== $this->getCurrentStepIndex()) {
            $this->actions[] = ['action' => 'previous', 'label' => 'Précédent'];
        }

        if (self::MODE_CONFIGURE === $this->mode) {
            $this->actions[] = ['action' => 'save', 'label' => 'Enregistrer'];
        }

        if ($this->getCurrentStepIndex() < \count($this->steps) - 1) {
            $this->actions[] = ['action' => 'next', 'label' => 'Suivant'];
        } else {
            $this->actions[] = ['action' => 'finish', 'label' => 'Terminer la configuration'];
        }
    }
}
