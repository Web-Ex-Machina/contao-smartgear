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

namespace WEM\SmartgearBundle\Classes\Dca\Driver;

use Contao\DataContainer;

class DC_Table_Newsletter extends \Contao\DC_Table
{
    public function showAll(): string
    {
        if (DataContainer::$currentPid) {
            $this->procedure[] = 'channels REGEXP ?';
            $this->values[] = '.*;s:[0-9]+:"'.DataContainer::$currentPid.'".*';
        }

        return parent::showAll();
    }

    protected function removePidFilter(): void
    {
        $idx = array_search('pid=?', $this->procedure, true);
        if (!($idx === 0 || ($idx === '' || $idx === '0') || $idx === false)) {
            unset($this->procedure[$idx], $this->values[$idx]);
        }
    }

    protected function panel(): string
    {
        $this->removePidFilter();

        return parent::panel();
    }
}
