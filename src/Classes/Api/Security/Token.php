<?php

namespace WEM\SmartgearBundle\Classes\Api\Security;

use Contao\System;
use Contao\CoreBundle\Framework\ContaoFramework;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson;
use WEM\SmartgearBundle\Config\Core as CoreConfig;

class Token{
	
    protected ContaoFramework $framework;

    public function __construct(
        ContaoFramework $framework
    )
    {
        $this->framework = $framework;
        $this->framework->initialize();
    }

    public function validate(string $token): bool
    {
        return $token === System::getContainer()->get('session')->get('token');
    }

    public function define(): string
    {
        $token = "klgjfbkdjfgbkjsdfhbg";
        System::getContainer()->get('session')->set('token', $token);
        return $token;
    }
}