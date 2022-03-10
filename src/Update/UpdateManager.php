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

namespace WEM\SmartgearBundle\Update;

use WEM\SmartgearBundle\Backup\BackupManager;
use WEM\SmartgearBundle\Backup\Results\CreateResult as BackupResult;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigManager;
use WEM\SmartgearBundle\Classes\Migration\MigrationInterface;
use WEM\SmartgearBundle\Classes\Migration\Result as MigrationResult;
use WEM\SmartgearBundle\Update\Results\SingleMigrationResult;
use WEM\SmartgearBundle\Update\Results\UpdateResult;

class UpdateManager
{
    /** @var CoreConfigManager */
    protected $configurationManager;
    /** @var BackupManager */
    protected $backupManager;
    /** @var array */
    protected $migrations;

    public function __construct(
        CoreConfigManager $configurationManager,
        BackupManager $backupManager,
        array $migrations
    ) {
        $this->configurationManager = $configurationManager;
        $this->backupManager = $backupManager;
        $this->migrations = $migrations;
    }

    public function update(): UpdateResult
    {
        $updateResult = new UpdateResult();
        $updateResult->setBackupResult($this->doBackup());

        foreach ($this->migrations as $migration) {
            $singleMigrationResult = $this->updateSingle($migration);
            $updateResult->addResult($singleMigrationResult);
            if (MigrationResult::STATUS_FAIL === $singleMigrationResult->getResult()->getStatus()) {
                $updateResult->setStatusFail();
                $updateResult = $this->setRemainingMigrationsAsUntouched($updateResult);
                break;
            }
        }

        return $updateResult;
    }

    protected function setRemainingMigrationsAsUntouched(UpdateResult $updateResult): UpdateResult
    {
        $remainingMigrations = \array_slice($this->migrations, \count($updateResult->getResults()));
        foreach ($remainingMigrations as $remainingMigration) {
            $updateResult->addResult(
                (new SingleMigrationResult())
                ->setMigration($remainingMigration)
                ->setResult((new MigrationResult()))
            );
        }

        return $updateResult;
    }

    protected function doBackup(): BackupResult
    {
        return $this->backupManager->new();
    }

    protected function updateSingle(MigrationInterface $migration): SingleMigrationResult
    {
        $singleMigrationResult = new SingleMigrationResult();
        $singleMigrationResult->setMigration($migration);
        $singleMigrationResult->setResult($migration->shouldRun());
        if (MigrationResult::STATUS_SHOULD_RUN !== $singleMigrationResult->getResult()) {
            return $singleMigrationResult;
        }

        $singleMigrationResult->setResult($migration->do());

        return $singleMigrationResult;
    }
}
