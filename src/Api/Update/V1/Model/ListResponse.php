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

namespace WEM\SmartgearBundle\Api\Update\V1\Model;

class ListResponse
{
    /** @var int */
    protected $total = 0;
    /** @var array */
    protected $updates = [];

    public function toJson(): string
    {
        $json = new \stdClass();
        $json->total = $this->total;
        $json->updates = $this->updates;

        return json_encode($json);
    }

    /**
     * @return mixed
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    public function setTotal(int $total): self
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdates(): array
    {
        return $this->updates;
    }

    public function setUpdates(array $updates): self
    {
        $this->updates = $updates;

        return $this;
    }

    public function addUpdate(array $update): self
    {
        $this->updates[] = $update;

        return $this;
    }
}
