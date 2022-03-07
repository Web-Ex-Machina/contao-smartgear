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

namespace WEM\SmartgearBundle\Api\Backup\V1;

use WEM\SmartgearBundle\Backup\BackupManager;

class Api
{
    /** @var BackupManager */
    protected $backupManager;

    public function __construct(BackupManager $backupManager)
    {
        $this->backupManager = $backupManager;
    }

    public function list(int $limit, int $offset, ?int $before = null, ?int $after = null)
    {
        try {
            $listResult = $this->backupManager->list($limit, $offset, $before, $after);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return $listResult;
    }
}
