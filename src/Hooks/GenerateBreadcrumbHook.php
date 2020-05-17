<?php

/**
 * SMARTGEAR for Contao Open Source CMS
 *
 * Copyright (c) 2015-2019 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

namespace WEM\SmartgearBundle\Hooks;

/**
 * Class GenerateBreadcrumbHook
 *
 * Handle Smartgear generateBreadcrumb hooks
 */
class GenerateBreadcrumbHook
{
    /**
     * Catch requests from external websites
     *
     * @return mixed|string
     */
    public function updateRootItem($arrItems, \Module $objModule)
    {
        $arrSourceItems = $arrItems;

        try {
            // Determine if we are at the root of the website
            global $objPage;
            $objHomePage = \PageModel::findFirstPublishedRegularByPid($objPage->rootId);
            
            // If we are, remove breadcrumb
            if ($objHomePage->id === $objPage->id) {
                return [];
            }
        } catch (\Exception $e) {
            return $arrSourceItems;
        }

        return $arrItems;
    }
}
