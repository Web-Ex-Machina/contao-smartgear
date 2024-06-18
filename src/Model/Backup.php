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
    public const SOURCE_API = 'api';

    public const SOURCE_UPDATE = 'update';

    public const SOURCE_COMMAND = 'command';

    public const SOURCE_UI = 'ui';

    public const SOURCE_CONFIGURATION_RESET = 'configuration reset';

    /**
     * Search fields.
     */
    public static array $arrSearchFields = ['tstamp', 'name'];

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
     */
    public static function formatStatement(string $strField, mixed $varValue, string $strOperator = '='): array
    {
        $arrColumns = [];
        $t = static::$strTable;

        switch ($strField) {
            case 'before':
                $arrColumns[] = sprintf($t . ".tstamp <= %s", $varValue);
                break;
            case 'after':
                $arrColumns[] = sprintf($t . ".tstamp >= %s", $varValue);
                break;
            default:
                return parent::formatStatement($strField, $varValue, $strOperator);
        }

        return $arrColumns;
    }
}
