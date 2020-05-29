<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2020 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

namespace WEM\SmartgearBundle\BackupService;

use Ausi\SlugGenerator\SlugGeneratorInterface;
use Contao\CoreBundle\Framework\ContaoFramework;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RequestStack;

class BackupService
{
    /**
     * @var ContaoFramework
     */
    private $framework;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var SlugGeneratorInterface
     */
    private $slugGenerator;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @internal Do not inherit from this class; decorate the "smartgear.backupmanager" service instead
     */
    public function __construct(ContaoFramework $framework, Filesystem $filesystem, SlugGeneratorInterface $slugGenerator, RequestStack $requestStack)
    {
        $this->framework = $framework;
        $this->filesystem = $filesystem;
        $this->slugGenerator = $slugGenerator;
        $this->requestStack = $requestStack;
    }

    public function list($path, $filename = 'sgbackup')
    {
        $backups = [];

        foreach (scandir(TL_ROOT.'/'.$path) as $f) {
            if ('.' === $f || '..' === $f) {
                continue;
            }

            $ext = pathinfo($path.'/'.$f, PATHINFO_EXTENSION);
            $name = explode('_', str_replace('.'.$ext, '', $f));

            if ($filename !== $name[0]) {
                continue;
            }

            // get the date as the last portion
            $date = new \Date(end($name), 'YmdHi');

            // Regroup backups by date
            if (!\array_key_exists(end($name), $backups)) {
                $backups[end($name)] = [
                    'date' => $date->datim,
                    'name' => $filename,
                    'hasFiles' => false,
                    'hasSql' => false,
                ];
            }

            if ('zip' === $ext) {
                $backups[end($name)]['hasFiles'] = true;
                $backups[end($name)]['zipPath'] = $path.'/'.$f;
            }

            if ('sql' === $ext) {
                $backups[end($name)]['hasSql'] = true;
                $backups[end($name)]['sqlPath'] = $path.'/'.$f;
            }
        }

        sort($backups);

        return $backups;
    }

    public function save($files, $options = []): void
    {
        try {
            // Generate the archive name
            if ($options['name']) {
                $strArchiveName = $options['name'].'_'.date('YmdHi');
            } else {
                $strArchiveName = 'sgbackup_'.date('YmdHi');
            }

            // Generate the full path
            if ($options['folder']) {
                $strFolderPath = $options['folder'];

                // Add last slash if not found
                if ('' !== substr($strFolderPath, 0, -1)) {
                    $strFolderPath .= '/';
                }
            } else {
                $strFolderPath = 'files/backups/';
            }

            // Prepare the ZipArchive of the backup
            $objArchive = new \ZipWriter($strFolderPath.$strArchiveName.'.zip');

            foreach ($files as $f) {
                if (!$this->filesystem->exists($f)) {
                    continue;
                }
                $objArchive->addFile($f);
            }

            $objArchive->close();

            // Save the database
            $objDatabaseBackup = new BackupDatabase();
            $objDatabaseBackup->backup('*', $strArchiveName, $strFolderPath);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function restore(): void
    {
    }
}
