<?php

declare(strict_types=1);

use Contao\Rector\Set\ContaoSetList;
use Rector\CodeQuality\Rector\If_\CombineIfRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([__DIR__ . '/src'])
    ->withPhpSets(php82: true)
    ->withSkip([
        CombineIfRector::class
    ])
    ->withAttributesSets(symfony: true, doctrine: true)
    ->withSets([
        ContaoSetList::CONTAO_50,
        ContaoSetList::CONTAO_53,
        ContaoSetList::ANNOTATIONS_TO_ATTRIBUTES,
        ContaoSetList::FQCN
    ])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true)
    ;