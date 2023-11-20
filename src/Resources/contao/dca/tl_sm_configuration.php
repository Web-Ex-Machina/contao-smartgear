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

$GLOBALS['TL_DCA']['tl_sm_configuration'] = [
    // Config
    'config' => [
        'dataContainer' => \Contao\DC_Table::class,
        'switchToEdit' => true,
        'enableVersioning' => true,
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
        'onsubmit_callback' => [['smartgear.data_container.configuration.configuration', 'onsubmitCallback']],
    ],

    // List
    'list' => [
        'sorting' => [
            'mode' => 1,
            'fields' => ['title'],
            'flag' => 1,
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
        '__selector__' => ['statistic_solution', 'legal_owner_type', 'api_enabled'],
        'default' => '
            {title_legend},title;
            {general_legend},version,mode,admin_email,domain,email_gateway,language;
            {framway_legend},framway_path;
            {fonts_legend},google_fonts;
            {statistics_legend},statistic_solution;
            {legal_informations},legal_owner_type,owner_email,host_name,host_street,host_postal_code,host_city,host_region,host_country;
            {contao_theme_legend},contao_theme, contao_layout_full, contao_layout_standard;
            {contao_pages_legend},contao_page_root, contao_page_home, contao_page_404;
            {api_legend},api_enabled;
        ',
    ],

    // Subpalettes
    'subpalettes' => [
        'statistic_solution_none' => '',
        'statistic_solution_matomo' => 'matomo_host,matomo_id',
        'statistic_solution_google' => 'google_id',
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
            'default' => '1.0.0',
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
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
            'options_callback' => function () {
                return \Contao\System::getLanguages();
            },
            'eval' => ['mandatory' => true, 'chosen' => true, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'framway_path' => [
            'exclude' => true,
            'search' => true,
            'default' => '/assets/framway',
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
        'statistic_solution' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'select',
            'options' => ['none', 'matomo', 'google'],
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
            'options' => ['person', 'company'],
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
        'owner_email' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'rgxp' => 'email', 'tl_class' => 'w50'],
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
            'options_callback' => function () {
                return \Contao\System::getCountries();
            },
            'eval' => ['mandatory' => false,  'chosen' => true, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'contao_theme' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'picker',
            'foreignKey' => 'tl_theme.id',
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
            'relation' => ['type' => 'belongsTo', 'load' => 'eager', 'field' => 'id'],
        ],
        'contao_page_root' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'picker',
            'foreignKey' => 'tl_page.id',
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
            'relation' => ['type' => 'belongsTo', 'load' => 'eager', 'field' => 'id'],
        ],
        'contao_page_home' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'picker',
            'foreignKey' => 'tl_page.id',
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
            'relation' => ['type' => 'belongsTo', 'load' => 'eager', 'field' => 'id'],
        ],
        'contao_page_404' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'picker',
            'foreignKey' => 'tl_page.id',
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
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
            'eval' => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
    ],
];
