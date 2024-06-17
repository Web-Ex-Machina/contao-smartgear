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

use Contao\Input;
use WEM\SmartgearBundle\Classes\Backend\ConfigurationStep;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\DirectoriesSynchronizer;
use WEM\SmartgearBundle\Classes\UtilFramway;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Config\Framway as FramwayConfig;
use WEM\SmartgearBundle\Config\Manager\Framway as ConfigurationManagerFramway;
use WEM\SmartgearBundle\Exceptions\File\NotFound;

class FramwayConfiguration extends ConfigurationStep
{

    protected string $strTemplate = 'be_wem_sg_install_block_configuration_step_core_framway_configuration';

    public function __construct(
        string                                             $module,
        string                                             $type,
        protected ConfigurationManager                     $configurationManager,
        protected ConfigurationManagerFramway              $configurationManagerFramway,
        protected DirectoriesSynchronizer                  $templateRSCESynchronizer,
        protected DirectoriesSynchronizer                  $templateSmartgearSynchronizer,
        protected DirectoriesSynchronizer                  $templateGeneralSynchronizer,
        protected DirectoriesSynchronizer                  $tinyMCEPluginsSynchronizer,
        protected DirectoriesSynchronizer                  $tarteAuCitronSynchronizer,
        protected DirectoriesSynchronizer                  $outdatedBrowserSynchronizer,
        protected DirectoriesSynchronizer                  $socialShareButtonsSynchronizer,
        protected UtilFramway                              $framwayUtil
    ) {
        parent::__construct($module, $type);
        $this->title = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['FRAMWAYCONFIGURATION']['Title'];
        try {
            /** @var CoreConfig $config */
            $config = $this->configurationManager->load();
            /** @var FramwayConfig $framwayConfig */
            $framwayConfig = $this->configurationManagerFramway->load();

            $themesOptions = [];
            foreach ($framwayConfig->getThemesAvailables() as $theme) {
                $themesOptions[] = ['value' => $theme, 'label' => $theme];
            }

            $componentsOptions = [];
            foreach ($framwayConfig->getComponentsAvailables() as $component) {
                $componentsOptions[] = ['value' => $component, 'label' => $component];
            }

            $this->addSelectField('themes[]', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['FRAMWAYCONFIGURATION']['FieldThemes'], $themesOptions, $framwayConfig->getThemes(), true, true);
            $this->addSelectField('themesAvailables[]', '', $themesOptions, $framwayConfig->getThemesAvailables(), true, true, 'hidden');
            $this->addSelectField('components[]', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['FRAMWAYCONFIGURATION']['FieldComponents'], $componentsOptions, $framwayConfig->getComponents(), true, true);
            $this->addSelectField('componentsAvailables[]', '', $componentsOptions, $framwayConfig->getComponentsAvailables(), true, true, 'hidden');
            $this->addTextField('new_theme', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['FRAMWAYCONFIGURATION']['FieldNewTheme'], '', false, 'hidden', 'text');
        } catch (NotFound) {
        }
    }

    public function isStepValid(): bool
    {
        // check if the step is correct
        if (empty(Input::post('themes')) || empty(Input::post('components'))) {
            return false;
        }
        return true;
    }

    /**
     * @throws \Exception
     */
    public function do(): void
    {
        // do what is meant to be done in this step
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $this->updateFramwayConfiguration(
            Input::post('themes') ?? [],
            Input::post('components'),
            Input::post('themesAvailables'),
            Input::post('componentsAvailables'),
        );
        $this->updateCoreConfiguration(Input::post('themes') ?? []);

        // no build needed anymore
        // $this->framwayUtil->build();

        $this->importRSCETemplates();
        $this->importSmartgearTemplates();
        $this->importGeneralTemplates();
        $this->importTinyMCEPlugins();
        $this->importOutdatedBrowser();
        $this->importTarteAuCitron();
        $this->importSocialShareButtons();
    }

    public function framwayThemeAdd(): string
    {
        if (empty(Input::post('new_theme'))) {
            throw new \InvalidArgumentException($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['FRAMWAYCONFIGURATION']['FieldNewThemeEmpty']);
        }

        $theme = Input::post('new_theme');

        if (!preg_match($this->framwayUtil::THEME_NAME_REGEXP, $theme)) {
            throw new \InvalidArgumentException($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['FRAMWAYCONFIGURATION']['FieldNewThemeIncorrectFormat']);
        }

        return $this->framwayUtil->addTheme($theme);
    }

    /**
     * Update Framway configuration.
     */
    protected function updateFramwayConfiguration(array $themes, array $components, array $themesAvailables, array $componentsAvailables): FramwayConfig
    {
        /** @var FramwayConfig $framwayConfig */
        $framwayConfig = $this->configurationManagerFramway->load();
        $framwayConfig->setThemes($themes);
        $framwayConfig->setThemesAvailables($themesAvailables);
        $framwayConfig->setComponents($components);
        $framwayConfig->setComponentsAvailables($componentsAvailables);

        $this->configurationManagerFramway->save($framwayConfig);

        return $framwayConfig;
    }

    /**
     * Update Core configuration.
     *
     * @param array $themes [description]
     */
    protected function updateCoreConfiguration(array $themes): CoreConfig
    {
        /** @var CoreConfig $coreConfif */
        $coreConfif = $this->configurationManager->load();
        $coreConfif->setSgFramwayThemes($themes);

        $this->configurationManager->save($coreConfif);

        return $coreConfif;
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
}
