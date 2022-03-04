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

abstract class AbstractResult
{
    /** @var File */
    protected $backup;
    /** @var array */
    protected $files = [];
    /** @var array */
    protected $filesInError = [];

    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @return mixed
     */
    public function getBackup(): File
    {
        return $this->backup;
    }

    /**
     * @param mixed $backup
     */
    public function setBackup(File $backup): self
    {
        $this->backup = $backup;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFilesInError(): array
    {
        return $this->filesInError;
    }

    /**
     * @param mixed $filesInError
     */
    public function setFilesInError(array $filesInError): self
    {
        $this->filesInError = $filesInError;

        return $this;
    }

    public function addFileInError(string $path): self
    {
        $this->filesInError[] = $path;
        $this->files[] = $path;

        return $this;
    }

    protected function setFiles(array $files): self
    {
        $this->files = $files;

        return $this;
    }
}
