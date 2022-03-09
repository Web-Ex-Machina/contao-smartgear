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

$GLOBALS['TL_CONFIG']['licenseAccepted'] = true;
$GLOBALS['TL_CONFIG']['installPassword'] = '$2y$10$D80Ctv4uiRF7pSQJew3NvOALREdkxwrnHUSKW/PIVHtwLXdNlqjz2'; // azertyui
$GLOBALS['TL_CONFIG']['adminEmail'] = 'contact@webexmachina.fr';
$GLOBALS['TL_CONFIG']['fs_replaceChar'] = '-';
$GLOBALS['TL_CONFIG']['fs_validAlphabets'] = 'a:2:{i:0;s:12:"smallLetters";i:1;s:7:"numbers";}';
$GLOBALS['TL_CONFIG']['fs_validSpecialChars'] = '-';
$GLOBALS['TL_CONFIG']['fs_trim'] = true;
$GLOBALS['TL_CONFIG']['fs_trimChars'] = '-_.,;|';
$GLOBALS['TL_CONFIG']['fs_condenseSeparators'] = true;
$GLOBALS['TL_CONFIG']['fs_charReplacements'] = 'a:4:{i:0;a:3:{s:6:"source";s:2:"ä";s:6:"target";s:2:"ae";s:10:"ignoreCase";s:1:"1";}i:1;a:3:{s:6:"source";s:2:"ö";s:6:"target";s:2:"oe";s:10:"ignoreCase";s:1:"1";}i:2;a:3:{s:6:"source";s:2:"ü";s:6:"target";s:2:"ue";s:10:"ignoreCase";s:1:"1";}i:3;a:3:{s:6:"source";s:2:"ß";s:6:"target";s:2:"ss";s:10:"ignoreCase";s:1:"1";}}';
$GLOBALS['TL_CONFIG']['dbCacheMaxTime'] = 'a:2:{s:4:"unit";s:1:"d";s:5:"value";i:1;}';
$GLOBALS['TL_CONFIG']['timeZone'] = 'Europe/Paris';
$GLOBALS['TL_CONFIG']['og_image_size'] = 'a:3:{i:0;s:0:"";i:1;s:0:"";i:2;s:0:"";}';
$GLOBALS['TL_CONFIG']['twitter_image_size'] = 'a:3:{i:0;s:0:"";i:1;s:0:"";i:2;s:0:"";}';
// @todo : remove this line until Contao BE file explorer is no more broken
// cf cd https://github.com/marcel-mathias-nolte/contao-filesmanager-fileusage/issues/4
$GLOBALS['TL_CONFIG']['fileusageSkipReplaceInsertTags'] = true;
