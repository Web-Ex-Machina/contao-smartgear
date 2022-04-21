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
    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecards'],
    'contentCategory' => 'SMARTGEAR',
    'standardFields' => ['cssID'],
    'fields' => [
        'listItems' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecards']['list_items'], 'elementLabel' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecards']['list_item'], 'inputType' => 'list', 'fields' => [
                'price_legend' => [
                    'label' => [&$GLOBALS['TL_LANG']['tl_content']['rsce_pricecards']['price_legend']], 'inputType' => 'group',
                ],
                'amount' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecards']['amount'], 'inputType' => 'text', 'eval' => ['tl_class' => 'clr w50'],
                ],
                'currency' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecards']['currency'], 'inputType' => 'text', 'eval' => ['tl_class' => 'clr w50'],
                ],
                'period' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecards']['period'], 'inputType' => 'text', 'eval' => ['tl_class' => 'clr w50'],
                ],
                'content_legend' => [
                    'label' => [&$GLOBALS['TL_LANG']['tl_content']['rsce_pricecards']['content_legend']], 'inputType' => 'group',
                ],
                'lines' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecards']['lines'], 'inputType' => 'listWizard', 'eval' => ['tl_class' => 'clr w50', 'allowHtml' => true],
                ],
                'cta_text' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['linkTitle'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50 clr', 'mandatory' => false],
                ],
                'cta_href' => [
                    'label' => &$GLOBALS['TL_LANG']['MSC']['url'], 'inputType' => 'text', 'eval' => ['rgxp' => 'url', 'tl_class' => 'w50 wizard '], 'wizard' => [['tl_content', 'pagePicker']],
                ],
                'cta_title' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['titleText'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
                ],
                'cta_target' => [
                    'label' => &$GLOBALS['TL_LANG']['MSC']['target'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50'],
                ],
                'cta_classes' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecards']['cta_classes'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50 clr'],
                ],
                'style_legend' => [
                    'label' => [&$GLOBALS['TL_LANG']['tl_content']['rsce_pricecards']['style_legend']], 'inputType' => 'group',
                ],
                'isMain' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pricecards']['isMain'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50'],
                ],
            ],
        ],
    ],
];
