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

namespace WEM\SmartgearBundle\Override;

class ModuleNewsReader extends \Contao\ModuleNewsReader
{
    /**
     * Generate the module.
     */
    protected function compile(): void
    {
        parent::compile();
        // Get the news item
        /** @var \Contao\NewsModel */
        $objArticle = \Contao\NewsModel::findPublishedByParentAndIdOrAlias(\Contao\Input::get('items'), $this->news_archives);
        if ($objArticle) {
            $htmlDecoder = \Contao\System::getContainer()->get('contao.string.html_decoder');

            if ($objArticle->pageTitle) {
                $GLOBALS['TL_HEAD'][] = sprintf('<meta name="og:title" content="%s">', $objArticle->pageTitle); // Already stored decoded
            } elseif ($objArticle->headline) {
                $GLOBALS['TL_HEAD'][] = sprintf('<meta name="og:title" content="%s">', $htmlDecoder->inputEncodedToPlainText($objArticle->headline));
            }

            if ($objArticle->description) {
                $GLOBALS['TL_HEAD'][] = sprintf('<meta name="og:description" content="%s">', $htmlDecoder->inputEncodedToPlainText($objArticle->description));
            } elseif ($objArticle->teaser) {
                $GLOBALS['TL_HEAD'][] = sprintf('<meta name="og:description" content="%s">', $htmlDecoder->htmlToPlainText($objArticle->teaser));
            }

            if ($objArticle->addImage) {
                $objImage = \Contao\FilesModel::findByUuid($objArticle->singleSRC);
                if ($objImage) {
                    $GLOBALS['TL_HEAD'][] = sprintf('<meta name="og:image" content="%s">', \Contao\Environment::get('base').$objImage->path);
                }
            }
        }
    }
}
