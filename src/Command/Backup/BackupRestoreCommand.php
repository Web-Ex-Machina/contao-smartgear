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

class BackupRestoreCommand extends AbstractBackupCommand
{
    protected static $defaultName = 'smartgear:backup:restore';
    protected static $defaultDescription = 'Restore a backup';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            if (empty($input->getOption('backup'))) {
                throw new \Exception('Argument "backup" not defined');
            }
            $logs = $this->backupManager->restore($input->getOption('backup'));
        } catch (\Exception $e) {
            if ($this->isJson($input)) {
                $io->writeln(json_encode(['error' => $e->getMessage()]));
            } else {
                $io->error($e->getMessage());
            }

            return 1;
        }

        if ($this->isJson($input)) {
            $io->writeln($this->formatForJson($logs));

            return 0;
        }

        $io->table(['Results'], $this->formatForTable($logs));

        return 0;
    }

    protected function configure(): void
    {
        parent::configure();
        $this
            ->addOption('backup', null, InputOption::VALUE_REQUIRED, 'The backup name', '')
        ;
    }

    private function formatForTable(array $logs): array
    {
        $formatted = [];
        foreach ($logs as $log) {
            $formatted[] = [$log];
        }

        return $formatted;
    }

    private function formatForJson(array $logs): string
    {
        $json = [];

        foreach ($logs as $log) {
            $json[] = ['log' => $log];
        }

        return json_encode($json);
    }
}
