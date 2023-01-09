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

use Contao\DataContainer;
use Contao\DC_Table;

$GLOBALS['TL_DCA']['tl_sm_login'] = [
    // Config
    'config' => [
        'dataContainer' => DC_Table::class,
        'ctable' => [],
        'ptable' => '',
        'switchToEdit' => false,
        'enableVersioning' => false,
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],

    // List
    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_SORTED,
            'fields' => ['createdAt'],
            'flag' => DataContainer::SORT_INITIAL_LETTER_DESC,
            'panelLayout' => 'filter;search,limit',
        ],
        'label' => [
            'fields' => ['createdAt', 'hash', 'context'],
            'showColumns' => true,
        ],
        'global_operations' => [],
        'operations' => [
            'show' => [
                'href' => 'act=show',
                'icon' => 'show.svg',
            ],
        ],
    ],
    // Palettes
    'palettes' => [
        'default' => '{title_legend},hash,context,createdAt;',
    ],
    // Fields
    'fields' => [
        'id' => [
            'search' => true,
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'tstamp' => [
            'flag' => 8,
            'sql' => "varchar(10) NOT NULL default ''",
        ],
        'createdAt' => [
            'default' => time(),
            'flag' => 8,
            'sql' => "varchar(10) NOT NULL default ''",
        ],
        'hash' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => false, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'context' => [
            'search' => true,
            'inputType' => 'select',
            'options' => ['FE', 'BE'],
            'eval' => ['mandatory' => true, 'tl_class' => 'w50'],
            'sql' => 'varchar(2) NOT NULL default ""',
        ],
    ],
];
