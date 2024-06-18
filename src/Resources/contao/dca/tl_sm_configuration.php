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
use WEM\SmartgearBundle\Model\Configuration\Configuration;

$GLOBALS['TL_DCA']['tl_sm_configuration'] = [
    // Config
    'config' => [
        'dataContainer' => \Contao\DC_Table::class,
        'ctable' => ['tl_sm_configuration_item'],
        'switchToEdit' => true,
        'enableVersioning' => true,
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
        'onsubmit_callback' => [['smartgear.data_container.configuration.configuration', 'onsubmitCallback']],
        'ondelete_callback' => [['smartgear.data_container.configuration.configuration', 'ondeleteCallback']],
    ],

    // List
    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_SORTED,
            'fields' => ['title'],
            'flag' => DataContainer::SORT_INITIAL_LETTER_ASC,
            'panelLayout' => 'filter;search,limit',
        ],
        'label' => [
            'fields' => ['title'],
            'showColumns' => true,
            // 'format' => '[%s] %s | %s',
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
                'href' => 'table=tl_sm_configuration_item',
                'icon' => 'edit.svg',
            ],
            'editheader' => [
                'href' => 'act=edit',
                'icon' => 'header.svg',
                // 'button_callback' => ['tl_form', 'editHeader'],
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
        '__selector__' => ['analytics_solution', 'legal_owner_type', 'api_enabled'],
        'default' => '
            {title_legend},title;
            {general_legend},version,mode,admin_email,domain,email_gateway,language;
            {framway_legend},framway_path;
            {fonts_legend},google_fonts;
            {analytics_legend},analytics_solution;
            {legal_informations_legend},legal_owner_type,legal_owner_email,legal_owner_street,legal_owner_postal_code,legal_owner_city,legal_owner_region,legal_owner_country;
            {host_informations_legend},host_name,host_street,host_postal_code,host_city,host_region,host_country;
            {contao_theme_legend},contao_theme;
            {contao_modules_legend},contao_module_sitemap;
            {contao_layouts_legend},contao_layout_full, contao_layout_standard;
            {contao_pages_legend},contao_page_root, contao_page_home, contao_page_404;
            {api_legend},api_enabled;
        ',
        // {contao_modules_legend},contao_module_nav,contao_module_wem_sg_header,contao_module_breadcrumb,contao_module_wem_sg_footer,contao_module_sitemap,contao_module_footernav;
    ],

    // Subpalettes
    'subpalettes' => [
        'analytics_solution_none' => '',
        'analytics_solution_matomo' => 'matomo_host,matomo_id',
        'analytics_solution_google' => 'google_id',
        'legal_owner_type_person' => 'legal_owner_person_lastname,legal_owner_person_firstname',
        'legal_owner_type_company' => 'legal_owner_company_name,legal_owner_company_status,legal_owner_company_identifier,legal_owner_company_dpo_name,legal_owner_company_dpo_email',
        'api_enabled' => 'api_key',
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
        'title' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'version' => [
            'exclude' => true,
            'search' => true,
            // 'default' => '1.0.0',
            'inputType' => 'text',
            'load_callback' => [['smartgear.data_container.configuration.configuration', 'versionLoadCallback']],
            'eval' => ['mandatory' => true, 'readonly' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'mode' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'select',
            'options' => ['dev', 'prod'],
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'admin_email' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'rgxp' => 'email', 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'domain' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'email_gateway' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'picker',
            'foreignKey' => 'tl_nc_gateway.id',
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
            'relation' => ['type' => 'belongsTo', 'load' => 'eager', 'field' => 'id'],
        ],
        'language' => [
            'exclude' => true,
            'search' => true,
            'default' => 'fr',
            'inputType' => 'select',
            'options_callback' => static fn() => \Contao\System::getLanguages(),
            'eval' => ['mandatory' => true, 'chosen' => true, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'framway_path' => [
            'exclude' => true,
            'search' => true,
            'default' => Configuration::DEFAULT_FRAMWAY_PATH,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'google_fonts' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'listWizard',
            'save_callback' => [['smartgear.data_container.configuration.configuration', 'fieldGoogleFontsOnsaveCallback']], // items to item1,item2
            'load_callback' => [['smartgear.data_container.configuration.configuration', 'fieldGoogleFontsOnloadCallback']], // item1,item2 to items
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'analytics_solution' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'select',
            'options' => [
                Configuration::ANALYTICS_SOLUTION_NONE,
                Configuration::ANALYTICS_SOLUTION_GOOGLE,
                Configuration::ANALYTICS_SOLUTION_MATOMO,
            ],
            'eval' => ['submitOnChange' => true, 'mandatory' => true, 'tl_class' => 'w50'],
            'sql' => "varchar(6) NOT NULL default ''",
        ],
        'matomo_host' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'matomo_id' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'google_id' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'legal_owner_type' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'select',
            'options' => Configuration::TYPES_ALLOWED,
            'eval' => ['submitOnChange' => true, 'mandatory' => true, 'tl_class' => 'w50'],
            'sql' => "varchar(7) NOT NULL default ''",
        ],
        'legal_owner_person_firstname' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'legal_owner_person_lastname' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'legal_owner_company_name' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'legal_owner_company_status' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'legal_owner_company_identifier' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'legal_owner_company_dpo_name' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'legal_owner_company_dpo_email' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'rgxp' => 'email', 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'legal_owner_email' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'rgxp' => 'email', 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'legal_owner_street' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'legal_owner_postal_code' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'legal_owner_region' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'legal_owner_city' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'legal_owner_country' => [
            'exclude' => true,
            'search' => true,
            'default' => 'fr',
            'inputType' => 'select',
            'options_callback' => static fn() => \Contao\System::getCountries(),
            'eval' => ['mandatory' => false,  'chosen' => true, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],

        'host_name' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'host_street' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'host_postal_code' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'host_region' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'host_city' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'host_country' => [
            'exclude' => true,
            'search' => true,
            'default' => 'fr',
            'inputType' => 'select',
            'options_callback' => static fn() => \Contao\System::getCountries(),
            'eval' => ['mandatory' => false,  'chosen' => true, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'contao_theme' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'picker',
            'foreignKey' => 'tl_theme.id',
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => 'int(10) NOT NULL default 0',
            'relation' => ['type' => 'belongsTo', 'load' => 'eager', 'field' => 'id'],
        ],
        // 'contao_module_nav' => [
        //     'exclude' => true,
        //     'search' => true,
        //     'inputType' => 'picker',
        //     'foreignKey' => 'tl_module.id',
        //     'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
        //     'sql' => 'int(10) NOT NULL default 0',
        //     'relation' => ['type' => 'belongsTo', 'load' => 'eager', 'field' => 'id'],
        // ],
        // 'contao_module_wem_sg_header' => [
        //     'exclude' => true,
        //     'search' => true,
        //     'inputType' => 'picker',
        //     'foreignKey' => 'tl_module.id',
        //     'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
        //     'sql' => 'int(10) NOT NULL default 0',
        //     'relation' => ['type' => 'belongsTo', 'load' => 'eager', 'field' => 'id'],
        // ],
        // 'contao_module_breadcrumb' => [
        //     'exclude' => true,
        //     'search' => true,
        //     'inputType' => 'picker',
        //     'foreignKey' => 'tl_module.id',
        //     'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
        //     'sql' => 'int(10) NOT NULL default 0',
        //     'relation' => ['type' => 'belongsTo', 'load' => 'eager', 'field' => 'id'],
        // ],
        // 'contao_module_wem_sg_footer' => [
        //     'exclude' => true,
        //     'search' => true,
        //     'inputType' => 'picker',
        //     'foreignKey' => 'tl_module.id',
        //     'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
        //     'sql' => 'int(10) NOT NULL default 0',
        //     'relation' => ['type' => 'belongsTo', 'load' => 'eager', 'field' => 'id'],
        // ],
        'contao_module_sitemap' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'picker',
            'foreignKey' => 'tl_module.id',
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => 'int(10) NOT NULL default 0',
            'relation' => ['type' => 'belongsTo', 'load' => 'eager', 'field' => 'id'],
        ],
        // 'contao_module_footernav' => [
        //     'exclude' => true,
        //     'search' => true,
        //     'inputType' => 'picker',
        //     'foreignKey' => 'tl_module.id',
        //     'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
        //     'sql' => 'int(10) NOT NULL default 0',
        //     'relation' => ['type' => 'belongsTo', 'load' => 'eager', 'field' => 'id'],
        // ],
        'contao_layout_full' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'picker',
            'foreignKey' => 'tl_layout.id',
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => 'int(10) NOT NULL default 0',
            'relation' => ['type' => 'belongsTo', 'load' => 'eager', 'field' => 'id'],
        ],
        'contao_layout_standard' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'picker',
            'foreignKey' => 'tl_layout.id',
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => 'int(10) NOT NULL default 0',
            'relation' => ['type' => 'belongsTo', 'load' => 'eager', 'field' => 'id'],
        ],
        'contao_page_root' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'picker',
            'foreignKey' => 'tl_page.id',
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => 'int(10) NOT NULL default 0',
            'relation' => ['type' => 'belongsTo', 'load' => 'eager', 'field' => 'id'],
        ],
        'contao_page_home' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'picker',
            'foreignKey' => 'tl_page.id',
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => 'int(10) NOT NULL default 0',
            'relation' => ['type' => 'belongsTo', 'load' => 'eager', 'field' => 'id'],
        ],
        'contao_page_404' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'picker',
            'foreignKey' => 'tl_page.id',
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => 'int(10) NOT NULL default 0',
            'relation' => ['type' => 'belongsTo', 'load' => 'eager', 'field' => 'id'],
        ],
        'api_enabled' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'checkbox',
            'eval' => ['submitOnChange' => true, 'tl_class' => 'w50'],
            'sql' => "char(1) NOT NULL default '0'",
        ],
        'api_key' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'save_callback' => [['smartgear.data_container.configuration.configuration', 'apiKeySaveCallback']],
            'load_callback' => [['smartgear.data_container.configuration.configuration', 'apiKeyLoadCallback']],
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
    ],
];
