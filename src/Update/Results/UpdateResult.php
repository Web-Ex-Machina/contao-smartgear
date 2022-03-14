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

namespace WEM\SmartgearBundle\Update\Results;

use WEM\SmartgearBundle\Backup\Results\CreateResult as BackupResult;

class UpdateResult
{
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAIL = 'fail';
    public const STATUS_ONGOING = 'ongoing';
    /** @var string */
    protected $status = self::STATUS_ONGOING;
    /** @var array */
    protected $results = [];
    /** @var BackupResult */
    protected $backupResult;

    public function addResult(SingleMigrationResult $result): self
    {
        $this->results[] = $result;

        return $this;
    }

    public function getResults(): array
    {
        return $this->results;
    }

    public function isSuccess(): bool
    {
        return self::STATUS_SUCCESS === $this->status;
    }

    public function isFail(): bool
    {
        return self::STATUS_FAIL === $this->status;
    }

    public function setStatusSuccess(): self
    {
        $this->status = self::STATUS_SUCCESS;

        return $this;
    }

    public function setStatusFail(): self
    {
        $this->status = self::STATUS_FAIL;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getBackupResult(): BackupResult
    {
        return $this->backupResult;
    }

    public function setBackupResult(BackupResult $backupResult): self
    {
        $this->backupResult = $backupResult;

        return $this;
    }
}
