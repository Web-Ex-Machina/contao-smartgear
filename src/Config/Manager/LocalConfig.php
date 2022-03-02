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

use Symfony\Component\Yaml\Yaml;
use WEM\SmartgearBundle\Classes\Config\ConfigInterface;
use WEM\SmartgearBundle\Classes\Config\ConfigYamlInterface;
use WEM\SmartgearBundle\Classes\Config\Manager\AbstractManager;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerYamlInterface;
use WEM\SmartgearBundle\Config\LocalConfig as ConfigLocalConfig;
use WEM\SmartgearBundle\Exceptions\File\NotFound as FileNotFoundException;

class LocalConfig extends AbstractManager implements ManagerYamlInterface
{
    /** @var ConfigInterface */
    protected $configuration;
    /** @var string */
    protected $configurationFilePath;
    protected $parser;

    public function __construct(
        ConfigLocalConfig $configuration,
        string $configurationFilePath
    ) {
        $this->configuration = $configuration;
        $this->configurationFilePath = $configurationFilePath;
        $this->parser = new \Symfony\Component\Yaml\Parser();
    }

    /**
     * [load description].
     */
    public function new(): ConfigYamlInterface
    {
        return $this->configuration->reset();
    }

    /**
     * [load description].
     */
    public function load(): ConfigYamlInterface
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
        $yaml = Yaml::dump($this->configuration->export(), 10);

        return false !== file_put_contents($this->configurationFilePath, $yaml);
    }

    public function retrieveConfigurationAsImportableFormatFromFile(): array
    {
        try {
            $content = $this->retrieveConfigurationFromFile();
        } catch (FileNotFoundException $e) {
            return [];
        }

        return $this->parser->parse($content);
    }

    // protected function retrieveConfigurationFromFile(): string
    // {
    //     if (!file_exists($this->configurationFilePath)) {
    //         throw new FileNotFoundException('Configuration file not found');
    //     }

    //     return file_get_contents($this->configurationFilePath);
    // }
}
