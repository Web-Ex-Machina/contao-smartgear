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

namespace WEM\SmartgearBundle\Api\Airtable\V0;

use DateTime;
use Exception;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigManager;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Exceptions\Api\ResponseContentException;
use WEM\SmartgearBundle\Exceptions\Api\ResponseSyntaxException;
use WEM\SmartgearBundle\Exceptions\File\NotFound;

/**
 * Very minimal class to call Airtable API.
 */
class Api
{
    public const BASE_URL = 'https://api.airtable.com/v0/';
    public const CACHE_PATH = 'assets/smartgear/';
    /** @var CoreConfigManager */
    protected $configurationManager;
    /** @var string */
    protected $apiKey;

    public function __construct(CoreConfigManager $configurationManager)
    {
        $this->configurationManager = $configurationManager;
        try {
            /** @var CoreConfig */
            $config = $this->configurationManager->load();

            $this->apiKey = $config->getSgAirtableApiKey();
        } catch (NotFound $e) {
            // nothing
        }
    }

    public function getHostingInformations(string $hostname): array
    {
        $base = 'appgIyjWEM42B7t7k';
        $filename = 'airtable_hosting_informations.json';
        if ($this->cacheFileExists($filename) && $this->hasValidCache($filename)) {
            return $this->retrieveFromCache($filename)['data'];
        }

        $url = sprintf('%s%s/%s?maxRecords=1&view=All&filterByFormula=%s', self::BASE_URL, $base, urlencode('Hébergements'), urlencode(sprintf('{Domaines concernés} = "%s"', $hostname)));
        $arrRecords = $this->call($url)->records;

        if (!$arrRecords) {
            return [];
        }
        $fields = json_decode(json_encode($arrRecords[0]->fields), true);

        $data = [
            'project' => $fields['Projet'] ?? '',
            'notes' => $fields['Notes'] ?? '',
            'typology' => $fields['Typologie'] ?? '',
            'yearly_price' => $fields['Montant annuel'] ?? '',
            'domains' => $fields['Domaines concernés'] ?? '',
            'location' => $fields['Emplacement'] ?? '',
            'emails' => $fields['Emails'] ?? '',
            'renew_domain' => $fields['Renew Domain'] ?? '',
            'allowed_space' => $fields['Espace disponible (Go)'] ?? '',
            'must_pay' => $fields['Doit passer à la caisse'] ?? '',
            'birthday' => $fields['Anniversaire'] ?? '',
            'client_id' => $fields['Client'][0] ?? '',
            'invoices_ids' => $fields['Factures'] ?? '',
            'invoices_dates' => $fields['Date (from Factures)'] ?? '',
            'invoices_prices' => $fields['HT (from Factures)'] ?? '',
            'invoices_urls' => $fields['URLs Factures'] ?? '',
            'services_other' => $fields['Services annexe'] ?? '',
        ];

        $this->saveCacheFile($filename, $data);

        return $data;
    }

    public function createTicket(string $clientId, string $subject, string $url, string $message, string $mail, ?string $screenshotFileUrl = null): void
    {
        $base = 'appnCkg7yADMSvVAz';
        $apiUrl = sprintf('%s%s/%s', self::BASE_URL, $base, urlencode('Tickets'));
        $data = [
            'records' => [
                [
                    'fields' => [
                        'Client' => $clientId,
                        'Sujet' => $subject,
                        'URL' => $url,
                        'Message' => $message,
                        'Mail' => $mail,
                        'Date' => (new DateTime())->format('m/d/Y H:i'), // Yup, american style
                        'Status' => 'Todo',
                    ],
                ],
            ],
        ];
        if (null !== $screenshotFileUrl) {
            $data['records'][0]['fields']['Capture d\'écran'][] = ['url' => $screenshotFileUrl];
        }

        $result = $this->call($apiUrl, $data);

        if ($result->error) {
            throw new Exception('['.$result->error->type.'] '.$result->error->message);
        }
    }

    protected function cacheFileExists(string $name): bool
    {
        return file_exists($this->buildFilePath($name));
    }

    protected function saveCacheFile(string $name, array $data): void
    {
        $data = [
            'expiration_timestamp' => time() + 86400,
            'data' => $data,
        ];

        file_put_contents($this->buildFilePath($name), json_encode($data));
    }

    protected function hasValidCache(string $name): bool
    {
        $data = $this->retrieveFromCache($name);

        return $data['expiration_timestamp'] > time();
    }

    protected function retrieveFromCache(string $name): array
    {
        return json_decode(file_get_contents($this->buildFilePath($name)), true);
    }

    protected function buildFilePath(string $name): string
    {
        return self::CACHE_PATH.$name;
    }

    protected function call(string $url, array $data = [])
    {
        $baseUrl = static::BASE_URL;

        if (false === strpos($url, $baseUrl)) {
            $url = $baseUrl.$url;
        }

        $curl = curl_init();
        $httpHeaders = [sprintf('Authorization: Bearer %s', $this->apiKey)];
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, 'webexmachina/1.0 +'.\Contao\Environment::get('base'));
        if (!empty($data)) {
            $httpHeaders['Content-Type'] = 'application/json';
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $httpHeaders);
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
