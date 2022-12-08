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
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Api\Airtable\V0\Api as AirtableApi;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Config\Component\Core as CoreConfig;
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
            'body' => str_replace("\r\n", '%0D%0A', $this->translator->trans('WEMSG.DASHBOARD.SUPPORT.mailContent', [$config->getSgOwnerName()], 'contao_default')),
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
        $objTemplate = new BackendTemplate('be_wem_sg_dashboard_support_form');
        $objTemplate->title = $this->translator->trans('WEMSG.DASHBOARD.SUPPORT.formTitle', [], 'contao_default');

        return $objTemplate->parse();
    }
}
