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

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\DcaLoader;
use WEM\SmartgearBundle\Classes\Dca\Manipulator as DCAManipulator;

(new DcaLoader('tl_news'))->load();

DCAManipulator::create('tl_news')
    ->addField('update_reminder', [
        'label' => &$GLOBALS['TL_LANG']['WEMSG']['DCA']['update_reminder'],
        'inputType' => 'checkbox',
        'eval' => ['submitOnChange' => true, 'tl_class' => 'w50 m12'],
        'sql' => "char(1) NOT NULL default '0'",
    ])
    ->addField('update_reminder_period', [
        'label' => &$GLOBALS['TL_LANG']['WEMSG']['DCA']['update_reminder_period'],
        'exclude' => true,
        'inputType' => 'select',
        // 'options_callback' => [ModuleDCA::class, 'getModules'],
        'options' => [
            'PT1S' => &$GLOBALS['TL_LANG']['WEMSG']['DCA']['update_reminder_period']['PT1S'],
            'PT1M' => &$GLOBALS['TL_LANG']['WEMSG']['DCA']['update_reminder_period']['PT1M'],
            'PT1H' => &$GLOBALS['TL_LANG']['WEMSG']['DCA']['update_reminder_period']['PT1H'],
            'P1D' => &$GLOBALS['TL_LANG']['WEMSG']['DCA']['update_reminder_period']['P1D'],
            'P1M' => &$GLOBALS['TL_LANG']['WEMSG']['DCA']['update_reminder_period']['P1M'],
            'P6M' => &$GLOBALS['TL_LANG']['WEMSG']['DCA']['update_reminder_period']['P6M'],
            'P1Y' => &$GLOBALS['TL_LANG']['WEMSG']['DCA']['update_reminder_period']['P1Y'],
        ],
        'eval' => ['mandatory' => true, 'chosen' => true, 'tl_class' => 'w50 wizard'],
        'sql' => "varchar(4) NOT NULL default 'P6M'",
    ])
    ->addField('update_reminder_date', [
        'label' => &$GLOBALS['TL_LANG']['WEMSG']['DCA']['update_reminder_date'],
        'inputType' => 'text',
        'eval' => ['rgxp'=>'datim', 'datepicker'=>true, 'tl_class'=>'w50 wizard'],
        'sql' => "int(10) unsigned NOT NULL default '0'",
    ])
    ->addConfigOnsubmitCallback(\WEM\SmartgearBundle\DataContainer\Core::class, 'updateReminder')
    ->addConfigOnloadCallback(\WEM\SmartgearBundle\DataContainer\Core::class, 'displayReminderMessage')
;

foreach ($GLOBALS['TL_DCA']['tl_news']['palettes'] as $paletteName => $paletteConfig) {
    if ('__selector__' !== $paletteName) {
        PaletteManipulator::create()
            ->addLegend('update_reminder_legend')
            ->addField('update_reminder', 'update_reminder_legend')
            ->addField('update_reminder_period', 'update_reminder_legend')
            ->addField('update_reminder_date', 'update_reminder_legend')
            ->applyToPalette($paletteName, 'tl_news')
        ;
    }
}
