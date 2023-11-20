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

namespace WEM\SmartgearBundle\Migrations\V1\Y0\Z21\M202310121049;

use Doctrine\DBAL\Connection;
use NotificationCenter\Model\Language as NotificationLanguageModel;
use NotificationCenter\Model\Message as NotificationMessageModel;
use NotificationCenter\Model\Notification as NotificationModel;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Analyzer\Htaccess;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\Migration\Result;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Classes\Version\Comparator as VersionComparator;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Migrations\V1\Y0\Z0\MigrationAbstract;

class Migration extends MigrationAbstract
{
    protected $name = 'Smargear update to v1.0.21';
    protected $description = 'Set Smartgear to version 1.0.21';
    protected $version = '1.0.21';
    protected $translation_key = 'WEMSG.MIGRATIONS.V1_0_21_M202310121049';
    /** @var Htaccess */
    protected $htaccessAnalyzer;

    public function __construct(
        Connection $connection,
        TranslatorInterface $translator,
        CoreConfigurationManager $coreConfigurationManager,
        VersionComparator $versionComparator,
        Htaccess $htaccessAnalyzer
    ) {
        parent::__construct($connection, $translator, $coreConfigurationManager, $versionComparator);
        $this->htaccessAnalyzer = $htaccessAnalyzer;
    }

    public function shouldRun(): Result
    {
        $result = parent::shouldRun();

        if (Result::STATUS_SHOULD_RUN !== $result->getStatus()) {
            if (!$this->htaccessAnalyzer->hasRedirectToWwwAndHttps_OLD()) {
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
            /** @var CoreConfig */
            $coreConfig = $this->coreConfigurationManager->load();

            $coreConfig->setSgVersion($this->version);

            // if old system in place
            // disable old system
            // enable new one
            if ($this->htaccessAnalyzer->hasRedirectToWwwAndHttps_OLD()) {
                $this->htaccessAnalyzer->disableRedirectToWwwAndHttps_OLD();
                $this->htaccessAnalyzer->enableRedirectToWwwAndHttps();
            }

            $this->coreConfigurationManager->save($coreConfig);

            $result
                ->setStatus(Result::STATUS_SUCCESS)
                ->addLog($this->translator->trans($this->buildTranslationKey('done'), [], 'contao_default'))
            ;
        } catch (\Exception $e) {
            $result
                ->setStatus(Result::STATUS_FAIL)
                ->addLog($e->getMessage())
            ;
        }

        return $result;
    }

    protected function createNotificationSupportGatewayNotification(): NotificationModel
    {
        /** @var CoreConfig */
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
        /** @var CoreConfig */
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
        /** @var CoreConfig */
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
        /** @var CoreConfig */
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
        /** @var CoreConfig */
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
