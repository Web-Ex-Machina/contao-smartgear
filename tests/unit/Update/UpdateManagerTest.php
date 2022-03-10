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
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Update\UpdateManager;

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

        $this->sut = new UpdateManager(
            $this->configManager,
            $this->backupManager,
            []
        );
    }

    public function testUpdate(): void
    {
        $this->assertTrue(false);
    }
}
