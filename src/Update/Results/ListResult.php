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

namespace WEM\SmartgearBundle\Update\Results;

class ListResult
{
    /** @var array */
    protected $results = [];

    public function addResult(SingleMigrationResult $result): self
    {
        $this->results[] = $result;

        return $this;
    }

    public function getResults(): array
    {
        return $this->results;
    }
}
