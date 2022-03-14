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

namespace WEM\SmartgearBundle\Migrations\V1_0_0;

use WEM\SmartgearBundle\Classes\Migration\MigrationInterface;
use WEM\SmartgearBundle\Classes\Migration\Result;
use WEM\SmartgearBundle\Classes\Version\Version;

class Migration implements MigrationInterface
{
    public function shouldRun(): Result
    {
        return (new Result())->setStatus(Result::STATUS_SHOULD_RUN);
    }

    public function do(): Result
    {
        return (new Result())
            ->setStatus(Result::STATUS_SKIPPED)
            ->addLog('Step 1 : preparing')
            ->addLog('Step 2 : doing')
            ->addLog('Step 3 : doing again')
            ->addLog('Step 4 : done !')
            ->addLog('Step 5 : enjoy \o/')
        ;
    }

    public function getName(): string
    {
        return 'test';
    }

    public function getDescription(): string
    {
        return 'a getDescription';
    }

    public function getVersion(): Version
    {
        return (new Version())->fromString('1.0.0');
    }
}
