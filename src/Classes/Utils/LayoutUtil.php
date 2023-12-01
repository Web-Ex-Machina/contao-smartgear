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

use Contao\ArrayUtil;
use Contao\LayoutModel;
use Contao\ModuleModel;
use InvalidArgumentException;
use WEM\SmartgearBundle\Classes\StringUtil;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Model\Configuration\Configuration;

class LayoutUtil
{
    /**
     * Shortcut for layout creation.
     */
    public static function createLayout(string $strName, int $pid, ?array $arrData = []): LayoutModel
    {
        // Create the theme
        if (\array_key_exists('id', $arrData)) {
            $objLayout = LayoutModel::findOneById($arrData['id']);
            if (!$objLayout) {
                //     throw new InvalidArgumentException('La présentation de page ayant pour id "'.$arrData['id'].'" n\'existe pas');
                $objLayout = new LayoutModel();
                $objLayout->id = $arrData['id'];
            }
        } else {
            $objLayout = new LayoutModel();
        }

        $objLayout->name = $strName;
        $objLayout->pid = $pid;
        $objLayout->tstamp = time();

        // Now we get the default values, get the arrData table
        if (!empty($arrData)) {
            foreach ($arrData as $k => $v) {
                $objLayout->$k = $v;
            }
        }

        $objLayout->save();

        return $objLayout;
    }

    public static function createLayoutFullpage(string $strName, int $pid, ?array $arrData = []): LayoutModel
    {
        $head = self::buildHead($arrData['replace']['head']);
        $script = self::buildScript($arrData['replace']['script']);

        $objLayout = null;
        if (\array_key_exists('id', $arrData)) {
            $objLayout = LayoutModel::findOneById($arrData['id']);
        }

        if (\array_key_exists('modules_raw', $arrData)) {
            $arrLayoutModules = self::reorderLayoutModules(
                self::mergeLayoutsModules(
                    StringUtil::deserialize($objLayout ? $objLayout->modules ?? [] : []),
                    self::buildDefaultModulesConfiguration(
                        (int) $arrData['modules_raw']['wem_sg_header']->id,
                        (int) $arrData['modules_raw']['breadcrumb']->id,
                        (int) $arrData['modules_raw']['wem_sg_footer']->id,
                    )
                ),
                $arrData['modules_raw']
            );
        }

        $arrData = array_merge([
            'name' => $strName,
            'rows' => '3rw',
            'cols' => '1cl',
            'loadingOrder' => 'external_first',
            'combineScripts' => 1,
            'viewport' => 'width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=0',
            'modules' => serialize($arrLayoutModules ?: [['mod' => 0, 'col' => 'main', 'enable' => 1]]),
            'template' => 'fe_page_full',
            // 'webfonts' => $config->getSgGoogleFonts(),
            'head' => $head,
            'script' => $script,
            'framework' => serialize([]),
            'tstamp' => time(),
        ], $arrData);

        return self::createLayout($strName, $pid, $arrData);
    }

    public static function createLayoutStandard(string $strName, int $pid, ?array $arrData = []): LayoutModel
    {
        $head = self::buildHead($arrData['replace']['head']);
        $script = self::buildScript($arrData['replace']['script']);

        $objLayout = null;
        if (\array_key_exists('id', $arrData)) {
            $objLayout = LayoutModel::findOneById($arrData['id']);
        }

        if (\array_key_exists('modules_raw', $arrData)) {
            $arrLayoutModules = self::reorderLayoutModules(
                self::mergeLayoutsModules(
                    StringUtil::deserialize($objLayout ? $objLayout->modules ?? [] : []),
                    self::buildDefaultModulesConfiguration(
                        (int) $arrData['modules_raw']['wem_sg_header']->id,
                        (int) $arrData['modules_raw']['breadcrumb']->id,
                        (int) $arrData['modules_raw']['wem_sg_footer']->id,
                    )
                ),
                $arrData['modules_raw']
            );
        }

        $arrData = array_merge([
            'name' => $strName,
            'rows' => '3rw',
            'cols' => '1cl',
            'loadingOrder' => 'external_first',
            'combineScripts' => 1,
            'viewport' => 'width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=0',
            'modules' => serialize($arrLayoutModules ?: [['mod' => 0, 'col' => 'main', 'enable' => 1]]),
            'template' => 'fe_page',
            // 'webfonts' => $config->getSgGoogleFonts(),
            'head' => $head,
            'script' => $script,
            'framework' => serialize([]),
            'tstamp' => time(),
        ], $arrData);

        return self::createLayout($strName, $pid, $arrData);
    }

    public static function buildHead(?array $arrReplace = []): string
    {
        $head = file_get_contents(Util::getPublicOrWebDirectory().'/bundles/wemsmartgear/examples/balises_supplementaires_1.js');
        foreach ($arrReplace as $toReplace => $newValue) {
            $head = str_replace($toReplace, $newValue, $head);
        }
        // $head = str_replace('{{config.framway.path}}', $config->getSgFramwayPath(), $head);

        return $head;
    }

    public static function buildScript(?array $arrReplace = []): string
    {
        $script = file_get_contents(Util::getPublicOrWebDirectory().'/bundles/wemsmartgear/examples/code_javascript_personnalise_1.js');

        if (\array_key_exists('{{config.googleFonts}}', $arrReplace)) {
            $script = str_replace('{{config.googleFonts}}', "'".$arrReplace['{{config.googleFonts}}']."'", $script);
        } else {
            $script = preg_replace('/\/\/ -- GFONT(.*)\/\/ -- \/GFONT/s', '', $script);
        }

        $script = str_replace('{{config.framway.path}}', $arrReplace['{{config.framway.path}}'], $script);
        switch ($arrReplace['{{config.analytics.system}}']) {
            case Configuration::ANALYTICS_SOLUTION_NONE:
                $script = preg_replace('/\/\/ -- GTAG(.*)\/\/ -- \/GTAG/s', '', $script);
                $script = preg_replace('/\/\/ -- MATOMO(.*)\/\/ -- \/MATOMO/s', '', $script);
            break;
            case Configuration::ANALYTICS_SOLUTION_GOOGLE:
                $script = str_replace('{{config.analytics.google.id}}', $arrReplace['{{config.analytics.google.id}}'], $script);
                $script = preg_replace('/\/\/ -- MATOMO(.*)\/\/ -- \/MATOMO/s', '', $script);
            break;
            case Configuration::ANALYTICS_SOLUTION_MATOMO:
                $script = str_replace('{{config.analytics.matomo.host}}', $arrReplace['{{config.analytics.matomo.host}}'], $script);
                $script = str_replace('{{config.analytics.matomo.id}}', $arrReplace['{{config.analytics.matomo.id}}'], $script);
                $script = preg_replace('/\/\/ -- GTAG(.*)\/\/ -- \/GTAG/s', '', $script);
            break;
        }
        // $head = str_replace('{{config.framway.path}}', $config->getSgFramwayPath(), $head);

        return $script;
    }

    public static function buildDefaultModulesConfiguration(int $modWemSgHeaderId, int $modBreadcrumbId, int $modWemSgFooterId): array
    {
        return [
            ['mod' => $modWemSgHeaderId, 'col' => 'header', 'enable' => '1'],
            ['mod' => $modBreadcrumbId, 'col' => 'main', 'enable' => '1'],
            ['mod' => 0, 'col' => 'main', 'enable' => '1'],
            ['mod' => $modWemSgFooterId, 'col' => 'footer', 'enable' => '1'],
        ];
    }

    /**
     * Merge layout modules with default ones.
     *
     * @param array $currentLayoutModules Current layout's modules
     * @param array $defaultLayoutModules Default layout's modules
     */
    public static function mergeLayoutsModules(array $currentLayoutModules, array $defaultLayoutModules): array
    {
        if (empty($currentLayoutModules)) {
            return $defaultLayoutModules;
        }

        foreach ($defaultLayoutModules as $layoutModuleDefault) {
            $layoutMOduleDefaultFoundInLayoutModule = false;
            foreach ($currentLayoutModules as $layoutModule) {
                if ((int) $layoutModule['mod'] === (int) $layoutModuleDefault['mod']) {
                    $layoutMOduleDefaultFoundInLayoutModule = true;
                    break;
                }
            }
            if (!$layoutMOduleDefaultFoundInLayoutModule) {
                $currentLayoutModules[] = $layoutModuleDefault;
            }
        }

        return $currentLayoutModules;
    }

    /**
     * Reorder layout's modules.
     *
     * @param array $layoutModules The layout's modules
     * @param array $modules       The modules
     */
    public static function reorderLayoutModules(array $layoutModules, array $modules): array
    {
        $layoutModuleHeader = null;
        $layoutModuleFooter = null;
        $layoutModuleBreadcrumb = null;
        $layoutModuleBreadcrumbIndex = null;
        $layoutModuleContentIndex = null;
        foreach ($layoutModules as $index => $layoutModule) {
            if ((int) $layoutModule['mod'] === (int) $modules['wem_sg_header']->id) {
                $layoutModuleHeader = $layoutModule;
                unset($layoutModules[$index]);
            } elseif ((int) $layoutModule['mod'] === (int) $modules['wem_sg_footer']->id) {
                $layoutModuleFooter = $layoutModule;
                unset($layoutModules[$index]);
            } elseif ((int) $layoutModule['mod'] === (int) $modules['breadcrumb']->id) {
                $layoutModuleBreadcrumb = $layoutModule;
                $layoutModuleBreadcrumbIndex = $index;
            } elseif (0 === (int) $layoutModule['mod']) { // content
                $layoutModuleContentIndex = $index;
            }
        }

        // breadcrumb is always placed before content
        if ($layoutModuleBreadcrumbIndex > $layoutModuleContentIndex) {
            unset($layoutModules[$layoutModuleBreadcrumbIndex]);
            ArrayUtil::arrayInsert($layoutModules, $layoutModuleContentIndex - 1, [$layoutModuleBreadcrumb]);
        }

        // Header is always first
        array_unshift($layoutModules, $layoutModuleHeader);
        // Footer is always last
        $layoutModules[] = $layoutModuleFooter;

        return $layoutModules;
    }

    public static function replaceHeader(int $layoutId, int $moduleHeaderId): void
    {
        $objLayout = LayoutModel::findByPk($layoutId);
        if (!$objLayout) {
            throw new InvalidArgumentException('Layout with id "'.$layoutId.'" not found');
        }

        $layoutModules = StringUtil::deserialize($objLayout->modules, true);
        $previousHeaderIndex = null;
        foreach ($layoutModules as $index => $layoutModule) {
            if ('header' === $layoutModule['col']) {
                $objModule = ModuleModel::findById($layoutModule['mod']);
                if (\in_array($objModule->type, ['wem_sg_header', 'header'], true)) {
                    $previousHeaderIndex = $index;
                    break;
                }
            }
        }

        if (null !== $previousHeaderIndex) {
            $layoutModules[$previousHeaderIndex]['mod'] = $moduleHeaderId;
            $layoutModules[$previousHeaderIndex]['enable'] = 1;
        } else {
            // header is first in header col
            array_unshift($layoutModules, ['mod' => $moduleHeaderId, 'col' => 'header', 'enable' => 1]);
        }

        $objLayout->modules = serialize($layoutModules);
        $objLayout->save();
    }

    public static function replaceFooter(int $layoutId, int $moduleFooterId): void
    {
        $objLayout = LayoutModel::findByPk($layoutId);
        if (!$objLayout) {
            throw new InvalidArgumentException('Layout with id "'.$layoutId.'" not found');
        }

        $layoutModules = array_reverse(StringUtil::deserialize($objLayout->modules, true), true);
        $previousFooterIndex = null;
        foreach ($layoutModules as $index => $layoutModule) {
            if ('footer' === $layoutModule['col']) {
                $objModule = ModuleModel::findById($layoutModule['mod']);
                if (\in_array($objModule->type, ['wem_sg_footer', 'footer', 'html'], true)) {
                    $previousFooterIndex = $index;
                    break;
                }
            }
        }

        $layoutModules = array_reverse($layoutModules, true);
        if (null !== $previousFooterIndex) {
            $layoutModules[$previousFooterIndex]['mod'] = $moduleFooterId;
            $layoutModules[$previousFooterIndex]['enable'] = 1;
        } else {
            // footer is last in footer col
            $layoutModules[] = ['mod' => $moduleFooterId, 'col' => 'footer', 'enable' => 1];
        }

        $objLayout->modules = serialize($layoutModules);
        $objLayout->save();
    }

    public static function replaceBreadcrumb(int $layoutId, int $moduleBreadcrumbId): void
    {
        $objLayout = LayoutModel::findByPk($layoutId);
        if (!$objLayout) {
            throw new InvalidArgumentException('Layout with id "'.$layoutId.'" not found');
        }

        $layoutModules = StringUtil::deserialize($objLayout->modules, true);
        $previousBreadcrumbIndex = null;
        $firstMainColumnIndex = null;
        foreach ($layoutModules as $index => $layoutModule) {
            if ('main' === $layoutModule['col']) {
                $firstMainColumnIndex = $firstMainColumnIndex ?? $index;

                $objModule = ModuleModel::findById($layoutModule['mod']);
                if (\in_array($objModule->type, ['breadcrumb'], true)) {
                    $previousBreadcrumbIndex = $index;
                    break;
                }
            }
        }

        if (null !== $previousBreadcrumbIndex) {
            $layoutModules[$previousBreadcrumbIndex]['mod'] = $moduleBreadcrumbId;
            $layoutModules[$previousBreadcrumbIndex]['enable'] = 1;
        } else {
            // breadcrumb is first in main col
            if (null !== $firstMainColumnIndex) {
                $layoutModulesBefore = \array_slice($layoutModules, 0, $firstMainColumnIndex);
                $layoutModulesAfter = \array_slice($layoutModules, $firstMainColumnIndex, null, true);
                $layoutModulesBefore[] = ['mod' => $moduleBreadcrumbId, 'col' => 'main', 'enable' => 1];

                $layoutModules = array_merge($layoutModulesBefore, $layoutModulesAfter);
            } else {
                $layoutModules[] = ['mod' => $moduleBreadcrumbId, 'col' => 'main', 'enable' => 1];
            }
        }

        $objLayout->modules = serialize($layoutModules);
        $objLayout->save();
    }
}
