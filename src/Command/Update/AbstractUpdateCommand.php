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

namespace WEM\SmartgearBundle\Command\Update;

use WEM\SmartgearBundle\Command\AbstractCommand;
use WEM\SmartgearBundle\Update\UpdateManager;
use Contao\CoreBundle\Framework\ContaoFramework;

class AbstractUpdateCommand extends AbstractCommand
{
    protected UpdateManager $updateManager;

    public function __construct(UpdateManager $updateManager, ContaoFramework $framework)
    {
        $this->updateManager = $updateManager;

        parent::__construct($framework);

        $this->framework->initialize();
    }
}