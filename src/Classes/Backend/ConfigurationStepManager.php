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

use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;

class ConfigurationStepManager extends StepManager
{
    public const MODE_INSTALL = 'install';

    public const MODE_CONFIGURE = 'configure';

    public string $mode = '';

    public function __construct(
        protected ConfigurationManager $configurationManager,
        TranslatorInterface $translator,
        string $module,
        string $type,
        string $stepSessionKey,
        array $steps
    ) {
        parent::__construct($translator, $module, $type, $stepSessionKey, $steps);
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

    public function getCurrentStep(): AbstractStep
    {
        return parent::getCurrentStep();
    }

    protected function fillActions(): void
    {
        if (0 !== $this->getCurrentStepIndex()) {
            $this->actions[] = ['action' => 'previous', 'label' => $this->translator->trans('WEM.SMARTGEAR.DEFAULT.PreviousStep', [], 'contao_default')];
        }

        if (self::MODE_CONFIGURE === $this->mode) {
            $this->actions[] = ['action' => 'save', 'label' => $this->translator->trans('WEM.SMARTGEAR.DEFAULT.Save', [], 'contao_default')];
        }

        if ($this->getCurrentStepIndex() < \count($this->steps) - 1) {
            $this->actions[] = ['action' => 'next', 'label' => $this->translator->trans('WEM.SMARTGEAR.DEFAULT.NextStep', [], 'contao_default')];
            if (self::MODE_CONFIGURE === $this->mode) {
                $this->actions[] = ['action' => 'finish', 'label' => $this->translator->trans('WEMSG.CONFIGURATIONSTEPMANAGER.BUTTONS.finish', [], 'contao_default')];
            }
        } else {
            $this->actions[] = ['action' => 'finish', 'label' => $this->translator->trans('WEMSG.CONFIGURATIONSTEPMANAGER.BUTTONS.finish', [], 'contao_default')];
        }

        $this->actions[] = ['action' => 'dashboard', 'label' => $this->translator->trans('WEM.SMARTGEAR.DEFAULT.BackToDashboard', [], 'contao_default')];
    }
}
