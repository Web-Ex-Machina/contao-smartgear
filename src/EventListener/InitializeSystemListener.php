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

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\TemplateLoader;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\TemplateFinder;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Exceptions\File\NotFound;
use WEM\UtilsBundle\Classes\ScopeMatcher;

#[AsHook('initializeSystem',null,-1)]
class InitializeSystemListener
{

    public function __construct(
        protected CoreConfigurationManager $configurationManager,
        protected readonly ScopeMatcher $scopeMatcher,
        protected TemplateFinder $templateFinder)
    {
    }

    public function __invoke(): void
    {
        if(!$this->scopeMatcher->isFrontend()) {exit();}

        try {
            /** @var CoreConfig $config */
            $config = $this->configurationManager->load();
        } catch (NotFound) {
            return;
        }

        if (!$config->getSgInstallComplete()) {
            return;
        }

        TemplateLoader::addFiles($this->templateFinder->buildList());
    }
}
