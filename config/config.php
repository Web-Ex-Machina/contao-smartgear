<?php

/**
 * SMARTGEAR for Contao Open Source CMS
 *
 * Copyright (c) 2015-2018 Web ex Machina
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */

/**
 * Move Page Backend Module
 */
array_insert($GLOBALS['BE_MOD']['content'], 0, array
(
	'page' => $GLOBALS['BE_MOD']['design']['page']
));
unset($GLOBALS['BE_MOD']['design']['page']);