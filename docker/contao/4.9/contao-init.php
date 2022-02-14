<?php

declare(strict_types=1);

/**
 * Metal Store Bundle for Contao Open Source CMS
 * Copyright (c) 2021-2021 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/metalstore-contao-bundle
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/metalstore-contao-bundle/
 */

$sql = '
INSERT INTO `tl_user`
(`id`,`tstamp`,`username`,`name`,`email`,`language`,`backendTheme`,`fullscreen`,`uploader`,`showHelp`,`thumbnails`,`useRTE`,`useCE`,`password`,`pwChange`,`admin`,`groups`,`inherit`,`modules`,`themes`,`elements`,`fields`,`pagemounts`,`alpty`,`filemounts`,`fop`,`imageSizes`,`forms`,`formp`,`amg`,`disable`,`start`,`stop`,`session`,`dateAdded`,`secret`,`useTwoFactor`,`lastLogin`,`currentLogin`,`loginAttempts`,`locked`,`backupCodes`,`trustedTokenVersion`,`faqs`,`faqp`,`news`,`newp`,`newsfeeds`,`newsfeedp`,`calendars`,`calendarp`,`calendarfeeds`,`calendarfeedp`,`newsletters`,`newsletterp`)
VALUES
(1,1643644391,\''.getenv('CONTAO_USER').'\',\''.getenv('CONTAO_USER').'\',\''.getenv('CONTAO_USER_MAIL').'\',\'fr\',\'flexible\',\'\',\'\',\'1\',\'1\',\'1\',\'1\',\''.getenv('CONTAO_USER_PWD').'\',\'\',\'1\',NULL,\'group\',\'a:0:{}\',\'a:0:{}\',\'a:0:{}\',\'a:0:{}\',\'a:0:{}\',\'a:0:{}\',\'a:0:{}\',\'a:0:{}\',\'a:0:{}\',\'a:0:{}\',\'a:0:{}\',\'a:0:{}\',\'\',\'\',\'\',\'a:0:{}\',1643644391,NULL,\'\',0,1643644400,0,0,NULL,0,\'a:0:{}\',\'a:0:{}\',\'a:0:{}\',\'a:0:{}\',\'a:0:{}\',\'a:0:{}\',\'a:0:{}\',\'a:0:{}\',\'a:0:{}\',\'a:0:{}\',\'a:0:{}\',\'a:0:{}\');
';

$sqlConnection = new PDO(
    'mysql:host='.getenv('DB_HOST').';port='.getenv('DB_PORT').';dbname='.getenv('DB_DATABASE'),
    getenv('DB_USER'),
    getenv('DB_PASSWORD')
);
$sqlConnection->prepare($sql, [])->execute();

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
