<?php

$header = <<<EOF
SMARTGEAR for Contao Open Source CMS
Copyright (c) 2015-2021 Web ex Machina

@category ContaoBundle
@package  Web-Ex-Machina/contao-smartgear
@author   Web ex Machina <contact@webexmachina.fr>
@link     https://github.com/Web-Ex-Machina/contao-smartgear/
EOF;

$finder = PhpCsFixer\Finder::create()
->in(__DIR__ . '/src')
->in(__DIR__ . '/tests')
;

$config = new PhpCsFixer\Config();
return $config->setRules([
    '@PhpCsFixer' => true,
    '@PhpCsFixer:risky' => true,
    '@PHP71Migration' => true,
    '@PHP71Migration:risky' => true,
    '@PHPUnit75Migration:risky' => true,

    // @PhpCsFixer adjustments
    'blank_line_before_statement' => [
        'statements' => [
            'case',
            'declare',
            'default',
            'do',
            'for',
            'foreach',
            'if',
            'return',
            'switch',
            'throw',
            'try',
            'while',
        ],
    ],
    'explicit_indirect_variable' => false,
    'explicit_string_variable' => false,
    'method_chaining_indentation' => false,
    'no_extra_blank_lines' => [
        'tokens' => [
            'curly_brace_block',
            'extra',
            'parenthesis_brace_block',
            'square_brace_block',
            'throw',
            'use',
        ],
    ],
    'php_unit_internal_class' => false,
    'php_unit_test_class_requires_covers' => false,
    'phpdoc_types_order' => false,
    'single_line_comment_style' => [
        'comment_types' => ['hash'],
    ],
    'single_line_throw' => true,

    // @PhpCsFixer:risky adjustments
    'final_internal_class' => false,
    'php_unit_strict' => false,
    'php_unit_test_case_static_method_calls' => [
        'call_type' => 'this',
    ],

    // Other
    'linebreak_after_opening_tag' => true,
    'list_syntax' => ['syntax' => 'short'],
    'no_superfluous_phpdoc_tags' => true,
    'static_lambda' => true,

    // WeM Rules
    'header_comment' => ['header' => $header]
])
->setFinder($finder)
->setRiskyAllowed(true)
->setUsingCache(false)
;