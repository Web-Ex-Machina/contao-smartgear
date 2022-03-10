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

use WEM\SmartgearBundle\Classes\Migration\MigrationInterface;
use WEM\SmartgearBundle\Classes\Migration\Result as MigrationResult;

class SingleMigrationResult
{
    /** @var MigrationInterface */
    protected $migration;
    /** @var MigrationResult */
    protected $result;

    public function getMigration(): MigrationInterface
    {
        return $this->migration;
    }

    public function setMigration(MigrationInterface $migration): self
    {
        $this->migration = $migration;

        return $this;
    }

    public function getResult(): MigrationResult
    {
        return $this->result;
    }

    public function setResult(MigrationResult $result): self
    {
        $this->result = $result;

        return $this;
    }
}
