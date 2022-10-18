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

namespace WEM\SmartgearBundle\Dataset\Example;

use WEM\SmartgearBundle\Classes\DataManager\DataProvider;
use WEM\SmartgearBundle\Classes\DataManager\DataSetInterface;

class DataSet extends DataProvider implements DataSetInterface
{
    protected $name = 'sample';
    protected $requireSmartgear = ['core'];
    protected $requireTables = [];
    // protected $type = 'component';
    // protected $module = 'core';
}
