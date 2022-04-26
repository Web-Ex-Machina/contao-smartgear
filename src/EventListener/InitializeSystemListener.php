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

namespace WEM\SmartgearBundle\EventListener;

use WEM\SmartgearBundle\Classes\TemplateFinder;

class InitializeSystemListener
{
    /** @var TemplateFinder */
    protected $templateFinder;

    public function __construct(
        TemplateFinder $templateFinder
    ) {
        $this->templateFinder = $templateFinder;
    }

    public function __invoke(): void
    {
        \Contao\TemplateLoader::addFiles($this->templateFinder->buildList());
    }
}
