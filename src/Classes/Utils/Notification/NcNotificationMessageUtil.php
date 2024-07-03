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
use Terminal42\NotificationCenterBundle\Receipt\ReceiptCollection;

readonly class NcNotificationMessageUtil
{
    public function __construct(private NotificationCenter $notificationCenter){}
    /**
     * Shortcut for article creation.
     */
    public function createNotification(int $pid, ?array $arrData = []): ReceiptCollection
    {

        return $this->notificationCenter->sendNotification($pid, $arrData);
    }

    public function createSupportFormNotificationMessageUser(int $pid, ?array $arrData = []): ReceiptCollection
    {
        return $this->createNotification($pid, array_merge([
            'title' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['titleNotificationSupportGatewayMessageUser'],
        ], $arrData));
    }

    public function createSupportFormNotificationMessageAdmin(int $pid, ?array $arrData = []): ReceiptCollection
    {
        return $this->createNotification($pid, array_merge([
            'title' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['titleNotificationSupportGatewayMessageAdmin'],
        ], $arrData));
    }

    public function createContactFormSentNotificationMessageUser(int $pid, ?array $arrData = []): ReceiptCollection
    {
        return $this->createNotification($pid, array_merge([
            'title' => $GLOBALS['TL_LANG']['WEMSG']['FORMCONTACT']['INSTALL_GENERAL']['titleNotificationGatewayMessageUser'],
        ], $arrData));
    }

    public function createContactFormSentNotificationMessageAdmin(int $pid, ?array $arrData = []): ReceiptCollection
    {
        return $this->createNotification($pid, array_merge([
            'title' => $GLOBALS['TL_LANG']['WEMSG']['FORMCONTACT']['INSTALL_GENERAL']['titleNotificationGatewayMessageAdmin'],
        ], $arrData));
    }
}
