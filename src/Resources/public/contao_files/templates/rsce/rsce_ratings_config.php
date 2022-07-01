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
    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_ratings'],
    'contentCategory' => 'SMARTGEAR',
    'standardFields' => ['cssID'/*,'headline'*/],
    'fields' => [
        'noteMax' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_ratings']['note_max'],
            'inputType' => 'text',
            'default' => 5,
            'eval' => ['tl_class' => 'w50 clr', 'rgxp' => 'digit'],
        ],
        'ratings' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_ratings']['ratings'],
            'elementLabel' => '%s. note',
            'inputType' => 'list',
            'fields' => [
                'label' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_ratings']['label'],
                    'inputType' => 'text',
                    'eval' => ['tl_class' => 'clr w50', 'allowHtml' => true],
                ],
                'note' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_ratings']['note'],
                    'inputType' => 'text',
                    'eval' => ['tl_class' => 'w50', 'rgxp' => 'digit', 'mandatory' => true],
                ],
                'text' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_ratings']['text'], 
                    'inputType' => 'textarea', 
                    'eval' => ['rte' => 'tinyMCE', 'tl_class' => 'clr'],
                ],
            ],
        ],
    ],
];
