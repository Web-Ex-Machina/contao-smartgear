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

namespace WEM\SmartgearBundle\Hooks;

/**
 * Class GenerateBreadcrumbHook.
 *
 * Handle Smartgear generateBreadcrumb hooks
 */
class GenerateBreadcrumbHook
{
    /**
     * Catch requests from external websites.
     *
     * @return array
     */
    public function updateRootItem(array $arrItems, \Contao\Module $objModule)
    {
        $arrSourceItems = $arrItems;

        try {
            // Determine if we are at the root of the website
            global $objPage;
            $objHomePage = \Contao\PageModel::findFirstPublishedRegularByPid($objPage->rootId);

            // If we are, remove breadcrumb
            if ($objHomePage->id === $objPage->id) {
                return [];
            }

            return $arrSourceItems;
        } catch (\Exception $e) {
            return $arrSourceItems;
        }

        return $arrItems;
    }
}
