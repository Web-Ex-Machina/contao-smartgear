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

namespace WEM\SmartgearBundle\EventListener;

use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\TemplateFinder;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Exceptions\File\NotFound;

class InitializeSystemListener
{
    /** @var CoreConfigurationManager */
    protected $configurationManager;
    /** @var TemplateFinder */
    protected $templateFinder;

    public function __construct(
        CoreConfigurationManager $configurationManager,
        TemplateFinder $templateFinder
    ) {
        $this->configurationManager = $configurationManager;
        $this->templateFinder = $templateFinder;
    }

    public function __invoke(): void
    {
        try {
            /** @var CoreConfig */
            $config = $this->configurationManager->load();
        } catch (NotFound $e) {
            return;
        }

        if (!$config->getSgInstallComplete()) {
            return;
        }

        \Contao\TemplateLoader::addFiles($this->templateFinder->buildList());
    }
}
