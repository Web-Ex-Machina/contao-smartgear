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

use Contao\Config;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;

#[AsFrontendModule(type: 'events', name:'calendar')]
class ModuleCalendar extends \Contao\ModuleCalendar
{
    protected array $filters = [];

    protected array $arrFilters = [];

    protected array $config = [];

    public function getArrFilters(): array
    {
        return $this->arrFilters;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function generate(): bool|string
    {
        // Show the event reader if an item has been selected
        if ($this->cal_readerModule > 0 && (isset($_GET['events']) || (Config::get('useAutoItem') && isset($_GET['auto_item'])))) {
            return $this->getFrontendModule($this->cal_readerModule, $this->strColumn);
        }

        return parent::generate();
    }

    /**
     * Generate the module.
     */
    protected function compile(): void
    {
        parent::compile();

        $this->Template->filters = $this->filters;
        $this->Template->config = $this->config;
    }
}
