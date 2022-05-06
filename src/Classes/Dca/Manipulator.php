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

    /**
     * Set the singleSRC field's path.
     *
     * @param string $path The new path
     */
    public function setFieldSingleSRCPath(string $path): void
    {
        $this->checkConfiguration();
        $GLOBALS['TL_DCA'][$this->table]['fields']['singleSRC']['eval']['path'] = $path;
    }

    /**
     * Removes all fields not in $fieldsKeyTokeep from the DCA.
     *
     * @param array $fieldsKeyToKeep The fields to keep
     */
    public function removeOtherFields(array $fieldsKeyToKeep): void
    {
        $this->checkConfiguration();
        //get rid of all unnecessary fields
        $fieldsKeyToRemove = array_diff(array_keys($GLOBALS['TL_DCA'][$this->table]['fields']), $fieldsKeyToKeep);
        $palettesNames = array_keys($GLOBALS['TL_DCA'][$this->table]['palettes']);
        $subpalettesNames = array_keys($GLOBALS['TL_DCA'][$this->table]['subpalettes']);
        $pm = PaletteManipulator::create();
        foreach ($fieldsKeyToRemove as $field) {
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
    }

    /**
     * Set the alias field's readonly value.
     *
     * @param bool|bool $readOnly The readonly value
     */
    public function setFieldAliasReadonly(?bool $readOnly = true): void
    {
        $this->checkConfiguration();
        self::setFieldReadonly('alias', $readOnly);
    }

    /**
     * Set a field's readonly value.
     *
     * @param bool|bool $readOnly The readonly value
     */
    public function setFieldReadonly(string $field, ?bool $readOnly = true): void
    {
        $this->checkConfiguration();
        $GLOBALS['TL_DCA'][$this->table]['fields'][$field]['eval']['readonly'] = $readOnly;
    }

    /**
     * Remove the "edit" operation in list.
     */
    public function removeListOperationsEdit(): void
    {
        $this->checkConfiguration();
        unset($GLOBALS['TL_DCA'][$this->table]['list']['operations']['edit']);
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
