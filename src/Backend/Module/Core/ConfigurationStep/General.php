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
}
