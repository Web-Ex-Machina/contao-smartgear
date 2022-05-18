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
    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_blockcard'],
    'types' => ['content'],
    'contentCategory' => 'SMARTGEAR',
    'standardFields' => ['cssID'],
    'fields' => [
        'content_order' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_blockcard']['content_order'],
            'inputType' => 'select',
            'options' => [
                'img_first' => &$GLOBALS['TL_LANG']['tl_content']['rsce_blockcard']['content_order']['optionImg_first'],
                'txt_first' => &$GLOBALS['TL_LANG']['tl_content']['rsce_blockcard']['content_order']['optionTxt_first'],
            ],
            'eval' => ['tl_class' => 'clr w50'],
        ],
        'addRadius' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_blockcard']['addRadius'],
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50'],
        ],
        'preset_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['rsce_blockcard']['preset_legend']],
            'inputType' => 'group',
        ],
        'preset' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_blockcard']['preset'],
            'inputType' => 'select',
            'options' => [
                'light' => &$GLOBALS['TL_LANG']['tl_content']['rsce_blockcard']['preset']['optionLight'],
                'thumbnail' => &$GLOBALS['TL_LANG']['tl_content']['rsce_blockcard']['preset']['optionThumbnail'],
                'inline' => &$GLOBALS['TL_LANG']['tl_content']['rsce_blockcard']['preset']['optionInline'],
            ],
            'eval' => ['tl_class' => 'w50 clr', 'includeBlankOption' => true],
        ],
        'image_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['rsce_blockcard']['image_legend']],
            'inputType' => 'group',
        ],
        'singleSRC' => [
            'inputType' => 'standardField',
            'eval' => ['tl_class' => 'w50', 'mandatory' => false],
        ],
        'fullsize' => [
            'inputType' => 'standardField',
            'eval' => ['tl_class' => 'clr w50'],
        ],
        'alt' => [
            'inputType' => 'standardField',
            'eval' => ['tl_class' => 'clr w100 long'],
        ],
        'size' => [
            'inputType' => 'standardField',
        ],
        'image_css' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_blockcard']['image_css'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50 clr', 'mandatory' => false],
        ],
        'content_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['rsce_blockcard']['content_legend']],
            'inputType' => 'group',
        ],
        'headline' => [
            'label' => ['Titre', 'Si souhaité, indiquez un titre'], 'inputType' => 'standardField', 'eval' => ['tl_class' => 'w50', 'mandatory' => false, 'allowHtml' => true, 'includeBlankOption' => true],
        ],
        'text' => [
            'inputType' => 'standardField',
            'eval' => ['tl_class' => 'clr', 'mandatory' => false],
        ],
        'title_css' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_blockcard']['title_css'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50 clr', 'mandatory' => false],
        ],
        'text_css' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_blockcard']['text_css'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50 ', 'mandatory' => false],
        ],
        'link_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['rsce_blockcard']['link_legend']],
            'inputType' => 'group',
        ],
        'url' => [
            'inputType' => 'standardField',
            'eval' => ['mandatory' => false],
        ],
        'linkTitle' => [
            'inputType' => 'standardField',
            'eval' => ['allowHtml' => true],
        ],
        'target' => [
            'label' => &$GLOBALS['TL_LANG']['MSC']['target'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50'],
        ],
        'link_mode' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_blockcard']['link_mode'],
            'inputType' => 'select',
            'options' => [
                'wrapper' => &$GLOBALS['TL_LANG']['tl_content']['rsce_blockcard']['link_mode']['optionWrapper'],
                'btn' => &$GLOBALS['TL_LANG']['tl_content']['rsce_blockcard']['link_mode']['optionBtn'],
                'link' => &$GLOBALS['TL_LANG']['tl_content']['rsce_blockcard']['link_mode']['optionLink'],
            ],
            'eval' => ['tl_class' => 'w50 clr'],
        ],
        'link_css' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_blockcard']['link_css'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50 ', 'mandatory' => false],
        ],
    ],
];
