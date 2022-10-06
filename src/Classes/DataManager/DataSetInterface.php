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

namespace WEM\SmartgearBundle\Classes\DataManager;

use WEM\SmartgearBundle\Config\DataManagerDataSet;

interface DataSetInterface
{
    public function getModule(): string;

    public function getType(): string;

    public function getName(): string;

    public function import(): DataManagerDataSet;

    public function remove();
}
