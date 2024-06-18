<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2023 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

namespace WEM\SmartgearBundle\Config\Manager;

use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Config\ConfigInterface;
use WEM\SmartgearBundle\Classes\Config\Manager\AbstractManager;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManagerCore;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJsonInterface;
use WEM\SmartgearBundle\Exceptions\File\NotFound as FileNotFoundException;

class Framway extends AbstractManager implements ManagerJsonInterface
{

    protected ?string $configurationFilePath = null;

    protected ?string $configurationRootFilePath = null;

    public function __construct(
        TranslatorInterface $translator,
        protected ConfigInterface $configuration,
        protected ConfigurationManagerCore $configurationManagerCore
    ) {
        parent::__construct($translator);
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

    /**
     * @throws FileNotFoundException
     * @throws Exception
     */
    public function retrieveConfigurationAsImportableFormatFromFile(): \stdClass
    {
        $notJsonCompliant = $this->retrieveConfigurationFromFile();

        $notJsonCompliant = $this->specificPregReplaceForNotJsonCompliantConfigurationImport($notJsonCompliant);

        try {
            return json_decode($notJsonCompliant, false, 512, \JSON_THROW_ON_ERROR);
        } catch (Exception $exception) {
            throw new Exception($this->translator->trans('WEMSG.ERR.FRAMWAY.configJsonDecodeError', [\JSON_ERROR_NONE !== json_last_error() ? json_last_error_msg() : $exception->getMessage()], 'contao_default'), $exception->getCode(), $exception);
        }
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

    public function getConfigurationRootFilePath(): ?string
    {
        return $this->configurationRootFilePath;
    }

    public function setConfigurationRootFilePath(?string $configurationRootFilePath = null): self
    {
        $this->configurationRootFilePath = $configurationRootFilePath;
        $this->assignConfigurationFilePath();

        return $this;
    }

    protected function specificPregReplaceForNotJsonCompliantConfigurationImport(string $notJsonCompliant): string
    {
        $notJsonCompliant = str_replace('module.exports = ', '', $notJsonCompliant);
        $notJsonCompliant = preg_replace('/^([\s\t]*)\/\/(.*)/m', '', $notJsonCompliant); // remove one liner comments
        $notJsonCompliant = preg_replace('/^(.*)\'\,([\s\t]*)\/\/(.*)/m', '$1\',', $notJsonCompliant); // remove comments at the end of a line
        $notJsonCompliant = preg_replace('/^(.*)\,([\s\t]*)\/\/(.*)/m', '$1,', $notJsonCompliant); // remove comments at the end of a line

        ////////
        $notJsonCompliant = preg_replace('/([\s]*)([A-Za-z_\-0-9$]*)([\s]*):([\s]*)([\{]{1})/', '$1"$2":{', $notJsonCompliant);
        $notJsonCompliant = preg_replace('/([\s]*)([A-Za-z_\-0-9$]*)([\s]*):([\s]*)([\[]{1})/', '$1"$2":[', $notJsonCompliant);
        // $notJsonCompliant = preg_replace('/([\s]*)([A-Za-z_\-0-9$]*)([\s]*):([\s]*)([^\/\:])/', '$1"$2":', $notJsonCompliant);
        $notJsonCompliant = preg_replace('/\'/', '"', $notJsonCompliant);
        // $notJsonCompliant = preg_replace('/\"([A-Za-z_\-0-9$]*)\":[\s]\[/', '"$1":[', $notJsonCompliant);
        $notJsonCompliant = preg_replace('/([\s]*)([A-Za-z_\-0-9$]*)([\s]*):([\s]*)([A-Za-z_\-0-9$]*)/', '$1"$2":$5', $notJsonCompliant);

        $notJsonCompliant = preg_replace('/([\"]+)([A-Za-z_\-0-9$]+)([\"]+)/', '"$2"', $notJsonCompliant);
        ////////

        $notJsonCompliant = preg_replace('/\t/', '', $notJsonCompliant);
        $notJsonCompliant = preg_replace('/\n/', '', $notJsonCompliant);
        $notJsonCompliant = preg_replace('/\s\s/', '', $notJsonCompliant);
        $notJsonCompliant = preg_replace('/,([\s]*)\]/', ']', $notJsonCompliant); // final comma in array
        $notJsonCompliant = preg_replace('/,([\s]*)\}/', '}', $notJsonCompliant); // final comma in object

        $notJsonCompliant = preg_replace('/\.\"com\":/', '.com:', $notJsonCompliant); // dirty quickfix, don't know yet how to cleanly workaround this
        $notJsonCompliant = preg_replace('/\"https\":/', '"https:', $notJsonCompliant); // dirty quickfix, don't know yet how to cleanly workaround this

        return preg_replace('/\"http\":/', '"http:', $notJsonCompliant); // dirty quickfix, don't know yet how to cleanly workaround this
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
        if (null !== $this->getConfigurationRootFilePath()) {
            $this->configurationFilePath = $this->getConfigurationRootFilePath().\DIRECTORY_SEPARATOR.'framway.config.js';
        } else {
            $config = $this->configurationManagerCore->load();
            $this->configurationFilePath = $config->getSgFramwayPath().\DIRECTORY_SEPARATOR.'framway.config.js';
        }
    }
}
