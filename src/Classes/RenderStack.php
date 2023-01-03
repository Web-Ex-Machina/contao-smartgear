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

namespace WEM\SmartgearBundle\Classes;

use Contao\ContentModel;
use Contao\Model;
use Contao\ModuleModel;
use Exception;

/**
 * This class aim to retain all generated elements.
 *
 * Warning : it will weight nearly as much as the rendered page itself.
 * Use with caution.
 */
class RenderStack
{
    /** @var self */
    protected static $instance;
    /** @var array */
    protected $stack = [
        'current_index' => [
            'all' => 0,
            //other columns will go here
        ],
        'items' => [],
        'breadcrumb_indexes' => [
            'all' => [],
            //other columns will go here
        ],
    ];

    public function __construct()
    {
    }

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Add a ModuleModel/ContentModel to the stack.
     *
     * @param \Contao\ModuleModel|\Contao\ContentModel $model           The model (either ModuleModel or ContentModel)
     * @param string                                   $buffer          The generated HTML
     * @param \Contao\Module|\Contao\ContentElement    $contentOrModule The module or content element
     */
    public function add(Model $model, string $buffer, $contentOrModule): void
    {
        if (!is_a($model, ModuleModel::class) && !is_a($model, ContentModel::class)) {
            return;
        }

        $column = $contentOrModule->Template->inColumn;

        if (!\array_key_exists($column, $this->stack['current_index'])) {
            $this->stack['current_index'][$column] = 0;
        }
        $this->stack['items'][$this->stack['current_index']['all']] = [
            'index' => $this->stack['current_index']['all'],
            'index_in_column' => $this->stack['current_index'][$column],
            'model' => $model,
            'buffer' => $buffer,
            'contentOrModule' => $contentOrModule,
            'column' => $column,
        ];

        if (is_a($model, ModuleModel::class) && 'breadcrumb' === $model->type) {
            $this->stack['breadcrumb_indexes']['all'][] = $this->stack['current_index']['all'];
            $this->stack['breadcrumb_indexes'][$contentOrModule->Template->inColumn][] = $this->stack['current_index'][$column];
        }

        ++$this->stack['current_index']['all'];
        ++$this->stack['current_index'][$column];
    }

    /**
     * Get an element in the stack by its index.
     *
     * @param int $index The element's index
     *
     * @throws Exception if index value is out of bounds
     */
    public function get(int $index): array
    {
        if (!\array_key_exists($index, $this->stack['items'])) {
            throw new Exception('Out of bounds');
        }

        return $this->stack['items'][$index];
    }

    /**
     * Return all the breadcrumb elements indexes.
     *
     * @param string|null $column If given, will only returns breadcrumbs from that column
     */
    public function getBreadcrumbIndexes(?string $column = null): array
    {
        $column = null === $column ? 'all' : $column;
        if (!\array_key_exists($column, $this->stack['breadcrumb_indexes'])) {
            return [];
        }

        return $this->stack['breadcrumb_indexes'][$column];
    }

    /**
     * Return all items.
     *
     * @param string|null $column If given, will only returns items from that column
     */
    public function getItems(?string $column = null): array
    {
        if (null === $column) {
            return $this->stack['items'];
        }

        $items = [];
        foreach ($this->stack['items'] as $item) {
            if ($column === $item['column']) {
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * Return all the breadcrumb items.
     *
     * @param string|null $column If given, will only returns breadcrumbs from that column
     */
    public function getBreadcrumbItems(?string $column = null): array
    {
        $column = null === $column ? 'all' : $column;
        $breadcrumbItems = [];
        $indexes = $this->stack['breadcrumb_indexes']['all'];

        foreach ($indexes as $index) {
            if ('all' === $column || $column === $this->stack['items'][$index]['column']) {
                $breadcrumbItems[] = $this->stack['items'][$index];
            }
        }

        return $breadcrumbItems;
    }
}
