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

use Contao\System;
use NotificationCenter\Model\Language;
use WEM\SmartgearBundle\Classes\Util;

class NcNotificationMessageLanguageUtil
{
    /**
     * Shortcut for article creation.
     */
    public static function createNotification(int $pid, string $language, bool $fallback, ?array $arrData = []): Language
    {
        // Create the article
        $objNotificationMessageLanguage = isset($arrData['id']) ? Language::findById($arrData['id']) ?? new Language() : new Language();
        $objNotificationMessageLanguage->tstamp = time();
        $objNotificationMessageLanguage->pid = $pid;
        $objNotificationMessageLanguage->language = $language;
        $objNotificationMessageLanguage->fallback = $fallback;

        // Now we get the default values, get the arrData table
        if ($arrData !== null && $arrData !== []) {
            foreach ($arrData as $k => $v) {
                $objNotificationMessageLanguage->$k = $v;
            }
        }

        $objNotificationMessageLanguage->save();

        // Return the model
        return $objNotificationMessageLanguage;
    }

    public static function createSupportFormNotificationMessageUserLanguage(int $pid, string $language, bool $fallback, ?array $arrData = []): Language
    {
        $strText = file_get_contents(sprintf('%s/bundles/wemsmartgear/examples/dashboard/%s/ticket_mail_user.html', Util::getPublicOrWebDirectory(), $language));

        $htmlDecoder = System::getContainer()->get('contao.string.html_decoder');

        return self::createNotification($pid, $language, $fallback, array_merge([
            'recipients' => '##sg_owner_email##',
            'gateway_type' => 'email',
            // 'email_sender_name' => $config->getSgWebsiteTitle(),
            'email_sender_name' => '##email_sender_name##',
            'email_sender_address' => '##sg_owner_email##',
            'email_subject' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['subjectNotificationSupportGatewayMessageLanguageUser'],
            'email_mode' => 'textAndHtml',
            'email_text' => $htmlDecoder->htmlToPlainText($strText, false),
            'email_html' => $strText,
            'attachment_tokens' => '##ticket_file##',
            'published' => 1,
        ], $arrData));
    }

    public static function createSupportFormNotificationMessageAdminLanguage(int $pid, string $language, bool $fallback, ?array $arrData = []): Language
    {
        $strText = file_get_contents(sprintf('%s/bundles/wemsmartgear/examples/dashboard/%s/ticket_mail_admin.html', Util::getPublicOrWebDirectory(), $language));

        $htmlDecoder = System::getContainer()->get('contao.string.html_decoder');

        return self::createNotification($pid, $language, $fallback, array_merge([
            'recipients' => '##support_email##',
            'gateway_type' => 'email',
            // 'email_sender_name' => $config->getSgWebsiteTitle(),
            'email_sender_name' => '##email_sender_name##',
            'email_sender_address' => '##sg_owner_email##',
            // 'email_subject' => $this->translator->trans('WEMSG.INSTALL.WEBSITE.subjectNotificationSupportGatewayMessageLanguageAdmin', [$config->getSgWebsiteTitle()], 'contao_default'),
            'email_subject' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['subjectNotificationSupportGatewayMessageLanguageAdmin'],
            'email_mode' => 'textAndHtml',
            'email_text' => $htmlDecoder->htmlToPlainText($strText, false),
            'email_html' => $strText,
            'email_replyTo' => '##sg_owner_email##',
            'attachment_tokens' => '##ticket_file##',
            'published' => 1,
        ], $arrData));
    }

    public static function createContactFormSentNotificationMessageUserLanguage(int $pid, string $formTitle, string $websiteTitle, string $language, bool $fallback, ?array $arrData = []): Language
    {
        $strText = file_get_contents(sprintf('%s/bundles/wemsmartgear/examples/formContact/%s/user_form.html', Util::getPublicOrWebDirectory(), $language));

        $htmlDecoder = System::getContainer()->get('contao.string.html_decoder');

        return self::createNotification($pid, $language, $fallback, array_merge([
            'recipients' => '##form_email##',
            'gateway_type' => 'email',
            // 'email_sender_name' => $config->getSgWebsiteTitle(),
            'email_sender_name' => '##email_sender_name##',
            'email_sender_address' => '##admin_email##',
            'email_subject' => sprintf($GLOBALS['TL_LANG']['WEMSG']['FORMCONTACT']['INSTALL_GENERAL']['subjectNotificationGatewayMessageLanguageUser'], $websiteTitle, $formTitle),
            'email_mode' => 'textAndHtml',
            'email_text' => $htmlDecoder->htmlToPlainText($strText, false),
            'email_html' => $strText,
            'published' => 1,
            'fallback' => 1,
        ], $arrData));
    }

    public static function createContactFormSentNotificationMessageAdminLanguage(int $pid, string $formTitle, string $websiteTitle, string $ownerEmail, string $language, bool $fallback, ?array $arrData = []): Language
    {
        $strText = file_get_contents(sprintf('%s/bundles/wemsmartgear/examples/formContact/%s/admin_form.html', Util::getPublicOrWebDirectory(), $language));

        $htmlDecoder = System::getContainer()->get('contao.string.html_decoder');

        return self::createNotification($pid, $language, $fallback, array_merge([
            'recipients' => '##admin_email##',
            'gateway_type' => 'email',
            // 'email_sender_name' => $config->getSgWebsiteTitle(),
            'email_sender_name' => '##email_sender_name##',
            'email_sender_address' => $ownerEmail,
            'email_subject' => sprintf($GLOBALS['TL_LANG']['WEMSG']['FORMCONTACT']['INSTALL_GENERAL']['subjectNotificationGatewayMessageLanguageAdmin'], $websiteTitle, $formTitle),
            'email_mode' => 'textAndHtml',
            'email_text' => $htmlDecoder->htmlToPlainText($strText, false),
            'email_html' => $strText,
            'email_replyTo' => '##form_email##',
            'published' => 1,
            'fallback' => 1,
        ], $arrData));
    }
}
