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
use WEM\SmartgearBundle\Classes\Dca\Manipulator as DCAManipulator;
use WEM\SmartgearBundle\DataContainer\Module as ModuleDCA;

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
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'wem_sg_navigation';
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'wem_sg_display_share_buttons';
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'wem_sg_header_add_catchline';
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'wem_sg_header_add_search';
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'wem_sg_header_add_lang_selector';
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'wem_sg_header_add_topbar';
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'wem_sg_header_add_postnav_content';
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'wem_sg_breadcrumb_auto_placement';

$GLOBALS['TL_DCA']['tl_module']['palettes']['wem_sg_header'] = '
{wem_sg_header_logo_legend},singleSRC,imgSize,wem_sg_header_alt,wem_sg_header_add_catchline;
{wem_sg_header_nav_legend},wem_sg_header_nav_module,wem_sg_header_nav_position,wem_sg_header_panel_position,wem_sg_header_add_search,wem_sg_header_add_lang_selector;
{wem_sg_header_config_legend},wem_sg_header_sticky,wem_sg_header_add_topbar,wem_sg_header_add_postnav_content;
{wem_sg_header_appearance_legend},wem_sg_header_width,wem_sg_header_background,wem_sg_header_bottom_style,wem_sg_header_hover_style;
{expert_legend:hide},customTpl,cssID
';

$GLOBALS['TL_DCA']['tl_module']['subpalettes']['wem_sg_navigation_module'] = 'wem_sg_navigation_module';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['wem_sg_header_add_catchline'] = 'wem_sg_header_catchline';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['wem_sg_header_add_search'] = 'wem_sg_header_search_page,wem_sg_header_search_bg,wem_sg_header_search_parameter';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['wem_sg_header_add_lang_selector'] = 'wem_sg_header_lang_selector_module,wem_sg_header_lang_selector_bg';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['wem_sg_header_add_topbar'] = 'wem_sg_header_topbar,wem_sg_header_topbar_bg';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['wem_sg_header_add_postnav_content'] = 'wem_sg_header_postnav_content';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['wem_sg_breadcrumb_auto_placement'] = 'wem_sg_breadcrumb_auto_placement_after_content_elements,wem_sg_breadcrumb_auto_placement_after_modules';
DCAManipulator::create('tl_module')
    ->addField('wem_sg_navigation', [
        'label' => &$GLOBALS['TL_LANG']['tl_module']['wem_sg_navigation'],
        'default' => 'classic',
        'exclude' => true,
        'inputType' => 'radio',
        'options' => ['classic', 'module'],
        'reference' => &$GLOBALS['TL_LANG']['tl_module']['wem_sg_navigation'],
        'eval' => ['submitOnChange' => true],
        'sql' => "varchar(32) NOT NULL default 'classic'",
    ])
    ->addField('wem_sg_navigation_module', [
        'label' => &$GLOBALS['TL_LANG']['tl_module']['wem_sg_navigation'],
        'exclude' => true,
        'inputType' => 'select',
        'options_callback' => [ModuleDCA::class, 'getModules'],
        'eval' => ['mandatory' => true, 'chosen' => true, 'tl_class' => 'w50 wizard'],
        'wizard' => [
            [ModuleDCA::class, 'editModule'],
        ],
        'sql' => "int(10) unsigned NOT NULL default '0'",
    ])
    ->addField('wem_sg_display_share_buttons', [
        'label' => &$GLOBALS['TL_LANG']['tl_module']['wem_sg_display_share_buttons'],
        'exclude' => true,
        'inputType' => 'checkbox',
        'eval' => ['tl_class' => 'w50 clr', 'submitOnChange' => true],
        'sql' => "char(1) NOT NULL default ''",
    ])
    ->addField('wem_sg_number_of_characters', [
        'label' => &$GLOBALS['TL_LANG']['tl_module']['wem_sg_number_of_characters'],
        'exclude' => true,
        'inputType' => 'text',
        'eval' => ['maxlength' => 4, 'tl_class' => 'w50'],
        'sql' => "int(10) unsigned NOT NULL default '0'",
    ])
    ->addField('wem_sg_header_alt', [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['header']['header_alt'],
        'inputType' => 'text',
        'eval' => ['tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ])
    ->addField('wem_sg_header_add_catchline', [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['header']['add_catchline'],
        'inputType' => 'checkbox',
        'eval' => ['tl_class' => 'w50 clr m12', 'submitOnChange' => true],
        'sql' => "CHAR(1) NOT NULL default '0'",
    ])
    ->addField('wem_sg_header_catchline', [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['header']['catchline'],
        'inputType' => 'text',
        'eval' => ['mandatory' => false, 'tl_class' => 'clr', 'rte' => 'ace|html'],
        'dependsOn' => [
            'field' => 'add_catchline',
        ],
        'sql' => 'TEXT NULL',
    ])
    ->addField('wem_sg_header_nav_module', [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['header']['nav_custom_module'],
        'inputType' => 'select',
        'options_callback' => [WEM\SmartgearBundle\DataContainer\Content::class, 'getModules'],
        'eval' => ['mandatory' => true, 'tl_class' => 'w50 wizard', 'includeBlankOption' => true],
        // 'wizard' => [['tl_content', 'editModule']],  // doesn't seem to work
        'sql' => "int(10) unsigned NOT NULL default '0'",
    ])
    ->addField('wem_sg_header_nav_position', [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['header']['nav_position'],
        'inputType' => 'select',
        'options' => [
            'left' => &$GLOBALS['TL_LANG']['tl_content']['alignment']['left'],
            'center' => &$GLOBALS['TL_LANG']['tl_content']['alignment']['center'],
            'right' => &$GLOBALS['TL_LANG']['tl_content']['alignment']['right'],
        ],
        'default' => 'right',
        'eval' => ['tl_class' => 'w50 clr'],
        'sql' => "varchar(255) NOT NULL default ''",
    ])
    ->addField('wem_sg_header_panel_position', [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['header']['panel_position'],
        'inputType' => 'select',
        'options' => [
            'left' => &$GLOBALS['TL_LANG']['tl_content']['alignment']['left'],
            'right' => &$GLOBALS['TL_LANG']['tl_content']['alignment']['right'],
        ],
        'default' => 'right',
        'eval' => ['tl_class' => 'w50 '],
        'sql' => "varchar(255) NOT NULL default ''",
    ])
    ->addField('wem_sg_header_add_search', [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['header']['add_search'],
        'inputType' => 'checkbox',
        'eval' => ['tl_class' => 'w50 clr m12 cbx', 'submitOnChange' => true],
        'sql' => "CHAR(1) NOT NULL default '0'",
    ])
    ->addField('wem_sg_header_search_page', [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['header']['search_page'],
        'inputType' => 'pageTree',
        'eval' => ['rgxp' => 'url', 'tl_class' => 'w50 wizard clr', 'mandatory' => true],
        'wizard' => [[WEM\SmartgearBundle\DataContainer\Content::class, 'pagePicker']],
        'dependsOn' => [
            'field' => 'add_search',
        ],
        'sql' => "int(10) unsigned NOT NULL default '0'",
    ])
    ->addField('wem_sg_header_search_bg', [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['header']['search_bg'],
        'inputType' => 'select',
        'options' => \WEM\SmartgearBundle\Classes\Util::getSmartgearColors(),
        'eval' => ['tl_class' => 'w50 ', 'includeBlankOption' => true],
        'dependsOn' => [
            'field' => 'add_search',
        ],
        'sql' => "varchar(255) NOT NULL default ''",
    ])
    ->addField('wem_sg_header_search_parameter', [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['header']['search_parameter'],
        'inputType' => 'text',
        'eval' => ['tl_class' => 'w50 clr', 'mandatory' => true],
        'default' => 'keywords',
        'dependsOn' => [
            'field' => 'add_search',
        ],
        'sql' => 'TEXT NULL',
    ])
    ->addField('wem_sg_header_add_lang_selector', [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['header']['add_lang_selector'],
        'inputType' => 'checkbox',
        'eval' => ['tl_class' => 'w50 clr m12 cbx', 'submitOnChange' => true],
        'sql' => "CHAR(1) NOT NULL default '0'",
    ])
    ->addField('wem_sg_header_lang_selector_module', [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['header']['lang_selector_module'],
        'inputType' => 'select',
        'options_callback' => [WEM\SmartgearBundle\DataContainer\Content::class, 'getModules'],
        'eval' => ['mandatory' => true, 'tl_class' => 'w50 clr wizard', 'includeBlankOption' => true],
        'dependsOn' => [
            'field' => 'add_lang_selector',
        ],
        'sql' => "int(10) unsigned NOT NULL default '0'",
    ])
    ->addField('wem_sg_header_lang_selector_bg', [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['header']['lang_selector_bg'],
        'inputType' => 'select',
        'options' => \WEM\SmartgearBundle\Classes\Util::getSmartgearColors(),
        'eval' => ['tl_class' => 'w50 ', 'includeBlankOption' => true],
        'dependsOn' => [
            'field' => 'add_lang_selector',
        ],
        'sql' => "varchar(255) NOT NULL default ''",
    ])
    ->addField('wem_sg_header_sticky', [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['header']['sticky'],
        'inputType' => 'checkbox',
        'default' => true,
        'eval' => ['tl_class' => 'w50 clr'],
        'sql' => "CHAR(1) NOT NULL default '0'",
    ])
    ->addField('wem_sg_header_add_topbar', [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['header']['add_topbar'],
        'inputType' => 'checkbox',
        'eval' => ['tl_class' => 'w50 clr ', 'submitOnChange' => true],
        'sql' => "CHAR(1) NOT NULL default '0'",
    ])
    ->addField('wem_sg_header_topbar', [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['header']['topbar'],
        'inputType' => 'text',
        'eval' => ['mandatory' => false, 'tl_class' => 'clr w50', 'rte' => 'ace|html'],
        'dependsOn' => [
            'field' => 'add_topbar',
        ],
        'sql' => 'TEXT NULL',
    ])
    ->addField('wem_sg_header_topbar_bg', [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['header']['topbar_bg'],
        'inputType' => 'select',
        'options' => \WEM\SmartgearBundle\Classes\Util::getSmartgearColors(),
        'eval' => ['tl_class' => 'w50 ', 'includeBlankOption' => true],
        'dependsOn' => [
            'field' => 'add_topbar',
        ],
        'sql' => "varchar(255) NOT NULL default ''",
    ])
    ->addField('wem_sg_header_add_postnav_content', [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['header']['add_postnav_content'],
        'inputType' => 'checkbox',
        'eval' => ['tl_class' => 'w50 clr', 'submitOnChange' => true],
        'sql' => "CHAR(1) NOT NULL default '0'",
    ])
    ->addField('wem_sg_header_postnav_content', [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['header']['postnav_content'],
        'inputType' => 'text',
        'eval' => ['mandatory' => false, 'tl_class' => 'clr', 'rte' => 'ace|html'],
        'dependsOn' => [
            'field' => 'add_postnav_content',
        ],
        'sql' => 'TEXT NULL',
    ])
    ->addField('wem_sg_header_width', [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['header']['width'],
        'inputType' => 'select',
        'options' => [
            'fullsize' => &$GLOBALS['TL_LANG']['tl_content']['header']['width']['optionFullsize'],
            'container' => &$GLOBALS['TL_LANG']['tl_content']['header']['width']['optionContainer'],
        ],
        'default' => 'fullsize',
        'eval' => ['tl_class' => 'w50 clr'],
        'sql' => "varchar(255) NOT NULL default ''",
    ])
    ->addField('wem_sg_header_background', [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['header']['background'],
        'inputType' => 'select',
        'options' => \WEM\SmartgearBundle\Classes\Util::getSmartgearColors(),
        'eval' => ['tl_class' => 'w50 clr', 'includeBlankOption' => true],
        'sql' => "varchar(255) NOT NULL default ''",
    ])
    ->addField('wem_sg_header_bottom_style', [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['header']['bottom_style'],
        'inputType' => 'select',
        'options' => [
            'shadow' => &$GLOBALS['TL_LANG']['tl_content']['header']['bottom_style']['optionShadow'],
            'border' => &$GLOBALS['TL_LANG']['tl_content']['header']['bottom_style']['optionBorder'],
        ],
        'eval' => ['tl_class' => 'w50 clr', 'includeBlankOption' => true],
        'sql' => "varchar(255) NOT NULL default ''",
    ])
    ->addField('wem_sg_header_hover_style', [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['header']['hover_style'],
        'inputType' => 'select',
        'options' => [
            'underline' => &$GLOBALS['TL_LANG']['tl_content']['header']['hover_style']['optionUnderline'],
            'background' => &$GLOBALS['TL_LANG']['tl_content']['header']['hover_style']['optionBackground'],
        ],
        'default' => 'underline',
        'eval' => ['tl_class' => 'w50 clr'],
        'sql' => "varchar(255) NOT NULL default ''",
    ])
    ->addField('wem_sg_login_pwd_lost_jumpTo', [
        'exclude' => true,
        'inputType' => 'pageTree',
        'foreignKey' => 'tl_page.title',
        'eval' => ['fieldType' => 'radio'],
        'sql' => 'int(10) unsigned NOT NULL default 0',
        'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
    ])
    ->addField('wem_sg_login_register_jumpTo', [
        'exclude' => true,
        'inputType' => 'pageTree',
        'foreignKey' => 'tl_page.title',
        'eval' => ['fieldType' => 'radio'],
        'sql' => 'int(10) unsigned NOT NULL default 0',
        'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
    ])
    ->addField('wem_sg_breadcrumb_auto_placement', [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['breadcrumb']['auto_placement'],
        'inputType' => 'checkbox',
        'eval' => ['tl_class' => 'w50', 'submitOnChange' => true],
        'sql' => "CHAR(1) NOT NULL default '0'",
    ])
    ->addField('wem_sg_breadcrumb_auto_placement_after_content_elements', [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['breadcrumb']['auto_placement_after_content_elements'],
        'inputType' => 'select',
        'eval' => ['tl_class' => 'w50 clr', 'multiple' => true, 'mandatory' => false, 'chosen' => true],
        'options_callback' => [WEM\SmartgearBundle\DataContainer\Module::class, 'getOptionsForBreadcrumbAutoPlacementAfterContentElements'],
        'sql' => 'TEXT NULL',
    ])
    ->addField('wem_sg_breadcrumb_auto_placement_after_modules', [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['breadcrumb']['auto_placement_after_modules'],
        'inputType' => 'select',
        'eval' => ['tl_class' => 'w50 clr', 'multiple' => true, 'mandatory' => false, 'chosen' => true],
        'options_callback' => [WEM\SmartgearBundle\DataContainer\Module::class, 'getOptionsForBreadcrumbAutoPlacementAfterModules'],
        'sql' => 'TEXT NULL',
    ])

;

$paletteManipulator = PaletteManipulator::create()
    ->addField('wem_sg_display_share_buttons', 'config_legend', PaletteManipulator::POSITION_APPEND)
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
    ->addField('wem_sg_number_of_characters', 'config_legend', PaletteManipulator::POSITION_APPEND)
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

$paletteManipulator = PaletteManipulator::create()
    ->addLegend('password_lost_legend', 'redirect_legend')
    ->addField('wem_sg_login_pwd_lost_jumpTo', 'password_lost_legend', PaletteManipulator::POSITION_APPEND)
    ->addField('wem_sg_login_register_jumpTo', 'register_legend', PaletteManipulator::POSITION_APPEND)
;
$palettesToUpdate = [
    'login',
];
foreach ($palettesToUpdate as $paletteName) {
    if (\array_key_exists($paletteName, $GLOBALS['TL_DCA']['tl_module']['palettes'])) {
        $paletteManipulator->applyToPalette($paletteName, 'tl_module');
    }
}

$paletteManipulator = PaletteManipulator::create()
    ->addLegend('auto_placement_legend', 'nav_legend')
    ->addField('wem_sg_breadcrumb_auto_placement', 'auto_placement_legend', PaletteManipulator::POSITION_APPEND)
;
$palettesToUpdate = [
    'breadcrumb',
];
foreach ($palettesToUpdate as $paletteName) {
    if (\array_key_exists($paletteName, $GLOBALS['TL_DCA']['tl_module']['palettes'])) {
        $paletteManipulator->applyToPalette($paletteName, 'tl_module');
    }
}
