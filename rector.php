<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([__DIR__ . '/src'])
    ->withPhpSets()
    ->withPreparedSets(typeDeclarations: true, deadCode: true, codeQuality: true,codingStyle: true);