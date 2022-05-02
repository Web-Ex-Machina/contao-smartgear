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
            && \array_key_exists('month', $searchConfig['date']) && !empty($searchConfig['date']['month'])
            && \array_key_exists('year', $searchConfig['date']) && !empty($searchConfig['date']['year'])
            ) {
                $date = \DateTime::createFromFormat('Y-m-d', $searchConfig['date']['year'].'-'.$searchConfig['date']['month'].'-01');
                $col[] = 'date >= ? AND date <= ?';
                $val[] = $date->setTime(0, 0, 0, 0)->getTimestamp();
                $val[] = $date->setTime(0, 0, 0, 0)->add(new \DateInterval('P1M'))->getTimestamp();
            }

            return NewsModel::countBy($col, $val, []);
        }

        return false;
    }
}
