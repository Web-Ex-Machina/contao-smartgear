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
use Contao\Folder;
use Contao\ImageSizeModel;
use Contao\LayoutModel;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\ThemeModel;
use WEM\SmartgearBundle\Classes\StringUtil;
use WEM\SmartgearBundle\Classes\Utils\ArticleUtil;
use WEM\SmartgearBundle\Classes\Utils\ContentUtil;
use WEM\SmartgearBundle\Classes\Utils\ImageSizeUtil;
use WEM\SmartgearBundle\Classes\Utils\LayoutUtil;
use WEM\SmartgearBundle\Classes\Utils\ModuleUtil;
use WEM\SmartgearBundle\Classes\Utils\PageUtil;
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
        if (!empty($objItem->contao_theme)) {
            $objTheme = ThemeModel::findByPk($objItem->contao_theme);
        }

        if (!$objTheme) {
            $clientTemplatesFolder = new Folder('templates'.\DIRECTORY_SEPARATOR.StringUtil::generateAlias($objItem->title));

            $objTheme = ThemeUtil::createTheme('Smartgear '.$objItem->title, array_merge([
                'author' => 'Web Ex Machina',
                'templates' => sprintf('templates/%s', StringUtil::generateAlias($objItem->title)),
            ],
            !empty($objItem->contao_theme) ? ['id' => $objItem->contao_theme] : []
            ));
            $objItem->contao_theme = $objTheme->id;
            // check for images sizes
            $objImageSize = ImageSizeModel::findBy(['pid = ?', 'name = ?'], [$objItem->contao_theme, '16:9']);
            if (!$objImageSize) {
                ImageSizeUtil::createImageSize_16_9($objItem->contao_theme);
            }
            $objImageSize = ImageSizeModel::findBy(['pid = ?', 'name = ?'], [$objItem->contao_theme, '2:1']);
            if (!$objImageSize) {
                ImageSizeUtil::createImageSize_2_1($objItem->contao_theme);
            }
            $objImageSize = ImageSizeModel::findBy(['pid = ?', 'name = ?'], [$objItem->contao_theme, '1:2']);
            if (!$objImageSize) {
                ImageSizeUtil::createImageSize_1_2($objItem->contao_theme);
            }
            $objImageSize = ImageSizeModel::findBy(['pid = ?', 'name = ?'], [$objItem->contao_theme, '1:1']);
            if (!$objImageSize) {
                ImageSizeUtil::createImageSize_1_1($objItem->contao_theme);
            }
            $objImageSize = ImageSizeModel::findBy(['pid = ?', 'name = ?'], [$objItem->contao_theme, '4:3']);
            if (!$objImageSize) {
                ImageSizeUtil::createImageSize_4_3($objItem->contao_theme);
            }
        }

        // create modules
        // nav
        // if (!empty($objItem->contao_module_nav)) {
        //     $objModuleNav = ModuleModel::findByPk($objItem->contao_module_nav);
        // }
        // if (!$objModuleNav) {
        //     $objModuleNav = ModuleUtil::createModuleNav((int) $objTheme->id, !empty($objItem->contao_module_nav) ? ['id' => $objItem->contao_module_nav] : []);
        //     $objItem->contao_module_nav = $objModuleNav->id;
        // }

        // wem_sg_header
        // if (!empty($objItem->contao_module_wem_sg_header)) {
        //     $objModuleWemSgHeader = ModuleModel::findByPk($objItem->contao_module_wem_sg_header);
        // }
        // if (!$objModuleWemSgHeader) {
        //     $objModuleWemSgHeader = ModuleUtil::createModuleWemSgHeader((int) $objTheme->id, (int) $objModuleNav->id, !empty($objItem->contao_module_wem_sg_header) ? ['id' => $objItem->contao_module_wem_sg_header] : []);
        //     $objItem->contao_module_wem_sg_header = $objModuleWemSgHeader->id;
        // }

        // breadcrumb
        // if (!empty($objItem->contao_module_breadcrumb)) {
        //     $objModuleBreadcrumb = ModuleModel::findByPk($objItem->contao_module_breadcrumb);
        // }
        // if (!$objModuleBreadcrumb) {
        //     $objModuleBreadcrumb = ModuleUtil::createModuleBreadcrumb((int) $objTheme->id, !empty($objItem->contao_module_breadcrumb) ? ['id' => $objItem->contao_module_breadcrumb] : []);
        //     $objItem->contao_module_breadcrumb = $objModuleBreadcrumb->id;
        // }

        // wem_sg_footer
        // if (!empty($objItem->contao_module_wem_sg_footer)) {
        //     $objModuleWemSgFooter = ModuleModel::findByPk($objItem->contao_module_wem_sg_footer);
        // }
        // if (!$objModuleWemSgFooter) {
        //     $objModuleWemSgFooter = ModuleUtil::createModuleWemSgFooter((int) $objTheme->id, !empty($objItem->contao_module_wem_sg_footer) ? ['id' => $objItem->contao_module_wem_sg_footer] : []);
        //     $objItem->contao_module_wem_sg_footer = $objModuleWemSgFooter->id;
        // }

        // sitemap
        if (!empty($objItem->contao_module_sitemap)) {
            $objModuleSitemap = ModuleModel::findByPk($objItem->contao_module_sitemap);
        }
        if (!$objModuleSitemap) {
            $objModuleSitemap = ModuleUtil::createModuleSitemap((int) $objTheme->id, !empty($objItem->contao_module_sitemap) ? ['id' => $objItem->contao_module_sitemap] : []);
            $objItem->contao_module_sitemap = $objModuleSitemap->id;
        }

        // footernav
        // if (!empty($objItem->contao_module_footernav)) {
        //     $objModuleFooterNav = ModuleModel::findByPk($objItem->contao_module_footernav);
        // }
        // if (!$objModuleFooterNav) {
        //     $objModuleFooterNav = ModuleUtil::createModuleFooterNav((int) $objTheme->id, !empty($objItem->contao_module_footernav) ? ['id' => $objItem->contao_module_footernav] : []);
        //     $objItem->contao_module_footernav = $objModuleFooterNav->id;
        // }

        // create Contao Layout fullwidth
        if (!empty($objItem->contao_layout_full)) {
            $objLayoutFull = LayoutModel::findByPk($objItem->contao_layout_full);
        }
        if (!$objLayoutFull) {
            $objLayoutFull = LayoutUtil::createLayoutFullpage(
                $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['LayoutStandardFullwidthName'],
                $objTheme->id,
                array_merge([
                    'webfonts' => $objItem->google_fonts,
                    // 'modules_raw' => [
                    //     'nav' => $objModuleNav,
                    //     'wem_sg_header' => $objModuleWemSgHeader,
                    //     'breadcrumb' => $objModuleBreadcrumb,
                    //     'wem_sg_footer' => $objModuleWemSgFooter,
                    //     'sitemap' => $objModuleSitemap,
                    //     'footernav' => $objModuleFooterNav,
                    // ],
                    'replace' => [
                        'head' => [
                            '{{config.framway.path}}' => $objItem->framway_path,
                        ],
                        'script' => [
                            '{{config.googleFonts}}' => $objItem->google_fonts,
                            '{{config.framway.path}}' => $objItem->framway_path,
                            '{{config.analytics.system}}' => $objItem->analytics_solution,
                            '{{config.analytics.google.id}}' => $objItem->google_id,
                            '{{config.analytics.matomo.host}}' => $objItem->matomo_host,
                            '{{config.analytics.matomo.id}}' => $objItem->matomo_id,
                        ],
                    ],
                ],
            !empty($objItem->contao_layout_full) ? ['id' => $objItem->contao_layout_full] : []
            ));
            $objItem->contao_layout_full = $objLayoutFull->id;
        }

        // create Contao Layout standard
        if (!empty($objItem->contao_layout_standard)) {
            $objLayoutStandard = LayoutModel::findByPk($objItem->contao_layout_standard);
        }
        if (!$objLayoutStandard) {
            $objLayoutStandard = LayoutUtil::createLayoutStandard(
                $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['LayoutStandardName'],
                $objTheme->id,
                array_merge([
                    'webfonts' => $objItem->google_fonts,
                    // 'modules_raw' => [
                    //     'nav' => $objModuleNav,
                    //     'wem_sg_header' => $objModuleWemSgHeader,
                    //     'breadcrumb' => $objModuleBreadcrumb,
                    //     'wem_sg_footer' => $objModuleWemSgFooter,
                    //     'sitemap' => $objModuleSitemap,
                    //     'footernav' => $objModuleFooterNav,
                    // ],
                    'replace' => [
                        'head' => [
                            '{{config.framway.path}}' => $objItem->framway_path,
                        ],
                        'script' => [
                            '{{config.googleFonts}}' => $objItem->google_fonts,
                            '{{config.framway.path}}' => $objItem->framway_path,
                            '{{config.analytics.system}}' => $objItem->analytics_solution,
                            '{{config.analytics.google.id}}' => $objItem->google_id,
                            '{{config.analytics.matomo.host}}' => $objItem->matomo_host,
                            '{{config.analytics.matomo.id}}' => $objItem->matomo_id,
                        ],
                    ],
                ],
            !empty($objItem->contao_layout_standard) ? ['id' => $objItem->contao_layout_standard] : []
            ));

            $objItem->contao_layout_standard = $objLayoutStandard->id;
        }

        // Page - root
        if (!empty($objItem->contao_page_root)) {
            $objPageRoot = PageModel::findByPk($objItem->contao_page_root);
        }
        if (!$objPageRoot) {
            $objPageRoot = PageUtil::createPageRoot($objItem->title, $objItem->owner_email, (int) $objLayoutStandard->id, $objItem->language, !empty($objItem->contao_page_root) ? ['id' => $objItem->contao_page_root] : []);
            $objItem->contao_page_root = $objPageRoot->id;

            // create nothing because it is a root page
        }

        // Page - homepage
        if (!empty($objItem->contao_page_home)) {
            $objPageHome = PageModel::findByPk($objItem->contao_page_home);
        }
        if (!$objPageHome) {
            $objPageHome = PageUtil::createPageHome($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PageHomeTitle'], (int) $objItem->contao_page_root, !empty($objItem->contao_page_home) ? ['id' => $objItem->contao_page_home] : []);
            $objItem->contao_page_home = $objPageHome->id;

            // create article + content
            $objArticleHome = ArticleUtil::createArticle($objPageHome);
            // $objContentHome = ContentUtil::createContent($objPageHome, []);
        }

        // Page - 404
        if (!empty($objItem->contao_page_404)) {
            $objPage404 = PageModel::findByPk($objItem->contao_page_404);
        }
        if (!$objPage404) {
            $objPage404 = PageUtil::createPage404($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['Page404Title'], (int) $objItem->contao_page_root, !empty($objItem->contao_page_404) ? ['id' => $objItem->contao_page_404] : []);
            $objItem->contao_page_404 = $objPage404->id;

            // create article + content
            $objArticle404 = ArticleUtil::createArticle($objPage404);

            $contents['headline'] = ContentUtil::createContent($objArticle404, array_merge([
                'headline' => serialize([
                    'unit' => 'h1',
                    'value' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['Page404Headline'],
                ]),
                'text' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['Page404Text'],
                // ], ['id' => null !== $content ? $content->id : null]));
            ]));

            $contents['sitemap'] = ContentUtil::createContent($objArticle404, array_merge([
                'type' => 'module', 'module' => $objModuleSitemap->id,
                // ], ['id' => null !== $content ? $content->id : null]));
            ]));
        }

        $objItem->save();
    }

    public function fieldGoogleFontsOnsaveCallback($value, DataContainer $dc)
    {
        $valueFormatted = StringUtil::deserialize($value, true);

        return implode(',', $valueFormatted);
    }

    public function fieldGoogleFontsOnloadCallback($value, DataContainer $dc)
    {
        return serialize(explode(',', $value));
    }

    public function apiKeySaveCallback($value, DataContainer $dc)
    {
        $encryptionService = \Contao\System::getContainer()->get('plenta.encryption');

        return $encryptionService->encrypt($value);
    }

    public function apiKeyLoadCallback($value, DataContainer $dc)
    {
        $encryptionService = \Contao\System::getContainer()->get('plenta.encryption');

        return $encryptionService->decrypt($value);
    }
}
