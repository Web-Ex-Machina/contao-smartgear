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

namespace WEM\SmartgearBundle\Backend\Component\Core\ConfigurationStep;

use Contao\FrontendTemplate;
use WEM\SmartgearBundle\Classes\Backend\ConfigurationStep;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\DirectoriesSynchronizer;
use WEM\SmartgearBundle\Classes\UtilFramway;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Config\Framway as FramwayConfig;
use WEM\SmartgearBundle\Config\Manager\Framway as ConfigurationManagerFramway;

class FramwayRetrievalMinimal extends ConfigurationStep
{
    /** @var ConfigurationManager */
    protected $configurationManager;
    /** @var ConfigurationManagerFramway */
    protected $configurationManagerFramway;
    /** @var DirectoriesSynchronizer */
    protected $framwaySynchronizer;
    /** @var DirectoriesSynchronizer */
    protected $templateRSCESynchronizer;
    /** @var DirectoriesSynchronizer */
    protected $templateSmartgearSynchronizer;
    /** @var DirectoriesSynchronizer */
    protected $templateGeneralSynchronizer;
    /** @var DirectoriesSynchronizer */
    protected $tinyMCEPluginsSynchronizer;
    /** @var DirectoriesSynchronizer */
    protected $tarteAuCitronSynchronizer;
    /** @var DirectoriesSynchronizer */
    protected $outdatedBrowserSynchronizer;
    /** @var DirectoriesSynchronizer */
    protected $socialShareButtonsSynchronizer;
    /** @var UtilFramway */
    protected $framwayUtil;
    protected $strTemplate = 'be_wem_sg_install_block_configuration_step_core_framway_retrieval_minimal';

    public function __construct(
        string $module,
        string $type,
        ConfigurationManager $configurationManager,
        ConfigurationManagerFramway $configurationManagerFramway,
        DirectoriesSynchronizer $framwaySynchronizer,
        DirectoriesSynchronizer $templateRSCESynchronizer,
        DirectoriesSynchronizer $templateSmartgearSynchronizer,
        DirectoriesSynchronizer $templateGeneralSynchronizer,
        DirectoriesSynchronizer $tinyMCEPluginsSynchronizer,
        DirectoriesSynchronizer $tarteAuCitronSynchronizer,
        DirectoriesSynchronizer $outdatedBrowserSynchronizer,
        DirectoriesSynchronizer $socialShareButtonsSynchronizer,
        UtilFramway $framwayUtil
    ) {
        parent::__construct($module, $type);
        $this->title = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['FRAMWAYRETRIEVALMINIMAL']['Title'];
        $this->configurationManager = $configurationManager;
        $this->configurationManagerFramway = $configurationManagerFramway;
        $this->framwaySynchronizer = $framwaySynchronizer;
        $this->templateRSCESynchronizer = $templateRSCESynchronizer;
        $this->templateSmartgearSynchronizer = $templateSmartgearSynchronizer;
        $this->templateGeneralSynchronizer = $templateGeneralSynchronizer;
        $this->tinyMCEPluginsSynchronizer = $tinyMCEPluginsSynchronizer;
        $this->tarteAuCitronSynchronizer = $tarteAuCitronSynchronizer;
        $this->outdatedBrowserSynchronizer = $outdatedBrowserSynchronizer;
        $this->socialShareButtonsSynchronizer = $socialShareButtonsSynchronizer;
        $this->framwayUtil = $framwayUtil;
    }

    public function getFilledTemplate(): FrontendTemplate
    {
        // to render the step
        $objTemplate = parent::getFilledTemplate();
        $objTemplate->framway_is_present = $this->checkFramwayPresence();
        // And return the template, parsed.
        return $objTemplate;
    }

    public function isStepValid(): bool
    {
        // check if the step is correct

        return true; //$this->checkFramwayPresence();
    }

    public function do(): void
    {
        // do what is meant to be done in this step
        $this->importFramway();
        $framwayConfig = $this->updateFramwayConfiguration();
        $this->updateCoreConfiguration($framwayConfig->getThemes());

        $this->importRSCETemplates();
        $this->importSmartgearTemplates();
        $this->importGeneralTemplates();
        $this->importTinyMCEPlugins();
        $this->importOutdatedBrowser();
        $this->importTarteAuCitron();
        $this->importSocialShareButtons();
    }

    public function checkFramwayPresence()
    {
        return $this->framwayUtil->checkPresence();
    }

    protected function importFramway(): void
    {
        $this->framwaySynchronizer->synchronize(true);
    }

    protected function updateFramwayConfiguration(): FramwayConfig
    {
        /** @var FramwayConfig */
        $framwayConfig = $this->configurationManagerFramway->load();
        $this->configurationManagerFramway->save($framwayConfig);

        return $framwayConfig;
    }

    /**
     * Update Core configuration.
     *
     * @param array $themes [description]
     */
    protected function updateCoreConfiguration(array $themes): void
    {
        /** @var CoreConfig */
        $coreConfig = $this->configurationManager->load();
        $coreConfig->setSgFramwayThemes($themes);
        $this->configurationManager->save($coreConfig);
    }

    protected function importRSCETemplates(): void
    {
        $this->templateRSCESynchronizer->synchronize(false);
    }

    protected function importSmartgearTemplates(): void
    {
        $this->templateSmartgearSynchronizer->synchronize(false);
    }

    protected function importGeneralTemplates(): void
    {
        $this->templateGeneralSynchronizer->synchronize(false);
    }

    protected function importTinyMCEPlugins(): void
    {
        $this->tinyMCEPluginsSynchronizer->synchronize(false);
    }

    protected function importOutdatedBrowser(): void
    {
        $this->outdatedBrowserSynchronizer->synchronize(true);
    }

    protected function importTarteAuCitron(): void
    {
        $this->tarteAuCitronSynchronizer->synchronize(true);
    }

    protected function importSocialShareButtons(): void
    {
        $this->socialShareButtonsSynchronizer->synchronize(true);
    }
}
