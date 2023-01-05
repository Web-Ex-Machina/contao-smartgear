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
use WEM\SmartgearBundle\Classes\StringUtil;
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

    /**
     * Manage the breadcrumb's behaviour.
     */
    protected function manageBreadcrumbBehaviour(PageModel $pageModel, LayoutModel $layout, PageRegular $pageRegular): void
    {
        $renderStack = RenderStack::getInstance();
        $mainBreadcrumItems = $renderStack->getBreadcrumbItems('main');

        if (empty($mainBreadcrumItems)
        || 0 !== $mainBreadcrumItems[0]['index_in_column']
        || false === (bool) $mainBreadcrumItems[0]['model']->wem_sg_breadcrumb_auto_placement
        ) {
            return;
        }

        $breadcrumb = $mainBreadcrumItems[0];
        $mainItems = $renderStack->getItems('main');
        $firstItemAfterBreadcrumb = $mainItems[1];
        $breadcrumbItemsToPlaceAfterContentElements = null !== $breadcrumb['model']->wem_sg_breadcrumb_auto_placement_after_content_elements
        ? StringUtil::deserialize($breadcrumb['model']->wem_sg_breadcrumb_auto_placement_after_content_elements)
        : [];
        $breadcrumbItemsToPlaceAfterModules = null !== $breadcrumb['model']->wem_sg_breadcrumb_auto_placement_after_modules
        ? StringUtil::deserialize($breadcrumb['model']->wem_sg_breadcrumb_auto_placement_after_modules)
        : [];

        if (\in_array($firstItemAfterBreadcrumb['model']->type, $breadcrumbItemsToPlaceAfterContentElements, true)
        || \in_array($firstItemAfterBreadcrumb['model']->type, $breadcrumbItemsToPlaceAfterModules, true)
        ) {
            $pageRegular->Template->main = str_replace($breadcrumb['buffer'], '', $pageRegular->Template->main);
            $pageRegular->Template->main = str_replace($firstItemAfterBreadcrumb['buffer'], $firstItemAfterBreadcrumb['buffer'].$breadcrumb['buffer'], $pageRegular->Template->main);
        }
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
        $objItem->url = System::getReferer();
        $objItem->ip = Environment::get('ip');
        $objItem->createdAt = time();
        $objItem->tstamp = time();
        $objItem->save();
    }
}
