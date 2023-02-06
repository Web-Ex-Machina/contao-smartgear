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

    public function __construct(
        CoreConfigurationManager $configurationManager,
        ScopeMatcher $scopeMatcher
    ) {
        $this->configurationManager = $configurationManager;
        $this->scopeMatcher = $scopeMatcher;
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

        $hash = Util::getCookieVisitorUniqIdHash();
        if (null === $hash) {
            $hash = Util::buildCookieVisitorUniqIdHash();
            Util::setCookieVisitorUniqIdHash($hash);
        }

        if (!$this->scopeMatcher->isFrontend()) {
            return;
        }

        $uri = Environment::get('uri');
        $referer = System::getReferer();

        // add a new visit
        $objItem = new PageVisit();
        $objItem->pid = $pageModel->id;
        $objItem->page_url = $uri;
        $objItem->page_url_base = false !== strpos($uri, '?') ? substr($uri, 0, strpos($uri, '?')) : $uri;
        $objItem->referer = $referer;
        $objItem->referer_base = false !== strpos($referer, '?') ? substr($referer, 0, strpos($referer, '?')) : $referer;
        $objItem->url = System::getReferer();
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
        // check if assets/smartgear/languages/{lang}/custom.json exists
        // if so, include it
        $container = System::getContainer();
        $strLanguage = $container->get('request_stack')->getCurrentRequest()->getLocale();
        $filePath = System::getContainer()->getParameter('kernel.project_dir').'/assets/smartgear/languages/'.$strLanguage.'/custom.json';
        if (file_exists($filePath)) {
            $content = file_get_contents($filePath);
            if (!$content) {
                return;
            }
            $json = json_decode($content, true);
            if (!$json) {
                return;
            }
            $this->JSONFileToLangArray($json);
        }
    }

    protected function JSONFileToLangArray(array $json): void
    {
        foreach ($json as $key => $value) {
            // check if key is 4 chunks long max
            $keys = explode('.', $key);
            switch (\count($keys)) {
                case 1:
                    $GLOBALS['TL_LANG'][$keys[0]] = $value;
                break;
                case 2:
                    $GLOBALS['TL_LANG'][$keys[0]][$keys[1]] = $value;
                break;
                case 3:
                    $GLOBALS['TL_LANG'][$keys[0]][$keys[1]][$keys[2]] = $value;
                break;
                case 4:
                    $GLOBALS['TL_LANG'][$keys[0]][$keys[1]][$keys[2]][$keys[3]] = $value;
                break;
                default:
                break;
            }
        }
    }
}
