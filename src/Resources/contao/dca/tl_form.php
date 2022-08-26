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
use Contao\DcaLoader;
use WEM\SmartgearBundle\Classes\Dca\Manipulator as DCAManipulator;

(new DcaLoader('tl_form'))->load();

DCAManipulator::create('tl_form')
    ->addListOperation('contacts', [
        'href' => 'table=tl_sm_form_storage',
        'icon' => 'user.svg',
    ])
    ->addCtable('tl_sm_form_storage')
    ->addListLabelLabelCallback('smartgear.data_container.form', 'listItems')
    ->addField('storeViaFormDataManager', [
        'inputType' => 'checkbox',
        'sql' => "char(1) NOT NULL default ''",
    ])
;

PaletteManipulator::create()
    ->addField('storeViaFormDataManager', 'storeValues', PaletteManipulator::POSITION_BEFORE)
    ->applyToPalette('default', 'tl_form')
;
