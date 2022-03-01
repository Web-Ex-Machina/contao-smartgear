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

namespace Config;

use WEM\SmartgearBundle\Config\Core as CoreConfig;

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
    public function testImport(): void
    {
        $json = new \stdClass();
        $json->installComplete = true;
        $json->version = '1';
        $json->selectedModules = ['foo', 'bar'];
        $json->mode = CoreConfig::MODE_DEV;
        $json->websiteTitle = 'bar';

        $json->framway = new \stdClass();
        $json->framway->path = '/path/to/somwhere';
        $json->framway->themes = ['theme_foo', 'theme_bar'];

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

        $this->assertSame($this->sut->getSgInstallComplete(), $json->installComplete);
        $this->assertSame($this->sut->getSgVersion(), $json->version);
        $this->assertSame($this->sut->getSgSelectedModules(), $json->selectedModules);
        $this->assertSame($this->sut->getSgMode(), $json->mode);
        $this->assertSame($this->sut->getSgWebsiteTitle(), $json->websiteTitle);
        $this->assertSame($this->sut->getSgFramwayPath(), $json->framway->path);
        $this->assertSame($this->sut->getSgFramwayThemes(), $json->framway->themes);
        $this->assertSame($this->sut->getSgAnalytics(), $json->analytics->system);
        $this->assertSame($this->sut->getSgAnalyticsGoogleId(), $json->analytics->google->id);
        $this->assertSame($this->sut->getSgAnalyticsMatomoHost(), $json->analytics->matomo->host);
        $this->assertSame($this->sut->getSgAnalyticsMatomoId(), $json->analytics->matomo->id);
        $this->assertSame($this->sut->getSgOwnerEmail(), $json->owner->email);
        $this->assertSame($this->sut->getSgOwnerDomain(), $json->owner->domain);
        $this->assertSame($this->sut->getSgOwnerHost(), $json->owner->host);
        $this->assertSame($this->sut->getSgOwnerLogo(), $json->owner->logo);
        $this->assertSame($this->sut->getSgOwnerStatus(), $json->owner->status);
        $this->assertSame($this->sut->getSgOwnerStreet(), $json->owner->street);
        $this->assertSame($this->sut->getSgOwnerPostal(), $json->owner->postal);
        $this->assertSame($this->sut->getSgOwnerCity(), $json->owner->city);
        $this->assertSame($this->sut->getSgOwnerRegion(), $json->owner->region);
        $this->assertSame($this->sut->getSgOwnerCountry(), $json->owner->country);
        $this->assertSame($this->sut->getSgOwnerSiret(), $json->owner->siret);
        $this->assertSame($this->sut->getSgOwnerDpoName(), $json->owner->dpo->name);
        $this->assertSame($this->sut->getSgOwnerDpoEmail(), $json->owner->dpo->email);
    }

    public function testImportWillFailOverInvalidAnalyticsSystem(): void
    {
        $json = new \stdClass();
        $json->analytics = new \stdClass();
        $json->analytics->system = 'foo';

        try {
            $this->sut->import($json);
        } catch (\Exception $e) {
            $this->assertSame('InvalidArgumentException', \get_class($e));
            $this->assertSame('Invalid analytics system "foo" given', $e->getMessage());
        }
    }

    public function testImportWillFailOverInvalidMode(): void
    {
        $json = new \stdClass();
        $json->mode = 'foo';

        try {
            $this->sut->import($json);
        } catch (\Exception $e) {
            $this->assertSame('InvalidArgumentException', \get_class($e));
            $this->assertSame('Invalid mode "foo" given', $e->getMessage());
        }
    }
}
