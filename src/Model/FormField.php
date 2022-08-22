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
class FormField extends CoreModel
{
    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_form_field';
    /**
     * Default order column.
     *
     * @var string
     */
    protected static $strOrderColumn = 'tstamp DESC';

    /**
     * Find items, depends on the arguments.
     *
     * @param array $arrConfig  [Request Config]
     * @param int   $intLimit   [Query Limit]
     * @param int   $intOffset  [Query Offset]
     * @param array $arrOptions [Query Options]
     *
     * @return Collection
     */
    public static function findItems($arrConfig = [], $intLimit = 0, $intOffset = 0, array $arrOptions = [])
    {
        try {
            return parent::findItems($arrConfig, $intLimit, $intOffset, $arrOptions);
        } catch (\Exception $e) {
            dump($e);
            exit();
            throw $e;
        }
    }
}
