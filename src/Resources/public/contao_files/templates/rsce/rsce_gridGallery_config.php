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
    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_gridgallery'],
    'contentCategory' => 'SMARTGEAR',
    'standardFields' => ['cssID'],
    'fields' => [
        'listItems' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_gridgallery']['items_legend'], 'elementLabel' => &$GLOBALS['TL_LANG']['tl_content']['rsce_gridgallery']['item_legend'], 'inputType' => 'list',
            'fields' => [
                'img_legend' => [
                    'label' => [&$GLOBALS['TL_LANG']['tl_content']['rsce_gridgallery']['img_legend']], 'inputType' => 'group',
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

                'link_legend' => [
                    'label' => [&$GLOBALS['TL_LANG']['tl_content']['rsce_gridgallery']['link_legend']], 'inputType' => 'group',
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
            ],
        ],
    ],
];
