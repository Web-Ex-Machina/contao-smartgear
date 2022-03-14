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

interface MigrationInterface
{
    public function shouldRun(): Result;

    public function do(): Result;

    public function getName(): string;

    public function getDescription(): string;

    public function getVersion(): Version;
}
