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

namespace WEM\SmartgearBundle\Backup\Model\Results;

class RestoreResult extends AbstractResult
{
    /** @var array */
    protected $filesRestored = [];
    /** @var array */
    protected $filesDeleted = [];
    /** @var bool */
    protected $databaseRestored = false;

    public function getFilesReplacedByRestore(): array
    {
        return array_intersect($this->filesDeleted, $this->filesRestored);
    }

    public function getFilesDeletedByRestore(): array
    {
        return array_diff($this->filesDeleted, $this->filesRestored);
    }

    public function getFilesAddedByRestore(): array
    {
        return array_diff($this->filesRestored, $this->filesDeleted);
    }

    public function getFilesRestored(): array
    {
        return $this->filesRestored;
    }

    public function setFilesRestored(array $filesRestored): self
    {
        $this->filesRestored = $filesRestored;

        return $this;
    }

    public function addFileRestored(string $path): self
    {
        $this->filesRestored[] = $path;
        $this->files[] = $path;

        return $this;
    }

    public function getFilesDeleted(): array
    {
        return $this->filesDeleted;
    }

    public function setFilesDeleted(array $filesDeleted): self
    {
        $this->filesDeleted = $filesDeleted;

        return $this;
    }

    public function addFileDeleted(string $path): self
    {
        $this->filesDeleted[] = $path;

        return $this;
    }

    public function getDatabaseRestored(): bool
    {
        return $this->databaseRestored;
    }

    public function setDatabaseRestored(bool $databaseRestored): self
    {
        $this->databaseRestored = $databaseRestored;

        return $this;
    }
}
