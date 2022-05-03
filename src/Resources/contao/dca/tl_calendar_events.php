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

use Contao\DataContainer;

class tl_wem_sg_calendar_events extends tl_calendar_events
{
    /**
     * Add the source options depending on the allowed fields (see #5498).
     *
     * @return array
     */
    public function getSourceOptions(DataContainer $dc)
    {
        $arrOptions = parent::getSourceOptions($dc);
        $valuesToKeep = ['default', 'external'];
        foreach ($arrOptions as $index => $value) {
            if (!\in_array($value, $valuesToKeep, true)) {
                unset($arrOptions[$index]);
            }
        }

        return $arrOptions;
    }
}
