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

namespace WEM\SmartgearBundle\EventListener;

use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;

class ReplaceInsertTagsListener
{
    /** @var CoreConfigurationManager */
    protected $coreConfigurationManager;

    public function __construct(
        CoreConfigurationManager $coreConfigurationManager
    ) {
        $this->coreConfigurationManager = $coreConfigurationManager;
    }

    /**
     * Handles Smartgear insert tags.
     *
     * @see https://docs.contao.org/dev/reference/hooks/replaceInsertTags/
     *
     * @param string $insertTag   the unknown insert tag
     * @param bool   $useCache    indicates if we are supposed to cache
     * @param string $cachedValue the cached replacement for this insert tag (if there is any)
     * @param array  $flags       an array of flags used with this insert tag
     * @param array  $tags        contains the result of spliting the page’s content in order to replace the insert tags
     * @param array  $cache       the cached replacements of insert tags found on the page so far
     * @param int    $_rit        counter used while iterating over the parts in $tags
     * @param int    $_cnt        number of elements in $tags
     *
     * @return string|false if the tags isn't managed by this class, return false
     */
    public function onReplaceInsertTags(
        string $insertTag,
        bool $useCache,
        string $cachedValue,
        array $flags,
        array $tags,
        array $cache,
        int $_rit,
        int $_cnt
    ) {
        $elements = explode('::', $insertTag);
        $key = strtolower($elements[0]);
        if ('sg' === $key) {
            $config = $this->coreConfigurationManager->load();

            switch ($elements[1]) {
                case 'InstallComplete':
                    return $config->getSgInstallComplete() ? '1' : '0';
                break;
                case 'Version':
                    return $config->getSgVersion();
                break;
                case 'FramwayPath':
                    return $config->getSgFramwayPath();
                break;
                case 'FramwayThemes':
                    return implode(', ', $config->getSgFramwayThemes());
                break;
                case 'GoogleFonts':
                    return implode(', ', $config->getSgGoogleFonts());
                break;
                case 'SelectedModules':
                    return implode(', ', $config->getSgSelectedModules());
                break;
                case 'Mode':
                    return $config->getSgMode();
                break;
                case 'WebsiteTitle':
                    return $config->getSgWebsiteTitle();
                break;
                case 'OwnerEmail':
                    return $config->getSgOwnerEmail();
                break;
                case 'Analytics':
                    return $config->getSgAnalytics();
                break;
                case 'AnalyticsGoogleId':
                    return $config->getSgAnalyticsGoogleId();
                break;
                case 'AnalyticsMatomoHost':
                    return $config->getSgAnalyticsMatomoHost();
                break;
                case 'AnalyticsMatomoId':
                    return $config->getSgAnalyticsMatomoId();
                break;
                case 'OwnerName':
                    return $config->getSgOwnerName();
                break;
                case 'OwnerDomain':
                    return $config->getSgOwnerDomain();
                break;
                case 'OwnerHost':
                    return $config->getSgOwnerHost();
                break;
                case 'OwnerLogo':
                    return $config->getSgOwnerLogo();
                break;
                case 'OwnerStatus':
                    return $config->getSgOwnerStatus();
                break;
                case 'OwnerStreet':
                    return $config->getSgOwnerStreet();
                break;
                case 'OwnerPostal':
                    return $config->getSgOwnerPostal();
                break;
                case 'OwnerCity':
                    return $config->getSgOwnerCity();
                break;
                case 'OwnerRegion':
                    return $config->getSgOwnerRegion();
                break;
                case 'OwnerCountry':
                    return $config->getSgOwnerCountry();
                break;
                case 'OwnerSiret':
                    return $config->getSgOwnerSiret();
                break;
                case 'OwnerDpoName':
                    return $config->getSgOwnerDpoName();
                break;
                case 'OwnerDpoEmail':
                    return $config->getSgOwnerDpoEmail();
                break;
                case 'Theme':
                    return $config->getSgTheme();
                break;
                case 'Modules':
                    $modules = $config->getSgModules();
                    $arrModules = [];
                    foreach ($modules as $module) {
                        $arrModules[] = $module->type;
                    }

                    return implode(', ', $arrModules);
                break;
                case 'ApiKey':
                    return $config->getSgApiKey();
                break;
                default:
                return false;
            }
        }

        return false;
    }
}
