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

namespace WEM\SmartgearBundle\Backend\Dashboard;

use Contao\BackendModule;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Config\Component\Core\Core;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Exceptions\File\NotFound;

class ShortcutExternal extends BackendModule
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'be_wem_sg_dashboard_shortcut_external';
    protected $strId = 'wem_sg_dashboard_shortcut_external';
    /** @var TranslatorInterface */
    protected $translator;
    /** @var ConfigurationManager */
    protected $configurationManager;

    /**
     * Initialize the object.
     */
    public function __construct(
        TranslatorInterface $translator,
        configurationManager $configurationManager
    ) {
        parent::__construct();
        $this->translator = $translator;
        $this->configurationManager = $configurationManager;
    }

    public function generate(): string
    {
        return parent::generate();
    }

    public function compile(): void
    {
        try {
            /** @var CoreConfig */
            $config = $this->configurationManager->load();
        } catch (NotFound $e) {
            return;
        }
        $this->Template->title = $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTEXTERNAL.title', [], 'contao_default');
        // manuals
        $this->Template->manualsUrl = 'https://manuels.smartgear.fr';
        $this->Template->linkManualsText = $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTEXTERNAL.linkManualsText', [], 'contao_default');
        $this->Template->linkManualsTitle = $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTEXTERNAL.linkManualsTitle', [], 'contao_default');

        // demos
        $this->Template->demosUrl = 'https://demos.smartgear.fr';
        $this->Template->linkDemosText = $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTEXTERNAL.linkDemosText', [], 'contao_default');
        $this->Template->linkDemosTitle = $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTEXTERNAL.linkDemosTitle', [], 'contao_default');

        // analytics
        $this->Template->analyticsUrl = '';
        $this->Template->linkAnalyticsText = '';
        $this->Template->linkAnalyticsTitle = '';
        if (Core::ANALYTICS_SYSTEM_NONE !== $config->getSgAnalytics()) {
            switch ($config->getSgAnalytics()) {
                case Core::ANALYTICS_SYSTEM_GOOGLE:
                    $this->Template->analyticsUrl = 'https://analytics.google.com';
                break;
                case Core::ANALYTICS_SYSTEM_MATOMO:
                    $this->Template->analyticsUrl = 'https:'.$config->getSgAnalyticsMatomoHost();
                break;
            }
            $this->Template->linkAnalyticsText = $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTEXTERNAL.linkAnalyticsText', [$config->getSgAnalytics()], 'contao_default');
            $this->Template->linkAnalyticsTitle = $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTEXTERNAL.linkAnalyticsTitle', [$config->getSgAnalytics()], 'contao_default');
        }

        // Google Search Console
        $this->Template->googleSearchConsoleUrl = 'https://search.google.com/search-console';
        $this->Template->linkGoogleSearchConsoleText = $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTEXTERNAL.linkGoogleSearchConsoleText', [], 'contao_default');
        $this->Template->linkGoogleSearchConsoleTitle = $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTEXTERNAL.linkGoogleSearchConsoleTitle', [], 'contao_default');
    }
}
