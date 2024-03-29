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

use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Config\ConfigInterface;

class ManagerJson extends AbstractManager implements ManagerJsonInterface
{
    /** @var ConfigInterface */
    protected $configuration;
    /** @var string */
    protected $configurationFilePath;

    public function __construct(
        TranslatorInterface $translator,
        ConfigInterface $configuration,
        string $configurationFilePath
    ) {
        parent::__construct($translator);
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
     * Backup a configuration.
     *
     * @return string|bool The backup file's path if all goes well, false otherwise
     */
    public function createBackupFile()
    {
        $backupFilePath = $this->configurationFilePath.'_'.date('Ymd_His');
        $this->load();

        return $this->file_force_contents($backupFilePath, $this->configuration->export()) ? $path : false;
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
     * @param string $filepath The file's path
     * @param mixed  $content  The content to write
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
