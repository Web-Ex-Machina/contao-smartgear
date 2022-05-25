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
        'config_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['config_legend']], 'inputType' => 'group',
        ],
        'block_height' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['block_height'],
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50 clr'],
        ],
        'force_fullheight' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['force_fullheight'],
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 clr'],
        ],
        'force_fullwidth' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_content']['force_fullwidth'],
            'inputType' => 'checkbox',
            'eval' => array( 'tl_class' => 'w50 clr')
        ),
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
        'image_opacity' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_content']['image_opacity'],
            'inputType' => 'text',
            'eval' => array('rgxp' => 'digit', 'tl_class' => 'w50', 'min'=>0, 'max'=>10)
        ),
        'content_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['rsce_hero']['content_legend']],
            'inputType' => 'group',
        ],
        'headline' => array(
            'inputType' => 'standardField',
            'eval' => array('mandatory'=>false,'includeBlankOption'=>true)
        ),
        'title_modifier' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_content']['title_modifier'],
            'inputType' => 'select',
            'options' => array(
                '' => ' - ',
                'title--1' => 'Style 1',
                'title--2' => 'Style 2',
                'title--3' => 'Style 3',
                'title--4' => 'Style 4',
            ),
            'eval' => array('tl_class'=>'w50'),
        ),
        'text' => [
            'inputType' => 'standardField',
            'eval' => ['mandatory' => false, 'tl_class' => 'clr'],
        ],
        'content_horizontal' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_content']['content_horizontal'],
            'inputType' => 'select',
            'options' => array(
                'center' => 'Centré',
                'left' => 'Gauche',
                'right' => 'Droite',
            ),
            'eval' => array('tl_class'=>'w50','includeBlankOption'=>true),
        ),
        'content_vertical' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_content']['content_vertical'],
            'inputType' => 'select',
            'options' => array(
                'center' => 'Centré',
                'top' => 'Haut',
                'bottom' => 'Bas',
            ),
            'eval' => array('tl_class'=>'w50','includeBlankOption'=>true),
        ),
        'link_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['rsce_hero']['link_legend']],
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
        'link_css' => array(
            'label' => array('Classe(s) CSS lien', 'Classe(s) CSS à ajouter au lien')
            ,'inputType' => 'text'
            ,'eval' => array('tl_class'=>'w50 clr', 'mandatory' => false)
        ),
    ],
];


// STYLE MANAGER NEEDED TWEAKS
// remove fwherowfull, fwherofigureopacity, fwherotitle, fwherocontentvertical, fwherocontenthorizontal
// tabs global: fwheroft,fwherocontentbg, fwherocontentbgopacity, fwheroheightcontent, fwherowidthcontent
// tabs picture: fwheroimgvertical, fwheroimghorizontal