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

use Contao\Environment;
use Contao\LayoutModel;
use Contao\PageModel;
use Contao\PageRegular;
use Contao\System;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\RenderStack;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Exceptions\File\NotFound;
use WEM\SmartgearBundle\Model\PageVisit;

class GeneratePageListener
{
    /** @var CoreConfigurationManager */
    protected $configurationManager;

    public function __construct(
        CoreConfigurationManager $configurationManager
    ) {
        $this->configurationManager = $configurationManager;
    }

    public function __invoke(PageModel $pageModel, LayoutModel $layout, PageRegular $pageRegular): void
    {
        $this->registerPageVisit($pageModel);
        $this->manageBreadcrumbBehaviour($pageModel, $layout, $pageRegular);
    }

    protected function manageBreadcrumbBehaviour(PageModel $pageModel, LayoutModel $layout, PageRegular $pageRegular): void
    {
        $mainBreadcrumItems = RenderStack::getBreadcrumbItems('main');
        $mainItems = RenderStack::getItems('main');
        dump($mainBreadcrumItems);
        dump($mainItems);
    }

    /**
     * Register the visit into statistics.
     *
     * @param PageModel $pageModel The current page model
     */
    protected function registerPageVisit(PageModel $pageModel): void
    {
        try {
            /** @var CoreConfig */
            $config = $this->configurationManager->load();
        } catch (NotFound $e) {
            return;
        }

        if (\defined('TL_MODE') && TL_MODE !== 'FE') {
            return;
        }

        // add a new visit
        $objItem = new PageVisit();
        $objItem->pid = $pageModel->id;
        $objItem->page_url = Environment::get('uri');
        $objItem->referer = System::getReferer();
        $objItem->createdAt = time();
        $objItem->tstamp = time();
        $objItem->save();
    }
}
