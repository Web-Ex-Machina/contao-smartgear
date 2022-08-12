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
use WEM\SmartgearBundle\Model\FormStorage;

$GLOBALS['TL_DCA']['tl_sm_form_storage'] = [
    // Config
    'config' => [
        'dataContainer' => 'Table',
        'ctable' => ['tl_sm_form_storage_data'],
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
            'fields' => ['createdAt', 'status'],
            'flag' => DataContainer::SORT_INITIAL_LETTER_ASC,
            'panelLayout' => 'filter;search,limit',
        ],
        'label' => [
            'fields' => ['createdAt', 'status'],
            'showColumns' => true,
            // 'format' => '%s',
        ],
        'global_operations' => [
            'all' => [
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
            ],
        ],
        'operations' => [
            'edit' => [
                'href' => 'table=tl_sm_form_storage_data',
                'icon' => 'edit.svg',
            ],
            'editheader' => [
                'href' => 'act=edit',
                'icon' => 'header.svg',
            ],
            'copy' => [
                'href' => 'act=paste&amp;mode=copy',
                'icon' => 'copy.svg',
                'attributes' => 'onclick="Backend.getScrollOffset()"',
            ],
            'cut' => [
                'href' => 'act=paste&amp;mode=cut',
                'icon' => 'cut.svg',
                'attributes' => 'onclick="Backend.getScrollOffset()"',
            ],
            'delete' => [
                'href' => 'act=delete',
                'icon' => 'delete.svg',
                'attributes' => 'onclick="if(!confirm(\''.($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null).'\'))return false;Backend.getScrollOffset()"',
            ],
            'show' => [
                'href' => 'act=show',
                'icon' => 'show.svg',
            ],
        ],
    ],
    // Palettes
    'palettes' => [
        'default' => '{title_legend},pid,status,token',
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
            'exclude' => true,
            'inputType' => 'select',
            'foreignKey' => 'tl_form.title',
            'eval' => ['mandatory' => true, 'tl_class' => 'clr'],
            'sql' => 'int(10) unsigned NOT NULL default 0',
            'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
        ],
        'status' => [
            'search' => true,
            'inputType' => 'select',
            'options' => [
                FormStorage::STATUS_UNREAD,
                FormStorage::STATUS_READ,
                FormStorage::STATUS_SPAM,
                FormStorage::STATUS_OK,
                FormStorage::STATUS_REPLIED,
            ],
            'eval' => ['mandatory' => true, 'maxlength' => 32, 'tl_class' => 'w50'],
            'sql' => "varchar(32) NOT NULL default ''",
        ],
        'token' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
    ],
];
