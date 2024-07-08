<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2023 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

namespace WEM\SmartgearBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\Model\Collection;
use WEM\SmartgearBundle\Classes\Backend\Component\EventListener\ReplaceInsertTagsListener as AbstractReplaceInsertTagsListener;
use WEM\SmartgearBundle\Model\Configuration\Configuration;
use WEM\SmartgearBundle\Model\Configuration\ConfigurationItem;

#[AsHook('replaceInsertTags',"onReplaceInsertTags",-1)]
class ReplaceInsertTagsListener
{
    public function __construct(protected array $listeners)
    {
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
    ): false|string
    {
        $elements = explode('::', $insertTag);
        $key = strtolower($elements[0]);
        if ('sg' === $key) {
            $returnValue = $this->replaceInsertTags($insertTag, $useCache, $cachedValue, $flags, $tags, $cache, $_rit, $_cnt);
            if (AbstractReplaceInsertTagsListener::NOT_HANDLED !== $returnValue) {
                return $returnValue;
            }

            foreach ($this->listeners as $listener) {
                $returnValue = $listener->onReplaceInsertTags($insertTag, $useCache, $cachedValue, $flags, $tags, $cache, $_rit, $_cnt);
                if (AbstractReplaceInsertTagsListener::NOT_HANDLED !== $returnValue) {
                    return $returnValue;
                }
            }
        }

        return false;
    }

    protected function replaceInsertTags(
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
            global $objPage;
            $objConfiguration = $objPage ? Configuration::findOneByPage($objPage) : null;

            if (!$objPage || !$objConfiguration) {
                return false;
            }

            switch ($elements[1]) {
                case 'config':
                    if ($elements[2] === 'title') {
                    return $objConfiguration->title;
                }

                break;
                case 'title':
                case 'version':
                case 'mode':
                case 'admin_email':
                case 'domain':
                case 'email_gateway':
                case 'language':
                case 'framway_path':
                case 'google_fonts':
                case 'analytics_solution':
                case 'matomo_host':
                case 'matomo_id':
                case 'google_id':
                case 'legal_owner_type':
                case 'legal_owner_email':
                case 'legal_owner_street':
                case 'legal_owner_postal_code':
                case 'legal_owner_city':
                case 'legal_owner_region':
                case 'legal_owner_country':
                case 'legal_owner_person_lastname':
                case 'legal_owner_person_firstname':
                case 'legal_owner_company_name':
                case 'legal_owner_company_status':
                case 'legal_owner_company_identifier':
                case 'legal_owner_company_dpo_name':
                case 'legal_owner_company_dpo_email':
                case 'host_name':
                case 'host_street':
                case 'host_postal_code':
                case 'host_city':
                case 'host_region':
                case 'host_country':
                case 'contao_theme':
                case 'contao_module_sitemap':
                case 'contao_layout_full':
                case 'contao_layout_standard':
                case 'contao_page_root':
                case 'contao_page_home':
                case 'contao_page_404':
                case 'api_enabled':
                case 'api_key':
                    return $objConfiguration->{$elements[1]};
                case 'websiteTitle':
                    return $objConfiguration->title;
                case 'legal_owner_address_full':
                    return $objConfiguration->legal_owner_street.' '.$objConfiguration->legal_owner_postal_code.' '.$objConfiguration->legal_owner_city.' '.$objConfiguration->legal_owner_region.' '.$objConfiguration->legal_owner_country;
                case 'domain_full':
                    return str_contains($objConfiguration->domain, 'https://')
                    ? $objConfiguration->domain
                    : (
                        str_contains($objConfiguration->domain, 'http://')
                        ? str_replace('http://', 'https://', $objConfiguration->domain)
                        : 'https://'.$objConfiguration->domain
                    )
                    ;
                case 'pouet':
                    return 'https://pouet-pouet-pouet.fr';
                case 'page-legal-notice':
                    $objCI = ConfigurationItem::findItems(['pid' => $objConfiguration->id, 'type' => ConfigurationItem::TYPE_PAGE_LEGAL_NOTICE], 1);
                    if (!$objCI instanceof Collection) {
                        return false;
                    }

                    $objPage2 = $objCI->getRelated('contao_page');
                    if (!$objPage2) {
                        return false;
                    }

                    return \array_key_exists(2, $elements) ? $objPage2->{$elements[2]} : $objPage2->alias;
                case 'page-privacy-politics':
                    $objCI = ConfigurationItem::findItems(['pid' => $objConfiguration->id, 'type' => ConfigurationItem::TYPE_PAGE_PRIVACY_POLITICS], 1);
                    if (!$objCI instanceof Collection) {
                        return false;
                    }

                    $objPage2 = $objCI->getRelated('contao_page');
                    if (!$objPage2) {
                        return false;
                    }

                    return \array_key_exists(2, $elements) ? $objPage2->{$elements[2]} : $objPage2->alias;
                case 'page-sitemap':
                    $objCI = ConfigurationItem::findItems(['pid' => $objConfiguration->id, 'type' => ConfigurationItem::TYPE_PAGE_SITEMAP], 1);
                    if (!$objCI instanceof Collection) {
                        $objCI = ConfigurationItem::findItems(['pid' => $objConfiguration->id, 'type' => ConfigurationItem::TYPE_MIXED_SITEMAP], 1);
                    }

                    if (!$objCI instanceof Collection) {
                        return false;
                    }

                    $objPage2 = $objCI->getRelated('contao_page');
                    if (!$objPage2) {
                        return false;
                    }

                    return \array_key_exists(2, $elements) ? $objPage2->{$elements[2]} : $objPage2->alias;
            }
        }

        return AbstractReplaceInsertTagsListener::NOT_HANDLED;
    }
}
