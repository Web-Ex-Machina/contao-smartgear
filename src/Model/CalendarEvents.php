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
class CalendarEvents extends CoreModel
{
    /**
     * Search fields.
     */
    public static array $arrSearchFields = [];

    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_calendar_events';

    public function getAllLocations(array $calendars): array
    {
        $items = [];
        $date = new \DateTime();
        $sql = sprintf('
                        SELECT DISTINCT ce.location
                        FROM %s ce
                        WHERE ce.pid IN (%s)
                        AND (ce.published = 1 AND (ce.start <= "%s" OR ce.start = "") AND (ce.stop >= "%s" OR ce.stop = ""))
                        ORDER BY ce.location DESC
                    ',
                self::getTable(),
                implode(',', $calendars),
                $date->getTimestamp(),
                $date->getTimestamp()
                );
        $objResults = \Contao\Database::getInstance()->prepare($sql)->execute();
        while ($objResults->next()) {
            $items[] = $objResults->location;
        }

        return $items;
    }
}
