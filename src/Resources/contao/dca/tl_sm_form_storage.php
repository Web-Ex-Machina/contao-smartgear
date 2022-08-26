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
        'ptable' => 'tl_form',
        'ctable' => ['tl_sm_form_storage_data'],
        'switchToEdit' => false,
        'enableVersioning' => false,
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'pid' => 'index',
            ],
        ],
        'onshow_callback' => [['smartgear.data_container.form_storage', 'onShowCallback']],
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
            'fields' => ['pid', 'createdAt', 'status', 'sender'],
            'showColumns' => true,
            'label_callback' => ['smartgear.data_container.form_storage', 'listItems'],
        ],
        'global_operations' => [
            'all' => [
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
            ],
            'export_all' => [
                'href' => 'key=export_all',
                'icon' => 'store.svg',
            ],
        ],
        'operations' => [
            'show_data' => [
                'href' => 'table=tl_sm_form_storage_data',
                'icon' => 'rows.svg',
            ],
            'edit' => [
                'href' => 'act=edit',
                'icon' => 'edit.svg',
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
            'export' => [
                'href' => 'key=export',
                'icon' => 'store.svg',
            ],
        ],
    ],
    // Palettes
    'palettes' => [
        'default' => '{title_legend},pid,status,note;{statistics_legend},completion_percentage,delay_to_first_interaction,delay_to_submission;{page_legend},current_page,current_page_url,referer_page,referer_page_url;{data_legend},form_storage_data;',
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
            'search' => true,
            'inputType' => 'select',
            'foreignKey' => 'tl_form.title',
            'eval' => ['mandatory' => true, 'tl_class' => 'clr', 'disabled' => true],
            'sql' => 'int(10) unsigned NOT NULL default 0',
            'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
        ],
        'status' => [
            'search' => true,
            'inputType' => 'select',
            'options' => [
                FormStorage::STATUS_UNREAD => &$GLOBALS['TL_LANG']['tl_sm_form_storage']['status']['unread'],
                FormStorage::STATUS_READ => &$GLOBALS['TL_LANG']['tl_sm_form_storage']['status']['read'],
                FormStorage::STATUS_SPAM => &$GLOBALS['TL_LANG']['tl_sm_form_storage']['status']['spam'],
                FormStorage::STATUS_OK => &$GLOBALS['TL_LANG']['tl_sm_form_storage']['status']['ok'],
                FormStorage::STATUS_REPLIED => &$GLOBALS['TL_LANG']['tl_sm_form_storage']['status']['replied'],
            ],
            'eval' => ['mandatory' => true, 'maxlength' => 32, 'tl_class' => 'w50'],
            'sql' => "varchar(32) NOT NULL default ''",
        ],
        'note' => [
            'inputType' => 'textarea',
            'eval' => ['tl_class' => 'w50', 'rows' => 3],
            'sql' => 'TEXT NULL',
        ],
        'token' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'completion_percentage' => [
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'rgxp' => 'custom', 'customRgxp' => '/^([0-9]{1,3}),([0-9]{2})$/', 'tl_class' => 'w50 clr', 'disabled' => true, 'maxval' => 100, 'minval' => 0],
            'sql' => 'DECIMAL(5,2) unsigned NOT NULL default 0',
        ],
        'delay_to_first_interaction' => [
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'rgxp' => 'custom', 'customRgxp' => '/^([0-9]{1,14})$/', 'tl_class' => 'w50', 'disabled' => true, 'minval' => 0],
            'load_callback' => [function ($value, $dc): string {
                $minutes = (int) ($value / 60000);
                $value = ($value % 60000);
                $seconds = (int) ($value / 1000);
                $value = ($value % 1000);
                $ms = $value;

                return sprintf('%02dm%02ds%03dms', $minutes, $seconds, $ms);
            }],
            'sql' => "varchar(14) NOT NULL default ''",
        ],
        'delay_to_submission' => [
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'rgxp' => 'custom', 'customRgxp' => '/^([0-9]{1,14})$/', 'tl_class' => 'w50 clr', 'disabled' => true, 'minval' => 0],
            'load_callback' => [function ($value, $dc): string {
                $minutes = (int) ($value / 60000);
                $value = ($value % 60000);
                $seconds = (int) ($value / 1000);
                $value = ($value % 1000);
                $ms = $value;

                return sprintf('%02dm%02ds%03dms', $minutes, $seconds, $ms);
            }],
            'sql' => "varchar(14) NOT NULL default ''",
        ],
        'current_page' => [
            'inputType' => 'select',
            'foreignKey' => 'tl_page.title',
            'eval' => ['tl_class' => 'w50', 'disabled' => true],
            'sql' => 'int(10) unsigned NOT NULL default 0',
            'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
        ],
        'current_page_url' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'mandatory' => true, 'rgxp' => 'httpurl', 'disabled' => true],
            'sql' => 'TEXT NULL',
        ],
        'referer_page' => [
            'inputType' => 'select',
            'foreignKey' => 'tl_page.title',
            'eval' => ['tl_class' => 'w50', 'disabled' => true],
            'sql' => 'int(10) unsigned NOT NULL default 0',
            'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
        ],
        'referer_page_url' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'rgxp' => 'httpurl', 'disabled' => true],
            'sql' => 'TEXT NULL',
        ],
        'form_storage_data' => [
            'search' => false,
            'exclude' => true,
            'input_field_callback' => ['smartgear.data_container.form_storage', 'showData'],
        ],
    ],
];
