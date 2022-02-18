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

namespace WEM\SmartgearBundle\Classes\Config;

use WEM\SmartgearBundle\Exceptions\File\NotFound as FileNotFoundException;

class Manager implements ManagerInterface
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
        return $this->configuration->import($this->retrieveConfigurationAsStdClassFromFile());
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

    public function retrieveConfigurationAsStdClassFromFile(): \stdClass
    {
        return json_decode($this->retrieveConfigurationFromFile(), false, 512, \JSON_THROW_ON_ERROR);
    }

    protected function retrieveConfigurationFromFile(): string
    {
        $content = file_get_contents($this->configurationFilePath);
        if (!$content) {
            throw new FileNotFoundException('Configuration file not found');
        }

        return $content;
    }
}
