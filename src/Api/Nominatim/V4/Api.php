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

namespace WEM\SmartgearBundle\Api\Nominatim\V4;

use WEM\SmartgearBundle\Api\Nominatim\V4\Model\Mapper\StdClassToSearchResponse as StdClassToSearchResponseMapper;
use WEM\SmartgearBundle\Api\Nominatim\V4\Model\SearchResponse;
use WEM\SmartgearBundle\Exceptions\Api\ResponseContentException;
use WEM\SmartgearBundle\Exceptions\Api\ResponseSyntaxException;

/**
 * Very minimal class to call this geocoding API.
 */
class Api
{
    public const BASE_URL = 'https://nominatim.openstreetmap.org/';

    /** @var StdClassToSearchResponseMapper */
    protected $stdClassToSearchResponseMapper;

    public function __construct(StdClassToSearchResponseMapper $stdClassToSearchResponseMapper)
    {
        $this->stdClassToSearchResponseMapper = $stdClassToSearchResponseMapper;
    }

    public function search(string $search): SearchResponse
    {
        $apiResponse = $this->call(sprintf('%ssearch?q=%s&format=jsonv2', self::BASE_URL, urlencode($search)))[0];

        return null !== $apiResponse
        ? $this->stdClassToSearchResponseMapper->map(
            $apiResponse,
            new SearchResponse()
        )
        : new SearchResponse()
        ;
    }

    protected function call(string $url): array
    {
        $baseUrl = static::BASE_URL;

        if (false === strpos($url, $baseUrl)) {
            $url = $baseUrl.$url;
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, 'webexmachina/1.0 +'.\Contao\Environment::get('base'));
        sleep(1);
        $jsonRaw = curl_exec($curl);
        curl_close($curl);
        $json = json_decode($jsonRaw);

        if (\JSON_ERROR_NONE !== json_last_error()) {
            throw new ResponseSyntaxException(json_last_error_msg());
        }
        // @TODO : find a working way to test the response' http code
        // https://www.php.net/manual/fr/function.curl-getinfo.php
        // (official method responds "0" which isn't helpful)
        if (1 === \count($json) && !empty($json->message)) {
            throw new ResponseContentException($json->message);
        }

        return $json;
    }
}
