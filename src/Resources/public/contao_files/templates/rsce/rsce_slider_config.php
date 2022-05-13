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
    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider'], 'contentCategory' => 'slider', 'standardFields' => ['cssID'], 'fields' => [
        'config_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['config_label']], 'inputType' => 'group',
        ],
        'slide_height' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['slide_height'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
        ], 'slide_autoplay' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['slide_autoplay'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50 m12'],
        ], 'slider_loop' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['slider_loop'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50 m12'],
        ], 'slider_transition' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['slider_transition'], 'inputType' => 'select', 'options' => [
                'translate' => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['slider_transition']['optionTranslate'],
                'fade' => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['slider_transition']['optionFade'],
                'none' => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['slider_transition']['optionNone'],
            ], 'eval' => ['tl_class' => 'w50'],
        ],
        'config_nav_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['config_nav_legend']], 'inputType' => 'group',
        ],
        'nav_arrows' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['nav_arrows'],
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 m12'],
        ],
        'disable_swipe' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['disable_swipe'],
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 m12'],
        ], 'config_content_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['config_content_legend']], 'inputType' => 'group',
        ],
        'content_noblur' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['content_noblur'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50 m12'],
        ]

        // Items
        , 'items' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['items_legend'], 'elementLabel' => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['item_legend'], 'inputType' => 'list', 'fields' => [
                // Background
                'slide_img_src' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['slide_img_src'], 'inputType' => 'fileTree', 'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'extensions' => Config::get('validImageTypes')],
                ], 'slide_img_size' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['size'], 'inputType' => 'imageSize', 'reference' => &$GLOBALS['TL_LANG']['MSC'], 'eval' => ['rgxp' => 'natural', 'includeBlankOption' => true, 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'w50 clr'], 'options_callback' => function () {
                        return System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(BackendUser::getInstance());
                    },
                ], 'slide_img_alt' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['alt'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
                ],

                // Content
                'slide_content' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['slide_content'], 'inputType' => 'textarea', 'eval' => ['rte' => 'tinyMCE', 'tl_class' => 'clr'],
                ]

                // Link
                , 'slide_link_href' => [
                    'label' => &$GLOBALS['TL_LANG']['MSC']['url'], 'inputType' => 'text', 'eval' => ['rgxp' => 'url', 'tl_class' => 'w50 wizard'], 'wizard' => [['tl_content', 'pagePicker']],
                ], 'slide_link_text' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['linkTitle'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
                ], 'slide_link_title' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['titleText'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
                ], 'slide_link_classes' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['slide_link_classes'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
                ], 'slide_link_target' => [
                    'label' => &$GLOBALS['TL_LANG']['MSC']['target'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50'],
                ],
            ],
        ],
    ],
];
