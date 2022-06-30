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
        // Items
        'items' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['items_legend'], 'elementLabel' => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['item_legend'], 'inputType' => 'list', 'fields' => [
                // Background
                'image_legend' => [
                    'label' => [&$GLOBALS['TL_LANG']['tl_content']['image_legend']],
                    'inputType' => 'group',
                ],
                'img_src' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['img_src'], 
                    'inputType' => 'fileTree', 
                    'eval' => ['filesOnly' => true, 
                    'fieldType' => 'radio', 
                    'extensions' => Config::get('validImageTypes')],
                ], 
                'img_size' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['size'], 
                    'inputType' => 'imageSize', 
                    'reference' => &$GLOBALS['TL_LANG']['MSC'], 
                    'eval' => ['rgxp' => 'natural', 'includeBlankOption' => true, 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'w50 clr'], 
                    'options_callback' => function () {
                        return System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(BackendUser::getInstance());
                    },
                ], 
                'img_alt' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['alt'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
                ],
                'image_align_horizontal' => array(
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['image_align_horizontal'],
                    'inputType' => 'select',
                    'options' => array(
                        'img--left'   => &$GLOBALS['TL_LANG']['tl_content']['alignment']['left'],
                        'img--center' => &$GLOBALS['TL_LANG']['tl_content']['alignment']['center'],
                        'img--right'  => &$GLOBALS['TL_LANG']['tl_content']['alignment']['right'],
                    ),
                    'default' =>  'img--center',
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
                    'eval' => array('tl_class'=>'w50'),
                ),
                'image_opacity' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['image_opacity'],
                    'inputType' => 'select',
                    'options' => [
                        '0'  => '0%',
                        '1'  => '10%',
                        '2'  => '20%',
                        '3'  => '30%',
                        '4'  => '40%',
                        '5'  => '50%',
                        '6'  => '60%',
                        '7'  => '70%',
                        '8'  => '80%',
                        '9'  => '90%',
                        '10' => '100%',
                    ],
                    'default' => '10',
                    'eval' => ['tl_class' => 'w50', 'isAssociative' => true ],
                ],
                'overlay_legend' => [
                    'label' => [&$GLOBALS['TL_LANG']['tl_content']['overlay_legend']],
                    'inputType' => 'group',
                ],
                'overlay_background' => array(
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['overlay_background'],
                    'inputType' => 'select',
                    'options' => \WEM\SmartgearBundle\Classes\Util::getSmartgearColors(),
                    'eval' => array('tl_class'=>'w50 clr','includeBlankOption'=>true),
                ),
                'overlay_opacity' => array(
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['overlay_opacity'],
                    'inputType' => 'select',
                    'options' => [
                        '0'  => '0%',
                        '1'  => '10%',
                        '2'  => '20%',
                        '3'  => '30%',
                        '4'  => '40%',
                        '5'  => '50%',
                        '6'  => '60%',
                        '7'  => '70%',
                        '8'  => '80%',
                        '9'  => '90%',
                        '10' => '100%',
                    ],
                    'default' => '2',
                    'eval' => ['tl_class' => 'w50', 'isAssociative' => true ],
                ),

                // Content
                'content_legend' => [
                    'label' => [&$GLOBALS['TL_LANG']['tl_content']['content_legend']],
                    'inputType' => 'group',
                ],
                'headline' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['headline'],
                    'inputType' => 'inputUnit',
                    'options' => [
                        'h1' => 'H1',
                        'h2' => 'H2',
                        'h3' => 'H3',
                        'h4' => 'H4',
                        'h5' => 'H5',
                        'h6' => 'H6',
                    ],
                    'eval' => ['tl_class' => 'w50 clr', 'mandatory' => false, 'includeBlankOption' => true, 'allowHtml' => true],
                ],
                'title_modifier' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['title_modifier'],
                    'inputType' => 'select',
                    'options' => [
                        'title--1' => sprintf($GLOBALS['TL_LANG']['tl_content']['title_modifier']['option'], '1'),
                        'title--2' => sprintf($GLOBALS['TL_LANG']['tl_content']['title_modifier']['option'], '2'),
                        'title--3' => sprintf($GLOBALS['TL_LANG']['tl_content']['title_modifier']['option'], '3'),
                        'title--4' => sprintf($GLOBALS['TL_LANG']['tl_content']['title_modifier']['option'], '4'),
                    ],
                    'eval' => ['tl_class' => 'w50', 'mandatory' => false, 'includeBlankOption'=> true]
                ],
                'content' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['content'], 'inputType' => 'textarea', 'eval' => ['rte' => 'tinyMCE', 'tl_class' => 'clr'],
                ],
                'content_align_horizontal' => array(
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['content_horizontal'],
                    'inputType' => 'select',
                    'options' => array(
                        'content--h--left'   => &$GLOBALS['TL_LANG']['tl_content']['alignment']['left'],
                        'content--h--center' => &$GLOBALS['TL_LANG']['tl_content']['alignment']['center'],
                        'content--h--right'  => &$GLOBALS['TL_LANG']['tl_content']['alignment']['right'],
                    ),
                    'default' =>  null,
                    'eval' => array('tl_class'=>'w50 m12 cbx clr', 'includeBlankOption' => true),
                ),
                'content_align_vertical' => array(
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['content_vertical'],
                    'inputType' => 'select',
                    'options' => array(
                        'content--v--top'    => &$GLOBALS['TL_LANG']['tl_content']['alignment']['top'],
                        'content--v--center' => &$GLOBALS['TL_LANG']['tl_content']['alignment']['center'],
                        'content--v--bottom' => &$GLOBALS['TL_LANG']['tl_content']['alignment']['bottom'],
                    ),
                    'default' =>  null,
                    'eval' => array('tl_class'=>'w50 m12 cbx', 'includeBlankOption' => true),
                ),
                'content_fontcolor' => array(
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['content_fontcolor'],
                    'inputType' => 'select',
                    'options' => \WEM\SmartgearBundle\Classes\Util::getSmartgearColors(),
                    'eval' => array('tl_class'=>'w50 clr','includeBlankOption'=>true),
                ),

                // Link
                'link_legend' => [
                    'label' => [&$GLOBALS['TL_LANG']['tl_content']['link_legend']],
                    'inputType' => 'group',
                ],
                'link_href' => [
                    'label' => &$GLOBALS['TL_LANG']['MSC']['url'], 'inputType' => 'text', 'eval' => ['rgxp' => 'url', 'tl_class' => 'w50 wizard'], 'wizard' => [['tl_content', 'pagePicker']],
                ], 
                'link_text' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['linkTitle'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
                ], 
                'link_title' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['titleText'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
                ], 
                'link_classes' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['link_classes'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
                ], 
                'link_target' => [
                    'label' => &$GLOBALS['TL_LANG']['MSC']['target'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50'],
                ],
            ],
        ],
        'config_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['config_label']], 
            'inputType' => 'group',
        ],
        'autoplay' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['autoplay'],
            'inputType' => 'checkbox', 
            'default' => false,
            'eval'      => ['tl_class' => 'w50 clr'],
        ], 
        'loop' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['loop'],
            'inputType' => 'checkbox', 
            'default' => true,
            'eval'      => ['tl_class' => 'w50 '],
        ],
        'transition' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['transition'],
            'inputType' => 'select', 
            'options'   => [
                'translate' => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['transition']['optionTranslate'],
                'fade'      => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['transition']['optionFade'],
                'none'      => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['transition']['optionNone'],
            ], 'eval' => ['tl_class' => 'w50'],
        ],
        'transition_duration' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['transition_duration'],
            'inputType' => 'text',
            'default'   => 1000,
            'eval'      => ['tl_class' => 'w50 clr', 'rgxp' => 'digit'],
        ],
        'transition_delay' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['transition_delay'],
            'inputType' => 'text',
            'default' => 5600,
            'eval'      => ['tl_class' => 'w50 ', 'rgxp' => 'digit'],
        ],
        'config_nav_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['config_nav_legend']], 'inputType' => 'group',
        ],
        'nav_display' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['nav_display'],
            'inputType' => 'select', 
            'options'   => [
                ''            => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['nav_display']['default'],
                'nav--below'  => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['nav_display']['below'],
                'nav--hidden' => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['nav_display']['hidden'],
            ],
            'default' => '',
            'eval' => ['tl_class' => 'w50'],
        ],
        'nav_position_horizontal' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_content']['content_horizontal'],
            'inputType' => 'select', 
            'options'   => [
                'nav--left'  => &$GLOBALS['TL_LANG']['tl_content']['alignment']['left'],
                'nav--right' => &$GLOBALS['TL_LANG']['tl_content']['alignment']['right'],
            ],
            'default' => null,
            'eval' => ['tl_class' => 'w50 clr','includeBlankOption'=>true],
            'dependsOn' => array(
                'field' => 'nav_display', 
                'value' => array('','nav--below'),
            ),
        ],
        'nav_position_vertical' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_content']['content_vertical'],
            'inputType' => 'select', 
            'options'   => [
                'nav--top'    => &$GLOBALS['TL_LANG']['tl_content']['alignment']['top'],
                'nav--bottom' => &$GLOBALS['TL_LANG']['tl_content']['alignment']['bottom'],
            ],
            'default' => null,
            'eval' => ['tl_class' => 'w50 ','includeBlankOption'=>true],
            'dependsOn' => array(
                'field' => 'nav_display', 
                'value' => array(''),
            ),
        ],
        'nav_arrows' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['nav_arrows'],
            'inputType' => 'checkbox',
            'default' => false,
            'eval' => ['tl_class' => 'w50 clr'],
        ],
        'swipe' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['swipe'],
            'inputType' => 'checkbox',
            'default' => true,
            'eval' => ['tl_class' => 'w50 m12 clr'],
        ],
        'keypress' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['keypress'],
            'inputType' => 'checkbox',
            'default' => false,
            'eval' => ['tl_class' => 'w50 m12'],
        ],
        'advanced_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['advanced_legend']],
            'inputType' => 'group',
        ],
        'itemsPerRow' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['itemsPerRow'],
            'inputType' => 'text',
            'default'   => 1,
            'eval'      => ['tl_class' => 'w50 clr', 'rgxp' => 'natural', 'minval' => 1],
        ],
        'height' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_content']['rsce_slider']['height'],
            'inputType' => 'inputUnit',
            'options' => ['px','vh','vw','em','rem'],
            'eval'      => ['tl_class' => 'w50 clr','rgxp'=>'natural'],
        ],
        'fullheight' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_content']['force_fullheight'],
            'inputType' => 'checkbox', 
            'default' => false,
            'eval'      => ['tl_class' => 'w50 clr'],
        ], 
        'fullwidth' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_content']['force_fullwidth'],
            'inputType' => 'checkbox', 
            'default' => false,
            'eval'      => ['tl_class' => 'w50 '],
        ], 
    ],
];
