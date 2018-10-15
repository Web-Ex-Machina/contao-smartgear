<?php

/**
 * SMARTGEAR for Contao Open Source CMS
 *
 * Copyright (c) 2015-2018 Web ex Machina
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */

/**
 * Add fields for header component
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'wem_sg_header_content';
$GLOBALS['TL_DCA']['tl_module']['palettes']['wem_sg_header']    = '
	{title_legend},name,type;
	{config_legend},wem_sg_header_preset,wem_sg_header_above,wem_sg_header_sticky;
	{wemsgheader_legend},wem_sg_header_logo,wem_sg_header_logo_size,wem_sg_header_logo_alt,wem_sg_header_content;
	{nav_legend},wem_sg_navigation;
	{expert_legend:hide},customTpl,cssID
';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['wem_sg_header_content'] = 'wem_sg_header_content_html';

$GLOBALS['TL_DCA']['tl_module']['fields']['wem_sg_header_preset'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['wem_sg_header_preset'],
	'default'                 => 'classic',
	'exclude'                 => true,
	'inputType'               => 'select',
	'options'                 => array('classic', 'arrowed'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_module']['wem_sg_header_preset'],
	'eval'                    => array('helpwizard'=>true),
	'sql'                     => "varchar(32) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['wem_sg_header_above'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['wem_sg_header_above'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50 m12'),
	'sql'                     => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['wem_sg_header_sticky'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['wem_sg_header_sticky'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50 m12'),
	'sql'                     => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['wem_sg_header_logo'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['wem_sg_header_logo'],
	'exclude'                 => true,
	'inputType'               => 'fileTree',
	'eval'                    => array('fieldType'=>'radio', 'filesOnly'=>true, 'mandatory'=>true, 'tl_class'=>'clr'),
	'sql'                     => "binary(16) NULL"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['wem_sg_header_logo_size'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['wem_sg_header_logo_size'],
	'exclude'                 => true,
	'inputType'               => 'imageSize',
	'reference'               => &$GLOBALS['TL_LANG']['MSC'],
	'eval'                    => array('rgxp'=>'natural', 'includeBlankOption'=>true, 'nospace'=>true, 'helpwizard'=>true, 'tl_class'=>'w50'),
	'options_callback' => function (){
		return System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(BackendUser::getInstance());
	},
	'sql'                     => "varchar(64) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['wem_sg_header_logo_alt'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['wem_sg_header_logo_alt'],
	'exclude'                  => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['wem_sg_header_content'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['wem_sg_header_content'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50 clr', 'submitOnChange'=>true),
	'sql'                     => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['wem_sg_header_content_html'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['wem_sg_header_content_html'],
	'exclude'                 => true,
	'inputType'               => 'textarea',
	'eval'                    => array('allowHtml'=>true, 'class'=>'monospace', 'rte'=>'ace|html', 'helpwizard'=>true),
	'explanation'             => 'insertTags',
	'sql'                     => "text NULL"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['wem_sg_navigation'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['wem_sg_navigation'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('tl_wem_sg_module', 'getModules'),
	'eval'                    => array('mandatory'=>true, 'chosen'=>true, 'tl_class'=>'w50 wizard'),
	'wizard' 				  => array(
		array('tl_wem_sg_module', 'editModule')
	),
	'sql'                     => "int(10) unsigned NOT NULL default '0'"
);

class tl_wem_sg_module extends tl_module
{
	/**
	 * Return the edit module wizard
	 *
	 * @param DataContainer $dc
	 *
	 * @return string
	 */
	public function editModule(DataContainer $dc)
	{
		return ($dc->value < 1) ? '' : ' <a href="contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $dc->value . '&amp;popup=1&amp;nb=1&amp;rt=' . REQUEST_TOKEN . '" title="' . sprintf(StringUtil::specialchars($GLOBALS['TL_LANG']['tl_content']['editalias'][1]), $dc->value) . '" onclick="Backend.openModalIframe({\'title\':\'' . StringUtil::specialchars(str_replace("'", "\\'", sprintf($GLOBALS['TL_LANG']['tl_content']['editalias'][1], $dc->value))) . '\',\'url\':this.href});return false">' . Image::getHtml('alias.svg', $GLOBALS['TL_LANG']['tl_content']['editalias'][0]) . '</a>';
	}

	/**
	 * Get all modules and return them as array
	 *
	 * @return array
	 */
	public function getModules()
	{
		$arrModules = array();
		$objModules = $this->Database->execute(sprintf("SELECT m.id, m.name, t.name AS theme FROM tl_module m LEFT JOIN tl_theme t ON m.pid=t.id WHERE m.id != %s ORDER BY t.name, m.name", \Input::get('id')));

		while ($objModules->next())
		{
			$arrModules[$objModules->theme][$objModules->id] = $objModules->name . ' (ID ' . $objModules->id . ')';
		}

		return $arrModules;
	}
}