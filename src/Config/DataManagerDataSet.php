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

class DataManagerDataSet implements ConfigJsonInterface
{
    /** @var string */
    protected $name = '';
    /** @var string */
    protected $type = '';
    /** @var string */
    protected $module = '';
    /** @var int */
    protected $dateInstallation = 0;
    /** @var array */
    protected $items = [];

    public function reset(): self
    {
        return $this;
    }

    public function import(\stdClass $json): self
    {
        $this
            ->setName($json->name ?? '')
            ->setType($json->type ?? '')
            ->setModule($json->module ?? '')
            ->setDateInstallation($json->date_installation ?? 0)
        ;
        if ($json->items) {
            foreach ($json->items as $item) {
                $this->addItem((new DataManagerDataSetItem())->import($item));
            }
        }

        return $this;
    }

    public function export(): string
    {
        $json = new \stdClass();

        $json->name = $this->getName();
        $json->type = $this->getType();
        $json->module = $this->getModule();
        $json->date_installation = $this->getDateInstallation();
        $json->items = $this->getItems();

        return json_encode($json, \JSON_PRETTY_PRINT);
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function setItems(array $items): self
    {
        $this->items = $items;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getModule(): string
    {
        return $this->module;
    }

    public function setModule(string $module): self
    {
        $this->module = $module;

        return $this;
    }

    public function getDateInstallation(): int
    {
        return $this->dateInstallation;
    }

    public function setDateInstallation(int $dateInstallation): self
    {
        $this->dateInstallation = $dateInstallation;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function addItem(DataManagerDataSetItem $item): self
    {
        $this->items[] = $item;

        return $this;
    }

    public function removeItem(DataManagerDataSetItem $item): self
    {
        foreach ($this->items as $key => $existingItem) {
            if ($this->buildItemAlias($existingItem) === $this->buildItemAlias($item)) {
                unset($this->items[$key]);

                return $this;
            }
        }

        throw new Exception('Unable to find the item to remove');
    }

    protected function buildItemAlias(DataManagerDataSetItem $item): string
    {
        return sprintf('%s-%s', $item->getTable(), $item->getId());
    }
}
