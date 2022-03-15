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

$GLOBALS['TL_DCA']['tl_sm_backup'] = [
    // Config
    'config' => [
        'dataContainer' => 'Table',
        'ctable' => [],
        'switchToEdit' => false,
        'enableVersioning' => false,
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
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
        'name' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'source' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'files' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'tl_class' => 'w100'],
            'sql' => "mediumtext NOT NULL default ''",
        ],
    ],
];
