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

use Doctrine\DBAL\Connection;

require_once realpath(__DIR__.'/../Util/SmartgearTestCase.php');
use Doctrine\DBAL\Platforms\MySQLPlatform;
use WEM\SmartgearBundle\Classes\RenderStack;

class RenderStackTest extends \Util\SmartgearTestCase
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /** @var RenderStack */
    protected $sut;

    protected function setUp(): void
    {
        if (!\defined('TL_ROOT')) {
            \define('TL_ROOT', sys_get_temp_dir());
        }

        $GLOBALS['TL_CONFIG']['uploadPath'] = sys_get_temp_dir();

        $container = $this->getContainerWithContaoConfiguration();
        $container->setParameter('kernel.project_dir', codecept_data_dir());
        $container->setParameter('contao.resources_paths', codecept_data_dir());
        $container->set('database_connection', $this->createConfiguredMock(Connection::class, [
            'getDatabasePlatform' => $this->createMock(MySQLPlatform::class),
        ]));

        $container->set('contao.cache.entity_tags', $this->createConfiguredMock(\Contao\CoreBundle\Cache\EntityCacheTags::class, []));
        $container->set('security.firewall.map', $this->createConfiguredMock(\Symfony\Component\Security\Http\FirewallMap::class, []));
        $container->set('security.token_storage', $this->createConfiguredMock(\Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage::class, []));
        $container->set('session', $this->createConfiguredMock(\Symfony\Component\HttpFoundation\Session\Session::class, []));
        $container->set('security.authentication.trust_resolver', $this->createConfiguredMock(\Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolver::class, []));
        $container->set('security.access.simple_role_voter', $this->createConfiguredMock(\Symfony\Component\Security\Core\Authorization\Voter\Voter::class, []));
        $container->set('router', $this->createConfiguredMock(\Symfony\Component\Routing\Router::class, []));

        \Contao\System::setContainer($container);
        $this->getFreshSut();

        global $objPage;
        $objPage = new \Contao\PageModel();
    }

    /**
     * [testAdd description].
     *
     * @dataProvider dpForTestAdd
     */
    public function testAdd(array $items, int $expectedItemsNumber, array $expectedItems): void
    {
        $this->getFreshSut();

        foreach ($items as $item) {
            $this->sut->add($item['model'], $item['buffer'], $item['contentOrModule']);
        }

        $getItems = $this->sut->getItems();
        $this->assertSame(\count($getItems), $expectedItemsNumber);
        $this->assertSame($getItems, $expectedItems['items']);
    }

    /**
     * [testAdd description].
     *
     * @dataProvider dpForTestGetBreadcrumbIndexes
     */
    public function testGetBreadcrumbIndexes(array $items, array $expectedIndexes, ?string $column, array $indexes): void
    {
        $this->getFreshSut();

        foreach ($items as $item) {
            $this->sut->add($item['model'], $item['buffer'], $item['contentOrModule']);
        }

        $this->assertSame($this->sut->getBreadcrumbIndexes(), $expectedIndexes);
        $this->assertSame($this->sut->getBreadcrumbIndexes($column), $indexes);
    }

    public function dpForTestGetBreadcrumbIndexes(): array
    {
        $this->setUp();

        $moduleModel = new \Contao\ModuleModel();
        $moduleModel->id = 1;

        $moduleBreadcrumbModel = new \Contao\ModuleModel();
        $moduleBreadcrumbModel->type = 'breadcrumb';
        $moduleBreadcrumbModel->id = 2;

        $contentModel = new \Contao\ContentModel();
        $contentModel->id = 1;

        $moduleHtml = new \Contao\ModuleHtml($moduleModel);
        $moduleHtml->generate();

        $contentHtml = new \Contao\ContentHtml($contentModel);
        $contentHtml->generate();

        $moduleBreadcrumb = new \Contao\ModuleBreadcrumb($moduleBreadcrumbModel);
        $moduleBreadcrumb->type = 'breadcrumb';
        $moduleBreadcrumb->generate();

        $dp1 = [
            'items' => [
                [
                    'model' => $moduleModel,
                    'buffer' => 'foo',
                    'contentOrModule' => $moduleHtml,
                ],
                [
                    'model' => $contentModel,
                    'buffer' => 'bar',
                    'contentOrModule' => $contentHtml,
                ],
            ],
            'expectedIndexes' => [],
            'column' => 'all',
            'indexes' => [],
        ];
        $dp2 = [
            'items' => [
                [
                    'model' => $moduleBreadcrumbModel,
                    'buffer' => 'breadcrumb_foobar',
                    'contentOrModule' => $moduleBreadcrumb,
                ],
                [
                    'model' => $moduleModel,
                    'buffer' => 'foo',
                    'contentOrModule' => $moduleHtml,
                ],
                [
                    'model' => $contentModel,
                    'buffer' => 'bar',
                    'contentOrModule' => $contentHtml,
                ],
            ],
            'expectedIndexes' => [0 => 0],
            'column' => 'all',
            'indexes' => [0],
        ];

        $moduleModel = new \Contao\ModuleModel();
        $moduleModel->id = 1;

        $moduleBreadcrumbModel = new \Contao\ModuleModel();
        $moduleBreadcrumbModel->type = 'breadcrumb';
        $moduleBreadcrumbModel->id = 2;

        $contentModel = new \Contao\ContentModel();
        $contentModel->id = 1;

        $moduleHtml = new \Contao\ModuleHtml($moduleModel);
        $moduleHtml->generate();

        $contentHtml = new \Contao\ContentHtml($contentModel, 'left');
        $contentHtml->generate();

        $moduleBreadcrumb = new \Contao\ModuleBreadcrumb($moduleBreadcrumbModel, 'left');
        $moduleBreadcrumb->type = 'breadcrumb';
        $moduleBreadcrumb->generate();

        $dp3 = [
            'items' => [
                [
                    'model' => $contentModel,
                    'buffer' => 'bar',
                    'contentOrModule' => $contentHtml,
                ],
                [
                    'model' => $moduleModel,
                    'buffer' => 'foo',
                    'contentOrModule' => $moduleHtml,
                ],
                [
                    'model' => $moduleBreadcrumbModel,
                    'buffer' => 'breadcrumb_foobar',
                    'contentOrModule' => $moduleBreadcrumb,
                ],
            ],
            'expectedIndexes' => [0 => 2],
            'column' => 'left',
            'indexes' => [1],
        ];

        return [
            'scenario_1_no_breadcrumb' => $dp1,
            'scenario_2_breadcrumb_in_first_place' => $dp2,
            'scenario_3_breadcrumb_in_second_place_left_column' => $dp3,
        ];
    }

    public function dpForTestAdd(): array
    {
        $this->setUp();

        $moduleModel = new \Contao\ModuleModel();
        $moduleModel->id = 1;

        $moduleBreadcrumbModel = new \Contao\ModuleModel();
        $moduleBreadcrumbModel->type = 'breadcrumb';
        $moduleBreadcrumbModel->id = 2;

        $contentModel = new \Contao\ContentModel();
        $contentModel->id = 1;

        $moduleHtml = new \Contao\ModuleHtml($moduleModel);
        $moduleHtml->generate();

        $contentHtml = new \Contao\ContentHtml($contentModel);
        $contentHtml->generate();

        $moduleBreadcrumb = new \Contao\ModuleHtml($moduleBreadcrumbModel);
        $moduleBreadcrumb->type = 'breadcrumb';
        $moduleBreadcrumb->generate();

        $dp1 = [
            'items' => [
                [
                    'model' => $moduleModel,
                    'buffer' => 'foo',
                    'contentOrModule' => $moduleHtml,
                ],
                [
                    'model' => $contentModel,
                    'buffer' => 'bar',
                    'contentOrModule' => $contentHtml,
                ],
            ],
            'expectedItemsNumber' => 2,
            'expectedItemsGenerator' => [
                'current_index' => [
                    'all' => 2,
                    //other columns will go here
                ],
                'items' => [
                    [
                        'index' => 0,
                        'index_in_column' => 0,
                        'model' => $moduleModel,
                        'buffer' => 'foo',
                        'contentOrModule' => $moduleHtml,
                        'column' => 'main',
                    ],
                    [
                        'index' => 1,
                        'index_in_column' => 1,
                        'model' => $contentModel,
                        'buffer' => 'bar',
                        'contentOrModule' => $contentHtml,
                        'column' => 'main',
                    ],
                ],
                'breadcrumb_indexes' => [
                    'all' => [],
                    //other columns will go here
                ],
            ],
        ];
        $dp2 = [
            'items' => [
                [
                    'model' => $moduleBreadcrumbModel,
                    'buffer' => 'breadcrumb_foobar',
                    'contentOrModule' => $moduleBreadcrumb,
                ],
                [
                    'model' => $moduleModel,
                    'buffer' => 'foo',
                    'contentOrModule' => $moduleHtml,
                ],
                [
                    'model' => $contentModel,
                    'buffer' => 'bar',
                    'contentOrModule' => $contentHtml,
                ],
            ],
            'expectedItemsNumber' => 3,
            'expectedItemsGenerator' => [
                'current_index' => [
                    'all' => 3,
                    //other columns will go here
                ],
                'items' => [
                    [
                        'index' => 0,
                        'index_in_column' => 0,
                        'model' => $moduleBreadcrumbModel,
                        'buffer' => 'breadcrumb_foobar',
                        'contentOrModule' => $moduleBreadcrumb,
                        'column' => 'main',
                    ],
                    [
                        'index' => 1,
                        'index_in_column' => 1,
                        'model' => $moduleModel,
                        'buffer' => 'foo',
                        'contentOrModule' => $moduleHtml,
                        'column' => 'main',
                    ],
                    [
                        'index' => 2,
                        'index_in_column' => 2,
                        'model' => $contentModel,
                        'buffer' => 'bar',
                        'contentOrModule' => $contentHtml,
                        'column' => 'main',
                    ],
                ],
                'breadcrumb_indexes' => [
                    'all' => [
                        0,
                    ],
                    //other columns will go here
                ],
            ],
        ];

        $moduleModel = new \Contao\ModuleModel();
        $moduleModel->id = 1;

        $moduleBreadcrumbModel = new \Contao\ModuleModel();
        $moduleBreadcrumbModel->type = 'breadcrumb';
        $moduleBreadcrumbModel->id = 2;

        $contentModel = new \Contao\ContentModel();
        $contentModel->id = 1;

        $moduleHtml = new \Contao\ModuleHtml($moduleModel);
        $moduleHtml->generate();

        $contentHtml = new \Contao\ContentHtml($contentModel, 'left');
        $contentHtml->generate();

        $moduleBreadcrumb = new \Contao\ModuleBreadcrumb($moduleBreadcrumbModel, 'left');
        $moduleBreadcrumb->type = 'breadcrumb';
        $moduleBreadcrumb->generate();

        $dp3 = [
            'items' => [
                [
                    'model' => $contentModel,
                    'buffer' => 'bar',
                    'contentOrModule' => $contentHtml,
                ],
                [
                    'model' => $moduleModel,
                    'buffer' => 'foo',
                    'contentOrModule' => $moduleHtml,
                ],
                [
                    'model' => $moduleBreadcrumbModel,
                    'buffer' => 'breadcrumb_foobar',
                    'contentOrModule' => $moduleBreadcrumb,
                ],
            ],
            'expectedItemsNumber' => 3,
            'expectedItemsGenerator' => [
                'current_index' => [
                    'all' => 3,
                    //other columns will go here
                ],
                'items' => [
                    [
                        'index' => 0,
                        'index_in_column' => 0,
                        'model' => $contentModel,
                        'buffer' => 'bar',
                        'contentOrModule' => $contentHtml,
                        'column' => 'left',
                    ],
                    [
                        'index' => 1,
                        'index_in_column' => 0,
                        'model' => $moduleModel,
                        'buffer' => 'foo',
                        'contentOrModule' => $moduleHtml,
                        'column' => 'main',
                    ],
                    [
                        'index' => 2,
                        'index_in_column' => 1,
                        'model' => $moduleBreadcrumbModel,
                        'buffer' => 'breadcrumb_foobar',
                        'contentOrModule' => $moduleBreadcrumb,
                        'column' => 'left',
                    ],
                ],
                'breadcrumb_indexes' => [
                    'all' => [
                        2,
                    ],
                    'left' => [
                        2,
                    ],
                    //other columns will go here
                ],
            ],
        ];

        return [
            'scenario_1_no_breadcrumb' => $dp1,
            'scenario_2_breadcrumb_in_first_place' => $dp2,
            'scenario_3_breadcrumb_in_second_place_left_column' => $dp3,
        ];
    }

    /**
     * Easy way to reset our singleton class (which is a problem only for tests, not production code).
     */
    protected function getFreshSut(): void
    {
        $this->sut = RenderStack::getInstance(); // no idea what's inside
        $reflection = new \ReflectionClass($this->sut);
        $instance = $reflection->getProperty('instance');
        $instance->setAccessible(true); // now we can modify that :)
        $instance->setValue(null, null); // instance is gone
        $instance->setAccessible(false); // clean up

        // now recreate a fresh object
        $this->sut = RenderStack::getInstance();
    }
}
