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

class BackupDeleteCommand extends AbstractBackupCommand
{
    protected static $defaultName = 'smartgear:backup:delete';

    protected static $defaultDescription = 'Delete a backup';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Backup deletion');

        try {
            if (empty($input->getOption('backup'))) {
                throw new \Exception('Argument "backup" not defined');
            }

            $result = $this->backupManager->delete($input->getOption('backup'));
        } catch (\Exception $exception) {
            if ($this->isJson($input)) {
                $io->writeln(json_encode(['error' => $exception->getMessage()]));
            } else {
                $io->error($exception->getMessage());
            }

            return 1;
        }

        if ($this->isJson($input)) {
            $io->writeln(json_encode([
                'backup' => $input->getOption('backup'),
                'deleted' => $result,
            ]));

            return 0;
        }

        if ($result) {
            $io->success(sprintf('Successfully deleted backup "%s".', $input->getOption('backup')));
        } else {
            $io->error(sprintf('Something went wrong during backup "%s" deletion.', $input->getOption('backup')));
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
}
