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

use Contao\File;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Api\Backup\V1\Model\CreateResponse;
use WEM\SmartgearBundle\Api\Backup\V1\Model\ListResponse;
use WEM\SmartgearBundle\Api\Backup\V1\Model\Mapper\CreateResultToCreateResponse;
use WEM\SmartgearBundle\Api\Backup\V1\Model\Mapper\ListResultToListResponse;
use WEM\SmartgearBundle\Backup\BackupManager;

class Api
{
    public function __construct(
        protected TranslatorInterface $translator,
        protected BackupManager $backupManager,
        protected ListResultToListResponse $listResultToListResponseMapper,
        protected CreateResultToCreateResponse $createResultToCreateResponseMapper)
    {
    }

    /**
     * @throws \Exception
     */
    public function list(int $limit, int $offset, ?int $before = null, ?int $after = null): ListResponse
    {
        try {
            $listResult = $this->backupManager->list($limit, $offset, $before, $after);
            $response = $this->listResultToListResponseMapper->map($listResult, (new ListResponse()));
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode(), $exception);
        }

        return $response;
    }

    /**
     * @throws \Exception
     */
    public function create(): CreateResponse
    {
        try {
            $createResult = $this->backupManager->newFromAPI();
            $response = $this->createResultToCreateResponseMapper->map($createResult, (new CreateResponse()));
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode(), $exception);
        }

        return $response;
    }

    /**
     * @throws \Exception
     */
    public function delete(string $backupName): false|string
    {
        try {
            if (!$this->backupManager->delete($backupName)) {
                throw new \Exception($this->translator->trans('WEMSG.BACKUPMANAGER.API.messageDeleteError', [$backupName], 'contao_default'));
            }
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode(), $exception);
        }

        return json_encode(['message' => $this->translator->trans('WEMSG.BACKUPMANAGER.API.messageDeleteSuccess', [$backupName], 'contao_default')]);
    }

    /**
     * @throws \Exception
     */
    public function restore(string $backupName): false|string
    {
        try {
            $restoreResult = $this->backupManager->restore($backupName);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode(), $exception);
        }

        return json_encode(['message' => $this->translator->trans('WEMSG.BACKUPMANAGER.API.messageRestoreSuccess', [$backupName], 'contao_default')]);
    }

    /**
     * @throws \Exception
     */
    public function get(string $backupName): File
    {
        try {
            $getResult = $this->backupManager->get($backupName);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode(), $exception);
        }

        return $getResult;
    }
}
