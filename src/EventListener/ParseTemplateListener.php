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
use Contao\LayoutModel;
use Contao\Template;
use Contao\TemplateLoader;
use Contao\ThemeModel;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\ScopeMatcher;
use WEM\SmartgearBundle\Classes\TemplateFinder;

#[AsHook('parseTemplate',null,-1)]
class ParseTemplateListener
{
    public function __construct(
        protected CoreConfigurationManager $configurationManager,
        protected ScopeMatcher $scopeMatcher,
        protected TemplateFinder $templateFinder)
    {
    }

    public function __invoke(Template $template): void // TODO for futur : Deprecated since Contao 5.0, to be removed in Contao 6; use Twig templates instead
    {
        if ($this->scopeMatcher->isFrontend()) {
            global $objPage;
            if (($objLayout = LayoutModel::findByPk($objPage->layout)) && ($objTheme = ThemeModel::findByPk($objLayout->pid))) {
                TemplateLoader::addFiles($this->templateFinder->buildList($objTheme->templates));
            }
        }
    }
}
