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

use Contao\BackendTemplate;
use Contao\DataContainer;
use Contao\DC_Table;
use WEM\SmartgearBundle\Classes\Util;

$GLOBALS['TL_DCA']['tl_sm_dataset'] = [
    // Config
    'config' => [
        'dataContainer' => DC_Table::class,
        'ctable' => ['tl_sm_dataset_install'],
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
            'fields' => ['name'],
            'flag' => DataContainer::SORT_INITIAL_LETTER_ASC,
            'panelLayout' => 'filter;search,limit',
        ],
        'label' => [
            'showColumns' => true,
            'fields' => ['name', 'path', 'nb_elements', 'nb_media'],
            // 'format' => '%s %s %s',
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
                'href' => 'act=edit',
                'icon' => 'edit.svg',
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
            'install' => [
                'href' => 'key=install',
                'icon' => 'header.svg',
                'button_callback' => [\WEM\SmartgearBundle\DataContainer\Dataset::class, 'installButton'],
            ],
            'showInstalls' => [
                'href' => 'table=tl_sm_dataset_install',
                'icon' => 'header.svg',
            ],
        ],
    ],
    // Palettes
    'palettes' => [
        'default' => '{title_legend},path;{configuration_legend},name,mainTable,nb_elements,nb_media,uninstallable,allowMultipleInstall;{code_legend},phpCode,jsonCode;',
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
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50', 'readonly' => true],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'path' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'mainTable' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50', 'readonly' => true],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'uninstallable' => [
            'search' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 m12', 'disabled' => true],
            'sql' => "char(1) NOT NULL default ''",
        ],
        'allowMultipleInstall' => [
            'search' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 m12', 'disabled' => true],
            'sql' => "char(1) NOT NULL default ''",
        ],
        'nb_elements' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['disabled' => true, 'maxlength' => 3, 'tl_class' => 'w50'],
            'sql' => 'int(10) unsigned NOT NULL default 0',
        ],
        'nb_media' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['disabled' => true, 'maxlength' => 3, 'tl_class' => 'w50'],
            'sql' => 'int(10) unsigned NOT NULL default 0',
        ],
        'phpCode' => [
            'search' => false,
            'inputType' => 'textarea',
            'eval' => ['tl_class' => 'w50'],
            'input_field_callback' => function (DataContainer $dc, string $label) {
                $objTemplate = new BackendTemplate('be_ace');
                $objTemplate->selector = 'ctrl_source';
                $objTemplate->type = 'php';

                $codeEditor = $objTemplate->parse();

                $content = file_get_contents(Util::getDatasetPhpFileFromPath($dc->activeRecord->path));

                return '<div class="widget w50">
    <h3><label for="ctrl_source">'.$label.'</label></h3>
    <textarea name="source" id="ctrl_source" class="tl_textarea monospace" rows="12" cols="80" style="height:400px" onfocus="Backend.getScrollOffset()">'."\n".htmlspecialchars($content).'</textarea><p class="tl_help tl_tip">'.$GLOBALS['TL_LANG']['tl_files']['editor'][1].'</p></div>'.$codeEditor;
            },
        ],
        'jsonCode' => [
            'search' => false,
            'inputType' => 'textarea',
            'eval' => ['tl_class' => 'w50'],
            'input_field_callback' => function (DataContainer $dc, string $label) {
                $objTemplate = new BackendTemplate('be_ace');
                $objTemplate->selector = 'ctrl_source';
                $objTemplate->type = 'json';

                $codeEditor = $objTemplate->parse();

                $content = file_get_contents(Util::getDatasetJsonFileFromPath($dc->activeRecord->path));

                return '<div class="widget w50">
    <h3><label for="ctrl_source">'.$label.'</label></h3>
    <textarea name="source" id="ctrl_source" class="tl_textarea monospace" rows="12" cols="80" style="height:400px" onfocus="Backend.getScrollOffset()">'."\n".htmlspecialchars($content).'</textarea><p class="tl_help tl_tip">'.$GLOBALS['TL_LANG']['tl_files']['editor'][1].'</p></div>'.$codeEditor;
            },
        ],
    ],
];
