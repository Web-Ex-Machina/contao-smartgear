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

namespace WEM\SmartgearBundle\Api\Smartgear\V1;

use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson;
use WEM\SmartgearBundle\Config\Core as CoreConfig;
use WEM\SmartgearBundle\Classes\Api\Security\ApiKey;
use WEM\SmartgearBundle\Classes\Api\Security\Token;
use WEM\SmartgearBundle\Classes\Util;

class Api
{
    protected ManagerJson $coreConfigurationManager;
    protected ApiKey $securityApiKey;
    protected Token $securityToken;

    public function __construct(
        ManagerJson $coreConfigurationManager,
        ApiKey $securityApiKey,
        Token $securityToken
    )
    {
        $this->coreConfigurationManager = $coreConfigurationManager;
        $this->securityApiKey = $securityApiKey;
        $this->securityToken = $securityToken;
    }

    public function token(): string
    {
        return json_encode(['token'=>$this->securityToken->define()]);
    }

    public function version(): string
    {
        /** @var CoreConfig */
        $config = $this->coreConfigurationManager->load();
        return json_encode([
            'installed'=>$config->getSgVersion(),
            'package'=>Util::getPackageVersion()
        ]);
    }
}
