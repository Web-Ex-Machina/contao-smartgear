<?php

/**
 * SMARTGEAR for Contao Open Source CMS
 *
 * Copyright (c) 2015-2018 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

// Load icon in Contao 4.2 backend
if ('BE' === TL_MODE) {
    $GLOBALS['TL_CSS'][] = 'system/modules/wem-contao-smartgear/assets/backend/backend.css';
}

// Load Contao Bundles
$bundles = \System::getContainer()->getParameter('kernel.bundles');

/**
 * Move Page Backend Module
 */
array_insert($GLOBALS['BE_MOD']['content'], 0, array(
    'page' => $GLOBALS['BE_MOD']['design']['page']
));
unset($GLOBALS['BE_MOD']['design']['page']);

/**
 * Move Files Backend Module
 */
array_insert($GLOBALS['BE_MOD']['content'], 99, array(
    'files' => $GLOBALS['BE_MOD']['system']['files']
));
unset($GLOBALS['BE_MOD']['system']['files']);

/**
 * Move Newsletter Backend Module
 */
if (isset($bundles['ContaoNewsletterBundle'])) {
    array_insert($GLOBALS['BE_MOD'], 1, array(
        'newsletters' => array(
            'channels' => $GLOBALS['BE_MOD']['content']['newsletter'],
            'newsletter' => array(
                'tables'     => array('tl_newsletter'),
                'send'       => array('\WEM\SmartGear\Override\Newsletter', 'send'),
                'stylesheet' => 'bundles/contaonewsletter/style.css'
            )
        )
    ));
    unset($GLOBALS['BE_MOD']['content']['newsletter']);
}

/**
 * Move Multilingual pages
 */
if (isset($bundles['VerstaerkerI18nl10nBundle'])) {
    array_insert(
        $GLOBALS['BE_MOD']['content'],
        array_search('page', array_keys($GLOBALS['BE_MOD']['content'])) + 1,
        array(
            'i18nl10n' => $GLOBALS['BE_MOD']['design']['i18nl10n']
        )
    );
    unset($GLOBALS['BE_MOD']['design']['i18nl10n']);
}

/**
 * Backend modules
 */
array_insert($GLOBALS['BE_MOD']['system'], 0, array(
    'smartgear' => array(
        'callback' => "\WEM\SmartGear\Backend\Install"
    )
));

/**
 * Frontend modules
 */
array_insert($GLOBALS['FE_MOD'], 2, array(
    'smartgear' => array(
        'wem_sg_header'         => '\WEM\SmartGear\Module\Header',
    )
));

/**
 * Add BE Hooks
 */
if ('BE' === TL_MODE) {
    $GLOBALS['TL_HOOKS']['executePreActions'][] = array('\WEM\SmartGear\Backend\Install', 'processAjaxRequest');
}

/**
 * Add FE Hooks
 */
if ('FE' === TL_MODE) {
    $GLOBALS['TL_HOOKS']['generateFrontendUrl'][] = array('\WEM\SmartGear\Hooks\GenerateFrontendUrlHook', 'generateFrontendUrl');
}