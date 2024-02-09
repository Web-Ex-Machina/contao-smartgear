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

use NotificationCenter\Model\Message;

class NcNotificationMessageUtil
{
    /**
     * Shortcut for article creation.
     */
    public static function createNotification(int $gatewayId, string $gatewayType, int $pid, ?array $arrData = []): Message
    {
        // Create the article
        $objNotificationMessage = isset($arrData['id']) ? Message::findById($arrData['id']) ?? new Message() : new Message();
        $objNotificationMessage->tstamp = time();
        $objNotificationMessage->pid = $pid;
        $objNotificationMessage->gateway = $gatewayId;
        $objNotificationMessage->gateway_type = $gatewayType;
        $objNotificationMessage->published = 1;

        // Now we get the default values, get the arrData table
        if (!empty($arrData)) {
            foreach ($arrData as $k => $v) {
                $objNotificationMessage->$k = $v;
            }
        }

        $objNotificationMessage->save();

        // Return the model
        return $objNotificationMessage;
    }

    public static function createSupportFormNotificationMessageUser(int $gatewayId, string $gatewayType, int $pid, ?array $arrData = []): Message
    {
        return self::createNotification($gatewayId, $gatewayType, $pid, array_merge([
            'title' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['titleNotificationSupportGatewayMessageUser'],
        ], $arrData));
    }

    public static function createSupportFormNotificationMessageAdmin(int $gatewayId, string $gatewayType, int $pid, ?array $arrData = []): Message
    {
        return self::createNotification($gatewayId, $gatewayType, $pid, array_merge([
            'title' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['titleNotificationSupportGatewayMessageAdmin'],
        ], $arrData));
    }

    public static function createContactFormSentNotificationMessageUser(int $gatewayId, string $gatewayType, int $pid, ?array $arrData = []): Message
    {
        return self::createNotification($gatewayId, $gatewayType, $pid, array_merge([
            'title' => $GLOBALS['TL_LANG']['WEMSG']['FORMCONTACT']['INSTALL_GENERAL']['titleNotificationGatewayMessageUser'],
        ], $arrData));
    }

    public static function createContactFormSentNotificationMessageAdmin(int $gatewayId, string $gatewayType, int $pid, ?array $arrData = []): Message
    {
        return self::createNotification($gatewayId, $gatewayType, $pid, array_merge([
            'title' => $GLOBALS['TL_LANG']['WEMSG']['FORMCONTACT']['INSTALL_GENERAL']['titleNotificationGatewayMessageAdmin'],
        ], $arrData));
    }
}
