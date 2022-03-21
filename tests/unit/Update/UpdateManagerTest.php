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

namespace Update;

use Contao\TestCase\ContaoTestCase;
use WEM\SmartgearBundle\Backup\BackupManager;
use WEM\SmartgearBundle\Backup\Model\Results\CreateResult as BackupResult;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\Migration\Result as MigrationResult;
use WEM\SmartgearBundle\Classes\Version\Comparator as VersionComparator;
use WEM\SmartgearBundle\Update\Results\SingleMigrationResult;
use WEM\SmartgearBundle\Update\Results\UpdateResult;
use WEM\SmartgearBundle\Update\UpdateManager;

require_once realpath(__DIR__.'/../../../tests/_data/migrations/1-0-1/Migration.php');

class UpdateManagerTest extends ContaoTestCase
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    /** @var UpdateManager */
    protected $sut;
    /** @var CoreConfigurationManager */
    protected $configManager;
    /** @var BackupManager */
    protected $backupManager;
    /** @var array */
    protected $migrations = [];

    protected function setUp(): void
    {
        if (!\defined('TL_ROOT')) {
            \define('TL_ROOT', sys_get_temp_dir());
        }

        $container = $this->getContainerWithContaoConfiguration();
        $container->setParameter('kernel.project_dir', realpath(__DIR__.'/../../../tests/_data'));
        \Contao\System::setContainer($container);
        $this->getTempDir();

        $this->configManager = $this->createMock(CoreConfigurationManager::class);
        $this->backupManager = $this->createMock(BackupManager::class);
    }

    /**
     * @dataProvider dpForTestUpdate
     *
     * @return [type] [description]
     */
    public function testUpdate(callable $migrationsGen, callable $expectedUpdateResultGen): void
    {
        $migrations = $migrationsGen();
        $this->sut = new UpdateManager(
            $this->configManager,
            $this->backupManager,
            $migrations
        );
        $expectedUpdateResult = $expectedUpdateResultGen();
        $updateResult = $this->sut->update();
        $this->assertSame($expectedUpdateResult->getStatus(), $updateResult->getStatus());

        for ($i = 0; $i < \count($migrations); ++$i) {
            $this->assertSame(
                ($expectedUpdateResult->getResults()[$i])->getResult()->getStatus(),
                ($updateResult->getResults()[$i])->getResult()->getStatus()
            );
        }
    }

    public function dpForTestUpdate(): array
    {
        return [
            'one migration : OK' => [
                'migrationsGen' => function (): array {
                    $m1 = $this->createMock(\Migration::class);
                    $m1->method('shouldRun')
                        ->willReturn(
                        (new MigrationResult())
                        ->setStatus(MigrationResult::STATUS_SHOULD_RUN)
                    )
                    ;

                    $m1->method('do')
                        ->willReturn(
                        (new MigrationResult())
                        ->setStatus(MigrationResult::STATUS_SUCCESS)
                    )
                    ;

                    return [$m1];
                },
                'expectedUpdateResultGen' => function () {
                    return (new UpdateResult())
                    ->setStatusSuccess()
                    ->addResult(
                        (new SingleMigrationResult())
                        ->setMigration(
                            // (new \Migration($this->createMock(CoreConfigurationManager::class), $this->createMock(VersionComparator::class)))
                            $this->createMock(\Migration::class)
                        )
                        ->setResult(
                            (new MigrationResult())
                            ->setStatus(MigrationResult::STATUS_SUCCESS)
                        )
                    )
                    ->setBackupResult(
                        (new BackupResult())
                    )
                    ;
                },
            ],
            'two migrations : first OK, second KO' => [
                'migrationsGen' => function (): array {
                    $m1 = $this->createMock(\Migration::class);
                    $m1->method('shouldRun')
                        ->willReturn(
                        (new MigrationResult())
                        ->setStatus(MigrationResult::STATUS_SHOULD_RUN)
                    )
                    ;

                    $m1->method('do')
                        ->willReturn(
                        (new MigrationResult())
                        ->setStatus(MigrationResult::STATUS_SUCCESS)
                    )
                    ;
                    $m2 = $this->createMock(\Migration::class);
                    $m2->method('shouldRun')
                        ->willReturn(
                        (new MigrationResult())
                        ->setStatus(MigrationResult::STATUS_SHOULD_RUN)
                    )
                    ;

                    $m2->method('do')
                        ->willReturn(
                        (new MigrationResult())
                        ->setStatus(MigrationResult::STATUS_FAIL)
                    )
                    ;

                    return [$m1, $m2];
                },
                'expectedUpdateResultGen' => function () {
                    return (new UpdateResult())
                    ->setStatusFail()
                    ->addResult(
                        (new SingleMigrationResult())
                        ->setMigration(
                            $this->createMock(\Migration::class)
                        )
                        ->setResult(
                            (new MigrationResult())
                            ->setStatus(MigrationResult::STATUS_SUCCESS)
                        )
                    )
                    ->addResult(
                        (new SingleMigrationResult())
                        ->setMigration(
                            $this->createMock(\Migration::class)
                        )
                        ->setResult(
                            (new MigrationResult())
                            ->setStatus(MigrationResult::STATUS_FAIL)
                        )
                    )
                    ->setBackupResult(
                        (new BackupResult())
                    )
                    ;
                },
            ],
            'two migrations : first KO, second not runned' => [
                'migrationsGen' => function (): array {
                    $m1 = $this->createMock(\Migration::class);
                    $m1->method('shouldRun')
                        ->willReturn(
                        (new MigrationResult())
                        ->setStatus(MigrationResult::STATUS_FAIL)
                    )
                    ;
                    $m2 = $this->createMock(\Migration::class);

                    return [$m1, $m2];
                },
                'expectedUpdateResultGen' => function () {
                    return (new UpdateResult())
                    ->setStatusFail()
                    ->addResult(
                        (new SingleMigrationResult())
                        ->setMigration(
                            $this->createMock(\Migration::class)
                        )
                        ->setResult(
                            (new MigrationResult())
                            ->setStatus(MigrationResult::STATUS_FAIL)
                        )
                    )
                    ->addResult(
                        (new SingleMigrationResult())
                        ->setMigration(
                            $this->createMock(\Migration::class)
                        )
                        ->setResult(
                            (new MigrationResult())
                            ->setStatus(MigrationResult::STATUS_NOT_EXCUTED_YET)
                        )
                    )
                    ->setBackupResult(
                        (new BackupResult())
                    )
                    ;
                },
            ],
            'three migrations : first OK, second KO, third untouched' => [
                'migrationsGen' => function (): array {
                    $m1 = $this->createMock(\Migration::class);
                    $m1->method('shouldRun')
                        ->willReturn(
                        (new MigrationResult())
                        ->setStatus(MigrationResult::STATUS_SHOULD_RUN)
                    )
                    ;

                    $m1->method('do')
                        ->willReturn(
                        (new MigrationResult())
                        ->setStatus(MigrationResult::STATUS_SUCCESS)
                    )
                    ;
                    $m2 = $this->createMock(\Migration::class);
                    $m2->method('shouldRun')
                        ->willReturn(
                        (new MigrationResult())
                        ->setStatus(MigrationResult::STATUS_SHOULD_RUN)
                    )
                    ;

                    $m2->method('do')
                        ->willReturn(
                        (new MigrationResult())
                        ->setStatus(MigrationResult::STATUS_FAIL)
                    )
                    ;
                    $m3 = $this->createMock(\Migration::class);

                    return [$m1, $m2, $m3];
                },
                'expectedUpdateResultGen' => function () {
                    return (new UpdateResult())
                    ->setStatusFail()
                    ->addResult(
                        (new SingleMigrationResult())
                        ->setMigration(
                            $this->createMock(\Migration::class)
                        )
                        ->setResult(
                            (new MigrationResult())
                            ->setStatus(MigrationResult::STATUS_SUCCESS)
                        )
                    )
                    ->addResult(
                        (new SingleMigrationResult())
                        ->setMigration(
                            $this->createMock(\Migration::class)
                        )
                        ->setResult(
                            (new MigrationResult())
                            ->setStatus(MigrationResult::STATUS_FAIL)
                        )
                    )
                    ->addResult(
                        (new SingleMigrationResult())
                        ->setMigration(
                            $this->createMock(\Migration::class)
                        )
                        ->setResult(
                            (new MigrationResult())
                            ->setStatus(MigrationResult::STATUS_NOT_EXCUTED_YET)
                        )
                    )
                    ->setBackupResult(
                        (new BackupResult())
                    )
                    ;
                },
            ],
        ];
    }
}
