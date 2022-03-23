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

namespace WEM\SmartgearBundle\Classes\Migration;

use WEM\SmartgearBundle\Classes\Version\Version;

abstract class MigrationAbstract implements MigrationInterface
{
    public function getName(): string
    {
        return static::$name;
    }

    public function getDescription(): string
    {
        return static::$description;
    }

    public function getVersion(): Version
    {
        return (new Version())->fromString(static::$version);
    }

    abstract public function shouldRun(): Result;

    abstract public function do(): Result;
}
