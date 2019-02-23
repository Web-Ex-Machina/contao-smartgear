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

namespace WEM\Smartgear\Hooks;

/**
 * Class GenerateFrontendUrlHook
 *
 * Handle Smartgear generateFrontendUrl hooks
 */
class GenerateFrontendUrlHook
{
    /**
     * @param $arrRow
     * @param $strParams
     * @param $strUrl
     * @return mixed|string
     * @throws \Exception
     *
     * @todo:   Don't use TL_LANGUAGE in backend
     * @todo:   Get language by domain
     * @todo:   I18nl10n::getInstance()->getLanguagesByDomain() not valid in BE, since the domain is taken from url
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
