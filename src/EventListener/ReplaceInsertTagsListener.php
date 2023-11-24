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

use WEM\SmartgearBundle\Classes\Backend\Component\EventListener\ReplaceInsertTagsListener as AbstractReplaceInsertTagsListener;
use WEM\SmartgearBundle\Model\Configuration\Configuration;

class ReplaceInsertTagsListener
{
    /** @var array */
    protected $listeners;

    public function __construct(
        array $listeners
    ) {
        $this->listeners = $listeners;
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
            switch ($elements[1]) {
                case 'config':
                    global $objPage;
                    if (!$objPage) {
                        return false;
                    }
                    $objConfiguration = Configuration::findOneByPage($objPage);
                    if (!$objConfiguration) {
                        return false;
                    }

                    switch ($elements[2]) {
                        case 'title':
                            return $objConfiguration->title;
                        break;
                    }

                break;
            }
        }

        return AbstractReplaceInsertTagsListener::NOT_HANDLED;
    }
}
