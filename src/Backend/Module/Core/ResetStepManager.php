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

namespace WEM\SmartgearBundle\Backend\Module\Core;

use WEM\SmartgearBundle\Classes\Backend\StepManager;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;

class ResetStepManager extends StepManager
{
    /** @var ConfigurationManager */
    protected $configurationManager;

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
        $this->setInstallAsIncomplete();
    }

    public function setInstallAsIncomplete(): void
    {
        $config = $this->configurationManager->load();
        $config->setSgInstallComplete(false);
        $this->configurationManager->save($config);
    }

    protected function fillActions(): void
    {
        $this->actions[] = ['action' => 'reset_mode_check_cancel', 'label' => 'Annuler'];

        if (0 !== $this->getCurrentStepIndex()) {
            $this->actions[] = ['action' => 'previous', 'label' => 'Précédent'];
        }

        if ($this->getCurrentStepIndex() < \count($this->steps) - 1) {
            $this->actions[] = ['action' => 'next', 'label' => 'Suivant'];
        } else {
            $this->actions[] = ['action' => 'finish', 'label' => 'Terminer la réinitialisation'];
        }
    }
}
