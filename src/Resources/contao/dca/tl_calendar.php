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

use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\Image;
use Contao\Input;
use Contao\System;

$GLOBALS['TL_DCA']['tl_calendar']['config']['onload_callback'][] = ['tl_wem_sg_calendar', 'checkPermission'];
$GLOBALS['TL_DCA']['tl_calendar']['list']['operations']['delete']['button_callback'] = ['tl_wem_sg_calendar', 'deleteCalendar'];

/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @property News $News
 */
class tl_wem_sg_calendar extends tl_calendar
{
    /**
     * Check permissions to edit table tl_calendar.
     *
     * @throws AccessDeniedException
     */
    public function checkPermission(): void
    {
        parent::checkPermission();

        // Check current action
        switch (Input::get('act')) {
            case 'delete':
                if ($this->isCalendarUsedBySmartgear((int) Input::get('id'))) {
                    throw new AccessDeniedException('Not enough permissions to '.Input::get('act').' calendar ID '.Input::get('id').'.');
                }
            break;
        }
    }

    /**
     * Return the delete calendar button.
     *
     * @param array  $row
     * @param string $href
     * @param string $label
     * @param string $title
     * @param string $icon
     * @param string $attributes
     *
     * @return string
     */
    public function deleteCalendar($row, $href, $label, $title, $icon, $attributes)
    {
        if ($this->isCalendarUsedBySmartgear((int) $row['id'])) {
            return Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
        }

        return parent::deleteCalendar($row, $href, $label, $title, $icon, $attributes);
    }

    /**
     * Check if the calendar is being used by Smartgear.
     *
     * @param int $id calendar's ID
     */
    protected function isCalendarUsedBySmartgear(int $id): bool
    {
        $configManager = System::getContainer()->get('smartgear.config.manager.core');
        try {
            $eventsConfig = $configManager->load()->getSgEvents();
            if ($eventsConfig->getSgInstallComplete() && $id === (int) $eventsConfig->getSgCalendar()) {
                return true;
            }
        } catch (\Exception $e) {
        }

        return false;
    }
}
