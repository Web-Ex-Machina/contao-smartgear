<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Array_\CallableThisArrayToAnonymousFunctionRector;
use Rector\CodeQuality\Rector\If_\CombineIfRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([__DIR__ . '/src'])
    ->withPhpSets(php82: true)
    ->withSkip([
        CallableThisArrayToAnonymousFunctionRector::class,
        CombineIfRector::class
    ])
    ->withAttributesSets(symfony: true, doctrine: true)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true)
    ;