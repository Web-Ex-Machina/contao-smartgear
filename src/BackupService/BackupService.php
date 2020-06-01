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
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class BackupService
{
    /**
     * @var array
     */
    public $logs;

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

        $this->logs = [];
    }

    public function getLogs()
    {
        return $this->logs;
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
                    'tstamp' => $date->timestamp,
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

            if ('txt' === $ext) {
                $backups[end($name)]['fileslistPath'] = $path.'/'.$f;
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

            $objFileList = new \File($strFolderPath.$strArchiveName.'.txt');
            $objFileList->write(implode("\n", $files));
            $objFileList->close();

            // Save the database
            $objDatabaseBackup = new BackupDatabase();
            $objDatabaseBackup->backup('*', $strArchiveName, $strFolderPath);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function restore($backup): void
    {
        // Nota : Restoring a backup as a certain date means we have to play ALL the backups done after the one we want
        // This way, we're sure everything is restored the way it was
        $backups = [];

        // Retrieve backup folder
        $folder = pathinfo($backup, PATHINFO_DIRNAME);

        // Retrieve backup name
        $name = explode('_', str_replace('.'.pathinfo($backup, PATHINFO_EXTENSION), '', pathinfo($backup, PATHINFO_FILENAME)));
        $date = $date = new \Date($name[1], 'YmdHi');

        // Get backup list with the same settings
        $arrBackups = $this->list($folder, $name[0]);

        // Retrieve backups made after this one
        foreach ($arrBackups as $k => $b) {
            if ($date->timestamp >= $b['timestamp']) {
                $backups[] = $b;
            }
        }

        // Reverse backups
        $backups = array_reverse($backups);

        // Now, we can execute the backups
        foreach ($backups as $b) {
            // Restore files
            if ($b['hasFiles']) {
                // Remove all the files listed in the txt
                $objList = new \File($b['fileslistPath']);
                $this->dumpFiles(explode("\n", $objList->getContent()));

                // Then, import files
                $this->importFiles($b['zipPath']);
            }
        }

        // Restore sql
        // Nota : Here, we do not need to execute the dump of each backup, since we've saved everything each time.
        $backup = end($backups);
        if ($backup['hasSql']) {
            // No questions asked, we dump the database
            $this->removeDatabase();

            // And then, we import the backup database
            $this->importDatabase(TL_ROOT.'/'.$backup['sqlPath']);
        }

        // Warmup cache
        //$this->warmUpCache();
    }

    public function importFiles($archive): void
    {
        $objArchive = new \ZipReader($archive);
        $files = $objArchive->getFileList();
        $this->logs[] = 'Import files: '.\count($files).' to import';

        while ($objArchive->next()) {
            $strFilename = $objArchive->current()['file_name'];
            $this->logs[] = 'File to import: '.$strFilename;

            $strContent = $objArchive->unzip();

            $objFile = new \File($strFilename);
            $objFile->truncate();
            $objFile->write($strContent);

            if ($strContent !== $objFile->getContent()) {
                $this->logs[] = 'File was not imported correctly';
            } else {
                ++$i;
            }

            $objFile->close();
        }

        $this->logs[] = 'End of process: '.$i.'/'.\count($files).' files imported';
    }

    public function dumpFiles($files): void
    {
        $this->logs[] = 'Remove files: '.\count($files).' to remove';

        $i = 0;
        foreach ($files as $f) {
            $objFile = new \File($f);
            $this->logs[] = 'File to delete: '.$f;

            if (!$objFile->exists()) {
                $this->logs[] = 'File do not exists: skipped';
                continue;
            }

            if ($objFile->delete()) {
                $this->logs[] = 'File deleted';
                ++$i;
            }
        }

        $this->logs[] = 'End of process: '.$i.'/'.\count($files).' files deleted';
    }

    public function importDatabase($path): void
    {
        $this->logs[] = 'Import Database '.$path;
        $cmd = sprintf(
            'mysql -u %s -p%s %s < %s',
            \Config::get('dbUser'),
            \Config::get('dbPass'),
            \Config::get('dbDatabase'),
            $path
        );
        $this->logs[] = 'Command: '.$cmd;
        $process = method_exists(Process::class, 'fromShellCommandline') ? Process::fromShellCommandline(
            $cmd
        ) : new Process($cmd);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $this->logs[] = 'End of process : '.$process->getOutput();
    }

    public function removeDatabase(): void
    {
        $this->logs[] = 'Remove Database';

        // No questions asked, we dump the database
        $cmd = sprintf(
            'mysqldump -u %s -p%s --no-data --add-drop-table %s | grep ^DROP | mysql -u %s -p%s -v %s',
            \Config::get('dbUser'),
            \Config::get('dbPass'),
            \Config::get('dbDatabase'),
            \Config::get('dbUser'),
            \Config::get('dbPass'),
            \Config::get('dbDatabase')
        );

        $this->logs[] = 'Command: '.$cmd;
        $process = method_exists(Process::class, 'fromShellCommandline') ? Process::fromShellCommandline(
            $cmd
        ) : new Process($cmd);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $this->logs[] = 'End of process: '.$process->getOutput();
    }

    public function warmUpCache(): void
    {
        $this->logs[] = 'Cache Warmup';

        // Finally, clean the Contao cache
        $cmd = sprintf(
            'php ../vendor/bin/contao-console cache:warmup --env=prod'
        );
        $this->logs[] = 'Command: '.$cmd;
        $process = method_exists(Process::class, 'fromShellCommandline') ? Process::fromShellCommandline(
            $cmd
        ) : new Process($cmd);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $this->logs[] = 'End of process: '.$process->getOutput();
    }
}
