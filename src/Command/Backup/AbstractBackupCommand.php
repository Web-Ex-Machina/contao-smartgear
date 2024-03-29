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

use WEM\SmartgearBundle\Command\AbstractCommand;
use WEM\SmartgearBundle\Backup\BackupManager;
use Contao\CoreBundle\Framework\ContaoFramework;

class AbstractBackupCommand extends AbstractCommand
{
    protected BackupManager $backupManager;

    public function __construct(BackupManager $backupManager, ContaoFramework $framework)
    {
        $this->backupManager = $backupManager;

        parent::__construct($framework);

        $this->framework->initialize();
    }
}