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

namespace WEM\SmartgearBundle\Classes;

use Contao\File;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;

class DirectoriesSynchronizer
{

    protected TranslatorInterface $translator;

    protected array $filesToAdd = [];

    protected array $filesToDelete = [];

    protected array $filesToUpdate = [];

    /**
     * @param string $sourceDirectory      Path to source directory (without root path)
     * @param string $destinationDirectory Path to destination directory (without root path)
     * @param string $rootDir              Root path
     * @param bool   $manageSubfolders     true to manage subfolders
     */
    public function __construct(
        TranslatorInterface $translator,
        protected string $sourceDirectory,
        protected string $destinationDirectory,
        protected string $rootDir,
        protected bool $manageSubfolders)
    {
    }

    /**
     * Synchronize folders.
     *
     * @param ?bool $withDeletions true to delete files in destination not present in source
     * @throws Exception
     */
    public function synchronize(?bool $withDeletions = true): void
    {
        $this->sourceDirectory = $this->stripRootPathFromPath(str_replace('[public_or_web]', Util::getPublicOrWebDirectory(), $this->sourceDirectory));
        $this->destinationDirectory = $this->stripRootPathFromPath(str_replace('[public_or_web]', Util::getPublicOrWebDirectory(), $this->destinationDirectory));

        $this->checkFiles(
            $this->getSourceDirectoryFiles(),
            $this->getDestinationDirectoryFiles()
        );

        foreach ($this->filesToAdd as $relativePath => $realPath) {
            $objFile = new File($realPath);
            if (!$objFile->copyTo($this->destinationDirectory.$relativePath)) {
                throw new Exception($this->translator->trans('WEMSG.DIRECTORIESSYNCHRONIZER.error', [$realPath, $this->destinationDirectory.$relativePath], 'contao_default'));
            }
        }

        foreach ($this->filesToUpdate as $relativePath => $realPath) {
            $objFileFrom = new File($realPath);
            $objFileTo = new File($this->destinationDirectory.$relativePath);

            $objFileTo->truncate();
            $objFileTo->write($objFileFrom->getContent());

            $objFileTo->close();
        }

        if ($withDeletions && $this->filesToDelete !== []) {
            foreach ($this->filesToDelete as $realPath) {
                $objFile = new File($realPath);
                $objFile->delete();
            }
        }
    }

    public function getFilesToAdd(): array
    {
        return $this->filesToAdd;
    }

    public function getFilesToDelete(): array
    {
        return $this->filesToDelete;
    }

    public function getFilesToUpdate(): array
    {
        return $this->filesToUpdate;
    }

    /**
     * @todo remove and find another way of doing it
     */
    public function getDestinationDirectory(): string
    {
        return $this->destinationDirectory;
    }

    /**
     * @todo remove and find another way of doing it
     */
    public function setDestinationDirectory(string $destinationDirectory): static
    {
        $this->destinationDirectory = $destinationDirectory;

        return $this;
    }

    protected function getSourceDirectoryFiles(): array
    {
        return $this->getFiles($this->rootDir.\DIRECTORY_SEPARATOR.$this->sourceDirectory, $this->manageSubfolders);
    }

    protected function getDestinationDirectoryFiles(): array
    {
        return $this->getFiles($this->rootDir.\DIRECTORY_SEPARATOR.$this->destinationDirectory, $this->manageSubfolders);
    }

    /**
     * Compare folders to kjnow which files to create, update or delete.
     *
     * @param array $sourceFiles      path from the "getFiles" method
     * @param array $destinationFiles path from the "getFiles" method
     * @throws Exception
     */
    protected function checkFiles(array $sourceFiles, array $destinationFiles): void
    {
        foreach ($sourceFiles as $relativePath => $realPath) {
            if (\array_key_exists($relativePath, $destinationFiles)) {
                // check difference
                $objFileFrom = new File($realPath);
                $objFileTo = new File($destinationFiles[$relativePath]);
                if ($this->checkIfFilesAreDifferent($objFileFrom, $objFileTo)) {
                    $this->filesToUpdate[$relativePath] = $objFileFrom->path;
                }
            } else {
                $this->filesToAdd[$relativePath] = $realPath;
            }
        }

        foreach ($destinationFiles as $relativePath => $realPath) {
            if (!\array_key_exists($relativePath, $sourceFiles)) {
                $this->filesToDelete[$relativePath] = $realPath;
            }
        }
    }

    /**
     * Get files from folder.
     *
     * @param string    $startPath        The start path
     * @param bool|bool $blnGetSubFolders true to check files inside subfolders
     *
     * @return array array of path, in the form ['relative path from start path' => 'fullpath' (without rootdir prefix)]
     */
    protected function getFiles(string $startPath, ?bool $blnGetSubFolders = true, ?string $relativePathFromStartPath = ''): array
    {
        $strBasePath = $startPath.$relativePathFromStartPath;
        $arrFiles = is_dir($strBasePath) ? scandir($strBasePath) : [];
        $arrPaths = [];
        foreach ($arrFiles as $f) {
            if ('.' === $f || '..' === $f) {
                continue;
            }

            $isFolder = is_dir($strBasePath.\DIRECTORY_SEPARATOR.$f);

            if ($blnGetSubFolders && $isFolder) {
                $arrPaths = array_merge($arrPaths, $this->getFiles($startPath, $blnGetSubFolders, $relativePathFromStartPath.\DIRECTORY_SEPARATOR.$f));
            } elseif (!$isFolder) {
                $arrPaths[$relativePathFromStartPath.\DIRECTORY_SEPARATOR.$f] = $this->stripRootPathFromPath($strBasePath.\DIRECTORY_SEPARATOR.$f);
            }
        }

        return $arrPaths;
    }

    /**
     * Check if 2 files are differents.
     *
     * @param File $objFileA [description]
     * @param File $objFileB [description]
     */
    protected function checkIfFilesAreDifferent(File $objFileA, File $objFileB): bool
    {
        if (!$objFileA->exists()) {
            return true;
        }

        if (!$objFileB->exists()) {
            return true;
        }
        return $objFileA->hash !== $objFileB->hash;
    }

    /**
     * Remove rootdir from path.
     *
     * @param string $path [description]
     *
     * @return string The path without the rootdir prefix
     */
    protected function stripRootPathFromPath(string $path): string
    {
        if ($this->rootDir === '' || $this->rootDir === '0') {
            return $path;
        }

        return str_replace($this->rootDir.\DIRECTORY_SEPARATOR, '', $path);
    }
}
