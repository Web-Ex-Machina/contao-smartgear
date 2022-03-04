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

namespace WEM\SmartgearBundle\Classes;

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2022 Web ex Machina.
 *
 * @category ContaoBundle
 *
 * @author   Web ex Machina <contact@webexmachina.fr>
 *
 * @see     https://github.com/Web-Ex-Machina/contao-smartgear/
 */
class UtilFramway
{
    public const SCRIPTS_PATH = './bundles/wemsmartgear/scripts/smartgear/module/core/';

    public static function retrieve(string $framwayPath, bool $live = false)
    {
        set_time_limit(0);
        if ($live) {
            return Util::executeCmdLive('sh '.self::SCRIPTS_PATH.'framway_retrieve.sh ./'.$framwayPath);
        }

        return Util::executeCmd('sh '.self::SCRIPTS_PATH.'framway_retrieve.sh ./'.$framwayPath);
    }

    public static function install(string $framwayPath, bool $live = false)
    {
        set_time_limit(0);

        if ($live) {
            return Util::executeCmdLive('sh '.self::SCRIPTS_PATH.'framway_install.sh ./'.$framwayPath);
        }

        return Util::executeCmd('sh '.self::SCRIPTS_PATH.'framway_install.sh ./'.$framwayPath);
    }

    public static function initialize(string $framwayPath, bool $live = false)
    {
        set_time_limit(0);

        if ($live) {
            return Util::executeCmdLive('sh '.self::SCRIPTS_PATH.'framway_initialize.sh ./'.$framwayPath);
        }

        return Util::executeCmd('sh '.self::SCRIPTS_PATH.'framway_initialize.sh ./'.$framwayPath);
    }

    public static function build(string $framwayPath, bool $live = false)
    {
        set_time_limit(0);

        if ($live) {
            return Util::executeCmdLive('sh '.self::SCRIPTS_PATH.'framway_build.sh ./'.$framwayPath);
        }

        return Util::executeCmd('sh '.self::SCRIPTS_PATH.'framway_build.sh ./'.$framwayPath);
    }

    public static function checkPresence(string $framwayPath)
    {
        return file_exists($framwayPath.\DIRECTORY_SEPARATOR.'framway.config.js') && file_exists($framwayPath.\DIRECTORY_SEPARATOR.'build');
    }

    public static function addTheme(string $framwayPath, string $themeName, bool $live = false)
    {
        self::checkThemeName($themeName);

        if ($live) {
            return Util::executeCmdLive('sh '.self::SCRIPTS_PATH.'framway_theme_add.sh ./'.$framwayPath);
        }

        return Util::executeCmd('sh '.self::SCRIPTS_PATH.'framway_theme_add.sh ./'.$framwayPath.' '.$themeName);
    }

    public static function checkThemeName(string $themeName): void
    {
        if (!preg_match('/^([A-Za-z0-9-_]+)$/', $themeName)) {
            throw new \InvalidArgumentException('Le nom du nouveau thème est invalide! Les caractères autorisés sont : lettres, chiffres, tirets ("-") et underscores ("_").');
        }
    }
}