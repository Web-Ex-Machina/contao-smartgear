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

use WEM\SmartgearBundle\Classes\Dca\Manipulator as DCAManipulator;

DCAManipulator::create('tl_calendar_events')
    ->addField('addressLat', [
        'sql' => "varchar(20) unsigned NOT NULL DEFAULT ''",
    ])
    ->addField('addressLon', [
        'sql' => "varchar(20) unsigned NOT NULL DEFAULT ''",
    ])
;
