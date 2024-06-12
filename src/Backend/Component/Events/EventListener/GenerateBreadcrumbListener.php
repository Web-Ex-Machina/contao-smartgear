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

use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Exceptions\File\NotFound as FileNotFoundException;
use WEM\SmartgearBundle\Model\Configuration\ConfigurationItem;

class GenerateBreadcrumbListener
{
    /** @var CoreConfigurationManager */
    protected $coreConfigurationManager;

    public function __construct(
        CoreConfigurationManager $coreConfigurationManager
    ) {
        $this->coreConfigurationManager = $coreConfigurationManager;
    }

    public function __invoke(array $items, \Contao\Module $module): array
    {
        $arrSourceItems = $items;

        // $eventPageId = null;
        // try {
        //     /** @var CoreConfig */
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
            if ($objConfigurationItemEvent) {
                // get the current tl_news
                $objEvent = \Contao\CalendarEventsModel::findPublishedByParentAndIdOrAlias(\Contao\Input::get('auto_item'), [$objConfigurationItemEvent->contao_calendar]);
                if ($objEvent) {
                    $items[\count($items) - 1]['isActive'] = false;
                    $items[] = [
                        'isActive' => true,
                        'title' => $objEvent->title,
                        'link' => $objEvent->title,
                        'href' => \Contao\Environment::get('uri'),
                    ];
                }
            }

            return $items;
        } catch (\Exception) {
            return $arrSourceItems;
        }
    }
}
