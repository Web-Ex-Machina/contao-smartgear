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

class DC_Table_Newsletter extends \Contao\DC_Table
{
    public function showAll()
    {
        if (CURRENT_ID) {
            $this->procedure[] = 'channels REGEXP ?';
            $this->values[] = '.*;s:[0-9]+:"'.CURRENT_ID.'".*';
        }

        return parent::showAll();
    }

    protected function removePidFilter(): void
    {
        $idx = array_search('pid=?', $this->procedure, true);
        if (!empty($idx)) {
            unset($this->procedure[$idx], $this->values[$idx]);
        }
    }

    protected function panel()
    {
        $this->removePidFilter();

        return parent::panel();
    }
}
