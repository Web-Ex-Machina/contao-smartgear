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

namespace WEM\SmartgearBundle\Command\Backup;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use WEM\SmartgearBundle\Backup\Model\Results\ListResult;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Exceptions\Backup\ManagerException as BackupManagerException;

class BackupListCommand extends AbstractBackupCommand
{
    protected static $defaultName = 'smartgear:backup:list';
    protected static $defaultDescription = 'List backups';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Backup list');
        try {
            /** @var ListResult */
            $listResult = $this->backupManager->list(0, 0);
        } catch (BackupManagerException $e) {
            if ($this->isJson($input)) {
                $io->writeln(json_encode(['error' => $e->getMessage()]));
            } else {
                $io->error($e->getMessage());
            }

            return 1;
        }

        if ($this->isJson($input)) {
            $io->writeln($this->formatForJson($listResult->getBackups()));

            return 0;
        }

        $io->table(['filename', 'size', 'date'], $this->formatForTable($listResult->getBackups()));

        return 0;
    }

    private function formatForTable(array $backups): array
    {
        $formatted = [];
        foreach ($backups as $backup) {
            $formatted[] = [
                $backup->basename,
                Util::humanReadableFilesize($backup->size),
                \DateTime::createFromFormat('U', (string) $backup->ctime)->format('d/m/Y H:i:s'),
            ];
        }

        return $formatted;
    }

    private function formatForJson(array $backups): string
    {
        $json = [];

        foreach ($backups as $backup) {
            $json[] = [
                'filename' => $backup->basename,
                'size' => Util::humanReadableFilesize($backup->size),
                'date' => \DateTime::createFromFormat('U', (string) $backup->ctime)->format('d/m/Y H:i:s'),
            ];
        }

        return json_encode($json);
    }
}
