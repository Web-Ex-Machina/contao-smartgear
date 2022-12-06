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
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Api\Airtable\V0\Api as AirtableApi;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Config\Component\Core as CoreConfig;
use WEM\SmartgearBundle\Exceptions\File\NotFound;

class AnalyticsExternal extends BackendModule
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'be_wem_sg_dashboard_analytics_external';
    protected $strId = 'wem_sg_dashboard_analytics_external';
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

        // $hostingInfos = $this->airtableApi->getHostingInformations($_SERVER['SERVER_NAME']);
        $hostingInfos = $this->airtableApi->getHostingInformations('altrad.com');

        $this->Template->allowed_space = $hostingInfos['allowed_space'];

        $arrInvoices = [];
        foreach ($hostingInfos['invoices_ids'] as $index => $id) {
            $arrInvoices[] = [
                'id' => $id,
                'date' => $hostingInfos['invoices_dates'][$index],
                'price' => $hostingInfos['invoices_prices'][$index],
                'url' => $hostingInfos['invoices_urls'][$index],
            ];
        }

        $this->Template->invoices = $arrInvoices;

        $this->Template->title = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSEXTERNAL.title', [], 'contao_default');

        $this->Template->informationsTitle = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSEXTERNAL.informationsTitle', [], 'contao_default');
        $this->Template->birthdayLabel = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSEXTERNAL.birthdayLabel', [], 'contao_default');
        $this->Template->birthday = $hostingInfos['birthday'];

        $this->Template->invoicesTitle = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSEXTERNAL.invoicesTitle', [], 'contao_default');
        $this->Template->invoiceDateHeader = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSEXTERNAL.invoiceDateHeader', [], 'contao_default');
        $this->Template->invoicePriceHeader = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSEXTERNAL.invoicePriceHeader', [], 'contao_default');
        $this->Template->invoiceUrlHeader = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSEXTERNAL.invoiceUrlHeader', [], 'contao_default');
        $this->Template->invoiceUrlTitle = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSEXTERNAL.invoiceUrlTitle', [], 'contao_default');
    }
}
