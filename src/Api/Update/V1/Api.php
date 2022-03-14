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

namespace WEM\SmartgearBundle\Api\Update\V1;

use WEM\SmartgearBundle\Api\Update\V1\Model\ListResponse;
use WEM\SmartgearBundle\Api\Update\V1\Model\Mapper\ListResultToListResponse;
use WEM\SmartgearBundle\Api\Update\V1\Model\Mapper\UpdateResultToUpdateResponse;
use WEM\SmartgearBundle\Api\Update\V1\Model\UpdateResponse;
use WEM\SmartgearBundle\Update\UpdateManager;

class Api
{
    /** @var UpdateManager */
    protected $updateManager;
    /** @var ListResultToListResponse */
    protected $listResultToListResponseMapper;
    /** @var UpdateResultToUpdateResponse */
    protected $updateResultToUpdateResponseMapper;

    public function __construct(
        UpdateManager $updateManager,
        ListResultToListResponse $listResultToListResponseMapper,
        UpdateResultToUpdateResponse $updateResultToUpdateResponseMapper
    ) {
        $this->updateManager = $updateManager;
        $this->listResultToListResponseMapper = $listResultToListResponseMapper;
        $this->updateResultToUpdateResponseMapper = $updateResultToUpdateResponseMapper;
    }

    public function list()
    {
        try {
            $listResult = $this->updateManager->list();
            $response = $this->listResultToListResponseMapper->map($listResult, (new ListResponse()));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return $response;
    }

    public function update()
    {
        try {
            $updateResult = $this->updateManager->update();
            $response = $this->updateResultToUpdateResponseMapper->map($updateResult, (new UpdateResponse()));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return $response;
    }
}
