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
use Contao\System;
use WEM\SmartgearBundle\Backend\Dashboard\ShortcutInternal;

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
        $this->Template->title = 'ciou';
        /** @var ShortcutInternal */
        $modShortcutInternal = System::getContainer()->get('smartgear.backend.dashboard.shortcut_internal');
        $this->Template->shortcutInternal = $modShortcutInternal->generate();
        /** @var ShortcutExternal */
        $modShortcutExternal = System::getContainer()->get('smartgear.backend.dashboard.shortcut_external');
        $this->Template->shortcutExternal = $modShortcutExternal->generate();
    }
}
