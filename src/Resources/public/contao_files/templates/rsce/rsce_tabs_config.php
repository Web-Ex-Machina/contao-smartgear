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
    'label' => ['Onglets / Tabs', 'Générez un ensemble d\'onglets ouvrants'], 'contentCategory' => 'SMARTGEAR', 'standardFields' => ['cssID'], 'fields' => [
        // Items
        'items' => [
            'label' => ['Tabs', 'Editez les onglets'], 'elementLabel' => '%s. Onglet', 'inputType' => 'list', 'fields' => [
                // Content
                'title' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['headline'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w100 long'],
                ], 'content' => [
                    'label' => ['Contenu', 'Saisissez le contenu textuel de la slide'], 'inputType' => 'textarea', 'eval' => ['rte' => 'tinyMCE', 'tl_class' => 'clr'],
                ],
            ],
        ],
    ],
];
