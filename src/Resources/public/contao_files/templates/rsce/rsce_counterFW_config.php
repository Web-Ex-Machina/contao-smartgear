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
    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_counterfw'], 'contentCategory' => 'SMARTGEAR', 'standardFields' => ['cssID'], 'fields' => [
        'config_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['rsce_counterfw']['config_legend']], 'inputType' => 'group',
        ], 'startVal' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_counterfw']['startVal'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50', 'rgxp' => 'digit', 'mandatory' => true],
        ], 'endVal' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_counterfw']['startVal'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50', 'rgxp' => 'digit', 'mandatory' => true],
        ], 'decimals' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_counterfw']['decimals'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50', 'rgxp' => 'digit', 'minval' => 0],
        ], 'duration' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_counterfw']['duration'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50', 'rgxp' => 'digit', 'minval' => 0],
        ], 'delay' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_counterfw']['delay'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50', 'rgxp' => 'digit', 'minval' => 0],
        ], 'unit' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_counterfw']['unit'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
        ], 'label' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_counterfw']['label'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
        ],
    ],
];
