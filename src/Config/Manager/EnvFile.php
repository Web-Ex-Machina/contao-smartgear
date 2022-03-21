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

namespace WEM\SmartgearBundle\Config\Manager;

use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Config\ConfigEnvInterface;
use WEM\SmartgearBundle\Classes\Config\ConfigInterface;
use WEM\SmartgearBundle\Classes\Config\Manager\AbstractManager;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerEnvInterface;
use WEM\SmartgearBundle\Config\EnvFile as ConfigEnvFile;
use WEM\SmartgearBundle\Exceptions\File\NotFound as FileNotFoundException;

class EnvFile extends AbstractManager implements ManagerEnvInterface
{
    /** @var ConfigInterface */
    protected $configuration;
    /** @var string */
    protected $configurationFilePath;

    public function __construct(
        TranslatorInterface $translator,
        ConfigEnvFile $configuration,
        string $configurationFilePath
    ) {
        parent::__construct($translator);
        $this->configuration = $configuration;
        $this->configurationFilePath = $configurationFilePath;
    }

    /**
     * [load description].
     */
    public function new(): ConfigEnvInterface
    {
        return $this->configuration->reset();
    }

    /**
     * [load description].
     */
    public function load(): ConfigEnvInterface
    {
        return $this->configuration->import($this->retrieveConfigurationAsImportableFormatFromFile());
    }

    /**
     * [save description].
     *
     * @param ConfigInterface $configuration [description]
     */
    public function save(ConfigInterface $configuration): bool
    {
        $this->configuration = $configuration;

        return false !== file_put_contents($this->configurationFilePath, $this->configuration->export());
    }

    public function retrieveConfigurationAsImportableFormatFromFile(): array
    {
        try {
            $content = $this->retrieveConfigurationFromFile();
        } catch (FileNotFoundException $e) {
            return [];
        }
        $arrLines = explode("\n", $content);
        $arrFinal = [];
        foreach ($arrLines as $line) {
            $arrLineSeparated = explode('=', $line);
            $arrFinal[$arrLineSeparated[0]] = $arrLineSeparated[1];
        }

        return $arrFinal;
    }
}
