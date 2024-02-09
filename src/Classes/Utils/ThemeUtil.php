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

use Contao\ThemeModel;
use InvalidArgumentException;

class ThemeUtil
{
    /**
     * Shortcut for theme creation.
     */
    public static function createTheme(string $strName, ?array $arrData = []): ThemeModel
    {
        // Create the theme
        if (\array_key_exists('id', $arrData)) {
            $objTheme = ThemeModel::findOneById($arrData['id']);
            if (!$objTheme) {
                throw new InvalidArgumentException('Le thème ayant pour id "'.$arrData['id'].'" n\'existe pas');
            }
        } else {
            $objTheme = new ThemeModel();
        }

        $objTheme->name = $strName;
        $objTheme->tstamp = time();

        // Now we get the default values, get the arrData table
        if (!empty($arrData)) {
            foreach ($arrData as $k => $v) {
                $objTheme->$k = $v;
            }
        }

        $objTheme->save();

        return $objTheme;
    }
}
