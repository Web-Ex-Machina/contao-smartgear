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
    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_testimonials'], 'contentCategory' => 'miscellaneous', 'standardFields' => ['cssID'], 'fields' => [
        'config_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['rsce_testimonials']['config_legend']], 'inputType' => 'group',
        ], 'slide_height' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_testimonials']['slide_height'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
        ], 'slide_autoplay' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_testimonials']['slide_autoplay'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50 clr'],
        ], 'slider_loop' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_testimonials']['slider_loop'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50'],
        ],
        'config_nav_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['rsce_testimonials']['config_nav_legend']], 'inputType' => 'group',
        ],
        'nav_arrows' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_testimonials']['nav_arrows'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50 clr'],
        ]

        // Items
        , 'items' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_testimonials']['items_legend'], 'elementLabel' => &$GLOBALS['TL_LANG']['tl_content']['rsce_testimonials']['item_legend'], 'inputType' => 'list', 'fields' => [
                // Background
                'slide_img_src' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_testimonials']['slide_img_src'], 'inputType' => 'fileTree', 'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'extensions' => \Contao\Config::get('validImageTypes')],
                ], 'slide_img_size' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['size'], 'inputType' => 'imageSize', 'reference' => &$GLOBALS['TL_LANG']['MSC'], 'eval' => ['rgxp' => 'natural', 'includeBlankOption' => true, 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'w50 clr'], 'options_callback' => function () {
                        return \Contao\System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(\Contao\BackendUser::getInstance());
                    },
                ], 'slide_img_alt' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['alt'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
                ]

                // Content
                , 'slide_content' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_testimonials']['slide_content'], 'inputType' => 'textarea', 'eval' => ['rte' => 'tinyMCE', 'tl_class' => 'clr'],
                ], 'slide_author' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_testimonials']['slide_author'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50', 'tl_class' => 'clr'],
                ], 'author_classes' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_testimonials']['author_classes'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50 clr'],
                ],
            ],
        ],
    ],
];
