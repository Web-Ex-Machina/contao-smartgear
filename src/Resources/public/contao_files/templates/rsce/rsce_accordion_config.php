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
    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_accordion'], 
    'contentCategory' => 'accordion', 'standardFields' => ['cssID'], 
    'fields' => [
        // Items
         'items' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_accordion']['items_legend'], 
            'elementLabel' => &$GLOBALS['TL_LANG']['tl_content']['rsce_accordion']['item_legend'], 
            'inputType' => 'list', 'fields' => [
                // Content
                'headline' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['headline'],
                    'inputType' => 'inputUnit',
                    'options' => [
                        'h1' => 'H1',
                        'h2' => 'H2',
                        'h3' => 'H3',
                        'h4' => 'H4',
                        'h5' => 'H5',
                        'h6' => 'H6',
                    ],
                    'eval' => ['tl_class' => 'w50 clr', 'mandatory' => true, 'includeBlankOption' => true, 'allowHtml' => true],
                ]
                , 'content' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_accordion']['content'], 'inputType' => 'textarea', 'eval' => ['rte' => 'tinyMCE', 'tl_class' => 'clr', 'mandatory' => true],
                ], 'lock' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_accordion']['lock'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50'],
                ], 'active' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_accordion']['active'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50'],
                ],
            ],
        ],
        'config_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['rsce_accordion']['config_legend']], 'inputType' => 'group',
        ], 'deploy_all' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_accordion']['deploy_all'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50 clr'],
        ], 'disable_collapse' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_accordion']['disable_collapse'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50 clr'],
        ], 'auto_collapse' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_accordion']['auto_collapse'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50 clr'],
        ]
    ],
];
