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

namespace WEM\SmartgearBundle\Classes\Dca;

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Exception;

class Manipulator
{
    protected $table;

    public function getTable(): ?string
    {
        return $this->table;
    }

    public function setTable(?string $table): self
    {
        $this->table = $table;

        return $this;
    }

    public static function create(string $table)
    {
        return (new self())->setTable($table);
    }

    public function addCtable(string $table)
    {
        $this->checkConfiguration();
        $GLOBALS['TL_DCA'][$this->table]['config']['ctable'][] = $table;

        return $this;
    }

    public function addCtables(array $tables)
    {
        $this->checkConfiguration();
        foreach ($tables as $table) {
            $this->addCtable($table);
        }

        return $this;
    }

    public function removeCtable(string $table)
    {
        $this->checkConfiguration();
        unset($GLOBALS['TL_DCA'][$this->table]['config']['ctable'][$table]);

        return $this;
    }

    public function removeCtables(array $tables)
    {
        $this->checkConfiguration();
        foreach ($tables as $table) {
            $this->removeCtable($table);
        }

        return $this;
    }

    public function addConfigOnloadCallback(string $className, string $functionName): self
    {
        $this->checkConfiguration();
        $GLOBALS['TL_DCA'][$this->table]['config']['onload_callback'][] = [$className, $functionName];

        return $this;
    }

    public function addConfigOnsubmitCallback(string $className, string $functionName): self
    {
        $this->checkConfiguration();
        $GLOBALS['TL_DCA'][$this->table]['config']['onsubmit_callback'][] = [$className, $functionName];

        return $this;
    }

    public function setListOperationsDeleteButtonCallback(string $className, string $functionName): self
    {
        $this->checkConfiguration();
        $GLOBALS['TL_DCA'][$this->table]['list']['operations']['delete']['button_callback'] = [$className, $functionName];

        return $this;
    }

    public function setListOperationsEditheaderButtonCallback(string $className, string $functionName): self
    {
        $this->checkConfiguration();
        $GLOBALS['TL_DCA'][$this->table]['list']['operations']['editheader']['button_callback'] = [$className, $functionName];

        return $this;
    }

    public function addListLabelLabelCallback(string $className, string $functionName): self
    {
        $this->checkConfiguration();
        $GLOBALS['TL_DCA'][$this->table]['list']['label']['label_callback'] = [$className, $functionName];

        return $this;
    }

    /**
     * Set the singleSRC field's path.
     *
     * @param string $path The new path
     */
    public function setFieldSingleSRCPath(string $path): self
    {
        $this->checkConfiguration();
        $this->setFieldEvalProperty('singleSRC', 'path', $path);

        return $this;
    }

    /**
     * Set the source field's option callback.
     *
     * @param string $className    The class name containing the function
     * @param string $functionName The function name
     */
    public function setFieldSourceOptionCallback(string $className, string $functionName): self
    {
        $this->checkConfiguration();
        $this->setFieldProperty('source', 'options_callback', [$className, $functionName]);

        return $this;
    }

    /**
     * Removes all fields not in $fieldsKeyTokeep from the DCA.
     *
     * @param array $fieldsKeyToKeep The fields to keep
     */
    public function removeOtherFields(array $fieldsKeyToKeep): self
    {
        $this->checkConfiguration();
        //get rid of all unnecessary fields
        $fieldsKeyToRemove = array_diff(array_keys($GLOBALS['TL_DCA'][$this->table]['fields']), $fieldsKeyToKeep);
        $this->removeFields($fieldsKeyToRemove);

        return $this;
    }

    /**
     * Removes all fields in $fieldsKey from the DCA.
     */
    public function removeFields(array $fieldsKey): self
    {
        $this->checkConfiguration();
        //get rid of all unnecessary fields
        $palettesNames = array_keys($GLOBALS['TL_DCA'][$this->table]['palettes']);
        $subpalettesNames = array_keys($GLOBALS['TL_DCA'][$this->table]['subpalettes']);
        $pm = PaletteManipulator::create();
        foreach ($fieldsKey as $field) {
            $pm->removeField($field);
        }
        foreach ($palettesNames as $paletteName) {
            if (!\is_array($GLOBALS['TL_DCA'][$this->table]['palettes'][$paletteName])) {
                $pm->applyToPalette($paletteName, $this->table);
            }
        }
        foreach ($subpalettesNames as $subpaletteName) {
            if (!\is_array($GLOBALS['TL_DCA'][$this->table]['subpalettes'][$subpaletteName])) {
                $pm->applyToSubpalette($subpaletteName, $this->table);
            }
        }

        return $this;
    }

    /**
     * Set the alias field's readonly value.
     *
     * @param bool|bool $readOnly The readonly value
     */
    public function setFieldAliasReadonly(?bool $readOnly = true): self
    {
        $this->checkConfiguration();
        self::setFieldReadonly('alias', $readOnly);

        return $this;
    }

    /**
     * Set a field's readonly value.
     *
     * @param bool|bool $readOnly The readonly value
     */
    public function setFieldReadonly(string $field, ?bool $readOnly = true): self
    {
        $this->checkConfiguration();
        $this->setFieldProperty($field, 'readonly', $readOnly);

        return $this;
    }

    /**
     * Remove the "edit" operation in list.
     */
    public function removeListOperationsEdit(): self
    {
        $this->checkConfiguration();
        unset($GLOBALS['TL_DCA'][$this->table]['list']['operations']['edit']);

        return $this;
    }

    public function removeListOperation(string $key): self
    {
        $this->checkConfiguration();
        unset($GLOBALS['TL_DCA'][$this->table]['list']['operations'][$key]);

        return $this;
    }

    public function removeListOperations(array $keys): self
    {
        $this->checkConfiguration();
        foreach ($keys as $key) {
            $this->removeListOperation($key);
        }

        return $this;
    }

    public function addListOperation(string $key, array $options): self
    {
        $this->checkConfiguration();
        $GLOBALS['TL_DCA'][$this->table]['list']['operations'][$key] = $options;

        return $this;
    }

    public function addListOperations(array $operations): self
    {
        $this->checkConfiguration();
        foreach ($operations as $key => $options) {
            $this->addListOperation($key, $options);
        }

        return $this;
    }

    // public function getListOperation(string $key): ?array
    // {
    //     $this->checkConfiguration();

    //     return \array_key_exists($key, $GLOBALS['TL_DCA'][$this->table]['list']['operations']) ? $GLOBALS['TL_DCA'][$this->table]['list']['operations'][$key] : null;
    // }

    public function setFieldProperty(string $field, string $property, $value)
    {
        $this->checkConfiguration();
        $GLOBALS['TL_DCA'][$this->table]['fields'][$field][$property] = $value;

        return $this;
    }

    public function setFieldEvalProperty(string $field, string $property, $value)
    {
        $this->checkConfiguration();
        $GLOBALS['TL_DCA'][$this->table]['fields'][$field]['eval'][$property] = $value;

        return $this;
    }

    public function addField(string $field, array $configuration)
    {
        $this->checkConfiguration();
        $GLOBALS['TL_DCA'][$this->table]['fields'][$field] = $configuration;

        return $this;
    }

    protected function checkConfiguration(): void
    {
        if (null === $this->table) {
            throw new Exception('No table defined. Please call `setTable` method before.');
        }
        if (!\array_key_exists($this->table, $GLOBALS['TL_DCA'])) {
            throw new Exception(sprintf('Table "%s" not found.', $this->table));
        }
    }
}
