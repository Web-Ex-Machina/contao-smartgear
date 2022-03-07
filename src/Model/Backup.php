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

namespace WEM\SmartgearBundle\Model;

use WEM\UtilsBundle\Model\Model as CoreModel;

/**
 * Reads and writes items.
 */
class Backup extends CoreModel
{
    /**
     * Search fields.
     *
     * @var array
     */
    public static $arrSearchFields = ['tstamp', 'name'];
    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_sm_backup';

    /**
     * Generic statements format.
     *
     * @param string $strField    [Column to format]
     * @param mixed  $varValue    [Value to use]
     * @param string $strOperator [Operator to use, default "="]
     *
     * @return array
     */
    public static function formatStatement($strField, $varValue, $strOperator = '=')
    {
        $arrColumns = [];
        $t = static::$strTable;

        switch ($strField) {
            case 'before':
                $arrColumns[] = sprintf("$t.tstamp <= %s", $varValue);
                break;
            case 'after':
                $arrColumns[] = sprintf("$t.tstamp >= %s", $varValue);
                break;
            default:
                return parent::formatStatement($strField, $varValue, $strOperator);
        }

        return $arrColumns;
    }
}
