<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2023 Web ex Machina
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
use Exception;
use SplFileInfo;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Backup\Model\Backup as BackupBusinessModel;
use WEM\SmartgearBundle\Backup\Model\Results\CreateResult;
use WEM\SmartgearBundle\Backup\Model\Results\ListResult;
use WEM\SmartgearBundle\Backup\Model\Results\RestoreResult;
use WEM\SmartgearBundle\Classes\Command\Util as CommandUtil;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Exceptions\Backup\ManagerException as BackupManagerException;
use WEM\SmartgearBundle\Model\Backup as BackupModel;

class BackupManager
{
    /** @var string */
    protected $rootDir;
    /** @var string */
    protected $backupDirectory;
    /** @var CommandUtil */
    protected $commandUtil;
    /** @var string */
    protected $databaseBackupDirectory;
    /** @var DatabaseBackupManager */
    protected $databaseBackupManager;
    /** @var TranslatorInterface */
    protected $translator;
    /** @var array */
    protected $artifactsToBackup = [];
    /** @var array */
    protected $tablesToIgnore = [];

    public function __construct(
        string $rootDir,
        string $backupDirectory,
        CommandUtil $commandUtil,
        string $databaseBackupDirectory,
        DatabaseBackupManager $databaseBackupManager,
        TranslatorInterface $translator,
        array $artifactsToBackup,
        array $tablesToIgnore
    ) {
        $this->rootDir = $rootDir;
        $this->backupDirectory = $backupDirectory;
        $this->commandUtil = $commandUtil;
        $this->databaseBackupDirectory = $databaseBackupDirectory;
        $this->databaseBackupManager = $databaseBackupManager;
        $this->translator = $translator;
        $this->artifactsToBackup = $artifactsToBackup;
        $this->tablesToIgnore = $tablesToIgnore;
    }

    public function newFromCommand(): CreateResult
    {
        return $this->new(BackupModel::SOURCE_COMMAND);
    }

    public function newFromConfigurationReset(): CreateResult
    {
        return $this->new(BackupModel::SOURCE_CONFIGURATION_RESET);
    }

    public function newFromUpdate(): CreateResult
    {
        return $this->new(BackupModel::SOURCE_UPDATE);
    }

    public function newFromUI(): CreateResult
    {
        return $this->new(BackupModel::SOURCE_UI);
    }

    public function newFromAPI(): CreateResult
    {
        return $this->new(BackupModel::SOURCE_API);
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
            if ($models) {
                foreach ($models as $model) {
                    $result->addBackup(
                        (new BackupBusinessModel())
                        ->setFile(new File($this->getBackupPath($model->name)))
                        ->setSource($model->source)
                    );
                }
            }
        } catch (\Exception $e) {
            throw new BackupManagerException($this->translator->trans('WEM.SMARTGEAR.BACKUPMANAGER.messageRetrieveListError', [$e->getMessage()], 'contao_default'), $e->getCode(), $e);
        }

        return $result;
    }

    public function get(string $backupName): File
    {
        if (!file_exists($this->getBackupFullPath($backupName))) {
            throw new BackupManagerException($this->translator->trans('WEM.SMARTGEAR.BACKUPMANAGER.messageRetrieveSingleError', [], 'contao_default'));
        }

        return new File($this->getBackupPath($backupName));
    }

    public function restore(string $backupName): RestoreResult
    {
        try {
            $result = new RestoreResult();

            $model = BackupModel::findOneBy('name', $backupName);

            $result->setBackup(
                (new BackupBusinessModel())
                ->setFile(new File($this->getBackupPath($backupName)))
                ->setSource($model->source)
            );

            $backup = new ZipReader($this->getBackupPath($backupName));

            // 1) Here we'll delete what need to be deleted
            $result->setFilesDeleted($this->cleanArtifactsBeforeRestore());

            // 2) Restore the files
            $i = 0;
            $databaseBackupPath = $backup->first()->current()['file_name'];
            $backup->reset();

            $arrBigFiles = [];

            while ($backup->next()) {
                $strFilename = $backup->current()['file_name'];

                if (preg_match('/(.*)\.parts\_index/', $strFilename, $matches)) {
                    $strFilenameOrig = str_replace('.parts_index', '', $matches[1]);
                    if (!\in_array($strFilenameOrig, $arrBigFiles, true)) {
                        $arrBigFiles[$strFilenameOrig] = [
                            'index' => 0,
                            'chunks_done' => 0,
                            'chunks' => [],
                        ];
                    }

                    $arrBigFiles[$strFilenameOrig]['index'] = (int) $backup->unzip();
                    $result->addFileRestored($strFilename);
                } elseif (preg_match('/(.*)\.part\_([0-9]{8})/', $strFilename, $matches)) {
                    // continue;
                    // biiiig file chuncked
                    $strFilenameOrig = $matches[1];
                    $chunckIndex = $matches[2];

                    if (!\in_array($strFilenameOrig, $arrBigFiles, true)) {
                        $arrBigFiles[$strFilenameOrig] = [
                            'index' => 0,
                            'chunks_done' => 0,
                            'chunks' => [],
                        ];
                    }

                    $arrBigFiles[$strFilenameOrig]['chunks'][] = $chunckIndex;
                    ++$arrBigFiles[$strFilenameOrig]['chunks_done'];

                    $strContent = $backup->unzip();

                    $objFile = new File($strFilenameOrig);
                    // $objFile->truncate();
                    $objFile->append($strContent);
                    $objFile->close();
                // how to know we have finished with this ? prrrrt.
                } else {
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
            }

            // 2b) check if all big files have been restored correctly
            foreach ($arrBigFiles as $strFilename => $stats) {
                if ((int) $stats['index'] === (int) $stats['chunks_done']) {
                    $result->addFileInError($strFilename);
                } else {
                    $result->addFileRestored($strFilename);
                    ++$i;
                }
            }

            // 3) now files are in place, time to play our DB backup
            $config = new RestoreConfig(new Backup(basename($databaseBackupPath)));
            $config = $config->withTablesToIgnore($this->tablesToIgnore);
            $this->databaseBackupManager->restore($config);
            $result->setDatabaseRestored(true);

            // 4) rebuild search index if we didn't backup up the corresponding tables
            if (\in_array('+tl_search_index', $this->tablesToIgnore, true) || \in_array('+tl_search', $this->tablesToIgnore, true)) {
                try {
                    $result->setSearchIndexRebuildLog($this->commandUtil->executeCmdPHP('contao:crawl'));
                } catch (\Exception $e) {
                    $result->setSearchIndexRebuildLog($e->getMessage());
                }
            }
        } catch (\Exception $e) {
            throw new BackupManagerException($this->translator->trans('WEM.SMARTGEAR.BACKUPMANAGER.messageRestoreError', [$e->getMessage()], 'contao_default'), $e->getCode(), $e);
        }

        return $result;
    }

    public function delete(string $backupName): bool
    {
        if (!file_exists($this->getBackupFullPath($backupName))) {
            throw new BackupManagerException($this->translator->trans('WEM.SMARTGEAR.BACKUPMANAGER.messageDeleteError', [$this->getBackupFullPath($backupName)], 'contao_default'));
        }

        $model = BackupModel::findBy('name', $backupName);
        $model->delete();

        return unlink($this->getBackupFullPath($backupName));
    }

    /**
     * Create a new backup.
     *
     * @return CreateResult The backup result
     */
    protected function new(string $source): CreateResult
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
                    $this->addArtifactToBackup($backupArchive, $result, $artifactPath);
                } else {
                    $files = Util::getFileList($this->rootDir.\DIRECTORY_SEPARATOR.$artifactPath);
                    foreach ($files as $filePath) {
                        $this->addArtifactToBackup($backupArchive, $result, str_replace($this->rootDir.\DIRECTORY_SEPARATOR, '', $filePath));
                    }
                }
            }

            $backupArchive->close();

            $result->setBackup(
                 (new BackupBusinessModel())
                ->setFile(new File($path))
                ->setSource($source)
            );

            $model = new BackupModel();
            $model->tstamp = time();
            $model->createdAt = time();
            $model->name = $result->getBackup()->getFile()->basename;
            $model->files = implode(',', $result->getFiles());
            $model->source = $source;
            $model->save();
        } catch (\Exception $e) {
            throw new BackupManagerException($this->translator->trans('WEM.SMARTGEAR.BACKUPMANAGER.messageCreateError', [$e->getMessage()], 'contao_default'), $e->getCode(), $e);
        }

        return $result;
    }

    protected function addArtifactToBackup(ZipWriter &$backupArchive, CreateResult &$result, string $artifactPath): void
    {
        $artifactFullPath = $this->rootDir.\DIRECTORY_SEPARATOR.$artifactPath;
        if (!is_file($artifactFullPath)) {
            throw new Exception($artifactPath.' is not a file');
        }
        $fileInfo = new SplFileInfo($artifactFullPath);
        $fileSize = $fileInfo->getSize();
        // if ($fileSize > 524288000) { // 500 Mo
        if ($fileSize > 2684354560) { // 2,5 Go
            unset($fileInfo);
            $i = 0;
            // new
            $chunkSize = 52428800; // 50Mo
            $readBytes = 0;
            while ($readBytes < $fileSize) {
                ++$i;
                $strContent = file_get_contents($artifactFullPath, false, null, $readBytes, $chunkSize);
                $chunkFileName = $artifactPath.'.part_'.sprintf('%08d', $i);
                $backupArchive->addString($strContent, $chunkFileName);
                $result->addFileBackuped($chunkFileName);
                unset($strContent);
                $readBytes += $chunkSize;
            }

            $backupArchive->addString($i, $artifactPath.'.parts_index');
            $result->addFileBackuped($artifactPath.'.parts_index');
        } else {
            $backupArchive->addFile($artifactPath);
            $result->addFileBackuped($artifactPath);
        }
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
