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

namespace WEM\SmartgearBundle\EventListener\Security;

use Contao\Controller;
use Contao\Input;
use Contao\System;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\ScopeMatcher;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Exceptions\File\NotFound;
use WEM\SmartgearBundle\Model\Login;

class InteractiveLoginListener
{
    /** @var ConfigurationManager */
    protected $configurationManager;

    /** @var ScopeMatcher */
    protected $scopeMatcher;

    /**
     * Initialize the object.
     */
    public function __construct(
        configurationManager $configurationManager,
        ScopeMatcher $scopeMatcher
    ) {
        $this->configurationManager = $configurationManager;
        $this->scopeMatcher = $scopeMatcher;
    }

    public function __invoke(InteractiveLoginEvent $event): void
    {
        $this->registerBackendLoginInformations();
        $this->redirectToSmargearDashboard();
    }

    protected function registerBackendLoginInformations(): void
    {
        if (!$this->scopeMatcher->isBackend()) {
            return;
        }

        $hash = Util::getCookieVisitorUniqIdHash();
        if (null === $hash) {
            $hash = Util::buildCookieVisitorUniqIdHash();
            Util::setCookieVisitorUniqIdHash($hash);
        }

        // add a new backend login
        $objItem = new Login();
        $objItem->hash = $hash;
        $objItem->context = $this->scopeMatcher->isBackend() ? Login::CONTEXT_BE : Login::CONTEXT_FE;
        $objItem->createdAt = time();
        $objItem->tstamp = time();
        $objItem->save();
    }

    protected function redirectToSmargearDashboard(): void
    {
        if (!$this->scopeMatcher->isBackend()) {
            return;
        }

        try {
            /** @var CoreConfig */
            $config = $this->configurationManager->load();
        } catch (NotFound) {
            return;
        }

        if (!$config->getSgInstallComplete()) {
            return;
        }

        if (null !== Input::get('redirect')) {
            return;
        }

        $url = System::getContainer()->get('router')->generate('contao_backend', ['do' => 'wem_sg_dashboard']);

        Controller::redirect($url);
    }
}
