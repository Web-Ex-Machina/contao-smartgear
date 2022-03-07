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

namespace WEM\SmartgearBundle\Backup\Results;

use Contao\File;

class ListResult
{
    /** @var array */
    protected $backups = [];
    /** @var int */
    protected $total = 0;
    /** @var int */
    protected $offset = 0;
    /** @var int */
    protected $limit = 0;
    /** @var int */
    protected $before = 0;
    /** @var int */
    protected $after = 0;

    public function addBackup(File $backup): self
    {
        $this->backups[] = $backup;

        return $this;
    }

    public function getBackups(): array
    {
        return $this->backups;
    }

    public function getAfter(): int
    {
        return $this->after;
    }

    public function setAfter(int $after): self
    {
        $this->after = $after;

        return $this;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function setTotal(int $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function setOffset(int $offset): self
    {
        $this->offset = $offset;

        return $this;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function getBefore(): int
    {
        return $this->before;
    }

    public function setBefore(int $before): self
    {
        $this->before = $before;

        return $this;
    }

    protected function setBackups(array $backups): self
    {
        $this->backups = $backups;

        return $this;
    }
}
