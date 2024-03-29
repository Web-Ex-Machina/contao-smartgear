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

class GenerateBreadcrumbListener
{
    /** @var array */
    protected $listeners;

    public function __construct(
        array $listeners
    ) {
        $this->listeners = $listeners;
    }

    public function __invoke(array $items, \Contao\Module $module): array
    {
        $arrSourceItems = $items;
        try {
            // Determine if we are at the root of the website
            global $objPage;
            $objHomePage = \Contao\PageModel::findFirstPublishedRegularByPid($objPage->rootId);

            // If we are, remove breadcrumb
            if ($objHomePage->id === $objPage->id) {
                return [];
            }

            foreach ($this->listeners as $listener) {
                $items = $listener->__invoke($items, $module);
            }

            return $items;
        } catch (\Exception $e) {
            return $arrSourceItems;
        }
    }
}
