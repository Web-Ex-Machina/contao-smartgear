<?php

namespace Classes\Config;

use WEM\SmartgearBundle\Classes\Config\Core as CoreConfig;
use WEM\SmartgearBundle\Classes\Config\Manager;

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
        $this->configurationFilePath = codecept_data_dir() . '/assets/smartgear/config_core.json';
        $this->sut = new Manager(new CoreConfig(), $this->configurationFilePath);
    }

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testLoad()
    {
        $json = $this->sut->retrieveConfigurationAsStdClass();
        $config = $this->sut->load();

        $this->assertEquals($config->getSgVersion(), $json->version);
        $this->assertEquals($config->getSgSelectedModules(), $json->selectedModules);
        $this->assertEquals($config->getSgMode(), $json->mode);
        $this->assertEquals($config->getSgWebsiteTitle(), $json->websiteTitle);
        $this->assertEquals($config->getSgFramwayPath(), $json->framway->path);
        $this->assertEquals($config->getSgFramwayThemes(), $json->framway->themes);
        $this->assertEquals($config->getSgAnalytics(), $json->analytics->system);
        $this->assertEquals($config->getSgAnalyticsGoogleId(), $json->analytics->google->id);
        $this->assertEquals($config->getSgAnalyticsMatomoHost(), $json->analytics->matomo->host);
        $this->assertEquals($config->getSgAnalyticsMatomoId(), $json->analytics->matomo->id);
        $this->assertEquals($config->getSgOwnerEmail(), $json->owner->email);
        $this->assertEquals($config->getSgOwnerDomain(), $json->owner->domain);
        $this->assertEquals($config->getSgOwnerHost(), $json->owner->host);
        $this->assertEquals($config->getSgOwnerLogo(), $json->owner->logo);
        $this->assertEquals($config->getSgOwnerStatus(), $json->owner->status);
        $this->assertEquals($config->getSgOwnerStreet(), $json->owner->street);
        $this->assertEquals($config->getSgOwnerPostal(), $json->owner->postal);
        $this->assertEquals($config->getSgOwnerCity(), $json->owner->city);
        $this->assertEquals($config->getSgOwnerRegion(), $json->owner->region);
        $this->assertEquals($config->getSgOwnerCountry(), $json->owner->country);
        $this->assertEquals($config->getSgOwnerSiret(), $json->owner->siret);
        $this->assertEquals($config->getSgOwnerDpoName(), $json->owner->dpo->name);
        $this->assertEquals($config->getSgOwnerDpoEmail(), $json->owner->dpo->email);
    }
}
