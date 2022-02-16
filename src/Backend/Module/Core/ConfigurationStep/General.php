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

namespace WEM\SmartgearBundle\Backend\Module\Core\ConfigurationStep;

use WEM\SmartgearBundle\Classes\Backend\ConfigurationStep;

class General extends ConfigurationStep
{
    public function __construct(
        string $module
    ) {
        parent::__construct($module);

        $this->addTextField('toto', 'The totooooo !!!');
    }
}
