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

namespace WEM\SmartgearBundle\Backend\Dashboard;

use Contao\BackendModule;
use Contao\BackendTemplate;
use Contao\BackendUser;
use Contao\Config;
use Contao\File;
use Contao\FileUpload;
use Contao\Folder;
use Contao\Input;
use Contao\Message;
use Contao\PageModel;
use Contao\RequestToken;
use Exception;
use NotificationCenter\Model\Notification as NotificationModel;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Api\Airtable\V0\Api as AirtableApi;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Exceptions\File\NotFound;
use WEM\SmartgearBundle\Model\Configuration\Configuration;

class Support extends BackendModule
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'be_wem_sg_dashboard_support';
    protected $strId = 'wem_sg_dashboard_support';
    /** @var TranslatorInterface */
    protected $translator;
    /** @var ConfigurationManager */
    protected $configurationManager;
    /** @var AirtableApi */
    protected $airtableApi;

    /**
     * Initialize the object.
     */
    public function __construct(
        TranslatorInterface $translator,
        ConfigurationManager $configurationManager,
        AirtableApi $airtableApi
    ) {
        parent::__construct();
        $this->translator = $translator;
        $this->configurationManager = $configurationManager;
        $this->airtableApi = $airtableApi;
    }

    public function generate(): string
    {
        if (Input::post('TL_WEM_AJAX') && $this->strId === Input::post('wem_module')) {
            $this->processAjaxRequest(Input::post('action'));
        }

        return parent::generate();
    }

    public function compile(): void
    {
        try {
            /** @var CoreConfig */
            $config = $this->configurationManager->load();
        } catch (NotFound) {
            return;
        }

        $this->Template->title = $this->translator->trans('WEMSG.DASHBOARD.SUPPORT.title', [], 'contao_default');
        $this->Template->help = $this->translator->trans('WEMSG.DASHBOARD.SUPPORT.helpText', [], 'contao_default');

        $this->Template->supportMail = $this->getSupportMail();
        $this->Template->supportForm = $this->getSupportForm();
    }

    /**
     * Process AJAX actions.
     *
     * @param [String] $strAction - Ajax action wanted
     *
     * @return string - Ajax response, as String or JSON
     */
    public function processAjaxRequest($strAction)
    {
        try {
            switch ($strAction) {
                case 'ticketCreate':
                    $this->ticketCreate(Input::post('domain', true), Input::post('subject', true), Input::post('url'), Input::post('message', true), Input::post('mail'), $_FILES['files'] ?? []);

                    $arrResponse = ['status' => 'success', 'toastr' => ['status' => 'success', 'msg' => $this->translator->trans('WEMSG.DASHBOARD.SUPPORT.formSendSuccess', [], 'contao_default')]];
                break;
            }
        } catch (Exception $e) {
            $arrResponse = ['status' => 'error', 'msg' => $e->getMessage(), 'trace' => $e->getTrace()];
        }

        // Add Request Token to JSON answer and return
        $arrResponse['rt'] = RequestToken::get();
        echo json_encode($arrResponse);
        exit;
    }

    public function getStrId(): string
    {
        return $this->strId;
    }

    protected function ticketCreate(string $domain, string $subject, string $url, string $message, string $mail, array $screenshotFile): void
    {
        try {
            /** @var CoreConfig */
            $config = $this->configurationManager->load();
        } catch (NotFound) {
            return;
        }

        if (!Config::get('wem_sg_support_form_enabled')) {
            return;
        }

        // retrieve client ID
        // $arrDomains = Util::getRootPagesDomains();
        // $hostingInformations = $this->airtableApi->getHostingInformations($arrDomains);
        // $clientId = null;
        // $clientRef = null;
        // if (!empty($hostingInformations)
        // && \array_key_exists($domain, $hostingInformations)
        // && !empty($hostingInformations[$domain]['client_reference'])
        // ) {
        //     $supportClientInformations = $this->airtableApi->getSupportClientInformationsSingleClientRef($hostingInformations[$domain]['client_reference'][0]);
        //     $clientId = $supportClientInformations['id'];
        //     $clientRef = $hostingInformations[$domain]['client_reference'][0];
        // }

        // // save screenshot as file
        $objFile = null;
        $fileUrl = null;
        if (!empty($screenshotFile)) {
            $objFolder = new Folder(CoreConfig::DEFAULT_CLIENT_FILES_FOLDER.\DIRECTORY_SEPARATOR.'tickets');
            $objFolder->unprotect();
            $fileUploader = new FileUpload();
            $arrFiles = $fileUploader->uploadTo($objFolder->path);
            if ($fileUploader->hasError()) {
                throw new Exception(Message::generateUnwrapped(TL_MODE, true));
            }
            $objFile = new File($arrFiles[0]);
            // $fileUrl = $config->getSgOwnerDomain().$objFile->path;
            $fileUrl = $domain.$objFile->path;
        }

        // $this->airtableApi->createTicket($subject, $url, $message, $mail, $config->getSgVersion(), $clientId, $clientRef, $fileUrl);

        // send email
        // $notification = NotificationModel::findByPk((int) $config->getSgNotificationSupport());
        $notification = NotificationModel::findByPk((int) Config::get('wem_sg_support_form_notification'));
        if (!$notification) {
            return;
        }

        $objPage = PageModel::findOneBy(['type = ?', 'dns = ?'], ['root', $domain]);

        $objConfiguration = $objPage ? Configuration::findOneByPage($objPage) : null;

        $arrTokens = [
            'email_sender_name' => $objPage ? $objPage->title : 'N/A',
            // 'sg_owner_email' => $config->getSgOwnerEmail(),
            'sg_owner_email' => $objPage ? ($objPage->adminEmail ?: (
                        $objConfiguration ? ($objConfiguration->legal_owner_email ?: Config::get('adminEmail')) : Config::get('adminEmail'))) : Config::get('adminEmail'),
            // 'sg_owner_name' => $config->getSgOwnerName(),
            'sg_owner_name' => $objConfiguration ? $objConfiguration->getLegalOwnerName() : Config::get('adminEmail'),
            'support_email' => 'support@webexmachina.fr',
            'ticket_domain' => $domain && \strlen($domain) > 0 ? $domain : 'N/A',
            'ticket_subject' => $subject,
            'ticket_url' => $url,
            'ticket_message' => $message,
            'ticket_file' => $objFile ? $objFile->path : '',
        ];

        $notification->send($arrTokens);
    }

    protected function getSupportMail(): string
    {
        try {
            /** @var CoreConfig */
            $config = $this->configurationManager->load();
        } catch (NotFound) {
            return '';
        }
        $mail = 'support@webexmachina.fr';
        $urlMailto = '';
        $urlMailtoParams = [
            'cc' => $config->getSgOwnerEmail(),
            'subject' => $this->translator->trans('WEMSG.DASHBOARD.SUPPORT.mailSubject', [], 'contao_default'),
            'body' => str_replace("\r\n", '%0D%0A', $this->translator->trans('WEMSG.DASHBOARD.SUPPORT.mailContent', [
                BackendUser::getInstance()->name ?? $config->getSgOwnerName(), ], 'contao_default')),
        ];
        foreach ($urlMailtoParams as $key => $value) {
            $urlMailto .= '&'.$key.'='.$value;
        }
        $urlMailto = 'mailto:'.$mail.'?'.substr($urlMailto, 1);

        $objTemplate = new BackendTemplate('be_wem_sg_dashboard_support_mail');
        $objTemplate->title = $this->translator->trans('WEMSG.DASHBOARD.SUPPORT.mailTitle', [], 'contao_default');
        $objTemplate->mail = $mail;
        $objTemplate->url = $urlMailto;
        $objTemplate->mailLinkTitle = $this->translator->trans('WEMSG.DASHBOARD.SUPPORT.mailLinkTitle', [], 'contao_default');

        return $objTemplate->parse();
    }

    protected function getSupportForm(): string
    {
        if (!Config::get('wem_sg_support_form_enabled')) {
            return '';
        }
        try {
            /** @var CoreConfig */
            $config = $this->configurationManager->load();
        } catch (NotFound) {
            return '';
        }

        $arrDomains = Util::getRootPagesDomains();
        $hostingInformations = $this->airtableApi->getHostingInformations($arrDomains);
        $arrDomainsHavingClientRef = [];
        if (!empty($hostingInformations)) {
            foreach ($hostingInformations as $domain => $hostnameHostingInformations) {
                if ($hostnameHostingInformations['client_reference'] ?? null
                && \is_array($hostnameHostingInformations['client_reference'])
                && $hostnameHostingInformations['client_reference'][0]
                ) {
                    $arrDomainsHavingClientRef[] = $domain;
                }
            }
        }
        $arrDomainsHavingClientRef = $arrDomains;

        $objTemplate = new BackendTemplate('be_wem_sg_dashboard_support_form');
        $objTemplate->title = $this->translator->trans('WEMSG.DASHBOARD.SUPPORT.formTitle', [], 'contao_default');
        $objTemplate->moduleId = $this->strId;

        $objTemplate->domainLabel = $this->translator->trans('WEMSG.DASHBOARD.SUPPORT.formDomainLabel', [], 'contao_default');
        $objTemplate->domainHelp = $this->translator->trans('WEMSG.DASHBOARD.SUPPORT.formDomainHelp', [], 'contao_default');
        $objTemplate->domains = $arrDomainsHavingClientRef;

        $objTemplate->subjectLabel = $this->translator->trans('WEMSG.DASHBOARD.SUPPORT.formSubjectLabel', [], 'contao_default');
        $objTemplate->subjectHelp = $this->translator->trans('WEMSG.DASHBOARD.SUPPORT.formSubjectHelp', [], 'contao_default');

        $objTemplate->mail = $config->getSgOwnerEmail();
        $objTemplate->mailLabel = $this->translator->trans('WEMSG.DASHBOARD.SUPPORT.formMailLabel', [], 'contao_default');
        $objTemplate->mailHelp = $this->translator->trans('WEMSG.DASHBOARD.SUPPORT.formMailHelp', [], 'contao_default');

        $objTemplate->url = $config->getSgOwnerDomain();
        $objTemplate->urlLabel = $this->translator->trans('WEMSG.DASHBOARD.SUPPORT.formUrlLabel', [], 'contao_default');
        $objTemplate->urlHelp = $this->translator->trans('WEMSG.DASHBOARD.SUPPORT.formUrlHelp', [], 'contao_default');

        $objTemplate->messageLabel = $this->translator->trans('WEMSG.DASHBOARD.SUPPORT.formMessageLabel', [], 'contao_default');
        $objTemplate->messageHelp = $this->translator->trans('WEMSG.DASHBOARD.SUPPORT.formMessageHelp', [], 'contao_default');

        $objTemplate->screenshotLabel = $this->translator->trans('WEMSG.DASHBOARD.SUPPORT.formScreenshotLabel', [], 'contao_default');
        $objTemplate->screenshotHelp = $this->translator->trans('WEMSG.DASHBOARD.SUPPORT.formScreenshotHelp', [], 'contao_default');
        $objTemplate->screenshotPlaceholder = $this->translator->trans('WEMSG.DASHBOARD.SUPPORT.formScreenshotPlaceholder', [], 'contao_default');

        $objTemplate->sendLabel = $this->translator->trans('WEMSG.DASHBOARD.SUPPORT.formSendLabel', [], 'contao_default');

        return $objTemplate->parse();
    }
}
