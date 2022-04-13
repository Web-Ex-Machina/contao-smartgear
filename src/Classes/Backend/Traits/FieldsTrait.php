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

namespace WEM\SmartgearBundle\Classes\Backend\Traits;

use WEM\SmartgearBundle\Widget\SimpleFileTree;

trait FieldsTrait
{
    /** @var array */
    protected $fields = [];

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
     * Add a checkbox field.
     */
    protected function addCheckboxField(string $strName, string $strLabel, ?string $strValue = '', ?bool $blnChecked = false, ?bool $blnRequired = false, ?string $strClass = '', ?string $strPlaceholder = '', ?string $strHelp = ''): void
    {
        $this->fields[$strName] = [
            'type' => 'checkbox',
            'name' => $strName,
            'label' => $strLabel,
            'placeholder' => $strPlaceholder,
            'value' => $strValue,
            'required' => $blnRequired,
            'class' => $strClass,
            'help' => $strHelp,
            'checked' => $blnChecked,
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

    protected function addSimpleFileTree(string $strName, string $strLabel, ?string $strValue = null, ?bool $blnRequired = false, ?bool $blnMultiple = false, ?string $strClass = '', ?string $strHelp = '', ?array $arrAttributes = []): void
    {
        // okay, we'd need to pass the file/folder UUID as varValue ...
        // $item = \Contao\FilesModel::findByPath($strValue);
        // dump($item);
        $config = [
            'strName' => $strName,
            'strId' => $strName,
            'strLabel' => $strLabel,
            // 'varValue' => null !== $item ? $item->uuid : $strValue,
            // 'value' => $strValue,
            'varValue' => $strValue,
            'required' => $blnRequired,
            'multiple' => $blnMultiple,
        ];
        if (!empty($strClass)) {
            $config['strClass'] = $strClass;
        }
        $field = new SimpleFileTree(array_merge($arrAttributes, $config));

        $this->fields[$strName] = [
            'type' => 'widget',
            'name' => $strName,
            'label' => $strLabel,
            'required' => $blnRequired,
            // 'multiple' => $blnMultiple,
            'class' => $strClass,
            'help' => $strHelp,
            'objField' => $field,
        ];
    }
}
