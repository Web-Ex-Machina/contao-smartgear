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

namespace WEM\SmartgearBundle\Config;

use WEM\SmartgearBundle\Classes\Config\ConfigModuleInterface;

class Core implements ConfigModuleInterface
{
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

    public const DEFAULT_ANALYTICS_SYSTEM = self::ANALYTICS_SYSTEM_NONE;
    public const DEFAULT_MODE = self::MODE_DEV;
    public const DEFAULT_FRAMWAY_PATH = 'assets/framway';
    public const DEFAULT_OWNER_HOST = 'INFOMANIAK - 25 Eugène-Marziano 1227 Les Acacias - GENÈVE - SUISSE';
    public const DEFAULT_GOOGLE_FONTS = ['Exo', 'OpenSans', 'Shizuru'];
    public const DEFAULT_USER_USERNAME = 'webmaster';
    public const DEFAULT_USER_GROUP_ADMIN_NAME = 'Administrateurs';
    public const DEFAULT_ROOTPAGE_CHMOD = 'a:12:{i:0;s:2:"u1";i:1;s:2:"u2";i:2;s:2:"u3";i:3;s:2:"u4";i:4;s:2:"u5";i:5;s:2:"u6";i:6;s:2:"g1";i:7;s:2:"g2";i:8;s:2:"g3";i:9;s:2:"g4";i:10;s:2:"g5";i:11;s:2:"g6";}';
    /** @var bool */
    protected $sgInstallComplete = false;
    /** @var string */
    protected $sgVersion = '';
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
    /** @var string */
    protected $sgTheme = '';
    /** @var array */
    protected $sgModules = [];

    public function reset(): self
    {
        $this->setSgInstallComplete(false)
            ->setSgVersion('')
            ->setSgTheme('')
            ->setSgModules([])
            ->setSgSelectedModules([])
            ->setSgMode(static::DEFAULT_MODE)
            ->setSgWebsiteTitle('')
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
        ;

        return $this;
    }

    public function import(\stdClass $json): self
    {
        $this->setSgInstallComplete($json->installComplete ?? false)
            ->setSgVersion($json->version ?? '')
            ->setSgTheme($json->theme ?? '')
            ->setSgModules($json->modules ?? [])
            ->setSgSelectedModules($json->selectedModules ?? [])
            ->setSgMode($json->mode ?? static::DEFAULT_MODE)
            ->setSgWebsiteTitle($json->websiteTitle ?? '')
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
        ;

        return $this;
    }

    public function export(): string
    {
        $json = new \stdClass();
        $json->installComplete = $this->getSgInstallComplete();
        $json->version = $this->getSgVersion();
        $json->theme = $this->getSgTheme();
        $json->version = $this->getSgVersion();
        $json->selectedModules = $this->getSgSelectedModules();
        $json->modules = $this->getSgModules();
        $json->mode = $this->getSgMode();
        $json->websiteTitle = $this->getSgWebsiteTitle();
        $json->googleFonts = $this->getSgGoogleFonts();

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

    public function getSgTheme(): string
    {
        return $this->sgTheme;
    }

    public function setSgTheme(string $sgTheme): self
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
}
