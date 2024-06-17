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

use Contao\ArticleModel;
use Contao\PageModel;

class ArticleUtil
{
    /**
     * Shortcut for article creation.
     */
    public static function createArticle(PageModel $objPage, ?array $arrData = []): ArticleModel
    {
        // Create the article
        $objArticle = isset($arrData['id']) ? ArticleModel::findById($arrData['id']) ?? new ArticleModel() : new ArticleModel();
        $objArticle->tstamp = time();
        $objArticle->pid = $objPage->id;
        $objArticle->sorting = (ArticleModel::countBy('pid', $objPage->id) + 1) * 128;
        $objArticle->title = $objPage->title;
        $objArticle->alias = $objPage->alias;
        $objArticle->author = 1;
        $objArticle->inColumn = 'main';
        $objArticle->published = 1;

        // Now we get the default values, get the arrData table
        if ($arrData !== null && $arrData !== []) {
            foreach ($arrData as $k => $v) {
                $objArticle->$k = $v;
            }
        }

        $objArticle->save();

        // Return the model
        return $objArticle;
    }
}
