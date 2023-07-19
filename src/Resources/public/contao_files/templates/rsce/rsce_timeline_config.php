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

$arrColors = \WEM\SmartgearBundle\Classes\Util::getSmartgearColors('rsce');

return [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_timeline'],
    'types' => ['content'],
    'contentCategory' => 'miscellaneous',
    'standardFields' => ['cssID'],
    'fields' => [
        'timeline_style' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_timeline']['timeline_style'],
            'inputType' => 'select',
            'options' => [
                '' => &$GLOBALS['TL_LANG']['tl_content']['rsce_timeline']['timeline_style']['optionDefault'],
                'condensed' => &$GLOBALS['TL_LANG']['tl_content']['rsce_timeline']['timeline_style']['optionCondensed'],
            ],
            'eval' => ['tl_class' => 'w50 clr'],
        ],
        'timeline_color' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_timeline']['timeline_color'],
            'inputType' => 'select',
            'options' => $arrColors,
            'eval' => ['tl_class' => 'w50 clr'],
        ],
        'title_color' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_timeline']['title_color'],
            'inputType' => 'select',
            'options' => $arrColors,
            'eval' => ['tl_class' => 'w50'],
        ],
        'image_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['image_legend']],
            'inputType' => 'group',
        ],
        'image' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_timeline']['image'],
            'inputType' => 'fileTree',
            'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'extensions' => \Contao\Config::get('validImageTypes')],
        ],
        'imageCrop' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_timeline']['imageCrop'],
            'inputType' => 'imageSize',
            'options' => \Contao\System::getImageSizes(),
            'reference' => &$GLOBALS['TL_LANG']['MSC'],
            'eval' => ['rgxp' => 'natural', 'includeBlankOption' => true, 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'w50'],
        ],
        'imagePos' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_timeline']['imagePos'],
            'inputType' => 'select',
            'options' => ['left' => 'Gauche', 'right' => 'Droite'],
            'eval' => ['tl_class' => ' w50'],
        ],
        // Items
        'items' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_timeline']['items_legend'],
            'elementLabel' => &$GLOBALS['TL_LANG']['tl_content']['rsce_timeline']['item_legend'],
            'inputType' => 'list',
            'fields' => [
                // year, icon, title , texte
                // Content
                'year' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_timeline']['item_year'],
                    'inputType' => 'text',
                    'eval' => ['tl_class' => 'w50', 'mandatory' => false],
                ],
                'headline' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_timeline']['item_headline'],
                    'inputType' => 'inputUnit',
                    'options' => ['h2', 'h3', 'h4', 'h5', 'h6'],
                    'eval' => ['tl_class' => 'w100 long m12 clr', 'allowHtml' => true],
                ],
                'text' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_timeline']['item_text'],
                    'inputType' => 'textarea',
                    'eval' => ['rte' => 'tinyMCE', 'tl_class' => 'clr', 'mandatory' => false],
                ],
            ],
        ],
    ],
];
