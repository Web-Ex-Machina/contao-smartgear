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

use WEM\SmartgearBundle\Classes\Config\ConfigJsonInterface;

class DataManagerDataSetItem implements ConfigJsonInterface
{
    /** @var string */
    protected $table = '';
    /** @var int */
    protected $id = 0;

    public function reset(): self
    {
        return $this;
    }

    public function import(\stdClass $json): self
    {
        $this
            ->setTable($json->table ?? '')
            ->setId($json->id ?? 0)
        ;

        return $this;
    }

    public function export(): string
    {
        $json = new \stdClass();

        $json->table = $this->getTable();
        $json->id = $this->getId();

        return json_encode($json, \JSON_PRETTY_PRINT);
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
}
