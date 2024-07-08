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

namespace WEM\SmartgearBundle\Classes;

use Symfony\Component\HttpFoundation\RequestStack;
use Contao\CoreBundle\Routing\ScopeMatcher as ScopeMatcherBase;
readonly class ScopeMatcher
{
    /**
     * I use FQDN because PHP doesn't care about the "use".
     *
     * @param RequestStack $requestStack [description]
     * @param ScopeMatcherBase $scopeMatcher [description]
     */
    public function __construct(
        private RequestStack     $requestStack,
        private ScopeMatcherBase $scopeMatcher)
    {
    }

    public function isBackend(): bool
    {
        return $this->scopeMatcher->isBackendRequest($this->requestStack->getCurrentRequest());
    }

    public function isFrontend(): bool
    {
        return $this->scopeMatcher->isFrontendRequest($this->requestStack->getCurrentRequest());
    }
}
