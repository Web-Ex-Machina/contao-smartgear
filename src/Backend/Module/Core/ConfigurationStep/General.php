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

use Contao\Input;
use Exception;
use WEM\SmartgearBundle\Classes\Backend\ConfigurationStep;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Config\Core as CoreConfig;
use WEM\SmartgearBundle\Config\LocalConfig as LocalConfig;
use WEM\SmartgearBundle\Config\Manager\LocalConfig as LocalConfigManager;

class General extends ConfigurationStep
{
    /** @var ConfigurationManager */
    protected $configurationManager;

    public function __construct(
        string $module,
        string $type,
        ConfigurationManager $configurationManager,
        LocalConfigManager $localConfigManager
    ) {
        parent::__construct($module, $type);
        $this->configurationManager = $configurationManager;
        $this->localConfigManager = $localConfigManager;
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
        Util::executeCmdPHP('cache:clear');
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
        /** @var LocalConfig */
        $config = $this->localConfigManager->load();

        $config->setDateFormat('d/m/Y')
        ->setTimeFormat('H:i')
        ->setDatimFormat('d/m/Y à H:i')
        ->setTimeZone('Europe/Paris')
        ->setCharacterSet('utf-8')
        ->setUseAutoItem(1)
        ->setFolderUrl(1)
        ->setMaxResultsPerPage(500)
        ->setPrivacyAnonymizeIp(1)
        ->setPrivacyAnonymizeGA(1)
        ->setGdMaxImgWidth(5000)
        ->setGdMaxImgHeight(5000)
        ->setMaxFileSize(10000000)
        ->setUndoPeriod(7776000)
        ->setVersionPeriod(7776000)
        ->setLogPeriod(7776000)
        ->setAllowedTags('<script><iframe><a><abbr><acronym><address><area><article><aside><audio><b><bdi><bdo><big><blockquote><br><base><button><canvas><caption><cite><code><col><colgroup><data><datalist><dataset><dd><del><dfn><div><dl><dt><em><fieldset><figcaption><figure><footer><form><h1><h2><h3><h4><h5><h6><header><hgroup><hr><i><img><input><ins><kbd><keygen><label><legend><li><link><map><mark><menu><nav><object><ol><optgroup><option><output><p><param><picture><pre><q><s><samp><section><select><small><source><span><strong><style><sub><sup><table><tbody><td><textarea><tfoot><th><thead><time><tr><tt><u><ul><var><video><wbr>')
        ->setSgOwnerDomain(\Contao\Environment::get('base'))
        ->setSgOwnerHost(CoreConfig::DEFAULT_OWNER_HOST)
        ->setRejectLargeUploads(true)
        ->setImageSizes([
            '_defaults' => [
                'formats' => [
                    'jpg' => ['jpg', 'jpeg'],
                    'png' => ['png'],
                    'gif' => ['gif'],
                ],
                'lazy_loading' => true,
                'resize_mode' => 'crop',
            ],
            '16-9' => [
                'width' => 1920,
                'height' => 1080,
                'densities' => '0.5x, 1x, 2x',
            ],
            '2-1' => [
                'width' => 1920,
                'height' => 960,
                'densities' => '2x',
            ],
            '1-2' => [
                'width' => 960,
                'height' => 1920,
                'densities' => '0.5x',
            ],
            '1-1' => [
                'width' => 1920,
                'height' => 1920,
                'densities' => '1x',
            ],
            '4-3' => [
                'width' => 1920,
                'height' => 1440,
                'densities' => '0.5x, 1x, 2x',
            ],
        ])
        ;

        $this->localConfigManager->save($config);
    }
}
