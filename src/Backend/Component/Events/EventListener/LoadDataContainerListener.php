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

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Symfony\Component\Security\Core\Security;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Config\Core as CoreConfig;
use WEM\SmartgearBundle\Exceptions\File\NotFound as FileNotFoundException;
use WEM\SmartgearBundle\Security\SmartgearPermissions;

class LoadDataContainerListener
{
    /** @var Security */
    protected $security;
    /** @var CoreConfigurationManager */
    protected $coreConfigurationManager;

    public function __construct(
        Security $security,
        CoreConfigurationManager $coreConfigurationManager
    ) {
        $this->security = $security;
        $this->coreConfigurationManager = $coreConfigurationManager;
    }

    public function __invoke(string $table): void
    {
        try {
            /** @var CoreConfig */
            $config = $this->coreConfigurationManager->load();
            switch ($table) {
                case 'tl_calendar_events':
                    $eventsConfig = $config->getSgEvents();
                    if (!$eventsConfig->getSgInstallComplete()) {
                        return;
                    }
                    // limiting singleSRC fierld to the blog folder
                    $GLOBALS['TL_DCA'][$table]['fields']['singleSRC']['eval']['path'] = $eventsConfig->getSgEventsFolder();

                    if (!$this->security->isGranted(SmartgearPermissions::CORE_EXPERT)
                    && !$this->security->isGranted(SmartgearPermissions::EVENTS_EXPERT)
                    ) {
                        //get rid of all unnecessary actions.
                        unset($GLOBALS['TL_DCA'][$table]['list']['operations']['edit']);
                        //get rid of all unnecessary fields
                        $fieldsKeyToKeep = ['headline', 'title', 'alias', 'author', 'addTime', 'startTime', 'endTime', 'startDate', 'endDate', 'pageTitle', 'description', 'location', 'address', 'teaser', 'addImage', 'singleSRC', 'recurring', 'repeatEach', 'repeatEnd', 'recurrences', 'source', 'jumpTo', 'published', 'start', 'stop'];

                        $fieldsKeyToRemove = array_diff(array_keys($GLOBALS['TL_DCA'][$table]['fields']), $fieldsKeyToKeep);
                        $palettesNames = array_keys($GLOBALS['TL_DCA'][$table]['palettes']);
                        $subpalettesNames = array_keys($GLOBALS['TL_DCA'][$table]['subpalettes']);
                        $pm = PaletteManipulator::create();
                        foreach ($fieldsKeyToRemove as $field) {
                            $pm->removeField($field);
                        }
                        foreach ($palettesNames as $paletteName) {
                            if (!\is_array($GLOBALS['TL_DCA'][$table]['palettes'][$paletteName])) {
                                $pm->applyToPalette($paletteName, $table);
                            }
                        }
                        foreach ($subpalettesNames as $subpaletteName) {
                            if (!\is_array($GLOBALS['TL_DCA'][$table]['subpalettes'][$subpaletteName])) {
                                $pm->applyToSubpalette($subpaletteName, $table);
                            }
                        }

                        // update fields
                        $GLOBALS['TL_LANG'][$table]['teaser_legend'] = &$GLOBALS['TL_LANG']['WEMSG']['EVENTS']['FORM']['paletteTeaserLabel'];
                        $GLOBALS['TL_LANG'][$table]['teaser'][0] = &$GLOBALS['TL_LANG']['WEMSG']['EVENTS']['FORM']['fieldTeaserLabel'];
                        $GLOBALS['TL_LANG'][$table]['teaser'][1] = &$GLOBALS['TL_LANG']['WEMSG']['EVENTS']['FORM']['fieldTeaserHelp'];
                        $GLOBALS['TL_DCA'][$table]['fields']['alias']['eval']['readonly'] = true;
                        $GLOBALS['TL_DCA'][$table]['fields']['source']['options_callback'] = ['tl_wem_sg_calendar_events', 'getSourceOptions'];
                    }
                break;
            }
        } catch (FileNotFoundException $e) {
            //nothing
        }
    }
}