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

namespace WEM\SmartgearBundle\Config;

use Exception;
use WEM\SmartgearBundle\Classes\Config\ConfigJsonInterface;

class DataManager implements ConfigJsonInterface
{
    /** @var array */
    protected $datasets = [];

    public function reset(): self
    {
        $this->datasets = [];

        return $this;
    }

    public function import(\stdClass $json): self
    {
        $this->reset();
        if ($json->datasets) {
            foreach ($json->datasets as $dataset) {
                $this->addDataset((new DataManagerDataSet())->import($dataset));
            }
        }

        return $this;
    }

    public function export(): string
    {
        $json = new \stdClass();

        $arrDatasets = [];
        foreach ($this->datasets as $dataset) {
            $arrDatasets[] = $dataset->export();
        }

        $json->datasets = $arrDatasets;

        return json_encode($json, \JSON_PRETTY_PRINT);
    }

    public function getDatasets(): array
    {
        return $this->datasets;
    }

    public function setDatasets(array $datasets): self
    {
        $this->datasets = $datasets;

        return $this;
    }

    public function addDataset(DataManagerDataSet $item): self
    {
        $this->datasets[] = $item;

        return $this;
    }

    public function removeDataset(DataManagerDataSet $item): self
    {
        foreach ($this->datasets as $key => $dataset) {
            if ($this->buildDatasetAlias($dataset) === $this->buildDatasetAlias($item)) {
                unset($this->datasets[$key]);

                return $this;
            }
        }

        throw new Exception('Unable to find the dataset to remove');
    }

    public function hasDataset(DataManagerDataSet $item): bool
    {
        foreach ($this->datasets as $key => $dataset) {
            if ($this->buildDatasetAlias($dataset) === $this->buildDatasetAlias($item)) {
                return true;
            }
        }

        return false;
    }

    // public function getDataset(string $type, string $module, string $name): DataManagerDataSet
    public function getDataset(string $name): DataManagerDataSet
    {
        $item = (new DataManagerDataSet())
            // ->setType($type)
            // ->setModule($module)
            ->setName($name)
        ;
        foreach ($this->datasets as $key => $dataset) {
            if ($this->buildDatasetAlias($dataset) === $this->buildDatasetAlias($item)) {
                return $dataset;
            }
        }

        throw new Exception('Unable to find the dataset');
    }

    protected function buildDatasetAlias(DataManagerDataSet $item): string
    {
        return sprintf('%s', $item->getName());
        // return sprintf('%s-%s-%s', $item->getType(), $item->getModule(), $item->getName());
    }
}
