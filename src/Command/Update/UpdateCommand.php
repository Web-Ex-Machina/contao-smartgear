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

namespace WEM\SmartgearBundle\Command\Update;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use WEM\SmartgearBundle\Update\Results\UpdateResult;

class UpdateCommand extends AbstractUpdateCommand
{
    protected static $defaultName = 'smartgear:update:update';
    protected static $defaultDescription = 'Play updates';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Play updates');
        // $io->error($input->getOption('backup'));

        // return 1;
        try {
            /** @var UpdateResult */
            $updateResult = $this->updateManager->update();
        } catch (\Exception $e) {
            if ($this->isJson($input)) {
                $io->writeln(json_encode(['error' => $e->getMessage()]));
            } else {
                $io->error($e->getMessage());
            }

            return 1;
        }

        if ($this->isJson($input)) {
            $io->writeln($this->formatForJson($updateResult));

            return 0;
        }

        if ($updateResult->isSuccess()) {
            $io->success('Updates played');
        } elseif ($updateResult->isFail()) {
            $io->error('Updates not played');
        }

        $io->success(sprintf('Backup : %s', $updateResult->getBackupResult()->getBackup()->getFile()->basename));

        $io->table(['Version', 'Name', 'Description', 'Status', 'Logs'], $this->formatForTable($updateResult->getResults()));

        return 0;
    }

    private function formatForTable(array $singleMigrationResults): array
    {
        $formatted = [];
        foreach ($singleMigrationResults as $singleMigrationResult) {
            $formatted[] = [
                $singleMigrationResult->getVersion()->__toString(),
                $singleMigrationResult->getName(),
                $singleMigrationResult->getDescription(),
                $singleMigrationResult->getResult()->getStatus(),
                implode("\n", $singleMigrationResult->getResult()->getLogs()),
            ];
        }

        return $formatted;
    }

    private function formatForJson(UpdateResult $updateResult): string
    {
        $json = [
            'status' => $updateResult->getStatus(),
            'backup' => [
                'timestamp' => $updateResult->getBackupResult()->getBackup()->getFile()->ctime,
                'path' => $updateResult->getBackupResult()->getBackup()->getFile()->basename,
            ],
            'updates' => [],
        ];

        foreach ($updateResult->getResults() as $singleMigrationResult) {
            $json['updates'][] = [
                // 'classname' => \get_class($singleMigrationResult),
                'version' => $singleMigrationResult->getVersion()->__toString(),
                'name' => $singleMigrationResult->getName(),
                'description' => $singleMigrationResult->getDescription(),
                'status' => $singleMigrationResult->getResult()->getStatus(),
                'logs' => $singleMigrationResult->getResult()->getLogs(),
            ];
        }

        return json_encode($json);
    }
}
