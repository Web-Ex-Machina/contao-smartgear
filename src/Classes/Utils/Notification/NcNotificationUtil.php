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

use Terminal42\NotificationCenterBundle\NotificationCenter;

readonly class NcNotificationUtil
{
    public function __construct(private NotificationCenter $notificationCenter){}

    public function createSupportFormNotification(array $arrData = []): void
    {
        $this->notificationCenter->sendNotification($arrData['id'], array_merge([
            'title' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['titleNotificationSupportGatewayNotification'],
            'type' => 'ticket_creation',
        ], $arrData));
    }

    public function createFormContactSentNotification(string $title, ?array $arrData = []): void
    {
        $this->notificationCenter->sendNotification($arrData['id'],array_merge([
            'title' => $title,
            'type' => 'core_form',
        ], $arrData));
    }
}
