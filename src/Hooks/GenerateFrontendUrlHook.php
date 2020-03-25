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
 * Class GenerateFrontendUrlHook
 *
 * Handle Smartgear generateFrontendUrl hooks
 */
class GenerateFrontendUrlHook
{
    /**
     * Make sure empty requests are correctly redirected as root page
     *
     * @param $arrRow
     * @param $strParams
     * @param $strUrl
     *
     * @return mixed|string
     */
    public function generateFrontendUrl($arrRow, $strParams, $strUrl)
    {
        if (!is_array($arrRow)) {
            throw new \Exception('not an associative array.');
        }

        // Catch "/" page aliases and do not add suffix to them (as they are considered as base request)
        if ($strUrl == "/".\Config::get('urlSuffix')) {
            $strUrl = "/";
        }

        return $strUrl;
    }
}
