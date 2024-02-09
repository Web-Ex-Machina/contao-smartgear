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

(new DcaLoader('tl_style_manager_archive'))->load();

DCAManipulator::create('tl_style_manager_archive')
    ->addListLabelLabelCallback('smartgear.data_container.style_manager.style_manager_archive', 'listItems')
    ->addField('wem_sg_install', [
        'filter' => true,
        'label' => &$GLOBALS['TL_LANG']['WEMSG']['DCA']['wem_sg_install'],
        'inputType' => 'picker',
        'relation' => [
            'type' => 'hasOne',
            'load' => 'lazy',
            'table' => 'tl_sm_configuration',
        ],
        'eval' => ['submitOnChange' => true, 'tl_class' => 'clr m12'],
        'sql' => "INT(10) UNSIGNED NOT NULL default '0'",
    ])
;

foreach ($GLOBALS['TL_DCA']['tl_style_manager_archive']['palettes'] as $paletteName => $paletteConfig) {
    if ('__selector__' !== $paletteName) {
        PaletteManipulator::create()
            ->addLegend('wem_sg_install_legend')
            ->addField('wem_sg_install', 'wem_sg_install_legend')
            ->applyToPalette($paletteName, 'tl_style_manager_archive')
        ;
    }
}
