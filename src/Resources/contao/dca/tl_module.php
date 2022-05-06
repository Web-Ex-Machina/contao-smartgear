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
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\CoreBundle\Security\ContaoCorePermissions;
use Contao\Image;
use Contao\Input;
use Contao\System;

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

$GLOBALS['TL_DCA']['tl_module']['list']['operations']['delete']['button_callback'] = ['tl_wem_sg_module', 'deleteModule'];
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'wem_sg_header_content';
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'wem_sg_navigation';
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'wem_sg_display_share_buttons';

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
    'eval' => ['maxlength' => 4, 'tl_class' => 'w50'],
    'sql' => "int(10) unsigned NOT NULL default '0'",
];

$paletteManipulator = PaletteManipulator::create()
    ->addField('wem_sg_display_share_buttons', 'config_legend')
;
$palettesToUpdate = [
    'newsreader',
    'eventreader',
    'faqpage',
    'faqreader',
    'newsletterreader',
];

foreach ($palettesToUpdate as $paletteName) {
    if (\array_key_exists($paletteName, $GLOBALS['TL_DCA']['tl_module']['palettes'])) {
        $paletteManipulator->applyToPalette($paletteName, 'tl_module');
    }
}

$paletteManipulator = PaletteManipulator::create()
    ->addField('wem_sg_number_of_characters', 'config_legend')
;
$palettesToUpdate = [
    'newsreader',
    'newslist',
    'eventreader',
    'eventlist',
    'faqpage',
    'faqreader',
    'newsletterreader',
];
foreach ($palettesToUpdate as $paletteName) {
    if (\array_key_exists($paletteName, $GLOBALS['TL_DCA']['tl_module']['palettes'])) {
        $paletteManipulator->applyToPalette($paletteName, 'tl_module');
    }
}

class tl_wem_sg_module extends tl_module
{
    /**
     * Check permissions to edit table tl_modules.
     *
     * @throws AccessDeniedException
     */
    public function checkPermission(): void
    {
        parent::checkPermission();

        // Check current action
        switch (Input::get('act')) {
            case 'delete':
                if ($this->isModuleUsedBySmartgear((int) Input::get('id'))) {
                    throw new AccessDeniedException('Not enough permissions to '.Input::get('act').' module ID '.Input::get('id').'.');
                }
            break;
        }
    }

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

    /**
     * Return the delete module button.
     *
     * @param array  $row
     * @param string $href
     * @param string $label
     * @param string $title
     * @param string $icon
     * @param string $attributes
     *
     * @return string
     */
    public function deleteModule($row, $href, $label, $title, $icon, $attributes)
    {
        if ($this->isModuleUsedBySmartgear((int) $row['id'])) {
            return Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon));
        }

        return System::getContainer()->get('security.helper')->isGranted(ContaoCorePermissions::USER_CAN_ACCESS_FRONTEND_MODULES) ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ' : Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
    }

    /**
     * Check if the calendar is being used by Smartgear.
     *
     * @param int $id News archive's ID
     */
    protected function isModuleUsedBySmartgear(int $id): bool
    {
        $configManager = System::getContainer()->get('smartgear.config.manager.core');
        try {
            $config = $configManager->load();
            if ($config->getSgInstallComplete()) {
                $modules = $config->getSgModules();
                foreach ($modules as $module) {
                    if ($id === (int) $module->id) {
                        return true;
                    }
                }
            }
            $blogConfig = $config->getSgBlog();
            if ($blogConfig->getSgInstallComplete()
            && ($id === (int) $blogConfig->getSgModuleList() || $id === (int) $blogConfig->getSgModuleReader())
            ) {
                return true;
            }
            $eventsConfig = $config->getSgEvents();
            if ($eventsConfig->getSgInstallComplete()
            && ($id === (int) $eventsConfig->getSgModuleList() || $id === (int) $eventsConfig->getSgModuleReader() || $id === (int) $eventsConfig->getSgModuleCalendar())
            ) {
                return true;
            }
        } catch (\Exception $e) {
        }

        return false;
    }
}
