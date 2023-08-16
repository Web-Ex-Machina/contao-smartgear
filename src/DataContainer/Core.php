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

use Contao\Backend;
use Contao\Config;
use Contao\DataContainer;
use Contao\Date;
use Contao\Message;
use DateInterval;
use DateTime;

class Core extends Backend
{
    public function updateReminder(DataContainer $dc): void
    {
        if (!$dc->id) {
            return;
        }
        $model = \Contao\Model::getClassFromTable($dc->table);
        $objItem = $model::findById($dc->id);
        if (!$objItem) {
            return;
        }
        $updateReminderDate = 0;
        if ((bool) $objItem->update_reminder) {
            $dti = new DateInterval($objItem->update_reminder_period);
            $updateReminderDate = (new DateTime())
                ->setTimestamp((int) $objItem->tstamp)
                ->add($dti)
            ;
            $updateReminderDate->setTime((int) $updateReminderDate->format('H'), (int) $updateReminderDate->format('i'), 0);
            $updateReminderDate = $updateReminderDate->getTimestamp();
        }

        $objItem->update_reminder_date = $updateReminderDate;
        $objItem->save();
    }

    public function displayReminderMessage(DataContainer $dc): void
    {
        if (!$dc->id || null === \Contao\Input::get('act')) {
            return;
        }
        $model = \Contao\Model::getClassFromTable($dc->table);
        $objItem = $model::findById($dc->id);
        if (!$objItem) {
            return;
        }
        if ((bool) $objItem->update_reminder) {
            if (time() < (int) $objItem->update_reminder_date) {
                Message::addInfo(sprintf($GLOBALS['TL_LANG']['WEMSG']['DCA']['MESSAGE']['updateReminderFuture'], Date::parse(Config::get('datimFormat'), (int) $objItem->update_reminder_date)));
            } else {
                Message::addError(sprintf($GLOBALS['TL_LANG']['WEMSG']['DCA']['MESSAGE']['updateReminderPast'], Date::parse(Config::get('datimFormat'), (int) $objItem->update_reminder_date)));
            }
        }
    }
}
