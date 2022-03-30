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
    'label' => ['Témoignages', 'Générez un slider affichant des témoignages/citations'], 'contentCategory' => 'SMARTGEAR', 'standardFields' => ['cssID'], 'fields' => [
        'config_legend' => [
            'label' => ['Configuration du slider'], 'inputType' => 'group',
        ], 'slide_height' => [
            'label' => ['Hauteur du slider', 'Configurez la hauteur du slider'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
        ], 'slide_autoplay' => [
            'label' => ['Démarrage automatique', 'Cochez pour que les contenus défilent automatiquement'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50 clr'],
        ], 'slider_loop' => [
            'label' => ['Loop', 'Cochez pour visualiser les contenus en boucle'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50'],
        ], 'config_nav_legend' => [
            'label' => ['Configuration de la navigation'], 'inputType' => 'group',
        ], 'nav_display' => [
            'label' => ['Affichage de la navigation', 'Si souhaité, ajustez la position des boutons de navigation du slider'], 'inputType' => 'select', 'options' => ['' => 'Après', 'inner' => 'A l\'intérieur', 'hidden' => 'Caché'], 'eval' => ['tl_class' => 'w50'],
        ], 'nav_horizontal' => [
            'label' => ['Position horizontale', 'Si souhaité, ajustez la position horizontale du contenu'], 'inputType' => 'select', 'options' => ['' => 'Aucun', 'left' => 'A gauche', 'right' => 'A droite'], 'eval' => ['tl_class' => 'w50'],
        ], 'nav_vertical' => [
            'label' => ['Position verticale', 'Si souhaité, ajustez la position verticale du contenu'], 'inputType' => 'select', 'options' => ['' => 'Aucun', 'top' => 'En Haut', 'bottom' => 'En Bas'], 'eval' => ['tl_class' => 'w50'],
        ], 'nav_arrows' => [
            'label' => ['Navigation fléchée', 'Cochez pour remplacer la navigation par des flèches latérales'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50 clr'],
        ]

        // Items
        , 'items' => [
            'label' => ['Témoignages/citations', 'Editez les témoignages/citations'], 'elementLabel' => '%s. témoignage/citation', 'inputType' => 'list', 'fields' => [
                // Background
                'slide_img_src' => [
                    'label' => ['Fond', 'Insérez une image qui sera utilisé comme fond de cet élément'], 'inputType' => 'fileTree', 'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'extensions' => Config::get('validImageTypes')],
                ], 'slide_img_size' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['size'], 'inputType' => 'imageSize', 'reference' => &$GLOBALS['TL_LANG']['MSC'], 'eval' => ['rgxp' => 'natural', 'includeBlankOption' => true, 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'w50 clr'], 'options_callback' => function () {
                        return System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(BackendUser::getInstance());
                    },
                ], 'slide_img_alt' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['alt'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
                ]

                // Content
                , 'slide_content' => [
                    'label' => ['Témoignage/citation', 'Saisissez le texte de cet élément'], 'inputType' => 'textarea', 'eval' => ['rte' => 'tinyMCE', 'tl_class' => 'clr'],
                ], 'slide_author' => [
                    'label' => ['Auteur', 'Saisissez l\'auteur de cet élément'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50', 'tl_class' => 'clr'],
                ], 'author_classes' => [
                    'label' => ['Classes supplémentaires auteur', 'Indiquez, si souhaité, la ou les classes css à ajouter au bloc de l\'auteur'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50 clr'],
                ],
            ],
        ],
    ],
];
