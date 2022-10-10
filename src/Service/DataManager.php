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

use Exception;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson;
use WEM\SmartgearBundle\Classes\DataManager\DataSetFinder;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Config\DataManager as DataManagerConfig;
use WEM\SmartgearBundle\Exceptions\File\NotFound as FileNotFound;

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

    public function getDatasetList(array $c, ?int $limit = 0, ?int $offset = 0): array
    {
        try {
            /** @var DataManagerConfig */
            $datamanagerConfig = $this->dataManagerConfigurationManager->load();
        } catch (FileNotFound $e) {
            $datamanagerConfig = new DataManagerConfig();
        }
        try {
            /** @var CoreConfig */
            $coreConfig = $this->configurationManager->load();
        } catch (FileNotFound $e) {
            $coreConfig = new CoreConfig();
        }
        $arrDatasetsFiles = $this->finder->buildList();
        foreach ($arrDatasetsFiles as $datasetPath) {
            require_once $datasetPath;
            $datasetClassName = Util::getDatasetFQDNFromPath($datasetPath);
            $dtFile = new $datasetClassName($this->dataManagerConfigurationManager);

            if ($c['module'] && '' !== $c['module'] && $c['module'] !== $dtFile->getModule()) {
                continue;
            }

            if ($c['type'] && '' !== $c['type'] && $c['type'] !== $dtFile->getType()) {
                continue;
            }

            $arrDatasets[$datasetPath] = [
                'name' => $dtFile->getName(),
                'type' => $dtFile->getType(),
                'module' => $dtFile->getModule(),
                'nb_elements' => 0,
                'nb_media' => 0,
                'date_installation' => 0,
                'status' => $GLOBALS['TL_LANG']['WEMSG']['FILTERS']['LBL']['statusUnavailable'],
            ];
            // handle status
            if ($datamanagerConfig->hasDataset($dtFile->getConfig())) {
                $arrDatasets[$datasetPath]['status'] = $GLOBALS['TL_LANG']['WEMSG']['FILTERS']['LBL']['statusInstalled'];
                $arrDatasets[$datasetPath]['date_installation'] = $datamanagerConfig->getDataset($arrDatasets[$datasetPath]['type'], $arrDatasets[$datasetPath]['module'], $arrDatasets[$datasetPath]['name'])->getDateInstallation();
            } else {
                try {
                    if ('core' === $dtFile->getModule()) {
                        $moduleConfig = $coreConfig;
                    } else {
                        $moduleConfig = $coreConfig->getSubmoduleConfig($dtFile->getModule());
                    }
                    if ($moduleConfig->getSgInstallComplete()) {
                        $arrDatasets[$datasetPath]['status'] = $GLOBALS['TL_LANG']['WEMSG']['FILTERS']['LBL']['statusNotInstalled'];
                    }
                } catch (Exception $e) {
                    //do nothing
                }
            }
            // handle nb of items & media
            $dataJson = json_decode(file_get_contents(str_replace('DataSet.php', 'data.json', $datasetPath)));
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
        // here we'll manage filters
        foreach ($arrDatasets as $datasetPath => $dataset) {
            if (\array_key_exists('status', $c) && '' !== (string) $c['status']) {
                if (-1 === (int) $c['status'] && $GLOBALS['TL_LANG']['WEMSG']['FILTERS']['LBL']['statusUnavailable'] !== $dataset['status']) {
                    unset($arrDatasets[$datasetPath]);
                } elseif (0 === (int) $c['status'] && $GLOBALS['TL_LANG']['WEMSG']['FILTERS']['LBL']['statusNotInstalled'] !== $dataset['status']) {
                    unset($arrDatasets[$datasetPath]);
                } elseif (1 === (int) $c['status'] && $GLOBALS['TL_LANG']['WEMSG']['FILTERS']['LBL']['statusInstalled'] !== $dataset['status']) {
                    unset($arrDatasets[$datasetPath]);
                }
            }
        }

        // here we'll manage ordering
        // installed by date
        // not installed by name
        // not available by name
        $arrDatasetsInstalled = [];
        $arrDatasetsNotInstalled = [];
        $arrDatasetsUnavailable = [];
        foreach ($arrDatasets as $datasetPath => $dataset) {
            switch ($dataset['status']) {
                case $GLOBALS['TL_LANG']['WEMSG']['FILTERS']['LBL']['statusUnavailable']:
                    $arrDatasetsUnavailable[$datasetPath] = $dataset;
                break;
                case $GLOBALS['TL_LANG']['WEMSG']['FILTERS']['LBL']['statusNotInstalled']:
                    $arrDatasetsNotInstalled[$datasetPath] = $dataset;
                    break;
                case $GLOBALS['TL_LANG']['WEMSG']['FILTERS']['LBL']['statusInstalled']:
                    $arrDatasetsInstalled[$datasetPath] = $dataset;
                    break;
            }
        }

        $arrDatasetsInstalled = Util::array_sort($arrDatasetsInstalled, 'date_installation', \SORT_DESC);
        $arrDatasetsNotInstalled = Util::array_sort($arrDatasetsNotInstalled, 'name', \SORT_ASC);
        $arrDatasetsUnavailable = Util::array_sort($arrDatasetsUnavailable, 'name', \SORT_ASC);

        return $arrDatasetsInstalled + $arrDatasetsNotInstalled + $arrDatasetsUnavailable;
    }

    public function installDataset(string $datasetPath): void
    {
        $datasetClassName = Util::getDatasetFQDNFromPath($datasetPath);
        require_once $datasetPath;
        $dtFile = new $datasetClassName($this->dataManagerConfigurationManager);

        $dtFile->import();
    }

    public function removeDataset(string $datasetPath): void
    {
        $datasetClassName = Util::getDatasetFQDNFromPath($datasetPath);
        require_once $datasetPath;
        $dtFile = new $datasetClassName($this->dataManagerConfigurationManager);

        $dtFile->remove();
    }
}
