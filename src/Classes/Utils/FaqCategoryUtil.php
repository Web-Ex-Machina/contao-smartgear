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

use Contao\FaqCategoryModel;
use InvalidArgumentException;

class FaqCategoryUtil
{
    /**
     * Shortcut for FAQ category creation.
     */
    public static function createFaqCategory(string $strTitle, ?array $arrData = []): FaqCategoryModel
    {
        // Create the theme
        if (\array_key_exists('id', $arrData)) {
            $objFaqCategory = FaqCategoryModel::findOneById($arrData['id']);
            if (!$objFaqCategory) {
                throw new InvalidArgumentException('La categorie FAQ ayant pour id "'.$arrData['id'].'" n\'existe pas');
            }
        } else {
            $objFaqCategory = new FaqCategoryModel();
        }

        $objFaqCategory->title = $strTitle;
        $objFaqCategory->tstamp = time();

        // Now we get the default values, get the arrData table
        if (!empty($arrData)) {
            foreach ($arrData as $k => $v) {
                $objFaqCategory->$k = $v;
            }
        }

        $objFaqCategory->save();

        return $objFaqCategory;
    }
}
