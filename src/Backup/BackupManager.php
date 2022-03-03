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

namespace WEM\SmartgearBundle\Backup;

use Contao\CoreBundle\Doctrine\Backup\BackupManager as DatabaseBackupManager;
use Contao\File;
use Contao\ZipWriter;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Exceptions\Backup\ManagerException as BackupManagerException;

class BackupManager
{
    /** @var string */
    protected $rootDir;
    /** @var string */
    protected $backupDirectory;
    /** @var string */
    protected $databaseBackupDirectory;
    /** @var DatabaseBackupManager */
    protected $databaseBackupManager;
    /** @var array */
    protected $artifactsToBackup = [];
    /** @var array */
    protected $tablesToIgnore = [];

    public function __construct(
        string $rootDir,
        string $backupDirectory,
        string $databaseBackupDirectory,
        DatabaseBackupManager $databaseBackupManager,
        array $artifactsToBackup,
        array $tablesToIgnore
    ) {
        $this->rootDir = $rootDir;
        $this->backupDirectory = $backupDirectory;
        $this->databaseBackupDirectory = $databaseBackupDirectory;
        $this->databaseBackupManager = $databaseBackupManager;
        $this->artifactsToBackup = $artifactsToBackup;
        $this->tablesToIgnore = $tablesToIgnore;
    }

    /**
     * Create a new backup.
     *
     * @return File The zipped backup
     */
    public function new(): File
    {
        try {
            $path = $this->getNewBackupPath();

            $backupArchive = new ZipWriter($path);

            $databaseBackupConfig = $this->databaseBackupManager->createCreateConfig();
            $databaseBackupConfig->withTablesToIgnore($this->tablesToIgnore);
            $this->databaseBackupManager->create($databaseBackupConfig);
            $databaseBackup = $databaseBackupConfig->getBackup();

            $backupArchive->addFile($this->databaseBackupDirectory.\DIRECTORY_SEPARATOR.$databaseBackup->getFilename());

            foreach ($this->artifactsToBackup as $artifactPath) {
                if (is_file($this->rootDir.\DIRECTORY_SEPARATOR.$artifactPath)) {
                    $backupArchive->addFile($artifactPath);
                } else {
                    $files = Util::getFileList($this->rootDir.\DIRECTORY_SEPARATOR.$artifactPath);
                    foreach ($files as $filePath) {
                        $backupArchive->addFile(str_replace($this->rootDir.\DIRECTORY_SEPARATOR, '', $filePath));
                    }
                }
            }

            $backupArchive->close();
        } catch (\Exception $e) {
            throw new BackupManagerException('Une erreur est survenue lors de la création du backup : '.$e->getMessage(), $e->getCode(), $e);
        }

        return new File($path);
    }

    protected function getNewBackupPath(): string
    {
        return $this->backupDirectory.\DIRECTORY_SEPARATOR.date('YmdHis').'.zip';
    }
}
