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

namespace WEM\SmartgearBundle\Backend\Component\Core\EventListener;

use WEM\SmartgearBundle\Classes\Backend\Component\EventListener\ReplaceInsertTagsListener as AbstractReplaceInsertTagsListener;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;

class ReplaceInsertTagsListener extends AbstractReplaceInsertTagsListener
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
            /** @var CoreConfig */
            $config = $this->coreConfigurationManager->load();

            switch ($elements[1]) {
                case 'installComplete':
                    return $config->getSgInstallComplete() ? '1' : '0';
                break;
                case 'version':
                    return $config->getSgVersion();
                break;
                case 'framwayPath':
                    return $config->getSgFramwayPath();
                break;
                case 'framwayThemes':
                    return implode(', ', $config->getSgFramwayThemes());
                break;
                case 'googleFonts':
                    return implode(', ', $config->getSgGoogleFonts());
                break;
                case 'selectedModules':
                    return implode(', ', $config->getSgSelectedModules());
                break;
                case 'mode':
                    return $config->getSgMode();
                break;
                case 'websiteTitle':
                    return $config->getSgWebsiteTitle();
                break;
                case 'ownerEmail':
                    return $config->getSgOwnerEmail();
                break;
                case 'analytics':
                    return $config->getSgAnalytics();
                break;
                case 'analyticsGoogleId':
                    return $config->getSgAnalyticsGoogleId();
                break;
                case 'analyticsMatomoHost':
                    return $config->getSgAnalyticsMatomoHost();
                break;
                case 'analyticsMatomoId':
                    return $config->getSgAnalyticsMatomoId();
                break;
                case 'ownerName':
                    return $config->getSgOwnerName();
                break;
                case 'ownerDomain':
                    return $config->getSgOwnerDomain();
                break;
                case 'ownerHost':
                    return $config->getSgOwnerHost();
                break;
                case 'ownerLogo':
                    return $config->getSgOwnerLogo();
                break;
                case 'ownerStatus':
                    return $config->getSgOwnerStatus();
                break;
                case 'ownerStreet':
                    return $config->getSgOwnerStreet();
                break;
                case 'ownerPostal':
                    return $config->getSgOwnerPostal();
                break;
                case 'ownerCity':
                    return $config->getSgOwnerCity();
                break;
                case 'ownerRegion':
                    return $config->getSgOwnerRegion();
                break;
                case 'ownerCountry':
                    return $config->getSgOwnerCountry();
                break;
                case 'ownerSiret':
                    return $config->getSgOwnerSiret();
                break;
                case 'ownerDpoName':
                    return $config->getSgOwnerDpoName();
                break;
                case 'ownerDpoEmail':
                    return $config->getSgOwnerDpoEmail();
                break;
                case 'theme':
                    return (string) $config->getSgTheme();
                break;
                case 'layoutStandard':
                    return (string) $config->getSgLayoutStandard();
                break;
                case 'layoutFullwidth':
                    return (string) $config->getSgLayoutFullwidth();
                break;
                case 'pageRoot':
                    return (string) $config->getSgPageRoot();
                break;
                case 'pageHome':
                    return (string) $config->getSgPageHome();
                break;
                case 'page404':
                    return (string) $config->getSgPage404();
                break;
                case 'pageLegalNotice':
                    return (string) $config->getSgPageLegalNotice();
                break;
                case 'pagePrivacyPolitics':
                    return (string) $config->getSgPagePrivacyPolitics();
                break;
                case 'pageSitemap':
                    return (string) $config->getSgPageSitemap();
                break;
                case 'notificationGatewayEmail':
                    return (string) $config->getSgNotificationGatewayEmail();
                break;
                case 'modules':
                    $modules = $config->getSgModules();
                    $arrModules = [];
                    foreach ($modules as $module) {
                        $arrModules[] = $module->type;
                    }

                    return implode(', ', $arrModules);
                break;
                case 'apiKey':
                    return $config->getSgApiKey();
                break;
                default:
                return static::NOT_HANDLED;
            }
        }

        return static::NOT_HANDLED;
    }
}
