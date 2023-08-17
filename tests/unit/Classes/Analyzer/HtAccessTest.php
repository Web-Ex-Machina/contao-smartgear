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

use WEM\SmartgearBundle\Classes\Analyzer\Htaccess;

class HtAccessTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /** @var Htaccess */
    protected $sut;

    protected $originalFilePath;
    protected $saveFilePath;

    protected function setUp(): void
    {
        $this->originalFilePath = codecept_data_dir().'public/.htaccess';
        $this->saveFilePath = codecept_data_dir().'public/.htaccess_save';
        file_put_contents($this->saveFilePath, file_get_contents($this->originalFilePath));
        $this->sut = new Htaccess($this->saveFilePath);
    }

    protected function tearDown(): void
    {
        unlink($this->saveFilePath);
        // unlink all backup files too !!
        $arrFiles = glob($this->saveFilePath.'_*');
        if(!$arrFiles){
            return;
        }
        foreach($arrFiles as $filepath){
           unlink($filepath);
        }
    }

    public function testHasRedirectToWwwAndHttps(): void
    {
        $this->assertFalse($this->sut->hasRedirectToWwwAndHttps());
        $this->sut->enableRedirectToWwwAndHttps();
        $this->assertTrue($this->sut->hasRedirectToWwwAndHttps());
        $this->sut->disableRedirectToWwwAndHttps();
        $this->assertFalse($this->sut->hasRedirectToWwwAndHttps());
    }

    public function testEnableRedirectToWwwAndHttps(): void
    {
        $this->assertTrue($this->sut->enableRedirectToWwwAndHttps());
    }

    public function testDisableRedirectToWwwAndHttps(): void
    {
        $this->assertTrue($this->sut->disableRedirectToWwwAndHttps());
    }

    public function testEnableFramwayAssetsManagementRules(): void
    {
        $this->assertTrue($this->sut->enableFramwayAssetsManagementRules());
    }

    public function testDisableFramwayAssetsManagementRules(): void
    {
        $this->assertTrue($this->sut->disableFramwayAssetsManagementRules());
    }
}
