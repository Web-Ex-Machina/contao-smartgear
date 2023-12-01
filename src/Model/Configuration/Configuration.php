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

use Contao\LayoutModel;
use Contao\PageModel;
use WEM\UtilsBundle\Model\Model as CoreModel;

/**
 * Reads and writes items.
 */
class Configuration extends CoreModel
{
    public const ANALYTICS_SOLUTION_NONE = 'none';
    public const ANALYTICS_SOLUTION_GOOGLE = 'google';
    public const ANALYTICS_SOLUTION_MATOMO = 'matomo';

    public const FORBIDDEN_WEBSITE_TITLES = ['rsce', 'smartgear'];
    // public const ANALYTICS_SYSTEM_NONE = 'none';
    // public const ANALYTICS_SYSTEM_GOOGLE = 'google';
    // public const ANALYTICS_SYSTEM_MATOMO = 'matomo';
    public const ANALYTICS_SYSTEMS_ALLOWED = [
        self::ANALYTICS_SOLUTION_NONE,
        self::ANALYTICS_SOLUTION_GOOGLE,
        self::ANALYTICS_SOLUTION_MATOMO,
    ];

    public const MODE_DEV = 'dev';
    public const MODE_PROD = 'prod';
    public const MODES_ALLOWED = [
        self::MODE_DEV,
        self::MODE_PROD,
    ];

    public const TYPE_PERSON = 'person';
    public const TYPE_COMPANY = 'company';
    public const TYPES_ALLOWED = [
        self::TYPE_PERSON,
        self::TYPE_COMPANY,
    ];

    public const DEFAULT_VERSION = '1.0.0';
    public const DEFAULT_ANALYTICS_SYSTEM = self::ANALYTICS_SOLUTION_NONE;
    public const DEFAULT_ANALYTICS_SYSTEM_MATOMO_HOST = '//analytics.webexmachina.fr/';
    public const DEFAULT_MODE = self::MODE_DEV;
    public const DEFAULT_FRAMWAY_PATH = 'assets/framway';
    public const DEFAULT_OWNER_HOST = 'INFOMANIAK - 25 Eugène-Marziano 1227 Les Acacias - GENÈVE - SUISSE';
    public const DEFAULT_GOOGLE_FONTS = [];
    public const DEFAULT_USER_USERNAME = 'webmaster';
    public const DEFAULT_USER_GROUP_ADMIN_NAME = 'Administrateurs';
    public const DEFAULT_ROOTPAGE_CHMOD = 'a:12:{i:0;s:2:"u1";i:1;s:2:"u2";i:2;s:2:"u3";i:3;s:2:"u4";i:4;s:2:"u5";i:5;s:2:"u6";i:6;s:2:"g1";i:7;s:2:"g2";i:8;s:2:"g3";i:9;s:2:"g4";i:10;s:2:"g5";i:11;s:2:"g6";}';

    public const DEFAULT_CLIENT_FILES_FOLDER = 'files'.\DIRECTORY_SEPARATOR.'media';
    public const DEFAULT_CLIENT_LOGOS_FOLDER = 'files'.\DIRECTORY_SEPARATOR.'media'.\DIRECTORY_SEPARATOR.'logos';

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
            case 'not_id':
                $varValue = \is_array($varValue) ? $varValue : [$varValue];
                $arrColumns[] = sprintf("$t.id NOT IN (%s)", implode(',', $varValue));
                break;
            default:
                return parent::formatStatement($strField, $varValue, $strOperator);
        }

        return $arrColumns;
    }

    public static function findOneByPage(PageModel $objPage, ?array $arrOptions = [])
    {
        if ($objLayout = LayoutModel::findByPk($objPage->layout)) {
            // if ($objTheme = ThemeModel::findByPk($objLayout->pid)) {
            return self::findOneBy('contao_theme', $objLayout->pid, $arrOptions);
            // }
        }

        return null;
    }

    public static function findByPage(PageModel $objPage, ?array $arrOptions = [])
    {
        if ($objLayout = LayoutModel::findByPk($objPage->layout)) {
            // if ($objTheme = ThemeModel::findByPk($objLayout->pid)) {
            return self::findBy('contao_theme', $objLayout->pid, $arrOptions);
            // }
        }

        return null;
    }

    public static function findOneByPageId(int $pageId, ?array $arrOptions = [])
    {
        $objPage = PageModel::findOneByPk($pageId);
        if ($objPage) {
            return self::findOneByPage($objPage, $arrOptions);
        }

        return null;
    }

    public static function findByPageId(int $pageId, ?array $arrOptions = [])
    {
        $objPage = PageModel::findOneByPk($pageId);
        if ($objPage) {
            return self::findByPage($objPage, $arrOptions);
        }

        return null;
    }

    public function getLegalOwnerName(): string
    {
        switch ($this->legal_owner_type) {
            case self::TYPE_COMPANY:
                return $this->legal_owner_company_name;
            break;
            case self::TYPE_PERSON:
                return strtoupper($this->legal_owner_person_lastname).' '.$this->legal_owner_person_firstname;
            break;
        }

        return '';
    }
}
