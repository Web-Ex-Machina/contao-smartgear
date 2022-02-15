<?php

namespace Classes\Config;

use WEM\SmartgearBundle\Classes\Config\Core as CoreConfig;

class CoreTest extends \Codeception\Test\Unit
{
    /** @var CoreConfig */
    protected $sut;

    protected function setUp(): void
    {
        $this->sut = new CoreConfig();
    }

    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testImport()
    {
        $json = new \stdClass();
        $json->owner = new \stdClass();
        $json->analytics = new \stdClass();
        $json->analytics->system = CoreConfig::ANALYTICS_SYSTEM_GOOGLE;
        $json->owner->domain = "toto";

        $this->sut->import($json);

        $this->assertEquals($this->sut->getSgOwnerDomain(), $json->owner->domain);
        $this->assertEquals($this->sut->getSgAnalytics(), $json->analytics->system);
    }

    public function testImportWillFailOverInvalidAnalyticsSystem()
    {
        $json = new \stdClass();
        $json->analytics = new \stdClass();
        $json->analytics->system = "pouet";

        try {
            $this->sut->import($json);
        } catch (\Exception $e) {
            $this->assertEquals('InvalidArgumentException', get_class($e));
            $this->assertEquals('Invalid analytics system given', $e->getMessage());
        }
    }

    public function testImportWillFailOverInvalidMode()
    {
        $json = new \stdClass();
        $json->mode = "pouet";

        try {
            $this->sut->import($json);
        } catch (\Exception $e) {
            $this->assertEquals('InvalidArgumentException', get_class($e));
            $this->assertEquals('Invalid mode given', $e->getMessage());
        }
    }
}
