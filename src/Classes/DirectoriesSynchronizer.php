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

namespace WEM\SmartgearBundle\Classes;

use Contao\File;
use Exception;

class DirectoriesSynchronizer
{
    /** @var string */
    protected $directoryToSynchronizeFrom;
    /** @var string */
    protected $directoryToSynchronizeTo;

    protected $filesToAdd = [];
    protected $filesToDelete = [];
    protected $filesToUpdate = [];

    public function __construct(string $directoryToSynchronizeFrom, string $directoryToSynchronizeTo)
    {
        $this->directoryToSynchronizeFrom = $directoryToSynchronizeFrom;
        $this->directoryToSynchronizeTo = $directoryToSynchronizeTo;
    }

    public function synchronize(?bool $withDeletions = true): void
    {
        $this->checkFiles(
            $this->getDirectoryToSynchronizeFromFiles(),
            $this->getDirectoryToSynchronizeToFiles()
        );
        if (!empty($this->filesToAdd)) {
            foreach ($this->filesToAdd as $relativePath => $realPath) {
                $objFile = new File($realPath);
                $objFile->copyTo($this->directoryToSynchronizeTo.\DIRECTORY_SEPARATOR.$relativePath);
            }
        }

        if (!empty($this->filesToUpdate)) {
            foreach ($this->filesToUpdate as $relativePath => $realPath) {
                $objFileFrom = new File($realPath);
                $objFileTo = new File($this->directoryToSynchronizeTo.\DIRECTORY_SEPARATOR.$relativePath);

                $objFileTo->truncate();
                $objFileTo->write($objFileFrom->getContent());

                $objFileTo->close();
            }
        }

        if ($withDeletions && !empty($this->filesToDelete)) {
            foreach ($this->filesToDelete as $relativePath => $realPath) {
                $objFile = new File($realPath);
                $objFile->delete();
            }
        }
    }

    /**
     * @return mixed
     */
    public function getFilesToAdd(): array
    {
        return $this->filesToAdd;
    }

    /**
     * @return mixed
     */
    public function getFilesToDelete(): array
    {
        return $this->filesToDelete;
    }

    /**
     * @return mixed
     */
    public function getFilesToUpdate(): array
    {
        return $this->filesToUpdate;
    }

    protected function getDirectoryToSynchronizeFromFiles()
    {
        return $this->getFiles($this->directoryToSynchronizeFrom, true);
    }

    protected function getDirectoryToSynchronizeToFiles()
    {
        return $this->getFiles($this->directoryToSynchronizeTo, true);
    }

    protected function checkFiles(array $filesFrom, array $filesTo): void
    {
        foreach ($filesFrom as $relativePath => $realPath) {
            if (\array_key_exists($relativePath, $filesTo)) {
                // check difference
                $objFileFrom = new File($realPath);
                $objFileTo = new File($filesTo[$relativePath]);
                if ($this->checkIfFilesAreDifferent($objFileFrom, $objFileTo)) {
                    $this->filesToUpdate[$relativePath] = $objFileFrom->path;
                }
            } else {
                $this->filesToAdd[$relativePath] = $realPath;
            }
        }
        foreach ($filesTo as $relativePath => $realPath) {
            if (!\array_key_exists($relativePath, $filesFrom)) {
                $this->filesToDelete[$relativePath] = $realPath;
            }
        }
    }

    protected function getFiles(string $rootFolderPath, ?bool $blnGetSubFolders = true, ?string $currentFolderPath = ''): array
    {
        try {
            $strBasePath = $rootFolderPath.$currentFolderPath;
            $arrFiles = scandir($strBasePath);
            $arrPaths = [];

            foreach ($arrFiles as $f) {
                if ('.' === $f || '..' === $f) {
                    continue;
                }

                $isFolder = is_dir($strBasePath.\DIRECTORY_SEPARATOR.$f);

                if ($blnGetSubFolders && $isFolder) {
                    $arrPaths = array_merge($arrPaths, $this->getFiles($rootFolderPath, $blnGetSubFolders, $currentFolderPath.\DIRECTORY_SEPARATOR.$f, ));
                } elseif (!$isFolder) {
                    $arrPaths[$currentFolderPath.\DIRECTORY_SEPARATOR.$f] = $strBasePath.\DIRECTORY_SEPARATOR.$f;
                }
            }

            return $arrPaths;
        } catch (Exception $e) {
            throw $e;
        }
    }

    protected function checkIfFilesAreDifferent(File $objFileA, File $objFileB): bool
    {
        try {
            if (!$objFileA->exists()) {
                return true;
            }
            if (!$objFileB->exists()) {
                return true;
            }
            if ($objFileA->hash !== $objFileB->hash) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            throw $e;
        }
    }

    protected function stripRootPathFromPath(string $rootFolder, string $path): string
    {
        return str_replace($rootFolder, '', $path);
    }
}
