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
    /** @var ConfigurationManager */
    protected $configurationManager;
    /** @var ConfigurationManagerFramway */
    protected $configurationManagerFramway;
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
    protected $strTemplate = 'be_wem_sg_install_block_configuration_step_core_framway_configuration';

    public function __construct(
        string $module,
        string $type,
        ConfigurationManager $configurationManager,
        ConfigurationManagerFramway $configurationManagerFramway,
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
        $this->title = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['FRAMWAYCONFIGURATION']['Title'];
        $this->configurationManager = $configurationManager;
        $this->configurationManagerFramway = $configurationManagerFramway;
        $this->templateRSCESynchronizer = $templateRSCESynchronizer;
        $this->templateSmartgearSynchronizer = $templateSmartgearSynchronizer;
        $this->templateGeneralSynchronizer = $templateGeneralSynchronizer;
        $this->tinyMCEPluginsSynchronizer = $tinyMCEPluginsSynchronizer;
        $this->tarteAuCitronSynchronizer = $tarteAuCitronSynchronizer;
        $this->outdatedBrowserSynchronizer = $outdatedBrowserSynchronizer;
        $this->socialShareButtonsSynchronizer = $socialShareButtonsSynchronizer;
        $this->framwayUtil = $framwayUtil;
        try {
            /** @var CoreConfig */
            $config = $this->configurationManager->load();
            /** @var FramwayConfig */
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
        } catch (NotFound $e) {
        }
    }

    public function isStepValid(): bool
    {
        // check if the step is correct
        if (empty(Input::post('themes'))) {
            return false;
        }
        if (empty(Input::post('components'))) {
            return false;
        }

        return true;
    }

    public function do(): void
    {
        // do what is meant to be done in this step
        /** @var CoreConfig */
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

    public function framwayThemeAdd()
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
     *
     * @param array $themes               [description]
     * @param array $components           [description]
     * @param array $themesAvailables     [description]
     * @param array $componentsAvailables [description]
     */
    protected function updateFramwayConfiguration(array $themes, array $components, array $themesAvailables, array $componentsAvailables): FramwayConfig
    {
        /** @var FramwayConfig */
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
        /** @var CoreConfig */
        $coreConfif = $this->configurationManager->load();
        $coreConfif->setSgFramwayThemes($themes);
        $this->configurationManager->save($coreConfif);

        return $coreConfif;
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
