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

use Contao\Environment;
use Contao\FrontendTemplate;
use Contao\RequestToken;

class ConfigurationStep
{
    protected $strTemplate = 'be_wem_sg_install_block_configuration_step';
    protected $title = '';
    /** @var string */
    protected $module = '';
    /**
     * Generic array of messages.
     *
     * @var array
     */
    protected $messages = [];

    /**
     * Generic array of fields.
     *
     * @var array
     */
    protected $fields = [];

    public function __construct(
        string $module
    ) {
        $this->module = $module;
    }

    public function getFilledTemplate(): FrontendTemplate
    {
        // to render the step
        $objTemplate = new FrontendTemplate($this->strTemplate);
        $objTemplate->request = Environment::get('request');
        $objTemplate->token = RequestToken::get();
        $objTemplate->module = $this->module;
        $objTemplate->title = $this->title;
        $objTemplate->fields = $this->fields;
        // Always add messages
        $objTemplate->messages = $this->messages;

        // And return the template, parsed.
        return $objTemplate;
    }

    public function isStepValid(): bool
    {
        // check if the step is correct
        return false;
    }

    public function do(): void
    {
        // do what is meant to be done in this step
    }

    /**
     * Add a text field.
     */
    protected function addTextField($strName, $strLabel, $strValue = '', $blnRequired = false, $strClass = '', $strType = 'text', $strPlaceholder = ''): void
    {
        $this->fields[] = [
            'type' => $strType,
            'name' => $strName,
            'label' => $strLabel,
            'placeholder' => $strPlaceholder,
            'value' => $strValue,
            'required' => $blnRequired,
            'class' => $strClass,
        ];
    }

    /**
     * Add a dropdown/checkbox/radio.
     */
    protected function addSelectField($strName, $strLabel, $arrOptions, $strValue = '', $blnRequired = false, $strClass = '', $strType = 'select'): void
    {
        foreach ($arrOptions as &$o) {
            $o['selected'] = false;
            if ($strValue === $o['value']) {
                $o['selected'] = true;
            }
        }

        $this->fields[] = [
            'type' => $strType,
            'name' => $strName,
            'label' => $strLabel,
            'options' => $arrOptions,
            'required' => $blnRequired,
            'class' => $strClass,
        ];
    }

    protected function checkValue($strName): void
    {
        // Load config
        if (\Input::post($strName)) {
            $this->$strName = \Input::post($strName);
            $this->objSession->set('sg_install_'.$strName, $this->$strName);
        } elseif ($this->objSession->get('sg_install_'.$strName)) {
            $this->$strName = $this->objSession->get('sg_install_'.$strName);
        } elseif ($this->sgConfig[$strName]) {
            $this->$strName = $this->sgConfig[$strName];
        }
    }
}
