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
use Contao\CalendarEventsModel;
use Contao\Database;
use Contao\Date;
use Contao\FilesModel;
use Contao\MemberModel;
use Contao\NewsModel;
use Contao\System;
use Exception;
use Psr\Log\LogLevel;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Api\Airtable\V0\Api as AirtableApi;
use WEM\SmartgearBundle\Classes\CacheFileManager;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Exceptions\File\NotFound;
use WEM\SmartgearBundle\Model\Backup;

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

        $arrDomains = Util::getRootPagesDomains();
        $hostingInfos = $this->airtableApi->getHostingInformations($arrDomains);

        $this->Template->title = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSEXTERNAL.title', [], 'contao_default');

        $this->Template->invoices = $this->getInvoices($hostingInfos);
        $this->Template->diskUsage = $this->getDiskUsageBlock($hostingInfos);
        $this->Template->contentAnalytics = $this->getContentAnalytics($hostingInfos);
    }

    protected function getInvoices(array $hostingInfos): string
    {
        $blnAirtableClientFound = false;
        $arrBirthdays = [];
        $arrInvoices = [];
        foreach ($hostingInfos as $domain => $hostnameHostingInfos) {
            foreach ($hostnameHostingInfos['invoices_ids'] as $index => $id) {
                $arrInvoices[] = [
                    'id' => $id,
                    'date' => $hostnameHostingInfos['invoices_dates'][$index],
                    'price' => $hostnameHostingInfos['invoices_prices'][$index],
                    'url' => $hostnameHostingInfos['invoices_urls'][$index],
                ];
                $blnAirtableClientFound = $blnAirtableClientFound || !empty($hostnameHostingInfos['client_id']);
                $arrBirthdays[$domain] = (new Date($hostnameHostingInfos['birthday'], 'Y-m-d'))->date;
            }
        }

        $arrInvoices = array_unique($arrInvoices);

        $objTemplate = new BackendTemplate('be_wem_sg_dashboard_analytics_external_invoices');
        $objTemplate->invoicesTitle = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSEXTERNAL.invoicesTitle', [], 'contao_default');
        $objTemplate->airtableClientFound = $blnAirtableClientFound;
        $objTemplate->msgAirtableClientNotFound = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSEXTERNAL.msgAirtableClientNotFound', [], 'contao_default');

        $objTemplate->birthdayLabel = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSEXTERNAL.birthdayLabel', [], 'contao_default');
        $objTemplate->birthday = $arrBirthdays;

        $objTemplate->invoices = $arrInvoices;
        $objTemplate->invoiceDateHeader = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSEXTERNAL.invoiceDateHeader', [], 'contao_default');
        $objTemplate->invoicePriceHeader = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSEXTERNAL.invoicePriceHeader', [], 'contao_default');
        $objTemplate->invoiceUrlHeader = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSEXTERNAL.invoiceUrlHeader', [], 'contao_default');
        $objTemplate->invoiceUrlTitle = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSEXTERNAL.invoiceUrlTitle', [], 'contao_default');

        return $objTemplate->parse();
    }

    protected function getDiskUsageBlock(array $hostingInfos): string
    {
        $diskUsage = $this->getDiskUsage();
        $dbUsage = $this->getDatabaseUsage();

        $blnAirtableClientFound = false;
        $diskSpaceAllowed = 0;
        foreach ($hostingInfos as $domain => $hostnameHostingInfos) {
            $diskSpaceAllowed += (float) $hostnameHostingInfos['allowed_space'];
            $blnAirtableClientFound = $blnAirtableClientFound || !empty($hostnameHostingInfos['client_id']);
            continue;
        }

        $diskSpaceAllowed = (int) $diskSpaceAllowed * 1024 * 1024 * 1024;
        $objTemplate = new BackendTemplate('be_wem_sg_dashboard_analytics_external_diskusage');
        $objTemplate->informationsTitle = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSEXTERNAL.informationsTitle', [], 'contao_default');
        $objTemplate->airtableClientFound = $blnAirtableClientFound;
        $objTemplate->msgAirtableClientNotFound = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSEXTERNAL.msgAirtableClientNotFound', [], 'contao_default');

        // DB usage
        $objTemplate->dbUsageLabel = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSEXTERNAL.dbUsageLabel', [], 'contao_default');
        $objTemplate->dbUsage = Util::humanReadableFilesize($dbUsage, 2);

        // Disk usage
        $objTemplate->diskUsageLabel = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSEXTERNAL.diskUsageLabel', [], 'contao_default');
        $objTemplate->diskUsage = Util::humanReadableFilesize($diskUsage, 2);
        $objTemplate->diskSpaceAllowedLabel = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSEXTERNAL.diskSpaceAllowedLabel', [], 'contao_default');

        $objTemplate->diskSpaceAllowed = Util::humanReadableFilesize($diskSpaceAllowed, 2);

        $objTemplate->diskUsagePercentLabel = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSEXTERNAL.diskUsagePercentLabel', [], 'contao_default');
        $objTemplate->diskUsagePercent = 0 !== $diskSpaceAllowed ? round($diskUsage * 100 / ($diskSpaceAllowed), 2) : 0;

        if ($objTemplate->diskUsagePercent < 75) {
            $objTemplate->diskUsageBarColor = 'green';
        } elseif ($objTemplate->diskUsagePercent < 90) {
            $objTemplate->diskUsageBarColor = 'orange';
        } else {
            $objTemplate->diskUsageBarColor = 'red';
        }

        return $objTemplate->parse();
    }

    protected function getContentAnalytics(array $hostingInfos): string
    {
        $objTemplate = new BackendTemplate('be_wem_sg_dashboard_analytics_external_contentanalytics');
        $objTemplate->contentAnalyticsTitle = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSEXTERNAL.contentAnalyticsTitle', [], 'contao_default');

        try {
            /** @var CoreConfig */
            $config = $this->configurationManager->load();

            // Core
            $objTemplate->isCoreInstalled = $config->getSgInstallComplete();
            if ($objTemplate->isCoreInstalled) {
                $objTemplate->coreNbBackups = Backup::countAll();
                $objTemplate->coreNbBackupsLabel = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSEXTERNAL.coreNbBackupsLabel', [], 'contao_default');

                $objTemplate->coreNbFiles = FilesModel::countAll();
                $objTemplate->coreNbFilesLabel = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSEXTERNAL.coreNbFilesLabel', [], 'contao_default');
            }

            // Blog
            $objTemplate->isBlogInstalled = $config->getSgInstallComplete() && $config->getSgBlog()->getSgInstallComplete();
            if ($objTemplate->isBlogInstalled) {
                $objTemplate->blogNbNews = NewsModel::countBy(['pid' => $config->getSgBlog()->getSgNewsArchive()]);
                $objTemplate->blogNbNewsLabel = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSEXTERNAL.blogNbNewsLabel', [], 'contao_default');
            }

            // Events
            $objTemplate->isEventsInstalled = $config->getSgInstallComplete() && $config->getSgEvents()->getSgInstallComplete();
            if ($objTemplate->isEventsInstalled) {
                $objTemplate->eventsNbEvents = CalendarEventsModel::countBy(['pid' => $config->getSgEvents()->getSgCalendar()]);
                $objTemplate->eventsNbEventsLabel = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSEXTERNAL.eventsNbEventsLabel', [], 'contao_default');
            }

            // Extranet
            $objTemplate->isExtranetInstalled = $config->getSgInstallComplete() && $config->getSgExtranet()->getSgInstallComplete();
            if ($objTemplate->isExtranetInstalled) {
                $objTemplate->extranetNbMembers = MemberModel::countAll();
                $objTemplate->extranetNbMembersLabel = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSEXTERNAL.extranetNbMembersLabel', [], 'contao_default');
            }
        } catch (NotFound $e) {
            // return;
        }

        return $objTemplate->parse();
    }

    protected function getDatabaseUsage(): int
    {
        $cacheManager = new CacheFileManager('assets/smartgear/dashboard_db_usage.json', 86400);

        // check for cache
        if ($cacheManager->cacheFileExists() && $cacheManager->hasValidCache()) {
            return (int) $cacheManager->retrieveFromCache()['data']['size'];
        }

        $query = sprintf('
            SELECT SUM(DATA_LENGTH) + SUM(INDEX_LENGTH) AS usage_estimate
            FROM INFORMATION_SCHEMA.tables
            WHERE table_schema = \'%s\'',
            System::getContainer()->get('database_connection')->getDatabase()
        );
        $result = Database::getInstance()->execute($query);

        if (0 === $result->count()) {
            return 0;
        }

        $size = (int) $result->fetchAllAssoc()[0]['usage_estimate'];

        // write cache
        $cacheManager->saveCacheFile(['size' => $size]);

        return $size;
    }

    protected function folderSize($dir): int
    {
        $size = 0;

        // foreach (glob(rtrim($dir, '/').'/*', \GLOB_NOSORT) as $each) {
        foreach (glob(rtrim($dir, '/').'/{*,.[!.]*,..?*}', \GLOB_BRACE | \GLOB_NOSORT) as $each) {
            $size += is_file($each) ? filesize($each) : $this->folderSize($each);
        }

        return $size;
    }

    protected function getDirectorySize($path): int
    {
        $bytestotal = 0;
        $path = realpath($path);
        if (false !== $path && '' !== $path && file_exists($path)) {
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)) as $object) {
                try {
                    $bytestotal += $object->getSize();
                } catch (Exception $e) {
                    // $logger = static::getContainer()->get('monolog.logger.contao');
                    // $logger->log(LogLevel::ERROR, 'TEXT.TO.FIND', ['contao' => new \Contao\CoreBundle\Monolog\ContaoContext('getDirectorySize', 'ERROR')]);
                }
            }
        }

        return $bytestotal;
    }

    protected function getDiskUsage(): int
    {
        $cacheManager = new CacheFileManager('assets/smartgear/dashboard_file_usage.json', 86400);
        // check for cache
        if ($cacheManager->cacheFileExists() && $cacheManager->hasValidCache()) {
            return (int) $cacheManager->retrieveFromCache()['data']['size'];
        }
        //  get disk usage
        $size = (int) $this->folderSize(realpath('../'));

        $size2 = (int) $this->getDirectorySize('../');

        $size = $size > $size2 ? $size : $size2;

        // write cache
        $cacheManager->saveCacheFile(['size' => $size]);

        return $size;
    }
}
