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
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\Input;
use WEM\SmartgearBundle\Model\CalendarEvents;

#[AsFrontendModule(type: 'events', name:'eventlist')]
class ModuleEventList extends \Contao\ModuleEventlist
{
    protected array $filters = [];

    protected array $arrFilters = ['author', 'date'];

    protected array $config = [];

    public function getArrFilters(): array
    {
        return $this->arrFilters;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    protected function compile(): void
    {
        // Build List Filters
        $this->buildFilters();
        $this->adaptFiltersToEventlist();

        parent::compile();

        $this->Template->filters = $this->filters;
        $this->Template->config = $this->config;
    }

    protected function buildFilters(): void
    {
        $datesBounds = $this->getEventsDatesBound();
        $this->filters['month']['date'] = [
            'label' => $GLOBALS['TL_LANG']['WEMSG']['FILTERS']['LBL']['date'],
            'year' => [
                'label' => $GLOBALS['TL_LANG']['WEMSG']['FILTERS']['LBL']['dateYear'],
                'start' => $datesBounds['start'],
                'stop' => $datesBounds['stop'],
            ],
            'month' => [
                'label' => $GLOBALS['TL_LANG']['WEMSG']['FILTERS']['LBL']['dateMonth'],
            ],
        ];

        if (null !== Input::get('date')
        && (
            '' !== Input::get('date')['year']
            || '' !== Input::get('date')['month']
        )
        ) {
            $this->config['date']['year'] = Input::get('date')['year'];
            $this->config['date']['month'] = Input::get('date')['month'];

            if (!empty($this->config['date']['month'])) {
                $this->cal_format = 'cal_month';
            } elseif (!empty($this->config['date']['year'])) {
                $this->cal_format = 'cal_year';
            }
        }

        $locations = $this->getEventsLocations();
        $this->filters['select']['location'] = [
            'label' => $GLOBALS['TL_LANG']['WEMSG']['FILTERS']['LBL']['location'],
            'options' => [],
        ];
        foreach ($locations as $location) {
            $this->filters['select']['location']['options'][] = ['value' => $location, 'label' => $location];
        }

        if (null !== Input::get('location')) {
            $this->config['location'] = Input::get('location');
        }
    }

    protected function adaptFiltersToEventlist(): void
    {
        if (\array_key_exists('date', $this->config)) {
            if (\array_key_exists('month', $this->config['date']) && !empty($this->config['date']['month'])) {
                $_GET['month'] = sprintf('%s%s', empty($this->config['date']['year']) ? date('Y') : $this->config['date']['year'], $this->config['date']['month']);
            } elseif (\array_key_exists('year', $this->config['date']) && !empty($this->config['date']['year'])) {
                $_GET['year'] = $this->config['date']['year'];
            }
        }
    }

    protected function getEventsDatesBound(): array
    {
        $col = ['pid IN (?)', 'published = ?'];
        $val = [implode(',', $this->cal_calendar), 1];
        $firstEvent = CalendarEventsModel::findBy($col, $val, ['limit' => 1, 'order' => 'startDate ASC']);
        $lastEvent = CalendarEventsModel::findBy($col, $val, ['limit' => 1, 'order' => 'startDate DESC']);

        return [
            'start' => (new \DateTime())->setTimestamp((int) $firstEvent->startDate)->format('Y'),
            'stop' => (new \DateTime())->setTimestamp((int) $lastEvent->startDate)->format('Y'),
        ];
    }

    protected function getEventsLocations(): array
    {
        return (new CalendarEvents())->getAllLocations($this->cal_calendar);
    }
}
