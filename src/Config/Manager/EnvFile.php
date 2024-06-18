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
    public function __construct(
        TranslatorInterface $translator,
        protected ConfigInterface $configuration,
        protected ?string $configurationFilePath
    ) {
        parent::__construct($translator);
    }

    /**
     * [load description].
     */
    public function new(): ConfigEnvInterface
    {
        return $this->configuration->reset();
        // TODO : Return value is expected to be '\WEM\SmartgearBundle\Classes\Config\ConfigEnvInterface', '\WEM\SmartgearBundle\Classes\Config\ConfigInterface' returned
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
        } catch (FileNotFoundException) {
            return [];
        }

        $arrLines = explode("\n", $content);
        $arrFinal = [];
        foreach ($arrLines as $line) {
            if ($line === '' || $line === '0') {
                continue;
            }

            $arrLineSeparated = explode('=', $line);
            $arrFinal[$arrLineSeparated[0]] = $arrLineSeparated[1];
        }

        return $arrFinal;
    }
}
