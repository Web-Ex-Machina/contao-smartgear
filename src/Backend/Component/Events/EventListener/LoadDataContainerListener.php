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

namespace WEM\SmartgearBundle\Backend\Component\Events\EventListener;

use Symfony\Component\Security\Core\Security;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\Dca\Manipulator as DCAManipulator;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\DataContainer\CalendarEvents as CalendarEventsDCA;
use WEM\SmartgearBundle\Exceptions\File\NotFound as FileNotFoundException;
use WEM\SmartgearBundle\Security\SmartgearPermissions;

class LoadDataContainerListener
{



    /** @var string */
    protected string $do;

    public function __construct(protected Security $security, protected CoreConfigurationManager $coreConfigurationManager, protected DCAManipulator $dcaManipulator)
    {
    }

    public function __invoke(string $table): void
    {
        try {
            /** @var CoreConfig $config */
            // $config = $this->coreConfigurationManager->load();
            $this->dcaManipulator->setTable($table);
            switch ($table) {
                case 'tl_calendar_events':
                    // $eventsConfig = $config->getSgEvents();
                    // if (!$eventsConfig->getSgInstallComplete()) {
                    //     return;
                    // }
                    // limiting singleSRC field to the event folder
                    // $this->dcaManipulator->setFieldSingleSRCPath($eventsConfig->getSgEventsFolder());

                    if (!$this->security->isGranted('contao_user.smartgear_permissions', SmartgearPermissions::CORE_EXPERT)
                    && !$this->security->isGranted('contao_user.smartgear_permissions', SmartgearPermissions::EVENTS_EXPERT)
                    ) {
                        //get rid of all unnecessary actions.
                        $this->dcaManipulator->removeListOperationsEdit();
                        //get rid of all unnecessary fields
                        $fieldsKeyToKeep = ['headline', 'title', 'alias', 'author', 'addTime', 'startTime', 'endTime', 'startDate', 'endDate', 'pageTitle', 'description', 'location', 'address', 'teaser', 'addImage', 'singleSRC', 'recurring', 'repeatEach', 'repeatEnd', 'recurrences', 'source', 'jumpTo', 'published', 'start', 'stop'];
                        $this->dcaManipulator->removeOtherFields($fieldsKeyToKeep);

                        // update fields
                        $this->dcaManipulator->setFieldAliasReadonly(true);
                        $GLOBALS['TL_LANG'][$table]['teaser_legend'] = &$GLOBALS['TL_LANG']['WEMSG']['EVENTS']['FORM']['paletteTeaserLabel'];
                        $GLOBALS['TL_LANG'][$table]['teaser'][0] = &$GLOBALS['TL_LANG']['WEMSG']['EVENTS']['FORM']['fieldTeaserLabel'];
                        $GLOBALS['TL_LANG'][$table]['teaser'][1] = &$GLOBALS['TL_LANG']['WEMSG']['EVENTS']['FORM']['fieldTeaserHelp'];
                        $this->dcaManipulator->setFieldSourceOptionCallback(CalendarEventsDCA::class, 'getSourceOptions');
                    }

                    $this->dcaManipulator->addFieldSaveCallback('headline', static fn($varValue, \Contao\DataContainer $objDc) => (new \WEM\SmartgearBundle\DataContainer\Content())->cleanHeadline($varValue, $objDc));
                    $this->dcaManipulator->addFieldSaveCallback('title', static fn($varValue, \Contao\DataContainer $objDc) => (new \WEM\SmartgearBundle\DataContainer\Content())->cleanHeadline($varValue, $objDc));
                    $this->dcaManipulator->addFieldSaveCallback('teaser', static fn($varValue, \Contao\DataContainer $objDc) => (new \WEM\SmartgearBundle\DataContainer\Content())->cleanText($varValue, $objDc));
                    $this->dcaManipulator->addFieldSaveCallback('description', static fn($varValue, \Contao\DataContainer $objDc) => (new \WEM\SmartgearBundle\DataContainer\Content())->cleanText($varValue, $objDc));
                break;
                case 'tl_content':
                    if ('calendar' !== $this->do) {
                        return;
                    }

                    // $eventsConfig = $config->getSgEvents();
                    // if (!$eventsConfig->getSgInstallComplete()) {
                    //     return;
                    // }
                    // // limiting singleSRC field to the event folder
                    // $this->dcaManipulator->setFieldSingleSRCPath($eventsConfig->getSgEventsFolder());
                break;
            }
        } catch (FileNotFoundException) {
            //nothing
        }
    }

    public function setDo(string $do): self
    {
        $this->do = $do;

        return $this;
    }
}
