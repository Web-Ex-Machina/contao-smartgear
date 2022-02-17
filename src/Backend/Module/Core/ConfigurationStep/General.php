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

namespace WEM\SmartgearBundle\Backend\Module\Core\ConfigurationStep;

use Contao\Config;
use Contao\Input;
use Exception;
use Symfony\Component\Yaml\Yaml;
use WEM\SmartgearBundle\Classes\Backend\ConfigurationStep;
use WEM\SmartgearBundle\Classes\Config\Manager as ConfigurationManager;
use WEM\SmartgearBundle\Config\Core as CoreConfig;

class General extends ConfigurationStep
{
    /** @var ConfigurationManager */
    protected $configurationManager;

    public function __construct(
        string $module,
        string $type,
        ConfigurationManager $configurationManager
    ) {
        parent::__construct($module, $type);
        $this->configurationManager = $configurationManager;
        $this->title = 'Général';
        try {
            /** @var CoreConfig */
            $config = $this->configurationManager->load();
        } catch (Exception $e) {
            /** @var CoreConfig */
            $config = $this->configurationManager->new();
        }

        $this->addTextField('sgWebsiteTitle', 'Titre du site web', $config->getSgWebsiteTitle(), true);
        $this->addTextField('sgOwnerEmail', 'Adresse email de l\'administrateur', $config->getSgOwnerEmail(), true);

        $sgAnalyticsOptions = [
            [
                'label' => 'Aucun',
                'value' => CoreConfig::ANALYTICS_SYSTEM_NONE,
            ],
            [
                'label' => 'Matomo',
                'value' => CoreConfig::ANALYTICS_SYSTEM_MATOMO,
            ],
            [
                'label' => 'Google',
                'value' => CoreConfig::ANALYTICS_SYSTEM_GOOGLE,
            ],
        ];

        $this->addSelectField('sgAnalytics', 'Solution statistiques', $sgAnalyticsOptions, $config->getSgAnalytics(), true);
        $this->addTextField('sgAnalyticsGoogleId', 'Identifiant Google Analytics', $config->getSgAnalyticsGoogleId(), false);
        $this->addTextField('sgAnalyticsMatomoId', 'Identifiant Matomo', $config->getSgAnalyticsMatomoId(), false);
        $this->addTextField('sgAnalyticsMatomoHost', 'Host Matomo', $config->getSgAnalyticsMatomoHost(), false);
    }

    public function isStepValid(): bool
    {
        // check if the step is correct
        if (empty(Input::post('sgWebsiteTitle'))) {
            throw new Exception('Le titre du site web n\'est pas renseigné.');
        }

        if (empty(Input::post('sgOwnerEmail'))) {
            throw new Exception('L\'adresse email de l\'administrateur n\'est pas renseignée.');
        }

        if (CoreConfig::ANALYTICS_SYSTEM_MATOMO === Input::post('sgAnalytics')) {
            if (empty(Input::post('sgAnalyticsMatomoId'))) {
                throw new Exception('L\'identifiant Matomo n\'est pas renseigné.');
            }
            if (empty(Input::post('sgAnalyticsMatomoHost'))) {
                throw new Exception('Le Host Matomo n\'est pas renseigné.');
            }
        }

        if (CoreConfig::ANALYTICS_SYSTEM_GOOGLE === Input::post('sgAnalytics')) {
            if (empty(Input::post('sgAnalyticsGoogleId'))) {
                throw new Exception('L\'identifiant Google Analytics n\'est pas renseigné.');
            }
        }

        return true;
    }

    public function do(): void
    {
        // do what is meant to be done in this step
        $this->updateModuleConfiguration();
        $this->updateContaoConfiguration();
    }

    protected function updateModuleConfiguration(): void
    {
        try {
            /** @var CoreConfig */
            $config = $this->configurationManager->load();
        } catch (Exception $e) {
            /** @var CoreConfig */
            $config = $this->configurationManager->new();
        }
        $config->setSgWebsiteTitle(Input::post('sgWebsiteTitle'));
        $config->setSgOwnerEmail(Input::post('sgOwnerEmail'));
        $config->setSgAnalytics(Input::post('sgAnalytics'));
        $config->setSgAnalyticsGoogleId(Input::post('sgAnalyticsGoogleId'));
        $config->setSgAnalyticsMatomoId(Input::post('sgAnalyticsMatomoId'));
        $config->setSgAnalyticsMatomoHost(Input::post('sgAnalyticsMatomoHost'));

        $this->configurationManager->save($config);
    }

    protected function updateContaoConfiguration(): void
    {
        // Config::persist();
        $configFilePath = '../config/config.yml';
        $config = [];
        if (file_exists($configFilePath)) {
            $yamlparser = new \Symfony\Component\Yaml\Parser();
            $config = $yamlparser->parse(file_get_contents($configFilePath));
        }
        $config['contao']['localconfig']['dateFormat'] = 'd/m/Y';
        $config['contao']['localconfig']['timeFormat'] = 'H:i';
        $config['contao']['localconfig']['datimFormat'] = 'd/m/Y à H:i';
        $config['contao']['localconfig']['timeZone'] = 'Europe/Paris';
        $config['contao']['localconfig']['characterSet'] = 'utf-8';
        $config['contao']['localconfig']['useAutoItem'] = 1;
        $config['contao']['localconfig']['folderUrl'] = 1;
        $config['contao']['localconfig']['maxResultsPerPage'] = 500;
        $config['contao']['localconfig']['privacyAnonymizeIp'] = 1;
        $config['contao']['localconfig']['privacyAnonymizeGA'] = 1;
        $config['contao']['localconfig']['gdMaxImgWidth'] = 5000;
        $config['contao']['localconfig']['gdMaxImgHeight'] = 5000;
        $config['contao']['localconfig']['maxFileSize'] = 10000000;
        $config['contao']['localconfig']['undoPeriod'] = 7776000;
        $config['contao']['localconfig']['versionPeriod'] = 7776000;
        $config['contao']['localconfig']['logPeriod'] = 7776000;
        $config['contao']['localconfig']['allowedTags'] = '<script><iframe><a><abbr><acronym><address><area><article><aside><audio><b><bdi><bdo><big><blockquote><br><base><button><canvas><caption><cite><code><col><colgroup><data><datalist><dataset><dd><del><dfn><div><dl><dt><em><fieldset><figcaption><figure><footer><form><h1><h2><h3><h4><h5><h6><header><hgroup><hr><i><img><input><ins><kbd><keygen><label><legend><li><link><map><mark><menu><nav><object><ol><optgroup><option><output><p><param><picture><pre><q><s><samp><section><select><small><source><span><strong><style><sub><sup><table><tbody><td><textarea><tfoot><th><thead><time><tr><tt><u><ul><var><video><wbr>';
        $config['contao']['localconfig']['sgOwnerDomain'] = \Contao\Environment::get('base');
        $config['contao']['localconfig']['sgOwnerHost'] = 'INFOMANIAK - 25 Eugène-Marziano 1227 Les Acacias - GENÈVE - SUISSE';
        $config['contao']['image']['reject_large_uploads'] = true;
        /**
         * @todo : configure the sizes
         * https://docs.contao.org/manual/en/system/settings/#config-yml
         */
        $yaml = Yaml::dump($config);

        file_put_contents($configFilePath, $yaml);
    }
}
