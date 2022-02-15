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

class Manager
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
     * [load description]
     * @return ConfigInterface
     */
    public function load(): ConfigInterface
    {
        return $this->configuration->import($this->retrieveConfigurationAsStdClass());
    }

    /**
     * [save description]
     * @param  ConfigInterface $configuration [description]
     * @return bool
     */
    public function save(ConfigInterface $configuration): bool
    {
        $this->configuration = $configuration;
        return false !== file_put_contents($this->configurationFilePath, $this->configuration->export());
    }

    public function retrieveConfigurationAsStdClass(): \stdClass
    {
        return json_decode(file_get_contents($this->configurationFilePath));
    }
}
