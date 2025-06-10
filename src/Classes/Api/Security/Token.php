<?php

/*
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2025 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

namespace WEM\SmartgearBundle\Classes\Api\Security;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\System;

class Token
{
    public function __construct(
        protected ContaoFramework $framework
    ) {
        $this->framework->initialize();
    }

    public function validate(string $token): bool
    {
        return $token === System::getContainer()->get('session')->get('token');
    }

    public function define(): string
    {
        $token = str_replace(['=', '+', '/'], '', base64_encode(hash('sha256', random_bytes(20), true)));
        System::getContainer()->get('session')->set('token', $token);
        return $token;
    }
}
