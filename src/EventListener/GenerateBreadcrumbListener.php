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

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\Module;
use Contao\PageModel;
use WEM\UtilsBundle\Classes\ScopeMatcher;

#[AsHook('generateBreadcrumb',null,-1)]
class GenerateBreadcrumbListener
{
    public function __construct(protected array $listeners, protected readonly ScopeMatcher $scopeMatcher)
    {
    }

    public function __invoke(array $items, Module $module): ?array
    {
        if(!$this->scopeMatcher->isFrontend()) {exit();}

        $arrSourceItems = $items;
        try {
            // Determine if we are at the root of the website
            global $objPage;
            $objHomePage = PageModel::findFirstPublishedRegularByPid($objPage->rootId);

            // If we are, remove breadcrumb
            if ($objHomePage->id === $objPage->id) {
                return [];
            }

            foreach ($this->listeners as $listener) {
                $items = $listener->__invoke($items, $module);
            }

            return $items;
        } catch (\Exception) {
            return $arrSourceItems;
        }
    }
}
