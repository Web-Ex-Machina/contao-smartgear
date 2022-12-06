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

use Contao\Database;
use WEM\UtilsBundle\Model\Model as CoreModel;

/**
 * Reads and writes items.
 */
class PageVisit extends CoreModel
{
    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_sm_page_visit';
    /**
     * Default order column.
     *
     * @var string
     */
    protected static $strOrderColumn = 'tstamp DESC';

    public static function getReferersAnalytics(?array $arrConfig = [], ?int $limit = 5, ?int $offset = 0, ?array $arrOptions = [])
    {
        $t = self::getTable();

        return self::getAnalytics(['count(*) as amount', $t.'.referer'], $arrConfig, $limit, $offset, $arrOptions);
    }

    public static function getPagesUrlAnalytics(?array $arrConfig = [], ?int $limit = 5, ?int $offset = 0, ?array $arrOptions = [])
    {
        $t = self::getTable();

        return self::getAnalytics(['count(*) as amount', $t.'.page_url'], $arrConfig, $limit, $offset, $arrOptions);
    }

    public static function countReferersAnalytics(?array $arrConfig = [], ?array $arrOptions = []): int
    {
        $t = self::getTable();

        return self::countAnalytics(['count(*) as amount', $t.'.referer'], $arrConfig, $arrOptions);
    }

    public static function countPagesUrlAnalytics(?array $arrConfig = [], ?array $arrOptions = []): int
    {
        $t = self::getTable();

        return self::countAnalytics(['count(*) as amount', $t.'.page_url'], $arrConfig, $arrOptions);
    }

    public static function getAnalytics(array $arrSelect, ?array $arrConfig = [], ?int $limit = 5, ?int $offset = 0, ?array $arrOptions = [])
    {
        $t = self::getTable();
        $where = self::formatColumns($arrConfig);

        $arrOptions['table'] = self::getTable();
        $arrOptions['columns'] = $where;
        $arrOptions['select'] = $arrSelect;

        $sql = self::buildFindQuery($arrOptions);

        try {
            return Database::getInstance()->prepare($sql.' LIMIT '.$offset.','.$limit)->execute();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public static function countAnalytics(array $arrSelect, ?array $arrConfig = [], ?array $arrOptions = []): int
    {
        $t = self::getTable();
        $where = self::formatColumns($arrConfig);

        $arrOptions['table'] = self::getTable();
        $arrOptions['columns'] = $where;
        $arrOptions['select'] = $arrSelect;

        $sql = self::buildFindQuery($arrOptions);

        try {
            return (int) Database::getInstance()->prepare('SELECT count(*) as amount FROM ('.$sql.') as subquery')->execute()->fetchAssoc()['amount'];
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
