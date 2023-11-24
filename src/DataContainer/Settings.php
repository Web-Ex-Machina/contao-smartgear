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

use Contao\Config;
use Contao\DataContainer;
use NotificationCenter\Model\Gateway;
use NotificationCenter\Model\Notification;
use WEM\SmartgearBundle\Classes\Utils\Notification\NcNotificationMessageLanguageUtil;
use WEM\SmartgearBundle\Classes\Utils\Notification\NcNotificationMessageUtil;
use WEM\SmartgearBundle\Classes\Utils\Notification\NcNotificationUtil;

class Settings
{
    public function onsubmitCallback(DataContainer $dc): void
    {
        if (Config::get('wem_sg_support_form_enabled')) {
            if (Config::get('wem_sg_support_form_gateway') && !Config::get('wem_sg_support_form_notification')) {
                // create support notification
                if ($objGateway = Gateway::findByPk(Config::get('wem_sg_support_form_gateway'))) {
                    $objNcNotification = NcNotificationUtil::createSupportFormNotification();

                    $objNcNotificationMessageUser = NcNotificationMessageUtil::createSupportFormNotificationMessageUser((int) $objGateway->id, 'email', (int) $objNcNotification->id);
                    $objNcNotificationMessageUserLanguage = NcNotificationMessageLanguageUtil::createSupportFormNotificationMessageUserLanguage((int) $objNcNotificationMessageUser->id, 'fr', true);

                    $objNcNotificationMessageAdmin = NcNotificationMessageUtil::createSupportFormNotificationMessageAdmin((int) $objGateway->id, 'email', (int) $objNcNotification->id);
                    $objNcNotificationMessageAdminLanguage = NcNotificationMessageLanguageUtil::createSupportFormNotificationMessageAdminLanguage((int) $objNcNotificationMessageAdmin->id, 'fr', true);

                    $objConfig = Config::getInstance();
                    $objConfig->add("\$GLOBALS['TL_CONFIG']['wem_sg_support_form_notification']", $objNcNotification->id);
                    $objConfig->save();
                }
            }
        }
    }

    public function airtableApiKeyReadSaveCallback($value, DataContainer $dc)
    {
        $encryptionService = \Contao\System::getContainer()->get('plenta.encryption');

        return $encryptionService->encrypt($value);
    }

    public function airtableApiKeyReadLoadCallback($value, DataContainer $dc)
    {
        $encryptionService = \Contao\System::getContainer()->get('plenta.encryption');

        return $encryptionService->decrypt($value);
    }

    public function airtableApiKeyWriteSaveCallback($value, DataContainer $dc)
    {
        $encryptionService = \Contao\System::getContainer()->get('plenta.encryption');

        return $encryptionService->encrypt($value);
    }

    public function airtableApiKeyWriteLoadCallback($value, DataContainer $dc)
    {
        $encryptionService = \Contao\System::getContainer()->get('plenta.encryption');

        return $encryptionService->decrypt($value);
    }

    public function supportFormNotificationOptionsCallback(DataContainer $dc)
    {
        $arrOptions = [];

        $notifications = Notification::findBy('type', 'ticket_creation');
        if ($notifications) {
            while ($notifications->next()) {
                $arrOptions[$notifications->id] = $notifications->title;
            }
        }

        return $arrOptions;
    }

    public function supportFormGatewayOptionsCallback(DataContainer $dc)
    {
        $arrOptions = [];

        $gateways = Gateway::findAll();
        if ($gateways) {
            while ($gateways->next()) {
                $arrOptions[$gateways->id] = $gateways->title.' ('.$gateways->type.')';
            }
        }

        return $arrOptions;
    }
}
