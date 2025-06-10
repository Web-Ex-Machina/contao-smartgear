<?php

declare(strict_types=1);

/*
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2025 Web ex Machina
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

class FramwayCombined extends ManagerFramway
{
    public function __construct(
        TranslatorInterface $translator,
        ConfigInterface $configuration,
        ConfigurationManagerCore $configurationManagerCore,
    ) {
        parent::__construct($translator, $configuration, $configurationManagerCore);
        $this->configurationFilePath = null;
    }

    /**
     * [save description].
     *
     * @param ConfigInterface $configuration [description]
     */
    public function save(ConfigInterface $configuration): bool
    {
        return false;
    }

    public function retrieveConfigurationAsImportableFormatFromFile(): \stdClass
    {
        $futureJson = [
            'colors' => [],
            'primary' => null,
            'secondary' => null,
            'tertiary' => null,
            'success' => null,
            'info' => null,
            'warning' => null,
            'error' => null,
        ];
        $fileContent = $this->retrieveConfigurationFromFile();
        // retrieve colors
        if (preg_match('/\$colors: \((.*)\);/msU', $fileContent, $matches) !== 0) {
            $matchingColorLines = explode(',', $matches[1]);

            // dump($matchingColorLines);
            foreach ($matchingColorLines as $matchingColorLine) {
                if (preg_match("/'(.*)': (.*)/", $matchingColorLine, $colorLineMatches) !== 0) {
                    $futureJson['colors'][$colorLineMatches[1]] = $colorLineMatches[2];
                }
            }
        }

        // retrieve others
        if (preg_match('/\$primary:(.*);/', $fileContent, $matches) !== 0) {
            $futureJson['primary'] = trim($matches[1]);
        }

        if (preg_match('/\$secondary:(.*);/', $fileContent, $matches) !== 0) {
            $futureJson['secondary'] = trim($matches[1]);
        }

        if (preg_match('/\$tertiary:(.*);/', $fileContent, $matches) !== 0) {
            $futureJson['tertiary'] = trim($matches[1]);
        }

        if (preg_match('/\$success:(.*);/', $fileContent, $matches) !== 0) {
            $futureJson['success'] = trim($matches[1]);
        }

        if (preg_match('/\$info:(.*);/', $fileContent, $matches) !== 0) {
            $futureJson['info'] = trim($matches[1]);
        }

        if (preg_match('/\$warning:(.*);/', $fileContent, $matches) !== 0) {
            $futureJson['warning'] = trim($matches[1]);
        }

        if (preg_match('/\$error:(.*);/', $fileContent, $matches) !== 0) {
            $futureJson['error'] = trim($matches[1]);
        }

        return json_decode(json_encode($futureJson), false, 512, JSON_THROW_ON_ERROR);
    }

    protected function assignConfigurationFilePath(): void
    {
        $rootPath = $this->getConfigurationRootFilePath() ?? $this->configurationManagerCore->load()->getSgFramwayPath();

        $this->configurationFilePath = $rootPath . \DIRECTORY_SEPARATOR . 'build' . \DIRECTORY_SEPARATOR . 'combined' . \DIRECTORY_SEPARATOR . '_config.scss';
    }
}
