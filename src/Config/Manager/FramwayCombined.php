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
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManagerCore;
use WEM\SmartgearBundle\Config\Manager\Framway as ManagerFramway;

class FramwayCombined extends ManagerFramway
{
    /** @var string */
    protected $themeName = '';

    public function __construct(
        TranslatorInterface $translator,
        ConfigInterface $configuration,
        ConfigurationManagerCore $configurationManagerCore
    ) {
        parent::__construct($translator, $configuration, $configurationManagerCore);
        $this->configurationFilePath = $this->configurationManagerCore->load()->getSgFramwayPath().\DIRECTORY_SEPARATOR.'src'.\DIRECTORY_SEPARATOR.'combined'.\DIRECTORY_SEPARATOR.'_config.scss';
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
        if (0 !== preg_match('/\$colors: \((.*)\);/msU', $fileContent, $matches)) {
            $matchingColorLines = explode(',', $matches[1]);
            // dump($matchingColorLines);
            foreach ($matchingColorLines as $matchingColorLine) {
                if (0 !== preg_match('/\'(.*)\': (.*)/', $matchingColorLine, $colorLineMatches)) {
                    $futureJson['colors'][$colorLineMatches[1]] = $colorLineMatches[2];
                }
            }
        }
        // retrieve others
        if (0 !== preg_match('/\$primary:(.*);/', $fileContent, $matches)) {
            $futureJson['primary'] = trim($matches[1]);
        }
        if (0 !== preg_match('/\$secondary:(.*);/', $fileContent, $matches)) {
            $futureJson['secondary'] = trim($matches[1]);
        }
        if (0 !== preg_match('/\$tertiary:(.*);/', $fileContent, $matches)) {
            $futureJson['tertiary'] = trim($matches[1]);
        }
        if (0 !== preg_match('/\$success:(.*);/', $fileContent, $matches)) {
            $futureJson['success'] = trim($matches[1]);
        }
        if (0 !== preg_match('/\$info:(.*);/', $fileContent, $matches)) {
            $futureJson['info'] = trim($matches[1]);
        }
        if (0 !== preg_match('/\$warning:(.*);/', $fileContent, $matches)) {
            $futureJson['warning'] = trim($matches[1]);
        }
        if (0 !== preg_match('/\$error:(.*);/', $fileContent, $matches)) {
            $futureJson['error'] = trim($matches[1]);
        }

        return json_decode(json_encode($futureJson), false, 512, \JSON_THROW_ON_ERROR);
    }
}
