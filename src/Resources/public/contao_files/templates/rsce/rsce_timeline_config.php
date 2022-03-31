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

$arrColors = [
    '' => 'Par défaut', 'red' => 'red', 'grey' => 'grey', 'yellow' => 'yellow', 'blue' => 'blue', 'green' => 'green', 'orange' => 'orange', 'darkblue' => 'darkblue', 'gold' => 'gold', 'black' => 'black', 'blacklight' => 'blacklight', 'blacklighter' => 'blacklighter', 'greystronger' => 'greystronger', 'greystrong' => 'greystrong', 'greylight' => 'greylight', 'greylighter' => 'greylighter', 'white' => 'white', 'none' => 'none',
];

return [
    'label' => ['Timeline', 'Générez une timeline'],
    'types' => ['content'],
    'contentCategory' => 'includes',
    'standardFields' => ['cssID'],
    'fields' => [
        // 'headline' => array(
        //     'inputType' => 'standardField'
        //     ,'eval' => array('tl_class' => 'w50','allowHtml'=>true)
        // ),
        'timeline_style' => [
            'label' => ['Style', 'Choisissez un style à appliquer à la timeline'],
            'inputType' => 'select',
            'options' => [
                '' => 'Par défaut',
                'condensed' => 'Condensé',
            ],
            'eval' => ['tl_class' => 'w50 clr'],
        ],
        'timeline_color' => [
            'label' => ['Couleur timeline', 'Change la couleur de la timeline'],
            'inputType' => 'select',
            'options' => $arrColors,
            'eval' => ['tl_class' => 'w50 clr'],
        ],
        'title_color' => [
            'label' => ['Couleur titres', 'Change la couleur des titres et années'],
            'inputType' => 'select',
            'options' => $arrColors,
            'eval' => ['tl_class' => 'w50'],
        ],
        'image_legend' => [
            'label' => ['Image'],
            'inputType' => 'group',
        ],
        'image' => [
            'label' => ['Image', 'Insérez une image qui sera utilisée pour illustrer votre timeline'],
            'inputType' => 'fileTree',
            'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'extensions' => Config::get('validImageTypes')],
        ],
        'imageCrop' => [
            'label' => ['Redimensionnement', 'Ajustez si souhaité votre illustration'],
            'inputType' => 'imageSize',
            'options' => System::getImageSizes(),
            'reference' => &$GLOBALS['TL_LANG']['MSC'],
            'eval' => ['rgxp' => 'natural', 'includeBlankOption' => true, 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'w50'],
        ],
        'imagePos' => [
            'label' => ['Position', 'Sélectionnez la position de votre illustration, à droite ou à gauche de la timeline'],
            'inputType' => 'select',
            'options' => ['left' => 'Gauche', 'right' => 'Droite'],
            'eval' => ['tl_class' => ' w50'],
        ],
        // Items
        'items' => [
            'label' => ['Items', 'Editez les items de votre Timeline'],
            'elementLabel' => '%s. item',
            'inputType' => 'list',
            'fields' => [
                // year, icon, title , texte
                // Content
                'year' => [
                    'label' => ['Année', 'Saisissez l\'année a afficher'],
                    'inputType' => 'text',
                    'eval' => ['tl_class' => 'w50', 'mandatory' => false],
                ],
                'headline' => [
                    'label' => ['Titre', 'Saisissez, si souhaité, un titre pour cette date'],
                    'inputType' => 'inputUnit',
                    'options' => ['h2', 'h3', 'h4', 'h5', 'h6'],
                    'eval' => ['tl_class' => 'w100 long m12 clr', 'allowHtml' => true],
                ],
                'text' => [
                    'label' => ['Contenu', 'Saisissez le texte a afficher pour cette date'],
                    'inputType' => 'textarea',
                    'eval' => ['rte' => 'tinyMCE', 'tl_class' => 'clr', 'mandatory' => false],
                ],
            ],
        ],
    ],
];
