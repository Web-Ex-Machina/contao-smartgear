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
    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_tabs'], 'contentCategory' => 'miscellaneous', 'standardFields' => ['cssID'], 'fields' => [
        // Items
        'items' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_tabs']['items_legend'], 'elementLabel' => &$GLOBALS['TL_LANG']['tl_content']['rsce_tabs']['item_legend'], 'inputType' => 'list', 'fields' => [
                // Content
                'title' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['headline'], 
                    'inputType' => 'text', 
                    'eval' => ['tl_class' => 'w50','mandatory' => true], 
                ],
                'content' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_tabs']['content'], 
                    'inputType' => 'textarea', 
                    'eval' => ['rte' => 'tinyMCE', 'tl_class' => 'clr'],
                ],
            ],
        ],
    ],
];
