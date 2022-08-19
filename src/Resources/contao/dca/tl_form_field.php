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

DCAManipulator::create('tl_form_field')
    ->addField('contains_personal_data', [
        'inputType' => 'checkbox',
        'sql' => 'TINYINT(1) unsigned NOT NULL DEFAULT 0',
    ])
;

PaletteManipulator::create()
    ->addField('contains_personal_data', 'type')
    ->applyToPalette('explanation', 'tl_form_field')
    ->applyToPalette('html', 'tl_form_field')
    ->applyToPalette('text', 'tl_form_field')
    ->applyToPalette('textdigit', 'tl_form_field')
    ->applyToPalette('textcustom', 'tl_form_field')
    ->applyToPalette('password', 'tl_form_field')
    ->applyToPalette('passwordcustom', 'tl_form_field')
    ->applyToPalette('textarea', 'tl_form_field')
    ->applyToPalette('textareacustom', 'tl_form_field')
    ->applyToPalette('select', 'tl_form_field')
    ->applyToPalette('radio', 'tl_form_field')
    ->applyToPalette('checkbox', 'tl_form_field')
    ->applyToPalette('range', 'tl_form_field')
    ->applyToPalette('hidden', 'tl_form_field')
    ->applyToPalette('hiddencustom', 'tl_form_field')
;
