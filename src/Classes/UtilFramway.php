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

namespace WEM\SmartgearBundle\Classes;

use WEM\SmartgearBundle\Classes\Command\Util as CommandUtil;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Config\Manager\FramwayCombined as ConfigurationCombinedManager;
use WEM\SmartgearBundle\Config\Manager\FramwayTheme as ConfigurationThemeManager;

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2022 Web ex Machina.
 *
 * @category ContaoBundle
 *
 * @author   Web ex Machina <contact@webexmachina.fr>
 *
 * @see     https://github.com/Web-Ex-Machina/contao-smartgear/
 */
class UtilFramway
{
    public const THEME_NAME_REGEXP = '/^([A-Za-z0-9-_:@.\/]+)$/';
    public const SCRIPTS_PATH = './bundles/wemsmartgear/scripts/smartgear/component/core/';
    /** @var ConfigurationManager */
    protected $configurationManager;
    /** @var ConfigurationThemeManager */
    protected $configurationThemeManager;
    /** @var ConfigurationCombinedManager */
    protected $configurationCombinedManager;
    /** @var CommandUtil */
    protected $commandUtil;

    public function __construct(
        ConfigurationManager $configurationManager,
        CommandUtil $commandUtil,
        ConfigurationThemeManager $configurationThemeManager,
        ConfigurationCombinedManager $configurationCombinedManager
    ) {
        $this->commandUtil = $commandUtil;
        $this->configurationManager = $configurationManager;
        $this->configurationThemeManager = $configurationThemeManager;
        $this->configurationCombinedManager = $configurationCombinedManager;
    }

    public function getThemeColors(?string $themeName = null): array
    {
        return $this->configurationThemeManager->setThemeName($themeName)->load()->getColors();
    }

    public function getCombinedColors(): array
    {
        return $this->configurationCombinedManager->load()->getColors();
    }

    public function retrieve(bool $live = false)
    {
        set_time_limit(0);
        if ($live) {
            return $this->commandUtil->executeCmdLive('sh '.self::SCRIPTS_PATH.'framway_retrieve.sh ./'.$this->getFramwayPath());
        }

        return $this->commandUtil->executeCmd('sh '.self::SCRIPTS_PATH.'framway_retrieve.sh ./'.$this->getFramwayPath());
    }

    public function install(bool $live = false)
    {
        set_time_limit(0);

        if ($live) {
            return $this->commandUtil->executeCmdLive('sh '.self::SCRIPTS_PATH.'framway_install.sh ./'.$this->getFramwayPath());
        }

        return $this->commandUtil->executeCmd('sh '.self::SCRIPTS_PATH.'framway_install.sh ./'.$this->getFramwayPath());
    }

    public function initialize(bool $live = false)
    {
        set_time_limit(0);

        if ($live) {
            return $this->commandUtil->executeCmdLive('sh '.self::SCRIPTS_PATH.'framway_initialize.sh ./'.$this->getFramwayPath());
        }

        return $this->commandUtil->executeCmd('sh '.self::SCRIPTS_PATH.'framway_initialize.sh ./'.$this->getFramwayPath());
    }

    public function build(bool $live = false)
    {
        set_time_limit(0);

        if ($live) {
            return $this->commandUtil->executeCmdLive('sh '.self::SCRIPTS_PATH.'framway_build.sh ./'.$this->getFramwayPath());
        }

        return $this->commandUtil->executeCmd('sh '.self::SCRIPTS_PATH.'framway_build.sh ./'.$this->getFramwayPath());
    }

    public function checkPresence()
    {
        return file_exists($this->getFramwayPath().\DIRECTORY_SEPARATOR.'framway.config.js') && file_exists($this->getFramwayPath().\DIRECTORY_SEPARATOR.'build');
    }

    public function addTheme(string $themeName, bool $live = false)
    {
        $this->checkThemeName($themeName);

        if ($live) {
            return $this->commandUtil->executeCmdLive('sh '.self::SCRIPTS_PATH.'framway_theme_add.sh ./'.$this->getFramwayPath());
        }

        return $this->commandUtil->executeCmd('sh '.self::SCRIPTS_PATH.'framway_theme_add.sh ./'.$this->getFramwayPath().' '.$themeName);
    }

    public function checkThemeName(string $themeName): void
    {
        if (!preg_match(self::THEME_NAME_REGEXP, $themeName)) {
            throw new \InvalidArgumentException('New theme\'s name is incorrect ! Authorized characters are : letters, numbers, middlescores ("-"),underscores ("_"), dots ("."), semicolon (":"), slashes ("/"") and arobase ("@").');
        }
    }

    public function getAvailableThemes(): array
    {
        $arrThemes = [];
        if ($handle = opendir($this->getFramwayPath().\DIRECTORY_SEPARATOR.'src/themes')) {
            while (false !== ($entry = readdir($handle))) {
                if ('.' !== $entry && '..' !== $entry) {
                    $arrThemes[] = ['label' => $entry, 'value' => $entry];
                }
            }
            closedir($handle);
        }

        return $arrThemes;
    }

    public function getAvailableComponents(): array
    {
        $arrComponents = [];
        if ($handle = opendir($this->getFramwayPath().\DIRECTORY_SEPARATOR.'src/components')) {
            while (false !== ($entry = readdir($handle))) {
                if ('.' !== $entry && '..' !== $entry) {
                    $arrComponents[] = ['label' => $entry, 'value' => $entry];
                }
            }
            closedir($handle);
        }

        return $arrComponents;
    }

    protected function getFramwayPath(): string
    {
        return $this->configurationManager->load()->getSgFramwayPath();
    }
}
