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
    'label' => ['Bannière - Début', 'Générez un élément stylisé composé d\'un texte sur une image'], 'contentCategory' => 'SMARTGEAR', 'standardFields' => ['cssID'], 'fields' => [
        'config_legend' => [
            'label' => ['Configuration du bloc'], 'inputType' => 'group',
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
    ],
];
