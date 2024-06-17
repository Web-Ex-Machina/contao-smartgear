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

namespace WEM\SmartgearBundle\Classes\Version;

class Comparator
{
    public const CURRENT_VERSION_HIGHER = -1;

    public const VERSIONS_EQUALS = 0;

    public const CURRENT_VERSION_LOWER = 1;

    /**
     * Compare  2 versions.
     *
     * example : (new Comparator())->compare((new Version())->fromString('1.0.0'), (new Version())->fromString('1.2.3'))
     *
     * @param Version $currentVersion          The current version
     * @param Version $versionToCompareAgainst The version to compare against
     *
     * @return int Comparator::CURRENT_VERSION_HIGHER if current version is higher, Comparator::VERSIONS_EQUALS if equals, Comparator::CURRENT_VERSION_LOWER if current version is lower
     */
    public function compare(Version $currentVersion, Version $versionToCompareAgainst): int
    {
        if ($currentVersion->getMajor() === $versionToCompareAgainst->getMajor()) {
            if ($currentVersion->getMinor() === $versionToCompareAgainst->getMinor()) {
                if ($currentVersion->getFix() === $versionToCompareAgainst->getFix()) {
                    return self::VERSIONS_EQUALS;
                }

                if ($currentVersion->getFix() > $versionToCompareAgainst->getFix()) {
                    return self::CURRENT_VERSION_HIGHER;
                }

                return self::CURRENT_VERSION_LOWER;
            }

            if ($currentVersion->getMinor() > $versionToCompareAgainst->getMinor()) {
                return self::CURRENT_VERSION_HIGHER;
            }

            return self::CURRENT_VERSION_LOWER;
        }

        if ($currentVersion->getMajor() > $versionToCompareAgainst->getMajor()) {
            return self::CURRENT_VERSION_HIGHER;
        }

        return self::CURRENT_VERSION_LOWER;
    }
}
