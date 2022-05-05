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

namespace WEM\SmartgearBundle\Api\Nominatim\V4\Model\Mapper;

use WEM\SmartgearBundle\Api\Nominatim\V4\Model\SearchResponse;

class StdClassToSearchResponse
{
    public function map(\StdClass $stdClass, SearchResponse $searchResponse): SearchResponse
    {
        $searchResponse
            ->setLat($stdClass->lat)
            ->setLon($stdClass->lon)
        ;

        return $searchResponse;
    }
}
