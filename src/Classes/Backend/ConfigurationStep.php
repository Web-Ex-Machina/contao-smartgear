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

namespace WEM\SmartgearBundle\Classes\Backend;

class ConfigurationStep extends AbstractStep
{
    protected $strTemplate = 'be_wem_sg_install_block_configuration_step';

    public function __construct(
        string $module,
        string $type
    ) {
        parent::__construct($module, $type);
    }
}
