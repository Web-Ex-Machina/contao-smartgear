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

use Contao\CoreBundle\DataContainer\PaletteManipulator;

/*
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2022 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

/*
 * Add fields for header component
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'wem_sg_header_content';
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'wem_sg_navigation';
$GLOBALS['TL_DCA']['tl_module']['palettes']['wem_sg_header'] = '
	{title_legend},name,type;
	{config_legend},wem_sg_header_preset,wem_sg_header_above,wem_sg_header_sticky;
	{wemsgheader_legend},wem_sg_header_logo,wem_sg_header_logo_size,wem_sg_header_logo_alt,wem_sg_header_content;
	{nav_legend},wem_sg_navigation;
	{expert_legend:hide},customTpl,cssID
';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['wem_sg_header_content'] = 'wem_sg_header_content_html';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['wem_sg_navigation_module'] = 'wem_sg_navigation_module';

$GLOBALS['TL_DCA']['tl_module']['fields']['wem_sg_header_preset'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['wem_sg_header_preset'],
    'default' => 'classic',
    'exclude' => true,
    'inputType' => 'select',
    'options' => ['classic', 'nav--arrowed'],
    'reference' => &$GLOBALS['TL_LANG']['tl_module']['wem_sg_header_preset'],
    'eval' => ['helpwizard' => true],
    'sql' => "varchar(32) NOT NULL default ''",
];
$GLOBALS['TL_DCA']['tl_module']['fields']['wem_sg_header_above'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['wem_sg_header_above'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50 m12'],
    'sql' => "char(1) NOT NULL default ''",
];
$GLOBALS['TL_DCA']['tl_module']['fields']['wem_sg_header_sticky'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['wem_sg_header_sticky'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50 m12'],
    'sql' => "char(1) NOT NULL default ''",
];
$GLOBALS['TL_DCA']['tl_module']['fields']['wem_sg_header_logo'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['wem_sg_header_logo'],
    'exclude' => true,
    'inputType' => 'fileTree',
    'eval' => ['fieldType' => 'radio', 'filesOnly' => true, 'mandatory' => true, 'tl_class' => 'clr'],
    'sql' => 'binary(16) NULL',
];
$GLOBALS['TL_DCA']['tl_module']['fields']['wem_sg_header_logo_size'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['wem_sg_header_logo_size'],
    'exclude' => true,
    'inputType' => 'imageSize',
    'reference' => &$GLOBALS['TL_LANG']['MSC'],
    'eval' => ['rgxp' => 'natural', 'includeBlankOption' => true, 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'w50'],
    'options_callback' => function () {
        return System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(BackendUser::getInstance());
    },
    'sql' => "varchar(64) NOT NULL default ''",
];
$GLOBALS['TL_DCA']['tl_module']['fields']['wem_sg_header_logo_alt'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['wem_sg_header_logo_alt'],
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
    'sql' => "varchar(255) NOT NULL default ''",
];
$GLOBALS['TL_DCA']['tl_module']['fields']['wem_sg_header_content'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['wem_sg_header_content'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50 clr', 'submitOnChange' => true],
    'sql' => "char(1) NOT NULL default ''",
];
$GLOBALS['TL_DCA']['tl_module']['fields']['wem_sg_header_content_html'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['wem_sg_header_content_html'],
    'exclude' => true,
    'inputType' => 'textarea',
    'eval' => ['allowHtml' => true, 'class' => 'monospace', 'rte' => 'ace|html', 'helpwizard' => true],
    'explanation' => 'insertTags',
    'sql' => 'text NULL',
];
$GLOBALS['TL_DCA']['tl_module']['fields']['wem_sg_navigation'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['wem_sg_navigation'],
    'default' => 'classic',
    'exclude' => true,
    'inputType' => 'radio',
    'options' => ['classic', 'module'],
    'reference' => &$GLOBALS['TL_LANG']['tl_module']['wem_sg_navigation'],
    'eval' => ['submitOnChange' => true],
    'sql' => "varchar(32) NOT NULL default 'classic'",
];
$GLOBALS['TL_DCA']['tl_module']['fields']['wem_sg_navigation_module'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['wem_sg_navigation'],
    'exclude' => true,
    'inputType' => 'select',
    'options_callback' => ['tl_wem_sg_module', 'getModules'],
    'eval' => ['mandatory' => true, 'chosen' => true, 'tl_class' => 'w50 wizard'],
    'wizard' => [
        ['tl_wem_sg_module', 'editModule'],
    ],
    'sql' => "int(10) unsigned NOT NULL default '0'",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['wem_sg_display_share_buttons'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['wem_sg_display_share_buttons'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50 clr', 'submitOnChange' => true],
    'sql' => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['wem_sg_number_of_characters'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['wem_sg_number_of_characters'],
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['maxlength' => 10, 'tl_class' => 'w50'],
    'sql' => 'int NOT NULL default 0',
];

PaletteManipulator::create()
    ->addField('wem_sg_display_share_buttons', 'config_legend')
    ->applyToPalette('newsreader', 'tl_module')
    ->applyToPalette('eventreader', 'tl_module')
    ->applyToPalette('faqpage', 'tl_module')
    ->applyToPalette('faqreader', 'tl_module')
    ->applyToPalette('newsletterreader', 'tl_module')
;
PaletteManipulator::create()
    ->addField('wem_sg_number_of_characters', 'config_legend')
    ->applyToPalette('newsreader', 'tl_module')
    ->applyToPalette('newslist', 'tl_module')
    ->applyToPalette('eventreader', 'tl_module')
    ->applyToPalette('eventlist', 'tl_module')
    ->applyToPalette('faqpage', 'tl_module')
    ->applyToPalette('faqreader', 'tl_module')
    ->applyToPalette('newsletterreader', 'tl_module')
;

class tl_module extends tl_module
{
    /**
     * Return the edit module wizard.
     *
     * @return string
     */
    public function editModule(DataContainer $dc)
    {
        return ($dc->value < 1) ? '' : ' <a href="contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id='.$dc->value.'&amp;popup=1&amp;nb=1&amp;rt='.REQUEST_TOKEN.'" title="'.sprintf(StringUtil::specialchars($GLOBALS['TL_LANG']['tl_content']['editalias'][1]), $dc->value).'" onclick="Backend.openModalIframe({\'title\':\''.StringUtil::specialchars(str_replace("'", "\\'", sprintf($GLOBALS['TL_LANG']['tl_content']['editalias'][1], $dc->value))).'\',\'url\':this.href});return false">'.Image::getHtml('alias.svg', $GLOBALS['TL_LANG']['tl_content']['editalias'][0]).'</a>';
    }

    /**
     * Get all modules and return them as array.
     *
     * @return array
     */
    public function getModules()
    {
        $arrModules = [];
        $objModules = $this->Database->execute(sprintf('SELECT m.id, m.name, t.name AS theme FROM tl_module m LEFT JOIN tl_theme t ON m.pid=t.id WHERE m.id != %s ORDER BY t.name, m.name', \Input::get('id')));

        while ($objModules->next()) {
            $arrModules[$objModules->theme][$objModules->id] = $objModules->name.' (ID '.$objModules->id.')';
        }

        return $arrModules;
    }
}
