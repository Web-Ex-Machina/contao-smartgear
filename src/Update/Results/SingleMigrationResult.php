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
use WEM\SmartgearBundle\Classes\Version\Version;

class SingleMigrationResult
{
    protected string $name;

    protected string $description;

    protected Version $version;

    protected MigrationResult $result;

    public function setMigration(MigrationInterface $migration): self
    {
        $this->name = $migration->getTranslatedName();
        $this->description = $migration->getTranslatedDescription();
        $this->version = $migration->getVersion();

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

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getVersion(): Version
    {
        return $this->version;
    }
}
