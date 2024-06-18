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

namespace WEM\SmartgearBundle\DataContainer;

use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\Image;
use Contao\Input;
use Contao\System;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Model\Configuration\ConfigurationItem;

class Calendar extends \tl_calendar
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Check permissions to edit table tl_calendar.
     *
     * @throws AccessDeniedException
     */
    public function checkPermission(): void
    {
        parent::checkPermission();

        if (Input::get('act') === 'delete') {
            if (!$this->canItemBeDeleted((int) Input::get('id'))) {
                throw new AccessDeniedException('Not enough permissions to '.Input::get('act').' calendar ID '.Input::get('id').'.');
            }
        }
    }

    /**
     * Return the delete calendar button.
     */
    public function deleteItem(array $row, string $href, string $label, string $title, string $icon, string $attributes): string
    {
        if (!$this->canItemBeDeleted((int) $row['id'])) {
            return Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
        }

        return parent::deleteCalendar($row, $href, $label, $title, $icon, $attributes);
    }

    /**
     * Check if the calendar is being used by Smartgear.
     *
     * @param int $id calendar's ID
     */
    protected function isItemUsedBySmartgear(int $id): bool
    {
        // try {
        //     $eventsConfig = $this->configManager->load()->getSgEvents();
        //     if ($eventsConfig->getSgInstallComplete() && $id === (int) $eventsConfig->getSgCalendar()) {
        //         return true;
        //     }
        // } catch (\Exception $e) {
        // }

        if (0 < ConfigurationItem::countItems(['contao_calendar' => $id])) {
            return true;
        }

        return false;
    }

    protected function canItemBeDeleted(int $id): bool
    {
        return $this->User->admin || !$this->isItemUsedBySmartgear($id);
    }
}
