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

// use Contao\CoreBundle\Routing\ScopeMatcher as ScopeMatcherBase;
// use Symfony\Component\HttpFoundation\RequestStack;

namespace WEM\SmartgearBundle\Classes;

class ScopeMatcher
{
    private $requestStack;
    private $scopeMatcher;

    // public function __construct(RequestStack $requestStack, ScopeMatcherBase $scopeMatcher)
    public function __construct(
        \Symfony\Component\HttpFoundation\RequestStack $requestStack,
        \Contao\CoreBundle\Routing\ScopeMatcher $scopeMatcher)
    {
        $this->requestStack = $requestStack;
        $this->scopeMatcher = $scopeMatcher;
    }

    public function isBackend()
    {
        return $this->scopeMatcher->isBackendRequest($this->requestStack->getCurrentRequest());
    }

    public function isFrontend()
    {
        return $this->scopeMatcher->isFrontendRequest($this->requestStack->getCurrentRequest());
    }
}
