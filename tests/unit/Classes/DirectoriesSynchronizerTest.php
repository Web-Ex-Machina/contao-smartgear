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

namespace Classes;

use WEM\SmartgearBundle\Classes\DirectoriesSynchronizer;

require_once realpath(__DIR__.'/../Util/SmartgearTestCase.php');
class DirectoriesSynchronizerTest extends \Util\SmartgearTestCase
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /** @var DirectoriesSynchronizer */
    protected $sut;

    protected $fromPath = '';
    protected $toPath = '';
    protected $currentToPath = '';

    protected function setUp(): void
    {
        if (!\defined('TL_ROOT')) {
            \define('TL_ROOT', codecept_data_dir());
        }
        $GLOBALS['TL_CONFIG']['uploadPath'] = sys_get_temp_dir();

        $container = $this->getContainerWithContaoConfiguration();
        $container->setParameter('kernel.project_dir', codecept_data_dir());
        \Contao\System::setContainer($container);
        $this->getTempDir();

        $this->fromPath = 'synchronizer/from';
        $this->toPath = codecept_data_dir().'synchronizer/to';
        $this->currentToPath = 'synchronizer/to_current';

        $this->sut = new DirectoriesSynchronizer($this->getTranslator(), $this->fromPath, $this->currentToPath, $container->getParameter('kernel.project_dir'), true);
        $this->resetFiles();
    }

    // tests
    public function testSynchronize(): void
    {
        $this->sut->synchronize();
        $filesToAdd = $this->sut->getFilesToAdd();
        $filesToDelete = $this->sut->getFilesToDelete();
        $filesToUpdate = $this->sut->getFilesToUpdate();
        $this->assertArrayHasKey('/B.txt', $filesToAdd);
        $this->assertArrayHasKey('/F/G.txt', $filesToAdd);
        $this->assertArrayHasKey('/A.txt', $filesToUpdate);
        $this->assertArrayHasKey('/C.txt', $filesToDelete);
        $this->assertArrayHasKey('/D/E.txt', $filesToDelete);
        $this->resetFiles();
    }

    private function resetFiles(): void
    {
        $container = \Contao\System::getContainer();
        // delete anything inside $this->currentToPath
        // copy everything from $this->toPath to $this->currentToPath
        if (is_dir($container->getParameter('kernel.project_dir').'/'.$this->currentToPath)) {
            $folder = new \Contao\Folder($this->currentToPath);
            $folder->delete();
        }
        mkdir($container->getParameter('kernel.project_dir').'/'.$this->currentToPath);
        copy($this->toPath.'/A.txt', $container->getParameter('kernel.project_dir').'/'.$this->currentToPath.'/A.txt');
        copy($this->toPath.'/C.txt', $container->getParameter('kernel.project_dir').'/'.$this->currentToPath.'/C.txt');
        mkdir(\dirname($container->getParameter('kernel.project_dir').'/'.$this->currentToPath.'/D/E.txt'), 0777, true);
        copy($this->toPath.'/D/E.txt', $container->getParameter('kernel.project_dir').'/'.$this->currentToPath.'/D/E.txt');
    }
}
