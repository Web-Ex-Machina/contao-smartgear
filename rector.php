<?php

declare(strict_types=1);

/*
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2025 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

use Contao\Rector\Set\ContaoSetList;
use Rector\CodeQuality\Rector\If_\CombineIfRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([__DIR__ . '/src'])
    ->withSkip([
        __DIR__ . '/src/Migrations',
        __DIR__ . '/src/Resources/contao/dca'
    ])
    ->withSkip([
        CombineIfRector::class
    ])
    ->withAttributesSets(symfony: true, doctrine: true)
    ->withSets([
        ContaoSetList::CONTAO_50,
        ContaoSetList::CONTAO_53,
        ContaoSetList::ANNOTATIONS_TO_ATTRIBUTES,
        ContaoSetList::FQCN,
    ])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true)
    ->withRootFiles();