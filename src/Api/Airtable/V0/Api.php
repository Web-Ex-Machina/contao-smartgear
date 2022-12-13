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
use WEM\SmartgearBundle\Classes\CacheFileManager;
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
        $base = 'appgIyjWEM42B7t7k'; // TMA
        $tableId = 'tblf16abZvAYcYTsF'; // Hébergement
        $viewName = 'All'; // All
        $filename = 'airtable_hosting_informations.json';

        $cacheManager = new CacheFileManager(self::CACHE_PATH.$filename, 86400);

        if ($cacheManager->cacheFileExists() && $cacheManager->hasValidCache()) {
            return $cacheManager->retrieveFromCache()['data'];
        }

        $url = sprintf('%s%s/%s?maxRecords=1&view=%s&filterByFormula=%s&returnFieldsByFieldId=1', self::BASE_URL, $base, $tableId, urlencode($viewName), urlencode(sprintf('{Domaines concernés} = "%s"', $hostname)));
        $arrRecords = $this->call($url)->records;

        if (!$arrRecords) {
            return [];
        }
        $fields = json_decode(json_encode($arrRecords[0]->fields), true);

        $fieldIds = [
            'Projet' => 'fldC39iGcNGHzyfdA',
            'Client' => 'flduTTwPIQHud0qRh',
            'Anniversaire' => 'fldNu6ZtRQH8lezMX',
            'Doit passer à la caisse' => 'fldAa1tchPLPbnydU',
            'Notes' => 'fldgBBz3o2A59VojO',
            'Typologie' => 'fldMhHO1uY5gVZbhh',
            'Montant annuel' => 'fldvCAyI5s0mEix0Y',
            'Domaines concernés' => 'fldiVZrQbzjQ450t6',
            'Emplacement' => 'fldcv1eUW7DATxCM0',
            'Date de création' => 'fldEKOa7FaWVGm2bx',
            'Emails' => 'fldIIpUyY87tbukf8',
            'Renew Domain' => 'fldxGXfhDYF0Rovvq',
            'Factures' => 'fldHIfoCw4foRQY3F',
            'HT (from Factures)' => 'fldf4wpDljGU9glky',
            'Date (from Factures)' => 'fldv9QNTK6cW2PrDy',
            'Services annexe' => 'fldF76sjf0r6ldFlK',
            'Espace disponible (Go)' => 'fldx29zhDInO5Ntdu',
            'URLs Factures' => 'fldF5euVenVb70IpN',
            'RefClient' => 'fldKHIomEovveYM77',
        ];

        $data = [
            'project' => $fields[$fieldIds['Projet']] ?? '',
            'notes' => $fields[$fieldIds['Notes']] ?? '',
            'typology' => $fields[$fieldIds['Typologie']] ?? '',
            'yearly_price' => $fields[$fieldIds['Montant annuel']] ?? '',
            'domains' => $fields[$fieldIds['Domaines concernés']] ?? '',
            'location' => $fields[$fieldIds['Emplacement']] ?? '',
            'emails' => $fields[$fieldIds['Emails']] ?? '',
            'renew_domain' => $fields[$fieldIds['Renew Domain']] ?? '',
            'allowed_space' => $fields[$fieldIds['Espace disponible (Go)']] ?? '',
            'must_pay' => $fields[$fieldIds['Doit passer à la caisse']] ?? '',
            'birthday' => $fields[$fieldIds['Anniversaire']] ?? '',
            'client_id' => implode(',', $fields[$fieldIds['Client']]) ?? '',
            'invoices_ids' => $fields[$fieldIds['Factures']] ?? [],
            'invoices_dates' => $fields[$fieldIds['Date (from Factures)']] ?? [],
            'invoices_prices' => $fields[$fieldIds['HT (from Factures)']] ?? [],
            'invoices_urls' => $fields[$fieldIds['URLs Factures']] ?? [],
            'services_other' => $fields[$fieldIds['Services annexe']] ?? [],
            'client_reference' => $fields[$fieldIds['RefClient']] ?? '',
        ];

        $cacheManager->saveCacheFile($data);

        return $data;
    }

    public function getSupportClientInformations(string $clientRef): array
    {
        $base = 'appnCkg7yADMSvVAz'; // Support
        $tableId = 'tblJqg149TWDoZCCm'; // Client
        $viewName = 'Grid view'; // Grid view
        $filename = 'airtable_support_client_informations.json';
        $apiUrl = sprintf('%s%s/%s', self::BASE_URL, $base, $tableId);

        $cacheManager = new CacheFileManager(self::CACHE_PATH.$filename, 86400);

        if ($cacheManager->cacheFileExists() && $cacheManager->hasValidCache()) {
            return $cacheManager->retrieveFromCache()['data'];
        }

        $url = sprintf('%s%s/%s?maxRecords=1&view=%s&filterByFormula=%s&returnFieldsByFieldId=1', self::BASE_URL, $base, $tableId, urlencode($viewName), urlencode(sprintf('{Reference} = "%s"', $clientRef)));
        $arrRecords = $this->call($url)->records;

        if (!$arrRecords) {
            return [];
        }
        $record = json_decode(json_encode($arrRecords[0]), true);
        $fields = $record['fields'];

        $fieldIds = [
            'Name' => 'fldSciizZjeSyHQxW',
            'Temps restant' => 'fldALXGLmS6cNHEpk',
            'Alerte' => 'flddOEa7IWxIRbMbC',
            'Devis link' => 'fldsqFOUFKrChA4ll',
            'Facture Link' => 'fldvJC5lKN4YMWeh8',
            'Notes' => 'fldrqY5IGx51aC3p0',
            'Contrats de TMA' => 'fldAeqnOpYOyL789T',
            'Contact' => 'fld6cEY9TPXufdo1r',
            'Reference' => 'fldVuxcGmTE6obZsY',
            'Tickets' => 'fld9j8dpyBQomnGr6',
        ];

        $data = [
            'id' => $record['id'],
            'name' => $fields[$fieldIds['Name']] ?? '',
            'time_remaining' => $fields[$fieldIds['Temps restant']] ?? '',
            'alert' => $fields[$fieldIds['Alerte']] ?? '',
            'quotation_links' => $fields[$fieldIds['Devis link']] ?? '',
            'invoices_links' => $fields[$fieldIds['Facture Link']] ?? '',
            'notes' => $fields[$fieldIds['Notes']] ?? '',
            'contracts_maintenance' => $fields[$fieldIds['Contrats de TMA']] ?? '',
            'contact' => $fields[$fieldIds['Contact']] ?? '',
            'reference' => $fields[$fieldIds['Reference']] ?? '',
            'tickets_ids' => $fields[$fieldIds['Tickets']] ?? '',
        ];

        $cacheManager->saveCacheFile($data);

        return $data;
    }

    public function createTicket(string $subject, string $url, string $message, string $mail, string $version, ?string $clientId, ?string $clientRef, ?string $screenshotFileUrl = null): void
    {
        $base = 'appnCkg7yADMSvVAz'; // Support
        $tableId = 'tblSIbNnP1grnbZ53'; // Tickets
        $apiUrl = sprintf('%s%s/%s', self::BASE_URL, $base, $tableId);

        $fieldIds = [
            'Client' => 'fldqMCI8mqp1njQvJ',
            'Date' => 'fldItb12OJnNCC1rI',
            'Client ref' => 'fldH9RpuOdCa9rtyk',
            'Sujet' => 'fldNeIZNtYgWxnEcf',
            'Message' => 'fldV6wiIQqAhExbnY',
            'URL' => 'fldfT0Jw4jSrgv293',
            'Capture d\'écran' => 'fldWg89FPw5rBkFvT',
            'Mail' => 'fldS1XUp1VaWVXK9X',
            'Version' => 'fldaGltrHBsz7alt4',
            'Assignee' => 'flddgcEFglHG9nSjG',
            'Status' => 'flddJ2bDXFeBZGcy6',
            'Notes' => 'fldeDgrJoPC9tseuZ',
            'Temps estimé' => 'fldVQoDXRrVHy3dxV',
            'Temps passé' => 'fldbj3RJ05161kuZK',
        ];

        $data = [
            'records' => [
                [
                    'fields' => [
                        $fieldIds['Client'] => $clientId ? [$clientId] : '',
                        $fieldIds['Client ref'] => $clientRef ?? '',
                        $fieldIds['Sujet'] => $subject,
                        $fieldIds['URL'] => $url,
                        $fieldIds['Message'] => $message,
                        $fieldIds['Mail'] => $mail,
                        $fieldIds['Version'] => $version,
                        $fieldIds['Date'] => (new DateTime())->format('m/d/Y H:i'), // Yup, american style
                        $fieldIds['Status'] => 'Todo',
                    ],
                ],
            ],
        ];
        if (null !== $screenshotFileUrl) {
            $data['records'][0]['fields'][$fieldIds['Capture d\'écran']][] = ['url' => $screenshotFileUrl];
        }

        $result = $this->call($apiUrl, $data);

        if ($result->error) {
            throw new Exception('['.$result->error->type.'] '.$result->error->message);
        }
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
