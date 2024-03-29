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

namespace WEM\SmartgearBundle\Api\Backup\V1\Model;

class CreateResponse
{
    /** @var array */
    protected $backup = [];

    public function toJson(): string
    {
        $json = new \stdClass();
        $json->backup = $this->backup;

        return json_encode($json);
    }

    /**
     * @return mixed
     */
    public function getBackup(): array
    {
        return $this->backup;
    }

    public function setBackup(array $backup): self
    {
        $this->backup = $backup;

        return $this;
    }
}
