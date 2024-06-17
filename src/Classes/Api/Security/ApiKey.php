<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2023 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

namespace WEM\SmartgearBundle\Classes\Api\Security;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Model\Collection;
use Contao\System;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Model\Configuration\Configuration;

class ApiKey
{
    public function __construct(
        protected ContaoFramework $framework,
        protected ManagerJson $coreConfigurationManager
    ) {
        $this->framework->initialize();
    }

    public function validate(string $apiKey): bool
    {
        $configurations = Configuration::findItems();
        if ($configurations instanceof Collection) {
            $encryptionService = System::getContainer()->get('plenta.encryption');
            while ($configurations->next()) {
                if ($apiKey === $encryptionService->decrypt($configurations->api_key)) {
                    System::getContainer()->get('session')->set('configuration_source', 'database');
                    System::getContainer()->get('session')->set('configuration_id', $configurations->id);

                    return true;
                }
            }
        }

        /** @var CoreConfig $config */
        $config = $this->coreConfigurationManager->load();

        if ($apiKey === $config->getSgApiKey()) {
            System::getContainer()->get('session')->set('configuration_source', 'file');
            System::getContainer()->get('session')->set('configuration_id', null);

            return true;
        }

        return false;
    }
}
