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

use Contao\ModuleModel;
use WEM\SmartgearBundle\Classes\Util;

class ModuleUtil
{
    /**
     * Shortcut for module creation.
     */
    public static function createModule(int $pid, ?array $arrData = []): ModuleModel
    {
        // Create the module
        $objModule = isset($arrData['id']) ? ModuleModel::findById($arrData['id']) ?? new ModuleModel() : new ModuleModel();
        $objModule->tstamp = time();
        $objModule->pid = $pid;

        // Now we get the default values, get the arrData table
        if (!empty($arrData)) {
            foreach ($arrData as $k => $v) {
                $objModule->$k = $v;
            }
        }

        $objModule->save();

        // Return the model
        return $objModule;
    }

    public static function createModuleNav(int $pid, ?array $arrData = []): ModuleModel
    {
        $arrData = array_merge([
            'type' => 'navigation',
            'name' => 'Nav - main',
        ], $arrData);
        // Return the model
        return self::createModule($pid, $arrData);
    }

    public static function createModuleWemSgHeader(int $pid, int $moduleNavId, ?array $arrData = []): ModuleModel
    {
        $arrData = array_merge([
            'type' => 'wem_sg_header',
            'name' => 'HEADER',
            'imgSize' => 'a:3:{i:0;s:0:"";i:1;s:3:"100";i:2;s:12:"proportional";}',
            'wem_sg_header_sticky' => 1,
            'wem_sg_header_nav_module' => $moduleNavId,
            // 'wem_sg_header_alt' => 'Logo '.$config->getSgWebsiteTitle(),
            'wem_sg_header_search_parameter' => 'keywords',
            'wem_sg_header_nav_position' => 'right',
            'wem_sg_header_panel_position' => 'right',
        ], $arrData);
        // Return the model
        return self::createModule($pid, $arrData);
    }

    public static function createModuleBreadcrumb(int $pid, ?array $arrData = []): ModuleModel
    {
        $arrData = array_merge([
            'type' => 'breadcrumb',
            'name' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['ModuleBreadcrumbName'],
            'wem_sg_breadcrumb_auto_placement' => 1,
            'wem_sg_breadcrumb_auto_placement_after_content_elements' => serialize(['rsce_hero', 'rsce_heroStart']),
            'wem_sg_breadcrumb_auto_placement_after_modules' => serialize(['rsce_hero', 'rsce_heroStart']),
        ], $arrData);
        // Return the model
        return self::createModule($pid, $arrData);
    }

    public static function createModuleWemSgFooter(int $pid, ?array $arrData = []): ModuleModel
    {
        $arrData = array_merge([
            'type' => 'html',
            'name' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['ModuleFooterName'],
            'html' => file_get_contents(Util::getPublicOrWebDirectory().'/bundles/wemsmartgear/examples/footer_1.html'),
        ], $arrData);
        // Return the model
        return self::createModule($pid, $arrData);
    }

    public static function createModuleSitemap(int $pid, ?array $arrData = []): ModuleModel
    {
        $arrData = array_merge([
            'type' => 'sitemap',
            'name' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['ModuleSitemapName'],
        ], $arrData);
        // Return the model
        return self::createModule($pid, $arrData);
    }

    public static function createModuleWemSgSocialLink(int $pid, ?array $arrData = []): ModuleModel
    {
        $arrData = array_merge([
            'type' => 'wem_sg_social_link',
            'name' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['ModuleSocialLinkName'],
        ], $arrData);
        // Return the model
        return self::createModule($pid, $arrData);
    }

    public static function createModuleWemSgSocialLinkConfigCategories(int $pid, ?array $arrData = []): ModuleModel
    {
        $arrData = array_merge([
            'type' => 'wem_sg_social_link_config_categories',
            'name' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['ModuleSocialLinkConfigCategoriesName'],
        ], $arrData);
        // Return the model
        return self::createModule($pid, $arrData);
    }

    public static function createModuleWemPersonalDataManager(int $pid, ?array $arrData = []): ModuleModel
    {
        $arrData = array_merge([
            'type' => 'wem_personaldatamanager',
            'name' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['modulePersonalDataManagerName'],
        ], $arrData);
        // Return the model
        return self::createModule($pid, $arrData);
    }

    public static function createModuleFooterNav(int $pid, ?array $arrPagesIds = [], ?array $arrData = []): ModuleModel
    {
        $arrData = array_merge([
            'type' => 'customnav',
            'name' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['ModuleFooterNavName'],
            'pages' => $arrPagesIds,
            'navigationTpl' => 'nav_default',
        ], $arrData);
        // Return the model
        return self::createModule($pid, $arrData);
    }

    public static function createModuleFaq(int $pid, int $faqCategoryId, ?array $arrData = []): ModuleModel
    {
        $arrData = array_merge([
            'name' => 'FAQ - Reader',
            'pid' => $pid,
            'type' => 'faqpage',
            'faq_categories' => serialize([$faqCategoryId]),
            'numberOfItems' => 0,
            'imgSize' => serialize([0 => '480', 1 => '0', 2 => \Contao\Image\ResizeConfiguration::MODE_PROPORTIONAL]),
        ], $arrData);
        // Return the model
        return self::createModule($pid, $arrData);
    }

    public static function createModuleEventsList(int $pid, int $calendarId, int $moduleReaderId, ?array $arrData = []): ModuleModel
    {
        $arrData = array_merge([
            'name' => 'Events - List',
            // 'headline' => serialize(['unit' => 'h1', 'value' => $page->title]),
            'pid' => $pid,
            'type' => 'eventlist',
            'cal_calendar' => serialize([$calendarId]),
            'numberOfItems' => 0,
            'cal_format' => 'cal_month',
            'cal_order' => 'descending',
            'cal_readerModule' => $moduleReaderId,
            'perPage' => 15,
            'imgSize' => serialize([0 => '480', 1 => '0', 2 => \Contao\Image\ResizeConfiguration::MODE_PROPORTIONAL]),
        ], $arrData);
        // Return the model
        return self::createModule($pid, $arrData);
    }

    public static function createModuleEventsReader(int $pid, int $calendarId, ?array $arrData = []): ModuleModel
    {
        $arrData = array_merge([
            'name' => 'Events - Reader',
            'pid' => $pid,
            'type' => 'eventreader',
            'cal_calendar' => serialize([$calendarId]),
            'imgSize' => serialize([0 => '1200', 1 => '0', 2 => \Contao\Image\ResizeConfiguration::MODE_PROPORTIONAL]),
        ], $arrData);
        // Return the model
        return self::createModule($pid, $arrData);
    }

    public static function createModuleEventsCalendar(int $pid, int $calendarId, int $moduleReaderId, ?array $arrData = []): ModuleModel
    {
        $arrData = array_merge([
            'name' => 'Events - Calendar',
            'pid' => $pid,
            'type' => 'calendar',
            'cal_calendar' => serialize([$calendarId]),
            'numberOfItems' => 0,
            'cal_format' => 'cal_month',
            'cal_order' => 'descending',
            'cal_readerModule' => $moduleReaderId,
            'perPage' => 15,
            'imgSize' => serialize([0 => '480', 1 => '0', 2 => \Contao\Image\ResizeConfiguration::MODE_PROPORTIONAL]),
        ], $arrData);
        // Return the model
        return self::createModule($pid, $arrData);
    }

    public static function createModuleBlogList(int $pid, int $newsArchiveId, int $moduleReaderId, ?array $arrData = []): ModuleModel
    {
        $arrData = array_merge([
            'name' => 'Blog - List',
            // 'headline' => serialize(['value' => $page->title, 'unit' => 'h1']),
            'type' => 'newslist',
            'news_archives' => serialize([$newsArchiveId]),
            'numberOfItems' => 0,
            'news_readerModule' => $moduleReaderId,
            'news_order' => 'order_date_desc',
            'perPage' => 15,
            'imgSize' => serialize([0 => '480', 1 => '0', 2 => \Contao\Image\ResizeConfiguration::MODE_PROPORTIONAL]),
            'news_featured' => 'all_items',
            'news_template' => 'news_latest',
            'skipFirst' => 0,
            'news_metaFields' => serialize(['date', 'author']),
            // 'tstamp' => time(),
            'wem_sg_number_of_characters' => 200,
        ], $arrData);
        // Return the model
        return self::createModule($pid, $arrData);
    }

    public static function createModuleBlogReader(int $pid, int $newsArchiveId, ?array $arrData = []): ModuleModel
    {
        $arrData = array_merge([
            'name' => 'Blog - Reader',
            // 'pid' => $config->getSgTheme(),
            'type' => 'newsreader',
            'news_archives' => serialize([$newsArchiveId]),
            'news_metaFields' => serialize(['date', 'author']),
            'imgSize' => serialize([0 => '1200', 1 => '0', 2 => \Contao\Image\ResizeConfiguration::MODE_PROPORTIONAL]),
            'news_template' => 'news_full',
            'wem_sg_display_share_buttons' => '1',
        ], $arrData);
        // Return the model
        return self::createModule($pid, $arrData);
    }
}
