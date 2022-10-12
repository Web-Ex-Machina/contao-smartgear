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

namespace WEM\SmartgearBundle\EventListener;

use Contao\NewsModel;
use WEM\SmartgearBundle\Classes\Util;

class NewsListCountItemsListener
{
    public function __invoke(array $newsArchives, ?bool $featuredOnly, \Contao\Module $module)
    {
        $searchConfig = $module->getConfig();
        if (!empty($searchConfig)) {
            $col = ['published = ?', '(start = "" OR start <= ?)', '(stop = "" OR stop >= ?)', 'pid IN (?)'];
            $val = ['1', time(), time(), implode(',', $newsArchives)];

            if ($featuredOnly) {
                $col[] = 'featured = ?';
                $val[] = '1';
            }

            if (\array_key_exists('author', $searchConfig) && !empty($searchConfig['author'])) {
                $col[] = 'author = ?';
                $val[] = $searchConfig['author'];
            }

            if (\array_key_exists('date', $searchConfig) && !empty($searchConfig['date'])
            && (
                (\array_key_exists('year', $searchConfig['date']) && !empty($searchConfig['date']['year']))
                ||
                \array_key_exists('month', $searchConfig['date']) && !empty($searchConfig['date']['month'])
            )
            ) {
                $timestampsDuo = Util::getTimestampsFromDateConfig(
                    \array_key_exists('year', $searchConfig['date']) && !empty($searchConfig['date']['year']) ? (int) $searchConfig['date']['year'] : null,
                    \array_key_exists('month', $searchConfig['date']) && !empty($searchConfig['date']['month']) ? (int) $searchConfig['date']['month'] : null,
                    null
                );
                $colConfig = [];
                foreach ($timestampsDuo as $duo) {
                    $colConfig[] = 'date >= ? AND date <= ?';
                    $val[] = $duo[0];
                    $val[] = $duo[1];
                }
                $col[] = implode(' OR ', $colConfig);
            }

            return NewsModel::countBy($col, $val, []);
        }

        return false;
    }
}
