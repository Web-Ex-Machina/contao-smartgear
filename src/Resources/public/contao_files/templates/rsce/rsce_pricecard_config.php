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
    'label' => [&$GLOBALS['TL_LANG']['tl_content']['rsce_pricecard'][0], &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecard'][1]],
    'contentCategory' => 'miscellaneous',
    'standardFields' => ['cssID'],
    'fields' => [
        'title' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['headline'],
            'inputType' => 'text',
            'eval' => ['mandatory' => false, 'allowHtml' => true],
        ],
        'content_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['content_legend']],
            'inputType' => 'group',
        ],
        'text' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['text'],
            'inputType' => 'textarea',
            'eval' => ['rte' => 'tinyMCE', 'tl_class' => 'clr', 'mandatory' => false],
        ],
        'text_classes' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecard']['text_classes'],
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50 clr'],
        ],
        'lines' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecard']['lines'],
            'inputType' => 'list',
            'fields' => [
                'text' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecard']['lines_text'],
                    'inputType' => 'text',
                    'eval' => ['tl_class' => '', 'mandatory' => false, 'allowHtml' => true],
                ],
                'icon' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecard']['lines_icon'],
                    'inputType' => 'text',
                    'eval' => ['tl_class' => 'w50', 'mandatory' => false, 'allowHtml' => true],
                ],
                'tooltip' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecard']['lines_tooltip'],
                    'inputType' => 'text',
                    'eval' => ['tl_class' => 'w50', 'mandatory' => false, 'allowHtml' => true],
                ],
            ],
        ],
        'icon_location' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecard']['icon_location'],
            'inputType' => 'select',
            'options' => [
                'before' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecard']['icon_location']['before'],
                'after' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecard']['icon_location']['after'],
            ],
            'eval' => ['tl_class' => 'w50'],
        ],
        'lines_alignement' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecard']['lines_alignment'],
            'inputType' => 'select',
            'options' => [
                '' => &$GLOBALS['TL_LANG']['tl_content']['alignment']['left'],
                'm-left-auto m-right-auto w-fit' => &$GLOBALS['TL_LANG']['tl_content']['alignment']['center'],
                'm-left-auto w-fit' => &$GLOBALS['TL_LANG']['tl_content']['alignment']['right'],
            ],
            'eval' => ['tl_class' => 'w50'],
        ],
        'price_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['rsce_pricecard']['price_legend']],
            'inputType' => 'group',
        ],
        'amount' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecard']['amount'],
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50'],
        ],
        'currency' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecard']['currency'],
            'inputType' => 'text',
            'eval' => ['tl_class' => ' w50'],
        ],
        'frequency' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecard']['frequency'],
            'inputType' => 'text',
            'eval' => ['tl_class' => 'clr w50'],
        ],
        'link_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['link_legend']],
            'inputType' => 'group',
        ],
        'cta_text' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['linkTitle'],
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50 clr', 'mandatory' => false],
        ],
        'cta_href' => [
            'label' => &$GLOBALS['TL_LANG']['MSC']['url'],
            'inputType' => 'text',
            'eval' => ['rgxp' => 'url', 'tl_class' => 'w50 wizard '],
            'wizard' => [['tl_content', 'pagePicker']],
        ],
        'cta_title' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['titleText'],
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50'],
        ],
        'cta_target' => [
            'label' => &$GLOBALS['TL_LANG']['MSC']['target'],
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 m12'],
        ],
        'cta_classes' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecard']['cta_classes'],
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50 clr'],
        ],
        'style_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['rsce_pricecard']['style_legend']],
            'inputType' => 'group',
        ],
        'color' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['dominant_color'],
            'inputType' => 'select',
            'options_callback' => function () {return \WEM\SmartgearBundle\Classes\Util::getSmartgearColors(); },
            'eval' => ['tl_class' => 'w50', 'includeBlankOption' => true],
        ],
        'bg_title' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecard']['bg_title'],
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 clr'],
        ],
        'bordered' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecard']['bordered'],
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 clr'],
        ],
        'bordered_on_hover' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecard']['bordered_on_hover'],
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 clr'],
        ],
        'dropshadow' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecard']['dropshadow'],
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 clr'],
        ],
    ],
];
