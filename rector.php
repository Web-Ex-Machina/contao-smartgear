<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Array_\CallableThisArrayToAnonymousFunctionRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([__DIR__ . '/src'])
    ->withPhpSets(php83: true)
    ->withDeadCodeLevel(level:1)
    ->withSkip([
        CallableThisArrayToAnonymousFunctionRector::class
    ])
    ->withAttributesSets(symfony: true, doctrine: true)
    ->withPreparedSets(codeQuality: true, codingStyle: true, typeDeclarations: true)
    ;