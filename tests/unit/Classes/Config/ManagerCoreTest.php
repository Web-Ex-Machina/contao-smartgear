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

namespace Classes\Config;

use WEM\SmartgearBundle\Classes\Config\Manager;
use WEM\SmartgearBundle\Config\Core as CoreConfig;

class ManagerCoreTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /** @var CoreConfig */
    protected $sut;

    protected $configurationFilePath;

    protected function setUp(): void
    {
        $this->configurationFilePath = codecept_data_dir().'/assets/smartgear/config_core.json';
        $this->sut = new Manager(new CoreConfig(), $this->configurationFilePath);
    }

    // tests
    public function testLoad(): void
    {
        $json = $this->sut->retrieveConfigurationAsStdClassFromFile();
        $config = $this->sut->load();

        $this->assertSame($config->getSgInstallComplete(), $json->installComplete);
        $this->assertSame($config->getSgVersion(), $json->version);
        $this->assertSame($config->getSgSelectedModules(), $json->selectedModules);
        $this->assertSame($config->getSgMode(), $json->mode);
        $this->assertSame($config->getSgWebsiteTitle(), $json->websiteTitle);
        $this->assertSame($config->getSgFramwayPath(), $json->framway->path);
        $this->assertSame($config->getSgFramwayThemes(), $json->framway->themes);
        $this->assertSame($config->getSgAnalytics(), $json->analytics->system);
        $this->assertSame($config->getSgAnalyticsGoogleId(), $json->analytics->google->id);
        $this->assertSame($config->getSgAnalyticsMatomoHost(), $json->analytics->matomo->host);
        $this->assertSame($config->getSgAnalyticsMatomoId(), $json->analytics->matomo->id);
        $this->assertSame($config->getSgOwnerEmail(), $json->owner->email);
        $this->assertSame($config->getSgOwnerDomain(), $json->owner->domain);
        $this->assertSame($config->getSgOwnerHost(), $json->owner->host);
        $this->assertSame($config->getSgOwnerLogo(), $json->owner->logo);
        $this->assertSame($config->getSgOwnerStatus(), $json->owner->status);
        $this->assertSame($config->getSgOwnerStreet(), $json->owner->street);
        $this->assertSame($config->getSgOwnerPostal(), $json->owner->postal);
        $this->assertSame($config->getSgOwnerCity(), $json->owner->city);
        $this->assertSame($config->getSgOwnerRegion(), $json->owner->region);
        $this->assertSame($config->getSgOwnerCountry(), $json->owner->country);
        $this->assertSame($config->getSgOwnerSiret(), $json->owner->siret);
        $this->assertSame($config->getSgOwnerDpoName(), $json->owner->dpo->name);
        $this->assertSame($config->getSgOwnerDpoEmail(), $json->owner->dpo->email);
    }

    protected function _before(): void
    {
    }

    protected function _after(): void
    {
    }
}
