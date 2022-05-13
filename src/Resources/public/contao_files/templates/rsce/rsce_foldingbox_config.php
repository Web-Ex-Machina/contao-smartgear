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
    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_foldingbox'], 'contentCategory' => 'SMARTGEAR', 'standardFields' => ['cssID'], 'fields' => [
        'config_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['rsce_foldingbox']['config_legend']], 'inputType' => 'group',
        ], 'height' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_foldingbox']['height'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
        ], 'break' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_foldingbox']['break'], 'inputType' => 'select', 'options' => ['' => 'Par défaut', 'xxs' => 'XXS / 520px', 'xs' => 'XS / 620px', 'sm' => 'SM / 768px', 'md' => 'MD / 992px', 'lg' => 'LG / 1200px', 'xl' => 'XL / 1400px'], 'eval' => ['tl_class' => 'w50'],
        ]

        // Items
        , 'items' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_foldingbox']['items_legend'], 'elementLabel' => &$GLOBALS['TL_LANG']['tl_content']['rsce_foldingbox']['item_legend'], 'inputType' => 'list', 'fields' => [
                // Background
                'img_src' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_foldingbox']['img_src'], 'inputType' => 'fileTree', 'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'extensions' => Config::get('validImageTypes')],
                ], 'img_size' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['size'], 'inputType' => 'imageSize', 'reference' => &$GLOBALS['TL_LANG']['MSC'], 'eval' => ['rgxp' => 'natural', 'includeBlankOption' => true, 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'w50'], 'options_callback' => function () {
                        return System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(BackendUser::getInstance());
                    },
                ], 'img_alt' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['alt'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
                ]

                // Content
                , 'content' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_foldingbox']['content'], 'inputType' => 'textarea', 'eval' => ['rte' => 'tinyMCE', 'tl_class' => 'clr'],
                ]

                // Link
                , 'link_href' => [
                    'label' => &$GLOBALS['TL_LANG']['MSC']['url'], 'inputType' => 'text', 'eval' => ['rgxp' => 'url', 'tl_class' => 'w50 wizard'], 'wizard' => [['tl_content', 'pagePicker']],
                ], 'link_title' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['linkTitle'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
                ], 'link_text' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['titleText'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
                ], 'link_target' => [
                    'label' => &$GLOBALS['TL_LANG']['MSC']['target'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50'],
                ],
            ],
        ],
    ],
];
