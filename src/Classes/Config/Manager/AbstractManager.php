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

use WEM\SmartgearBundle\Exceptions\File\NotFound as FileNotFoundException;

abstract class AbstractManager implements ManagerInterface
{
    /**
     * Retrieve content from configuration file.
     */
    protected function retrieveConfigurationFromFile(): string
    {
        if (!file_exists($this->configurationFilePath)) {
            throw new FileNotFoundException('Configuration file not found');
        }

        return file_get_contents($this->configurationFilePath);
    }
}
