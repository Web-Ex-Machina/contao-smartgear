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
class ConfigurationItem extends CoreModel
{
    public const TYPE_PAGE_LEGAL_NOTICE = 'page-legal-notice';

    public const TYPE_PAGE_PRIVACY_POLITICS = 'page-privacy-politics';

    public const TYPE_PAGE_SITEMAP = 'page-sitemap';

    public const TYPE_USER_GROUP_ADMINISTRATORS = 'user-group-administrators';

    public const TYPE_USER_GROUP_REDACTORS = 'user-group-redactors';

    public const TYPE_MODULE_WEM_SG_HEADER = 'module-wem-sg-header';

    public const TYPE_MODULE_WEM_SG_FOOTER = 'module-wem-sg-footer';

    public const TYPE_MODULE_BREADCRUMB = 'module-breadcrumb';

    public const TYPE_MODULE_WEM_SG_SOCIAL_NETWORKS = 'module-wem-sg-social-networks';

    public const TYPE_MIXED_SITEMAP = 'mixed-sitemap';

    public const TYPE_MIXED_FAQ = 'mixed-faq';

    public const TYPE_MIXED_EVENTS = 'mixed-events';

    public const TYPE_MIXED_BLOG = 'mixed-blog';

    public const TYPE_MIXED_FORM_CONTACT = 'mixed-form-contact';

    public const TYPES_PAGE = [
        self::TYPE_PAGE_LEGAL_NOTICE,
        self::TYPE_PAGE_PRIVACY_POLITICS,
        self::TYPE_PAGE_SITEMAP,
    ];

    public const TYPES_MODULE = [
        self::TYPE_MODULE_WEM_SG_HEADER,
        self::TYPE_MODULE_WEM_SG_FOOTER,
        self::TYPE_MODULE_BREADCRUMB,
        self::TYPE_MODULE_WEM_SG_SOCIAL_NETWORKS,
    ];

    public const TYPES_USER_GROUP = [
        self::TYPE_USER_GROUP_ADMINISTRATORS,
        self::TYPE_USER_GROUP_REDACTORS,
    ];

    public const TYPES_MIXED = [
        self::TYPE_MIXED_SITEMAP,
        self::TYPE_MIXED_FAQ,
        self::TYPE_MIXED_EVENTS,
        self::TYPE_MIXED_BLOG,
        self::TYPE_MIXED_FORM_CONTACT,
    ];

    public const TYPES = [
        'pages' => self::TYPES_PAGE,
        'user_groups' => self::TYPES_USER_GROUP,
        'modules' => self::TYPES_MODULE,
        'mixed' => self::TYPES_MIXED,
    ];

    /**
     * Search fields.
     */
    public static array $arrSearchFields = ['tstamp'];

    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_sm_configuration_item';

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
     */
    public static function formatStatement(string $strField, $varValue, string $strOperator = '='): array
    {
        $arrColumns = [];
        $t = static::$strTable;

        switch ($strField) {
            case 'not_id':
                $varValue = \is_array($varValue) ? $varValue : [$varValue];
                $arrColumns[] = sprintf($t . ".id NOT IN (%s)", implode(',', $varValue));
                break;
            case 'type':
                $varValue = \is_array($varValue) ? $varValue : [$varValue];
                $arrColumns[] = sprintf($t . ".type IN ('%s')", implode("','", $varValue));
                break;
            default:
                return parent::formatStatement($strField, $varValue, $strOperator);
        }

        return $arrColumns;
    }
}
