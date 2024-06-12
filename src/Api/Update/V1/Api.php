<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2023 Web ex Machina
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
    public function __construct(
        protected UpdateManager $updateManager,
        protected ListResultToListResponse $listResultToListResponseMapper,
        protected UpdateResultToUpdateResponse $updateResultToUpdateResponseMapper)
    {
    }

    /**
     * @throws \Exception
     */
    public function list(): ListResponse
    {
        try {
            $listResult = $this->updateManager->list();
            $response = $this->listResultToListResponseMapper->map($listResult, (new ListResponse()));
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode(), $exception);
        }

        return $response;
    }

    /**
     * @throws \Exception
     */
    public function update(?bool $noBackup = false): UpdateResponse
    {
        try {
            $updateResult = $this->updateManager->update(!$noBackup);
            $response = $this->updateResultToUpdateResponseMapper->map($updateResult, (new UpdateResponse()));
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode(), $exception);
        }

        return $response;
    }
}
