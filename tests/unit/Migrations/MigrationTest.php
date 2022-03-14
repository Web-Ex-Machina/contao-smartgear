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

use Contao\TestCase\ContaoTestCase;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\Migration\Result as MigrationResult;
use WEM\SmartgearBundle\Classes\Version\Comparator as VersionComparator;
use WEM\SmartgearBundle\Config\Core as CoreConfig;

require_once realpath(__DIR__.'/../../../tests/_data/migrations/1-0-1/Migration.php');

class MigrationTest extends ContaoTestCase
{
    /** @var \UnitTester */
    protected $tester;
    /** @var Migration */
    protected $sut;
    /** @var CoreConfigurationManager */
    protected $configManager;
    /** @var VersionComparator */
    protected $versionComparator;

    protected function setUp(): void
    {
        if (!\defined('TL_ROOT')) {
            \define('TL_ROOT', sys_get_temp_dir());
        }

        $container = $this->getContainerWithContaoConfiguration();
        $container->setParameter('kernel.project_dir', realpath(__DIR__.'/../../../tests/_data'));
        \Contao\System::setContainer($container);
        $this->getTempDir();

        $this->configManager = $this->getMockBuilder(CoreConfigurationManager::class)->disableOriginalConstructor()->getMock();
        $this->versionComparator = $this->createMock(VersionComparator::class);

        $this->sut = new Migration(
            $this->configManager,
            $this->versionComparator
        );
    }

    public function testShouldRunShouldRun(): void
    {
        $coreConfig = new CoreConfig();
        $coreConfig->setSgVersion('1.0.0');

        $this->configManager->method('load')
            ->willReturn($coreConfig)
        ;

        $this->versionComparator->method('compare')
            ->willReturn(VersionComparator::CURRENT_VERSION_LOWER)
        ;

        /** @var MigrationResult */
        $result = $this->sut->shouldRun();

        $this->assertSame(MigrationResult::STATUS_SHOULD_RUN, $result->getStatus());
    }

    public function testShouldRunSkippedBecauseCurrentVersionEquals(): void
    {
        $coreConfig = new CoreConfig();
        $coreConfig->setSgVersion('1.0.2');

        $this->configManager->method('load')
            ->willReturn($coreConfig)
        ;

        $this->versionComparator->method('compare')
            ->willReturn(VersionComparator::VERSIONS_EQUALS)
        ;

        /** @var MigrationResult */
        $result = $this->sut->shouldRun();

        $this->assertSame(MigrationResult::STATUS_SKIPPED, $result->getStatus());
    }

    public function testShouldRunSkippedBecauseCurrentVersionHigher(): void
    {
        $coreConfig = new CoreConfig();
        $coreConfig->setSgVersion('1.0.2');

        $this->configManager->method('load')
            ->willReturn($coreConfig)
        ;

        $this->versionComparator->method('compare')
            ->willReturn(VersionComparator::CURRENT_VERSION_HIGHER)
        ;

        /** @var MigrationResult */
        $result = $this->sut->shouldRun();

        $this->assertSame(MigrationResult::STATUS_SKIPPED, $result->getStatus());
    }

    public function testDoSkippedBecauseCurrentVersionEquals(): void
    {
        $coreConfig = new CoreConfig();
        $coreConfig->setSgVersion('1.0.2');

        $this->configManager->method('load')
            ->willReturn($coreConfig)
        ;

        $this->versionComparator->method('compare')
            ->willReturn(VersionComparator::VERSIONS_EQUALS)
        ;

        /** @var MigrationResult */
        $result = $this->sut->do();

        $this->assertSame(MigrationResult::STATUS_SKIPPED, $result->getStatus());
    }

    public function testDoSkippedBecauseCurrentVersionHigher(): void
    {
        $coreConfig = new CoreConfig();
        $coreConfig->setSgVersion('1.0.2');

        $this->configManager->method('load')
            ->willReturn($coreConfig)
        ;

        $this->versionComparator->method('compare')
            ->willReturn(VersionComparator::CURRENT_VERSION_HIGHER)
        ;

        /** @var MigrationResult */
        $result = $this->sut->do();

        $this->assertSame(MigrationResult::STATUS_SKIPPED, $result->getStatus());
    }

    public function testDoDone(): void
    {
        $coreConfig = new CoreConfig();
        $coreConfig->setSgVersion('1.0.0');

        $this->configManager->method('load')
            ->willReturn($coreConfig)
        ;

        $this->versionComparator->method('compare')
            ->willReturn(VersionComparator::CURRENT_VERSION_LOWER)
        ;

        /** @var MigrationResult */
        $result = $this->sut->do();

        $this->assertSame(MigrationResult::STATUS_SUCCESS, $result->getStatus());
    }
}
