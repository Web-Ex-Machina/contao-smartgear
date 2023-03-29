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
$bundleAuthJsonContent = file_exists(getenv('WORKDIR_BUNDLE').'/auth.json') ? json_decode(file_get_contents(getenv('WORKDIR_BUNDLE').'/auth.json') ?? '{}', true) : [];
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

$contaoComposerJsonContent['repositories'] = array_values(unique_multidim_array(array_merge(
    $contaoComposerJsonContent['repositories'],
    $bundleComposerJsonContent['repositories'] ?? [],
    [
        [
            'type' => 'path',
            // 'url' => getenv('WORKDIR_BUNDLE'),
            'url' => getenv('WORKDIR_CONTAO').'/vendor/webexmachina/'.getenv('BUNDLE_NAME'),
        ],
    ]
), 'url'));

$contaoComposerJsonContent['config']['allow-plugins'] = array_merge($contaoComposerJsonContent['config']['allow-plugins'] ?? [], $additionnalComposerJsonContent['config']['allow-plugins'] ?? []);

if (\array_key_exists('github-oauth', $bundleAuthJsonContent)) {
    file_put_contents('/var/www/.config/composer/auth.json', '{"github-oauth":'.json_encode($bundleAuthJsonContent['github-oauth'], \JSON_PRETTY_PRINT).'}');
    $contaoComposerJsonContent['github-oauth'] = $bundleAuthJsonContent['github-oauth'] ?? '';
}

$contaoComposerJsonContent['minimum-stability'] = 'dev';
$contaoComposerJsonContent['prefer-stable'] = true;
file_put_contents(getenv('WORKDIR_CONTAO').'/composer.json', json_encode($contaoComposerJsonContent, \JSON_PRETTY_PRINT));

/**
 * [unique_multidim_array description].
 *
 * @source https://www.php.net/manual/fr/function.array-unique.php#116302
 *
 * @param  [type] $array [description]
 * @param  [type] $key   [description]
 *
 * @return [type]        [description]
 */
function unique_multidim_array($array, $key)
{
    $temp_array = [];
    $i = 0;
    $key_array = [];

    foreach ($array as $val) {
        if (!\in_array($val[$key], $key_array, true)) {
            $key_array[$i] = $val[$key];
            $temp_array[$i] = $val;
        }
        ++$i;
    }

    return $temp_array;
}
