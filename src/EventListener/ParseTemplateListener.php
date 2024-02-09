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

use Contao\LayoutModel;
use Contao\Template;
use Contao\ThemeModel;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\ScopeMatcher;
use WEM\SmartgearBundle\Classes\TemplateFinder;

class ParseTemplateListener
{
    /** @var CoreConfigurationManager */
    protected $configurationManager;

    /** @var ScopeMatcher */
    protected $scopeMatcher;

    /** @var TemplateFinder */
    protected $templateFinder;

    public function __construct(
        CoreConfigurationManager $configurationManager,
        ScopeMatcher $scopeMatcher,
        TemplateFinder $templateFinder
    ) {
        $this->configurationManager = $configurationManager;
        $this->scopeMatcher = $scopeMatcher;
        $this->templateFinder = $templateFinder;
    }

    public function __invoke(Template $template): void
    {
        if ($this->scopeMatcher->isFrontend()) {
            global $objPage;
            if ($objLayout = LayoutModel::findByPk($objPage->layout)) {
                if ($objTheme = ThemeModel::findByPk($objLayout->pid)) {
                    \Contao\TemplateLoader::addFiles($this->templateFinder->buildList($objTheme->templates));
                }
            }
        }
    }
}
