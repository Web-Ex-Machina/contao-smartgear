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

namespace WEM\SmartgearBundle\EventListener;

class GetAllEventsListener
{
    public function __invoke(array $events, array $calendars, int $timeStart, int $timeEnd, \Contao\Module $module): array
    {
        $searchConfig = $module->getConfig();
        if (!empty($searchConfig)) {
            // we get rid of events not compliant with our criterias
            foreach ($events as $index => $event) {
                // do our things
            }
        }

        return $events;
    }
}
