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

return [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_counter'], 'contentCategory' => 'SMARTGEAR', 'standardFields' => ['cssID'], 'fields' => [
        'config_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['rsce_counter']['config_legend']], 'inputType' => 'group',
        ], 'startVal' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_counter']['startVal'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50', 'rgxp' => 'digit', 'mandatory' => true],
        ], 'endVal' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_counter']['startVal'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50', 'rgxp' => 'digit', 'mandatory' => true],
        ], 'decimals' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_counter']['decimals'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50', 'rgxp' => 'digit', 'minval' => 0],
        ], 'duration' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_counter']['duration'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50', 'rgxp' => 'digit', 'minval' => 0],
        ], 'delay' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_counter']['delay'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50', 'rgxp' => 'digit', 'minval' => 0],
        ], 'unit' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_counter']['unit'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
        ], 'label' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_counter']['label'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
        ],
    ],
];
