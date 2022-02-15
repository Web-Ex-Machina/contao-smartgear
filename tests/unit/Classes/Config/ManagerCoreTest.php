<?php

namespace Classes\Config;

use WEM\SmartgearBundle\Classes\Config\Core as CoreConfig;
use WEM\SmartgearBundle\Classes\Config\Manager;

class ManagerCoreTest extends \Codeception\Test\Unit
{
    /** @var CoreConfig */
    protected $sut;

    protected $configurationFilePath;

    protected function setUp(): void
    {
        $this->configurationFilePath = codecept_data_dir() . '/assets/smartgear/config_core.json';
        $this->sut = new Manager(new CoreConfig(), $this->configurationFilePath);
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
    public function testLoad()
    {
        $json = $this->sut->retrieveConfigurationAsStdClass();
        $config = $this->sut->load();
        $this->assertEquals($config->getSgMode(), $json->mode);
    }
}
