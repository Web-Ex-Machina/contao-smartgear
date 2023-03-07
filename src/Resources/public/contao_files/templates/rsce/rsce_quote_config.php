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
    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_quote'],
    'types' => ['content'],
    'contentCategory' => 'texts',
    'standardFields' => ['cssID'],
    'fields' => [
        'image_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['image_legend']],
            'inputType' => 'group',
        ],
        'singleSRC' => [
            'inputType' => 'standardField',
            'eval' => ['mandatory' => false],
        ],
        'alt' => [
            'inputType' => 'standardField',
            'eval' => ['tl_class' => 'w100 long'],
        ],
        'size' => [
            'inputType' => 'standardField',
            'eval' => ['mandatory' => false]
        ],
        'image_pos' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_quote']['image_pos'],
            'inputType' => 'select',
            'options' => array(
                'before' => &$GLOBALS['TL_LANG']['tl_content']['alignment']['left'],
                'after' => &$GLOBALS['TL_LANG']['tl_content']['alignment']['right'],
            ),
            'eval' => array('tl_class'=>'w50'),
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
                // 'field' => 'image_displaymode', 
                // 'value' => 'img--cover',
            ),
            'eval' => array('tl_class'=>'w50'),
        ),
        'image_displaymode' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_content']['image_displaymode'],
            'inputType' => 'select',
            'options' => array(
                'fit--cover'    => &$GLOBALS['TL_LANG']['tl_content']['image_displaymode']['cover'],
                'fit--contain'  => &$GLOBALS['TL_LANG']['tl_content']['image_displaymode']['contain'],
                // 'fit--natural'  => &$GLOBALS['TL_LANG']['tl_content']['image_displaymode']['natural'],
            ),
            'default' => 'fit--cover',
            'dependsOn' => array(
                'field' => 'image_ratio', 
                'value' => array(
                    'r_16-9',
                    'r_4-3' ,
                    'r_2-1' ,
                    'r_1-1' ,
                    'r_1-2' ,
                ),
            ),
            'eval' => array('tl_class'=>'w50 '),
        ),
        'image_align_horizontal' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_content']['image_align_horizontal'],
            'inputType' => 'select',
            'options' => array(
                'img--left'   => &$GLOBALS['TL_LANG']['tl_content']['alignment']['left'],
                'img--center' => &$GLOBALS['TL_LANG']['tl_content']['alignment']['center'],
                'img--right'  => &$GLOBALS['TL_LANG']['tl_content']['alignment']['right'],
            ),
            'default' =>  'img--center',
            // 'dependsOn' => array(
            //     'field' => 'image_displaymode', 
            //     'value' => 'img--cover',
            // ),
            'eval' => array('tl_class'=>'w50 clr'),
        ),
        'image_align_vertical' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_content']['image_align_vertical'],
            'inputType' => 'select',
            'options' => array(
                'img--top'    => &$GLOBALS['TL_LANG']['tl_content']['alignment']['top'],
                'img--center' => &$GLOBALS['TL_LANG']['tl_content']['alignment']['center'],
                'img--bottom' => &$GLOBALS['TL_LANG']['tl_content']['alignment']['bottom'],
            ),
            'default' =>  'img--center',
            // 'dependsOn' => array(
            //     'field' => 'image_displaymode', 
            //     'value' => 'img--cover',
            // ),
            'eval' => array('tl_class'=>'w50'),
        ),
        'rounded' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_content']['picture_rounded'],
            'inputType' => 'checkbox', 
            'default' => false,
            'eval'      => ['tl_class' => 'w50 clr'],
        ],
        'content_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['content_legend']],
            'inputType' => 'group',
        ],
        'text' => [
            'inputType' => 'standardField',
            'eval' => ['mandatory' => true, 'tl_class' => 'clr'],
        ],
        'author' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_quote']['author'], 
            'inputType' => 'text', 
            'eval' => ['tl_class' => 'w50 clr', 'mandatory' => false],
        ],
    ],
];
