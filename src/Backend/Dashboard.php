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

namespace WEM\SmartgearBundle\Backend;

use Contao\BackendModule;
use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\DataContainer;
use Contao\Input;
use Contao\Message;
use Contao\System;
use WEM\SmartgearBundle\Api\Airtable\V0\Api as AirtableApi;
use WEM\SmartgearBundle\Backend\Dashboard\AnalyticsExternal;
use WEM\SmartgearBundle\Backend\Dashboard\AnalyticsInternal;
use WEM\SmartgearBundle\Backend\Dashboard\ShortcutExternal;
use WEM\SmartgearBundle\Backend\Dashboard\ShortcutInternal;
use WEM\SmartgearBundle\Backend\Dashboard\Support;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Config\Component\Core as CoreConfig;
use WEM\SmartgearBundle\Exceptions\File\NotFound;
use WEM\UtilsBundle\Classes\ScopeMatcher;

#[AsHook('executePreActions','processAjaxRequest',-1)]
class Dashboard extends BackendModule
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'be_wem_sg_dashboard';

    protected string $strId = 'wem_sg_dashboard';

    /**
     * Module basepath.
     */
    protected string $strBasePath = 'bundles/wemsmartgear';

    public function __construct(protected readonly ScopeMatcher $scopeMatcher, DataContainer|null $dc = null)
    {
        parent::__construct($dc);

        $configurationManager = System::getContainer()->get('smartgear.config.manager.core');

        try {
            /** @var CoreConfig $config */
            $config = $configurationManager->load();

            /** @var AirtableApi $airtableApi */
            $airtableApi = System::getContainer()->get('smartgear.api.airtable.v0.api');

            $arrDomains = Util::getRootPagesDomains();
            $hostingInformations = $airtableApi->getHostingInformations($arrDomains);
            if (!empty($hostingInformations)) {
                $clientsRef = Util::getAirtableClientsRef($hostingInformations);
                $airtableApi->getSupportClientInformations($clientsRef);
            }
        } catch (NotFound) {
        }

        /* @var ShortcutInternal $this->modShortcutInternal */
        $this->modShortcutInternal = System::getContainer()->get('smartgear.backend.dashboard.shortcut_internal');
        /* @var ShortcutExternal $this->modShortcutExternal */
        $this->modShortcutExternal = System::getContainer()->get('smartgear.backend.dashboard.shortcut_external');
        /* @var AnalyticsInternal $this->modAnalyticsInternal */
        $this->modAnalyticsInternal = System::getContainer()->get('smartgear.backend.dashboard.analytics_internal');
        /* @var AnalyticsExternal $this->modAnalyticsExternal */
        $this->modAnalyticsExternal = System::getContainer()->get('smartgear.backend.dashboard.analytics_external');
        /* @var Support $this->modSupport */
        $this->modSupport = System::getContainer()->get('smartgear.backend.dashboard.support');
    }

    public function generate(): string
    {
        // Add WEM styles to template
        $GLOBALS['TL_CSS'][] = $this->strBasePath.'/backend/wemsg.css';

        return parent::generate();
    }

    protected function compile(): void
    {
        $configurationManager = System::getContainer()->get('smartgear.config.manager.core');
        try {
            /** @var CoreConfig $config */
            $config = $configurationManager->load();
        } catch (NotFound) {
            return;
        }

        if (!$config->getSgInstallComplete()) {
            Message::add($GLOBALS['TL_LANG']['WEMSG']['DASHBOARD']['smartgearNotInstalled'], 'TL_ERROR');

            return;
        }

        $this->Template->title = 'ciou';
        $this->Template->shortcutInternal = $this->modShortcutInternal->generate();
        $this->Template->shortcutExternal = $this->modShortcutExternal->generate();
        $this->Template->analyticsInternal = $this->modAnalyticsInternal->generate();
        $this->Template->analyticsExternal = $this->modAnalyticsExternal->generate();
        $this->Template->support = $this->modSupport->generate();
    }

    /**
     * Process AJAX actions.
     *
     * @param string $strAction - Ajax action wanted
     */
    public function processAjaxRequest(string $strAction): void
    {
        if(!$this->scopeMatcher->isFrontend()) {exit();}

        if (Input::post('TL_WEM_AJAX') && Input::post('wem_module') === $this->modSupport->getStrId()) {
            $this->modSupport->processAjaxRequest(Input::post('action'));
        }
    }
}
