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

namespace WEM\SmartgearBundle\Migrations\V1\Y1\Z0\M202311301519;

use Contao\Config;
use Doctrine\DBAL\Connection;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\Migration\Result;
use WEM\SmartgearBundle\Classes\Utils\Configuration\ConfigurationItemUtil;
use WEM\SmartgearBundle\Classes\Utils\Configuration\ConfigurationUtil;
use WEM\SmartgearBundle\Classes\Version\Comparator as VersionComparator;
use WEM\SmartgearBundle\Config\Component\Blog\Blog as BlogConfig;
use WEM\SmartgearBundle\Config\Component\Core\Core;
use WEM\SmartgearBundle\Config\Component\Events\Events as EventsConfig;
use WEM\SmartgearBundle\Config\Component\Faq\Faq as FaqConfig;
use WEM\SmartgearBundle\Config\Component\FormContact\FormContact as FormContactConfig;
use WEM\SmartgearBundle\Exceptions\File\NotFound;
use WEM\SmartgearBundle\Migrations\V1\Y0\Z0\MigrationAbstract;
use WEM\SmartgearBundle\Model\Configuration\Configuration;
use WEM\SmartgearBundle\Model\Configuration\ConfigurationItem;

class Migration extends MigrationAbstract
{
    protected string $name = 'Migrates v1.0.x to v1.1 structure';
    protected string $description = 'Migrates a Smartegar configuration\'s file from v1.0.x to the new v1.1 structure';
    protected string $version = '1.1.0';
    protected string $translation_key = 'WEMSG.MIGRATIONS.V1_1_0_M202311301519';

    public function __construct(
        Connection $connection,
        TranslatorInterface $translator,
        CoreConfigurationManager $coreConfigurationManager,
        VersionComparator $versionComparator
    ) {
        parent::__construct($connection, $translator, $coreConfigurationManager, $versionComparator);
    }

    public function shouldRun(): Result
    {
        $result = parent::shouldRunWithoutCheckingVersion();

        if (Result::STATUS_SHOULD_RUN !== $result->getStatus()) {
            return $result;
        }
        $schemaManager = $this->connection->getSchemaManager();
        if (!$schemaManager->tablesExist([Configuration::getTable(), ConfigurationItem::getTable()])) {
            $result
                ->setStatus(Result::STATUS_FAIL)
                ->addLog($this->translator->trans($this->buildTranslationKey('shouldRunConfigurationTablesMissing'), [], 'contao_default'))
            ;

            return $result;
        }
        $result
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
            /** @var Core */
            $config = $this->coreConfigurationManager->load();
        } catch (NotFound $e) {
            $result
                ->setStatus(Result::STATUS_SKIPPED)
                ->addLog($this->translator->trans($this->buildTranslationKey('doNoPreviousInstallToMigrate'), [], 'contao_default'))
            ;

            $this->updateConfigurationsVersion($this->version);

            return $result;
        }
        try {
            $objConfiguration = $this->configurationFileToConfigrationDatabase($config);
            $this->configurationFileToContaoSettings($config);
            $this->configurationFileToConfigrationItemsDatabase($config, $objConfiguration);

            $result
                ->setStatus(Result::STATUS_SUCCESS)
                // ->addLog($this->translator->trans($this->buildTranslationKey('doAddSocialNetworks'), [], 'contao_default'))
                ->addLog($this->translator->trans($this->buildTranslationKey('done'), [], 'contao_default'))
            ;
            $this->updateConfigurationsVersion($this->version);
        } catch (\Exception $e) {
            $result
                ->setStatus(Result::STATUS_FAIL)
                ->addLog($e->getMessage())
            ;
        }

        return $result;
    }

    protected function configurationFileToConfigrationItemsDatabase(Core $config, Configuration $objConfiguration): void
    {
        // page privacy politics
        $objConfigurationItem = ConfigurationItem::findItems([
            'pid' => $objConfiguration->id,
            'type' => ConfigurationItem::TYPE_PAGE_PRIVACY_POLITICS,
        ], 1);
        if (!$objConfigurationItem) {
            $objConfigurationItem = new ConfigurationItem();
            $objConfigurationItem->created_at = time();
            $objConfigurationItem->pid = $objConfiguration->id;
            $objConfigurationItem->type = ConfigurationItem::TYPE_PAGE_PRIVACY_POLITICS;
        } else {
            $objConfigurationItem = $objConfigurationItem->current();
        }
        $objConfigurationItem->contao_page = $config->getSgPagePrivacyPolitics();
        $objConfigurationItem->save();
        $objConfigurationItem = ConfigurationItemUtil::createEverythingFromConfigurationItem($objConfigurationItem, [], (int) $objConfigurationItem->tstamp);
        $objConfigurationItem->tstamp = time();
        $objConfigurationItem->save();
        // page legal notice
        $objConfigurationItem = ConfigurationItem::findItems([
            'pid' => $objConfiguration->id,
            'type' => ConfigurationItem::TYPE_PAGE_LEGAL_NOTICE,
        ], 1);
        if (!$objConfigurationItem) {
            $objConfigurationItem = new ConfigurationItem();
            $objConfigurationItem->created_at = time();
            $objConfigurationItem->pid = $objConfiguration->id;
            $objConfigurationItem->type = ConfigurationItem::TYPE_PAGE_LEGAL_NOTICE;
        } else {
            $objConfigurationItem = $objConfigurationItem->current();
        }
        $objConfigurationItem->contao_page = $config->getSgPageLegalNotice();
        $objConfigurationItem->save();
        $objConfigurationItem = ConfigurationItemUtil::createEverythingFromConfigurationItem($objConfigurationItem, [], (int) $objConfigurationItem->tstamp);
        $objConfigurationItem->tstamp = time();
        $objConfigurationItem->save();
        // page sitemap => NO, WE HAVE A FULL MIXED
        // usergroup admin
        $objConfigurationItem = ConfigurationItem::findItems([
            'pid' => $objConfiguration->id,
            'type' => ConfigurationItem::TYPE_USER_GROUP_ADMINISTRATORS,
        ], 1);
        if (!$objConfigurationItem) {
            $objConfigurationItem = new ConfigurationItem();
            $objConfigurationItem->created_at = time();
            $objConfigurationItem->pid = $objConfiguration->id;
            $objConfigurationItem->type = ConfigurationItem::TYPE_USER_GROUP_ADMINISTRATORS;
        } else {
            $objConfigurationItem = $objConfigurationItem->current();
        }
        $objConfigurationItem->contao_user_group = $config->getSgUserGroupAdministrators();
        $objConfigurationItem->save();
        $objConfigurationItem = ConfigurationItemUtil::createEverythingFromConfigurationItem($objConfigurationItem, [], (int) $objConfigurationItem->tstamp);
        $objConfigurationItem->tstamp = time();
        $objConfigurationItem->save();
        // usergroup redactors
        $objConfigurationItem = ConfigurationItem::findItems([
            'pid' => $objConfiguration->id,
            'type' => ConfigurationItem::TYPE_USER_GROUP_REDACTORS,
        ], 1);
        if (!$objConfigurationItem) {
            $objConfigurationItem = new ConfigurationItem();
            $objConfigurationItem->created_at = time();
            $objConfigurationItem->pid = $objConfiguration->id;
            $objConfigurationItem->type = ConfigurationItem::TYPE_USER_GROUP_REDACTORS;
        } else {
            $objConfigurationItem = $objConfigurationItem->current();
        }
        $objConfigurationItem->contao_user_group = $config->getSgUserGroupRedactors();
        $objConfigurationItem->save();
        $objConfigurationItem = ConfigurationItemUtil::createEverythingFromConfigurationItem($objConfigurationItem, [], (int) $objConfigurationItem->tstamp);
        $objConfigurationItem->tstamp = time();
        $objConfigurationItem->save();
        // module wem_sg_header
        $objConfigurationItem = ConfigurationItem::findItems([
            'pid' => $objConfiguration->id,
            'type' => ConfigurationItem::TYPE_MODULE_WEM_SG_HEADER,
        ], 1);
        if (!$objConfigurationItem) {
            $objConfigurationItem = new ConfigurationItem();
            $objConfigurationItem->created_at = time();
            $objConfigurationItem->pid = $objConfiguration->id;
            $objConfigurationItem->type = ConfigurationItem::TYPE_MODULE_WEM_SG_HEADER;
        } else {
            $objConfigurationItem = $objConfigurationItem->current();
        }
        $objConfigurationItem->contao_module = $config->getSgModuleByKey('wem_sg_header');
        $objConfigurationItem->save();
        $objConfigurationItem = ConfigurationItemUtil::createEverythingFromConfigurationItem($objConfigurationItem, [], (int) $objConfigurationItem->tstamp);
        $objConfigurationItem->tstamp = time();
        $objConfigurationItem->save();
        // module wem_sg_footer
        $objConfigurationItem = ConfigurationItem::findItems([
            'pid' => $objConfiguration->id,
            'type' => ConfigurationItem::TYPE_MODULE_WEM_SG_FOOTER,
        ], 1);
        if (!$objConfigurationItem) {
            $objConfigurationItem = new ConfigurationItem();
            $objConfigurationItem->created_at = time();
            $objConfigurationItem->pid = $objConfiguration->id;
            $objConfigurationItem->type = ConfigurationItem::TYPE_MODULE_WEM_SG_FOOTER;
        } else {
            $objConfigurationItem = $objConfigurationItem->current();
        }
        $objConfigurationItem->contao_module = $config->getSgModuleByKey('wem_sg_footer');
        $objConfigurationItem->save();
        $objConfigurationItem = ConfigurationItemUtil::createEverythingFromConfigurationItem($objConfigurationItem, [], (int) $objConfigurationItem->tstamp);
        $objConfigurationItem->tstamp = time();
        $objConfigurationItem->save();
        // module wem_sg_social_links
        $objConfigurationItem = ConfigurationItem::findItems([
            'pid' => $objConfiguration->id,
            'type' => ConfigurationItem::TYPE_MODULE_WEM_SG_SOCIAL_NETWORKS,
        ], 1);
        if (!$objConfigurationItem) {
            $objConfigurationItem = new ConfigurationItem();
            $objConfigurationItem->created_at = time();
            $objConfigurationItem->pid = $objConfiguration->id;
            $objConfigurationItem->type = ConfigurationItem::TYPE_MODULE_WEM_SG_SOCIAL_NETWORKS;
        } else {
            $objConfigurationItem = $objConfigurationItem->current();
        }
        $objConfigurationItem->contao_module = $config->getSgModuleByKey('wem_sg_social_link');
        $objConfigurationItem->save();
        $objConfigurationItem = ConfigurationItemUtil::createEverythingFromConfigurationItem($objConfigurationItem, [], (int) $objConfigurationItem->tstamp);
        $objConfigurationItem->tstamp = time();
        $objConfigurationItem->save();
        // module wem_breadcrumb
        $objConfigurationItem = ConfigurationItem::findItems([
            'pid' => $objConfiguration->id,
            'type' => ConfigurationItem::TYPE_MODULE_BREADCRUMB,
        ], 1);
        if (!$objConfigurationItem) {
            $objConfigurationItem = new ConfigurationItem();
            $objConfigurationItem->created_at = time();
            $objConfigurationItem->pid = $objConfiguration->id;
            $objConfigurationItem->type = ConfigurationItem::TYPE_MODULE_BREADCRUMB;
        } else {
            $objConfigurationItem = $objConfigurationItem->current();
        }
        $objConfigurationItem->contao_module = $config->getSgModuleByKey('breadcrumb');
        $objConfigurationItem->save();
        $objConfigurationItem = ConfigurationItemUtil::createEverythingFromConfigurationItem($objConfigurationItem, [], (int) $objConfigurationItem->tstamp);
        $objConfigurationItem->tstamp = time();
        $objConfigurationItem->save();
        // mixed sitemap
        $objConfigurationItem = ConfigurationItem::findItems([
            'pid' => $objConfiguration->id,
            'type' => ConfigurationItem::TYPE_MIXED_SITEMAP,
        ], 1);
        if (!$objConfigurationItem) {
            $objConfigurationItem = new ConfigurationItem();
            $objConfigurationItem->created_at = time();
            $objConfigurationItem->pid = $objConfiguration->id;
            $objConfigurationItem->type = ConfigurationItem::TYPE_MIXED_SITEMAP;
        } else {
            $objConfigurationItem = $objConfigurationItem->current();
        }
        $objConfigurationItem->contao_page = $config->getSgPageSitemap();
        $objConfigurationItem->contao_module = $config->getSgModuleByKey('sitemap');
        $objConfigurationItem->save();
        $objConfigurationItem = ConfigurationItemUtil::createEverythingFromConfigurationItem($objConfigurationItem, [], (int) $objConfigurationItem->tstamp);
        $objConfigurationItem->tstamp = time();
        $objConfigurationItem->save();
        // mixed blog
        /** @var BlogConfig */
        $blogConfig = $config->getSgBlog();
        if ($blogConfig->getSgInstallComplete()) {
            $objConfigurationItem = ConfigurationItem::findItems([
                'pid' => $objConfiguration->id,
                'type' => ConfigurationItem::TYPE_MIXED_BLOG,
            ], 1);
            if (!$objConfigurationItem) {
                $objConfigurationItem = new ConfigurationItem();
                $objConfigurationItem->created_at = time();
                $objConfigurationItem->pid = $objConfiguration->id;
                $objConfigurationItem->type = ConfigurationItem::TYPE_MIXED_BLOG;
            } else {
                $objConfigurationItem = $objConfigurationItem->current();
            }
            $objConfigurationItem->contao_page = $blogConfig->getSgPage();
            $objConfigurationItem->contao_module_reader = $blogConfig->getSgModuleReader();
            $objConfigurationItem->contao_module_list = $blogConfig->getSgModuleList();
            $objConfigurationItem->contao_news_archive = $blogConfig->getSgNewsArchive();
            $objConfigurationItem->save();
            $objConfigurationItem = ConfigurationItemUtil::createEverythingFromConfigurationItem($objConfigurationItem, [], (int) $objConfigurationItem->tstamp);
            $objConfigurationItem->tstamp = time();
            $objConfigurationItem->save();
        }
        // mixed events
        /** @var EventsConfig */
        $eventsConfig = $config->getSgEvents();
        if ($eventsConfig->getSgInstallComplete()) {
            $objConfigurationItem = ConfigurationItem::findItems([
                'pid' => $objConfiguration->id,
                'type' => ConfigurationItem::TYPE_MIXED_EVENTS,
            ], 1);
            if (!$objConfigurationItem) {
                $objConfigurationItem = new ConfigurationItem();
                $objConfigurationItem->created_at = time();
                $objConfigurationItem->pid = $objConfiguration->id;
                $objConfigurationItem->type = ConfigurationItem::TYPE_MIXED_EVENTS;
            } else {
                $objConfigurationItem = $objConfigurationItem->current();
            }
            $objConfigurationItem->contao_page = $eventsConfig->getSgPage();
            $objConfigurationItem->contao_module_reader = $eventsConfig->getSgModuleReader();
            $objConfigurationItem->contao_module_list = $eventsConfig->getSgModuleList();
            $objConfigurationItem->contao_module_calendar = $eventsConfig->getSgModuleCalendar();
            $objConfigurationItem->contao_calendar = $eventsConfig->getSgCalendar();
            $objConfigurationItem->save();
            $objConfigurationItem = ConfigurationItemUtil::createEverythingFromConfigurationItem($objConfigurationItem, [], (int) $objConfigurationItem->tstamp);
            $objConfigurationItem->tstamp = time();
            $objConfigurationItem->save();
        }
        // mixed faq
        /** @var FaqConfig */
        $faqConfig = $config->getSgFaq();
        if ($faqConfig->getSgInstallComplete()) {
            $objConfigurationItem = ConfigurationItem::findItems([
                'pid' => $objConfiguration->id,
                'type' => ConfigurationItem::TYPE_MIXED_FAQ,
            ], 1);
            if (!$objConfigurationItem) {
                $objConfigurationItem = new ConfigurationItem();
                $objConfigurationItem->created_at = time();
                $objConfigurationItem->pid = $objConfiguration->id;
                $objConfigurationItem->type = ConfigurationItem::TYPE_MIXED_FAQ;
            } else {
                $objConfigurationItem = $objConfigurationItem->current();
            }
            $objConfigurationItem->contao_page = $faqConfig->getSgPage();
            $objConfigurationItem->contao_module = $faqConfig->getSgModuleFaq();
            $objConfigurationItem->contao_faq_category = $faqConfig->getSgFaqCategory();
            $objConfigurationItem->save();
            $objConfigurationItem = ConfigurationItemUtil::createEverythingFromConfigurationItem($objConfigurationItem, [], (int) $objConfigurationItem->tstamp);
            $objConfigurationItem->tstamp = time();
            $objConfigurationItem->save();
        }
        // mixed form contact
        /** @var FormContactConfig */
        $formContactConfig = $config->getSgFormContact();
        if ($formContactConfig->getSgInstallComplete()) {
            $objConfigurationItem = ConfigurationItem::findItems([
                'pid' => $objConfiguration->id,
                'type' => ConfigurationItem::TYPE_MIXED_FORM_CONTACT,
            ], 1);
            if (!$objConfigurationItem) {
                $objConfigurationItem = new ConfigurationItem();
                $objConfigurationItem->created_at = time();
                $objConfigurationItem->pid = $objConfiguration->id;
                $objConfigurationItem->type = ConfigurationItem::TYPE_MIXED_FORM_CONTACT;
            } else {
                $objConfigurationItem = $objConfigurationItem->current();
            }
            $objConfigurationItem->contao_page_form = $formContactConfig->getSgPageForm();
            $objConfigurationItem->contao_page_form_sent = $formContactConfig->getSgPageFormSent();
            $objConfigurationItem->contao_form = $formContactConfig->getSgFormContact();
            $objConfigurationItem->contao_notification = $formContactConfig->getSgNotification();
            $objConfigurationItem->save();
            $objConfigurationItem = ConfigurationItemUtil::createEverythingFromConfigurationItem($objConfigurationItem, [], (int) $objConfigurationItem->tstamp);
            $objConfigurationItem->tstamp = time();
            $objConfigurationItem->save();
        }
    }

    protected function configurationFileToContaoSettings(Core $config): void
    {
        $encryptionService = \Contao\System::getContainer()->get('plenta.encryption');

        Config::set('wem_sg_encryption_key', $config->getSgEncryptionKey());
        Config::persist('wem_sg_encryption_key', $config->getSgEncryptionKey());

        Config::set('wem_sg_host_managed', 1);
        Config::persist('wem_sg_host_managed', 1);

        Config::set('wem_sg_airtable_api_key_read', $encryptionService->encrypt($config->getSgAirtableApiKeyForRead()));
        Config::persist('wem_sg_airtable_api_key_read', $encryptionService->encrypt($config->getSgAirtableApiKeyForRead()));

        Config::set('wem_sg_support_form_enabled', 1);
        Config::persist('wem_sg_support_form_enabled', 1);

        Config::set('wem_sg_airtable_api_key_write', $encryptionService->encrypt($config->getSgAirtableApiKeyForWrite()));
        Config::persist('wem_sg_airtable_api_key_write', $encryptionService->encrypt($config->getSgAirtableApiKeyForWrite()));

        Config::set('wem_sg_support_form_notification', $config->getSgNotificationSupport());
        Config::persist('wem_sg_support_form_notification', $config->getSgNotificationSupport());

        Config::set('wem_sg_support_form_gateway', $config->getSgNotificationGatewayEmail());
        Config::persist('wem_sg_support_form_gateway', $config->getSgNotificationGatewayEmail());
    }

    protected function configurationFileToConfigrationDatabase(Core $config): Configuration
    {
        $objConfiguration = Configuration::findItems(['title' => $config->getSgWebsiteTitle()], 1);
        if (!$objConfiguration) {
            $objConfiguration = new Configuration();
            $objConfiguration->created_at = time();
        } else {
            $objConfiguration = $objConfiguration->current();
        }

        $objConfiguration->title = $config->getSgWebsiteTitle();
        $objConfiguration->version = $config->getSgVersion();
        $objConfiguration->mode = $config->getSgMode();
        $objConfiguration->admin_email = $config->getSgOwnerEmail();
        $objConfiguration->domain = $config->getSgOwnerDomain();
        $objConfiguration->email_gateway = $config->getSgNotificationGatewayEmail();
        $objConfiguration->language = 'fr';
        $objConfiguration->framway_path = $config->getSgFramwayPath();
        $objConfiguration->google_fonts = implode(',', $config->getSgGoogleFonts());
        $objConfiguration->analytics_solution = $config->getSgAnalytics();
        $objConfiguration->matomo_host = $config->getSgAnalyticsMatomoHost();
        $objConfiguration->matomo_id = $config->getSgAnalyticsMatomoId();
        $objConfiguration->google_id = $config->getSgAnalyticsGoogleId();
        $objConfiguration->legal_owner_type = 'company';
        $objConfiguration->legal_owner_person_lastname = '';
        $objConfiguration->legal_owner_person_firstname = '';
        $objConfiguration->legal_owner_company_name = $config->getSgOwnerName();
        $objConfiguration->legal_owner_company_status = $config->getSgOwnerStatus();
        $objConfiguration->legal_owner_company_identifier = $config->getSgOwnerSiret();
        $objConfiguration->legal_owner_company_dpo_name = $config->getSgOwnerDpoName();
        $objConfiguration->legal_owner_company_dpo_email = $config->getSgOwnerDpoEmail();
        $objConfiguration->legal_owner_email = $config->getSgOwnerEmail();
        $objConfiguration->legal_owner_street = $config->getSgOwnerStreet();
        $objConfiguration->legal_owner_postal_code = $config->getSgOwnerPostal();
        $objConfiguration->legal_owner_city = $config->getSgOwnerCity();
        $objConfiguration->legal_owner_region = $config->getSgOwnerRegion();
        $objConfiguration->legal_owner_country = $config->getSgOwnerCountry();
        $objConfiguration->host_name = 'INFOMANIAK';
        $objConfiguration->host_street = '25 Eugène-Marziano  Les Acacias';
        $objConfiguration->host_postal_code = '1227';
        $objConfiguration->host_city = 'GENÈVE';
        $objConfiguration->host_region = '';
        $objConfiguration->host_country = 'ch';

        $objConfiguration->contao_theme = $config->getSgTheme();
        $objConfiguration->contao_module_sitemap = $config->getSgModuleByKey('sitemap');
        $objConfiguration->contao_layout_full = $config->getSgLayoutFullwidth();
        $objConfiguration->contao_layout_standard = $config->getSgLayoutStandard();
        $objConfiguration->contao_page_root = $config->getSgPageRoot();
        $objConfiguration->contao_page_home = $config->getSgPageHome();
        $objConfiguration->contao_page_404 = $config->getSgPage404();
        $objConfiguration->api_enabled = true;

        $encryptionService = \Contao\System::getContainer()->get('plenta.encryption');

        $objConfiguration->api_key = $encryptionService->encrypt($config->getSgApiKey());
        $objConfiguration->tstamp = time();

        $objConfiguration->save();

        return ConfigurationUtil::createEverythingFromConfiguration($objConfiguration);
    }
}
