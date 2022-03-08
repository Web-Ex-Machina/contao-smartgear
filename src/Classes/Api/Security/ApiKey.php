<?php

namespace WEM\SmartgearBundle\Classes\Api\Security;

use Contao\CoreBundle\Framework\ContaoFramework;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson;
use WEM\SmartgearBundle\Config\Core as CoreConfig;

class ApiKey{
	
    protected ContaoFramework $framework;
    protected ManagerJson $coreConfigurationManager;

    public function __construct(
        ContaoFramework $framework,
        ManagerJson $coreConfigurationManager
    )
    {
        $this->framework = $framework;
        $this->coreConfigurationManager = $coreConfigurationManager;
        $this->framework->initialize();
    }

    public function validate(string $apiKey): bool
    {
    	/** @var CoreConfig */
        $config = $this->coreConfigurationManager->load();

        return $apiKey === $config->getSgApiKey();
    }
}