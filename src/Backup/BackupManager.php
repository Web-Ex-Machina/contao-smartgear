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
use Contao\Folder;
use WEM\SmartgearBundle\Backup\Results\CreateResult;
use WEM\SmartgearBundle\Backup\Results\RestoreResult;

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
     * @return CreateResult The backup result
     */
    public function new(): CreateResult
    {
        try {
            $result = new CreateResult();
            $path = $this->getNewBackupPath();

            $backupArchive = new ZipWriter($path);

            $databaseBackupConfig = $this->databaseBackupManager->createCreateConfig();
            $databaseBackupConfig->withTablesToIgnore($this->tablesToIgnore);
            $this->databaseBackupManager->create($databaseBackupConfig);
            $databaseBackup = $databaseBackupConfig->getBackup();

            $backupArchive->addFile($this->databaseBackupDirectory.\DIRECTORY_SEPARATOR.$databaseBackup->getFilename());
            $result->addFileBackuped($this->databaseBackupDirectory.\DIRECTORY_SEPARATOR.$databaseBackup->getFilename());

            foreach ($this->artifactsToBackup as $artifactPath) {
                if (is_file($this->rootDir.\DIRECTORY_SEPARATOR.$artifactPath)) {
                    $backupArchive->addFile($artifactPath);
                    $result->addFileBackuped($artifactPath);
                } else {
                    $files = Util::getFileList($this->rootDir.\DIRECTORY_SEPARATOR.$artifactPath);
                    foreach ($files as $filePath) {
                        $backupArchive->addFile(str_replace($this->rootDir.\DIRECTORY_SEPARATOR, '', $filePath));
                        $result->addFileBackuped(str_replace($this->rootDir.\DIRECTORY_SEPARATOR, '', $filePath));
                    }
                }
            }

            $backupArchive->close();

            $result->setBackup(new File($path));
        } catch (\Exception $e) {
            throw new BackupManagerException('Une erreur est survenue lors de la création du backup : '.$e->getMessage(), $e->getCode(), $e);
        }

        return $result;
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

    public function restore(string $backupName): RestoreResult
    {
        try{
            $result = new RestoreResult();
            $result->setBackup(new File($this->getBackupFullPath($backupName)));
            $backup = new ZipReader($this->getBackupFullPath($backupName));

            // 1) we delete the files / empty the dirs from $this->artifactsToBackup
            // 2) we restore files
            // 3) we restore the DB (Contao's DB backup manager handles the deletion before restoration)

            // 1) Here we'll delete what need to be deleted
            $result->setFilesDeleted($this->cleanArtifactsBeforeRestore());

            // 2) Restore the files
            $i = 0;
            $databaseBackupPath = $backup->first()->current()['file_name'];
            $backup->reset();
            while ($backup->next()) {
                $strFilename = $backup->current()['file_name'];

                $strContent = $backup->unzip();

                $objFile = new File($strFilename);
                $objFile->truncate();
                $objFile->write($strContent);

                if ($strContent !== $objFile->getContent()) {
                    $result->addFileInError($strFilename);
                } else {
                    $result->addFileRestored($strFilename);
                    ++$i;
                }

                $objFile->close();

            }

            // 3) now files are in place, time to play our DB backup
            $config = new RestoreConfig(new Backup(\basename($databaseBackupPath)));
            $this->databaseBackupManager->restore($config);
            $result->setDatabaseRestored(true);

        } catch (\Exception $e) {
            throw new BackupManagerException('Une erreur est survenue lors de la restauration du backup : '.$e->getMessage(), $e->getCode(), $e);
        }

        return $result;
    }

    protected function cleanArtifactsBeforeRestore(): array
    {
        $filesDeleted = [];
        foreach ($this->artifactsToBackup as $artifactPath) {
            if (is_file($this->rootDir.\DIRECTORY_SEPARATOR.$artifactPath)) {
                unlink($this->rootDir.\DIRECTORY_SEPARATOR.$artifactPath);
                $filesDeleted[] = $artifactPath;
            } else {
                $folder = new Folder($artifactPath);
                $folder->purge();
                $filesDeleted[] = $folder->name;
            }
        }

        return $filesDeleted;
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
