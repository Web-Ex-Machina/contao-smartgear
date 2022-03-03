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
use Contao\ZipReader;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Exceptions\Backup\ManagerException as BackupManagerException;
use Contao\CoreBundle\Doctrine\Backup\Config\RestoreConfig;
use Contao\CoreBundle\Doctrine\Backup\Backup;

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

    public function list(): array
    {
        try {
            $objFiles = [];
            $files = Util::getFileList($this->rootDir.\DIRECTORY_SEPARATOR.$this->backupDirectory);
            foreach ($files as $filePath) {
                $objFiles[] = new File(str_replace($this->rootDir.\DIRECTORY_SEPARATOR, '', $filePath));
            }

             usort($objFiles, static fn (File $a, File $b) => $b->ctime <=> $a->ctime);

        } catch (\Exception $e) {
            throw new BackupManagerException('Une erreur est survenue lors de la récupération de la liste des backups : '.$e->getMessage(), $e->getCode(), $e);
        }

        return $objFiles;
    }

    public function restore(string $backupName): array
    {
        try{
            $backup = new ZipReader($this->getBackupFullPath($backupName));

            // 1) we delete the files / empty the dirs from $this->artifactsToBackup
            // 2) we restore files
            // 3) we delete the DB
            // 4) we restore the DB

            // Here we'll delete what need to be deleted

            $i = 0;
            $backupPath = '';
            $files = $backup->getFileList();
            $logs[] = 'Import files: '.\count($files).' to import';
            $backupPath = $backup->first()->current()['file_name'];
            $backup->reset();
            while ($backup->next()) {
                $strFilename = $backup->current()['file_name'];
                $logs[] = 'File to import: '.$strFilename;

                $strContent = $backup->unzip();

                $objFile = new File($strFilename);
                $objFile->truncate();
                $objFile->write($strContent);

                if ($strContent !== $objFile->getContent()) {
                    $logs[] = 'File was not imported correctly';
                } else {
                    ++$i;
                }

                $objFile->close();

            }
            $logs[] = 'End of process: '.$i.'/'.\count($files).' files imported';

            // now files are in place, time to play our DB backup
            $config = new RestoreConfig(new Backup(\basename($backupPath)));
            $this->databaseBackupManager->restore($config);

            $logs[] = 'Database restored';

        } catch (\Exception $e) {
            throw new BackupManagerException('Une erreur est survenue lors de la restauration du backup : '.$e->getMessage(), $e->getCode(), $e);
        }

        return $logs;
    }

    protected function getNewBackupPath(): string
    {
        return $this->backupDirectory.\DIRECTORY_SEPARATOR.date('YmdHis').'.zip';
    }

    protected function getBackupFullPath(string $backupName): string
    {
        return $this->backupDirectory.\DIRECTORY_SEPARATOR.$backupName;
    }
}
