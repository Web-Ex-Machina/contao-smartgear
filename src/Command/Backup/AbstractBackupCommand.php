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

use Symfony\Component\Console\Command\Command;
use WEM\SmartgearBundle\Backup\BackupManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Contao\CoreBundle\Framework\ContaoFramework;

class AbstractBackupCommand extends Command
{
    protected BackupManager $backupManager;
    protected ContaoFramework $framework;

    public function __construct(BackupManager $backupManager, ContaoFramework $framework)
    {
        $this->backupManager = $backupManager;
        $this->framework = $framework;

        parent::__construct();

        $this->framework->initialize();
    }

    protected function configure(): void
    {
        $this
            ->addOption('format', null, InputOption::VALUE_REQUIRED, 'The output format (txt, json)', 'txt')
        ;
    }

    protected function isJson(InputInterface $input): bool
    {
        $format = $input->getOption('format');

        if (!\in_array($format, ['json', 'txt'], true)) {
            throw new \InvalidArgumentException('This command only supports the "txt" and "json" formats.');
        }

        return 'json' === $format;
    }
}