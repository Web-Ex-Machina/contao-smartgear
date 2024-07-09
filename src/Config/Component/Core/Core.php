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

namespace WEM\SmartgearBundle\Config\Component\Core;

use Exception;
use WEM\SmartgearBundle\Classes\Config\ConfigModuleInterface;
use WEM\SmartgearBundle\Config\Component\Blog\Blog as BlogConfig;
use WEM\SmartgearBundle\Config\Component\Events\Events as EventsConfig;
use WEM\SmartgearBundle\Config\Component\Faq\Faq as FaqConfig;
use WEM\SmartgearBundle\Config\Component\FormContact\FormContact as FormContactConfig;
use WEM\SmartgearBundle\Config\Module\Extranet\Extranet as ExtranetConfig;
use WEM\SmartgearBundle\Config\Module\FormDataManager\FormDataManager as FormDataManagerConfig;

class Core implements ConfigModuleInterface
{
    public ?string $sgDefaultClientFilesFolder = null;

    public const FORBIDDEN_WEBSITE_TITLES = ['rsce', 'smartgear'];

    public const ANALYTICS_SYSTEM_NONE = 'none';

    public const ANALYTICS_SYSTEM_GOOGLE = 'google';

    public const ANALYTICS_SYSTEM_MATOMO = 'matomo';

    public const ANALYTICS_SYSTEMS_ALLOWED = [
        self::ANALYTICS_SYSTEM_NONE,
        self::ANALYTICS_SYSTEM_GOOGLE,
        self::ANALYTICS_SYSTEM_MATOMO,
    ];

    public const MODE_DEV = 'dev';

    public const MODE_PROD = 'prod';

    public const MODES_ALLOWED = [
        self::MODE_DEV,
        self::MODE_PROD,
    ];

    public const DEFAULT_VERSION = '1.0.0';

    public const DEFAULT_ANALYTICS_SYSTEM = self::ANALYTICS_SYSTEM_NONE;

    public const DEFAULT_ANALYTICS_SYSTEM_MATOMO_HOST = '//analytics.webexmachina.fr/';

    public const DEFAULT_MODE = self::MODE_DEV;

    public const DEFAULT_FRAMWAY_PATH = 'assets/framway';

    public const DEFAULT_OWNER_HOST = 'INFOMANIAK - 25 Eugène-Marziano 1227 Les Acacias - GENÈVE - SUISSE';

    public const DEFAULT_GOOGLE_FONTS = [];

    public const DEFAULT_USER_USERNAME = 'webmaster';

    public const DEFAULT_USER_GROUP_ADMIN_NAME = 'Administrateurs';

    public const DEFAULT_ROOTPAGE_CHMOD = 'a:12:{i:0;s:2:"u1";i:1;s:2:"u2";i:2;s:2:"u3";i:3;s:2:"u4";i:4;s:2:"u5";i:5;s:2:"u6";i:6;s:2:"g1";i:7;s:2:"g2";i:8;s:2:"g3";i:9;s:2:"g4";i:10;s:2:"g5";i:11;s:2:"g6";}';

    public const DEFAULT_CLIENT_FILES_FOLDER = 'files'.\DIRECTORY_SEPARATOR.'media';

    public const DEFAULT_CLIENT_LOGOS_FOLDER = 'files'.\DIRECTORY_SEPARATOR.'media'.\DIRECTORY_SEPARATOR.'logos';

    public const SUBMODULES_KEYS = ['blog', 'events', 'faq', 'form_contact', 'extranet', 'form_data_manager'];

    protected bool $sgInstallComplete = false;

    protected bool $sgInstallLocked = false;

    protected bool $sgUsePdmForMembers = true;

    protected string $sgVersion = self::DEFAULT_VERSION;

    protected string $sgFramwayPath = self::DEFAULT_FRAMWAY_PATH;

    protected array $sgFramwayThemes = [];

    protected array $sgGoogleFonts = self::DEFAULT_GOOGLE_FONTS;

    protected array $sgSelectedModules = [];

    protected string $sgMode = self::DEFAULT_MODE;

    protected string $sgWebsiteTitle = '';

    protected string $sgOwnerEmail = '';

    protected string $sgAnalytics = self::DEFAULT_ANALYTICS_SYSTEM;

    protected string $sgAnalyticsGoogleId = '';

    protected string $sgAnalyticsMatomoHost = self::DEFAULT_ANALYTICS_SYSTEM_MATOMO_HOST;

    protected string $sgAnalyticsMatomoId = '';

    protected string $sgOwnerName = '';

    protected string $sgOwnerDomain = '';

    protected string $sgOwnerHost = '';

    protected string $sgOwnerLogo = '';

    protected string $sgOwnerStatus = '';

    protected string $sgOwnerStreet = '';

    protected string $sgOwnerPostal = '';

    protected string $sgOwnerCity = '';

    protected string $sgOwnerRegion = '';

    protected string $sgOwnerCountry = '';

    protected string $sgOwnerSiret = '';

    protected string $sgOwnerDpoName = '';

    protected string $sgOwnerDpoEmail = '';

    protected ?int $sgTheme = null;

    protected ?int $sgPageRoot = null;

    protected ?int $sgPageHome = null;

    protected ?int $sgPage404 = null;

    protected ?int $sgPageLegalNotice = null;

    protected ?int $sgPagePrivacyPolitics = null;

    protected ?int $sgPageSitemap = null;

    protected ?int $sgArticleHome = null;

    protected ?int $sgArticle404 = null;

    protected ?int $sgArticleLegalNotice = null;

    protected ?int $sgArticlePrivacyPolitics = null;

    protected ?int $sgArticleSitemap = null;

    protected ?int $sgContent404Headline = null;

    protected ?int $sgContent404Sitemap = null;

    protected ?int $sgContentLegalNotice = null;

    protected ?int $sgContentPrivacyPolitics = null;

    protected ?int $sgContentSitemapHeadline = null;

    protected ?int $sgContentSitemap = null;

    protected ?int $sgUserWebmaster = null;

    protected ?int $sgUserGroupRedactors = null;

    protected ?int $sgUserGroupAdministrators = null;

    protected ?int $sgLayoutStandard = null;

    protected ?int $sgLayoutFullwidth = null;

    protected ?int $sgNotificationGatewayEmail = null;

    protected array $sgModules = [];

    protected string $sgApiKey = '';

    protected string $sgEncryptionKey = '';

    protected string $sgAirtableApiKey = '';

    protected string $sgAirtableApiKeyForRead = '';

    protected string $sgAirtableApiKeyForWrite = '';

    protected array $sgImageSizes = [];

    protected ?int $sgNotificationSupport = null;

    protected ?int $sgNotificationSupportMessageUser = null;

    protected ?int $sgNotificationSupportMessageAdmin = null;

    protected ?int $sgNotificationSupportMessageUserLanguage = null;

    protected ?int $sgNotificationSupportMessageAdminLanguage = null;

    protected BlogConfig $sgBlog;

    protected EventsConfig $sgEvents;

    protected FaqConfig $sgFaq;

    protected FormContactConfig $sgFormContact;

    protected ExtranetConfig $sgExtranet;

    protected FormDataManagerConfig $sgFormDataManager;

    public function __clone()
    {
        foreach (get_object_vars($this) as $name => $value) {
            if (\is_object($value)) {
                $this->{$name} = clone $value;
            }
        }
    }

    public function reset(): self
    {
        $this->setSgInstallComplete(false)
            ->setSgInstallLocked(false)
            ->setSgUsePdmForMembers(true)
            ->setSgVersion(static::DEFAULT_VERSION)
            ->setSgTheme(null)
            ->setSgImageSizes([])
            ->setSgLayoutStandard(null)
            ->setSgLayoutFullwidth(null)
            ->setSgPageRoot(null)
            ->setSgPageHome(null)
            ->setSgPage404(null)
            ->setSgPageLegalNotice(null)
            ->setSgPagePrivacyPolitics(null)
            ->setSgPageSitemap(null)
            ->setSgArticleHome(null)
            ->setSgArticle404(null)
            ->setSgArticleLegalNotice(null)
            ->setSgArticlePrivacyPolitics(null)
            ->setSgArticleSitemap(null)
            ->setSgContent404Headline(null)
            ->setSgContent404Sitemap(null)
            ->setSgContentLegalNotice(null)
            ->setSgContentPrivacyPolitics(null)
            ->setSgContentSitemapHeadline(null)
            ->setSgContentSitemap(null)
            ->setSgUserWebmaster(null)
            ->setSgUserGroupRedactors(null)
            ->setSgUserGroupAdministrators(null)
            ->setSgModules([])
            ->setSgSelectedModules([])
            ->setSgMode(static::DEFAULT_MODE)
            ->setSgWebsiteTitle('')
            ->setSgNotificationGatewayEmail(null)
            ->setSgFramwayPath(self::DEFAULT_FRAMWAY_PATH)
            ->setSgFramwayThemes([])
            ->setSgAnalytics(static::DEFAULT_ANALYTICS_SYSTEM)
            ->setSgAnalyticsGoogleId('')
            ->setSgAnalyticsMatomoHost(self::DEFAULT_ANALYTICS_SYSTEM_MATOMO_HOST)
            ->setSgAnalyticsMatomoId('')
            ->setSgOwnerName('')
            ->setSgOwnerEmail('')
            ->setSgOwnerDomain('')
            ->setSgOwnerHost(self::DEFAULT_OWNER_HOST)
            ->setSgOwnerLogo('')
            ->setSgOwnerStatus('')
            ->setSgOwnerStreet('')
            ->setSgOwnerPostal('')
            ->setSgOwnerCity('')
            ->setSgOwnerRegion('')
            ->setSgOwnerCountry('')
            ->setSgOwnerSiret('')
            ->setSgOwnerDpoName('')
            ->setSgOwnerDpoEmail('')
            ->setSgGoogleFonts(self::DEFAULT_GOOGLE_FONTS)
            ->setSgApiKey('')
            ->setSgEncryptionKey('')
            ->setSgAirtableApiKey('')
            ->setSgAirtableApiKeyForRead('')
            ->setSgAirtableApiKeyForWrite('')
            ->setSgNotificationSupport(null)
            ->setSgNotificationSupportMessageAdmin(null)
            ->setSgNotificationSupportMessageUser(null)
            ->setSgNotificationSupportMessageAdminLanguage(null)
            ->setSgNotificationSupportMessageUserLanguage(null)
            ->setSgBlog((new BlogConfig())->reset())
            ->setSgEvents((new EventsConfig())->reset())
            ->setSgFaq((new FaqConfig())->reset())
            ->setSgFormContact((new FormContactConfig())->reset())
            ->setSgExtranet((new ExtranetConfig())->reset())
            ->setSgFormDataManager((new FormDataManagerConfig())->reset())
        ;

        return $this;
    }

    public function import(\stdClass $json): self
    {
        $this->setSgInstallComplete($json->installComplete ?? false)
            ->setSgInstallLocked($json->installLocked ?? false)
            ->setSgUsePdmForMembers($json->usePdmForMembers ?? true)
            ->setSgVersion($json->version ?? static::DEFAULT_VERSION)
            ->setSgTheme($json->contao->theme ?? null)
            ->setSgImageSizes($json->contao->imageSizes ?? [])
            ->setSgLayoutStandard($json->contao->layouts->standard ?? null)
            ->setSgLayoutFullwidth($json->contao->layouts->fullwidth ?? null)
            ->setSgPageRoot($json->contao->pages->root ?? null)
            ->setSgPageHome($json->contao->pages->home ?? null)
            ->setSgPage404($json->contao->pages->error404 ?? null)
            ->setSgPageLegalNotice($json->contao->pages->legalNotice ?? null)
            ->setSgPagePrivacyPolitics($json->contao->pages->privacyPolitics ?? null)
            ->setSgPageSitemap($json->contao->pages->sitemap ?? null)
            ->setSgArticleHome($json->contao->articles->home ?? null)
            ->setSgArticle404($json->contao->articles->error404 ?? null)
            ->setSgArticleLegalNotice($json->contao->articles->legalNotice ?? null)
            ->setSgArticlePrivacyPolitics($json->contao->articles->privacyPolitics ?? null)
            ->setSgArticleSitemap($json->contao->articles->sitemap ?? null)
            ->setSgContent404Headline($json->contao->contents->error404Headline ?? null)
            ->setSgContent404Sitemap($json->contao->contents->error404Sitemap ?? null)
            ->setSgContentLegalNotice($json->contao->contents->legalNotice ?? null)
            ->setSgContentPrivacyPolitics($json->contao->contents->privacyPolitics ?? null)
            ->setSgContentSitemapHeadline($json->contao->contents->sitemapHeadline ?? null)
            ->setSgContentSitemap($json->contao->contents->sitemap ?? null)
            ->setSgUserWebmaster($json->contao->users->webmaster ?? null)
            ->setSgUserGroupRedactors($json->contao->userGroups->redactors ?? null)
            ->setSgUserGroupAdministrators($json->contao->userGroups->administrators ?? null)
            ->setSgModules($json->contao->modules ?? [])
            ->setSgSelectedModules($json->selectedModules ?? [])
            ->setSgMode($json->mode ?? static::DEFAULT_MODE)
            ->setSgWebsiteTitle($json->websiteTitle ?? '')
            ->setSgNotificationGatewayEmail($json->contao->notificationGateways->email ?? null)
            ->setSgFramwayPath($json->framway->path ?? self::DEFAULT_FRAMWAY_PATH)
            ->setSgFramwayThemes($json->framway->themes ?? [])
            ->setSgAnalytics($json->analytics->system ?? static::DEFAULT_ANALYTICS_SYSTEM)
            ->setSgAnalyticsGoogleId($json->analytics->google->id ?? '')
            ->setSgAnalyticsMatomoHost($json->analytics->matomo->host ?? self::DEFAULT_ANALYTICS_SYSTEM_MATOMO_HOST)
            ->setSgAnalyticsMatomoId($json->analytics->matomo->id ?? '')
            ->setSgOwnerName($json->owner->name ?? '')
            ->setSgOwnerEmail($json->owner->email ?? '')
            ->setSgOwnerDomain($json->owner->domain ?? '')
            ->setSgOwnerHost($json->owner->host ?? self::DEFAULT_OWNER_HOST)
            ->setSgOwnerLogo($json->owner->logo ?? '')
            ->setSgOwnerStatus($json->owner->status ?? '')
            ->setSgOwnerStreet($json->owner->street ?? '')
            ->setSgOwnerPostal($json->owner->postal ?? '')
            ->setSgOwnerCity($json->owner->city ?? '')
            ->setSgOwnerRegion($json->owner->region ?? '')
            ->setSgOwnerCountry($json->owner->country ?? '')
            ->setSgOwnerSiret($json->owner->siret ?? '')
            ->setSgOwnerDpoName($json->owner->dpo->name ?? '')
            ->setSgOwnerDpoEmail($json->owner->dpo->email ?? '')
            ->setSgGoogleFonts($json->googleFonts ?? self::DEFAULT_GOOGLE_FONTS)
            ->setSgApiKey($json->api->key ?? '')
            ->setSgEncryptionKey($json->encryption->key ?? '')
            ->setSgAirtableApiKey($json->airtable->api->key ?? '')
            ->setSgAirtableApiKeyForRead($json->airtable->api->key_read ?? '')
            ->setSgAirtableApiKeyForWrite($json->airtable->api->key_write ?? '')
            ->setSgNotificationSupport($json->notification->support->id ?? null)
            ->setSgNotificationSupportMessageAdmin($json->notification->support->admin->message->id ?? null)
            ->setSgNotificationSupportMessageUser($json->notification->support->user->message->id ?? null)
            ->setSgNotificationSupportMessageAdminLanguage($json->notification->support->admin->message->language->id ?? null)
            ->setSgNotificationSupportMessageUserLanguage($json->notification->support->user->message->language->id ?? null)
            ->setSgBlog(
                property_exists($json, 'blog')
                ? (new BlogConfig())->import($json->blog)
                : (new BlogConfig())->reset()
            )
            ->setSgEvents(
                property_exists($json, 'events')
                ? (new EventsConfig())->import($json->events)
                : (new EventsConfig())->reset()
            )
            ->setSgFaq(
                property_exists($json, 'faq')
                ? (new FaqConfig())->import($json->faq)
                : (new FaqConfig())->reset()
            )
            ->setSgFormContact(
                property_exists($json, 'formContact')
                ? (new FormContactConfig())->import($json->formContact)
                : (new FormContactConfig())->reset()
            )
            ->setSgExtranet(
                property_exists($json, 'extranet')
                ? (new ExtranetConfig())->import($json->extranet)
                : (new ExtranetConfig())->reset()
            )
            ->setSgFormDataManager(
                property_exists($json, 'formDataManager')
                ? (new FormDataManagerConfig())->import($json->formDataManager)
                : (new FormDataManagerConfig())->reset()
            )
        ;

        return $this;
    }

    public function export(): string
    {
        $json = new \stdClass();
        $json->installComplete = $this->getSgInstallComplete();
        $json->installLocked = $this->getSgInstallLocked();
        $json->usePdmForMembers = $this->getSgUsePdmForMembers();
        $json->version = $this->getSgVersion();
        $json->selectedModules = $this->getSgSelectedModules();
        $json->mode = $this->getSgMode();
        $json->websiteTitle = $this->getSgWebsiteTitle();
        $json->googleFonts = $this->getSgGoogleFonts();

        $json->contao = new \stdClass();
        $json->contao->theme = $this->getSgTheme();
        $json->contao->imageSizes = $this->getSgImageSizes();
        $json->contao->modules = $this->getSgModules();

        $json->contao->pages = new \stdClass();
        $json->contao->pages->root = $this->getSgPageRoot();
        $json->contao->pages->home = $this->getSgPageHome();
        $json->contao->pages->error404 = $this->getSgPage404();
        $json->contao->pages->legalNotice = $this->getSgPageLegalNotice();
        $json->contao->pages->privacyPolitics = $this->getSgPagePrivacyPolitics();
        $json->contao->pages->sitemap = $this->getSgPageSitemap();

        $json->contao->articles = new \stdClass();
        $json->contao->articles->home = $this->getSgArticleHome();
        $json->contao->articles->error404 = $this->getSgArticle404();
        $json->contao->articles->legalNotice = $this->getSgArticleLegalNotice();
        $json->contao->articles->privacyPolitics = $this->getSgArticlePrivacyPolitics();
        $json->contao->articles->sitemap = $this->getSgArticleSitemap();

        $json->contao->contents = new \stdClass();
        $json->contao->contents->error404Headline = $this->getSgContent404Headline();
        $json->contao->contents->error404Sitemap = $this->getSgContent404Sitemap();
        $json->contao->contents->legalNotice = $this->getSgContentLegalNotice();
        $json->contao->contents->privacyPolitics = $this->getSgContentPrivacyPolitics();
        $json->contao->contents->sitemapHeadline = $this->getSgContentSitemapHeadline();
        $json->contao->contents->sitemap = $this->getSgContentSitemap();

        $json->contao->users = new \stdClass();
        $json->contao->users->webmaster = $this->getSgUserWebmaster();

        $json->contao->userGroups = new \stdClass();
        $json->contao->userGroups->redactors = $this->getSgUserGroupRedactors();
        $json->contao->userGroups->administrators = $this->getSgUserGroupAdministrators();

        $json->contao->layouts = new \stdClass();
        $json->contao->layouts->standard = $this->getSgLayoutStandard();
        $json->contao->layouts->fullwidth = $this->getSgLayoutFullwidth();

        $json->contao->notificationGateways = new \stdClass();
        $json->contao->notificationGateways->email = $this->getSgNotificationGatewayEmail();

        $json->framway = new \stdClass();
        $json->framway->path = $this->getSgFramwayPath();
        $json->framway->themes = $this->getSgFramwayThemes();

        $json->analytics = new \stdClass();
        $json->analytics->system = $this->getSgAnalytics();
        $json->analytics->google = new \stdClass();
        $json->analytics->google->id = $this->getSgAnalyticsGoogleId();
        $json->analytics->matomo = new \stdClass();
        $json->analytics->matomo->host = $this->getSgAnalyticsMatomoHost();
        $json->analytics->matomo->id = $this->getSgAnalyticsMatomoId();

        $json->owner = new \stdClass();
        $json->owner->name = $this->getSgOwnerName();
        $json->owner->email = $this->getSgOwnerEmail();
        $json->owner->domain = $this->getSgOwnerDomain();
        $json->owner->host = $this->getSgOwnerHost();
        $json->owner->logo = $this->getSgOwnerLogo();
        $json->owner->status = $this->getSgOwnerStatus();
        $json->owner->street = $this->getSgOwnerStreet();
        $json->owner->postal = $this->getSgOwnerPostal();
        $json->owner->city = $this->getSgOwnerCity();
        $json->owner->region = $this->getSgOwnerRegion();
        $json->owner->country = $this->getSgOwnerCountry();
        $json->owner->siret = $this->getSgOwnerSiret();
        $json->owner->dpo = new \stdClass();
        $json->owner->dpo->name = $this->getSgOwnerDpoName();
        $json->owner->dpo->email = $this->getSgOwnerDpoEmail();

        $json->api = new \stdClass();
        $json->api->key = $this->getSgApiKey();

        $json->encryption = new \stdClass();
        $json->encryption->key = $this->getSgEncryptionKey();

        $json->airtable = new \stdClass();
        $json->airtable->api = new \stdClass();
        $json->airtable->api->key = $this->getSgAirtableApiKey();
        $json->airtable->api->key_read = $this->getSgAirtableApiKeyForRead();
        $json->airtable->api->key_write = $this->getSgAirtableApiKeyForWrite();

        $json->notification = new \stdClass();
        $json->notification->support = new \stdClass();
        $json->notification->support->id = $this->getSgNotificationSupport();
        $json->notification->support->admin = new \stdClass();
        $json->notification->support->admin->message = new \stdClass();
        $json->notification->support->admin->message->id = $this->getSgNotificationSupportMessageAdmin();
        $json->notification->support->admin->message->language = new \stdClass();
        $json->notification->support->admin->message->language->id = $this->getSgNotificationSupportMessageAdminLanguage();
        $json->notification->support->user = new \stdClass();
        $json->notification->support->user->message = new \stdClass();
        $json->notification->support->user->message->id = $this->getSgNotificationSupportMessageUser();
        $json->notification->support->user->message->language = new \stdClass();
        $json->notification->support->user->message->language->id = $this->getSgNotificationSupportMessageUserLanguage();

        $json->blog = $this->getSgBlog()->export();
        $json->events = $this->getSgEvents()->export();
        $json->faq = $this->getSgFaq()->export();
        $json->formContact = $this->getSgFormContact()->export();
        $json->extranet = $this->getSgExtranet()->export();
        $json->formDataManager = $this->getSgFormDataManager()->export();

        return json_encode($json, \JSON_PRETTY_PRINT);
    }

    /**
     * @throws Exception
     */
    public function getSubmodulesConfigs(): array
    {
        return [
            'blog' => $this->getSubmoduleConfig('blog'),
            'events' => $this->getSubmoduleConfig('events'),
            'faq' => $this->getSubmoduleConfig('faq'),
            'form_contact' => $this->getSubmoduleConfig('form_contact'),
            'extranet' => $this->getSubmoduleConfig('extranet'),
            'form_data_manager' => $this->getSubmoduleConfig('form_data_manager'),
        ];
    }

    /**
     * @throws Exception
     */
    public function getSubmoduleConfig(string $submodule): BlogConfig|EventsConfig|FaqConfig|FormContactConfig|ExtranetConfig|FormDataManagerConfig|null
    {
        if (!$this->isSubmoduleNameKnown($submodule)) {
            throw new Exception(sprintf('The submodule "%s" is unknown', $submodule));
        }

        return match ($submodule) {
            'blog' => $this->getSgBlog(),
            'events' => $this->getSgEvents(),
            'faq' => $this->getSgFaq(),
            'form_contact' => $this->getSgFormContact(),
            'extranet' => $this->getSgExtranet(),
            'form_data_manager' => $this->getSgFormDataManager(),
            default => null,
        };
    }

    /**
     * @throws Exception
     */
    public function setSubmodulesConfigs(array $submodulesConfigs): self
    {
        foreach ($submodulesConfigs as $submodule => $config) {
            $this->setSubmoduleConfig($submodule, $config);
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    public function setSubmoduleConfig(string $submodule, FormDataManagerConfig $config): self
    {
        if (!$this->isSubmoduleNameKnown($submodule)) {
            throw new Exception(sprintf('The submodule "%s" is unknown', $submodule));
        }

        match ($submodule) {
            'blog' => $this->setSgBlog($config), // TODO : Expected parameter of type '\WEM\SmartgearBundle\Config\Component\Blog\Blog', '\WEM\SmartgearBundle\Config\Module\FormDataManager\FormDataManager' provided
            'events' => $this->setSgEvents($config),
            'faq' => $this->setSgFaq($config),
            'form_contact' => $this->setSgFormContact($config),
            'extranet' => $this->setSgExtranet($config),
            'form_data_manager' => $this->setSgFormDataManager($config),
            default => $this,
        };

        return $this;
    }

    public function getContaoModulesIdsForAll(): array
    {
        return array_merge(
            $this->getContaoModulesIds(),
            $this->getSgBlog()->getContaoModulesIds(),
            $this->getSgEvents()->getContaoModulesIds(),
            $this->getSgFaq()->getContaoModulesIds(),
            $this->getSgFormContact()->getContaoModulesIds(),
            $this->getSgExtranet()->getContaoModulesIds(),
            $this->getSgFormDataManager()->getContaoModulesIds(),
        );
    }

    public function getContaoModulesIds(): array
    {
        if (!$this->getSgInstallComplete()) {
            return [];
        }

        $modules = [];
        foreach ($this->getSgModules() as $module) {
            if (null !== $module->id) {
                $modules[] = (int) $module->id;
            }
        }

        return $modules;
    }

    public function getContaoPagesIdsForAll(): array
    {
        return array_merge(
            $this->getContaoPagesIds(),
            $this->getSgBlog()->getContaoPagesIds(),
            $this->getSgEvents()->getContaoPagesIds(),
            $this->getSgFaq()->getContaoPagesIds(),
            $this->getSgFormContact()->getContaoPagesIds(),
            $this->getSgExtranet()->getContaoPagesIds(),
            $this->getSgFormDataManager()->getContaoPagesIds(),
        );
    }

    public function getContaoPagesIds(): array
    {
        if (!$this->getSgInstallComplete()) {
            return [];
        }

        return [
            $this->getSgPageRoot(),
            $this->getSgPageHome(),
            $this->getSgPage404(),
            $this->getSgPageLegalNotice(),
            $this->getSgPagePrivacyPolitics(),
            $this->getSgPageSitemap(),
        ];
    }

    public function getContaoContentsIdsForAll(): array
    {
        return array_merge(
            $this->getContaoContentsIds(),
            $this->getSgBlog()->getContaoContentsIds(),
            $this->getSgEvents()->getContaoContentsIds(),
            $this->getSgFaq()->getContaoContentsIds(),
            $this->getSgFormContact()->getContaoContentsIds(),
            $this->getSgExtranet()->getContaoContentsIds(),
            $this->getSgFormDataManager()->getContaoContentsIds(),
        );
    }

    public function getContaoContentsIds(): array
    {
        if (!$this->getSgInstallComplete()) {
            return [];
        }

        return [
            $this->getSgContent404Headline(),
            $this->getSgContent404Sitemap(),
            $this->getSgContentLegalNotice(),
            $this->getSgContentPrivacyPolitics(),
            $this->getSgContentSitemapHeadline(),
            $this->getSgContentSitemap(),
        ];
    }

    public function getContaoArticlesIdsForAll(): array
    {
        return array_merge(
            $this->getContaoArticlesIds(),
            $this->getSgBlog()->getContaoArticlesIds(),
            $this->getSgEvents()->getContaoArticlesIds(),
            $this->getSgFaq()->getContaoArticlesIds(),
            $this->getSgFormContact()->getContaoArticlesIds(),
            $this->getSgExtranet()->getContaoArticlesIds(),
            $this->getSgFormDataManager()->getContaoArticlesIds(),
        );
    }

    public function getContaoArticlesIds(): array
    {
        if (!$this->getSgInstallComplete()) {
            return [];
        }

        return [
            $this->getSgArticleHome(),
            $this->getSgArticle404(),
            $this->getSgArticleLegalNotice(),
            $this->getSgArticlePrivacyPolitics(),
            $this->getSgArticleSitemap(),
        ];
    }

    public function getContaoFoldersIdsForAll(): array
    {
        return array_merge(
            $this->getContaoFoldersIds(),
            $this->getSgBlog()->getContaoFoldersIds(),
            $this->getSgEvents()->getContaoFoldersIds(),
            $this->getSgFaq()->getContaoFoldersIds(),
            $this->getSgFormContact()->getContaoFoldersIds(),
            $this->getSgExtranet()->getContaoFoldersIds(),
            $this->getSgFormDataManager()->getContaoFoldersIds(),
        );
    }

    public function getContaoFoldersIds(): array
    {
        if (!$this->getSgInstallComplete()) {
            return [];
        }

        return [
            self::DEFAULT_CLIENT_FILES_FOLDER,
            self::DEFAULT_CLIENT_LOGOS_FOLDER,
        ];
    }

    public function getContaoUsersIdsForAll(): array
    {
        return array_merge(
            $this->getContaoUsersIds(),
            $this->getSgBlog()->getContaoUsersIds(),
            $this->getSgEvents()->getContaoUsersIds(),
            $this->getSgFaq()->getContaoUsersIds(),
            $this->getSgFormContact()->getContaoUsersIds(),
            $this->getSgExtranet()->getContaoUsersIds(),
            $this->getSgFormDataManager()->getContaoUsersIds(),
        );
    }

    public function getContaoUsersIds(): array
    {
        if (!$this->getSgInstallComplete()) {
            return [];
        }

        return [
            $this->getSgUserWebmaster(),
        ];
    }

    public function getContaoUserGroupsIdsForAll(): array
    {
        return array_merge(
            $this->getContaoUserGroupsIds(),
            $this->getSgBlog()->getContaoUserGroupsIds(),
            $this->getSgEvents()->getContaoUserGroupsIds(),
            $this->getSgFaq()->getContaoUserGroupsIds(),
            $this->getSgFormContact()->getContaoUserGroupsIds(),
            $this->getSgExtranet()->getContaoUserGroupsIds(),
            $this->getSgFormDataManager()->getContaoUserGroupsIds(),
        );
    }

    public function getContaoUserGroupsIds(): array
    {
        if (!$this->getSgInstallComplete()) {
            return [];
        }

        return [
            $this->getSgUserGroupAdministrators(),
            $this->getSgUserGroupRedactors(),
        ];
    }

    public function getContaoMembersIdsForAll(): array
    {
        return array_merge(
            $this->getContaoMembersIds(),
            $this->getSgBlog()->getContaoMembersIds(),
            $this->getSgEvents()->getContaoMembersIds(),
            $this->getSgFaq()->getContaoMembersIds(),
            $this->getSgFormContact()->getContaoMembersIds(),
            $this->getSgExtranet()->getContaoMembersIds(),
            $this->getSgFormDataManager()->getContaoMembersIds(),
        );
    }

    public function getContaoMembersIds(): array
    {
        if (!$this->getSgInstallComplete()) {
            return [];
        }

        return [];
    }

    public function getContaoMemberGroupsIdsForAll(): array
    {
        return array_merge(
            $this->getContaoMemberGroupsIds(),
            $this->getSgBlog()->getContaoMemberGroupsIds(),
            $this->getSgEvents()->getContaoMemberGroupsIds(),
            $this->getSgFaq()->getContaoMemberGroupsIds(),
            $this->getSgFormContact()->getContaoMemberGroupsIds(),
            $this->getSgExtranet()->getContaoMemberGroupsIds(),
            $this->getSgFormDataManager()->getContaoMemberGroupsIds(),
        );
    }

    public function getContaoMemberGroupsIds(): array
    {
        if (!$this->getSgInstallComplete()) {
            return [];
        }

        return [];
    }

    public function getContaoNotificationsIdsForAll(): array
    {
        return array_merge(
            $this->getContaoNotificationsIds(),
            $this->getSgFormContact()->getContaoNotificationsIds(),
        );
    }

    public function getContaoNotificationsIds(): array
    {
        if (!$this->getSgInstallComplete()) {
            return [];
        }

        return [$this->getSgNotificationSupport()];
    }

    public function getContaoNotificationsMessagesIdsForAll(): array
    {
        return array_merge(
            $this->getContaoNotificationsMessagesIds(),
            $this->getSgFormContact()->getContaoNotificationsMessagesIds(),
        );
    }

    public function getContaoNotificationsMessagesIds(): array
    {
        if (!$this->getSgInstallComplete()) {
            return [];
        }

        return [$this->getSgNotificationSupportMessageAdmin(), $this->getSgNotificationSupportMessageUser()];
    }

    public function getContaoNotificationsMessagesLanguagesIdsForAll(): array
    {
        return array_merge(
            $this->getContaoNotificationsMessagesLanguagesIds(),
            $this->getSgFormContact()->getContaoNotificationsMessagesLanguagesIds(), // TODO : Not found
        );
    }

    public function getContaoNotificationsMessagesLanguagesIds(): array
    {
        if (!$this->getSgInstallComplete()) {
            return [];
        }

        return [$this->getSgNotificationSupportMessageAdminLanguage(), $this->getSgNotificationSupportMessageUserLanguage()];
    }

    public function getContaoImageSizesIds(): array
    {
        $imageSizes = [];
        foreach ($this->getSgImageSizes() as $imageSize) {
            if (null !== $imageSize->id) {
                $imageSizes[] = (int) $imageSize->id;
            }
        }

        return $imageSizes;
    }

    public function getContaoImageSizesIdsForAll(): array
    {
        return $this->getContaoImageSizesIds();
    }

    public function resetContaoImageSizesIds(): void
    {
        $this->setSgImageSizes([]);
    }

    public function resetContaoModulesIds(): void
    {
        $this->setSgModules([]);
    }

    public function resetContaoPagesIds(): void
    {
        $this->setSgPageHome(null);
        $this->setSgPageRoot(null);
        $this->setSgPagePrivacyPolitics(null);
        $this->setSgPage404(null);
        $this->setSgPageSitemap(null);
        $this->setSgPageLegalNotice(null);
    }

    public function resetContaoContentsIds(): void
    {
        $this->setSgContent404Headline(null);
        $this->setSgContent404Sitemap(null);
        $this->setSgContentSitemap(null);
        $this->setSgContentLegalNotice(null);
        $this->setSgContentPrivacyPolitics(null);
    }

    public function resetContaoArticlesIds(): void
    {
        $this->setSgArticle404(null);
        $this->setSgArticleHome(null);
        $this->setSgArticleSitemap(null);
        $this->setSgArticleLegalNotice(null);
        $this->setSgArticlePrivacyPolitics(null);
    }

    public function resetContaoFoldersIds(): void
    {
    }

    public function resetContaoUsersIds(): void
    {
        $this->setSgUserWebmaster(null);
    }

    public function resetContaoUserGroupsIds(): void
    {
        $this->setSgUserGroupAdministrators(null);
        $this->setSgUserGroupRedactors(null);
    }

    public function resetContaoMembersIds(): void
    {
    }

    public function resetContaoMemberGroupsIds(): void
    {
    }

    public function resetContaoNotificationsIds(): void
    {
        $this->setSgNotificationSupport(null);
    }

    public function resetContaoNotificationsMessagesIds(): void
    {
        $this->setSgNotificationSupportMessageAdmin(null);
        $this->setSgNotificationSupportMessageUser(null);
    }

    public function resetContaoNotificationsMessagesLangugesIds(): void
    {
        $this->setSgNotificationSupportMessageAdminLanguage(null);
        $this->setSgNotificationSupportMessageUserLanguage(null);
    }

    public function getSgVersion(): string
    {
        return $this->sgVersion;
    }

    public function setSgVersion(string $sgVersion): self
    {
        $this->sgVersion = $sgVersion;

        return $this;
    }

    public function getSgFramwayPath(): string
    {
        return $this->sgFramwayPath;
    }

    public function setSgFramwayPath(string $sgFramwayPath): self
    {
        $this->sgFramwayPath = $sgFramwayPath;

        return $this;
    }

    public function getSgFramwayThemes(): array
    {
        return $this->sgFramwayThemes;
    }

    public function setSgFramwayThemes(array $sgFramwayThemes): self
    {
        $this->sgFramwayThemes = $sgFramwayThemes;

        return $this;
    }

    public function getSgSelectedModules(): array
    {
        return $this->sgSelectedModules;
    }

    public function setSgSelectedModules(array $sgSelectedModules): self
    {
        $this->sgSelectedModules = $sgSelectedModules;

        return $this;
    }

    public function getSgMode(): string
    {
        return $this->sgMode;
    }

    public function setSgMode(string $sgMode): self
    {
        if (!\in_array($sgMode, static::MODES_ALLOWED, true)) {
            throw new \InvalidArgumentException(sprintf('Invalid mode "%s" given', $sgMode));
        }

        $this->sgMode = $sgMode;

        return $this;
    }

    public function getSgWebsiteTitle(): string
    {
        return $this->sgWebsiteTitle;
    }

    public function setSgWebsiteTitle(string $sgWebsiteTitle): self
    {
        $this->sgWebsiteTitle = $sgWebsiteTitle;

        return $this;
    }

    public function getSgOwnerEmail(): string
    {
        return $this->sgOwnerEmail;
    }

    public function setSgOwnerEmail(string $sgOwnerEmail): self
    {
        $this->sgOwnerEmail = $sgOwnerEmail;

        return $this;
    }

    public function getSgAnalytics(): string
    {
        return $this->sgAnalytics;
    }

    public function setSgAnalytics(string $sgAnalytics): self
    {
        if (!\in_array($sgAnalytics, static::ANALYTICS_SYSTEMS_ALLOWED, true)) {
            throw new \InvalidArgumentException(sprintf('Invalid analytics system "%s" given', $sgAnalytics));
        }

        $this->sgAnalytics = $sgAnalytics;

        return $this;
    }

    public function getSgAnalyticsGoogleId(): string
    {
        return $this->sgAnalyticsGoogleId;
    }

    public function setSgAnalyticsGoogleId(string $sgAnalyticsGoogleId): self
    {
        $this->sgAnalyticsGoogleId = $sgAnalyticsGoogleId;

        return $this;
    }

    public function getSgAnalyticsMatomoHost(): string
    {
        return $this->sgAnalyticsMatomoHost;
    }

    public function setSgAnalyticsMatomoHost(string $sgAnalyticsMatomoHost): self
    {
        $this->sgAnalyticsMatomoHost = $sgAnalyticsMatomoHost;

        return $this;
    }

    public function getSgAnalyticsMatomoId(): string
    {
        return $this->sgAnalyticsMatomoId;
    }

    public function setSgAnalyticsMatomoId(string $sgAnalyticsMatomoId): self
    {
        $this->sgAnalyticsMatomoId = $sgAnalyticsMatomoId;

        return $this;
    }

    public function getSgOwnerDomain(): string
    {
        return $this->sgOwnerDomain;
    }

    public function setSgOwnerDomain(string $sgOwnerDomain): self
    {
        $this->sgOwnerDomain = $sgOwnerDomain;

        return $this;
    }

    public function getSgOwnerHost(): string
    {
        return $this->sgOwnerHost;
    }

    public function setSgOwnerHost(string $sgOwnerHost): self
    {
        $this->sgOwnerHost = $sgOwnerHost;

        return $this;
    }

    public function getSgOwnerLogo(): string
    {
        return $this->sgOwnerLogo;
    }

    public function setSgOwnerLogo(string $sgOwnerLogo): self
    {
        $this->sgOwnerLogo = $sgOwnerLogo;

        return $this;
    }

    public function getSgOwnerStatus(): string
    {
        return $this->sgOwnerStatus;
    }

    public function setSgOwnerStatus(string $sgOwnerStatus): self
    {
        $this->sgOwnerStatus = $sgOwnerStatus;

        return $this;
    }

    public function getSgOwnerStreet(): string
    {
        return $this->sgOwnerStreet;
    }

    public function setSgOwnerStreet(string $sgOwnerStreet): self
    {
        $this->sgOwnerStreet = $sgOwnerStreet;

        return $this;
    }

    public function getSgOwnerPostal(): string
    {
        return $this->sgOwnerPostal;
    }

    public function setSgOwnerPostal(string $sgOwnerPostal): self
    {
        $this->sgOwnerPostal = $sgOwnerPostal;

        return $this;
    }

    public function getSgOwnerCity(): string
    {
        return $this->sgOwnerCity;
    }

    public function setSgOwnerCity(string $sgOwnerCity): self
    {
        $this->sgOwnerCity = $sgOwnerCity;

        return $this;
    }

    public function getSgOwnerRegion(): string
    {
        return $this->sgOwnerRegion;
    }

    public function setSgOwnerRegion(string $sgOwnerRegion): self
    {
        $this->sgOwnerRegion = $sgOwnerRegion;

        return $this;
    }

    public function getSgOwnerCountry(): string
    {
        return $this->sgOwnerCountry;
    }

    public function setSgOwnerCountry(string $sgOwnerCountry): self
    {
        $this->sgOwnerCountry = $sgOwnerCountry;

        return $this;
    }

    public function getSgOwnerSiret(): string
    {
        return $this->sgOwnerSiret;
    }

    public function setSgOwnerSiret(string $sgOwnerSiret): self
    {
        $this->sgOwnerSiret = $sgOwnerSiret;

        return $this;
    }

    public function getSgOwnerDpoName(): string
    {
        return $this->sgOwnerDpoName;
    }

    public function setSgOwnerDpoName(string $sgOwnerDpoName): self
    {
        $this->sgOwnerDpoName = $sgOwnerDpoName;

        return $this;
    }

    public function getSgOwnerDpoEmail(): string
    {
        return $this->sgOwnerDpoEmail;
    }

    public function setSgOwnerDpoEmail(string $sgOwnerDpoEmail): self
    {
        $this->sgOwnerDpoEmail = $sgOwnerDpoEmail;

        return $this;
    }

    public function getSgInstallComplete(): bool
    {
        return $this->sgInstallComplete;
    }

    public function setSgInstallComplete(bool $sgInstallComplete): self
    {
        $this->sgInstallComplete = $sgInstallComplete;

        return $this;
    }

    public function getSgTheme(): ?int
    {
        return $this->sgTheme;
    }

    public function setSgTheme(?int $sgTheme = null): self
    {
        $this->sgTheme = $sgTheme;

        return $this;
    }

    public function getSgModules(): array
    {
        return $this->sgModules;
    }

    public function setSgModules(array $sgModules): self
    {
        $this->sgModules = $sgModules;

        return $this;
    }

    public function getSgModuleByKey(string $moduleKey): ?int
    {
        foreach ($this->sgModules as $module) {
            if ($moduleKey === $module->key) {
                return (int) $module->id;
            }
        }

        return null;
    }

    public function getSgOwnerName(): string
    {
        return $this->sgOwnerName;
    }

    public function setSgOwnerName(string $sgOwnerName): self
    {
        $this->sgOwnerName = $sgOwnerName;

        return $this;
    }

    public function getSgGoogleFonts(): array
    {
        return $this->sgGoogleFonts;
    }

    public function setSgGoogleFonts(array $sgGoogleFonts): self
    {
        $this->sgGoogleFonts = $sgGoogleFonts;

        return $this;
    }

    public function getSgApiKey(): string
    {
        return $this->sgApiKey;
    }

    public function setSgApiKey(string $sgApiKey): self
    {
        $this->sgApiKey = $sgApiKey;

        return $this;
    }

    public function getSgDefaultClientFilesFolder(): string
    {
        return $this->sgDefaultClientFilesFolder;
    }

    public function setSgDefaultClientFilesFolder(string $sgDefaultClientFilesFolder): self
    {
        $this->sgDefaultClientFilesFolder = $sgDefaultClientFilesFolder;

        return $this;
    }

    public function getSgPageRoot(): ?int
    {
        return $this->sgPageRoot;
    }

    public function setSgPageRoot(?int $sgPageRoot = null): self
    {
        $this->sgPageRoot = $sgPageRoot;

        return $this;
    }

    public function getSgUserGroupAdministrators(): ?int
    {
        return $this->sgUserGroupAdministrators;
    }

    public function setSgUserGroupAdministrators(?int $sgUserGroupAdministrators): self
    {
        $this->sgUserGroupAdministrators = $sgUserGroupAdministrators;

        return $this;
    }

    public function getSgUserGroupRedactors(): ?int
    {
        return $this->sgUserGroupRedactors;
    }

    public function setSgUserGroupRedactors(?int $sgUserGroupRedactors): self
    {
        $this->sgUserGroupRedactors = $sgUserGroupRedactors;

        return $this;
    }

    public function getSgUserWebmaster(): ?int
    {
        return $this->sgUserWebmaster;
    }

    public function setSgUserWebmaster(?int $sgUserWebmaster): self
    {
        $this->sgUserWebmaster = $sgUserWebmaster;

        return $this;
    }

    public function getSgBlog(): BlogConfig
    {
        return $this->sgBlog;
    }

    public function setSgBlog(BlogConfig $sgBlog): self
    {
        $this->sgBlog = $sgBlog;

        return $this;
    }

    public function getSgEvents(): EventsConfig
    {
        return $this->sgEvents;
    }

    public function setSgEvents(EventsConfig $sgEvents): self
    {
        $this->sgEvents = $sgEvents;

        return $this;
    }

    public function getSgFaq(): FaqConfig
    {
        return $this->sgFaq;
    }

    public function setSgFaq(FaqConfig $sgFaq): self
    {
        $this->sgFaq = $sgFaq;

        return $this;
    }

    public function getSgLayoutStandard(): ?int
    {
        return $this->sgLayoutStandard;
    }

    public function setSgLayoutStandard(?int $sgLayoutStandard): self
    {
        $this->sgLayoutStandard = $sgLayoutStandard;

        return $this;
    }

    public function getSgLayoutFullwidth(): ?int
    {
        return $this->sgLayoutFullwidth;
    }

    public function setSgLayoutFullwidth(?int $sgLayoutFullwidth): self
    {
        $this->sgLayoutFullwidth = $sgLayoutFullwidth;

        return $this;
    }

    public function getSgPageHome(): ?int
    {
        return $this->sgPageHome;
    }

    public function setSgPageHome(?int $sgPageHome): self
    {
        $this->sgPageHome = $sgPageHome;

        return $this;
    }

    public function getSgPage404(): ?int
    {
        return $this->sgPage404;
    }

    public function setSgPage404(?int $sgPage404): self
    {
        $this->sgPage404 = $sgPage404;

        return $this;
    }

    public function getSgPageLegalNotice(): ?int
    {
        return $this->sgPageLegalNotice;
    }

    public function setSgPageLegalNotice(?int $sgPageLegalNotice): self
    {
        $this->sgPageLegalNotice = $sgPageLegalNotice;

        return $this;
    }

    public function getSgPagePrivacyPolitics(): ?int
    {
        return $this->sgPagePrivacyPolitics;
    }

    public function setSgPagePrivacyPolitics(?int $sgPagePrivacyPolitics): self
    {
        $this->sgPagePrivacyPolitics = $sgPagePrivacyPolitics;

        return $this;
    }

    public function getSgPageSitemap(): ?int
    {
        return $this->sgPageSitemap;
    }

    public function setSgPageSitemap(?int $sgPageSitemap): self
    {
        $this->sgPageSitemap = $sgPageSitemap;

        return $this;
    }

    public function getSgNotificationGatewayEmail(): ?int
    {
        return $this->sgNotificationGatewayEmail;
    }

    public function setSgNotificationGatewayEmail(?int $sgNotificationGatewayEmail): self
    {
        $this->sgNotificationGatewayEmail = $sgNotificationGatewayEmail;

        return $this;
    }

    public function getSgArticleHome(): ?int
    {
        return $this->sgArticleHome;
    }

    public function setSgArticleHome(?int $sgArticleHome): self
    {
        $this->sgArticleHome = $sgArticleHome;

        return $this;
    }

    public function getSgArticle404(): ?int
    {
        return $this->sgArticle404;
    }

    public function setSgArticle404(?int $sgArticle404): self
    {
        $this->sgArticle404 = $sgArticle404;

        return $this;
    }

    public function getSgArticleLegalNotice(): ?int
    {
        return $this->sgArticleLegalNotice;
    }

    public function setSgArticleLegalNotice(?int $sgArticleLegalNotice): self
    {
        $this->sgArticleLegalNotice = $sgArticleLegalNotice;

        return $this;
    }

    public function getSgArticlePrivacyPolitics(): ?int
    {
        return $this->sgArticlePrivacyPolitics;
    }

    public function setSgArticlePrivacyPolitics(?int $sgArticlePrivacyPolitics): self
    {
        $this->sgArticlePrivacyPolitics = $sgArticlePrivacyPolitics;

        return $this;
    }

    public function getSgArticleSitemap(): ?int
    {
        return $this->sgArticleSitemap;
    }

    public function setSgArticleSitemap(?int $sgArticleSitemap): self
    {
        $this->sgArticleSitemap = $sgArticleSitemap;

        return $this;
    }

    public function getSgContent404Headline(): ?int
    {
        return $this->sgContent404Headline;
    }

    public function setSgContent404Headline(?int $sgContent404Headline): self
    {
        $this->sgContent404Headline = $sgContent404Headline;

        return $this;
    }

    public function getSgContent404Sitemap(): ?int
    {
        return $this->sgContent404Sitemap;
    }

    public function setSgContent404Sitemap(?int $sgContent404Sitemap): self
    {
        $this->sgContent404Sitemap = $sgContent404Sitemap;

        return $this;
    }

    public function getSgContentLegalNotice(): ?int
    {
        return $this->sgContentLegalNotice;
    }

    public function setSgContentLegalNotice(?int $sgContentLegalNotice): self
    {
        $this->sgContentLegalNotice = $sgContentLegalNotice;

        return $this;
    }

    public function getSgContentPrivacyPolitics(): ?int
    {
        return $this->sgContentPrivacyPolitics;
    }

    public function setSgContentPrivacyPolitics(?int $sgContentPrivacyPolitics): self
    {
        $this->sgContentPrivacyPolitics = $sgContentPrivacyPolitics;

        return $this;
    }

    public function getSgContentSitemap(): ?int
    {
        return $this->sgContentSitemap;
    }

    public function setSgContentSitemap(?int $sgContentSitemap): self
    {
        $this->sgContentSitemap = $sgContentSitemap;

        return $this;
    }

    public function getSgFormContact(): FormContactConfig
    {
        return $this->sgFormContact;
    }

    public function setSgFormContact(FormContactConfig $sgFormContact): self
    {
        $this->sgFormContact = $sgFormContact;

        return $this;
    }

    public function getSgExtranet(): ExtranetConfig
    {
        return $this->sgExtranet;
    }

    public function setSgExtranet(ExtranetConfig $sgExtranet): self
    {
        $this->sgExtranet = $sgExtranet;

        return $this;
    }

    public function getSgEncryptionKey(): ?string
    {
        return $this->sgEncryptionKey;
    }

    public function setSgEncryptionKey(?string $sgEncryptionKey): self
    {
        $this->sgEncryptionKey = $sgEncryptionKey;

        return $this;
    }

    public function getSgFormDataManager(): FormDataManagerConfig
    {
        return $this->sgFormDataManager;
    }

    public function setSgFormDataManager(FormDataManagerConfig $sgFormDataManager): self
    {
        $this->sgFormDataManager = $sgFormDataManager;

        return $this;
    }

    public function getSgAirtableApiKey(): string
    {
        return $this->sgAirtableApiKey;
    }

    public function setSgAirtableApiKey(string $sgAirtableApiKey): self
    {
        $this->sgAirtableApiKey = $sgAirtableApiKey;

        return $this;
    }

    public function getSgContentSitemapHeadline(): ?int
    {
        return $this->sgContentSitemapHeadline;
    }

    public function setSgContentSitemapHeadline(?int $sgContentSitemapHeadline): self
    {
        $this->sgContentSitemapHeadline = $sgContentSitemapHeadline;

        return $this;
    }

    public function getSgAirtableApiKeyForRead(): string
    {
        return $this->sgAirtableApiKeyForRead;
    }

    public function setSgAirtableApiKeyForRead(string $sgAirtableApiKeyForRead): self
    {
        $this->sgAirtableApiKeyForRead = $sgAirtableApiKeyForRead;

        return $this;
    }

    public function getSgAirtableApiKeyForWrite(): string
    {
        return $this->sgAirtableApiKeyForWrite;
    }

    public function setSgAirtableApiKeyForWrite(string $sgAirtableApiKeyForWrite): self
    {
        $this->sgAirtableApiKeyForWrite = $sgAirtableApiKeyForWrite;

        return $this;
    }

    public function getSgImageSizes(): array
    {
        return $this->sgImageSizes;
    }

    public function setSgImageSizes(array $sgImageSizes): self
    {
        $this->sgImageSizes = $sgImageSizes;

        return $this;
    }

    public function getSgNotificationSupport(): ?int
    {
        return $this->sgNotificationSupport;
    }

    public function setSgNotificationSupport(?int $sgNotificationSupport): self
    {
        $this->sgNotificationSupport = $sgNotificationSupport;

        return $this;
    }

    public function getSgNotificationSupportMessageAdmin(): ?int
    {
        return $this->sgNotificationSupportMessageAdmin;
    }

    public function setSgNotificationSupportMessageAdmin(?int $sgNotificationSupportMessageAdmin): self
    {
        $this->sgNotificationSupportMessageAdmin = $sgNotificationSupportMessageAdmin;

        return $this;
    }

    public function getSgNotificationSupportMessageUser(): ?int
    {
        return $this->sgNotificationSupportMessageUser;
    }

    public function setSgNotificationSupportMessageUser(?int $sgNotificationSupportMessageUser): self
    {
        $this->sgNotificationSupportMessageUser = $sgNotificationSupportMessageUser;

        return $this;
    }

    public function getSgNotificationSupportMessageAdminLanguage(): ?int
    {
        return $this->sgNotificationSupportMessageAdminLanguage;
    }

    public function setSgNotificationSupportMessageAdminLanguage(?int $sgNotificationSupportMessageAdminLanguage): self
    {
        $this->sgNotificationSupportMessageAdminLanguage = $sgNotificationSupportMessageAdminLanguage;

        return $this;
    }

    public function getSgNotificationSupportMessageUserLanguage(): ?int
    {
        return $this->sgNotificationSupportMessageUserLanguage;
    }

    public function setSgNotificationSupportMessageUserLanguage(?int $sgNotificationSupportMessageUserLanguage): self
    {
        $this->sgNotificationSupportMessageUserLanguage = $sgNotificationSupportMessageUserLanguage;

        return $this;
    }

    public function getSgInstallLocked(): bool
    {
        return $this->sgInstallLocked;
    }

    public function setSgInstallLocked(bool $sgInstallLocked): self
    {
        $this->sgInstallLocked = $sgInstallLocked;

        return $this;
    }

    public function getSgUsePdmForMembers(): bool
    {
        return $this->sgUsePdmForMembers;
    }

    public function setSgUsePdmForMembers(bool $sgUsePdmForMembers): self
    {
        $this->sgUsePdmForMembers = $sgUsePdmForMembers;

        return $this;
    }

    protected function isSubmoduleNameKnown(string $submodule): bool
    {
        return \in_array($submodule, self::SUBMODULES_KEYS, true);
    }
}
