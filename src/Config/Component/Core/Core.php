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

namespace WEM\SmartgearBundle\Config\Component\Core;

use WEM\SmartgearBundle\Classes\Config\ConfigModuleInterface;
use WEM\SmartgearBundle\Config\Component\Blog\Blog as BlogConfig;
use WEM\SmartgearBundle\Config\Component\Events\Events as EventsConfig;
use WEM\SmartgearBundle\Config\Component\Faq\Faq as FaqConfig;

class Core implements ConfigModuleInterface
{
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
    public const DEFAULT_MODE = self::MODE_DEV;
    public const DEFAULT_FRAMWAY_PATH = 'assets/framway';
    public const DEFAULT_OWNER_HOST = 'INFOMANIAK - 25 Eugène-Marziano 1227 Les Acacias - GENÈVE - SUISSE';
    public const DEFAULT_GOOGLE_FONTS = ['Exo', 'OpenSans', 'Shizuru'];
    public const DEFAULT_USER_USERNAME = 'webmaster';
    public const DEFAULT_USER_GROUP_ADMIN_NAME = 'Administrateurs';
    public const DEFAULT_ROOTPAGE_CHMOD = 'a:12:{i:0;s:2:"u1";i:1;s:2:"u2";i:2;s:2:"u3";i:3;s:2:"u4";i:4;s:2:"u5";i:5;s:2:"u6";i:6;s:2:"g1";i:7;s:2:"g2";i:8;s:2:"g3";i:9;s:2:"g4";i:10;s:2:"g5";i:11;s:2:"g6";}';

    public const DEFAULT_API_KEY = 'api-key-to-change';
    public const DEFAULT_CLIENT_FILES_FOLDER = 'files'.\DIRECTORY_SEPARATOR.'media';
    public const DEFAULT_CLIENT_LOGOS_FOLDER = 'files'.\DIRECTORY_SEPARATOR.'media'.\DIRECTORY_SEPARATOR.'logos';
    /** @var bool */
    protected $sgInstallComplete = false;
    /** @var string */
    protected $sgVersion = self::DEFAULT_VERSION;
    /** @var string */
    protected $sgFramwayPath = self::DEFAULT_FRAMWAY_PATH;
    /** @var array */
    protected $sgFramwayThemes = [];
    /** @var array */
    protected $sgGoogleFonts = self::DEFAULT_GOOGLE_FONTS;
    /** @var array */
    protected $sgSelectedModules = [];
    /** @var string */
    protected $sgMode = self::DEFAULT_MODE;
    /** @var string */
    protected $sgWebsiteTitle = '';
    /** @var string */
    protected $sgOwnerEmail = '';
    /** @var string */
    protected $sgAnalytics = self::DEFAULT_ANALYTICS_SYSTEM;
    /** @var string */
    protected $sgAnalyticsGoogleId = '';
    /** @var string */
    protected $sgAnalyticsMatomoHost = '';
    /** @var string */
    protected $sgAnalyticsMatomoId = '';
    /** @var string */
    protected $sgOwnerName = '';
    /** @var string */
    protected $sgOwnerDomain = '';
    /** @var string */
    protected $sgOwnerHost = '';
    /** @var string */
    protected $sgOwnerLogo = '';
    /** @var string */
    protected $sgOwnerStatus = '';
    /** @var string */
    protected $sgOwnerStreet = '';
    /** @var string */
    protected $sgOwnerPostal = '';
    /** @var string */
    protected $sgOwnerCity = '';
    /** @var string */
    protected $sgOwnerRegion = '';
    /** @var string */
    protected $sgOwnerCountry = '';
    /** @var string */
    protected $sgOwnerSiret = '';
    /** @var string */
    protected $sgOwnerDpoName = '';
    /** @var string */
    protected $sgOwnerDpoEmail = '';
    /** @var int */
    protected $sgTheme;
    /** @var int */
    protected $sgPageRoot;
    /** @var int */
    protected $sgPageHome;
    /** @var int */
    protected $sgPage404;
    /** @var int */
    protected $sgPageLegalNotice;
    /** @var int */
    protected $sgPagePrivacyPolitics;
    /** @var int */
    protected $sgPageSitemap;
    /** @var int */
    protected $sgArticleHome;
    /** @var int */
    protected $sgArticle404;
    /** @var int */
    protected $sgArticleLegalNotice;
    /** @var int */
    protected $sgArticlePrivacyPolitics;
    /** @var int */
    protected $sgArticleSitemap;
    /** @var int */
    protected $sgContent404Headline;
    /** @var int */
    protected $sgContent404Sitemap;
    /** @var int */
    protected $sgContentLegalNotice;
    /** @var int */
    protected $sgContentPrivacyPolitics;
    /** @var int */
    protected $sgContentSitemap;
    /** @var int */
    protected $sgUserWebmaster;
    /** @var int */
    protected $sgUserGroupWebmasters;
    /** @var int */
    protected $sgUserGroupAdministrators;
    /** @var int */
    protected $sgLayoutStandard;
    /** @var int */
    protected $sgLayoutFullwidth;
    /** @var int */
    protected $sgNotificationGatewayEmail;
    /** @var array */
    protected $sgModules = [];
    /** @var string */
    protected $sgApiKey = self::DEFAULT_API_KEY;
    /** @var BlogConfig */
    protected $sgBlog;
    /** @var EventsConfig */
    protected $sgEvents;
    /** @var FaqConfig */
    protected $sgFaq;

    public function reset(): self
    {
        $this->setSgInstallComplete(false)
            ->setSgVersion(static::DEFAULT_VERSION)
            ->setSgTheme(null)
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
            ->setSgContentSitemap(null)
            ->setSgUserWebmaster(null)
            ->setSgUserGroupWebmasters(null)
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
            ->setSgAnalyticsMatomoHost('')
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
            ->setSgApiKey(self::DEFAULT_API_KEY)
            ->setSgBlog((new BlogConfig())->reset())
            ->setSgEvents((new EventsConfig())->reset())
            ->setSgFaq((new FaqConfig())->reset())
        ;

        return $this;
    }

    public function import(\stdClass $json): self
    {
        $this->setSgInstallComplete($json->installComplete ?? false)
            ->setSgVersion($json->version ?? static::DEFAULT_VERSION)
            ->setSgTheme($json->contao->theme ?? null)
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
            ->setSgContentSitemap($json->contao->contents->sitemap ?? null)
            ->setSgUserWebmaster($json->contao->users->webmaster ?? null)
            ->setSgUserGroupWebmasters($json->contao->userGroups->webmasters ?? null)
            ->setSgUserGroupAdministrators($json->contao->userGroups->administrators ?? null)
            ->setSgModules($json->contao->modules ?? [])
            ->setSgSelectedModules($json->selectedModules ?? [])
            ->setSgMode($json->mode ?? static::DEFAULT_MODE)
            ->setSgWebsiteTitle($json->websiteTitle ?? '')
            ->setSgNotificationGatewayEmail($json->contao->notificationGateways->email ?? '')
            ->setSgFramwayPath($json->framway->path ?? self::DEFAULT_FRAMWAY_PATH)
            ->setSgFramwayThemes($json->framway->themes ?? [])
            ->setSgAnalytics($json->analytics->system ?? static::DEFAULT_ANALYTICS_SYSTEM)
            ->setSgAnalyticsGoogleId($json->analytics->google->id ?? '')
            ->setSgAnalyticsMatomoHost($json->analytics->matomo->host ?? '')
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
            ->setSgApiKey($json->api->key ?? self::DEFAULT_API_KEY)
            ->setSgBlog(
                $json->blog
                ? (new BlogConfig())->import($json->blog)
                : (new BlogConfig())->reset()
            )
            ->setSgEvents(
                $json->events
                ? (new EventsConfig())->import($json->events)
                : (new EventsConfig())->reset()
            )
            ->setSgFaq(
                $json->faq
                ? (new FaqConfig())->import($json->faq)
                : (new FaqConfig())->reset()
            )
        ;

        return $this;
    }

    public function export(): string
    {
        $json = new \stdClass();
        $json->installComplete = $this->getSgInstallComplete();
        $json->version = $this->getSgVersion();
        $json->selectedModules = $this->getSgSelectedModules();
        $json->mode = $this->getSgMode();
        $json->websiteTitle = $this->getSgWebsiteTitle();
        $json->googleFonts = $this->getSgGoogleFonts();

        $json->contao = new \stdClass();
        $json->contao->theme = $this->getSgTheme();
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
        $json->contao->contents->sitemap = $this->getSgContentSitemap();

        $json->contao->users = new \stdClass();
        $json->contao->users->webmaster = $this->getSgUserWebmaster();

        $json->contao->userGroups = new \stdClass();
        $json->contao->userGroups->webmasters = $this->getSgUserGroupWebmasters();
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

        $json->blog = $this->getSgBlog()->export();
        $json->events = $this->getSgEvents()->export();
        $json->faq = $this->getSgFaq()->export();

        return json_encode($json, \JSON_PRETTY_PRINT);
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

    public function getSgUserGroupWebmasters(): ?int
    {
        return $this->sgUserGroupWebmasters;
    }

    public function setSgUserGroupWebmasters(?int $sgUserGroupWebmasters): self
    {
        $this->sgUserGroupWebmasters = $sgUserGroupWebmasters;

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
}