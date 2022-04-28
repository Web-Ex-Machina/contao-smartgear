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
use WEM\SmartgearBundle\Security\SmartgearPermissions;

$GLOBALS['TL_DCA']['tl_user_group']['fields']['smartgear_permissions'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['multiple' => true, 'helpwizard' => true],
    'options' => [
        $GLOBALS['TL_LANG']['WEMSG']['SECURITY']['CORE']['optGroup'] => [
            // SmartgearPermissions::CORE_SIMPLE => &$GLOBALS['TL_LANG']['WEMSG']['SECURITY']['CORE']['simple'],
            SmartgearPermissions::CORE_EXPERT => &$GLOBALS['TL_LANG']['WEMSG']['SECURITY']['CORE']['expert'],
        ],
        $GLOBALS['TL_LANG']['WEMSG']['SECURITY']['BLOG']['optGroup'] => [
            // SmartgearPermissions::BLOG_SIMPLE => &$GLOBALS['TL_LANG']['WEMSG']['SECURITY']['BLOG']['simple'],
            SmartgearPermissions::BLOG_EXPERT => &$GLOBALS['TL_LANG']['WEMSG']['SECURITY']['BLOG']['expert'],
        ],
    ],
    'sql' => ['type' => 'blob', 'notnull' => false],
    'explanation' => 'smartgear_permissions',
];

PaletteManipulator::create()
    ->addLegend('smartgear_permissions_legend', null)
    ->addField('smartgear_permissions', 'smartgear_permissions_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_user_group')
;
