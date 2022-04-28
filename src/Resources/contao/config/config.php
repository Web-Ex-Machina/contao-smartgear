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

// Load icon in Contao 4.2 backend
if ('BE' === TL_MODE) {
    $GLOBALS['TL_CSS'][] = 'bundles/wemsmartgear/backend/backend.css';
}

// Load Contao Bundles
$bundles = \System::getContainer()->getParameter('kernel.bundles');

/*
 * Move Page Backend Module
 */
array_insert($GLOBALS['BE_MOD']['content'], 0, [
    'page' => $GLOBALS['BE_MOD']['design']['page'],
]);
unset($GLOBALS['BE_MOD']['design']['page']);

/*
 * Move Files Backend Module
 */
array_insert($GLOBALS['BE_MOD']['content'], 99, [
    'files' => $GLOBALS['BE_MOD']['system']['files'],
]);
unset($GLOBALS['BE_MOD']['system']['files']);

/*
 * Move Newsletter Backend Module
 */
if (isset($bundles['ContaoNewsletterBundle'])) {
    array_insert($GLOBALS['BE_MOD'], 1, [
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
    array_insert(
        $GLOBALS['BE_MOD']['content'],
        array_search('page', array_keys($GLOBALS['BE_MOD']['content']), true) + 1,
        [
            'i18nl10n' => $GLOBALS['BE_MOD']['design']['i18nl10n'],
        ]
    );
    unset($GLOBALS['BE_MOD']['design']['i18nl10n']);
}

// ComponentStyleSelect override
$GLOBALS['BE_FFL']['stylemanager'] = WEM\SmartgearBundle\Widget\ComponentStyleSelect::class;
/*
 * Backend modules
 */
array_insert($GLOBALS['BE_MOD']['system'], 0, [
    'smartgear' => [
        'callback' => "\WEM\SmartgearBundle\Backend\Smartgear",
    ],
]);

/*
 * Frontend modules
 */
array_insert($GLOBALS['FE_MOD'], 2, [
    'smartgear' => [
        'wem_sg_header' => '\WEM\SmartgearBundle\Module\Header',
    ],
]);
$GLOBALS['FE_MOD']['news']['newsreader'] = \WEM\SmartgearBundle\Override\ModuleNewsReader::class;
/*
 * Models
 */
$GLOBALS['TL_MODELS'][WEM\SmartgearBundle\Model\Backup::getTable()] = WEM\SmartgearBundle\Model\Backup::class;

/*
 * Add BE Hooks
 */
if ('BE' === TL_MODE) {
    $GLOBALS['TL_HOOKS']['executePreActions'][] = ['\WEM\SmartgearBundle\Backend\Smartgear', 'processAjaxRequest'];
    $GLOBALS['TL_HOOKS']['loadDataContainer'][] = ['smartgear.listener.load_data_container', '__invoke'];
    $GLOBALS['TL_HOOKS']['initializeSystem'][] = ['smartgear.listener.initialize_system', '__invoke'];
}

/*
 * Add FE Hooks
 */
if ('FE' === TL_MODE) {
    $GLOBALS['TL_HOOKS']['getPageLayout'][] = ['\WEM\SmartgearBundle\Hooks\GetPageLayoutHook', 'generateApiToken'];
    $GLOBALS['TL_HOOKS']['executePreActions'][] = ['\WEM\SmartgearBundle\Hooks\ExecutePreActionsHook', 'catchApiRequests'];
    $GLOBALS['TL_HOOKS']['generateFrontendUrl'][] = ['\WEM\SmartgearBundle\Hooks\GenerateFrontendUrlHook', 'generateFrontendUrl'];
    $GLOBALS['TL_HOOKS']['generateBreadcrumb'][] = ['smartgear.listener.generate_breadcrumb', '__invoke'];
    $GLOBALS['TL_HOOKS']['parseTemplate'][] = ['\WEM\SmartgearBundle\Hooks\ParseTemplateHook', 'overrideDefaultTemplate'];
    $GLOBALS['TL_HOOKS']['replaceInsertTags'][] = ['smartgear.listener.replace_insert_tags', 'onReplaceInsertTags'];
    $GLOBALS['TL_HOOKS']['initializeSystem'][] = ['smartgear.listener.initialize_system', '__invoke'];
}

/*
 * Add custom rights
 */
$GLOBALS['TL_PERMISSIONS'][] = 'smartgear';
