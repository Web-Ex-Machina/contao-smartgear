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

use stdClass;
use WEM\SmartgearBundle\Classes\Config\ConfigJsonInterface;

class DataManagerDataSetItem implements ConfigJsonInterface
{
    /** @var string */
    protected $table = '';
    /** @var int */
    protected $id = 0;
    /** @var string */
    protected $reference = '';

    public function reset(): self
    {
        return $this;
    }

    public function import(stdClass $json): self
    {
        $this
            ->setTable($json->table ?? '')
            ->setId($json->id ?? 0)
            ->setReference($json->reference ?? '')
        ;

        return $this;
    }

    public function export(): stdClass
    {
        $json = new \stdClass();

        $json->table = $this->getTable();
        $json->id = $this->getId();
        $json->reference = $this->getReference();

        return $json;
    }

    /**
     * @return mixed
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @param mixed $table
     */
    public function setTable(string $table): self
    {
        $this->table = $table;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @param mixed $reference
     */
    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }
}
