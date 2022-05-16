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

namespace WEM\SmartgearBundle\DataContainer\Drivers;

use Contao\DC_Table;

class DC_TableCustom extends DC_Table
{
    /**
     * Compile buttons from the table configuration array and return them as HTML.
     *
     * @param array  $arrRow
     * @param string $strTable
     * @param array  $arrRootIds
     * @param bool   $blnCircularReference
     * @param array  $arrChildRecordIds
     * @param string $strPrevious
     * @param string $strNext
     *
     * @return string
     */
    public function generateButtonsPublic($arrRow, $strTable, $arrRootIds = [], $blnCircularReference = false, $arrChildRecordIds = null, $strPrevious = null, $strNext = null)
    {
        return parent::generateButtons($arrRow, $strTable, $arrRootIds, $blnCircularReference, $arrChildRecordIds, $strPrevious, $strNext);
    }
}
