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

namespace WEM\SmartgearBundle\Backend\Component\Faq\EventListener;

use WEM\SmartgearBundle\Classes\Backend\Component\EventListener\ReplaceInsertTagsListener as AbstractReplaceInsertTagsListener;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Config\Component\Faq\Faq as FaqConfig;

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
        if ('sg' === $key && str_starts_with($elements[1], 'faq')) {
            return static::NOT_HANDLED;
            /** @var CoreConfig */
            $config = $this->coreConfigurationManager->load();
            /** @var FaqConfig */
            $faqConfig = $config->getSgFaq();

            if (!$faqConfig->getSgInstallComplete()) {
                return static::NOT_HANDLED;
            }

            return match ($elements[1]) {
                'faq_installComplete' => $faqConfig->getSgInstallComplete() ? '1' : '0',
                'faq_faqFolder' => $faqConfig->getSgFaqFolder(),
                'faq_pageTitle' => $faqConfig->getSgPageTitle(),
                'faq_faqTitle' => $faqConfig->getSgFaqTitle(),
                'faq_page' => $faqConfig->getSgPage(),
                'faq_article' => (string) $faqConfig->getSgArticle(),
                'faq_content' => (string) $faqConfig->getSgContent(),
                'faq_moduleFaq' => $faqConfig->getSgModuleFaq(),
                'faq_faqCategory' => $faqConfig->getSgFaqCategory(),
                'faq_archived' => $faqConfig->getSgArchived() ? '1' : '0',
                'faq_archivedAt' => $faqConfig->getSgArchivedAt(),
                'faq_archivedMode' => $faqConfig->getSgArchivedMode(),
                default => static::NOT_HANDLED,
            };
        }

        return static::NOT_HANDLED;
    }
}
