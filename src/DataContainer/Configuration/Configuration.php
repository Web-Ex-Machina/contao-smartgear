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

namespace WEM\SmartgearBundle\DataContainer\Configuration;

use Contao\DataContainer;
use Contao\LayoutModel;
use Contao\ModuleModel;
use Contao\ThemeModel;
use WEM\SmartgearBundle\Classes\StringUtil;
use WEM\SmartgearBundle\Classes\Utils\LayoutUtil;
use WEM\SmartgearBundle\Classes\Utils\ModuleUtil;
use WEM\SmartgearBundle\Classes\Utils\ThemeUtil;
use WEM\SmartgearBundle\DataContainer\Core;
use WEM\SmartgearBundle\Model\Configuration\Configuration as ConfigurationModel;

class Configuration extends Core
{
    public function __construct()
    {
        parent::__construct();
    }

    public function onsubmitCallback(DataContainer $dc): void
    {
        $objItem = ConfigurationModel::findOneById($dc->activeRecord->id);

        // here we'll call everything to create contao contents
        if (empty($objItem->contao_theme)) {
            $objTheme = ThemeUtil::createTheme('Smartgear '.$dc->activeRecord->title, [
                'author' => 'Web Ex Machina',
                'templates' => sprintf('templates/%s', StringUtil::generateAlias($dc->activeRecord->title)),
            ]);
            $objItem->contao_theme = $objTheme->id;
        } else {
            $objTheme = ThemeModel::findByPk($dc->activeRecord->contao_theme);
        }

        // create modules
        // nav
        if (empty($objItem->contao_module_nav)) {
            $objModuleNav = ModuleUtil::createModuleNav((int) $objTheme->id);
            $objItem->contao_module_nav = $objModuleNav->id;
        } else {
            $objModuleNav = ModuleModel::findByPk($dc->activeRecord->contao_module_nav);
        }

        // wem_sg_header
        if (empty($objItem->contao_module_wem_sg_header)) {
            $objModuleWemSgHeader = ModuleUtil::createModuleWemSgHeader((int) $objTheme->id, (int) $objModuleNav->id);
            $objItem->contao_module_wem_sg_header = $objModuleWemSgHeader->id;
        } else {
            $objModuleWemSgHeader = ModuleModel::findByPk($dc->activeRecord->contao_module_wem_sg_header);
        }

        // breadcrumb
        if (empty($objItem->contao_module_breadcrumb)) {
            $objModuleBreadcrumb = ModuleUtil::createModuleBreadcrumb((int) $objTheme->id);
            $objItem->contao_module_breadcrumb = $objModuleBreadcrumb->id;
        } else {
            $objModuleBreadcrumb = ModuleModel::findByPk($dc->activeRecord->contao_module_breadcrumb);
        }

        // wem_sg_footer
        if (empty($objItem->contao_module_wem_sg_footer)) {
            $objModuleWemSgFooter = ModuleUtil::createModuleWemSgFooter((int) $objTheme->id);
            $objItem->contao_module_wem_sg_footer = $objModuleWemSgFooter->id;
        } else {
            $objModuleWemSgFooter = ModuleModel::findByPk($dc->activeRecord->contao_module_wem_sg_footer);
        }

        // sitemap
        if (empty($objItem->contao_module_sitemap)) {
            $objModuleSitemap = ModuleUtil::createModuleSitemap((int) $objTheme->id);
            $objItem->contao_module_sitemap = $objModuleSitemap->id;
        } else {
            $objModuleSitemap = ModuleModel::findByPk($dc->activeRecord->contao_module_sitemap);
        }

        // footernav
        if (empty($objItem->contao_module_footernav)) {
            $objModuleFooterNav = ModuleUtil::createModuleFooterNav((int) $objTheme->id);
            $objItem->contao_module_footernav = $objModuleFooterNav->id;
        } else {
            $objModuleFooterNav = ModuleModel::findByPk($dc->activeRecord->contao_module_footernav);
        }

        if (empty($objItem->contao_layout_full)) {
            // create Contao Theme
            $objLayoutFull = LayoutUtil::createLayoutFullpage('title', $objTheme->id, []);
            $objItem->contao_layout_full = $objLayoutFull->id;
        } else {
            $objLayoutFull = LayoutModel::findByPk($dc->activeRecord->contao_layout_full);
        }

        $objItem->save();
    }

    public function fieldGoogleFontsOnsaveCallback($value, DataContainer $dc)
    {
        // dump($value);
        // exit();
        $valueFormatted = StringUtil::deserialize($value, true);

        return implode(',', $valueFormatted);
    }

    public function fieldGoogleFontsOnloadCallback($value, DataContainer $dc)
    {
        return serialize(explode(',', $value));
    }
}
