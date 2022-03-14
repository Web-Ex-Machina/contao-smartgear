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

namespace WEM\SmartgearBundle\Command\Update;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use WEM\SmartgearBundle\Update\Results\ListResult;

class UpdateListCommand extends AbstractUpdateCommand
{
    protected static $defaultName = 'smartgear:update:list';
    protected static $defaultDescription = 'List updates';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Update list');
        try {
            /** @var ListResult */
            $listResult = $this->updateManager->list();
        } catch (\Exception $e) {
            if ($this->isJson($input)) {
                $io->writeln(json_encode(['error' => $e->getMessage()]));
            } else {
                $io->error($e->getMessage());
            }

            return 1;
        }

        if ($this->isJson($input)) {
            $io->writeln($this->formatForJson($listResult->getResults()));

            return 0;
        }

        $io->table(['Version', 'Name', 'Description', 'Status'], $this->formatForTable($listResult->getResults()));

        return 0;
    }

    private function formatForTable(array $singleMigrationResults): array
    {
        $formatted = [];
        foreach ($singleMigrationResults as $singleMigrationResult) {
            $formatted[] = [
                $singleMigrationResult->getMigration()->getVersion(),
                $singleMigrationResult->getMigration()->getName(),
                $singleMigrationResult->getMigration()->getDescription(),
                $singleMigrationResult->getResult()->getStatus(),
            ];
        }

        return $formatted;
    }

    private function formatForJson(array $singleMigrationResults): string
    {
        $json = [];

        foreach ($singleMigrationResults as $singleMigrationResult) {
            $json[] = [
                'classname' => \get_class($singleMigrationResult->getMigration()),
                'version' => $singleMigrationResult->getMigration()->getVersion(),
                'name' => $singleMigrationResult->getMigration()->getName(),
                'description' => $singleMigrationResult->getMigration()->getDescription(),
                'status' => $singleMigrationResult->getResult()->getStatus(),
            ];
        }

        return json_encode($json);
    }
}
