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

namespace WEM\SmartgearBundle\Classes\DataManager;

use Contao\File;
use Contao\FilesModel;
use Contao\Model;
use Exception;
use ReflectionClass;
use stdClass;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as DataManagerConfig;
use WEM\SmartgearBundle\Config\DataManager;
use WEM\SmartgearBundle\Config\DataManagerDataSet;
use WEM\SmartgearBundle\Config\DataManagerDataSetItem;
use WEM\SmartgearBundle\Exceptions\File\NotFound as FileNotFoundException;

class DataProvider
{
    /** @var array */
    protected $references = [];
    /** @var DataManagerDataSet */
    protected $config;
    /** @var DataManagerConfig */
    protected $configurationManager;

    public function __construct(DataManagerConfig $configurationManager)
    {
        $this->configurationManager = $configurationManager;
        $this->config = new DataManagerDataSet();
        $this->config->setModule($this->module);
        $this->config->setType($this->type);
        $this->config->setName($this->name);
    }

    public function getModule(): string
    {
        return $this->module;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isInstalled(): bool
    {
        try {
            /** @var DataManager */
            $config = $this->configurationManager->load();
        } catch (FileNotFoundException $e) {
            $config = new DataManager();
        }

        return $config->hasDataset($this->config);
    }

    public function duplicateMediaToFiles(string $sourcePath, string $targetPath): FilesModel
    {
        $objFileModel = FilesModel::findByPath($targetPath);
        if (!$objFileModel) {
            $objFile = new File($targetPath);
            $objFile->write(file_get_contents($sourcePath));
            $objFile->close();
            $objFileModel = $objFile->getModel();
        }

        return $objFileModel;
    }

    public function getDataAsObject(): stdClass
    {
        // get reflection for the current class
        $reflection = new ReflectionClass(static::class);

        // get the filename where the class was defined
        $definitionPath = $reflection->getFileName();

        $jsonPath = realpath(\dirname($definitionPath).\DIRECTORY_SEPARATOR.'data.json');

        if (!file_exists($jsonPath)) {
            throw new FileNotFoundException('File not found');
        }

        return json_decode(file_get_contents($jsonPath));
    }

    public function installItem(stdClass $item): DataManagerDataSetItem
    {
        switch ($item->type) {
            case 'database':
                return $this->installItemDatabase($item);
            break;
            case 'media':
                return $this->installItemMedia($item);
            break;
        }
    }

    public function removeItem(DataManagerDataSetItem $item)
    {
        switch ($item->getTable()) {
            case 'tl_files':
                return $this->removeItemMedia($item);
            break;
            default:
                return $this->removeItemDatabase($item);
            break;
        }
    }

    /**
     * @return mixed
     */
    public function getConfig(): DataManagerDataSet
    {
        return $this->config;
    }

    protected function installItemDatabase(stdClass $item): DataManagerDataSetItem
    {
        $config = new DataManagerDataSetItem();

        $model = Model::getClassFromTable($item->table);
        $objItem = new $model();

        foreach ($item->fields as $fieldObject) {
            $field = $fieldObject->field;
            $value = $fieldObject->value;
            if ($this->isValueAReferenceToAnotherObject($value)) {
                $reference = $this->getCleanedValueReference($value);
                $referenceObjectName = $this->getValueReferenceObjectName($reference);
                $referenceObjectField = $this->getValueReferenceObjectField($reference);
                $objReference = $this->references[$referenceObjectName];

                $value = $objReference->$referenceObjectField;
            }

            $objItem->$field = $value;
        }
        $objItem->save();

        $this->references[$item->reference] = $objItem;
        $config->setTable($item->table);
        $config->setId((int) $this->references[$item->reference]->id);
        $config->setReference($item->reference);

        return $config;
    }

    protected function installItemMedia(stdClass $item): DataManagerDataSetItem
    {
        $config = new DataManagerDataSetItem();
        $this->references[$item->reference] = $this->duplicateMediaToFiles($item->source, $item->target);
        $config->setTable('tl_files');
        $config->setId((int) $this->references[$item->reference]->id);
        $config->setReference($item->reference);

        return $config;
    }

    protected function removeItemDatabase(DataManagerDataSetItem $item): bool
    {
        $model = Model::getClassFromTable($item->getTable());
        $objItem = $model::findById($item->getId());

        if (!$objItem) {
            return true;
        }

        return 0 !== $objItem->delete();
    }

    protected function removeItemMedia(DataManagerDataSetItem $item): bool
    {
        // remove media only if not used elsewhere
        $objFileModel = FilesModel::findById($item->getId());
        if (!$objFileModel) {
            return true;
        }
        /** @var DataManager */
        $dataManagerConfig = $this->configurationManager->load();
        foreach ($dataManagerConfig->getDatasets() as $dataset) {
            foreach ($dataset->getItems() as $datasetItem) {
                if ('tl_files' === $datasetItem->getTable()
                && $item->getId() === $datasetItem->getId()
                && (
                    $dataset->getType() !== $this->config->getType()
                    || $dataset->getModule() !== $this->config->getModule()
                    || $dataset->getName() !== $this->config->getName()
                )
                ) {
                    throw new Exception('Media used by another dataset');
                }
            }
        }

        $objFile = new File($objFileModel->path);

        return $objFile->delete();
    }

    protected function isValueAReferenceToAnotherObject($value): bool
    {
        return \is_string($value) && '[[' === substr($value, 0, 2) && ']]' === substr($value, -2, 2);
    }

    protected function getCleanedValueReference(string $value): string
    {
        return substr($value, 2, \strlen($value) - 4);
    }

    protected function getValueReferenceObjectName(string $value): string
    {
        return substr($value, 0, strpos($value, '.'));
    }

    protected function getValueReferenceObjectField(string $value): string
    {
        return substr($value, strpos($value, '.') + 1);
    }
}
