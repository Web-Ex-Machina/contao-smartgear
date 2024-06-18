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
use WEM\SmartgearBundle\Backup\Model\Results\CreateResult;
use WEM\SmartgearBundle\Exceptions\Backup\ManagerException as BackupManagerException;

class BackupCreateCommand extends AbstractBackupCommand
{
    protected static $defaultName = 'smartgear:backup:create';

    protected static $defaultDescription = 'Creates a new backup';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Backup creation');

        try {
            $result = $this->backupManager->newFromCommand();
        } catch (BackupManagerException $backupManagerException) {
            if ($this->isJson($input)) {
                $io->writeln(json_encode(['error' => $backupManagerException->getMessage()]));
            } else {
                $io->error($backupManagerException->getMessage());
            }

            return 1;
        }

        if ($this->isJson($input)) {
            $io->writeln(json_encode([
                'backup' => $result->getBackup()->getFile()->basename,
                'files' => [
                    'not_backuped' => $result->getFilesInError(),
                    'backuped' => $result->getFilesBackuped(),
                ],
            ]));

            return 0;
        }

        $io->table(['File', 'Status'], $this->formatForTable($result));
        if ($result->getFilesInError() === []) {
            $io->success(sprintf('Successfully created backup "%s".', $result->getBackup()->getFile()->basename));
        } else {
            $io->error(sprintf('Something went wrong during backup "%s" creation.', $result->getBackup()->getFile()->basename));
        }

        return 0;
    }

    private function formatForTable(CreateResult $result): array
    {
        $formatted = [];
        foreach ($result->getFilesInError() as $filepath) {
            $formatted[] = [$filepath, 'not backuped'];
        }

        foreach ($result->getFilesBackuped() as $filepath) {
            $formatted[] = [$filepath, 'backuped'];
        }

        return $formatted;
    }
}
