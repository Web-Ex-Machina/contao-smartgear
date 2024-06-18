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

class CompileFormFieldsListener
{
    public function __construct(
        protected TranslatorInterface $translator,
        protected CoreConfigurationManager $configurationManager,
        protected array $listeners)
    {
    }

    public function __invoke(
        array $arrFields,
        string $formId,
        Form $form
    ): array {
        $GLOBALS['TL_JAVASCRIPT']['sg_formdatamanager'] = 'bundles/wemsmartgear/js/module/formdatamanager/frontend.js';

        return $this->applyListeners($arrFields, $formId, $form);
    }

    protected function applyListeners(
        array $arrFields,
        string $formId,
        Form $form
    ): array {
        foreach ($this->listeners as $listener) {
            $arrFields = $listener->__invoke($arrFields, $formId, $form);
        }

        return $arrFields;
    }
}
