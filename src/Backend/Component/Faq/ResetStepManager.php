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

namespace WEM\SmartgearBundle\Backend\Component\Faq;

use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Backend\StepManager;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;

class ResetStepManager extends StepManager
{
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
        $this->setInstallAsIncomplete();
    }

    public function setInstallAsIncomplete(): void
    {
        $config = $this->configurationManager->load();
        $faqConfig = $config->getSgFaq();
        $faqConfig->setSgInstallComplete(false);

        $config->setSgFaq($faqConfig);
        $this->configurationManager->save($config);
    }

    protected function fillActions(): void
    {
        $this->actions[] = ['action' => 'reset_mode_check_cancel', 'label' => $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['Cancel']];

        if (0 !== $this->getCurrentStepIndex()) {
            $this->actions[] = ['action' => 'previous', 'label' => $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['PreviousStep']];
        }

        if ($this->getCurrentStepIndex() < \count($this->steps) - 1) {
            $this->actions[] = ['action' => 'next', 'label' => $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NextStep']];
        } else {
            $this->actions[] = ['action' => 'finish', 'label' => $GLOBALS['TL_LANG']['WEMSG']['CORE']['RESETSTEPMANAGER']['buttonTerminateLabel']];
        }
    }
}
