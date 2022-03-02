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
     * Get a new configuration.
     */
    public function new(): ConfigInterface
    {
        return $this->configuration->reset();
    }

    /**
     * Load a configuration.
     */
    public function load(): ConfigInterface
    {
        return $this->configuration->import($this->retrieveConfigurationAsImportableFormatFromFile());
    }

    /**
     * Save a configuration.
     *
     * @param ConfigInterface $configuration [description]
     */
    public function save(ConfigInterface $configuration): bool
    {
        $this->configuration = $configuration;

        return $this->file_force_contents($this->configurationFilePath, $this->configuration->export());
    }

    /**
     * Retrieve the configuration from the file, but as an importable format.
     */
    public function retrieveConfigurationAsImportableFormatFromFile(): \stdClass
    {
        return json_decode($this->retrieveConfigurationFromFile(), false, 512, \JSON_THROW_ON_ERROR);
    }

    /**
     * Write content in a file.
     * If the file doesn't exists, it is created.
     *
     * @param  [type] $content [description]
     */
    protected function file_force_contents(string $filepath, $content): bool
    {
        $parts = explode('/', $filepath);
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

        return false !== file_put_contents("$dir/$file", $content);
    }
}
