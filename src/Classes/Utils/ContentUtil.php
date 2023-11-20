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

use Contao\ContentModel;

class ContentUtil
{
    /**
     * Shortcut for content creation.
     */
    public static function createContent($objArticle, $arrData = []): ContentModel
    {
        // Dynamic ptable support
        if (!$arrData['ptable']) {
            $arrData['ptable'] = 'tl_article';
        }

        $objContentHighestSorting = ContentModel::findOneBy(['pid = ?', 'ptable = ?'], [$objArticle->id, $arrData['ptable']], ['order' => 'sorting DESC']);

        // Create the content
        $objContent = isset($arrData['id']) ? ContentModel::findById($arrData['id']) ?? new ContentModel() : new ContentModel();
        $objContent->tstamp = time();
        $objContent->pid = $objArticle->id;
        $objContent->ptable = $arrData['ptable'];
        $objContent->sorting = $objContentHighestSorting->sorting + 128;
        $objContent->type = 'text';

        // Now we get the default values, get the arrData table
        if (!empty($arrData)) {
            foreach ($arrData as $k => $v) {
                $objContent->$k = $v;
            }
        }

        $objContent->save();

        // Return the model
        return $objContent;
    }
}
