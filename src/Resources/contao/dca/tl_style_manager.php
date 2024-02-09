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

use Contao\DcaLoader;
use WEM\SmartgearBundle\Classes\Dca\Manipulator as DCAManipulator;

(new DcaLoader('tl_style_manager'))->load();

DCAManipulator::create('tl_style_manager')
    ->addListSortingHeaderCallback('smartgear.data_container.style_manager.style_manager', 'headerCallback')
    ->addListSortingChildRecordCallback('smartgear.data_container.style_manager.style_manager', 'listItems')
;
