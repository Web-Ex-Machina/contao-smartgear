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

namespace WEM\SmartgearBundle\Classes\Utils;

use Contao\CalendarModel;
use InvalidArgumentException;

class CalendarUtil
{
    /**
     * Shortcut for calendar creation.
     */
    public static function createCalendar(string $strTitle, int $jumpTo, ?array $arrData = []): CalendarModel
    {
        // Create the theme
        if (\array_key_exists('id', $arrData)) {
            $objCalFee = CalendarModel::findOneById($arrData['id']);
            if (!$objCalFee) {
                throw new InvalidArgumentException('Le calendrier d\'évènements ayant pour id "'.$arrData['id'].'" n\'existe pas');
            }
        } else {
            $objCalFee = new CalendarModel();
        }

        $objCalFee->title = $strTitle;
        $objCalFee->jumpTo = $jumpTo;
        $objCalFee->tstamp = time();

        // Now we get the default values, get the arrData table
        if (!empty($arrData)) {
            foreach ($arrData as $k => $v) {
                $objCalFee->$k = $v;
            }
        }

        $objCalFee->save();

        return $objCalFee;
    }
}
