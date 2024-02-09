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

return [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_counter'], 'contentCategory' => 'miscellaneous', 'standardFields' => ['cssID'], 'fields' => [
        'config_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['rsce_counter']['config_legend']], 'inputType' => 'group',
        ],
        'startVal' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_counter']['startVal'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50', 'rgxp' => 'digit', 'mandatory' => true],
        ],
        'endVal' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_counter']['endVal'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50', 'rgxp' => 'digit', 'mandatory' => true],
        ],
        'prefix' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_counter']['prefix'],
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50'],
        ],
        'unit' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_counter']['unit'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
        ],
        'decimals' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_counter']['decimals'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50', 'rgxp' => 'digit', 'minval' => 0],
        ],
        'decimal' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_counter']['decimal'],
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50'],
        ],
        'separator' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_counter']['separator'],
            'inputType' => 'checkbox',
            'default' => 1,
            'eval' => ['tl_class' => 'w50 clr'],
        ],
        'duration' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_counter']['duration'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50 clr', 'rgxp' => 'digit', 'minval' => 0],
        ],
        'delay' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_counter']['delay'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50', 'rgxp' => 'digit', 'minval' => 0],
        ],
        'label' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_counter']['label'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
        ],
        'icon' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_counter']['icon'],
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'allowHtml' => true],
        ],
        'color' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['dominant_color'],
            'inputType' => 'select',
            'options_callback' => function ($dc) {return \WEM\SmartgearBundle\Classes\Util::getSmartgearColors($dc->table, (int) $dc->id); },
            'eval' => ['tl_class' => 'w50', 'includeBlankOption' => true],
        ],
    ],
];
