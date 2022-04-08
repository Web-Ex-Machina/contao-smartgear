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
    'label' => ['Slider (seul)', 'Générez un slider et configurez vos images'], 'contentCategory' => 'slider', 'standardFields' => ['cssID'], 'fields' => [
        'config_legend' => [
            'label' => ['Configuration du slider'], 'inputType' => 'group',
        ],
        // 'force_fullwidth' => [
        //     'label' => ['Toute la largeur', "Cochez pour forcer le bloc à prendre toute la largeur de l'écran"],
        //     'inputType' => 'checkbox',
        //     'eval' => ['tl_class' => 'w50 m12'],
        // ],
        'slide_height' => [
            'label' => ['Hauteur du slider', 'Configurez la hauteur du slider'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
        ], 'slide_autoplay' => [
            'label' => ['Démarrage automatique', 'Cochez pour faire en sorte que le slider se lance automatiquement'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50 m12'],
        ], 'slider_loop' => [
            'label' => ['Répétition', 'Cochez pour faire en sorte que le slider se relance une fois fini'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50 m12'],
        ], 'slider_transition' => [
            'label' => ['Transition', 'Sélectionner le type de transition souhaitée entre les slides'], 'inputType' => 'select', 'options' => ['translate' => 'Translation', 'fade' => 'Fading', 'none' => 'Aucune'], 'eval' => ['tl_class' => 'w50'],
        ],
        'config_nav_legend' => [
            'label' => ['Configuration de la navigation'], 'inputType' => 'group',
        ],
        // 'nav_display' => [
        //     'label' => ['Affichage de la navigation', 'Si souhaité, ajustez la position des boutons de navigation du slider'], 'inputType' => 'select', 'options' => ['' => 'Après', 'inner' => 'A l\'intérieur', 'hidden' => 'Caché'], 'eval' => ['tl_class' => 'w50'],
        // ],
        // 'nav_horizontal' => [
        //     'label' => ['Position horizontale', 'Si souhaité, ajustez la position horizontale de la navigation'], 'inputType' => 'select', 'options' => ['' => 'Aucun', 'left' => 'A gauche', 'right' => 'A droite'], 'eval' => ['tl_class' => 'clr w50'],
        // ],
        // 'nav_vertical' => [
        //     'label' => ['Position verticale', 'Si souhaité, ajustez la position verticale de la navigation'], 'inputType' => 'select', 'options' => ['' => 'Aucun', 'top' => 'En Haut', 'bottom' => 'En Bas'], 'eval' => ['tl_class' => 'w50'],
        // ],
        'nav_arrows' => [
            'label' => ['Navigation fléchée', 'Cochez pour activer la navigation fléchée (désactive la navigation classique)'],
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 m12'],
        ],
        'disable_swipe' => [
            'label' => ['Désactiver swipe', 'Cochez pour désactiver la navigation par swipe'],
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 m12'],
        ], 'config_content_legend' => [
            'label' => ['Configuration des contenus'], 'inputType' => 'group',
        ],
        // 'content_horizontal' => [
        //     'label' => ['Alignement Horizontal', 'Si souhaité, ajustez la position horizontale du contenu'], 'inputType' => 'select', 'options' => ['' => 'Aucun', 'right' => 'Droite', 'center' => 'Center'], 'eval' => ['tl_class' => 'w50'],
        // ],
        // 'content_vertical' => [
        //     'label' => ['Alignement vertical', 'Si souhaité, ajustez la position verticale du contenu'], 'inputType' => 'select', 'options' => ['' => 'Aucun', 'top' => 'Haut', 'center' => 'Centre'], 'eval' => ['tl_class' => 'w50'],
        // ],
        'content_noblur' => [
            'label' => ['Pas de flou', "Cochez pour désactiver l'effet de flou derrière le contenu"], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50 m12'],
        ]

        // Items
        , 'items' => [
            'label' => ['Slides', 'Editez les slides'], 'elementLabel' => '%s. slide', 'inputType' => 'list', 'fields' => [
                // Background
                'slide_img_src' => [
                    'label' => ['Fond', 'Insérez une image qui sera utilisé comme fond de cet item'], 'inputType' => 'fileTree', 'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'extensions' => Config::get('validImageTypes')],
                ], 'slide_img_size' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['size'], 'inputType' => 'imageSize', 'reference' => &$GLOBALS['TL_LANG']['MSC'], 'eval' => ['rgxp' => 'natural', 'includeBlankOption' => true, 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'w50 clr'], 'options_callback' => function () {
                        return System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(BackendUser::getInstance());
                    },
                ], 'slide_img_alt' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['alt'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
                ],
                // 'imagesize_horizontal' => [
                //     'label' => ['Alignement Horizontal', 'Si souhaité, ajustez la position horizontale de l\'image'],
                //     'inputType' => 'select',
                //     'options' => [
                //         '' => 'Aucun',
                //         'left' => 'Gauche',
                //         'right' => 'Droite',
                //     ],
                //     'eval' => ['tl_class' => 'w50 clr'],
                // ],
                // 'imagesize_vertical' => [
                //     'label' => ['Alignement vertical', 'Si souhaité, ajustez la position verticale de l\'image'],
                //     'inputType' => 'select',
                //     'options' => [
                //         '' => 'Aucun',
                //         'top' => 'Haut',
                //         'bottom' => 'Bas',
                //     ],
                //     'eval' => ['tl_class' => 'w50'],
                // ],

                // Content
                'slide_content' => [
                    'label' => ['Contenu', 'Saisissez le contenu textuel de la slide'], 'inputType' => 'textarea', 'eval' => ['rte' => 'tinyMCE', 'tl_class' => 'clr'],
                ]

                // Link
                , 'slide_link_href' => [
                    'label' => &$GLOBALS['TL_LANG']['MSC']['url'], 'inputType' => 'text', 'eval' => ['rgxp' => 'url', 'tl_class' => 'w50 wizard'], 'wizard' => [['tl_content', 'pagePicker']],
                ], 'slide_link_text' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['linkTitle'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
                ], 'slide_link_title' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['titleText'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
                ], 'slide_link_classes' => [
                    'label' => ['Classe(s) css du lien', 'Saisissez une ou plusieurs classes css à appliquer au lien (séparées par un espace)'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
                ], 'slide_link_target' => [
                    'label' => &$GLOBALS['TL_LANG']['MSC']['target'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50'],
                ],
            ],
        ],
    ],
];
