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

namespace WEM\SmartgearBundle\Classes;

use Contao\File;

class FileUtil
{
    /**
     * Check if a file is displaybale in browser.
     *
     * @param File $objFile The file to check
     */
    public static function isDisplayableInBrowser(File $objFile): bool
    {
        $mime = strtolower($objFile->mime);

        if ('image/' === substr($mime, 0, 6)
        || 'application/pdf' === $mime
        ) {
            return true;
        }

        return false;
    }
}
