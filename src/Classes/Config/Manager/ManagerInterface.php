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

use WEM\SmartgearBundle\Classes\Config\ConfigInterface;

interface ManagerInterface
{
    /**
     * Get a new configuration.
     */
    public function new(): ConfigInterface;

    /**
     * Load a configuration.
     */
    public function load(): ConfigInterface;

    /**
     * Save a configuration.
     *
     * @param ConfigInterface $configuration [description]
     */
    public function save(ConfigInterface $configuration): bool;
}
