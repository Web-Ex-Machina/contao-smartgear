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

namespace WEM\SmartgearBundle\Classes\Config;

class Core implements ConfigInterface
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
    /** @var bool */
    protected $sgInstallComplete = false;
    /** @var string */
    protected $sgVersion = '';
    /** @var string */
    protected $sgFramwayPath = '';
    /** @var array */
    protected $sgFramwayThemes = [];
    /** @var array */
    protected $sgSelectedModules = [];
    /** @var string */
    protected $sgMode = self::MODE_DEV;
    /** @var string */
    protected $sgWebsiteTitle = '';
    /** @var string */
    protected $sgOwnerEmail = '';
    /** @var string */
    protected $sgAnalytics = self::ANALYTICS_SYSTEM_NONE;
    /** @var string */
    protected $sgAnalyticsGoogleId = '';
    /** @var string */
    protected $sgAnalyticsMatomoHost = '';
    /** @var string */
    protected $sgAnalyticsMatomoId = '';
    /** @var string */
    protected $sgOwnerDomain = '';
    /** @var string */
    protected $sgOwnerHost = 'INFOMANIAK - 25 Eugène-Marziano 1227 Les Acacias - GENÈVE - SUISSE';
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

    public function reset(): self
    {
        $this->setSgInstallComplete(false)
            ->setSgVersion('')
            ->setSgSelectedModules([])
            ->setSgMode(static::MODE_DEV)
            ->setSgWebsiteTitle('')
            ->setSgFramwayPath('')
            ->setSgFramwayThemes([])
            ->setSgAnalytics(static::ANALYTICS_SYSTEM_NONE)
            ->setSgAnalyticsGoogleId('')
            ->setSgAnalyticsMatomoHost('')
            ->setSgAnalyticsMatomoId('')
            ->setSgOwnerEmail('')
            ->setSgOwnerDomain('')
            ->setSgOwnerHost('INFOMANIAK - 25 Eugène-Marziano 1227 Les Acacias - GENÈVE - SUISSE')
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
        ;

        return $this;
    }

    public function import(\stdClass $json): self
    {
        $this->setSgInstallComplete($json->installComplete ?? false)
            ->setSgVersion($json->version ?? '')
            ->setSgSelectedModules($json->selectedModules ?? [])
            ->setSgMode($json->mode ?? static::MODE_DEV)
            ->setSgWebsiteTitle($json->websiteTitle ?? '')
            ->setSgFramwayPath($json->framway->path ?? '')
            ->setSgFramwayThemes($json->framway->themes ?? [])
            ->setSgAnalytics($json->analytics->system ?? static::ANALYTICS_SYSTEM_NONE)
            ->setSgAnalyticsGoogleId($json->analytics->google->id ?? '')
            ->setSgAnalyticsMatomoHost($json->analytics->matomo->host ?? '')
            ->setSgAnalyticsMatomoId($json->analytics->matomo->id ?? '')
            ->setSgOwnerEmail($json->owner->email ?? '')
            ->setSgOwnerDomain($json->owner->domain ?? '')
            ->setSgOwnerHost($json->owner->host ?? 'INFOMANIAK - 25 Eugène-Marziano 1227 Les Acacias - GENÈVE - SUISSE')
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

        return json_encode($json, JSON_PRETTY_PRINT);
    }

    /**
     * @return string
     */
    public function getSgVersion(): string
    {
        return $this->sgVersion;
    }

    /**
     * @param string $sgVersion
     *
     * @return self
     */
    public function setSgVersion(string $sgVersion): self
    {
        $this->sgVersion = $sgVersion;

        return $this;
    }

    /**
     * @return string
     */
    public function getSgFramwayPath(): string
    {
        return $this->sgFramwayPath;
    }

    /**
     * @param string $sgFramwayPath
     *
     * @return self
     */
    public function setSgFramwayPath(string $sgFramwayPath): self
    {
        $this->sgFramwayPath = $sgFramwayPath;

        return $this;
    }

    /**
     * @return array
     */
    public function getSgFramwayThemes(): array
    {
        return $this->sgFramwayThemes;
    }

    /**
     * @param array $sgFramwayThemes
     *
     * @return self
     */
    public function setSgFramwayThemes(array $sgFramwayThemes): self
    {
        $this->sgFramwayThemes = $sgFramwayThemes;

        return $this;
    }

    /**
     * @return array
     */
    public function getSgSelectedModules(): array
    {
        return $this->sgSelectedModules;
    }

    /**
     * @param array $sgSelectedModules
     *
     * @return self
     */
    public function setSgSelectedModules(array $sgSelectedModules): self
    {
        $this->sgSelectedModules = $sgSelectedModules;

        return $this;
    }

    /**
     * @return string
     */
    public function getSgMode(): string
    {
        return $this->sgMode;
    }

    /**
     * @param string $sgMode
     *
     * @return self
     */
    public function setSgMode(string $sgMode): self
    {
        if (!in_array($sgMode, static::MODES_ALLOWED)) {
            throw new \InvalidArgumentException(sprintf('Invalid mode "%s" given', $sgMode));
        }

        $this->sgMode = $sgMode;

        return $this;
    }

    /**
     * @return string
     */
    public function getSgWebsiteTitle(): string
    {
        return $this->sgWebsiteTitle;
    }

    /**
     * @param string $sgWebsiteTitle
     *
     * @return self
     */
    public function setSgWebsiteTitle(string $sgWebsiteTitle): self
    {
        $this->sgWebsiteTitle = $sgWebsiteTitle;

        return $this;
    }

    /**
     * @return string
     */
    public function getSgOwnerEmail(): string
    {
        return $this->sgOwnerEmail;
    }

    /**
     * @param string $sgOwnerEmail
     *
     * @return self
     */
    public function setSgOwnerEmail(string $sgOwnerEmail): self
    {
        $this->sgOwnerEmail = $sgOwnerEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getSgAnalytics(): string
    {
        return $this->sgAnalytics;
    }

    /**
     * @param string $sgAnalytics
     *
     * @return self
     */
    public function setSgAnalytics(string $sgAnalytics): self
    {
        if (!in_array($sgAnalytics, static::ANALYTICS_SYSTEMS_ALLOWED)) {
            throw new \InvalidArgumentException(sprintf('Invalid analytics system "%s" given', $sgAnalytics));
        }

        $this->sgAnalytics = $sgAnalytics;

        return $this;
    }

    /**
     * @return string
     */
    public function getSgAnalyticsGoogleId(): string
    {
        return $this->sgAnalyticsGoogleId;
    }

    /**
     * @param string $sgAnalyticsGoogleId
     *
     * @return self
     */
    public function setSgAnalyticsGoogleId(string $sgAnalyticsGoogleId): self
    {
        $this->sgAnalyticsGoogleId = $sgAnalyticsGoogleId;

        return $this;
    }

    /**
     * @return string
     */
    public function getSgAnalyticsMatomoHost(): string
    {
        return $this->sgAnalyticsMatomoHost;
    }

    /**
     * @param string $sgAnalyticsMatomoHost
     *
     * @return self
     */
    public function setSgAnalyticsMatomoHost(string $sgAnalyticsMatomoHost): self
    {
        $this->sgAnalyticsMatomoHost = $sgAnalyticsMatomoHost;

        return $this;
    }

    /**
     * @return string
     */
    public function getSgAnalyticsMatomoId(): string
    {
        return $this->sgAnalyticsMatomoId;
    }

    /**
     * @param string $sgAnalyticsMatomoId
     *
     * @return self
     */
    public function setSgAnalyticsMatomoId(string $sgAnalyticsMatomoId): self
    {
        $this->sgAnalyticsMatomoId = $sgAnalyticsMatomoId;

        return $this;
    }

    /**
     * @return string
     */
    public function getSgOwnerDomain(): string
    {
        return $this->sgOwnerDomain;
    }

    /**
     * @param string $sgOwnerDomain
     *
     * @return self
     */
    public function setSgOwnerDomain(string $sgOwnerDomain): self
    {
        $this->sgOwnerDomain = $sgOwnerDomain;

        return $this;
    }

    /**
     * @return string
     */
    public function getSgOwnerHost(): string
    {
        return $this->sgOwnerHost;
    }

    /**
     * @param string $sgOwnerHost
     *
     * @return self
     */
    public function setSgOwnerHost(string $sgOwnerHost): self
    {
        $this->sgOwnerHost = $sgOwnerHost;

        return $this;
    }

    /**
     * @return string
     */
    public function getSgOwnerLogo(): string
    {
        return $this->sgOwnerLogo;
    }

    /**
     * @param string $sgOwnerLogo
     *
     * @return self
     */
    public function setSgOwnerLogo(string $sgOwnerLogo): self
    {
        $this->sgOwnerLogo = $sgOwnerLogo;

        return $this;
    }

    /**
     * @return string
     */
    public function getSgOwnerStatus(): string
    {
        return $this->sgOwnerStatus;
    }

    /**
     * @param string $sgOwnerStatus
     *
     * @return self
     */
    public function setSgOwnerStatus(string $sgOwnerStatus): self
    {
        $this->sgOwnerStatus = $sgOwnerStatus;

        return $this;
    }

    /**
     * @return string
     */
    public function getSgOwnerStreet(): string
    {
        return $this->sgOwnerStreet;
    }

    /**
     * @param string $sgOwnerStreet
     *
     * @return self
     */
    public function setSgOwnerStreet(string $sgOwnerStreet): self
    {
        $this->sgOwnerStreet = $sgOwnerStreet;

        return $this;
    }

    /**
     * @return string
     */
    public function getSgOwnerPostal(): string
    {
        return $this->sgOwnerPostal;
    }

    /**
     * @param string $sgOwnerPostal
     *
     * @return self
     */
    public function setSgOwnerPostal(string $sgOwnerPostal): self
    {
        $this->sgOwnerPostal = $sgOwnerPostal;

        return $this;
    }

    /**
     * @return string
     */
    public function getSgOwnerCity(): string
    {
        return $this->sgOwnerCity;
    }

    /**
     * @param string $sgOwnerCity
     *
     * @return self
     */
    public function setSgOwnerCity(string $sgOwnerCity): self
    {
        $this->sgOwnerCity = $sgOwnerCity;

        return $this;
    }

    /**
     * @return string
     */
    public function getSgOwnerRegion(): string
    {
        return $this->sgOwnerRegion;
    }

    /**
     * @param string $sgOwnerRegion
     *
     * @return self
     */
    public function setSgOwnerRegion(string $sgOwnerRegion): self
    {
        $this->sgOwnerRegion = $sgOwnerRegion;

        return $this;
    }

    /**
     * @return string
     */
    public function getSgOwnerCountry(): string
    {
        return $this->sgOwnerCountry;
    }

    /**
     * @param string $sgOwnerCountry
     *
     * @return self
     */
    public function setSgOwnerCountry(string $sgOwnerCountry): self
    {
        $this->sgOwnerCountry = $sgOwnerCountry;

        return $this;
    }

    /**
     * @return string
     */
    public function getSgOwnerSiret(): string
    {
        return $this->sgOwnerSiret;
    }

    /**
     * @param string $sgOwnerSiret
     *
     * @return self
     */
    public function setSgOwnerSiret(string $sgOwnerSiret): self
    {
        $this->sgOwnerSiret = $sgOwnerSiret;

        return $this;
    }

    /**
     * @return string
     */
    public function getSgOwnerDpoName(): string
    {
        return $this->sgOwnerDpoName;
    }

    /**
     * @param string $sgOwnerDpoName
     *
     * @return self
     */
    public function setSgOwnerDpoName(string $sgOwnerDpoName): self
    {
        $this->sgOwnerDpoName = $sgOwnerDpoName;

        return $this;
    }

    /**
     * @return string
     */
    public function getSgOwnerDpoEmail(): string
    {
        return $this->sgOwnerDpoEmail;
    }

    /**
     * @param string $sgOwnerDpoEmail
     *
     * @return self
     */
    public function setSgOwnerDpoEmail(string $sgOwnerDpoEmail): self
    {
        $this->sgOwnerDpoEmail = $sgOwnerDpoEmail;

        return $this;
    }

    /**
     * @return bool
     */
    public function getSgInstallComplete(): bool
    {
        return $this->sgInstallComplete;
    }

    /**
     * @param bool $sgInstallComplete
     *
     * @return self
     */
    public function setSgInstallComplete(bool $sgInstallComplete): self
    {
        $this->sgInstallComplete = $sgInstallComplete;

        return $this;
    }
}
