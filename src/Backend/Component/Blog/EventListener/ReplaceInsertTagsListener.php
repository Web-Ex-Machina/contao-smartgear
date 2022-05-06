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

namespace WEM\SmartgearBundle\Backend\Component\Blog\EventListener;

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
        if ('sg' === $key && 'blog' === substr($elements[1], 0, 4)) {
            /** @var CoreConfig */
            $config = $this->coreConfigurationManager->load();
            $blogConfig = $config->getSgBlog();

            if (!$blogConfig->getSgInstallComplete()) {
                return static::NOT_HANDLED;
            }

            switch ($elements[1]) {
                case 'blog_installComplete':
                    return $blogConfig->getSgInstallComplete() ? '1' : '0';
                break;
                case 'blog_newsArchive':
                    return $blogConfig->getSgNewsArchive();
                break;
                case 'blog_page':
                    return $blogConfig->getSgPage();
                break;
                case 'blog_moduleReader':
                    return $blogConfig->getSgModuleReader();
                break;
                case 'blog_moduleList':
                    return $blogConfig->getSgModuleList();
                break;
                case 'blog_currentPresetIndex':
                    return $blogConfig->getSgCurrentPresetIndex();
                break;
                case 'blog_archived':
                    return $blogConfig->getSgArchived() ? '1' : '0';
                break;
                case 'blog_archivedAt':
                    return $blogConfig->getSgArchivedAt();
                break;
                case 'blog_archivedMode':
                    return $blogConfig->getSgArchivedMode();
                break;
                case 'blog_newsFolder':
                    return $blogConfig->getCurrentPreset()->getSgNewsFolder();
                break;
                case 'blog_newsArchiveTitle':
                    return $blogConfig->getCurrentPreset()->getSgNewsArchiveTitle();
                break;
                case 'blog_newsListPerPage':
                    return $blogConfig->getCurrentPreset()->getSgNewsListPerPage();
                break;
                case 'blog_newsPageTitle':
                    return $blogConfig->getCurrentPreset()->getSgPageTitle();
                break;
                default:
                return static::NOT_HANDLED;
            }
        }

        return static::NOT_HANDLED;
    }
}
