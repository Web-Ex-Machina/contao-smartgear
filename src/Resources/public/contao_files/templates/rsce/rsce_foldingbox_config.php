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
    'label' => ['Folding Box', 'Générez un ensemble de panels ouvrants'], 'contentCategory' => 'SMARTGEAR', 'standardFields' => ['cssID'], 'fields' => [
        'config_legend' => [
            'label' => ["Configuration de l'élément"], 'inputType' => 'group',
        ], 'height' => [
            'label' => ['Hauteur du slider', 'Configurez la hauteur de l\'élément'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
        ], 'break' => [
            'label' => ['Responsive', 'Si souhaité, ajustez le moment où les éléments passent les uns en dessous des autres'], 'inputType' => 'select', 'options' => ['' => 'Par défaut', 'xxs' => 'XXS / 520px', 'xs' => 'XS / 620px', 'sm' => 'SM / 768px', 'md' => 'MD / 992px', 'lg' => 'LG / 1200px', 'xl' => 'XL / 1400px'], 'eval' => ['tl_class' => 'w50'],
        ]

        // Items
        , 'items' => [
            'label' => ['Panels', 'Editez les panels'], 'elementLabel' => '%s. Panel', 'inputType' => 'list', 'fields' => [
                // Background
                'img_src' => [
                    'label' => ['Image de fond', 'Insérez une image qui sera utilisé comme fond de cet item'], 'inputType' => 'fileTree', 'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'extensions' => Config::get('validImageTypes')],
                ], 'img_size' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['size'], 'inputType' => 'imageSize', 'reference' => &$GLOBALS['TL_LANG']['MSC'], 'eval' => ['rgxp' => 'natural', 'includeBlankOption' => true, 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'w50'], 'options_callback' => function () {
                        return System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(BackendUser::getInstance());
                    },
                ], 'img_alt' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['alt'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
                ]

                // Content
                , 'content' => [
                    'label' => ['Contenu', 'Saisissez le contenu textuel de l\'élément'], 'inputType' => 'textarea', 'eval' => ['rte' => 'tinyMCE', 'tl_class' => 'clr'],
                ]

                // Link
                , 'link_href' => [
                    'label' => &$GLOBALS['TL_LANG']['MSC']['url'], 'inputType' => 'text', 'eval' => ['rgxp' => 'url', 'tl_class' => 'w50 wizard'], 'wizard' => [['tl_content', 'pagePicker']],
                ], 'link_title' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['linkTitle'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
                ], 'link_text' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['titleText'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
                ], 'link_target' => [
                    'label' => &$GLOBALS['TL_LANG']['MSC']['target'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50'],
                ],
            ],
        ],
    ],
];
