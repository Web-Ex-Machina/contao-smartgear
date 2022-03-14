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

namespace WEM\SmartgearBundle\Api\Backup\V1\Model\Mapper;

use WEM\SmartgearBundle\Api\Backup\V1\Model\ListResponse;
use WEM\SmartgearBundle\Backup\Model\Results\ListResult;

class ListResultToListResponse
{
    public function map(ListResult $listResult, ListResponse $listResponse): ListResponse
    {
        $listResponse->setTotal($listResult->getTotal());
        foreach ($listResult->getBackups() as $backup) {
            $listResponse->addBackup([
                'timestamp' => $backup->getFile()->ctime,
                'path' => $backup->getFile()->basename,
                'source' => $backup->getSource(),
            ]);
        }

        return $listResponse;
    }
}
