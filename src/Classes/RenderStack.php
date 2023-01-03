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

class RenderStack
{
    public static function init(): void
    {
        if (!\array_key_exists('WEMSG', $GLOBALS)) {
            $GLOBALS['WEMSG'] = [];
        }
        if (!\array_key_exists('RenderStack', $GLOBALS['WEMSG'])) {
            $GLOBALS['WEMSG']['RenderStack'] = [
                'current_index' => [
                    'all' => 0,
                ],
                'items' => [],
                'breadcrumb_indexes' => [
                    'all' => [],
                ],
            ];
        }
    }

    public static function add(Model $model, string $buffer, $contentOrModule): void
    {
        if (!is_a($model, ModuleModel::class) && !is_a($model, ContentModel::class)) {
            return;
        }

        $column = $contentOrModule->Template->inColumn;

        if (!\array_key_exists($column, $GLOBALS['WEMSG']['RenderStack']['current_index'])) {
            $GLOBALS['WEMSG']['RenderStack']['current_index'][$column] = 0;
        }
        $GLOBALS['WEMSG']['RenderStack']['items'][$GLOBALS['WEMSG']['RenderStack']['current_index']['all']] = [
            'index' => $GLOBALS['WEMSG']['RenderStack']['current_index']['all'],
            'index_in_column' => $GLOBALS['WEMSG']['RenderStack']['current_index'][$column],
            'model' => $model,
            'buffer' => $buffer,
            'contentOrModule' => $contentOrModule,
            'column' => $column,
        ];

        if (is_a($model, ModuleModel::class) && 'breadcrumb' === $model->type) {
            $GLOBALS['WEMSG']['RenderStack']['breadcrumb_indexes']['all'][] = $GLOBALS['WEMSG']['RenderStack']['current_index']['all'];
            $GLOBALS['WEMSG']['RenderStack']['breadcrumb_indexes'][$contentOrModule->Template->inColumn][] = $GLOBALS['WEMSG']['RenderStack']['current_index'][$column];
        }

        ++$GLOBALS['WEMSG']['RenderStack']['current_index']['all'];
        ++$GLOBALS['WEMSG']['RenderStack']['current_index'][$column];
    }

    public static function get(int $index): array
    {
        if (!\array_key_exists($index, $GLOBALS['WEMSG']['RenderStack']['items'])) {
            throw new Exception('Out of bounds');
        }

        return $GLOBALS['WEMSG']['RenderStack']['items'][$index];
    }

    public static function getBreadcrumbIndexes(?string $column = null): array
    {
        $column = null === $column ? 'all' : $column;
        if (!\array_key_exists($column, $GLOBALS['WEMSG']['RenderStack']['breadcrumb_indexes'])) {
            return [];
        }

        return $GLOBALS['WEMSG']['RenderStack']['breadcrumb_indexes'][$column];
    }

    public static function getItems(?string $column = null): array
    {
        if (null === $column) {
            return $GLOBALS['WEMSG']['RenderStack']['items'];
        }

        $items = [];
        foreach ($GLOBALS['WEMSG']['RenderStack']['items'] as $item) {
            if ($column === $item['column']) {
                $items[] = $item;
            }
        }

        return $items;
    }

    public static function getBreadcrumbItems(?string $column = null): array
    {
        $column = null === $column ? 'all' : $column;
        $breadcrumbItems = [];
        $indexes = $GLOBALS['WEMSG']['RenderStack']['breadcrumb_indexes']['all'];

        foreach ($indexes as $index) {
            if ('all' === $column || $column === $GLOBALS['WEMSG']['RenderStack']['items'][$index]['column']) {
                $breadcrumbItems[] = $GLOBALS['WEMSG']['RenderStack']['items'][$index];
            }
        }

        return $breadcrumbItems;
    }
}
