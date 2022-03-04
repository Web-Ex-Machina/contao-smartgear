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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use WEM\SmartgearBundle\Backup\Results\RestoreResult;

class BackupRestoreCommand extends AbstractBackupCommand
{
    protected static $defaultName = 'smartgear:backup:restore';
    protected static $defaultDescription = 'Restore a backup';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Backup restoration');

        try {
            if (empty($input->getOption('backup'))) {
                throw new \Exception('Argument "backup" not defined');
            }
            /** @var RestoreResult */
            $result = $this->backupManager->restore($input->getOption('backup'));
        } catch (\Exception $e) {
            if ($this->isJson($input)) {
                $io->writeln(json_encode(['error' => $e->getMessage()]));
            } else {
                $io->error($e->getMessage());
            }

            return 1;
        }

        if ($this->isJson($input)) {
            $io->writeln($this->formatForJson($result));

            return 0;
        }

        $io->table(['File', 'Status'], $this->formatForTable($result));
        if (empty($result->getFilesInError())) {
            $io->success(sprintf('Successfully restored backup "%s".', $result->getBackup()->basename));
        } else {
            $io->error(sprintf('Something went wrong during backup "%s" restoration.', $result->getBackup()->basename));
        }

        return 0;
    }

    protected function configure(): void
    {
        parent::configure();
        $this
            ->addOption('backup', null, InputOption::VALUE_REQUIRED, 'The backup name', '')
        ;
    }

    private function formatForTable(RestoreResult $result): array
    {
        $formatted = [];
        foreach ($result->getFilesDeleted() as $filepath) {
            $formatted[] = [$filepath, 'deleted'];
        }
        foreach ($result->getFilesInError() as $filepath) {
            $formatted[] = [$filepath, 'not restored'];
        }
        foreach ($result->getFilesRestored() as $filepath) {
            $formatted[] = [$filepath, 'restored'];
        }

        return $formatted;
    }

    private function formatForJson(RestoreResult $result): string
    {
        $json = [
            'backup' => $result->getBackup()->basename,
            'database_restored' => $result->getDatabaseRestored(),
            'files' => [
                'deleted' => $result->getFilesDeleted(),
                'not_restored' => $result->getFilesInError(),
                'restored' => $result->getFilesRestored(),
            ],
        ];

        return json_encode($json);
    }
}
