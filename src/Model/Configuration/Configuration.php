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

namespace WEM\SmartgearBundle\Model\Configuration;

use WEM\UtilsBundle\Model\Model as CoreModel;

/**
 * Reads and writes items.
 */
class Configuration extends CoreModel
{
    /**
     * Search fields.
     *
     * @var array
     */
    public static $arrSearchFields = ['tstamp', 'title'];
    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_sm_configuration';
    /**
     * Default order column.
     *
     * @var string
     */
    protected static $strOrderColumn = 'tstamp DESC';
}
