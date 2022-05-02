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
                    $this->filters['select']['author']['options'][$objItems->current()->status]['selected'] = true;
                    $this->config['author'] = $objItems->current()->id;
                }
            }
        }

        $this->filters['month']['date'] = [
            'label' => $GLOBALS['TL_LANG']['WEMSG']['FILTERS']['LBL']['date'],
            'year' => [
                'start' => 2000,
                'stop' => (int) date('Y'),
            ],
        ];

        if (null !== Input::get('date', null)) {
            $this->config['date']['month'] = Input::get('date', null)['month'];
            $this->config['date']['year'] = Input::get('date', null)['year'];
        }
    }
}