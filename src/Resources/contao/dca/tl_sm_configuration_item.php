<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2023 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

use Contao\DataContainer;
use WEM\SmartgearBundle\Model\Configuration\ConfigurationItem;

$GLOBALS['TL_DCA']['tl_sm_configuration_item'] = [
    // Config
    'config' => [
        'dataContainer' => \Contao\DC_Table::class,
        'ptable' => 'tl_sm_configuration',
        'switchToEdit' => true,
        'enableVersioning' => true,
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'pid' => 'index',
            ],
        ],
        'onsubmit_callback' => [['smartgear.data_container.configuration.configuration_item', 'onsubmitCallback']],
        'onload_callback' => [['smartgear.data_container.configuration.configuration_item', 'onloadCallback']],
    ],

    // List
    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_PARENT,
            // 'mode' => DataContainer::MODE_SORTED,
            'fields' => ['type'],
            // 'flag' => DataContainer::SORT_INITIAL_LETTERS_ASC,
            // 'flag' => 1,
            'panelLayout' => 'filter;search,limit',
            'headerFields' => ['title'],
        ],
        'label' => [
            'fields' => ['type', 'id'],
            'showColumns' => true,
            // 'format' => '[%s] %s | %s',
            'label_callback' => ['smartgear.data_container.configuration.configuration_item', 'listItems'],
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
                'icon' => 'edit.gif',
            ],
            'copy' => [
                'href' => 'act=copy',
                'icon' => 'copy.gif',
            ],
            'delete' => [
                'href' => 'act=delete',
                'icon' => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\''.$GLOBALS['TL_LANG']['MSC']['deleteConfirm'].'\'))return false;Backend.getScrollOffset()"',
            ],
            'show' => [
                'href' => 'act=show',
                'icon' => 'show.gif',
            ],
        ],
    ],

    // Palettes
    'palettes' => [
        '__selector__' => ['type'],
        'default' => '
            {title_legend},pid,type;
        ',
    ],

    // Subpalettes
    'subpalettes' => [
        'type_page-legal-notice' => ';{page_legend},contao_page,page_name;{content_legend},content_template',
        'type_page-privacy-politics' => ';{page_legend},contao_page,page_name;{content_legend},content_template',
        'type_page-sitemap' => ';{page_legend},contao_page,page_name;{module_legend},contao_module',
        'type_user-group-administrators' => ';{user_group_legend},contao_user_group,user_group_name',
        'type_user-group-redactors' => ';{user_group_legend},contao_user_group,user_group_name',
        'type_module-wem-sg-header' => ';{module_legend},contao_module,module_name;singleSRC,contao_layout_to_update',
        'type_module-wem-sg-footer' => ';{module_legend},contao_module,module_name;{content_legend},content_template,contao_layout_to_update',
        'type_module-breadcrumb' => ';{module_legend},contao_module,module_name;contao_layout_to_update',
        'type_module-wem-sg-social-networks' => ';{module_legend},contao_module,module_name',
        'type_mixed-sitemap' => ';{module_legend},contao_module,module_name;{page_legend},contao_page,page_name',
        'type_mixed-faq' => ';{module_legend},contao_module,module_name;{page_legend},contao_page,page_name;{faq_category_legend},contao_faq_category,faq_category_name',
    ],

    // Fields
    'fields' => [
        'id' => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'tstamp' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'created_at' => [
            'default' => time(),
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'pid' => [
            'foreignKey' => 'tl_sm_configuration.id',
            'sql' => "int(10) unsigned NOT NULL default '0'",
            'relation' => ['type' => 'belongsTo', 'load' => 'eager'],
        ],
        'type' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'select',
            'options' => ConfigurationItem::TYPES,
            'reference' => &$GLOBALS['TL_LANG']['tl_sm_configuration_item']['type'],
            'eval' => ['submitOnChange' => true, 'includeBlankOption' => true, 'mandatory' => true, 'chosen' => true, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'contao_page' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'picker',
            'foreignKey' => 'tl_page.id',
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => 'int(10) NOT NULL default 0',
            'relation' => ['type' => 'belongsTo', 'load' => 'eager', 'field' => 'id'],
        ],
        'page_name' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'contao_module' => [
            'exclude' => true,
            'search' => true,
            // 'inputType' => 'picker',
            'inputType' => 'select',
            'options_callback' => ['smartgear.data_container.configuration.configuration_item', 'contaoModuleOptionsCallback'],
            'foreignKey' => 'tl_module.name',
            'eval' => ['mandatory' => false, 'includeBlankOption' => true, 'maxlength' => 255, 'tl_class' => 'w50 wizard'],
            'sql' => 'int(10) NOT NULL default 0',
            'relation' => ['type' => 'belongsTo', 'load' => 'eager', 'field' => 'id'],
            'wizard' => [[\WEM\SmartgearBundle\DataContainer\Content::class, 'editModule']],
            'reference' => &$GLOBALS['TL_LANG']['FMD'],
        ],
        'module_name' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'content_template' => [
            'exclude' => true,
            'inputType' => 'select',
            'options_callback' => ['smartgear.data_container.configuration.configuration_item', 'contentTemplateOptionsCallback'],
            'eval' => ['chosen' => true, 'tl_class' => 'w50'],
            'sql' => 'TEXT NULL',
        ],
        'singleSRC' => [
            'exclude' => true,
            'inputType' => 'fileTree',
            'eval' => ['fieldType' => 'radio', 'filesOnly' => true, 'mandatory' => true, 'tl_class' => 'clr'],
            'sql' => 'binary(16) NULL',
        ],
        'contao_user_group' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'picker',
            'foreignKey' => 'tl_user_group.id',
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => 'int(10) NOT NULL default 0',
            'relation' => ['type' => 'belongsTo', 'load' => 'eager', 'field' => 'id'],
        ],
        'user_group_name' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'contao_faq_category' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'picker',
            'foreignKey' => 'tl_faq_category.id',
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => 'int(10) NOT NULL default 0',
            'relation' => ['type' => 'belongsTo', 'load' => 'eager', 'field' => 'id'],
        ],
        'faq_category_name' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'contao_layout_to_update' => [
            'exclude' => true,
            'inputType' => 'checkboxWizard',
            'options_callback' => ['smartgear.data_container.configuration.configuration_item', 'contaoLayoutToUpdateOptionsCallback'],
            // 'load_callback' => [['smartgear.data_container.configuration.configuration_item', 'contaoLayoutToUpdateLoadCallback']],
            'eval' => ['isAssociative' => true, 'multiple' => true, 'tl_class' => 'w50'],
            'sql' => 'blob NULL',
        ],
    ],
];
