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
    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_hero'], 'contentCategory' => 'SMARTGEAR', 'standardFields' => ['cssID'], 'fields' => [
        'image_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['image_legend']],
            'inputType' => 'group',
        ],
        'singleSRC' => [
            'inputType' => 'standardField',
        ],
        'alt' => [
            'inputType' => 'standardField',
            'eval' => ['tl_class' => 'w50'],
        ],
        'image_size' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['size'], 'inputType' => 'imageSize', 'reference' => &$GLOBALS['TL_LANG']['MSC'], 'eval' => ['rgxp' => 'natural', 'includeBlankOption' => true, 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'w50 clr'], 'options_callback' => function () {
                return System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(BackendUser::getInstance());
            },
        ],
        'image_opacity' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['image_opacity'],
            'inputType' => 'select',
            'options' => [
                '0' => '0%',
                '1' => '10%',
                '2' => '20%',
                '3' => '30%',
                '4' => '40%',
                '5' => '50%',
                '6' => '60%',
                '7' => '70%',
                '8' => '80%',
                '9' => '90%',
                '10' => '100%',
            ],
            'default' => '100%',
            'eval' => ['tl_class' => 'w50', 'isAssociative' => true],
        ],
        'content_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['rsce_hero']['content_legend']],
            'inputType' => 'group',
        ],
        'headline' => [
            'inputType' => 'standardField',
            'eval' => ['mandatory' => false, 'includeBlankOption' => true],
        ],
        'title_modifier' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['title_modifier'],
            'inputType' => 'select',
            'options' => [
                '' => ' - ',
                'title--1' => sprintf($GLOBALS['TL_LANG']['tl_content']['title_modifier']['option'], '1'),
                'title--2' => sprintf($GLOBALS['TL_LANG']['tl_content']['title_modifier']['option'], '2'),
                'title--3' => sprintf($GLOBALS['TL_LANG']['tl_content']['title_modifier']['option'], '3'),
                'title--4' => sprintf($GLOBALS['TL_LANG']['tl_content']['title_modifier']['option'], '4'),
            ],
            'eval' => ['tl_class' => 'w50'],
        ],
        'text' => [
            'inputType' => 'standardField',
            'eval' => ['mandatory' => false, 'tl_class' => 'clr'],
        ],
        'content_horizontal' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['content_horizontal'],
            'inputType' => 'select',
            'options' => [
                'center' => &$GLOBALS['TL_LANG']['tl_content']['content_horizontal']['optionCenter'],
                'left' => &$GLOBALS['TL_LANG']['tl_content']['content_horizontal']['optionLeft'],
                'right' => &$GLOBALS['TL_LANG']['tl_content']['content_horizontal']['optionRight'],
            ],
            'eval' => ['tl_class' => 'w50', 'includeBlankOption' => true],
        ],
        'content_vertical' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['content_vertical'],
            'inputType' => 'select',
            'options' => [
                'center' => &$GLOBALS['TL_LANG']['tl_content']['content_vertical']['optionCenter'],
                'top' => &$GLOBALS['TL_LANG']['tl_content']['content_vertical']['optionTop'],
                'bottom' => &$GLOBALS['TL_LANG']['tl_content']['content_vertical']['optionBottom'],
            ],
            'eval' => ['tl_class' => 'w50', 'includeBlankOption' => true],
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
        ],
        'target' => [
            'label' => &$GLOBALS['TL_LANG']['MSC']['target'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50'],
        ],
        'link_css' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_hero']['link_css'],
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50 clr', 'mandatory' => false],
        ],
        'config_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['config_legend']], 'inputType' => 'group',
        ],
        'hero_height' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['hero_height'],
            'inputType' => 'radio',
            'options' => [
                'custom' => &$GLOBALS['TL_LANG']['tl_content']['hero_height']['custom'],
                'viewport' => &$GLOBALS['TL_LANG']['tl_content']['hero_height']['viewport'],
                'content' => &$GLOBALS['TL_LANG']['tl_content']['hero_height']['content'],
            ],
            'default' => 'custom',
            'eval' => ['tl_class' => 'w50 clr'],
        ],
        'block_height' => [
            'label' => '',
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50 clr cbx', 'style' => 'margin-top: -15px;', 'mandatory' => true],
            'default' => '40vh',
            'dependsOn' => [
                'field' => 'hero_height',
                'value' => 'custom',
            ],
        ],
        'hero_width' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['hero_width'],
            'inputType' => 'radio',
            'options' => [
                'default' => &$GLOBALS['TL_LANG']['tl_content']['hero_width']['default'],
                'viewport' => &$GLOBALS['TL_LANG']['tl_content']['hero_width']['viewport'],
                'content' => &$GLOBALS['TL_LANG']['tl_content']['hero_width']['content'],
            ],
            'default' => 'default',
            'eval' => ['tl_class' => 'w50 clr'],
        ],
    ],
];
