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

namespace Config\Manager;

use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Config\Framway as FramwayConfig;
use WEM\SmartgearBundle\Config\Manager\Framway as FramwayConfigManager;

class FramwayTest extends \Util\SmartgearTestCase
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /** @var FramwayConfigManager */
    protected $sut;

    /** @var ConfigInterface */
    protected $configuration;
    /** @var CoreConfigurationManager */
    protected $configurationManagerCore;

    protected function setUp(): void
    {
        if (!\defined('TL_ROOT')) {
            \define('TL_ROOT', codecept_data_dir());
        }

        $container = $this->getContainerWithContaoConfiguration();
        $container->setParameter('kernel.project_dir', codecept_data_dir());
        \Contao\System::setContainer($container);
        $this->getTempDir();

        $this->configuration = $this->createMock(FramwayConfig::class);
        $this->configurationManagerCore = $this->createMock(CoreConfigurationManager::class);
    }

    /**
     * [testSpecificPregReplaceForNotJsonCompliantConfigurationImport description].
     *
     * @param string $notJsonCompliant [description]
     * @param string $expectedJson     [description]
     *
     * @return [type]                   [description]
     * @dataProvider dpForSpecificPregReplaceForNotJsonCompliantConfigurationImport
     */
    public function testSpecificPregReplaceForNotJsonCompliantConfigurationImport(callable $configurationManagerCoreGen, string $notJsonCompliant, \StdClass $expectedJson)
    {
        $this->configurationManagerCore = $configurationManagerCoreGen();
        $this->sut = new FramwayConfigManager(
            $this->getTranslator(),
            $this->configuration,
            $this->configurationManagerCore
        );

        $parsedJson = $this->sut->retrieveConfigurationAsImportableFormatFromFile();

        $this->assertEquals($parsedJson, $expectedJson);
    }

    public function dpForSpecificPregReplaceForNotJsonCompliantConfigurationImport(): array
    {
        return [
            'test_1' => [
                'configurationManagerCoreGen' => function () {
                    $configurationManagerCore = $this->createMock(CoreConfigurationManager::class);
                    $configurationManagerCore
                        ->method('load')
                        ->willReturnCallback(function () {
                            return (new \WEM\SmartgearBundle\Config\Component\Core\Core())
                                ->setSgFramwayPath(codecept_data_dir().'assets/framway/test_1')
                            ;
                        })
                    ;

                    return $configurationManagerCore;
                },
                'notJsonCompliant' => file_get_contents(codecept_data_dir().'assets/framway/test_1/framway.config.js'),
                'expectedJson' => json_decode(file_get_contents(codecept_data_dir().'assets/framway/test_1/result.json'), false, 512, \JSON_THROW_ON_ERROR),
            ],
            'test_2' => [
                'configurationManagerCoreGen' => function () {
                    $configurationManagerCore = $this->createMock(CoreConfigurationManager::class);
                    $configurationManagerCore
                        ->method('load')
                        ->willReturnCallback(function () {
                            return (new \WEM\SmartgearBundle\Config\Component\Core\Core())
                                ->setSgFramwayPath(codecept_data_dir().'assets/framway/test_2')
                            ;
                        })
                    ;

                    return $configurationManagerCore;
                },
                'notJsonCompliant' => file_get_contents(codecept_data_dir().'assets/framway/test_2/framway.config.js'),
                'expectedJson' => json_decode(file_get_contents(codecept_data_dir().'assets/framway/test_2/result.json'), false, 512, \JSON_THROW_ON_ERROR),
            ],
            'test_3' => [
                'configurationManagerCoreGen' => function () {
                    $configurationManagerCore = $this->createMock(CoreConfigurationManager::class);
                    $configurationManagerCore
                        ->method('load')
                        ->willReturnCallback(function () {
                            return (new \WEM\SmartgearBundle\Config\Component\Core\Core())
                                ->setSgFramwayPath(codecept_data_dir().'assets/framway/test_3')
                            ;
                        })
                    ;

                    return $configurationManagerCore;
                },
                'notJsonCompliant' => file_get_contents(codecept_data_dir().'assets/framway/test_3/framway.config.js'),
                'expectedJson' => json_decode(file_get_contents(codecept_data_dir().'assets/framway/test_3/result.json'), false, 512, \JSON_THROW_ON_ERROR),
            ],
        ];
    }
}
