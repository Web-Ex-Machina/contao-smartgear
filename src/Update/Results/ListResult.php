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

use WEM\SmartgearBundle\Classes\Migration\Result as MigrationResult;

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

    public function getNumbersOfUpdatesToPlay(): int
    {
        $numbersOfUpdatesToPlay = 0;
        foreach ($this->getResults() as $singleMigrationResult) {
            $numbersOfUpdatesToPlay = MigrationResult::STATUS_SHOULD_RUN === $singleMigrationResult->getResult()->getStatus() ? $numbersOfUpdatesToPlay + 1 : $numbersOfUpdatesToPlay;
        }

        return $numbersOfUpdatesToPlay;
    }
}
