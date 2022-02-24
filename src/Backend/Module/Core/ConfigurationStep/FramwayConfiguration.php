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

namespace WEM\SmartgearBundle\Backend\Module\Core\ConfigurationStep;

use Contao\Input;
use WEM\SmartgearBundle\Classes\Backend\ConfigurationStep;
use WEM\SmartgearBundle\Classes\Config\Manager as ConfigurationManager;
use WEM\SmartgearBundle\Classes\DirectoriesSynchronizer;
use WEM\SmartgearBundle\Classes\UtilFramway;
use WEM\SmartgearBundle\Config\Core as CoreConfig;
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
    protected $templateSynchronizer;
    /** @var DirectoriesSynchronizer */
    protected $tinyMCEPluginsSynchronizer;
    protected $strTemplate = 'be_wem_sg_install_block_configuration_step_core_framway_configuration';

    public function __construct(
        string $module,
        string $type,
        ConfigurationManager $configurationManager,
        ConfigurationManagerFramway $configurationManagerFramway,
        DirectoriesSynchronizer $templateSynchronizer,
        DirectoriesSynchronizer $tinyMCEPluginsSynchronizer
    ) {
        parent::__construct($module, $type);
        $this->title = 'Framway | Configuration';
        $this->configurationManager = $configurationManager;
        $this->configurationManagerFramway = $configurationManagerFramway;
        $this->templateSynchronizer = $templateSynchronizer;
        $this->tinyMCEPluginsSynchronizer = $tinyMCEPluginsSynchronizer;
        try {
            /** @var CoreConfig */
            $config = $this->configurationManager->load();
            /** @var FramwayConfig */
            $framwayConfig = $this->configurationManagerFramway->load();

            $arrThemes = [];
            $arrComponents = [];

            if ($handle = opendir($config->getSgFramwayPath().\DIRECTORY_SEPARATOR.'src/themes')) {
                while (false !== ($entry = readdir($handle))) {
                    if ('.' !== $entry && '..' !== $entry) {
                        $arrThemes[] = ['label' => $entry, 'value' => $entry];
                    }
                }
                closedir($handle);
            }

            if ($handle = opendir($config->getSgFramwayPath().\DIRECTORY_SEPARATOR.'src/components')) {
                while (false !== ($entry = readdir($handle))) {
                    if ('.' !== $entry && '..' !== $entry) {
                        $arrComponents[] = ['label' => $entry, 'value' => $entry];
                    }
                }
                closedir($handle);
            }

            $arrFontAwesome = [
                [
                    'label' => 'None', 'value' => FramwayConfig::USE_FA_NONE,
                ],
                [
                    'label' => 'kit Smartgear', 'value' => FramwayConfig::USE_FA_FREE,
                ],
                [
                    'label' => 'all', 'value' => FramwayConfig::USE_FA_PRO,
                ],
            ];

            $this->addSelectField('themes[]', 'Thèmes', $arrThemes, $framwayConfig->getThemes(), true, true);
            $this->addSelectField('components[]', 'Composants', $arrComponents, $framwayConfig->getComponents(), true, true);
            $this->addSelectField('fontawesome', 'Configuration Font-Awesome', $arrFontAwesome, $framwayConfig->getUseFA(), false);
            $this->addTextField('new_theme', 'Nouveau thème', '', false, 'hidden', 'text');
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
        $fa = Input::post('fontawesome');
        if (empty($fa)) {
            $fa = FramwayConfig::USE_FA_NONE;
        }
        if (!\in_array($fa, FramwayConfig::USE_FA_ALLOWED, true)) {
            return false;
        }

        return true;
    }

    public function do(): void
    {
        // do what is meant to be done in this step
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        $fa = Input::post('fontawesome');
        if (empty($fa)) {
            $fa = FramwayConfig::USE_FA_NONE;
        }

        $this->updateFramwayConfiguration(Input::post('themes') ?? [], Input::post('components'), $fa);
        $this->updateCoreConfiguration(Input::post('themes') ?? []);

        UtilFramway::build($config->getSgFramwayPath());

        $this->importRSCETemplates();
        $this->importTinyMCEPlugins();
    }

    public function framwayThemeAdd()
    {
        if (empty(Input::post('new_theme'))) {
            throw new \InvalidArgumentException('Le nom du nouveau thème est vide');
        }

        $theme = Input::post('new_theme');

        if (!preg_match('/^([A-Za-z0-9-_]+)$/', $theme)) {
            throw new \InvalidArgumentException('Le nom du nouveau thème est invalide! Les caractères autorisés sont : lettres, chiffres, tirets ("-") et underscores ("_").');
        }

        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        return UtilFramway::addTheme($config->getSgFramwayPath(), $theme);
    }

    /**
     * Update Framway configuration.
     *
     * @param array       $themes      [description]
     * @param array       $components  [description]
     * @param string|bool $fontawesome [description]
     */
    protected function updateFramwayConfiguration(array $themes, array $components, $fontawesome): FramwayConfig
    {
        /** @var FramwayConfig */
        $framwayConfig = $this->configurationManagerFramway->load();
        $framwayConfig->setThemes($themes);
        $framwayConfig->setComponents($components);
        $framwayConfig->setUseFA($fontawesome);
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
        $this->templateSynchronizer->synchronize(false);
    }

    protected function importTinyMCEPlugins(): void
    {
        $this->tinyMCEPluginsSynchronizer->synchronize(false);
    }
}
