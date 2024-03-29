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

interface ConfigJsonInterface extends ConfigInterface
{
    /**
     * Import a configuration.
     *
     * @param \stdClass $json [description]
     */
    public function import(\stdClass $json): self;

    /**
     * Export a configuration.
     */
    public function export(); //: string;
}
