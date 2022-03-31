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
    'label' => ['Galerie d\'images', 'Générez une galerie d\'images personnalisée utilisant une grille responsive'],
    'contentCategory' => 'SMARTGEAR',
    'standardFields' => ['cssID'],
    'fields' => [
        // 'headline' => [
        //     'inputType' => 'standardField', 'eval' => ['tl_class' => 'w100'],
        // ],
        'nbCols_default' => [
            'label' => ['Nombre de colonnes', 'Indiquez le nombre de colonnes souhaité (entre 1 et 12)'],
            'inputType' => 'text',
            'eval' => ['rgxp' => 'digit', 'tl_class' => 'w50', 'min' => 1, 'max' => 12, 'mandatory' => true],
        ],
        'ratio' => [
            'label' => ['Ratio des images', 'Si souhaité, indiquez le ratio par défaut des images de la galerie'],
            'inputType' => 'select',
            'options' => [
                '' => ' - ',
                'r_1-1' => '1:1',
                'r_2-1' => '2:1',
                'r_1-2' => '1:2',
                'r_16-9' => '16:9',
            ],
            'eval' => ['tl_class' => 'w50'],
        ],
        'responsive_legend' => [
            'label' => ['Configuration responsive'], 'inputType' => 'group',
        ],
        'nbCols_xl' => [
            'label' => ['Nombre de colonnes < 1400px', 'Indiquez le nombre de colonnes souhaité (entre 1 et 12)'],
            'inputType' => 'text',
            'eval' => ['rgxp' => 'digit', 'tl_class' => 'w50', 'min' => 1, 'max' => 12],
        ],
        'nbCols_lg' => [
            'label' => ['Nombre de colonnes < 1200px', 'Indiquez le nombre de colonnes souhaité (entre 1 et 12)'],
            'inputType' => 'text',
            'eval' => ['rgxp' => 'digit', 'tl_class' => 'w50', 'min' => 1, 'max' => 12],
        ],
        'nbCols_md' => [
            'label' => ['Nombre de colonnes < 992px', 'Indiquez le nombre de colonnes souhaité (entre 1 et 12)'],
            'inputType' => 'text',
            'eval' => ['rgxp' => 'digit', 'tl_class' => 'w50', 'min' => 1, 'max' => 12],
        ],
        'nbCols_sm' => [
            'label' => ['Nombre de colonnes < 768px', 'Indiquez le nombre de colonnes souhaité (entre 1 et 12)'],
            'inputType' => 'text',
            'eval' => ['rgxp' => 'digit', 'tl_class' => 'w50', 'min' => 1, 'max' => 12],
        ],
        'nbCols_xs' => [
            'label' => ['Nombre de colonnes < 620px', 'Indiquez le nombre de colonnes souhaité (entre 1 et 12)'],
            'inputType' => 'text',
            'eval' => ['rgxp' => 'digit', 'tl_class' => 'w50', 'min' => 1, 'max' => 12],
        ],
        'nbCols_xxs' => [
            'label' => ['Nombre de colonnes < 520px', 'Indiquez le nombre de colonnes souhaité (entre 1 et 12)'],
            'inputType' => 'text',
            'eval' => ['rgxp' => 'digit', 'tl_class' => 'w50', 'min' => 1, 'max' => 12],
        ],
        'listItems' => [
            'label' => ['Images', 'Editez les images'], 'elementLabel' => '%s. image', 'inputType' => 'list', 'fields' => [
                'img_legend' => [
                    'label' => ['Configuration de l\'image'], 'inputType' => 'group',
                ],
                'img_src' => [
                    'label' => ['Image', 'Sélectionnez une image'], 'inputType' => 'fileTree', 'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'extensions' => Config::get('validImageTypes'), 'mandatory' => true],
                ],
                'img_size' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['size'], 'inputType' => 'imageSize', 'reference' => &$GLOBALS['TL_LANG']['MSC'], 'eval' => ['rgxp' => 'natural', 'includeBlankOption' => true, 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'w50'], 'options_callback' => function () {
                        return System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(BackendUser::getInstance());
                    },
                ],
                'img_alt' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['alt'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
                ],
                'ratio' => [
                    'label' => ['Ratio de l\'image', 'Si souhaité, indiquez le ratio de cette image'],
                    'inputType' => 'select',
                    'options' => [
                        '' => ' - ',
                        'r_1-1' => '1:1',
                        'r_2-1' => '2:1',
                        'r_1-2' => '1:2',
                        'r_16-9' => '16:9',
                    ],
                    'eval' => ['tl_class' => 'w50 clr'],
                ],
                'link_legend' => [
                    'label' => ['Configuration du lien'], 'inputType' => 'group',
                ],
                'href' => [
                    'label' => &$GLOBALS['TL_LANG']['MSC']['url'], 'inputType' => 'text', 'eval' => ['rgxp' => 'url', 'tl_class' => 'w50 wizard '], 'wizard' => [['tl_content', 'pagePicker']],
                ],
                'title' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['titleText'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
                ],
                'target' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['fullsize'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50'],
                ],
                'misc_legend' => [
                    'label' => ['Configuration avancée'], 'inputType' => 'group',
                ],
                'span_cols' => [
                    'label' => ['Empiétement - colonnes', 'Indiquez le nombre de colonnes sur lequel doit s\'étendre l\'image'],
                    'inputType' => 'text',
                    'eval' => ['rgxp' => 'digit', 'tl_class' => 'w50', 'min' => 1, 'max' => 12],
                ],
                'span_rows' => [
                    'label' => ['Empiétement - lignes', 'Indiquez le nombre de lignes sur lequel doit s\'étendre l\'image'],
                    'inputType' => 'text',
                    'eval' => ['rgxp' => 'digit', 'tl_class' => 'w50', 'min' => 1, 'max' => 12],
                ],
                'classes' => [
                    'label' => ['Classes supplémentaires', 'Indiquez, si souhaité, la ou les classes css à ajouter a l\'image'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50 clr'],
                ],
            ],
        ],
    ],
];
