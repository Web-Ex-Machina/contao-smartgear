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

// $arrColors = [
//     '' => 'Par défaut', 'red' => 'red', 'grey' => 'grey', 'yellow' => 'yellow', 'blue' => 'blue', 'green' => 'green', 'orange' => 'orange', 'darkblue' => 'darkblue', 'gold' => 'gold', 'black' => 'black', 'blacklight' => 'blacklight', 'blacklighter' => 'blacklighter', 'greystronger' => 'greystronger', 'greystrong' => 'greystrong', 'greylight' => 'greylight', 'greylighter' => 'greylighter', 'white' => 'white', 'none' => 'none',
// ];

return [
    'label' => ['Citation', 'Générez un block citation, avec ou sans photo'],
    'types' => ['content'],
    'contentCategory' => 'texts',
    'standardFields' => ['cssID'],
    'fields' => [
        'image_legend' => [
            'label' => ['Image'],
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
        ],
        // 'imagesize_ratio' => array(
        //     'label' => array('Ratio', 'Si souhaité, sélectionnez un ratio d\'image'),
        //     'inputType' => 'select',
        //     'options' => array(
        //         '' => 'Original',
        //         'r_1-1' => '1:1',
        //         'r_2-1' => '2:1',
        //         'r_1-2' => '1:2',
        //         'r_16-9' => '16:9',
        //         'r_4-3' => '4:3',
        //     ),
        //     'eval' => array('tl_class'=>'w50'),
        // ),
        'image_pos' => [
            'label' => ['Positionnement', 'Sélectionnez la position de l\'image (gauche ou droite de la citation)'],
            'inputType' => 'select',
            'options' => [
                'before' => 'Gauche',
                'after' => 'Droite',
            ],
            'eval' => ['tl_class' => 'w50'],
        ],
        // 'imagesize_horizontal' => array(
        //     'label' => array('Alignement Horizontal', 'Si souhaité, ajustez la position horizontale de l\'image'),
        //     'inputType' => 'select',
        //     'options' => array(
        //         '' => 'Aucun',
        //         'left' => 'Gauche',
        //         'right' => 'Droite',
        //     ),
        //     'eval' => array('tl_class'=>'w50 clr'),
        // ),
        // 'imagesize_vertical' => array(
        //     'label' => array('Alignement vertical', 'Si souhaité, ajustez la position verticale de l\'image'),
        //     'inputType' => 'select',
        //     'options' => array(
        //         '' => 'Aucun',
        //         'top' => 'Haut',
        //         'bottom' => 'Bas',
        //     ),
        //     'eval' => array('tl_class'=>'w50'),
        // ),
        'content_legend' => [
            'label' => ['Contenu'],
            'inputType' => 'group',
        ],
        'text' => [
            'inputType' => 'standardField',
            'eval' => ['mandatory' => false, 'tl_class' => 'clr'],
        ],
        'author' => [
            'label' => ['Auteur', 'Si souhaité, indiquez l\'auteur de la citation'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50 clr', 'mandatory' => false],
        ],
        // 'bg_color' => [
        //     'label' => ['Couleur du fond', 'Si souhaité, ajustez la couleur de fond du bloc'],
        //     'inputType' => 'select',
        //     'options' => $arrColors,
        //     'eval' => ['tl_class' => 'w50'],
        // ],
    ],
];
