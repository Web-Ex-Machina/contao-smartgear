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

namespace WEM\SmartgearBundle\Backend\Component\Events\EventListener;

use WEM\SmartgearBundle\Classes\Backend\Component\EventListener\ReplaceInsertTagsListener as AbstractReplaceInsertTagsListener;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Config\Core as CoreConfig;

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
        if ('sg' === $key && 'events' === substr($elements[1], 0, 4)) {
            /** @var CoreConfig */
            $config = $this->coreConfigurationManager->load();
            $eventsConfig = $config->getSgEvents();

            if (!$eventsConfig->getSgInstallComplete()) {
                return static::NOT_HANDLED;
            }

            switch ($elements[1]) {
                case 'events_installComplete':
                    return $eventsConfig->getSgInstallComplete() ? '1' : '0';
                break;
                case 'events_calendar':
                    return $eventsConfig->getSgCalendar();
                break;
                case 'events_page':
                    return $eventsConfig->getSgPage();
                break;
                case 'events_moduleReader':
                    return $eventsConfig->getSgModuleReader();
                break;
                case 'events_moduleList':
                    return $eventsConfig->getSgModuleList();
                break;
                case 'events_moduleCalendar':
                    return $eventsConfig->getSgModuleCalendar();
                break;
                case 'events_archived':
                    return $eventsConfig->getSgArchived() ? '1' : '0';
                break;
                case 'events_archivedAt':
                    return $eventsConfig->getSgArchivedAt();
                break;
                case 'events_archivedMode':
                    return $eventsConfig->getSgArchivedMode();
                break;
                case 'events_eventsFolder':
                    return $eventsConfig->getSgEventsFolder();
                break;
                case 'events_calendarTitle':
                    return $eventsConfig->getSgCalendarTitle();
                break;
                case 'events_eventsListPerPage':
                    return $eventsConfig->getSgEventsListPerPage();
                break;
                case 'events_eventsPageTitle':
                    return $eventsConfig->getSgPageTitle();
                break;
                default:
                return static::NOT_HANDLED;
            }
        }

        return static::NOT_HANDLED;
    }
}
