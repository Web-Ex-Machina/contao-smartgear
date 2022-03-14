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

use WEM\SmartgearBundle\Api\Backup\V1\Model\CreateResponse;
use WEM\SmartgearBundle\Backup\Model\Results\CreateResult;

class CreateResultToCreateResponse
{
    public function map(CreateResult $createResult, CreateResponse $createResponse): CreateResponse
    {
        $createResponse->setBackup([
            'timestamp' => $createResult->getBackup()->getFile()->ctime,
            'path' => $createResult->getBackup()->getFile()->basename,
            'source' => $createResult->getBackup()->getSource(),
        ]);

        return $createResponse;
    }
}
