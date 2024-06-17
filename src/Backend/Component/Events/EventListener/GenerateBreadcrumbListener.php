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

namespace WEM\SmartgearBundle\Backend\Component\Events\EventListener;

use Contao\CalendarEventsModel;
use Contao\Environment;
use Contao\Input;
use Contao\Model\Collection;
use Contao\Module;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Exceptions\File\NotFound as FileNotFoundException;
use WEM\SmartgearBundle\Model\Configuration\ConfigurationItem;

class GenerateBreadcrumbListener
{
    public function __construct(protected CoreConfigurationManager $coreConfigurationManager)
    {
    }

    public function __invoke(array $items, Module $module): array
    {
        $arrSourceItems = $items;

        // $eventPageId = null;
        // try {
        //     /** @var CoreConfig $config */
        //     $config = $this->coreConfigurationManager->load();
        //     $eventConfig = $config->getSgEvents();
        //     $eventPageId = $eventConfig->getSgInstallComplete() ? $eventConfig->getSgPage() : $eventPageId;
        // } catch (FileNotFoundException $e) {
        //     //nothing
        // }

        try {
            // Determine if we are at the root of the website
            global $objPage;

            $objConfigurationItemEvent = ConfigurationItem::findItems(['contao_page' => $objPage->id, 'type' => ConfigurationItem::TYPE_MIXED_EVENTS], 1);

            // if ((int) $objPage->id === (int) $eventPageId) {
            if ($objConfigurationItemEvent instanceof Collection) {
                // get the current tl_news
                $objEvent = CalendarEventsModel::findPublishedByParentAndIdOrAlias(Input::get('auto_item'), [$objConfigurationItemEvent->contao_calendar]);
                if ($objEvent) {
                    $items[\count($items) - 1]['isActive'] = false;
                    $items[] = [
                        'isActive' => true,
                        'title' => $objEvent->title,
                        'link' => $objEvent->title,
                        'href' => Environment::get('uri'),
                    ];
                }
            }

            return $items;
        } catch (\Exception) {
            return $arrSourceItems;
        }
    }
}
