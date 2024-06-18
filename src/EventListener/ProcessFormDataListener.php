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

namespace WEM\SmartgearBundle\EventListener;

use Contao\Form;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;

class ProcessFormDataListener
{


    public function __construct(
        protected TranslatorInterface $translator,
        protected CoreConfigurationManager $configurationManager,
        protected array $listeners)
    {
    }

    public function __invoke(
        array $submittedData,
        array $formData,
        ?array $files,
        array $labels,
        Form $form
    ): void {
        $this->applyListeners($submittedData, $formData, $files, $labels, $form);
    }

    protected function applyListeners(
        array $submittedData,
        array $formData,
        ?array $files,
        array $labels,
        Form $form): void
    {
        foreach ($this->listeners as $listener) {
            $listener->__invoke($submittedData, $formData, $files, $labels, $form);
        }
    }
}
