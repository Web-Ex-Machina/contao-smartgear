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

// adding package informations into contao's composer.json
$bundleComposerJsonContent = json_decode(file_get_contents(getenv('WORKDIR_BUNDLE').'/composer.json'), true);
$contaoComposerJsonContent = json_decode(file_get_contents(getenv('WORKDIR_CONTAO').'/composer.json'), true);
$additionnalComposerJsonContent = json_decode(file_get_contents(__DIR__.\DIRECTORY_SEPARATOR.'additionnal_composer.json'), true);

if (\array_key_exists('require', $contaoComposerJsonContent)) {
    $contaoComposerJsonContent['require']['webexmachina/'.getenv('BUNDLE_NAME')] = '@dev';
} else {
    $contaoComposerJsonContent['require'] = ['webexmachina/'.getenv('BUNDLE_NAME') => '@dev'];
}

$contaoComposerJsonContent['require'] = array_merge($contaoComposerJsonContent['require'], $additionnalComposerJsonContent['require'] ?? []);
$contaoComposerJsonContent['require-dev'] = array_merge($contaoComposerJsonContent['require-dev'] ?? [], $bundleComposerJsonContent['require-dev'] ?? []);

$contaoComposerJsonContent['repositories'] = $contaoComposerJsonContent['repositories'] ?? [];
foreach ($contaoComposerJsonContent['repositories'] as $index => $repo) {
    if (\array_key_exists('url', $repo)
    && strpos($repo['url'], getenv('BUNDLE_NAME'))
    ) {
        unset($contaoComposerJsonContent['repositories'][$index]);
    }
}

$contaoComposerJsonContent['repositories'] = array_merge(
    $contaoComposerJsonContent['repositories'],
    [
        [
            'type' => 'path',
            // 'url' => getenv('WORKDIR_BUNDLE'),
            'url' => getenv('WORKDIR_CONTAO').'/vendor/webexmachina/'.getenv('BUNDLE_NAME'),
        ],
    ]
);

$contaoComposerJsonContent['minimum-stability'] = 'dev';
$contaoComposerJsonContent['prefer-stable'] = true;
file_put_contents(getenv('WORKDIR_CONTAO').'/composer.json', json_encode($contaoComposerJsonContent));
