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
use WEM\SmartgearBundle\Exceptions\Backup\ManagerException as BackupManagerException;

class BackupCreateCommand extends AbstractBackupCommand
{
    protected static $defaultName = 'smartgear:backup:create';
    protected static $defaultDescription = 'Creates a new backup';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $backup = $this->backupManager->new();
        } catch (BackupManagerException $e) {
            if ($this->isJson($input)) {
                $io->writeln(json_encode(['error' => $e->getMessage()]));
            } else {
                $io->error($e->getMessage());
            }

            return 1;
        }

        if ($this->isJson($input)) {
            $io->writeln(json_encode(['backup' => $backup->basename]));

            return 0;
        }

        $io->success(sprintf('Successfully created backup "%s".', $backup->basename));

        return 0;
    }
}
