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

namespace WEM\SmartgearBundle\EventListener;

/**
 * Class GenerateFrontendUrlListener.
 *
 * Handle Smartgear generateFrontendUrl hooks
 */
class GenerateFrontendUrlListener
{
    /**
     * Make sure empty requests are correctly redirected as root page.
     */
    public function __invoke(array $arrRow, string $strParams, string $strUrl): string
    {
        if (!\is_array($arrRow)) {
            throw new \Exception('not an associative array.');
        }

        // Catch "/" page aliases and do not add suffix to them (as they are considered as base request)
        if ('/' === $arrRow['alias']) {
            $strUrl = '/';
        }

        return $strUrl;
    }
}
