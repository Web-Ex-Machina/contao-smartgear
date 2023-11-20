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
}
