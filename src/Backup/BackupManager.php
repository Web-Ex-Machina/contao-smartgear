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

use Contao\CoreBundle\Doctrine\Backup\Backup;
use Contao\CoreBundle\Doctrine\Backup\BackupManager as DatabaseBackupManager;
use Contao\CoreBundle\Doctrine\Backup\Config\RestoreConfig;
use Contao\File;
use Contao\Folder;
use Contao\ZipReader;
use Contao\ZipWriter;
use WEM\SmartgearBundle\Backup\Results\CreateResult;
use WEM\SmartgearBundle\Backup\Results\ListResult;
use WEM\SmartgearBundle\Backup\Results\RestoreResult;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Exceptions\Backup\ManagerException as BackupManagerException;
use WEM\SmartgearBundle\Model\Backup as BackupModel;

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
        $this->model = $model;
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
            $databaseBackupConfig = $databaseBackupConfig->withTablesToIgnore($this->tablesToIgnore);
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

            $model = new BackupModel();
            $model->tstamp = time();
            $model->createdAt = time();
            $model->name = $result->getBackup()->basename;
            $model->files = implode(',', $result->getFiles());
            $model->save();
        } catch (\Exception $e) {
            throw new BackupManagerException('Une erreur est survenue lors de la création du backup : '.$e->getMessage(), $e->getCode(), $e);
        }

        return $result;
    }

    public function list(int $limit, int $offset, ?int $before = null, ?int $after = null): ListResult
    {
        try {
            $arrConfig = [];
            if (null !== $before) {
                $arrConfig['before'] = $before;
            }
            if (null !== $after) {
                $arrConfig['after'] = $after;
            }
            $models = BackupModel::findItems($arrConfig, $limit, $offset);
            $count = BackupModel::countItems($arrConfig);
            $result = new ListResult();
            $result
                ->setTotal($count)
                ->setLimit($limit)
                ->setOffset($offset)
            ;
            foreach ($models as $model) {
                $result->addBackup(new File($this->getBackupPath($model->name)));
            }
        } catch (\Exception $e) {
            throw new BackupManagerException('Une erreur est survenue lors de la récupération de la liste des backups : '.$e->getMessage(), $e->getCode(), $e);
        }

        return $result;
    }

    public function get(string $backupName): File
    {
        if (!file_exists($this->getBackupFullPath($backupName))) {
            throw new BackupManagerException('Le backup n\'existe pas');
        }

        return new File($this->getBackupPath($backupName));
    }

    public function restore(string $backupName): RestoreResult
    {
        try {
            $result = new RestoreResult();
            $result->setBackup(new File($this->getBackupPath($backupName)));
            $backup = new ZipReader($this->getBackupPath($backupName));

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
            $config = new RestoreConfig(new Backup(basename($databaseBackupPath)));
            $config = $config->withTablesToIgnore($this->tablesToIgnore);
            $this->databaseBackupManager->restore($config);
            $result->setDatabaseRestored(true);
        } catch (\Exception $e) {
            throw new BackupManagerException('Une erreur est survenue lors de la restauration du backup : '.$e->getMessage(), $e->getCode(), $e);
        }

        return $result;
    }

    public function delete(string $backupName): bool
    {
        if (!file_exists($this->getBackupFullPath($backupName))) {
            throw new BackupManagerException(sprintf('Le backup a supprimer n\'existe pas (%s)', $this->getBackupFullPath($backupName)));
        }

        $model = BackupModel::findBy('name', $backupName);
        $model->delete();

        return unlink($this->getBackupFullPath($backupName));
    }

    protected function cleanArtifactsBeforeRestore(): array
    {
        $filesDeleted = [];
        foreach ($this->artifactsToBackup as $artifactPath) {
            $fullPath = $this->rootDir.\DIRECTORY_SEPARATOR.$artifactPath;
            if (file_exists($fullPath)) {
                if (is_file($fullPath)) {
                    unlink($fullPath);
                    $filesDeleted[] = $artifactPath;
                } else {
                    $files = Util::getFileList($fullPath);
                    foreach ($files as $filePath) {
                        unlink($filePath);
                        $filesDeleted[] = str_replace($this->rootDir.\DIRECTORY_SEPARATOR, '', $filePath);
                    }
                    $folder = new Folder($artifactPath);
                    $folder->purge();
                    // $filesDeleted[] = $folder->name;
                }
            }
        }

        return $filesDeleted;
    }

    protected function getNewBackupPath(): string
    {
        return $this->backupDirectory.\DIRECTORY_SEPARATOR.date('YmdHis').'.zip';
    }

    protected function getBackupPath(string $backupName): string
    {
        return $this->backupDirectory.\DIRECTORY_SEPARATOR.$backupName;
    }

    protected function getBackupFullPath(string $backupName): string
    {
        return $this->rootDir.\DIRECTORY_SEPARATOR.$this->getBackupPath($backupName);
    }
}
