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

use WEM\SmartgearBundle\Classes\Config\ConfigInterface;
use WEM\SmartgearBundle\Classes\Config\Manager as ConfigurationManagerCore;
use WEM\SmartgearBundle\Classes\Config\ManagerJsonInterface;
use WEM\SmartgearBundle\Exceptions\File\NotFound as FileNotFoundException;

class Framway implements ManagerJsonInterface
{
    /** @var ConfigInterface */
    protected $configuration;
    /** @var ConfigurationManagerCore */
    protected $configurationManagerCore;

    public function __construct(
        ConfigInterface $configuration,
        ConfigurationManagerCore $configurationManagerCore
    ) {
        $this->configuration = $configuration;
        $this->configurationManagerCore = $configurationManagerCore;
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

    public function retrieveConfigurationAsImportableFormatFromFile(): \stdClass
    {
        $notJsonCompliant = $this->retrieveConfigurationFromFile();
        $notJsonCompliant = str_replace('module.exports = ', '', $notJsonCompliant);
        $notJsonCompliant = preg_replace('/\/\/(.*)/', '', $notJsonCompliant);
        $notJsonCompliant = preg_replace('/([\w]*):/', '"$1":', $notJsonCompliant);
        $notJsonCompliant = preg_replace('/\'/', '"', $notJsonCompliant);
        $notJsonCompliant = preg_replace('/\t/', '', $notJsonCompliant);
        $notJsonCompliant = preg_replace('/\n/', '', $notJsonCompliant);
        $notJsonCompliant = preg_replace('/\s\s/', '', $notJsonCompliant);
        $notJsonCompliant = preg_replace('/,\]/', ']', $notJsonCompliant);
        $notJsonCompliant = preg_replace('/,\}/', '}', $notJsonCompliant);

        return json_decode($notJsonCompliant, false, 512, \JSON_THROW_ON_ERROR);
    }

    /**
     * [save description].
     *
     * @param ConfigInterface $configuration [description]
     */
    public function save(ConfigInterface $configuration): bool
    {
        $this->configuration = $configuration;
        $json = $this->configuration->export();

        $json = preg_replace('/"(.*)":/', '$1:', $json);
        $json = preg_replace('/"(.*)"/', '\'$1\'', $json);

        return false !== file_put_contents($this->configurationManagerCore->load()->getSgFramwayPath().\DIRECTORY_SEPARATOR.'framway.config.js', 'module.exports = '.$json);
    }

    protected function retrieveConfigurationFromFile(): string
    {
        $content = file_get_contents($this->configurationManagerCore->load()->getSgFramwayPath().\DIRECTORY_SEPARATOR.'framway.config.js');
        if (!$content) {
            throw new FileNotFoundException('Configuration file not found');
        }

        return $content;
    }
}
