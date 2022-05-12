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

namespace WEM\SmartgearBundle\Backend\Component\FormContact\EventListener;

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
        if ('sg' === $key && 'formContact' === substr($elements[1], 0, 11)) {
            /** @var CoreConfig */
            $config = $this->coreConfigurationManager->load();
            $formContactConfig = $config->getSgFormContact();

            if (!$formContactConfig->getSgInstallComplete()) {
                return static::NOT_HANDLED;
            }

            switch ($elements[1]) {
                case 'formContact_installComplete':
                    return $formContactConfig->getSgInstallComplete() ? '1' : '0';
                break;
                case 'formContact_formContactFolder':
                    return $formContactConfig->getSgFormContactFolder();
                break;
                case 'formContact_pageTitle':
                    return $formContactConfig->getSgPageTitle();
                break;
                case 'formContact_formContactTitle':
                    return $formContactConfig->getSgFormContactTitle();
                break;
                case 'formContact_page':
                    return $formContactConfig->getSgPage();
                break;
                case 'formContact_article':
                    return (string) $formContactConfig->getSgArticle();
                break;
                case 'formContact_content':
                    return (string) $formContactConfig->getSgContent();
                break;
                case 'formContact_moduleFormContact':
                    return $formContactConfig->getSgModuleFormContact();
                break;
                case 'formContact_formContactCategory':
                    return $formContactConfig->getSgFormContactCategory();
                break;
                case 'formContact_archived':
                    return $formContactConfig->getSgArchived() ? '1' : '0';
                break;
                case 'formContact_archivedAt':
                    return $formContactConfig->getSgArchivedAt();
                break;
                case 'formContact_archivedMode':
                    return $formContactConfig->getSgArchivedMode();
                break;
                default:
                return static::NOT_HANDLED;
            }
        }

        return static::NOT_HANDLED;
    }
}
