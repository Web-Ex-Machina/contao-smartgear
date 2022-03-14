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

use WEM\SmartgearBundle\Classes\Version\Comparator;
use WEM\SmartgearBundle\Classes\Version\Version;

class ComparatorTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /** @var CoreConfig */
    protected $sut;

    protected function setUp(): void
    {
        $this->sut = new Comparator();
    }

    /**
     * [testCompare description].
     *
     * @param callable $currentVersionGen          [description]
     * @param callable $versionToCompareAgainstGen [description]
     * @param int      $expectedStatus             [description]
     * @dataProvider dpForTestCompare
     */
    public function testCompare(callable $currentVersionGen, callable $versionToCompareAgainstGen, int $expectedStatus): void
    {
        $v1 = $currentVersionGen();
        $v2 = $versionToCompareAgainstGen();

        $this->assertSame($expectedStatus, $this->sut->compare($v1, $v2));
    }

    public function dpForTestCompare(): array
    {
        return [
            'higher' => [
                'currentVersionGen' => function () {
                    return (new Version())->fromString('1.0.1');
                },
                'versionToCompareAgainstGen' => function () {
                    return (new Version())->fromString('1.0.0');
                },
                'expectedStatus' => Comparator::CURRENT_VERSION_HIGHER,
            ],
            'equals' => [
                'currentVersionGen' => function () {
                    return (new Version())->fromString('1.2.3');
                },
                'versionToCompareAgainstGen' => function () {
                    return (new Version())->fromString('1.2.3');
                },
                'expectedStatus' => Comparator::VERSIONS_EQUALS,
            ],
            'lower' => [
                'currentVersionGen' => function () {
                    return (new Version())->fromString('1.2.3');
                },
                'versionToCompareAgainstGen' => function () {
                    return (new Version())->fromString('1.3.0');
                },
                'expectedStatus' => Comparator::CURRENT_VERSION_LOWER,
            ],
        ];
    }
}
