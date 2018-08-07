<?php

/**
 * SMARTGEAR for Contao Open Source CMS
 *
 * Copyright (c) 2015-2018 Web ex Machina
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */

/**
 * Remove PID from tl_newsletter
 */
unset($GLOBALS['TL_DCA']['tl_newsletter']['config']['ptable']);
unset($GLOBALS['TL_DCA']['tl_newsletter']['config']['sql']['keys']['pid']);
unset($GLOBALS['TL_DCA']['tl_newsletter']['fields']['pid']);

$GLOBALS['TL_DCA']['tl_newsletter']['list']['sorting']['mode'] = 1;
$GLOBALS['TL_DCA']['tl_newsletter']['list']['sorting']['flag'] = 1;
$GLOBALS['TL_DCA']['tl_newsletter']['list']['label']['fields'] = array('subject');
$GLOBALS['TL_DCA']['tl_newsletter']['list']['label']['format'] = '%s';
$GLOBALS['TL_DCA']['tl_newsletter']['list']['label']['label_callback'] = $GLOBALS['TL_DCA']['tl_newsletter']['list']['sorting']['child_record_callback'];

$GLOBALS['TL_DCA']['tl_newsletter']['palettes']['default'] = str_replace('alias','alias,channels', $GLOBALS['TL_DCA']['tl_newsletter']['palettes']['default']);

$GLOBALS['TL_DCA']['tl_newsletter']['fields']['channels'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_newsletter']['channels'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'foreignKey'              => 'tl_newsletter_channel.title',
	'eval'                    => array('chosen'=>true, 'multiple'=>true, 'tl_class'=>'clr'),
	'sql'                     => "blob NULL"
);