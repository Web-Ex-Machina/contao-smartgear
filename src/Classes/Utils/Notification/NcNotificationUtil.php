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

namespace WEM\SmartgearBundle\Classes\Utils\Notification;

use NotificationCenter\Model\Notification;

class NcNotificationUtil
{
    /**
     * Shortcut for article creation.
     */
    public static function createNotification($arrData = []): Notification
    {
        // Create the article
        $objNotification = isset($arrData['id']) ? Notification::findById($arrData['id']) ?? new Notification() : new Notification();
        $objNotification->tstamp = time();

        // Now we get the default values, get the arrData table
        if (!empty($arrData)) {
            foreach ($arrData as $k => $v) {
                $objNotification->$k = $v;
            }
        }

        $objNotification->save();

        // Return the model
        return $objNotification;
    }

    public static function createSupportFormNotification($arrData = []): Notification
    {
        return self::createNotification(array_merge([
            'title' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['titleNotificationSupportGatewayNotification'],
            'type' => 'ticket_creation',
        ], $arrData));
    }
}
