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

namespace Classes;

require_once realpath(__DIR__.'/../Util/SmartgearTestCase.php');

use WEM\SmartgearBundle\Classes\Util;

class UtilTest extends \Util\SmartgearTestCase
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function setUp(): void
    {
    }

    public function testFormatPhpMemoryLimitToBytes(): void
    {
        $this->assertSame(Util::formatPhpMemoryLimitToBytes(-1), -1);
        $this->assertSame(Util::formatPhpMemoryLimitToBytes('-1'), -1);
        $this->assertSame(Util::formatPhpMemoryLimitToBytes('1024'), 1024);
        $this->assertSame(Util::formatPhpMemoryLimitToBytes(256000), 256000);
        $this->assertSame(Util::formatPhpMemoryLimitToBytes('1G'), 1 * 1024 * 1024 * 1024);
        $this->assertSame(Util::formatPhpMemoryLimitToBytes('2g'), 2 * 1024 * 1024 * 1024);
        $this->assertSame(Util::formatPhpMemoryLimitToBytes('768M'), 768 * 1024 * 1024);
        $this->assertSame(Util::formatPhpMemoryLimitToBytes('3k'), 3 * 1024);
    }
}
