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

namespace WEM\SmartgearBundle\Backend;

use Contao\BackendModule;
use Contao\DataContainer;
use Contao\Message;
use Contao\System;
use Exception;
use WEM\SmartgearBundle\Classes\Command\Util as CommandUtil;

/**
 * Back end module "smartgear".
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class CacheClear extends BackendModule
{
    protected ?CommandUtil $commandUtil;

    public function __construct(?DataContainer $dc = null)
    {
        parent::__construct($dc);
        $this->commandUtil = System::getContainer()->get('smartgear.classes.command.util');
    }

    /**
     * Generate the module.
     *
     * @throws Exception
     */
    protected function compile(): void
    {
        $this->commandUtil->executeCmdPHP('cache:clear');
        Message::addConfirmation($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['CACHECLEAR']['confirmation']);
        $this->redirect($this->getReferer());
    }
}
