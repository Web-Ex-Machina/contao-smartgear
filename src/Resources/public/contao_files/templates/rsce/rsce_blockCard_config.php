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
        'image_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['image_legend']],
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
            'label' => &$GLOBALS['TL_LANG']['tl_content']['image_css'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50', 'mandatory' => false],
        ],
        'image_displaymode' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_content']['image_displaymode'],
            'inputType' => 'select',
            'options' => array(
                'img--cover'    => &$GLOBALS['TL_LANG']['tl_content']['rsce_blockcard']['image_displaymode']['cover'],
                'img--contain'  => &$GLOBALS['TL_LANG']['tl_content']['rsce_blockcard']['image_displaymode']['contain'],
                'img--natural'  => &$GLOBALS['TL_LANG']['tl_content']['rsce_blockcard']['image_displaymode']['natural'],
            ),
            'default' => 'img--cover',
            'eval' => array('tl_class'=>'w50 clr'),
        ),
        'image_ratio' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_content']['image_ratio'],
            'inputType' => 'select',
            'options' => array(
                '' => 'Original',
                'r_16-9' => '16:9',
                'r_4-3'  => '4:3',
                'r_2-1'  => '2:1',
                'r_1-1'  => '1:1',
                'r_1-2'  => '1:2',
            ),
            'dependsOn' => array(
                'field' => 'image_displaymode', 
                'value' => 'img--cover',
            ),
            'eval' => array('tl_class'=>'w50'),
        ),
        'image_align_horizontal' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_content']['image_align_horizontal'],
            'inputType' => 'select',
            'options' => array(
                'left'   => &$GLOBALS['TL_LANG']['tl_content']['alignment']['left'],
                'center' => &$GLOBALS['TL_LANG']['tl_content']['alignment']['center'],
                'right'  => &$GLOBALS['TL_LANG']['tl_content']['alignment']['right'],
            ),
            'default' =>  'center',
            'dependsOn' => array(
                'field' => 'image_displaymode', 
                'value' => 'img--cover',
            ),
            'eval' => array('tl_class'=>'w50 clr'),
        ),
        'image_align_vertical' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_content']['image_align_vertical'],
            'inputType' => 'select',
            'options' => array(
                'top'    => &$GLOBALS['TL_LANG']['tl_content']['alignment']['top'],
                'center'       => &$GLOBALS['TL_LANG']['tl_content']['alignment']['center'],
                'bottom' => &$GLOBALS['TL_LANG']['tl_content']['alignment']['bottom'],
            ),
            'default' =>  'center',
            'dependsOn' => array(
                'field' => 'image_displaymode', 
                'value' => 'img--cover',
            ),
            'eval' => array('tl_class'=>'w50'),
        ),
        'content_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['content_legend']],
            'inputType' => 'group',
        ],
        'headline' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['headline'], 'inputType' => 'standardField', 'eval' => ['tl_class' => 'w50', 'mandatory' => false, 'allowHtml' => true, 'includeBlankOption' => true],
        ],
        'text' => [
            'inputType' => 'standardField',
            'eval' => ['tl_class' => 'clr', 'mandatory' => false],
        ],
        // 'content_background' => array(
        //     'label' => array('Couleur de fond', 'Si souhaité, ajustez la couleur de fond'),
        //     'inputType' => 'select',
        //     'options' => \WEM\SmartgearBundle\Classes\Util::getSmartgearColors(),
        //     'eval' => array('tl_class'=>'w50 clr','includeBlankOption'=>true),
        // ),
        // 'content_opacity' => array(
        //     'label' => array('Opacité du fond', 'Ajustez l\'opacité du fond de l\'item'),
        //     'inputType' => 'select',
        //     'options' => [
        //         '0'  => '0%',
        //         '1'  => '10%',
        //         '2'  => '20%',
        //         '3'  => '30%',
        //         '4'  => '40%',
        //         '5'  => '50%',
        //         '6'  => '60%',
        //         '7'  => '70%',
        //         '8'  => '80%',
        //         '9'  => '90%',
        //         '10' => '100%',
        //     ],
        //     'default' => '10',
        //     'eval' => ['tl_class' => 'w50', 'isAssociative' => true ],
        // ),
        'title_css' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['title_css'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50 clr', 'mandatory' => false],
        ],
        'text_css' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['text_css'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50 ', 'mandatory' => false],
        ],
        'link_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['link_legend']],
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
            'label' => &$GLOBALS['TL_LANG']['tl_content']['link_mode'],
            'inputType' => 'select',
            'options' => [
                'wrapper' => &$GLOBALS['TL_LANG']['tl_content']['link_mode']['optionWrapper'],
                'btn' => &$GLOBALS['TL_LANG']['tl_content']['link_mode']['optionBtn'],
                'link' => &$GLOBALS['TL_LANG']['tl_content']['link_mode']['optionLink'],
            ],
            'eval' => ['tl_class' => 'w50 clr'],
        ],
        'link_css' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['link_css'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50 ', 'mandatory' => false],
        ],
        'advanced_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['advanced_legend']],
            'inputType' => 'group',
        ],
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
            'eval' => ['tl_class' => 'clr'],
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
    ],
];
