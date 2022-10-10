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

namespace WEM\SmartgearBundle\Dataset\Example;

use Exception;
use WEM\SmartgearBundle\Classes\DataManager\DataProvider;
use WEM\SmartgearBundle\Classes\DataManager\DataSetInterface;
use WEM\SmartgearBundle\Config\DataManager;
use WEM\SmartgearBundle\Config\DataManagerDataSet;
use WEM\SmartgearBundle\Config\DataManagerDataSetItem;
use WEM\SmartgearBundle\Exceptions\File\NotFound as FileNotFoundException;

class DataSet extends DataProvider implements DataSetInterface
{
    protected $name = 'sample';
    protected $type = 'component';
    protected $module = 'core';

    public function import(): DataManagerDataSet
    {
        if ($this->isInstalled()) {
            throw new Exception('DataSet already installed');
        }
        $this->config->setDateInstallation(time());

        $json = $this->getDataAsObject();
        foreach ($json->items as $item) {
            /** @var DataManagerDataSetItem */
            $itemConfig = $this->installItem($item);
            $this->config->addItem($itemConfig);
        }

        try {
            /** @var DataManager */
            $datamanagerConfig = $this->configurationManager->load();
        } catch (FileNotFoundException $e) {
            $datamanagerConfig = new DataManager();
        }
        $datamanagerConfig->addDataset($this->config);
        $this->configurationManager->save($datamanagerConfig);

        return $this->config;
    }

    public function remove(): void
    {
        if (!$this->isInstalled()) {
            throw new Exception('DataSet not installed');
        }
        /** @var DataManagerDataSet */
        $datasetConfig = $this->configurationManager->load()->getDataset($this->config->getType(), $this->config->getModule(), $this->config->getName());

        foreach ($datasetConfig->getItems() as $item) {
            $this->removeItem($item);
        }
        /** @var DataManager */
        $datamanagerConfig = $this->configurationManager->load();
        $datamanagerConfig->removeDataset($datasetConfig);
        $this->configurationManager->save($datamanagerConfig);
    }
}
