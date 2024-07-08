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

use Contao\ContentElement;
use Contao\ContentModel;
use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\Module;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\RenderStack;
use WEM\UtilsBundle\Classes\ScopeMatcher;

#[AsHook('getContentElement',null,-1)]
class GetContentElementListener
{
    public function __construct(protected CoreConfigurationManager $configurationManager,protected readonly ScopeMatcher $scopeMatcher)
    {
    }

    public function __invoke(ContentModel $contentModel, string $buffer, Module|ContentElement $element): string
    {
        if(!$this->scopeMatcher->isFrontend()) {exit();}

        $renderStack = RenderStack::getInstance();
        $renderStack->add($contentModel, $buffer, $element);

        return $buffer;
    }
}
