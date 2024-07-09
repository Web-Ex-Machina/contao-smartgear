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

namespace WEM\SmartgearBundle\Migrations\V1\Y0\Z21\M202308181214;

use Contao\CoreBundle\String\HtmlDecoder;
use Doctrine\DBAL\Connection;
use WEM\SmartgearBundle\Model\NotificationCenter\Language as NotificationLanguageModel;
use WEM\SmartgearBundle\Model\NotificationCenter\Message as NotificationMessageModel;
use WEM\SmartgearBundle\Model\NotificationCenter\Notification as NotificationModel;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\Migration\Result;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Classes\Version\Comparator as VersionComparator;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Exceptions\File\NotFound as FileNotFoundException;
use WEM\SmartgearBundle\Migrations\V1\Y0\Z0\MigrationAbstract;

class Migration extends MigrationAbstract
{
    protected string $name = 'Smargear update to v1.0.21';

    protected string $description = 'Set Smartgear to version 1.0.21';

    protected string $version = '1.0.21';

    protected string $translation_key = 'WEMSG.MIGRATIONS.V1_0_21_M202308181214';

    public function __construct(
        Connection $connection,
        TranslatorInterface $translator,
        CoreConfigurationManager $coreConfigurationManager,
        VersionComparator $versionComparator,
        protected HtmlDecoder $htmlDecoder
    ) {
        parent::__construct($connection, $translator, $coreConfigurationManager, $versionComparator);
    }

    public function shouldRun(): Result
    {
        $result = parent::shouldRun();

        if (Result::STATUS_SHOULD_RUN !== $result->getStatus()) {
            try {
                $coreConfig = $this->coreConfigurationManager->load();
            } catch (FileNotFoundException) {
                return $result;
            }

            if (NotificationModel::findOneById($coreConfig->getSgNotificationSupport())
                && NotificationMessageModel::findOneById($coreConfig->getSgNotificationSupportMessageUser())
                && NotificationMessageModel::findOneById($coreConfig->getSgNotificationSupportMessageAdmin())
                && NotificationLanguageModel::findOneById($coreConfig->getSgNotificationSupportMessageUserLanguage())
                && NotificationLanguageModel::findOneById($coreConfig->getSgNotificationSupportMessageAdminLanguage())
            ) {
                return $result;
            }
        }

        $result
            ->setStatus(Result::STATUS_SHOULD_RUN)
            ->addLog($this->translator->trans('WEMSG.MIGRATIONS.shouldBeRun', [], 'contao_default'))
        ;

        return $result;
    }

    public function do(): Result
    {
        $result = $this->shouldRun();
        if (Result::STATUS_SHOULD_RUN !== $result->getStatus()) {
            return $result;
        }

        try {
            /** @var CoreConfig $config */
            // $coreConfig = $this->coreConfigurationManager->load();

            // $coreConfig->setSgVersion($this->version);

            // create the notification for support ticket creation
            $notificationSupport = $this->createNotificationSupportGatewayNotification();
            $notificationSupportGatewayMessages = $this->createNotificationSupportGatewayMessages($notificationSupport);
            $notificationSupportGatewayMessagesLanguages = $this->createNotificationSupportGatewayMessagesLanguages($notificationSupportGatewayMessages);

            // $this->coreConfigurationManager->save($coreConfig);

            $this->updateConfigurationsVersion($this->version);

            $result
                ->setStatus(Result::STATUS_SUCCESS)
                ->addLog($this->translator->trans($this->buildTranslationKey('done'), [], 'contao_default'))
            ;
        } catch (\Exception $exception) {
            $result
                ->setStatus(Result::STATUS_FAIL)
                ->addLog($exception->getMessage())
            ;
        }

        return $result;
    }

    protected function createNotificationSupportGatewayNotification(): NotificationModel
    {
        /** @var CoreConfig $config */
        $config = $this->coreConfigurationManager->load();

        $nc = NotificationModel::findOneById($config->getSgNotificationSupport()) ?? new NotificationModel();
        $nc->tstamp = time();
        $nc->title = $this->translator->trans('WEMSG.INSTALL.WEBSITE.titleNotificationSupportGatewayNotification', [], 'contao_default');
        $nc->type = 'ticket_creation';
        $nc->save();

        $config->setSgNotificationSupport((int) $nc->id);
        $this->coreConfigurationManager->save($config);

        return $nc;
    }

    protected function createNotificationSupportGatewayMessagesUser(NotificationModel $gateway): NotificationMessageModel
    {
        /** @var CoreConfig $config */
        $config = $this->coreConfigurationManager->load();

        $nm = NotificationMessageModel::findOneById($config->getSgNotificationSupportMessageUser()) ?? new NotificationMessageModel();
        $nm->pid = $gateway->id;
        $nm->gateway = $config->getSgNotificationGatewayEmail();
        $nm->gateway_type = 'email';
        $nm->tstamp = time();
        $nm->title = $this->translator->trans('WEMSG.INSTALL.WEBSITE.titleNotificationSupportGatewayMessageUser', [], 'contao_default');
        $nm->published = 1;
        $nm->save();

        $config->setSgNotificationSupportMessageUser((int) $nm->id);
        $this->coreConfigurationManager->save($config);

        return $nm;
    }

    protected function createNotificationSupportGatewayMessagesAdmin(NotificationModel $gateway): NotificationMessageModel
    {
        /** @var CoreConfig $config */
        $config = $this->coreConfigurationManager->load();

        $nm = NotificationMessageModel::findOneById($config->getSgNotificationSupportMessageAdmin()) ?? new NotificationMessageModel();
        $nm->pid = $gateway->id;
        $nm->gateway = $config->getSgNotificationGatewayEmail();
        $nm->gateway_type = 'email';
        $nm->tstamp = time();
        $nm->title = $this->translator->trans('WEMSG.INSTALL.WEBSITE.titleNotificationSupportGatewayMessageAdmin', [], 'contao_default');
        $nm->published = 1;
        $nm->save();

        $config->setSgNotificationSupportMessageAdmin((int) $nm->id);
        $this->coreConfigurationManager->save($config);

        return $nm;
    }

    protected function createNotificationSupportGatewayMessages(NotificationModel $gateway): array
    {
        return [
            'user' => $this->createNotificationSupportGatewayMessagesUser($gateway),
            'admin' => $this->createNotificationSupportGatewayMessagesAdmin($gateway),
        ];
    }

    protected function createNotificationSupportGatewayMessagesLanguagesUser(NotificationMessageModel $gatewayMessage): NotificationLanguageModel
    {
        /** @var CoreConfig $config */
        $config = $this->coreConfigurationManager->load();

        $strText = file_get_contents(sprintf('%s/bundles/wemsmartgear/examples/dashboard/%s/ticket_mail_user.html', Util::getPublicOrWebDirectory(), \Contao\BackendUser::getInstance()->language));

        $nl = NotificationLanguageModel::findOneById($config->getSgNotificationSupportMessageUserLanguage()) ?? new NotificationLanguageModel();
        $nl->pid = $gatewayMessage->id;
        $nl->tstamp = time();
        $nl->language = \Contao\BackendUser::getInstance()->language;
        $nl->fallback = 1;
        $nl->recipients = '##sg_owner_email##';
        $nl->gateway_type = 'email';
        $nl->email_sender_name = $config->getSgWebsiteTitle();
        $nl->email_sender_address = '##sg_owner_email##';
        $nl->email_subject = $this->translator->trans('WEMSG.INSTALL.WEBSITE.subjectNotificationSupportGatewayMessageLanguageUser', [$config->getSgWebsiteTitle()], 'contao_default');
        $nl->email_mode = 'textAndHtml';
        $nl->email_text = $this->htmlDecoder->htmlToPlainText($strText, false);
        $nl->email_html = $strText;
        $nl->attachment_tokens = '##ticket_file##';
        $nl->save();

        $config->setSgNotificationSupportMessageUserLanguage((int) $nl->id);
        $this->coreConfigurationManager->save($config);

        return $nl;
    }

    protected function createNotificationSupportGatewayMessagesLanguagesAdmin(NotificationMessageModel $gatewayMessage): NotificationLanguageModel
    {
        /** @var CoreConfig $config */
        $config = $this->coreConfigurationManager->load();

        $strText = file_get_contents(sprintf('%s/bundles/wemsmartgear/examples/dashboard/%s/ticket_mail_admin.html', Util::getPublicOrWebDirectory(), \Contao\BackendUser::getInstance()->language));

        $nl = NotificationLanguageModel::findOneById($config->getSgNotificationSupportMessageAdminLanguage()) ?? new NotificationLanguageModel();
        $nl->pid = $gatewayMessage->id;
        $nl->tstamp = time();
        $nl->language = \Contao\BackendUser::getInstance()->language;
        $nl->fallback = 1;
        $nl->recipients = '##support_email##';
        $nl->gateway_type = 'email';
        $nl->email_sender_name = $config->getSgWebsiteTitle();
        $nl->email_sender_address = '##sg_owner_email##';
        $nl->email_subject = $this->translator->trans('WEMSG.INSTALL.WEBSITE.subjectNotificationSupportGatewayMessageLanguageUser', [$config->getSgWebsiteTitle()], 'contao_default');
        $nl->email_mode = 'textAndHtml';
        $nl->email_text = $this->htmlDecoder->htmlToPlainText($strText, false);
        $nl->email_html = $strText;
        $nl->email_replyTo = '##sg_owner_email##';
        $nl->attachment_tokens = '##ticket_file##';
        $nl->save();

        $config->setSgNotificationSupportMessageAdminLanguage((int) $nl->id);
        $this->coreConfigurationManager->save($config);

        return $nl;
    }

    protected function createNotificationSupportGatewayMessagesLanguages(array $gatewayMessages): array
    {
        return [
            'user' => $this->createNotificationSupportGatewayMessagesLanguagesUser($gatewayMessages['user']),
            'admin' => $this->createNotificationSupportGatewayMessagesLanguagesAdmin($gatewayMessages['admin']),
        ];
    }
}
