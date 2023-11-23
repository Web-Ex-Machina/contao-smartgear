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

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\DcaLoader;
use WEM\SmartgearBundle\Classes\Dca\Manipulator as DCAManipulator;

(new DcaLoader('tl_settings'))->load();

DCAManipulator::create('tl_settings')
    ->addField('wem_sg_encryption_key', [
        'label' => &$GLOBALS['TL_DCA']['tl_settings']['wem_sg_encryption_key'],
        'inputType' => 'text',
        'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ])
    ->addField('wem_sg_host_managed', [
        'label' => &$GLOBALS['TL_DCA']['tl_settings']['wem_sg_host_managed'],
        'inputType' => 'checkbox',
        'eval' => ['submitOnChange' => true, 'tl_class' => 'w50 m12'],
        'sql' => "char(1) NOT NULL default '0'",
    ])
    ->addField('wem_sg_airtable_api_key_read', [
        'label' => &$GLOBALS['TL_DCA']['tl_settings']['wem_sg_airtable_api_key_read'],
        'inputType' => 'text',
        // 'save_callback' => [['smartgear.data_container.configuration.configuration', 'apiKeySaveCallback']],
        // 'load_callback' => [['smartgear.data_container.configuration.configuration', 'apiKeyLoadCallback']],
        'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ])
    ->addField('wem_sg_support_form_enabled', [
        'label' => &$GLOBALS['TL_DCA']['tl_settings']['wem_sg_support_form_enabled'],
        'inputType' => 'checkbox',
        'eval' => ['submitOnChange' => true, 'tl_class' => 'w50 m12'],
        'sql' => "char(1) NOT NULL default '0'",
    ])
    ->addField('wem_sg_airtable_api_key_write', [
        'label' => &$GLOBALS['TL_DCA']['tl_settings']['wem_sg_airtable_api_key_write'],
        'inputType' => 'text',
        // 'save_callback' => [['smartgear.data_container.configuration.configuration', 'apiKeySaveCallback']],
        // 'load_callback' => [['smartgear.data_container.configuration.configuration', 'apiKeyLoadCallback']],
        'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ])
;

foreach ($GLOBALS['TL_DCA']['tl_settings']['palettes'] as $paletteName => $paletteConfig) {
    if ('__selector__' !== $paletteName) {
        PaletteManipulator::create()
            ->addLegend('smartgear_legend')
            ->addField('wem_sg_encryption_key', 'smartgear_legend')
            ->applyToPalette($paletteName, 'tl_settings')
        ;
        PaletteManipulator::create()
            ->addLegend('smartgear_host_legend')
            ->addField('wem_sg_host_managed', 'smartgear_host_legend')
            // ->addField('wem_sg_airtable_api_key_read', 'smartgear_host_legend')
            ->applyToPalette($paletteName, 'tl_settings')
        ;
        PaletteManipulator::create()
            ->addLegend('smartgear_support_legend')
            ->addField('wem_sg_support_form_enabled', 'smartgear_support_legend')
            // ->addField('wem_sg_airtable_api_key_write', 'smartgear_support_legend')
            ->applyToPalette($paletteName, 'tl_settings')
        ;
    }
}

$GLOBALS['TL_DCA']['tl_settings']['palettes']['__selector__'][] = 'wem_sg_host_managed';
$GLOBALS['TL_DCA']['tl_settings']['palettes']['__selector__'][] = 'wem_sg_support_form_enabled';
$GLOBALS['TL_DCA']['tl_settings']['subpalettes']['wem_sg_host_managed'] = 'wem_sg_airtable_api_key_read';
$GLOBALS['TL_DCA']['tl_settings']['subpalettes']['wem_sg_support_form_enabled'] = 'wem_sg_airtable_api_key_write';
