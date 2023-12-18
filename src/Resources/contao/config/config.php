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

use Contao\ArrayUtil;
use Contao\System;

if (!\defined('SG_ROBOTSTXT_HEADER')) {
    \define('SG_ROBOTSTXT_HEADER', '# RESERVED TO SMARTGEAR - START');
}

if (!\defined('SG_ROBOTSTXT_FOOTER')) {
    \define('SG_ROBOTSTXT_FOOTER', '# RESERVED TO SMARTGEAR - END');
}

if (!\defined('SG_ROBOTSTXT_CONTENT')) {
    \define('SG_ROBOTSTXT_CONTENT', "User-agent: *\nUser-agent: AdsBot-Google\nDisallow: /");
}

if (!\defined('SG_ROBOTSTXT_CONTENT_FULL')) {
    \define('SG_ROBOTSTXT_CONTENT_FULL', SG_ROBOTSTXT_HEADER."\n".SG_ROBOTSTXT_CONTENT."\n".SG_ROBOTSTXT_FOOTER);
}

// Load icon in Contao 4.2 backend
if ('BE' === TL_MODE) {
    $GLOBALS['TL_CSS'][] = 'bundles/wemsmartgear/backend/backend.css';
}

// Load Contao Bundles
$bundles = System::getContainer()->getParameter('kernel.bundles');

/*
 * Move Page Backend Module
 */
ArrayUtil::arrayInsert($GLOBALS['BE_MOD']['content'], 0, [
    'page' => $GLOBALS['BE_MOD']['design']['page'],
]);
unset($GLOBALS['BE_MOD']['design']['page']);

/*
 * Move Files Backend Module
 */
ArrayUtil::arrayInsert($GLOBALS['BE_MOD']['content'], 99, [
    'files' => $GLOBALS['BE_MOD']['system']['files'],
]);
unset($GLOBALS['BE_MOD']['system']['files']);

/*
 * Move Newsletter Backend Module
 */
if (isset($bundles['ContaoNewsletterBundle'])) {
    ArrayUtil::arrayInsert($GLOBALS['BE_MOD'], 1, [
        'newsletters' => [
            'channels' => $GLOBALS['BE_MOD']['content']['newsletter'],
            'newsletter' => [
                'tables' => ['tl_newsletter'],
                'send' => ['\WEM\SmartgearBundle\Override\Newsletter', 'send'],
                'stylesheet' => 'bundles/contaonewsletter/style.css',
            ],
        ],
    ]);
    unset($GLOBALS['BE_MOD']['content']['newsletter']);
}

/*
 * Move Multilingual pages
 */
if (isset($bundles['VerstaerkerI18nl10nBundle'])) {
    ArrayUtil::arrayInsert(
        $GLOBALS['BE_MOD']['content'],
        array_search('page', array_keys($GLOBALS['BE_MOD']['content']), true) + 1,
        [
            'i18nl10n' => $GLOBALS['BE_MOD']['design']['i18nl10n'],
        ]
    );
    unset($GLOBALS['BE_MOD']['design']['i18nl10n']);
}

/*
 * Move Page Backend Module
 */
// ArrayUtil::arrayInsert($GLOBALS['BE_MOD']['extranet'], 0, [
//     'member' => $GLOBALS['BE_MOD']['accounts']['member'],
//     'mgroup' => $GLOBALS['BE_MOD']['accounts']['mgroup'],
// ]);
// unset($GLOBALS['BE_MOD']['accounts']['member'], $GLOBALS['BE_MOD']['accounts']['mgroup']);

$GLOBALS['BE_MOD']['content']['form']['tables'][] = 'tl_sm_form_storage';
$GLOBALS['BE_MOD']['content']['form']['tables'][] = 'tl_sm_form_storage_data';

// ComponentStyleSelect override
$GLOBALS['BE_FFL']['stylemanager'] = WEM\SmartgearBundle\Widget\ComponentStyleSelect::class;
/*
 * Backend modules
 */
// ArrayUtil::arrayInsert($GLOBALS['BE_MOD']['system'], 0, [
    // 'smartgear' => [
    //     'callback' => "\WEM\SmartgearBundle\Backend\Smartgear",
    // ],
    //     'wem_sg_smartgear_configuration' => [
    //         'tables' => ['tl_sm_configuration', 'tl_sm_configuration_item'],
    //     ],
    //     'wem_sg_social_link_config_categories' => [
    //         'tables' => ['tl_sm_social_network_category', 'tl_sm_social_network'],
    //     ],
// ]);
ArrayUtil::arrayInsert($GLOBALS['BE_MOD'], 0, [
    'wem_smartgear' => [
        'wem_sg_dashboard' => [
            'callback' => "\WEM\SmartgearBundle\Backend\Dashboard",
        ],
        'wem_sg_reminder' => [
            'callback' => "\WEM\SmartgearBundle\Backend\Reminder",
        ],
        'undo' => $GLOBALS['BE_MOD']['system']['undo'],
    ],
]);
ArrayUtil::arrayInsert($GLOBALS['BE_MOD'], 0, [
    'wem_smartgear_admin' => [
        'wem_sg_update' => [
            'callback' => '\WEM\SmartgearBundle\Backend\Update',
        ],
        'wem_sg_backup' => [
            'callback' => '\WEM\SmartgearBundle\Backend\Backup',
        ],
        'wem_sg_clear_cache' => [
            'callback' => '\WEM\SmartgearBundle\Backend\CacheClear',
            // 'callback' => function (): void {
            //     $commandUtil = System::getContainer()->get('smartgear.classes.command.util');
            //     $this->commandUtil->executeCmd('cache:clear');
            // },
        ],
        'wem_sg_smartgear_configuration' => [
            'tables' => ['tl_sm_configuration', 'tl_sm_configuration_item'],
        ],
        'wem_sg_social_link_config_categories' => [
            'tables' => ['tl_sm_social_network_category', 'tl_sm_social_network'],
        ],
    ],
]);
unset($GLOBALS['BE_MOD']['system']['undo']);
ArrayUtil::arrayInsert(
    $GLOBALS['BE_MOD']['content'],
    array_search('article', array_keys($GLOBALS['BE_MOD']['content']), true) + 1,
    [
        'wem_sg_social_link' => [
            'callback' => "\WEM\SmartgearBundle\Backend\SocialLink",
        ],
    ]
);
ArrayUtil::arrayInsert(
    $GLOBALS['BE_MOD']['content'],
    array_search('form', array_keys($GLOBALS['BE_MOD']['content']), true) + 1,
    [
        'wem_sg_form_data_manager' => [
            'tables' => ['tl_sm_form_storage', 'tl_sm_form_storage_data'],
            'export_all' => ['smartgear.backend.module.form_data_manager.backend_controller', 'exportAll'],
            'export' => ['smartgear.backend.module.form_data_manager.backend_controller', 'exportSingle'],
        ],
    ]
);
$GLOBALS['BE_MOD']['content']['form']['export_all'] = ['smartgear.backend.module.form_data_manager.backend_controller', 'exportAllFromForm'];
$GLOBALS['BE_MOD']['content']['form']['export'] = ['smartgear.backend.module.form_data_manager.backend_controller', 'exportSingle'];

/*
 * Frontend modules
 */
ArrayUtil::arrayInsert($GLOBALS['FE_MOD'], 2, [
    'smartgear' => [
        'wem_sg_header' => '\WEM\SmartgearBundle\Module\Header',
        'wem_sg_social_link' => '\WEM\SmartgearBundle\Module\SocialLink',
    ],
]);
$GLOBALS['FE_MOD']['news']['newsreader'] = \WEM\SmartgearBundle\Override\ModuleNewsReader::class;
$GLOBALS['FE_MOD']['news']['newslist'] = \WEM\SmartgearBundle\Override\ModuleNewsList::class;
$GLOBALS['FE_MOD']['events']['eventreader'] = \WEM\SmartgearBundle\Override\ModuleEventReader::class;
$GLOBALS['FE_MOD']['events']['eventlist'] = \WEM\SmartgearBundle\Override\ModuleEventList::class;
$GLOBALS['FE_MOD']['events']['calendar'] = \WEM\SmartgearBundle\Override\ModuleCalendar::class;
$GLOBALS['FE_MOD']['user']['login'] = \WEM\SmartgearBundle\Override\ModuleLogin::class;
$GLOBALS['FE_MOD']['navigationMenu']['breadcrumb'] = \WEM\SmartgearBundle\Override\ModuleBreadcrumb::class;
/*
 * Models
 */
$GLOBALS['TL_MODELS'][\WEM\SmartgearBundle\Model\Backup::getTable()] = WEM\SmartgearBundle\Model\Backup::class;
$GLOBALS['TL_MODELS'][\WEM\SmartgearBundle\Model\SocialNetworkCategory::getTable()] = WEM\SmartgearBundle\Model\SocialNetworkCategory::class;
$GLOBALS['TL_MODELS'][\WEM\SmartgearBundle\Model\SocialNetwork::getTable()] = WEM\SmartgearBundle\Model\SocialNetwork::class;
$GLOBALS['TL_MODELS'][\WEM\SmartgearBundle\Model\SocialLink::getTable()] = WEM\SmartgearBundle\Model\SocialLink::class;
$GLOBALS['TL_MODELS'][\WEM\SmartgearBundle\Model\Member::getTable()] = WEM\SmartgearBundle\Model\Member::class;
$GLOBALS['TL_MODELS'][\WEM\SmartgearBundle\Model\FormStorage::getTable()] = WEM\SmartgearBundle\Model\FormStorage::class;
$GLOBALS['TL_MODELS'][\WEM\SmartgearBundle\Model\FormStorageData::getTable()] = WEM\SmartgearBundle\Model\FormStorageData::class;
$GLOBALS['TL_MODELS'][\WEM\SmartgearBundle\Model\PageVisit::getTable()] = WEM\SmartgearBundle\Model\PageVisit::class;
$GLOBALS['TL_MODELS'][\WEM\SmartgearBundle\Model\Login::getTable()] = WEM\SmartgearBundle\Model\Login::class;
$GLOBALS['TL_MODELS'][\WEM\SmartgearBundle\Model\Configuration\Configuration::getTable()] = WEM\SmartgearBundle\Model\Configuration\Configuration::class;
$GLOBALS['TL_MODELS'][\WEM\SmartgearBundle\Model\Configuration\ConfigurationItem::getTable()] = WEM\SmartgearBundle\Model\Configuration\ConfigurationItem::class;
/*
 * Add BE Hooks
 */
if ('BE' === TL_MODE) {
    $GLOBALS['TL_HOOKS']['executePreActions'][] = ['\WEM\SmartgearBundle\Backend\Smartgear', 'processAjaxRequest'];
    $GLOBALS['TL_HOOKS']['executePreActions'][] = ['\WEM\SmartgearBundle\Backend\Dashboard', 'processAjaxRequest'];
    $GLOBALS['TL_HOOKS']['executePreActions'][] = ['\WEM\SmartgearBundle\Backend\Reminder', 'processAjaxRequest'];
    $GLOBALS['TL_HOOKS']['loadDataContainer'][] = ['smartgear.listener.load_data_container', '__invoke'];
    $GLOBALS['TL_HOOKS']['initializeSystem'][] = ['smartgear.listener.initialize_system', '__invoke'];
    $GLOBALS['TL_HOOKS']['replaceInsertTags'][] = ['smartgear.listener.replace_insert_tags', 'onReplaceInsertTags'];
    $GLOBALS['TL_HOOKS']['generatePage'][] = ['smartgear.listener.generate_page', '__invoke'];
    // Style Manager hooks
    $GLOBALS['TL_HOOKS']['styleManagerWidgetComponentStyleSelectGetStyleManagerArchiveModelCollection'][] = ['smartgear.listener.style_manager.widget_component_style_select_get_style_manager_archive_model_collection', '__invoke'];
    $GLOBALS['TL_HOOKS']['styleManagerWidgetComponentStyleSelectGetStyleManagerModelCollection'][] = ['smartgear.listener.style_manager.widget_component_style_select_get_style_manager_model_collection', '__invoke'];
}

/*
 * Add FE Hooks
 */
if ('FE' === TL_MODE) {
    // $GLOBALS['TL_HOOKS']['getPageLayout'][] = ['\WEM\SmartgearBundle\Hooks\GetPageLayoutHook', 'generateApiToken'];
    // $GLOBALS['TL_HOOKS']['executePreActions'][] = ['\WEM\SmartgearBundle\Hooks\ExecutePreActionsHook', 'catchApiRequests'];
    $GLOBALS['TL_HOOKS']['generateFrontendUrl'][] = ['smartgear.listener.generate_frontend_url', '__invoke'];
    $GLOBALS['TL_HOOKS']['generateBreadcrumb'][] = ['smartgear.listener.generate_breadcrumb', '__invoke'];
    $GLOBALS['TL_HOOKS']['parseTemplate'][] = ['smartgear.listener.parse_template', '__invoke'];
    $GLOBALS['TL_HOOKS']['replaceInsertTags'][] = ['smartgear.listener.replace_insert_tags', 'onReplaceInsertTags'];
    $GLOBALS['TL_HOOKS']['initializeSystem'][] = ['smartgear.listener.initialize_system', '__invoke'];
    $GLOBALS['TL_HOOKS']['newsListFetchItems'][] = ['smartgear.listener.news_list_fetch_items', '__invoke'];
    $GLOBALS['TL_HOOKS']['newsListCountItems'][] = ['smartgear.listener.news_list_count_items', '__invoke'];
    $GLOBALS['TL_HOOKS']['getAllEvents'][] = ['smartgear.listener.get_all_events', '__invoke'];
    $GLOBALS['TL_HOOKS']['createNewUser'][] = ['smartgear.listener.create_new_user', '__invoke'];
    $GLOBALS['TL_HOOKS']['processFormData'][] = ['smartgear.listener.process_form_data', '__invoke'];
    $GLOBALS['TL_HOOKS']['compileFormFields'][] = ['smartgear.listener.compile_form_fields', '__invoke'];
    $GLOBALS['TL_HOOKS']['generatePage'][] = ['smartgear.listener.generate_page', '__invoke'];
    $GLOBALS['TL_HOOKS']['getContentElement'][] = ['smartgear.listener.get_content_element', '__invoke'];
    $GLOBALS['TL_HOOKS']['getFrontendModule'][] = ['smartgear.listener.get_frontend_module', '__invoke'];
}

/*
 * Add globals Hooks
 */
$GLOBALS['TL_HOOKS']['loadLanguageFile'][] = ['smartgear.listener.load_language_file', '__invoke'];
$GLOBALS['TL_HOOKS']['sendNotificationMessage'][] = ['smartgear.listener.send_notification_message', '__invoke'];

/*
 * NC hooks
 */
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['contao']['core_form']['email_text'][] = 'useful_data';
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['contao']['core_form']['email_text'][] = 'useful_data_filled';
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['contao']['core_form']['email_html'][] = 'useful_data';
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['contao']['core_form']['email_html'][] = 'useful_data_filled';
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['contao']['core_form']['file_content'][] = 'useful_data';
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['contao']['core_form']['file_content'][] = 'useful_data_filled';

/*
 * Notifications
 */
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['smartgear']['ticket_creation'] = [
    'email_sender_name' => ['email_sender_name'],
    'email_sender_address' => ['sg_owner_email'],
    'email_subject' => ['ticket_subject', 'sg_title'],
    'recipients' => ['support_email', 'sg_owner_email'],
    'email_text' => ['ticket_*', 'sg_owner_name'],
    'email_html' => ['ticket_*', 'sg_owner_name'],
    'email_replyTo' => ['sg_owner_email'],
    'attachment_tokens' => ['ticket_file'],
];

/*
 * Add custom rights
 */
$GLOBALS['TL_PERMISSIONS'][] = 'smartgear_permissions';
// PDM EXPORT
$GLOBALS['WEM_HOOKS']['formatSinglePersonalDataForCsvExport'][] = ['smartgear.listener.personal_data_csv_formatter', 'formatSingle'];
$GLOBALS['WEM_HOOKS']['exportByPidAndPtableAndEmail'][] = ['smartgear.listener.personal_data_export', 'exportByPidAndPtableAndEmail'];
// PDM ANONYMIZE
$GLOBALS['WEM_HOOKS']['anonymizeByPidAndPtableAndEmail'][] = ['smartgear.listener.personal_data_anonymize', 'anonymizeByPidAndPtableAndEmail'];
// PDM UI
$GLOBALS['WEM_HOOKS']['sortData'][] = ['smartgear.listener.personal_data_ui', 'sortData'];
$GLOBALS['WEM_HOOKS']['renderSingleItemTitle'][] = ['smartgear.listener.personal_data_ui', 'renderSingleItemTitle'];
$GLOBALS['WEM_HOOKS']['renderSingleItemBodyOriginalModelSingle'][] = ['smartgear.listener.personal_data_ui', 'renderSingleItemBodyOriginalModelSingle'];
$GLOBALS['WEM_HOOKS']['renderSingleItemBodyOriginalModelSingleFieldValue'][] = ['smartgear.listener.personal_data_ui', 'renderSingleItemBodyOriginalModelSingleFieldValue'];
$GLOBALS['WEM_HOOKS']['renderSingleItemBodyPersonalDataSingle'][] = ['smartgear.listener.personal_data_ui', 'renderSingleItemBodyPersonalDataSingle'];
$GLOBALS['WEM_HOOKS']['buildSingleItemBodyPersonalDataSingleButtons'][] = ['smartgear.listener.personal_data_ui', 'buildSingleItemBodyPersonalDataSingleButtons'];
$GLOBALS['WEM_HOOKS']['renderSingleItemBodyPersonalDataSingleFieldLabel'][] = ['smartgear.listener.personal_data_ui', 'renderSingleItemBodyPersonalDataSingleFieldLabel'];
$GLOBALS['WEM_HOOKS']['renderSingleItemBodyPersonalDataSingleFieldValue'][] = ['smartgear.listener.personal_data_ui', 'renderSingleItemBodyPersonalDataSingleFieldValue'];
// PDM Manager
$GLOBALS['WEM_HOOKS']['getFileByPidAndPtableAndEmailAndField'][] = ['smartgear.listener.personal_data_manager', 'getFileByPidAndPtableAndEmailAndField'];
$GLOBALS['WEM_HOOKS']['isPersonalDataLinkedToFile'][] = ['smartgear.listener.personal_data_manager', 'isPersonalDataLinkedToFile'];

// override Contao alias
// class_alias(\WEM\SmartgearBundle\Model\Member::class, 'MemberModel');
