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

namespace WEM\SmartgearBundle\Classes\Utils\Configuration;

use Contao\Folder;
use Contao\ImageSizeModel;
use Contao\LayoutModel;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\System;
use Contao\ThemeModel;
use WEM\SmartgearBundle\Classes\StringUtil;
use WEM\SmartgearBundle\Classes\Utils\ArticleUtil;
use WEM\SmartgearBundle\Classes\Utils\ContentUtil;
use WEM\SmartgearBundle\Classes\Utils\ImageSizeUtil;
use WEM\SmartgearBundle\Classes\Utils\LayoutUtil;
use WEM\SmartgearBundle\Classes\Utils\ModuleUtil;
use WEM\SmartgearBundle\Classes\Utils\PageUtil;
use WEM\SmartgearBundle\Classes\Utils\ThemeUtil;
use WEM\SmartgearBundle\Model\Configuration\Configuration;
use WEM\SmartgearBundle\Model\Configuration\ConfigurationItem;

class ConfigurationUtil
{
    public static function deleteEverythingFromConfiguration(Configuration $objItem): Configuration
    {
        $configurationItems = ConfigurationItem::findItems(['pid' => $objItem->id]);
        if ($configurationItems) {
            while ($configurationItems->next()) {
                ConfigurationItemUtil::deleteEverythingFromConfigurationItem($configurationItems->current());
            }
        }

        if ($objItem->contao_page_home) {
            $objPage = $objItem->getRelated('contao_page_home');
            if ($objPage
            && 0 === ConfigurationItem::countItems(['contao_page' => $objItem->contao_page_home, 'not_id' => $objItem->id])
            && 0 === ConfigurationItem::countItems(['contao_page_form' => $objItem->contao_page_home, 'not_id' => $objItem->id])
            && 0 === ConfigurationItem::countItems(['contao_page_form_sent' => $objItem->contao_page_home, 'not_id' => $objItem->id])
            && 0 === Configuration::countItems(['contao_page_home' => $objItem->contao_page_home, 'not_id' => $objItem->id])
            && 0 === Configuration::countItems(['contao_page_404' => $objItem->contao_page_home, 'not_id' => $objItem->id])
            && 0 === Configuration::countItems(['contao_page_root' => $objItem->contao_page_home, 'not_id' => $objItem->id])
            ) {
                $objPage->delete();
                $objItem->contao_page_home = null;
            }
        }
        if ($objItem->contao_page_404) {
            $objPage = $objItem->getRelated('contao_page_404');
            if ($objPage
            && 0 === ConfigurationItem::countItems(['contao_page' => $objItem->contao_page_404, 'not_id' => $objItem->id])
            && 0 === ConfigurationItem::countItems(['contao_page_form' => $objItem->contao_page_404, 'not_id' => $objItem->id])
            && 0 === ConfigurationItem::countItems(['contao_page_form_sent' => $objItem->contao_page_404, 'not_id' => $objItem->id])
            && 0 === Configuration::countItems(['contao_page_home' => $objItem->contao_page_404, 'not_id' => $objItem->id])
            && 0 === Configuration::countItems(['contao_page_404' => $objItem->contao_page_404, 'not_id' => $objItem->id])
            && 0 === Configuration::countItems(['contao_page_root' => $objItem->contao_page_404, 'not_id' => $objItem->id])
            ) {
                $objPage->delete();
                $objItem->contao_page_404 = null;
            }
        }
        if ($objItem->contao_page_root) {
            $objPage = $objItem->getRelated('contao_page_root');
            if ($objPage
            && 0 === ConfigurationItem::countItems(['contao_page' => $objItem->contao_page_root, 'not_id' => $objItem->id])
            && 0 === ConfigurationItem::countItems(['contao_page_form' => $objItem->contao_page_root, 'not_id' => $objItem->id])
            && 0 === ConfigurationItem::countItems(['contao_page_form_sent' => $objItem->contao_page_root, 'not_id' => $objItem->id])
            && 0 === Configuration::countItems(['contao_page_home' => $objItem->contao_page_root, 'not_id' => $objItem->id])
            && 0 === Configuration::countItems(['contao_page_404' => $objItem->contao_page_root, 'not_id' => $objItem->id])
            && 0 === Configuration::countItems(['contao_page_root' => $objItem->contao_page_root, 'not_id' => $objItem->id])
            ) {
                $objPage->delete();
                $objItem->contao_page_root = null;
            }
        }
        if ($objItem->contao_module_sitemap) {
            $objModule = $objItem->getRelated('contao_module_sitemap');
            if ($objModule
            && 0 === ConfigurationItem::countItems(['contao_module' => $objItem->contao_module_sitemap, 'not_id' => $objItem->id])
            && 0 === ConfigurationItem::countItems(['contao_module_reader' => $objItem->contao_module_sitemap, 'not_id' => $objItem->id])
            && 0 === ConfigurationItem::countItems(['contao_module_list' => $objItem->contao_module_sitemap, 'not_id' => $objItem->id])
            && 0 === ConfigurationItem::countItems(['contao_module_calendar' => $objItem->contao_module_sitemap, 'not_id' => $objItem->id])
            && 0 === Configuration::countItems(['contao_module_sitemap' => $objItem->contao_module_sitemap, 'not_id' => $objItem->id])
            ) {
                $objModule->delete();
                $objItem->contao_module_sitemap = null;
            }
        }
        if ($objItem->contao_layout_full) {
            $objLayout = $objItem->getRelated('contao_layout_full');
            if ($objLayout
            && 0 === Configuration::countItems(['contao_layout_full' => $objItem->contao_layout_full, 'not_id' => $objItem->id])
            && 0 === Configuration::countItems(['contao_layout_standard' => $objItem->contao_layout_full, 'not_id' => $objItem->id])
            ) {
                $objLayout->delete();
                $objItem->contao_layout_full = null;
            }
        }
        if ($objItem->contao_layout_standard) {
            $objLayout = $objItem->getRelated('contao_layout_standard');
            if ($objLayout
            && 0 === Configuration::countItems(['contao_layout_full' => $objItem->contao_layout_standard, 'not_id' => $objItem->id])
            && 0 === Configuration::countItems(['contao_layout_standard' => $objItem->contao_layout_standard, 'not_id' => $objItem->id])
            ) {
                $objLayout->delete();
                $objItem->contao_layout_standard = null;
            }
        }
        if ($objItem->contao_theme) {
            $objTheme = $objItem->getRelated('contao_theme');
            if ($objTheme
            && 0 === Configuration::countItems(['contao_theme' => $objItem->contao_theme, 'not_id' => $objItem->id])
            ) {
                $objTheme->delete();
                $objItem->contao_theme = null;
            }
        }

        return $objItem;
    }

    public static function createEverythingFromConfiguration(Configuration $objItem): Configuration
    {
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
                ImageSizeUtil::createImageSize_16_9((int) $objItem->contao_theme);
            }
            $objImageSize = ImageSizeModel::findBy(['pid = ?', 'name = ?'], [$objItem->contao_theme, '2:1']);
            if (!$objImageSize) {
                ImageSizeUtil::createImageSize_2_1((int) $objItem->contao_theme);
            }
            $objImageSize = ImageSizeModel::findBy(['pid = ?', 'name = ?'], [$objItem->contao_theme, '1:2']);
            if (!$objImageSize) {
                ImageSizeUtil::createImageSize_1_2((int) $objItem->contao_theme);
            }
            $objImageSize = ImageSizeModel::findBy(['pid = ?', 'name = ?'], [$objItem->contao_theme, '1:1']);
            if (!$objImageSize) {
                ImageSizeUtil::createImageSize_1_1((int) $objItem->contao_theme);
            }
            $objImageSize = ImageSizeModel::findBy(['pid = ?', 'name = ?'], [$objItem->contao_theme, '4:3']);
            if (!$objImageSize) {
                ImageSizeUtil::createImageSize_4_3((int) $objItem->contao_theme);
            }
        }

        // create modules
        // sitemap
        if (!empty($objItem->contao_module_sitemap)) {
            $objModuleSitemap = ModuleModel::findByPk($objItem->contao_module_sitemap);
        }
        if (!$objModuleSitemap) {
            $objModuleSitemap = ModuleUtil::createModuleSitemap((int) $objTheme->id, !empty($objItem->contao_module_sitemap) ? ['id' => $objItem->contao_module_sitemap] : []);
            $objItem->contao_module_sitemap = $objModuleSitemap->id;
        }

        // create Contao Layout fullwidth
        if (!empty($objItem->contao_layout_full)) {
            $objLayoutFull = LayoutModel::findByPk($objItem->contao_layout_full);
        }
        if (!$objLayoutFull) {
            $objLayoutFull = LayoutUtil::createLayoutFullpage(
                $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['LayoutStandardFullwidthName'],
                (int) $objTheme->id,
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
                (int) $objTheme->id,
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
            $objPageRoot = PageUtil::createPageRoot($objItem->title, $objItem->legal_owner_email, (int) $objLayoutStandard->id, $objItem->language, !empty($objItem->contao_page_root) ? ['id' => $objItem->contao_page_root] : []);
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

        // allow "onclick" on "<a>" tag
        $allowedAttributes = StringUtil::deserialize(\Contao\Config::get('allowedAttributes'), true);
        foreach ($allowedAttributes as $index => $allowedAttribute) {
            if ('a' === $allowedAttribute['key']
            && false === strpos($allowedAttribute['value'], 'onclick')
            ) {
                $allowedAttributes[$index]['value'] .= ',onclick';
                \Contao\Config::set('allowedAttributes', serialize($allowedAttributes));
                \Contao\Config::persist('allowedAttributes', serialize($allowedAttributes));
                break;
            }
        }

        // update local config
        $localConfigManager = System::getContainer()->get('smartgear.config.manager.local_config');
        /** @var LocalConfig */
        $config = $localConfigManager->load();

        $config
            ->setDateFormat('d/m/Y')
            ->setTimeFormat('H:i')
            ->setDatimFormat('d/m/Y à H:i')
            ->setTimeZone('Europe/Paris')
            ->setCharacterSet('utf-8')
            ->setUseAutoItem(1)
            ->setFolderUrl(1)
            ->setMaxResultsPerPage(500)
            ->setPrivacyAnonymizeIp(1)
            ->setPrivacyAnonymizeGA(1)
            ->setGdMaxImgWidth(5000)
            ->setGdMaxImgHeight(5000)
            ->setMaxFileSize(10000000)
            ->setUndoPeriod(7776000)
            ->setVersionPeriod(7776000)
            ->setLogPeriod(7776000)
            ->setAllowedTags('<script><iframe><a><abbr><acronym><address><area><article><aside><audio><b><bdi><bdo><big><blockquote><br><base><button><canvas><caption><cite><code><col><colgroup><data><datalist><dataset><dd><del><dfn><div><dl><dt><em><fieldset><figcaption><figure><footer><form><h1><h2><h3><h4><h5><h6><header><hgroup><hr><i><img><input><ins><kbd><keygen><label><legend><li><link><map><mark><menu><nav><object><ol><optgroup><option><output><p><param><picture><pre><q><s><samp><section><select><small><source><span><strong><style><sub><sup><table><tbody><td><textarea><tfoot><th><thead><time><tr><tt><u><ul><var><video><wbr>')
            // ->setSgOwnerDomain(\Contao\Environment::get('base'))
            // ->setSgOwnerHost(CoreConfig::DEFAULT_OWNER_HOST)
            ->setRejectLargeUploads(true)
            ->setFileusageSkipReplaceInsertTags(true) // Still needed on some installations
        ;

        $localConfigManager->save($config);

        // sync templates + FW build
        /** @var DirectoriesSynchronizer */
        $synchronizer = System::getContainer()->get('smartgear.classes.directories_synchronizer.templates.rsce');
        $synchronizer->synchronize();
        $synchronizer = System::getContainer()->get('smartgear.classes.directories_synchronizer.templates.smartgear');
        $synchronizer->synchronize();
        $synchronizer = System::getContainer()->get('smartgear.classes.directories_synchronizer.templates.general');
        $synchronizer->synchronize();
        $synchronizer = System::getContainer()->get('smartgear.classes.directories_synchronizer.tiny_mce.plugins');
        $synchronizer->synchronize();
        $synchronizer = System::getContainer()->get('smartgear.classes.directories_synchronizer.outdated_browser');
        $synchronizer->synchronize(true);
        $synchronizer = System::getContainer()->get('smartgear.classes.directories_synchronizer.tarte_au_citron');
        $synchronizer->synchronize(true);
        $synchronizer = System::getContainer()->get('smartgear.classes.directories_synchronizer.social_share_buttons');
        $synchronizer->synchronize(true);
        /** @todo : FW path can change + check files are not already present ! (lot of changes to do) */
        $synchronizer = System::getContainer()->get('smartgear.classes.directories_synchronizer.framway');
        $synchronizer->setDestinationDirectory($objItem->framway_path);
        $synchronizer->synchronize();

        return $objItem;
    }
}
