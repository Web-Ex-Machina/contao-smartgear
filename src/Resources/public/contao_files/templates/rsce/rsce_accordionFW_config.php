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
    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_accordionfw'], 'contentCategory' => 'accordion', 'standardFields' => ['cssID'], 'fields' => [
        'config_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['rsce_accordionfw']['config_legend']], 'inputType' => 'group',
        ], 'deploy_all' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_accordionfw']['deploy_all'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50 clr'],
        ], 'disable_collapse' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_accordionfw']['disable_collapse'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50'],
        ], 'auto_collapse' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_accordionfw']['auto_collapse'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50'],
        ]

        // Items
        , 'items' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_accordionfw']['items_legend'], 'elementLabel' => &$GLOBALS['TL_LANG']['tl_content']['rsce_accordionfw']['item_legend'], 'inputType' => 'list', 'fields' => [
                // Content
                'title' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['headline'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50', 'mandatory' => true],
                ], 'hl_title' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_accordionfw']['hl_title'],
                    'inputType' => 'select',
                    'options' => [
                        '' => &$GLOBALS['TL_LANG']['tl_content']['rsce_accordionfw']['hl_title']['option_none'],
                        'h1' => 'H1',
                        'h2' => 'H2',
                        'h3' => 'H3',
                        'h4' => 'H4',
                        'h5' => 'H5',
                        'h6' => 'H6',
                    ],
                    'eval' => ['tl_class' => 'w50 clr', 'mandatory' => false],
                ], 'content' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_accordionfw']['content'], 'inputType' => 'textarea', 'eval' => ['rte' => 'tinyMCE', 'tl_class' => 'clr', 'mandatory' => true],
                ], 'lock' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_accordionfw']['lock'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50'],
                ], 'active' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_accordionfw']['active'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50'],
                ],
            ],
        ],
    ],
];
