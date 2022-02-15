<?php

namespace Classes\Config;

use WEM\SmartgearBundle\Classes\Config\Core as CoreConfig;

class CoreTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /** @var CoreConfig */
    protected $sut;

    protected function setUp(): void
    {
        $this->sut = new CoreConfig();
    }

    // tests
    public function testImport()
    {
        $json = new \stdClass();
        $json->version = '1';
        $json->selectedModules = ['foo','bar'];
        $json->mode = CoreConfig::MODE_DEV;
        $json->websiteTitle = 'bar';

        $json->framway = new \stdClass();
        $json->framway->path = '/path/to/somwhere';
        $json->framway->themes = ['theme_foo','theme_bar'];

        $json->analytics = new \stdClass();
        $json->analytics->system = CoreConfig::ANALYTICS_SYSTEM_MATOMO;
        $json->analytics->google = new \stdClass();
        $json->analytics->google->id = '';
        $json->analytics->matomo = new \stdClass();
        $json->analytics->matomo->host = 'foobar';
        $json->analytics->matomo->id = 'barfoo';

        $json->owner = new \stdClass();
        $json->owner->email = '';
        $json->owner->domain = '';
        $json->owner->host = '';
        $json->owner->logo = '';
        $json->owner->status = '';
        $json->owner->street = '';
        $json->owner->postal = '';
        $json->owner->city = '';
        $json->owner->region = '';
        $json->owner->country = '';
        $json->owner->siret = '';
        $json->owner->dpo = new \stdClass();
        $json->owner->dpo->name = '';
        $json->owner->dpo->email = '';

        $this->sut->import($json);

        $this->assertEquals($this->sut->getSgVersion(), $json->version);
        $this->assertEquals($this->sut->getSgSelectedModules(), $json->selectedModules);
        $this->assertEquals($this->sut->getSgMode(), $json->mode);
        $this->assertEquals($this->sut->getSgWebsiteTitle(), $json->websiteTitle);
        $this->assertEquals($this->sut->getSgFramwayPath(), $json->framway->path);
        $this->assertEquals($this->sut->getSgFramwayThemes(), $json->framway->themes);
        $this->assertEquals($this->sut->getSgAnalytics(), $json->analytics->system);
        $this->assertEquals($this->sut->getSgAnalyticsGoogleId(), $json->analytics->google->id);
        $this->assertEquals($this->sut->getSgAnalyticsMatomoHost(), $json->analytics->matomo->host);
        $this->assertEquals($this->sut->getSgAnalyticsMatomoId(), $json->analytics->matomo->id);
        $this->assertEquals($this->sut->getSgOwnerEmail(), $json->owner->email);
        $this->assertEquals($this->sut->getSgOwnerDomain(), $json->owner->domain);
        $this->assertEquals($this->sut->getSgOwnerHost(), $json->owner->host);
        $this->assertEquals($this->sut->getSgOwnerLogo(), $json->owner->logo);
        $this->assertEquals($this->sut->getSgOwnerStatus(), $json->owner->status);
        $this->assertEquals($this->sut->getSgOwnerStreet(), $json->owner->street);
        $this->assertEquals($this->sut->getSgOwnerPostal(), $json->owner->postal);
        $this->assertEquals($this->sut->getSgOwnerCity(), $json->owner->city);
        $this->assertEquals($this->sut->getSgOwnerRegion(), $json->owner->region);
        $this->assertEquals($this->sut->getSgOwnerCountry(), $json->owner->country);
        $this->assertEquals($this->sut->getSgOwnerSiret(), $json->owner->siret);
        $this->assertEquals($this->sut->getSgOwnerDpoName(), $json->owner->dpo->name);
        $this->assertEquals($this->sut->getSgOwnerDpoEmail(), $json->owner->dpo->email);
    }

    public function testImportWillFailOverInvalidAnalyticsSystem()
    {
        $json = new \stdClass();
        $json->analytics = new \stdClass();
        $json->analytics->system = "foo";

        try {
            $this->sut->import($json);
        } catch (\Exception $e) {
            $this->assertEquals('InvalidArgumentException', get_class($e));
            $this->assertEquals('Invalid analytics system "foo" given', $e->getMessage());
        }
    }

    public function testImportWillFailOverInvalidMode()
    {
        $json = new \stdClass();
        $json->mode = "foo";

        try {
            $this->sut->import($json);
        } catch (\Exception $e) {
            $this->assertEquals('InvalidArgumentException', get_class($e));
            $this->assertEquals('Invalid mode "foo" given', $e->getMessage());
        }
    }
}
