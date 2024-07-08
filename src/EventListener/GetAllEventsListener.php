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

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use WEM\UtilsBundle\Classes\ScopeMatcher;

#[AsHook('getAllEvents',null,-1)]
class GetAllEventsListener
{
    public function __construct(protected readonly ScopeMatcher $scopeMatcher)
    {
    }

    public function __invoke(array $events, array $calendars, int $timeStart, int $timeEnd, \Contao\Module $module): array
    {
        if(!$this->scopeMatcher->isFrontend()) {exit();}

        $searchConfig = $module->getConfig();
        if (!empty($searchConfig)) {
            // we get rid of events not compliant with our criterias
            foreach ($events as $startDate => $dateEvents) {
                foreach ($dateEvents as $startTime => $timeEvents) {
                    foreach ($timeEvents as $index => $event) {
                        // do our things
                        if (\array_key_exists('location', $searchConfig) && !empty($searchConfig['location']) && $event['location'] !== $searchConfig['location']) {
                            unset($events[$startDate][$startTime][$index]);
                        }
                    }
                }
            }
        }

        return $events;
    }
}
