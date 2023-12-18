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

use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Config\ConfigInterface;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManagerCore;
use WEM\SmartgearBundle\Config\Manager\Framway as ManagerFramway;

class FramwayTheme extends ManagerFramway
{
    /** @var string */
    protected $themeName = '';

    public function __construct(
        TranslatorInterface $translator,
        ConfigInterface $configuration,
        ConfigurationManagerCore $configurationManagerCore
    ) {
        parent::__construct($translator, $configuration, $configurationManagerCore);
    }

    public function getConfigurationFilePath(): string
    {
        $rootPath = $this->getConfigurationRootFilePath() ?? $this->configurationManagerCore->load()->getSgFramwayPath();

        return $rootPath.\DIRECTORY_SEPARATOR.(
            '' !== $this->getThemeName()
            ? 'src'.\DIRECTORY_SEPARATOR.'themes'.\DIRECTORY_SEPARATOR.$this->getThemeName().\DIRECTORY_SEPARATOR.'config.js'
            : 'src'.\DIRECTORY_SEPARATOR.'core'.\DIRECTORY_SEPARATOR.'config.js'
        );
    }

    public function getThemeName(): string
    {
        return $this->themeName;
    }

    public function setThemeName(string $themeName): self
    {
        $this->themeName = $themeName;

        return $this;
    }

    protected function specificPregReplaceForNotJsonCompliantConfigurationImport(string $notJsonCompliant): string
    {
        $notJsonCompliant = str_replace('module.exports = ', '', $notJsonCompliant);
        $notJsonCompliant = preg_replace('/^([\s\t]*)\/\/(.*)/m', '', $notJsonCompliant); // remove one liner comments
        $notJsonCompliant = preg_replace('/^(.*)\'\,([\s\t]*)\/\/(.*)/m', '$1\',', $notJsonCompliant); // remove comments at the end of a line
        $notJsonCompliant = preg_replace('/^(.*)\,([\s\t]*)\/\/(.*)/m', '$1,', $notJsonCompliant); // remove comments at the end of a line
        $notJsonCompliant = preg_replace('/,([\s]*)\]/', ']', $notJsonCompliant); // final comma in array
        $notJsonCompliant = preg_replace('/,([\s]*)\}/', '}', $notJsonCompliant); // final comma in object

        return preg_replace('/\'([^\']*)\'/', '"$1"', $notJsonCompliant);
    }
}
