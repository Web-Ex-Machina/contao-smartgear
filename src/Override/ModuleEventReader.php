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

use Contao\CalendarEventsModel;
use Contao\Environment;
use Contao\FilesModel;
use Contao\Input;
use Contao\PageModel;
use Contao\System;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use WEM\SmartgearBundle\Exceptions\File\NotFound as FileNotFound;

class ModuleEventReader extends \Contao\ModuleEventReader
{
    /**
     * Generate the module.
     */
    protected function compile(): void
    {
        parent::compile();
        // Get the news item
        /** @var CalendarEventsModel $objEvent */
        $objEvent = CalendarEventsModel::findPublishedByParentAndIdOrAlias(Input::get('events'), $this->cal_calendar);
        if ($objEvent) {
            $htmlDecoder = System::getContainer()->get('contao.string.html_decoder');

            if ($objEvent->pageTitle) {
                $GLOBALS['TL_HEAD'][] = sprintf('<meta property="og:title" content="%s">', $objEvent->pageTitle); // Already stored decoded
                $GLOBALS['TL_HEAD'][] = sprintf('<meta property="title" content="%s">', $objEvent->pageTitle); // Already stored decoded
            } elseif ($objEvent->headline) {
                $GLOBALS['TL_HEAD'][] = sprintf('<meta property="og:title" content="%s">', $htmlDecoder->inputEncodedToPlainText($objEvent->headline));
                $GLOBALS['TL_HEAD'][] = sprintf('<meta property="title" content="%s">', $htmlDecoder->inputEncodedToPlainText($objEvent->headline));
            } elseif ($objEvent->title) {
                $GLOBALS['TL_HEAD'][] = sprintf('<meta property="og:title" content="%s">', $htmlDecoder->inputEncodedToPlainText($objEvent->title));
                $GLOBALS['TL_HEAD'][] = sprintf('<meta property="title" content="%s">', $htmlDecoder->inputEncodedToPlainText($objEvent->title));
            }

            if ($objEvent->description) {
                $GLOBALS['TL_HEAD'][] = sprintf('<meta property="og:description" content="%s">', $htmlDecoder->inputEncodedToPlainText($objEvent->description));
            } elseif ($objEvent->teaser) {
                $GLOBALS['TL_HEAD'][] = sprintf('<meta property="og:description" content="%s">', $htmlDecoder->htmlToPlainText($objEvent->teaser));
            }

            if ($objEvent->addImage) {
                $objImage = FilesModel::findByUuid($objEvent->singleSRC);
                if ($objImage) {
                    $GLOBALS['TL_HEAD'][] = sprintf('<meta property="og:image" content="%s">', Environment::get('base').$objImage->path);
                }
            }
        }

        $configManager = System::getContainer()->get('smartgear.config.manager.core');
        /* @var UrlGeneratorInterface $routeGenerator*/
        $routeGenerator = System::getContainer()->get('contao.routing.content_url_generator');
        try {
            $eventConfig = $configManager->load()->getSgEvents();
            if ($eventConfig->getSgInstallComplete()) {
                $objPage = PageModel::findByPk($eventConfig->getSgPage());
                $this->Template->referer = $routeGenerator->generate($objPage->name, [], UrlGeneratorInterface::ABSOLUTE_URL);
            }
        } catch (FileNotFound) {
            // nothing
        }
    }
}
