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
        /** @var ShortcutInternal */
        $modShortcutInternal = System::getContainer()->get('smartgear.backend.dashboard.shortcut_internal');
        $this->Template->shortcutInternal = $modShortcutInternal->generate();
        /** @var ShortcutExternal */
        $modShortcutExternal = System::getContainer()->get('smartgear.backend.dashboard.shortcut_external');
        $this->Template->shortcutExternal = $modShortcutExternal->generate();
        /** @var AnalyticsInternal */
        $modAnalyticsInternal = System::getContainer()->get('smartgear.backend.dashboard.analytics_internal');
        $this->Template->analyticsInternal = $modAnalyticsInternal->generate();
        /** @var AnalyticsExternal */
        $modAnalyticsExternal = System::getContainer()->get('smartgear.backend.dashboard.analytics_external');
        $this->Template->analyticsExternal = $modAnalyticsExternal->generate();
    }
}
