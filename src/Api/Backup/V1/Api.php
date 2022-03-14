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

use WEM\SmartgearBundle\Api\Backup\V1\Model\CreateResponse;
use WEM\SmartgearBundle\Api\Backup\V1\Model\ListResponse;
use WEM\SmartgearBundle\Api\Backup\V1\Model\Mapper\CreateResultToCreateResponse;
use WEM\SmartgearBundle\Api\Backup\V1\Model\Mapper\ListResultToListResponse;
use WEM\SmartgearBundle\Backup\BackupManager;

class Api
{
    /** @var BackupManager */
    protected $backupManager;
    /** @var ListResultToListResponse */
    protected $listResultToListResponseMapper;
    /** @var CreateResultToCreateResponse */
    protected $createResultToCreateResponseMapper;

    public function __construct(
        BackupManager $backupManager,
        ListResultToListResponse $listResultToListResponseMapper,
        CreateResultToCreateResponse $createResultToCreateResponseMapper
    ) {
        $this->backupManager = $backupManager;
        $this->listResultToListResponseMapper = $listResultToListResponseMapper;
        $this->createResultToCreateResponseMapper = $createResultToCreateResponseMapper;
    }

    public function list(int $limit, int $offset, ?int $before = null, ?int $after = null)
    {
        try {
            $listResult = $this->backupManager->list($limit, $offset, $before, $after);
            $response = $this->listResultToListResponseMapper->map($listResult, (new ListResponse()));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return $response;
    }

    public function create()
    {
        try {
            $createResult = $this->backupManager->newFromAPI();
            $response = $this->createResultToCreateResponseMapper->map($createResult, (new CreateResponse()));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return $response;
    }

    public function delete(string $backupName)
    {
        try {
            if (!$this->backupManager->delete($backupName)) {
                throw new \Exception(sprintf('Une erreur est survenue lors de la suppression du backup "%s"', $backupName));
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return json_encode(['message' => sprintf('Le backup "%s" a bien été supprimé', $backupName)]);
    }

    public function restore(string $backupName)
    {
        try {
            $restoreResult = $this->backupManager->restore($backupName);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return json_encode(['message' => sprintf('Le backup "%s" a bien été restauré', $backupName)]);
    }

    public function get(string $backupName)
    {
        try {
            $getResult = $this->backupManager->get($backupName);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return $getResult;
    }
}
