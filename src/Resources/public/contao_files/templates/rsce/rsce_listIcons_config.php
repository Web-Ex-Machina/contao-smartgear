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
    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_listicons'],
    'types' => ['content'],
    'contentCategory' => 'miscellaneous',
    'standardFields' => ['cssID'],
    'fields' => [
        'listItems' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_listicons']['items_legend'],
            'elementLabel' => &$GLOBALS['TL_LANG']['tl_content']['rsce_listicons']['item_legend'],
            'inputType' => 'list',
            'fields' => [
                'icon_type' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_listicons']['icon_type'],
                    'inputType' => 'select',
                    'options' => [
                        'image' => &$GLOBALS['TL_LANG']['tl_content']['rsce_listicons']['icon_type']['image'],
                        'icon' => &$GLOBALS['TL_LANG']['tl_content']['rsce_listicons']['icon_type']['icon'],
                    ],
                    'default' => 'image',
                    'eval' => ['tl_class' => 'w50 clr'],
                ],
                'image_legend' => [
                    'label' => [&$GLOBALS['TL_LANG']['tl_content']['image_legend']],
                    'inputType' => 'group',
                    'dependsOn' => [
                        'field' => 'icon_type',
                        'value' => 'image',
                    ],
                ],
                'image_src' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['singleSRC'],
                    'inputType' => 'fileTree',
                    'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'extensions' => \Contao\Config::get('validImageTypes'), 'tl_class' => 'w50 clr'],
                ],
                'image_alt' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['alt'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w100 long clr'],
                ],
                'image_size' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['size'],
                    'inputType' => 'imageSize',
                    'reference' => &$GLOBALS['TL_LANG']['MSC'],
                    'eval' => ['rgxp' => 'natural', 'includeBlankOption' => true, 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'w50 clr'],
                    'options_callback' => function () {
                        return \Contao\System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(\Contao\BackendUser::getInstance());
                    },
                ],
                'image_displaymode' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['image_displaymode'],
                    'inputType' => 'select',
                    'options' => [
                        'fit--cover' => &$GLOBALS['TL_LANG']['tl_content']['image_displaymode']['cover'],
                        'fit--contain' => &$GLOBALS['TL_LANG']['tl_content']['image_displaymode']['contain'],
                        'fit--natural' => &$GLOBALS['TL_LANG']['tl_content']['image_displaymode']['natural'],
                    ],
                    'default' => 'fit--natural',
                    'eval' => ['tl_class' => 'w50 clr'],
                ],
                'image_ratio' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['image_ratio'],
                    'inputType' => 'select',
                    'options' => [
                        '' => 'Original',
                        'r_16-9' => '16:9',
                        'r_4-3' => '4:3',
                        'r_2-1' => '2:1',
                        'r_1-1' => '1:1',
                        'r_1-2' => '1:2',
                    ],
                    'dependsOn' => [
                        'field' => 'image_displaymode',
                        'value' => ['fit--cover', 'fit--contain'],
                    ],
                    'eval' => ['tl_class' => 'w50'],
                ],
                'image_align_horizontal' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['image_align_horizontal'],
                    'inputType' => 'select',
                    'options' => [
                        'img--left' => &$GLOBALS['TL_LANG']['tl_content']['alignment']['left'],
                        'img--center' => &$GLOBALS['TL_LANG']['tl_content']['alignment']['center'],
                        'img--right' => &$GLOBALS['TL_LANG']['tl_content']['alignment']['right'],
                    ],
                    'default' => 'img--center',
                    'dependsOn' => [
                        'field' => 'image_displaymode',
                        'value' => ['fit--cover', 'fit--contain'],
                    ],
                    'eval' => ['tl_class' => 'w50 clr'],
                ],
                'image_align_vertical' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['image_align_vertical'],
                    'inputType' => 'select',
                    'options' => [
                        'img--top' => &$GLOBALS['TL_LANG']['tl_content']['alignment']['top'],
                        'img--center' => &$GLOBALS['TL_LANG']['tl_content']['alignment']['center'],
                        'img--bottom' => &$GLOBALS['TL_LANG']['tl_content']['alignment']['bottom'],
                    ],
                    'default' => 'img--center',
                    'dependsOn' => [
                        'field' => 'image_displaymode',
                        'value' => ['fit--cover', 'fit--contain'],
                    ],
                    'eval' => ['tl_class' => 'w50'],
                ],
                'image_css' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['image_css'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50 clr', 'mandatory' => false],
                ],
                'icon_legend' => [
                    'label' => [&$GLOBALS['TL_LANG']['tl_content']['icon_legend']],
                    'inputType' => 'group',
                    'dependsOn' => [
                        'field' => 'icon_type',
                        'value' => 'icon',
                    ],
                ],
                'icon_html' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_listicons']['icon_html'],
                    'inputType' => 'text',
                    'eval' => ['tl_class' => 'clr', 'allowHtml' => true],
                ],
                'background_legend' => [
                    'label' => [&$GLOBALS['TL_LANG']['tl_content']['background_legend']],
                    'inputType' => 'group',
                ],
                'image_background' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_listicons']['image_background'],
                    'inputType' => 'select',
                    'options_callback' => function () {return \WEM\SmartgearBundle\Classes\Util::getSmartgearColors(); },
                    'eval' => ['tl_class' => 'w50 clr', 'includeBlankOption' => true],
                ],
                'content_legend' => [
                    'label' => [&$GLOBALS['TL_LANG']['tl_content']['content_legend']],
                    'inputType' => 'group',
                ],
                'text' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_listicons']['text'],
                    'inputType' => 'textarea',
                    'eval' => ['rte' => 'tinyMCE', 'tl_class' => 'clr', 'mandatory' => true],
                ],
                'href' => [
                    'label' => &$GLOBALS['TL_LANG']['MSC']['url'],
                    'inputType' => 'text',
                    'eval' => ['rgxp' => 'url', 'tl_class' => 'w50 wizard clr'],
                    'wizard' => [['tl_content', 'pagePicker']],
                ],
                'title' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['titleText'],
                    'inputType' => 'text',
                    'eval' => ['tl_class' => 'w50'],
                ],
                'target' => [
                    'label' => &$GLOBALS['TL_LANG']['MSC']['target'],
                    'inputType' => 'checkbox',
                    'eval' => ['tl_class' => 'w50'],
                ],
                'classes' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_listicons']['classes'],
                    'inputType' => 'text',
                    'eval' => ['tl_class' => 'w50 clr'],
                ],
            ],
        ],
    ],
];
