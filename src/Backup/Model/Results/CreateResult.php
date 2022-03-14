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

class CreateResult extends AbstractResult
{
    /** @var array */
    protected $filesBackuped = [];

    /**
     * @return mixed
     */
    public function getFilesBackuped(): array
    {
        return $this->filesBackuped;
    }

    /**
     * @param mixed $filesBackuped
     */
    public function setFilesBackuped(array $filesBackuped): self
    {
        $this->filesBackuped = $filesBackuped;

        return $this;
    }

    public function addFileBackuped(string $path): self
    {
        $this->filesBackuped[] = $path;
        $this->files[] = $path;

        return $this;
    }
}
