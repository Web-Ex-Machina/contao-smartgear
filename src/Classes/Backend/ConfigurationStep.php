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
use Contao\Input;
use Contao\RequestToken;

class ConfigurationStep
{
    protected $strTemplate = 'be_wem_sg_install_block_configuration_step';
    protected $title = '';
    protected $type = '';
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
        string $module,
        string $type
    ) {
        $this->module = $module;
        $this->type = $type;
    }

    public function getFilledTemplate(): FrontendTemplate
    {
        // to render the step
        $objTemplate = new FrontendTemplate($this->strTemplate);
        $objTemplate->request = Environment::get('request');
        $objTemplate->token = RequestToken::get();
        $objTemplate->module = $this->module;
        $objTemplate->type = $this->type;
        $objTemplate->title = $this->title;
        $objTemplate->fields = $this->fields;
        // Always add messages
        $objTemplate->messages = $this->messages;
        $objTemplate->actions = [];

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

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Add a text field.
     */
    protected function addTextField(string $strName, string $strLabel, ?string $strValue = '', ?bool $blnRequired = false, ?string $strClass = '', ?string $strType = 'text', ?string $strPlaceholder = '', ?string $strHelp = ''): void
    {
        $this->fields[$strName] = [
            'type' => $strType,
            'name' => $strName,
            'label' => $strLabel,
            'placeholder' => $strPlaceholder,
            'value' => $strValue,
            'required' => $blnRequired,
            'class' => $strClass,
            'help' => $strHelp,
        ];
    }

    /**
     * Add a text field.
     */
    protected function addFileField(string $strName, string $strLabel, ?bool $blnRequired = false, ?string $accept = '', ?string $strClass = '', ?string $strPlaceholder = '', ?string $strHelp = ''): void
    {
        $this->fields[$strName] = [
            'type' => 'file',
            'name' => $strName,
            'label' => $strLabel,
            'placeholder' => $strPlaceholder,
            'required' => $blnRequired,
            'class' => $strClass,
            'help' => $strHelp,
            'accept' => $accept,
        ];
    }

    /**
     * Add a dropdown/checkbox/radio.
     */
    protected function addSelectField(string $strName, string $strLabel, array $arrOptions, $strValue = '', ?bool $blnRequired = false, ?bool $blnMultiple = false, ?string $strClass = '', ?string $strType = 'select', ?string $strHelp = ''): void
    {
        if (\is_array($strValue)) {
            foreach ($arrOptions as &$o) {
                $o['selected'] = false;
                if (\in_array($o['value'], $strValue, true)) {
                    $o['selected'] = true;
                }
            }
        } else {
            foreach ($arrOptions as &$o) {
                $o['selected'] = false;
                if ($strValue === $o['value']) {
                    $o['selected'] = true;
                }
            }
        }

        $this->fields[$strName] = [
            'type' => $strType,
            'name' => $strName,
            'label' => $strLabel,
            'options' => $arrOptions,
            'required' => $blnRequired,
            'multiple' => $blnMultiple,
            'class' => $strClass,
            'help' => $strHelp,
        ];
    }

    protected function checkValue($strName): void
    {
        // Load config
        if (Input::post($strName)) {
            $this->$strName = Input::post($strName);
            $this->objSession->set('sg_install_'.$strName, $this->$strName);
        } elseif ($this->objSession->get('sg_install_'.$strName)) {
            $this->$strName = $this->objSession->get('sg_install_'.$strName);
        } elseif ($this->sgConfig[$strName]) {
            $this->$strName = $this->sgConfig[$strName];
        }
    }
}
