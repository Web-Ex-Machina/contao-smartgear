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

$GLOBALS['TL_DCA']['tl_sm_page_visit'] = [
    // Config
    'config' => [
        'dataContainer' => DC_Table::class,
        'ctable' => [],
        'ptable' => 'tl_page',
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
            'fields' => ['createdAt', 'pid', 'page_url', 'referer', 'hash'],
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
        'default' => '{title_legend},pid,page_url,page_url_base,referer,referer_base,hash;',
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
        'pid' => [
            'search' => true,
            'inputType' => 'picker',
            'foreignKey' => 'tl_page.name',
            'eval' => ['mandatory' => true, 'tl_class' => 'w50'],
            'sql' => 'int(10) unsigned NOT NULL default 0',
        ],
        'page_url' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'rgxp' => 'url', 'tl_class' => 'w50'],
            'sql' => 'TEXT NULL',
        ],
        'page_url_base' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => false, 'rgxp' => 'url', 'tl_class' => 'w50'],
            'sql' => 'TEXT NULL',
        ],
        'referer' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => false, 'rgxp' => 'url', 'tl_class' => 'w50'],
            'sql' => 'TEXT NULL',
        ],
        'referer_base' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => false, 'rgxp' => 'url', 'tl_class' => 'w50'],
            'sql' => 'TEXT NULL',
        ],
        'user_agent' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => false, 'tl_class' => 'w50'],
            'sql' => 'TEXT NULL',
        ],
        'hash' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => false, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
    ],
];
