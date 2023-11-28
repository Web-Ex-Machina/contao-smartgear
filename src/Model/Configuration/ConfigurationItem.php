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
    public const TYPES = [
        'pages' => [
            self::TYPE_PAGE_LEGAL_NOTICE,
            self::TYPE_PAGE_PRIVACY_POLITICS,
            self::TYPE_PAGE_SITEMAP,
        ],
        'user_groups' => [
            self::TYPE_USER_GROUP_ADMINISTRATORS,
            self::TYPE_USER_GROUP_REDACTORS,
        ],
        'modules' => [
            self::TYPE_MODULE_WEM_SG_HEADER,
            self::TYPE_MODULE_WEM_SG_FOOTER,
            self::TYPE_MODULE_BREADCRUMB,
            self::TYPE_MODULE_WEM_SG_SOCIAL_NETWORKS,
        ],
        'mixed' => [
            self::TYPE_MIXED_SITEMAP,
            self::TYPE_MIXED_FAQ,
            self::TYPE_MIXED_EVENTS,
            self::TYPE_MIXED_BLOG,
            self::TYPE_MIXED_FORM_CONTACT,
        ],
    ];

    /**
     * Search fields.
     *
     * @var array
     */
    public static $arrSearchFields = ['tstamp'];
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
}
