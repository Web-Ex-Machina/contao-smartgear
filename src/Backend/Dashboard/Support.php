<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2022 Web ex Machina
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
use Contao\File;
use Contao\FileUpload;
use Contao\Folder;
use Contao\Input;
use Contao\Message;
use Contao\RequestToken;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Api\Airtable\V0\Api as AirtableApi;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Exceptions\File\NotFound;

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
        } catch (NotFound $e) {
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
                    $this->ticketCreate(Input::post('subject', true), Input::post('url'), Input::post('message', true), Input::post('mail'), $_FILES['files'] ?? []);

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

    protected function ticketCreate(string $subject, string $url, string $message, string $mail, array $screenshotFile): void
    {
        try {
            /** @var CoreConfig */
            $config = $this->configurationManager->load();
        } catch (NotFound $e) {
            return;
        }

        // retrieve client ID
        $hostingInformations = $this->airtableApi->getHostingInformations($config->getSgOwnerDomain());
        $clientId = $hostingInformations['client_id'];

        // save screenshot as file
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
            $fileUrl = $config->getSgOwnerDomain().$objFile->path;
        }

        $this->airtableApi->createTicket($clientId, $subject, $url, $message, $mail, $fileUrl);
    }

    protected function getSupportMail(): string
    {
        try {
            /** @var CoreConfig */
            $config = $this->configurationManager->load();
        } catch (NotFound $e) {
            return '';
        }
        $mail = 'support.smartgear@webexmachina.fr';
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
        try {
            /** @var CoreConfig */
            $config = $this->configurationManager->load();
        } catch (NotFound $e) {
            return '';
        }

        $objTemplate = new BackendTemplate('be_wem_sg_dashboard_support_form');
        $objTemplate->title = $this->translator->trans('WEMSG.DASHBOARD.SUPPORT.formTitle', [], 'contao_default');
        $objTemplate->moduleId = $this->strId;

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
