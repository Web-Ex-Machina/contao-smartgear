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

use WEM\SmartgearBundle\Api\Update\V1\Model\ListResponse;
use WEM\SmartgearBundle\Update\Results\ListResult;

class ListResultToListResponse
{
    public function map(ListResult $listResult, ListResponse $listResponse): ListResponse
    {
        $listResponse->setTotal(\count($listResult->getResults()));
        foreach ($listResult->getResults() as $item) {
            $listResponse->addUpdate([
                'update' => \get_class($item->getMigration()),
                'status' => $item->getResult()->getStatus(),
                'logs' => $item->getResult()->getLogs(),
            ]);
        }

        return $listResponse;
    }
}
