<?php

/**
 * SMARTGEAR for Contao Open Source CMS
 *
 * Copyright (c) 2015-2018 Web ex Machina
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */

// Load icon in Contao 4.2 backend
if ('BE' === TL_MODE) {
    $GLOBALS['TL_CSS'][] = 'system/modules/wem-contao-smartgear/assets/backend/backend.css';
}

$bundles = \System::getContainer()->getParameter('kernel.bundles');

/**
 * Move Page Backend Module
 */
array_insert($GLOBALS['BE_MOD']['content'], 0, array
(
	'page' => $GLOBALS['BE_MOD']['design']['page']
));
unset($GLOBALS['BE_MOD']['design']['page']);

/**
 * Move Files Backend Module
 */
array_insert($GLOBALS['BE_MOD']['content'], 99, array
(
	'files' => $GLOBALS['BE_MOD']['system']['files']
));
unset($GLOBALS['BE_MOD']['system']['files']);

/**
 * Move Newsletter Backend Module
 */
if(isset($bundles['ContaoNewsletterBundle'])){
	array_insert($GLOBALS['BE_MOD'], 1, array(
		'newsletters' => array(
			'channels' => $GLOBALS['BE_MOD']['content']['newsletter'],
			'newsletter' => array(
				'tables' 	 => array('tl_newsletter'),
				'send'       => array('\WEM\SmartGear\Override\Newsletter', 'send'),
				'stylesheet' => 'bundles/contaonewsletter/style.css'
			)
		)
	));
	unset($GLOBALS['BE_MOD']['content']['newsletter']);
}

/**
 * Add SmartGear Module to System
 */
array_insert($GLOBALS['BE_MOD']['system'], 0, array
(
	'smartgear' => array
	(
		'callback' => "\WEM\SmartGear\Backend\Install"
	)
));