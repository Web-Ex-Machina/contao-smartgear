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

use Contao\Environment;
use Contao\Input;
use Contao\LayoutModel;
use Contao\PageModel;
use Contao\PageRegular;
use Contao\System;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\CustomLanguageFileLoader;
use WEM\SmartgearBundle\Classes\RenderStack;
use WEM\SmartgearBundle\Classes\ScopeMatcher;
use WEM\SmartgearBundle\Classes\StringUtil;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Exceptions\File\NotFound;
use WEM\SmartgearBundle\Model\PageVisit;

class GeneratePageListener
{
    /** @var CoreConfigurationManager */
    protected $configurationManager;

    /** @var ScopeMatcher */
    protected $scopeMatcher;

    /** @var CustomLanguageFileLoader */
    protected $customLanguageFileLoader;

    public function __construct(
        CoreConfigurationManager $configurationManager,
        ScopeMatcher $scopeMatcher,
        CustomLanguageFileLoader $customLanguageFileLoader
    ) {
        $this->configurationManager = $configurationManager;
        $this->scopeMatcher = $scopeMatcher;
        $this->customLanguageFileLoader = $customLanguageFileLoader;
    }

    public function __invoke(PageModel $pageModel, LayoutModel $layout, PageRegular $pageRegular): void
    {
        $this->loadCustomLanguageFile($pageModel);
        if ($this->scopeMatcher->isFrontend()) {
            $this->registerPageVisit($pageModel);
            $this->manageBreadcrumbBehaviour($pageModel, $layout, $pageRegular);
        }
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
        $firstItemAfterBreadcrumb = $mainItems[1] ?? null;
        $breadcrumbItemsToPlaceAfterContentElements = null !== $breadcrumb['model']->wem_sg_breadcrumb_auto_placement_after_content_elements
        ? StringUtil::deserialize($breadcrumb['model']->wem_sg_breadcrumb_auto_placement_after_content_elements)
        : [];
        $breadcrumbItemsToPlaceAfterModules = null !== $breadcrumb['model']->wem_sg_breadcrumb_auto_placement_after_modules
        ? StringUtil::deserialize($breadcrumb['model']->wem_sg_breadcrumb_auto_placement_after_modules)
        : [];

        if (
            $firstItemAfterBreadcrumb
            && (
                \in_array($firstItemAfterBreadcrumb['model']->type, $breadcrumbItemsToPlaceAfterContentElements, true)
                || \in_array($firstItemAfterBreadcrumb['model']->type, $breadcrumbItemsToPlaceAfterModules, true)
            )
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
        } catch (NotFound) {
            return;
        }

        $hash = Util::getCookieVisitorUniqIdHash();
        if (null === $hash) {
            $hash = Util::buildCookieVisitorUniqIdHash();
            Util::setCookieVisitorUniqIdHash($hash);
        }

        if (!$this->scopeMatcher->isFrontend()
        || Environment::get('isAjaxRequest')
        || Input::get('TL_AJAX')
        || Input::post('TL_AJAX')
        ) {
            return;
        }

        $url = Environment::get('url');
        $uri = Environment::get('uri');
        $referer = System::getReferer();

        $uriWithoutUrl = str_replace($url, '', $uri);

        $extension = 'html';
        if ($lastdot = strrpos($uriWithoutUrl, '.')) {
            $extension = substr($uriWithoutUrl, $lastdot + 1);
        }

        if ('html' !== strtolower($extension)) {
            return;
        }

        // add a new visit
        $objItem = new PageVisit();
        $objItem->pid = $pageModel->id;
        $objItem->page_url = $uri;
        $objItem->page_url_base = str_contains($uri, '?') ? substr($uri, 0, strpos($uri, '?')) : $uri;
        $objItem->referer = $referer;
        $objItem->referer_base = str_contains($referer, '?') ? substr($referer, 0, strpos($referer, '?')) : $referer;
        $objItem->hash = $hash;
        $objItem->createdAt = time();
        $objItem->tstamp = time();
        $objItem->save();
    }

    /**
     * Load custom language file.
     *
     * @param PageModel $pageModel The current page model
     */
    protected function loadCustomLanguageFile(PageModel $pageModel): void
    {
        $this->customLanguageFileLoader->loadCustomLanguageFile();
    }
}
