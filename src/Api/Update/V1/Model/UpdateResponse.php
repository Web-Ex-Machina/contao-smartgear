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

namespace WEM\SmartgearBundle\Api\Update\V1\Model;

class UpdateResponse
{
    /** @var array */
    protected $updates = [];
    /** @var array */
    protected $backup = [];
    /** @var string */
    protected $status;

    public function toJson(): string
    {
        $json = new \stdClass();
        $json->status = $this->status;
        $json->backup = $this->backup;
        $json->updates = $this->updates;

        return json_encode($json);
    }

    /**
     * @return mixed
     */
    public function getUpdates(): array
    {
        return $this->updates;
    }

    public function setUpdates(array $updates): self
    {
        $this->updates = $updates;

        return $this;
    }

    public function addUpdate(array $update): self
    {
        $this->updates[] = $update;

        return $this;
    }

    public function getBackup(): array
    {
        return $this->backup;
    }

    public function setBackup(array $backup): self
    {
        $this->backup = $backup;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
