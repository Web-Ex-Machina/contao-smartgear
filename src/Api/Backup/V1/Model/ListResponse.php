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

namespace WEM\SmartgearBundle\Api\Backup\V1\Model;

class ListResponse
{
    /** @var int */
    protected $total = 0;
    /** @var array */
    protected $backups = [];

    public function toJson(): string
    {
        $json = new \stdClass();
        $json->total = $this->total;
        $json->backups = $this->backups;

        return json_encode($json);
    }

    /**
     * @return mixed
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @param mixed $total
     */
    public function setTotal(int $total): self
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBackups(): array
    {
        return $this->backups;
    }

    /**
     * @param mixed $backups
     */
    public function setBackups(array $backups): self
    {
        $this->backups = $backups;

        return $this;
    }

    public function addBackup(array $backup): self
    {
        $this->backups[] = $backup;

        return $this;
    }
}
