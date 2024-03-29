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
use WEM\SmartgearBundle\Backup\Model\Results\RestoreResult;

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
            $io->success(sprintf('Successfully restored backup "%s".', $result->getBackup()->getFile()->basename));
        } else {
            $io->error(sprintf('Something went wrong during backup "%s" restoration.', $result->getBackup()->getFile()->basename));
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
        foreach ($result->getFilesDeletedByRestore() as $filepath) {
            $formatted[] = [$filepath, 'deleted'];
        }
        foreach ($result->getFilesInError() as $filepath) {
            $formatted[] = [$filepath, 'not restored'];
        }
        foreach ($result->getFilesReplacedByRestore() as $filepath) {
            $formatted[] = [$filepath, 'restored (replaced)'];
        }
        foreach ($result->getFilesAddedByRestore() as $filepath) {
            $formatted[] = [$filepath, 'restored (added)'];
        }

        return $formatted;
    }

    private function formatForJson(RestoreResult $result): string
    {
        $json = [
            'backup' => $result->getBackup()->getFile()->basename,
            'database_restored' => $result->getDatabaseRestored(),
            'files' => [
                'deleted' => [
                    'raw' => $result->getFilesDeleted(),
                    'detailed' => [
                        'deleted' => $result->getFilesDeletedByRestore(),
                    ],
                ],
                'not_restored' => $result->getFilesInError(),
                'restored' => [
                    'raw' => $result->getFilesRestored(),
                    'detailed' => [
                        'replaced' => $result->getFilesReplacedByRestore(),
                        'added' => $result->getFilesAddedByRestore(),
                    ],
                ],
            ],
        ];

        return json_encode($json);
    }
}
