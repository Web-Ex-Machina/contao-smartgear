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
    'label' => [$GLOBALS['TL_LANG']['tl_content']['rsce_pricecards'][0], $GLOBALS['TL_LANG']['tl_content']['rsce_pricecards'][1]],
    'contentCategory' => 'miscellaneous',
    'standardFields' => ['cssID'],
    'fields' => [
        'amount_position' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecards']['amount_position'],
            'inputType' => 'select',
            'options' => [
                'before' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecards']['amount_position']['before'],
                'after' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecards']['amount_position']['after'],
            ],
            'eval' => ['tl_class' => 'w50'],
        ],
        'bg_color_default' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecards']['bg_color_default'],
            'inputType' => 'select',
            'options' => \WEM\SmartgearBundle\Classes\Util::getSmartgearColors(),
            'eval' => ['tl_class' => 'w50 clr', 'includeBlankOption' => true],
        ],
        'font_color_default' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecards']['font_color_default'],
            'inputType' => 'select',
            'options' => \WEM\SmartgearBundle\Classes\Util::getSmartgearColors(),
            'eval' => ['tl_class' => 'w50', 'includeBlankOption' => true],
        ],
        'listItems' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecards']['list_items'],
            'elementLabel' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecards']['list_item'],
            'inputType' => 'list',
            'fields' => [
                'title' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['headline'],
                    'inputType' => 'text',
                    'eval' => ['mandatory' => false, 'allowHtml' => true],
                ],
                'price_legend' => [
                    'label' => [&$GLOBALS['TL_LANG']['tl_content']['rsce_pricecards']['price_legend']],
                    'inputType' => 'group',
                ],
                'amount' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecards']['amount'],
                    'inputType' => 'text',
                    'eval' => ['tl_class' => 'w50'],
                ],
                'currency' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecards']['currency'],
                    'inputType' => 'text',
                    'eval' => ['tl_class' => ' w50'],
                ],
                'period' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecards']['period'],
                    'inputType' => 'text',
                    'eval' => ['tl_class' => 'clr w50'],
                ],
                'content_legend' => [
                    'label' => [&$GLOBALS['TL_LANG']['tl_content']['rsce_pricecards']['content_legend']],
                    'inputType' => 'group',
                ],
                'content_mode' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecards']['content_mode'],
                    'inputType' => 'select',
                    'options' => [
                        'lines' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecards']['content_mode']['lines'],
                        'text' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecards']['content_mode']['text'],
                    ],
                    'eval' => ['tl_class' => 'w50 clr'],
                ],
                'text' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['text'],
                    'inputType' => 'textarea',
                    'eval' => ['rte' => 'tinyMCE', 'tl_class' => 'clr', 'mandatory' => false],
                    'dependsOn' => [
                        'field' => 'content_mode',
                        'value' => ['text'],
                    ],
                ],
                'lines' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecards']['lines'],
                    'inputType' => 'listWizard',
                    'eval' => ['tl_class' => 'clr w50', 'allowHtml' => true],
                    'dependsOn' => [
                        'field' => 'content_mode',
                        'value' => ['lines'],
                    ],
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
                'cta_title' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['titleText'],
                    'inputType' => 'text',
                    'eval' => ['tl_class' => 'w50'],
                ],
                'cta_href' => [
                    'label' => &$GLOBALS['TL_LANG']['MSC']['url'],
                    'inputType' => 'text',
                    'eval' => ['rgxp' => 'url', 'tl_class' => 'w50 wizard '],
                    'wizard' => [['tl_content', 'pagePicker']],
                ],
                'cta_target' => [
                    'label' => &$GLOBALS['TL_LANG']['MSC']['target'],
                    'inputType' => 'checkbox',
                    'eval' => ['tl_class' => 'w50 m12'],
                ],
                'cta_classes' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecards']['cta_classes'],
                    'inputType' => 'text',
                    'eval' => ['tl_class' => 'w50 clr'],
                ],
                'style_legend' => [
                    'label' => [&$GLOBALS['TL_LANG']['tl_content']['rsce_pricecards']['style_legend']],
                    'inputType' => 'group',
                ],
                'bg_color' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecards']['bg_color'],
                    'inputType' => 'select',
                    'options' => \WEM\SmartgearBundle\Classes\Util::getSmartgearColors(),
                    'eval' => ['tl_class' => 'w50 clr', 'includeBlankOption' => true],
                ],
                'font_color' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecards']['font_color'],
                    'inputType' => 'select',
                    'options' => \WEM\SmartgearBundle\Classes\Util::getSmartgearColors(),
                    'eval' => ['tl_class' => 'w50', 'includeBlankOption' => true],
                ],
                'isMain' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecards']['isMain'],
                    'inputType' => 'checkbox',
                    'eval' => ['tl_class' => 'w50 m12'],
                ],
                'isMain_text' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecards']['isMain_text'],
                    'inputType' => 'text',
                    'eval' => ['tl_class' => 'w50 clr'],
                    'dependsOn' => [
                        'field' => 'isMain',
                    ],
                ],
                'isMain_classes' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecards']['isMain_classes'],
                    'inputType' => 'text',
                    'eval' => ['tl_class' => 'w50'],
                    'dependsOn' => [
                        'field' => 'isMain',
                    ],
                ],
            ],
        ],
    ],
];
