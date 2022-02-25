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
    'label' => ['Hero FW', 'Générez un élément stylisé composé d\'un texte sur une image'], 'contentCategory' => 'SMARTGEAR', 'standardFields' => ['cssID'], 'fields' => [
        'config_legend' => [
            'label' => ['Configuration du bloc'], 'inputType' => 'group',
        ], 'force_fullwidth' => [
            'label' => ['Toute la largeur', "Cochez pour forcer le bloc à prendre toute la largeur de l'écran"],
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50'],
        ],
        'force_fullheight' => [
            'label' => ['Toute la hauteur', "Cochez pour forcer le bloc à prendre toute la hauteur de l'écran"],
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50'],
        ],
        'block_height' => [
            'label' => ["Hauteur de l'élément", "Indiquez la hauteur voulue de l'élément"],
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50'],
        ],

        'image_legend' => [
            'label' => ['Image'],
            'inputType' => 'group',
        ],
        'singleSRC' => [
            'inputType' => 'standardField',
        ],
        'alt' => [
            'inputType' => 'standardField',
            'eval' => ['tl_class' => 'w50'],
        ],
        'img_size' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['size'], 'inputType' => 'imageSize', 'reference' => &$GLOBALS['TL_LANG']['MSC'], 'eval' => ['rgxp' => 'natural', 'includeBlankOption' => true, 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'w50 clr'], 'options_callback' => function () {
                return System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(BackendUser::getInstance());
            },
        ],
        'image_opacity' => [
            'label' => ['Opacité', 'Ajustez, si souhaité, l\'opacité de l\'image (Valeur entre 0 et 10)'],
            'inputType' => 'text',
            'eval' => ['rgxp' => 'digit', 'tl_class' => 'w50', 'min' => 0, 'max' => 10],
        ],

        'imagesize_legend' => [
            'label' => ['Image - Positionnement'],
            'inputType' => 'group',
        ],
        'imagesize_horizontal' => [
            'label' => ['Alignement Horizontal', 'Si souhaité, ajustez la position horizontale de l\'image'],
            'inputType' => 'select',
            'options' => [
                '' => 'Aucun',
                'left' => 'Gauche',
                'right' => 'Droite',
            ],
            'eval' => ['tl_class' => 'w50 clr'],
        ],
        'imagesize_vertical' => [
            'label' => ['Alignement vertical', 'Si souhaité, ajustez la position verticale de l\'image'],
            'inputType' => 'select',
            'options' => [
                '' => 'Aucun',
                'top' => 'Haut',
                'bottom' => 'Bas',
            ],
            'eval' => ['tl_class' => 'w50'],
        ],

        'content_legend' => [
            'label' => ['Contenu'],
            'inputType' => 'group',
        ],
        'headline' => [
            'inputType' => 'standardField',
            'eval' => ['mandatory' => false],
        ],
        'title_modifier' => [
            'label' => ['Variante titre', 'Ajoute un style particulier au titre'],
            'inputType' => 'select',
            'options' => [
                '' => ' - ',
                'title--1' => 'Style 1',
                'title--2' => 'Style 2',
                'title--3' => 'Style 3',
                'title--4' => 'Style 4',
            ],
            'eval' => ['tl_class' => 'w50'],
        ],

        'text' => [
            'inputType' => 'standardField',
            'eval' => ['mandatory' => false, 'tl_class' => 'clr'],
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

        'contentstyle_legend' => [
            'label' => ['Style du contenu'],
            'inputType' => 'group',
        ],
        'content_horizontal' => [
            'label' => ['Alignement Horizontal', 'Si souhaité, ajustez la position horizontale du contenu'],
            'inputType' => 'select',
            'options' => [
                'center' => 'Centré',
                'left' => 'Gauche',
                'right' => 'Droite',
            ],
            'eval' => ['tl_class' => 'w50'],
        ],
        'content_vertical' => [
            'label' => ['Alignement vertical', 'Si souhaité, ajustez la position verticale du contenu'],
            'inputType' => 'select',
            'options' => [
                'center' => 'Centré',
                'top' => 'Haut',
                'bottom' => 'Bas',
            ],
            'eval' => ['tl_class' => 'w50'],
        ],
        'content_color' => [
            'label' => ['Couleur du texte', 'Si souhaité, ajustez la couleur du contenu'],
            'inputType' => 'select',
            'options' => \WEM\SmartgearBundle\Classes\Util::getSmartgearColors('rsce-ft'),
            'eval' => ['tl_class' => 'w50'],
        ],
        'content_background' => [
            'label' => ['Fond du texte', 'Si souhaité, ajustez le fond du contenu'],
            'inputType' => 'select',
            'options' => \WEM\SmartgearBundle\Classes\Util::getSmartgearColors(),
            'eval' => ['tl_class' => 'w50 clr'],
        ],
        'content_background_opacity' => [
            'label' => ['Opacité', 'Ajustez, si souhaité, l\'opacité du fond (Valeur entre 0 et 10)'],
            'inputType' => 'text',
            'eval' => ['rgxp' => 'digit', 'tl_class' => 'w50', 'min' => 0, 'max' => 10],
        ],
    ],
];
