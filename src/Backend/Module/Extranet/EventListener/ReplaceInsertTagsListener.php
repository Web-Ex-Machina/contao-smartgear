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

namespace WEM\SmartgearBundle\Backend\Module\Extranet\EventListener;

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
        if ('sg' === $key && 'extranet' === substr($elements[1], 0, 3)) {
            /** @var CoreConfig */
            $config = $this->coreConfigurationManager->load();
            $extranetConfig = $config->getSgExtranet();

            if (!$extranetConfig->getSgInstallComplete()) {
                return static::NOT_HANDLED;
            }

            switch ($elements[1]) {
                case 'extranet_installComplete':
                    return $extranetConfig->getSgInstallComplete() ? '1' : '0';
                break;
                case 'extranet_extranetFolder':
                    return $extranetConfig->getSgExtranetFolder();
                break;
                case 'extranet_pageTitle':
                    return $extranetConfig->getSgPageTitle();
                break;
                case 'extranet_extranetTitle':
                    return $extranetConfig->getSgExtranetTitle();
                break;
                case 'extranet_page':
                    return $extranetConfig->getSgPage();
                break;
                case 'extranet_article':
                    return (string) $extranetConfig->getSgArticle();
                break;
                case 'extranet_content':
                    return (string) $extranetConfig->getSgContent();
                break;
                case 'extranet_moduleExtranet':
                    return $extranetConfig->getSgModuleExtranet();
                break;
                case 'extranet_extranetCategory':
                    return $extranetConfig->getSgExtranetCategory();
                break;
                case 'extranet_archived':
                    return $extranetConfig->getSgArchived() ? '1' : '0';
                break;
                case 'extranet_archivedAt':
                    return $extranetConfig->getSgArchivedAt();
                break;
                case 'extranet_archivedMode':
                    return $extranetConfig->getSgArchivedMode();
                break;
                default:
                return static::NOT_HANDLED;
            }
        }

        return static::NOT_HANDLED;
    }
}
