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
use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\Module;
use Contao\ModuleModel;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\RenderStack;
use WEM\UtilsBundle\Classes\ScopeMatcher;

#[AsHook('getFrontendModule',null,-1)]
class GetFrontendModuleListener
{
    public function __construct(protected CoreConfigurationManager $configurationManager, protected readonly ScopeMatcher $scopeMatcher)
    {
    }

    public function __invoke(ModuleModel $model, string $buffer, Module|ContentElement $module): string
    {
        if(!$this->scopeMatcher->isFrontend()) {exit();}

        $renderStack = RenderStack::getInstance();
        $renderStack->add($model, $buffer, $module);

        return $buffer;
    }
}
