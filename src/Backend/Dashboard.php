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
use Contao\Input;
use Contao\Message;
use Contao\System;
use WEM\SmartgearBundle\Backend\Dashboard\ShortcutInternal;
use WEM\SmartgearBundle\Config\Component\Core as CoreConfig;
use WEM\SmartgearBundle\Exceptions\File\NotFound;

class Dashboard extends BackendModule
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'be_wem_sg_dashboard';
    protected $strId = 'wem_sg_dashboard';

    /**
     * Module basepath.
     *
     * @var string
     */
    protected $strBasePath = 'bundles/wemsmartgear';

    public function __construct($dc = null)
    {
        parent::__construct($dc);

        /* @var ShortcutInternal */
        $this->modShortcutInternal = System::getContainer()->get('smartgear.backend.dashboard.shortcut_internal');
        /* @var ShortcutExternal */
        $this->modShortcutExternal = System::getContainer()->get('smartgear.backend.dashboard.shortcut_external');
        /* @var AnalyticsInternal */
        $this->modAnalyticsInternal = System::getContainer()->get('smartgear.backend.dashboard.analytics_internal');
        /* @var AnalyticsExternal */
        $this->modAnalyticsExternal = System::getContainer()->get('smartgear.backend.dashboard.analytics_external');
        /* @var Support */
        $this->modSupport = System::getContainer()->get('smartgear.backend.dashboard.support');
    }

    public function generate(): string
    {
        // Add WEM styles to template
        $GLOBALS['TL_CSS'][] = $this->strBasePath.'/backend/wemsg.css';

        return parent::generate();
    }

    public function compile(): void
    {
        $configurationManager = System::getContainer()->get('smartgear.config.manager.core');
        try {
            /** @var CoreConfig */
            $config = $configurationManager->load();
        } catch (NotFound $e) {
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
     * @param [String] $strAction - Ajax action wanted
     *
     * @return string - Ajax response, as String or JSON
     */
    public function processAjaxRequest($strAction)
    {
        if (Input::post('TL_WEM_AJAX')) {
            switch (Input::post('wem_module')) {
                case $this->modSupport->getStrId():
                    $this->modSupport->processAjaxRequest(Input::post('action'));
                break;
            }
        }
    }
}
