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

namespace WEM\SmartgearBundle\Dataset\SimpleGrid;

use WEM\SmartgearBundle\Classes\DataManager\DataProvider;
use WEM\SmartgearBundle\Classes\DataManager\DataSetInterface;

class DataSet extends DataProvider implements DataSetInterface
{
    protected $name = 'Simple grid';
    protected $requireSmartgear = ['core'];
    protected $uninstallable = false;
    protected $allowMultipleInstall = true;
    protected $requireTables = [];
    protected $configuration = [
        'legend' => [
            'pid' => [
                'inputType' => 'picker',
                'foreignKey' => 'tl_sm_dataset_install.id',
                'eval' => ['mandatory' => true, 'tl_class' => 'clr'],
                'sql' => 'int(10) unsigned NOT NULL default 0',
                'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
            ],
        ],
    ];
}
