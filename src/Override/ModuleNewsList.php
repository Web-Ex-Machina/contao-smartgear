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

use Contao\Input;
use Contao\NewsModel;
use Contao\UserModel;

class ModuleNewsList extends \Contao\ModuleNewsList
{
    /**
     * List of filters, formatted.
     *
     * @var array
     */
    protected $filters = [];
    protected $arrFilters = ['author', 'date'];
    protected $config = [];

    public function getArrFilters(): array
    {
        return $this->arrFilters;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Generate the module.
     */
    protected function compile(): void
    {
        // Build List Filters
        $this->buildFilters();

        parent::compile();

        $this->Template->filters = $this->filters;
        $this->Template->config = $this->config;
    }

    protected function buildFilters(): void
    {
        $objItems = UserModel::findAll();
        if ($objItems) {
            $this->filters['select']['author'] = ['label' => $GLOBALS['TL_LANG']['WEMSG']['FILTERS']['LBL']['author'], 'options' => []];

            while ($objItems->next()) {
                $this->filters['select']['author']['options'][$objItems->current()->id] = ['label' => $objItems->current()->name, 'value' => $objItems->current()->id];

                if ($objItems->current()->id === Input::get('author')) {
                    $this->filters['select']['author']['options'][$objItems->current()->id]['selected'] = true;
                    $this->config['author'] = $objItems->current()->id;
                }
            }
        }

        $datesBounds = $this->getNewsDatesBound();

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
        if (null !== Input::get('date')) {
            $this->config['date']['month'] = Input::get('date')['month'];
            $this->config['date']['year'] = Input::get('date')['year'];
        }
    }

    protected function getNewsDatesBound(): array
    {
        $col = ['pid IN (?)', 'published = ?'];
        $val = [implode(',', $this->news_archives), 1];
        $firstEvent = NewsModel::findBy($col, $val, ['limit' => 1, 'order' => 'published ASC, tstamp ASC']);
        $lastEvent = NewsModel::findBy($col, $val, ['limit' => 1, 'order' => 'published DESC, tstamp DESC']);

        return [
            'start' => (new \DateTime())->setTimestamp((int) ('' !== $firstEvent->start ? $firstEvent->start : $firstEvent->tstamp))->format('Y'),
            'stop' => (new \DateTime())->setTimestamp((int) ('' !== $lastEvent->start ? $lastEvent->start : $lastEvent->tstamp))->format('Y'),
        ];
    }
}
