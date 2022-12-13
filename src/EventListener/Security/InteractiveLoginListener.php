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
use Contao\System;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Exceptions\File\NotFound;

class InteractiveLoginListener
{
    /**
     * Initialize the object.
     */
    public function __construct(
        configurationManager $configurationManager
    ) {
        $this->configurationManager = $configurationManager;
    }

    public function __invoke(InteractiveLoginEvent $event): void
    {
        try {
            $config = $this->configurationManager->load();
        } catch (NotFound $e) {
            return;
        }

        if (!$config->getSgInstallComplete()) {
            return;
        }

        $url = System::getContainer()->get('router')->generate('contao_backend', ['do' => 'wem_sg_dashboard']);

        Controller::redirect($url);
    }
}
