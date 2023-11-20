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

use Contao\LayoutModel;
use InvalidArgumentException;

class LayoutUtil
{
    /**
     * Shortcut for layout creation.
     */
    public static function createLayout(string $strTitle, int $pid, ?array $arrData = []): LayoutModel
    {
        // Create the theme
        if (\array_key_exists('id', $arrData)) {
            $objLayout = LayoutModel::findOneById($arrData['id']);
            if (!$objLayout) {
                throw new InvalidArgumentException('La présentation de page ayant pour id "'.$arrData['id'].'" n\'existe pas');
            }
        } else {
            $objLayout = new LayoutModel();
        }

        $objLayout->title = $strTitle;
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

    public static function createLayoutFullpage(string $strTitle, int $pid, ?array $arrData = []): LayoutModel
    {
        return self::createLayout($strTitle, $pid, $arrData);
    }

    public static function createLayoutStandard(string $strTitle, int $pid, ?array $arrData = []): LayoutModel
    {
        return self::createLayout($strTitle, $pid, $arrData);
    }
}
