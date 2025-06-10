<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Comment\HeaderCommentFixer;
use Symplify\CodingStandard\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer;

use Contao\EasyCodingStandard\Set\SetList;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()->withSets([SetList::CONTAO])
    ->withConfiguredRule(HeaderCommentFixer::class, configuration: [
        'header' =>
"SMARTGEAR for Contao Open Source CMS
Copyright (c) 2015-".date("Y")." Web ex Machina

@category ContaoBundle
@package  Web-Ex-Machina/contao-smartgear
@author   Web ex Machina <contact@webexmachina.fr>
@link     https://github.com/Web-Ex-Machina/contao-smartgear/"])
    ->withPaths([
    __DIR__ . '/src',
])
->withSkip([
    ArrayOpenerAndCloserNewlineFixer::class,
    __DIR__ . '/migrations',
    __DIR__ . '/vendor',
    __DIR__ . '/var',
    __DIR__ . '/config/jwt',
    __DIR__ . '/config/secrets',
    __DIR__ . '/config/bundles.php',
])

    // NOTE : common intègre les règles : arrays, spaces, namespaces, docblocks, controlStructures, phpunit, comments
->withPreparedSets(
    $psr12=true,
    $common=true,
    $strict=true,
);