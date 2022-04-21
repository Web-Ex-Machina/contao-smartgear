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
    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_notations'],
    'contentCategory' => 'SMARTGEAR',
    'standardFields' => ['cssID'/*,'headline'*/],
    'fields' => [
        'noteMax' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_notations']['note_max'],
            'inputType' => 'text',
            'default' => 5,
            'eval' => ['tl_class' => 'w50 clr', 'rgxp' => 'digit'],
        ],
        'notations' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_notations']['notations'],
            'elementLabel' => '%s. note',
            'inputType' => 'list',
            'fields' => [
                'label' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_notations']['label'],
                    'inputType' => 'text',
                    'eval' => ['tl_class' => 'clr w50', 'mandatory' => true],
                ],
                'note' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_notations']['note'],
                    'inputType' => 'text',
                    'eval' => ['tl_class' => 'w50', 'rgxp' => 'digit', 'mandatory' => true],
                ],
            ],
        ],
        'text' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_notations']['text'], 'inputType' => 'textarea', 'eval' => ['rte' => 'tinyMCE', 'tl_class' => 'clr'],
        ],
    ],
];
