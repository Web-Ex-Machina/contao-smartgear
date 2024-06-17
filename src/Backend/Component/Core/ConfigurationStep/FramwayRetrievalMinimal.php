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
use WEM\SmartgearBundle\Classes\Analyzer\Htaccess as HtaccessAnalyzer;
use WEM\SmartgearBundle\Classes\Backend\ConfigurationStep;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\DirectoriesSynchronizer;
use WEM\SmartgearBundle\Classes\UtilFramway;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Config\Framway as FramwayConfig;
use WEM\SmartgearBundle\Config\Manager\Framway as ConfigurationManagerFramway;

class FramwayRetrievalMinimal extends ConfigurationStep
{

    protected string $strTemplate = 'be_wem_sg_install_block_configuration_step_core_framway_retrieval_minimal';

    public function __construct(
        string                                $module,
        string                                $type,
        protected ConfigurationManager        $configurationManager,
        protected ConfigurationManagerFramway $configurationManagerFramway,
        protected DirectoriesSynchronizer     $framwaySynchronizer,
        protected DirectoriesSynchronizer     $templateRSCESynchronizer,
        protected DirectoriesSynchronizer     $templateSmartgearSynchronizer,
        protected DirectoriesSynchronizer     $templateGeneralSynchronizer,
        protected DirectoriesSynchronizer     $tinyMCEPluginsSynchronizer,
        protected DirectoriesSynchronizer     $tarteAuCitronSynchronizer,
        protected DirectoriesSynchronizer     $outdatedBrowserSynchronizer,
        protected DirectoriesSynchronizer     $socialShareButtonsSynchronizer,
        protected UtilFramway                 $framwayUtil,
        protected HtaccessAnalyzer            $htaccessAnalyzer
    ) {
        parent::__construct($module, $type);
        $this->title = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['FRAMWAYRETRIEVALMINIMAL']['Title'];
    }

    public function getFilledTemplate(): FrontendTemplate
    {
        // to render the step
        $objTemplate = parent::getFilledTemplate();
        $objTemplate->framway_is_present = $this->checkFramwayPresence();

        $arrFilesToCheck = $this->framwayUtil->getFilesToCheck();
        foreach ($arrFilesToCheck as $key => $filetoCheck) {
            $arrFilesToCheck[$key] = $this->framwayUtil->getFramwayPath().\DIRECTORY_SEPARATOR.$filetoCheck;
        }

        $objTemplate->filesToCheck = $arrFilesToCheck;
        // And return the template, parsed.
        return $objTemplate;
    }

    public function isStepValid(): bool
    {
        // check if the step is correct

        return true; //$this->checkFramwayPresence();
    }

    /**
     * @throws \Exception
     */
    public function do(): void
    {
        // do what is meant to be done in this step
        if (!$this->checkFramwayPresence()) {
            $this->importFramway();
        }

        $framwayConfig = $this->updateFramwayConfiguration();
        $this->updateCoreConfiguration($framwayConfig->getThemes());

        $this->importRSCETemplates();
        $this->importSmartgearTemplates();
        $this->importGeneralTemplates();
        $this->importTinyMCEPlugins();
        $this->importOutdatedBrowser();
        $this->importTarteAuCitron();
        $this->importSocialShareButtons();
        $this->enableFramwayAssetsManagementRules();
    }

    public function checkFramwayPresence(): bool
    {
        return $this->framwayUtil->checkPresence();
    }

    /**
     * @throws \Exception
     */
    protected function importFramway(): void
    {
        $this->framwaySynchronizer->synchronize(true);
    }

    protected function updateFramwayConfiguration(): FramwayConfig
    {
        /** @var FramwayConfig $framwayConfig */
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
        /** @var CoreConfig $coreConfig */
        $coreConfig = $this->configurationManager->load();
        $coreConfig->setSgFramwayThemes($themes);

        $this->configurationManager->save($coreConfig);
    }

    /**
     * @throws \Exception
     */
    protected function importRSCETemplates(): void
    {
        $this->templateRSCESynchronizer->synchronize(false);
    }

    /**
     * @throws \Exception
     */
    protected function importSmartgearTemplates(): void
    {
        $this->templateSmartgearSynchronizer->synchronize(false);
    }

    /**
     * @throws \Exception
     */
    protected function importGeneralTemplates(): void
    {
        $this->templateGeneralSynchronizer->synchronize(false);
    }

    /**
     * @throws \Exception
     */
    protected function importTinyMCEPlugins(): void
    {
        $this->tinyMCEPluginsSynchronizer->synchronize(false);
    }

    /**
     * @throws \Exception
     */
    protected function importOutdatedBrowser(): void
    {
        $this->outdatedBrowserSynchronizer->synchronize(true);
    }

    /**
     * @throws \Exception
     */
    protected function importTarteAuCitron(): void
    {
        $this->tarteAuCitronSynchronizer->synchronize(true);
    }

    /**
     * @throws \Exception
     */
    protected function importSocialShareButtons(): void
    {
        $this->socialShareButtonsSynchronizer->synchronize(true);
    }

    protected function enableFramwayAssetsManagementRules(): void
    {
        $this->htaccessAnalyzer->enableFramwayAssetsManagementRules();
    }
}
