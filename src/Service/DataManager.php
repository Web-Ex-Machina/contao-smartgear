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

namespace WEM\SmartgearBundle\Service;

use Contao\System;
use Exception;
use stdClass;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson;
use WEM\SmartgearBundle\Classes\DataManager\DataSetFinder;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Model\Dataset;
use WEM\SmartgearBundle\Model\DatasetInstall;

class DataManager
{
    /** @var ManagerJson */
    protected $configurationManager;
    /** @var ManagerJson */
    protected $dataManagerConfigurationManager;
    /** @var DataSetFinder */
    protected $finder;

    public function __construct(
        ManagerJson $configurationManager,
        ManagerJson $dataManagerConfigurationManager,
        DataSetFinder $finder
    ) {
        $this->configurationManager = $configurationManager;
        $this->dataManagerConfigurationManager = $dataManagerConfigurationManager;
        $this->finder = $finder;
    }

    public function synchroniseDatasetListFromDiskToDb(): void
    {
        $arrDatasets = $this->getDatasetListFromDisk();
        foreach ($arrDatasets as $arrDataset) {
            $objDataset = Dataset::findItems(['relative_path' => $arrDataset['relative_path']]);
            if (!$objDataset) {
                $objDataset = new Dataset();
            } else {
                $objDataset = $objDataset->current();
            }
            // update dataset DB here
            $objDataset->name = $arrDataset['name'];
            $objDataset->relative_path = $arrDataset['relative_path'];
            $objDataset->mainTable = $arrDataset['mainTable'];
            $objDataset->uninstallable = (bool) $arrDataset['uninstallable'] ? 1 : 0;
            $objDataset->allowMultipleInstall = (bool) $arrDataset['allowMultipleInstall'] ? 1 : 0;
            $objDataset->nb_elements = $arrDataset['nb_elements'];
            $objDataset->nb_media = $arrDataset['nb_media'];
            $objDataset->tstamp = !empty($objDataset->tstamp) ? $objDataset->tstamp : time();
            $objDataset->createdAt = !empty($objDataset->createdAt) ? $objDataset->createdAt : time();
            $objDataset->save();
        }
    }

    public function getDatasetListFromDisk(): array
    {
        $arrDatasetsFiles = $this->finder->buildList();
        foreach ($arrDatasetsFiles as $datasetPath) {
            $dtFile = $this->getDatasetClass($datasetPath);

            $arrDatasets[$datasetPath] = [
                'name' => $dtFile->getName(),
                'relative_path' => Util::getDatasetRelativePathFromPath(\dirname($datasetPath)),
                'mainTable' => $dtFile->getMainTable(),
                'uninstallable' => $dtFile->getUninstallable(),
                'allowMultipleInstall' => $dtFile->getAllowMultipleInstall(),
                'nb_elements' => 0,
                'nb_media' => 0,
            ];

            // handle nb of items & media
            $dataJson = $this->getDatasetJson($datasetPath);
            if ($dataJson) {
                $nbElements = 0;
                $nbMedia = 0;
                foreach ($dataJson->items as $item) {
                    if ('media' === $item->type) {
                        ++$nbMedia;
                    } else {
                        ++$nbElements;
                    }
                }
                $arrDatasets[$datasetPath]['nb_elements'] = $nbElements;
                $arrDatasets[$datasetPath]['nb_media'] = $nbMedia;
            }
        }

        return $arrDatasets;
    }

    public function installDataset(string $datasetPath, ?array $configuration = []): void
    {
        $dtFile = $this->getDatasetClass($datasetPath);

        if (!$this->canBeImported($datasetPath)) {
            throw new Exception('Dataset can\'t be imported');
        }

        $dtFile->import($configuration);
    }

    public function removeDataset(string $datasetPath): void
    {
        $dtFile = $this->getDatasetClass($datasetPath);

        if (!$this->canBeRemoved($datasetPath)) {
            throw new Exception('Dataset can\'t be removed');
        }

        $dtFile->remove();
    }

    public function getDatasetClass(string $datasetPath): \WEM\SmartgearBundle\Classes\DataManager\DataProvider
    {
        $datasetClassName = Util::getDatasetFQDNFromFilePath($datasetPath);

        require_once $datasetPath;

        return new $datasetClassName($this->dataManagerConfigurationManager);
    }

    public function getDatasetJson(string $datasetPath): stdClass
    {
        return json_decode(file_get_contents(str_replace('DataSet.php', 'data.json', $datasetPath)));
    }

    public function canBeRemoved(string $datasetPath): bool
    {
        return true;
    }

    public function canBeImported(string $datasetPath): bool
    {
        $dtFile = $this->getDatasetClass($datasetPath);

        if (!$dtFile->getAllowMultipleInstall()) {
            $objDataset = Dataset::findItems(['name' => $dtFile->getName()]);
            $nbInstall = DatasetInstall::countBy('pid', $objDataset->id);
            if ($nbInstall > 0) {
                return false;
            }
        }

        return $this->checkPtables($dtFile->getRequireTables()) && $this->checkSmartgear($dtFile->getRequireSmartgear()) && $dtFile->canBeImported();
    }

    public function needsConfiguration(string $datasetPath): bool
    {
        $dtFile = $this->getDatasetClass($datasetPath);

        return !empty($dtFile->getConfiguration());
    }

    protected function checkPtables(array $ptables): bool
    {
        if (empty($ptables)) {
            return true;
        }
        $connection = System::getContainer()->get('database_connection');
        $schemaManager = $connection->createSchemaManager();

        return $schemaManager->tablesExist($ptables);
    }

    protected function checkSmartgear(array $keys): bool
    {
        if (empty($keys)) {
            return true;
        }

        try {
            /** @var CoreConfig */
            $coreConfig = $this->configurationManager->load();
        } catch (Exception $e) {
            $coreConfig = new CoreConfig();
        }

        foreach ($keys as $key) {
            try {
                if ('core' === $key) {
                    $config = $coreConfig;
                } else {
                    $config = $coreConfig->getSubmoduleConfig($key);
                }
                if (!$config->getSgInstallComplete()) {
                    return false;
                }
            } catch (Exception $e) {
                return false;
            }
        }

        return true;
    }
}
