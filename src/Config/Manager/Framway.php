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
use WEM\SmartgearBundle\Classes\Config\ConfigInterface;
use WEM\SmartgearBundle\Classes\Config\Manager\AbstractManager;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManagerCore;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJsonInterface;
use WEM\SmartgearBundle\Exceptions\File\NotFound as FileNotFoundException;

class Framway extends AbstractManager implements ManagerJsonInterface
{
    /** @var ConfigInterface */
    protected $configuration;
    /** @var ConfigurationManagerCore */
    protected $configurationManagerCore;
    /** @var string */
    protected $configurationFilePath;

    public function __construct(
        TranslatorInterface $translator,
        ConfigInterface $configuration,
        ConfigurationManagerCore $configurationManagerCore
    ) {
        parent::__construct($translator);
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
        $this->assignConfigurationFilePathIfNotDefined();

        return $this->configuration->import($this->retrieveConfigurationAsImportableFormatFromFile());
    }

    public function retrieveConfigurationAsImportableFormatFromFile(): \stdClass
    {
        $notJsonCompliant = $this->retrieveConfigurationFromFile();
        $notJsonCompliant = str_replace('module.exports = ', '', $notJsonCompliant);
        $notJsonCompliant = preg_replace('/\/\/(.*)/', '', $notJsonCompliant);
        $notJsonCompliant = $this->specificPregReplaceForNotJsonCompliantConfigurationImport($notJsonCompliant);

        $notJsonCompliant = preg_replace('/\t/', '', $notJsonCompliant);
        $notJsonCompliant = preg_replace('/\n/', '', $notJsonCompliant);
        $notJsonCompliant = preg_replace('/\s\s/', '', $notJsonCompliant);
        $notJsonCompliant = preg_replace('/,([\s]*)\]/', ']', $notJsonCompliant);
        $notJsonCompliant = preg_replace('/,([\s]*)\}/', '}', $notJsonCompliant);
        $notJsonCompliant = preg_replace('/."com":/', '.com:', $notJsonCompliant); // dirty quickfix, don't know yet how to cleanly workaround this

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
        $this->assignConfigurationFilePathIfNotDefined();

        return false !== file_put_contents($this->getConfigurationFilePath(), 'module.exports = '.$json);
    }

    public function getConfigurationFilePath(): string
    {
        return $this->configurationFilePath;
    }

    protected function specificPregReplaceForNotJsonCompliantConfigurationImport(string $notJsonCompliant): string
    {
        $notJsonCompliant = preg_replace('/([\s]*)([A-Za-z_\-0-9$]*)([\s]*):/', '"$2":', $notJsonCompliant);
        $notJsonCompliant = preg_replace('/\'/', '"', $notJsonCompliant);

        return preg_replace('/([\"]+)([A-Za-z_\-0-9$]+)([\"]+)/', '"$2"', $notJsonCompliant);
    }

    protected function retrieveConfigurationFromFile(): string
    {
        $this->assignConfigurationFilePathIfNotDefined();
        if (!file_exists($this->getConfigurationFilePath())) {
            throw new FileNotFoundException($this->translator->trans('WEMSG.CONFIGURATIONMANAGER.fileNotFound', [], 'contao_default'));
        }

        return file_get_contents($this->getConfigurationFilePath());
    }

    protected function assignConfigurationFilePathIfNotDefined(): void
    {
        if (null === $this->configurationFilePath) {
            $this->assignConfigurationFilePath();
        }
    }

    protected function assignConfigurationFilePath(): void
    {
        $config = $this->configurationManagerCore->load();
        $this->configurationFilePath = $config->getSgFramwayPath().\DIRECTORY_SEPARATOR.'framway.config.js';
    }
}
