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

namespace WEM\SmartgearBundle\Backend;

use Contao\BackendModule;
use Contao\BackendTemplate;
use Contao\Config;
use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\Date;
use Contao\Environment;
use Contao\FilesModel;
use Contao\FrontendTemplate;
use Contao\Input;
use Contao\Pagination;
use Contao\System;
use Exception;
use WEM\SmartgearBundle\Config\DataManager as DataManagerConfig;
use WEM\SmartgearBundle\Config\DataManagerDataSet;
use WEM\SmartgearBundle\Security\SmartgearPermissions;

class DataManager extends BackendModule
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'be_wem_sg_data_manager';
    protected $arrConfig = [];
    protected $arrFilters = [];
    protected $id = 'wem_sg_data_manager';

    public function __construct($dc = null)
    {
        parent::__construct($dc);
        $this->security = System::getContainer()->get('security.helper');
        $this->configurationManager = System::getContainer()->get('smartgear.config.manager.core');
        $this->dataManagerConfigurationManager = System::getContainer()->get('smartgear.config.manager.data_manager');
        $this->datasetFinder = System::getContainer()->get('smartgear.classes.data_manager.dataset_finder');
        $this->dataManagerService = System::getContainer()->get('smartgear.service.data_manager');
    }

    public function generate(): string
    {
        $GLOBALS['TL_CSS'][] = 'https://use.fontawesome.com/releases/v5.3.1/css/all.css';
        $GLOBALS['TL_CSS'][] = 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css';
        $GLOBALS['TL_JAVASCRIPT'][] = 'https://code.jquery.com/jquery-3.3.1.min.js';
        $GLOBALS['TL_JAVASCRIPT'][] = 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js';

        return parent::generate();
    }

    public function compile(): void
    {
        try {
            if (!$this->security->isGranted(SmartgearPermissions::CORE_EXPERT)) {
                throw new Exception('Not allowed to do that');
            }

            // Catch AJAX request
            if (Input::post('TL_AJAX') && $this->id === Input::post('module')) {
                $this->handleAjaxRequest(Input::post('action'));
            }

            $limit = null;
            $offset = (int) $this->skipFirst;

            // Maximum number of items
            if ($this->numberOfItems > 0) {
                $limit = $this->numberOfItems;
            }

            $this->Template->rt = System::getContainer()->get('contao.csrf.token_manager')->getDefaultTokenValue();
            $this->Template->items = [];
            $this->Template->empty = $GLOBALS['TL_LANG']['WEMSG']['DATAMANAGER']['LIST']['empty'];
            $this->Template->filters = $this->buildFilters();

            // Get the total number of items
            $intTotal = $this->countItems();

            if ($intTotal < 1) {
                return;
            }

            $total = $intTotal - $offset;

            // Split the results
            if ($this->perPage > 0 && (!isset($limit) || $this->numberOfItems > $this->perPage)) {
                // Adjust the overall limit
                if (isset($limit)) {
                    $total = min($limit, $total);
                }

                // Get the current page
                $id = 'page_n'.$this->id;
                $page = Input::get($id) ?? 1;

                // Do not index or cache the page if the page number is outside the range
                if ($page < 1 || $page > max(ceil($total / $this->perPage), 1)) {
                    throw new PageNotFoundException('Page not found: '.Environment::get('uri'));
                }

                // Set limit and offset
                $limit = $this->perPage;
                $offset += (max($page, 1) - 1) * $this->perPage;
                $skip = (int) $this->skipFirst;

                // Overall limit
                if ($offset + $limit > $total + $skip) {
                    $limit = $total + $skip - $offset;
                }

                // Add the pagination menu
                $objPagination = new Pagination($total, $this->perPage, Config::get('maxPaginationLinks'), $id);
                $this->Template->pagination = $objPagination->generate("\n  ");
            }

            $objItems = $this->fetchItems(($limit ?: 0), $offset);

            // Add the items
            if (null !== $objItems) {
                $this->Template->items = $this->parseItems($objItems);
            }
        } catch (Exception $e) {
            $this->Template->error = true;
            $this->Template->message = $e->getMessage();
        }
    }

    protected function buildFilters()
    {
        $arrFilters = [];

        // status
        $arrFilters['select']['status'] = [
            'name' => 'status',
            'placeholder' => $GLOBALS['TL_LANG']['WEMSG']['FILTERS']['LBL']['status'],
            'options' => [
                [
                    'value' => '',
                    'label' => $GLOBALS['TL_LANG']['WEMSG']['FILTERS']['LBL']['status'],
                ],
                [
                    'value' => '',
                    'label' => '-------',
                    'disabled' => true,
                ],
                [
                    'value' => 1,
                    'label' => $GLOBALS['TL_LANG']['WEMSG']['FILTERS']['LBL']['statusInstalled'],
                    'selected' => 1 === (int) Input::get('status'),
                ],
                [
                    'value' => 0,
                    'label' => $GLOBALS['TL_LANG']['WEMSG']['FILTERS']['LBL']['statusNotInstalled'],
                    'selected' => null !== Input::get('status') && '' !== Input::get('status') && 0 === (int) Input::get('status'),
                ],
                [
                    'value' => -1,
                    'label' => $GLOBALS['TL_LANG']['WEMSG']['FILTERS']['LBL']['statusUnavailable'],
                    'selected' => -1 === (int) Input::get('status'),
                ],
            ],
        ];

        if (null !== Input::get('status') && '' !== (Input::get('status'))) {
            $this->arrConfig['status'] = (int) Input::get('status');
        }

        // modules & types
        $arrModules = System::getContainer()->get('smartgear.backend.smartgear')->getModules();
        $arrOptions = [
            [
                'value' => '',
                'label' => $GLOBALS['TL_LANG']['WEMSG']['FILTERS']['LBL']['module'],
            ],
            [
                'value' => '',
                'label' => '-------',
                'disabled' => true,
            ],
        ];
        foreach ($arrModules as $type => $items) {
            $lbl = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT'][$type];
            $arrOptions[$lbl] = [
                'options' => [],
            ];
            foreach ($items as $item) {
                $arrOptions[$lbl]['options'][] = [
                    'value' => $item,
                    'label' => $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT'][$item],
                    'selected' => $item === Input::get('module'),
                ];
            }
        }
        $arrFilters['select']['module'] = [
            'name' => 'module',
            'placeholder' => $GLOBALS['TL_LANG']['WEMSG']['FILTERS']['LBL']['module'],
            'options' => $arrOptions,
        ];

        if (null !== Input::get('module') && '' !== (Input::get('module'))) {
            $this->arrConfig['module'] = Input::get('module');
        }

        return $arrFilters;
    }

    protected function getListConfig()
    {
        return $this->arrConfig;
    }

    /**
     * Count the total matching items.
     *
     * @return int
     */
    protected function countItems()
    {
        $c = $this->getListConfig();

        return \count($this->retrieveItems($c));
    }

    /**
     * Fetch the matching items.
     *
     * @param int   $limit
     * @param int   $offset
     * @param array $options
     *
     * @return Collection|Company|null
     */
    protected function fetchItems($limit, $offset, $options = [])
    {
        $c = $this->getListConfig();

        return $this->retrieveItems($c, $limit, $offset, $options);
    }

    protected function retrieveItems(array $c, ?int $limit = 0, ?int $offset = 0, $options = []): array
    {
        return $this->dataManagerService->getDatasetList($c, $limit, $offset);
    }

    /**
     * Parse multiple items.
     *
     * @param string $strTemplate [Template]
     *
     * @return array
     */
    protected function parseItems(array $objItems, $strTemplate = 'be_wem_sg_data_manager_default')
    {
        try {
            $limit = \count($objItems);
            if ($limit < 1) {
                return [];
            }

            $count = 0;
            $arrItems = [];
            foreach ($objItems as $key => $objItem) {
                $arrItems[] = $this->parseItem($objItem, $key, $strTemplate, ((1 === ++$count) ? ' first' : '').(($count === $limit) ? ' last' : '').((0 === ($count % 2)) ? ' odd' : ' even'), $count);
            }

            return $arrItems;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Parse an item.
     *
     * @param string $strTemplate [Template]
     * @param string $strClass    [CSS Class]
     * @param int    $intCount    [Iterator]
     *
     * @return string
     */
    protected function parseItem(array $objItem, string $path, $strTemplate = 'be_wem_sg_data_manager_default', $strClass = '', $intCount = 0)
    {
        try {
            /* @var \PageModel $objPage */
            global $objPage;

            /** @var \FrontendTemplate|object $objTemplate */
            $objTemplate = new FrontendTemplate($strTemplate);
            $objTemplate->setData($objItem);
            $objTemplate->class = $strClass;
            $objTemplate->count = $intCount;
            $objTemplate->date_installation = 0 !== $objItem['date_installation']
                ? Date::parse(Config::get('datimFormat'), $objItem['date_installation'])
                : '-'
                ;

            $actions = [];
            if ($this->canBeInstalled($objItem)) {
                $actions[] = [
                    'name' => 'install',
                    'label' => $GLOBALS['TL_LANG']['WEMSG']['DATAMANAGER']['BTN']['install'],
                    'buttonClasses' => 'fa fa-download',
                    'ajax' => 'install',
                    'params' => sprintf('module=%s,path=%s', $this->id, $path),
                    'url' => Environment::get('request'),
                ];
            }
            if ($this->canBeRemoved($objItem)) {
                $actions[] = [
                    'name' => 'remove',
                    'label' => $GLOBALS['TL_LANG']['WEMSG']['DATAMANAGER']['BTN']['remove'],
                    'buttonClasses' => 'fa fa-trash',
                    'ajax' => 'remove',
                    'params' => sprintf('module=%s,path=%s', $this->id, $path),
                    'url' => Environment::get('request'),
                ];
            }
            $actions[] = [
                'name' => 'openDatasetShowModal',
                'label' => $GLOBALS['TL_LANG']['WEMSG']['DATAMANAGER']['BTN']['show'],
                'buttonClasses' => 'fa fa-eye',
                'ajax' => 'openDatasetShowModal',
                'params' => sprintf('module=%s,path=%s', $this->id, $path),
                'url' => Environment::get('request'),
            ];

            $objTemplate->actions = $actions;

            return $objTemplate->parse();
        } catch (Exception $e) {
            throw $e;
        }
    }

    protected function canBeInstalled(array $objItem): bool
    {
        return $objItem['status'] === $GLOBALS['TL_LANG']['WEMSG']['FILTERS']['LBL']['statusNotInstalled'];
    }

    protected function canBeRemoved(array $objItem): bool
    {
        return $objItem['status'] === $GLOBALS['TL_LANG']['WEMSG']['FILTERS']['LBL']['statusInstalled'];
    }

    protected function handleAjaxRequest($strAction): void
    {
        try {
            switch ($strAction) {
                case 'install':
                    if (!Input::post('path')) {
                        throw new Exception($GLOBALS['TL_LANG']['WEMSG']['DATAMANAGER']['AJAX']['noPathProvided']);
                    }

                    $this->dataManagerService->installDataset(Input::post('path'));

                    $arrResponse = [
                        'status' => 'success',
                        'msg' => $GLOBALS['TL_LANG']['WEMSG']['DATAMANAGER']['AJAX']['installOk'],
                        'callbacks' => [
                            ['method' => 'reload', 'args' => []],
                        ],
                    ];
                break;
                case 'remove':
                    if (!Input::post('path')) {
                        throw new Exception($GLOBALS['TL_LANG']['WEMSG']['DATAMANAGER']['AJAX']['noPathProvided']);
                    }

                    $this->dataManagerService->removeDataset(Input::post('path'));

                    $arrResponse = [
                        'status' => 'success',
                        'msg' => $GLOBALS['TL_LANG']['WEMSG']['DATAMANAGER']['AJAX']['removeOk'],
                        'callbacks' => [
                            ['method' => 'reload', 'args' => []],
                        ],
                    ];
                break;
                case 'openDatasetShowModal':
                    if (!Input::post('path')) {
                        throw new Exception($GLOBALS['TL_LANG']['WEMSG']['DATAMANAGER']['AJAX']['noPathProvided']);
                    }

                    $dtFile = $this->dataManagerService->getDatasetClass(Input::post('path'));
                    $arrResponse = [
                        'status' => 'success',
                        'msg' => '',
                        'callbacks' => [
                            [
                                'method' => 'openDatasetShowModal',
                                'args' => [
                                    'content' => $this->openDatasetShowModal(Input::post('path')),
                                    'title' => sprintf($GLOBALS['TL_LANG']['WEMSG']['DATAMANAGER']['SHOW']['modalTitle'], $dtFile->getName()),
                                ],
                            ],
                        ],
                    ];
                break;
                default:
                    throw new \Exception(sprintf($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['AjaxInvalidActionSpecified'], $strAction));
            }
        } catch (\Exception $e) {
            $arrResponse = [
                'status' => 'error',
                'msg' => $e->getMessage(),
            ];
        }

        echo json_encode($arrResponse, \JSON_INVALID_UTF8_IGNORE);
        exit;
    }

    protected function openDatasetShowModal(string $datasetPath): string
    {
        $objTemplate = new BackendTemplate('be_wem_sg_data_manager_show_modal');

        $dtFile = $this->dataManagerService->getDatasetClass($datasetPath);
        $dtJsonFile = $this->dataManagerService->getDatasetJson($datasetPath);
        /** @var DataManagerConfig */
        $dmConfig = $this->dataManagerConfigurationManager->load();

        $fakeDataset = (new DataManagerDataSet())
            ->setType($dtFile->getType())
            ->setModule($dtFile->getModule())
            ->setName($dtFile->getName())
        ;
        if ($dmConfig->hasDataset($fakeDataset)) {
            $dtInstalled = $dmConfig->getDataset($dtFile->getType(), $dtFile->getModule(), $dtFile->getName());
        } else {
            $dtInstalled = $fakeDataset;
        }

        $items = [];

        $router = System::getContainer()->get('router');

        foreach ($dtJsonFile->items as $jsonItem) {
            $items[$jsonItem->reference] = [
                'type' => $jsonItem->type,
                'reference' => $jsonItem->reference,
                'table' => $jsonItem->table ?? '',
                'id' => null,
                'fields' => [],
                'source' => $jsonItem->source ?? null,
                'target' => $jsonItem->target ?? null,
                'href' => '',
            ];
            // add info from installed item if exists
            try {
                $dtInstalledItem = $dtInstalled->getItem($jsonItem->reference);
                $items[$jsonItem->reference]['table'] = $dtInstalledItem->getTable();
                $items[$jsonItem->reference]['id'] = $dtInstalledItem->getId();
                $this->loadLanguageFile($items[$jsonItem->reference]['table']);
            } catch (Exception $e) {
                //do nothing
            }
            foreach ($jsonItem->fields ?? [] as $jsonItemField) {
                $items[$jsonItem->reference]['fields'][$jsonItemField->field] = $jsonItemField->value;
            }
            // find a way to associate table to backend route
            if ('media' === $items[$jsonItem->reference]['type']
            || 'tl_files' === $items[$jsonItem->reference]['table']
            ) {
                $objFileModel = FilesModel::findById($items[$jsonItem->reference]['id']);
                if (!$objFileModel) {
                    continue;
                }
                $items[$jsonItem->reference]['href'] = $router->generate('contao_backend', [
                    'do' => 'files',
                    'id' => $objFileModel->path,
                    'act' => 'edit',
                    'rt' => System::getContainer()->get('contao.csrf.token_manager')->getDefaultTokenValue(),
                ]);
            } else {
                foreach ($GLOBALS['BE_MOD'] as $strGroupName => $arrGroupModules) {
                    foreach ($arrGroupModules as $strModuleName => $arrConfig) {
                        if (\array_key_exists('tables', $arrConfig)
                    && \in_array($items[$jsonItem->reference]['table'], $arrConfig['tables'], true)
                    ) {
                            $items[$jsonItem->reference]['href'] = $router->generate('contao_backend', [
                                'do' => $strModuleName,
                                'table' => $items[$jsonItem->reference]['table'],
                                'id' => $items[$jsonItem->reference]['id'],
                                'act' => 'edit',
                                'rt' => System::getContainer()->get('contao.csrf.token_manager')->getDefaultTokenValue(),
                            ]);
                        }
                    }
                }
            }
        }

        $objTemplate->items = $items;

        return $objTemplate->parse();
    }
}
