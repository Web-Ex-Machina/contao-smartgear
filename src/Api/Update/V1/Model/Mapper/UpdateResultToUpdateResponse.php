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

namespace WEM\SmartgearBundle\Api\Update\V1\Model\Mapper;

use WEM\SmartgearBundle\Api\Update\V1\Model\UpdateResponse;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Update\Results\UpdateResult;

class UpdateResultToUpdateResponse
{
    public function map(UpdateResult $updateResult, UpdateResponse $updateResponse): UpdateResponse
    {
        $updateResponse->setStatus($updateResult->getStatus());
        $updateResponse->setBackup([
            'timestamp' => $updateResult->getBackupResult()->getBackup()->getFile()->ctime,
            'path' => $updateResult->getBackupResult()->getBackup()->getFile()->basename,
            'source' => $updateResult->getBackupResult()->getBackup()->getSource(),
            'size' => [
                'raw' => $updateResult->getBackupResult()->getBackup()->size,
                'human_readable' => Util::humanReadableFilesize((int) $updateResult->getBackupResult()->getBackup()->size),
            ],
        ]);
        foreach ($updateResult->getResults() as $item) {
            $updateResponse->addUpdate([
                'update' => \get_class($item->getMigration()),
                'status' => $item->getResult()->getStatus(),
                'logs' => $item->getResult()->getLogs(),
            ]);
        }

        return $updateResponse;
    }
}
