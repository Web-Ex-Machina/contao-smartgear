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

class Website extends ConfigurationStep
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
        $this->title = 'Informations';
        /** @var CoreConfig */
        $config = $this->configurationManager->new();

        // le logo, qlq part
        $this->addFileField('sgWebsiteLogo', 'Logo du site web', true);
        $this->addTextField('sgWebsiteTitle', 'Titre du site web', !empty($config->getSgOwnerName()) ? $config->getSgOwnerName() : $config->getSgWebsiteTitle(), true);
        $this->addTextField('sgOwnerStatus', 'Statut', $config->getSgOwnerStatus(), true);
        $this->addTextField('sgOwnerSiret', 'SIRET', $config->getSgOwnerSiret(), true);
        $this->addTextField('sgOwnerStreet', 'Adresse', $config->getSgOwnerStreet(), true);
        $this->addTextField('sgOwnerPostal', 'Code postal', $config->getSgOwnerPostal(), true);
        $this->addTextField('sgOwnerCity', 'Ville', $config->getSgOwnerCity(), true);
        $this->addTextField('sgOwnerRegion', 'Region', $config->getSgOwnerRegion(), true);
        $this->addSelectField('sgOwnerCountry', 'Pays', \Contao\System::getCountries(), $config->getSgOwnerCountry(), true);
        $this->addTextField('sgOwnerEmail', 'Email', $config->getSgOwnerEmail(), true);
        $this->addTextField('sgOwnerDomain', 'Domaine', !empty($config->getSgOwnerDomain()) ? $config->getSgOwnerDomain() : \Contao\Environment::get('base'), true);
        $this->addTextField('sgOwnerHost', 'Nom et adresse de l\'hébergeur', $config->getSgOwnerHost(), true);
        $this->addTextField('sgOwnerDpoName', 'Nom du DPO', $config->getSgOwnerDpoName(), true);
        $this->addTextField('sgOwnerDpoEmail', 'Email du DPO', $config->getSgOwnerDpoEmail(), true);
    }

    public function isStepValid(): bool
    {
        // check if the step is correct
        if (empty(Input::post('sgWebsiteTitle'))) {
            throw new Exception('Le titre du site web n\'est pas renseigné.');
        }

        if (empty(Input::post('sgOwnerStatus'))) {
            throw new Exception('Le statut n\'est pas renseigné.');
        }

        if (empty(Input::post('sgOwnerSiret'))) {
            throw new Exception('Le numéro de SIRET n\'est pas renseigné.');
        }

        if (empty(Input::post('sgOwnerStreet'))) {
            throw new Exception('La rue n\'est pas renseignée.');
        }

        if (empty(Input::post('sgOwnerPostal'))) {
            throw new Exception('Le code postal n\'est pas renseigné.');
        }

        if (empty(Input::post('sgOwnerCity'))) {
            throw new Exception('La ville n\'est pas renseignée.');
        }

        if (empty(Input::post('sgOwnerRegion'))) {
            throw new Exception('La région n\'est pas renseignée.');
        }

        if (empty(Input::post('sgOwnerCountry'))) {
            throw new Exception('Le pays n\'est pas renseigné.');
        }

        if (empty(Input::post('sgOwnerEmail'))) {
            throw new Exception('L\'adresse email de l\'administrateur n\'est pas renseignée.');
        }

        if (empty(Input::post('sgOwnerDomain'))) {
            throw new Exception('Le domaine n\'est pas renseigné.');
        }

        if (empty(Input::post('sgOwnerHost'))) {
            throw new Exception('Les informations de l\'hébergeur ne sont pas renseignées.');
        }

        if (empty(Input::post('sgOwnerDpoName'))) {
            throw new Exception('Le nom du DPO n\'est pas renseigné.');
        }

        if (empty(Input::post('sgOwnerDpoEmail'))) {
            throw new Exception('L\'adresse email du DPO n\'est pas renseignée.');
        }

        return true;
    }

    public function do(): void
    {
        // do what is meant to be done in this step
        $this->uploadLogo();
        $this->updateModuleConfiguration();
    }

    protected function updateModuleConfiguration(): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        $config->setSgWebsiteTitle(Input::post('sgWebsiteTitle'));
        $config->setSgOwnerStatus(Input::post('sgOwnerStatus'));
        $config->setSgOwnerSiret(Input::post('sgOwnerSiret'));
        $config->setSgOwnerStreet(Input::post('sgOwnerStreet'));
        $config->setSgOwnerPostal(Input::post('sgOwnerPostal'));
        $config->setSgOwnerCity(Input::post('sgOwnerCity'));
        $config->setSgOwnerRegion(Input::post('sgOwnerRegion'));
        $config->setSgOwnerCountry(Input::post('sgOwnerCountry'));
        $config->setSgOwnerEmail(Input::post('sgOwnerEmail'));
        $config->setSgOwnerDomain(Input::post('sgOwnerDomain'));
        $config->setSgOwnerHost(Input::post('sgOwnerHost'));
        $config->setSgOwnerDpoName(Input::post('sgOwnerDpoName'));
        $config->setSgOwnerDpoEmail(Input::post('sgOwnerDpoEmail'));

        $this->configurationManager->save($config);
    }
}
