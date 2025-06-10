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

use Contao\EasyCodingStandard\Fixer\CommentLengthFixer;
use Contao\EasyCodingStandard\Fixer\FunctionCallWithMultilineArrayFixer;
use Contao\EasyCodingStandard\Fixer\MultiLineIfIndentationFixer;
use Contao\EasyCodingStandard\Fixer\NoLineBreakBetweenMethodArgumentsFixer;
use Contao\EasyCodingStandard\Fixer\TypeHintOrderFixer;
use Contao\EasyCodingStandard\Set\SetList;
use PhpCsFixer\Fixer\Comment\HeaderCommentFixer;
use PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer;
use PhpCsFixer\Fixer\Import\FullyQualifiedStrictTypesFixer;
use PhpCsFixer\Fixer\Import\GlobalNamespaceImportFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withSets([SetList::CONTAO])
    ->withConfiguredRule(HeaderCommentFixer::class, configuration: [
        'header' => 'SMARTGEAR for Contao Open Source CMS
Copyright (c) 2015-' . date('Y') . ' Web ex Machina

@category ContaoBundle
@package  Web-Ex-Machina/contao-smartgear
@author   Web ex Machina <contact@webexmachina.fr>
@link     https://github.com/Web-Ex-Machina/contao-smartgear/',
    ])
    ->withPaths([__DIR__ . '/src'])
    ->withSkip([
        __DIR__ . '/src/Migrations',
        TypeHintOrderFixer::class,
        NoLineBreakBetweenMethodArgumentsFixer::class,
        GlobalNamespaceImportFixer::class,
        FullyQualifiedStrictTypesFixer::class,
        NativeFunctionInvocationFixer::class,
        MultiLineIfIndentationFixer::class,
        FunctionCallWithMultilineArrayFixer::class,
        CommentLengthFixer::class
    ])
    ->withPreparedSets(
        psr12: true,
        comments: true,
        docblocks: true,
        spaces: true,
        namespaces: true,
        controlStructures: true,
        strict: true,
        cleanCode: true,
    );
