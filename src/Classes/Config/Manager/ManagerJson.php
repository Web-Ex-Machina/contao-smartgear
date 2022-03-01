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

namespace WEM\SmartgearBundle\Classes\Config\Manager;

use WEM\SmartgearBundle\Classes\Config\ConfigInterface;

// use WEM\SmartgearBundle\Exceptions\File\NotFound as FileNotFoundException;

class ManagerJson extends AbstractManager implements ManagerJsonInterface
{
    /** @var ConfigInterface */
    protected $configuration;
    /** @var string */
    protected $configurationFilePath;

    public function __construct(
        ConfigInterface $configuration,
        string $configurationFilePath
    ) {
        $this->configuration = $configuration;
        $this->configurationFilePath = $configurationFilePath;
    }

    /**
     * [load description].
     */
    public function new(): ConfigInterface
    {
        return $this->configuration->reset();
    }

    /**
     * [load description].
     */
    public function load(): ConfigInterface
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

        return $this->file_force_contents($this->configurationFilePath, $this->configuration->export());
    }

    public function retrieveConfigurationAsImportableFormatFromFile(): \stdClass
    {
        return json_decode($this->retrieveConfigurationFromFile(), false, 512, \JSON_THROW_ON_ERROR);
    }

    // protected function retrieveConfigurationFromFile(): string
    // {
    //     if (!file_exists($this->configurationFilePath)) {
    //         throw new FileNotFoundException('Configuration file not found');
    //     }

    //     return file_get_contents($this->configurationFilePath);
    // }

    protected function file_force_contents($dir, $contents): bool
    {
        $parts = explode('/', $dir);
        $file = array_pop($parts);
        $dir = '';
        foreach ($parts as $part) {
            if ('.' !== $part) {
                if (!is_dir($dir .= "/$part")) {
                    mkdir($dir);
                }
            } else {
                $dir .= $part;
            }
        }

        return false !== file_put_contents("$dir/$file", $contents);
    }
}
