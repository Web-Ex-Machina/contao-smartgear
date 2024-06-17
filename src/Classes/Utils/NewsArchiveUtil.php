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

use Contao\NewsArchiveModel;
use InvalidArgumentException;

class NewsArchiveUtil
{
    /**
     * Shortcut for NewsArchive creation.
     */
    public static function createNewsArchive(string $strTitle, int $jumpTo, ?array $arrData = []): NewsArchiveModel
    {
        // Create the theme
        if (\array_key_exists('id', $arrData)) {
            $objCalFee = NewsArchiveModel::findOneById($arrData['id']);
            if (!$objCalFee) {
                throw new InvalidArgumentException('L\'archive d\'actualités ayant pour id "'.$arrData['id'].'" n\'existe pas');
            }
        } else {
            $objCalFee = new NewsArchiveModel();
        }

        $objCalFee->title = $strTitle;
        $objCalFee->jumpTo = $jumpTo;
        $objCalFee->tstamp = time();

        // Now we get the default values, get the arrData table
        if ($arrData !== null && $arrData !== []) {
            foreach ($arrData as $k => $v) {
                $objCalFee->$k = $v;
            }
        }

        $objCalFee->save();

        return $objCalFee;
    }
}
