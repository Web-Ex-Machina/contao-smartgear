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

namespace WEM\SmartgearBundle\Module;

use Contao\BackendTemplate;
use Contao\FilesModel;
use Contao\Module;
use Contao\ModuleModel;
use Contao\ModuleNavigation;
use Contao\PageModel;
use Contao\System;
use Exception;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

/**
 * Front end module.
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Header extends Module
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'mod_wem_sg_header';

    /**
     * Display a wildcard in the back end.
     *
     * @return string
     */
    public function generate()
    {
        if (TL_MODE === 'BE') {
            /** @var \BackendTemplate|object $objTemplate */
            $objTemplate = new BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### '.utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['wem_sg_header'][0]).' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id='.$this->id;

            return $objTemplate->parse();
        }

        return parent::generate();
    }

    /**
     * Generate the module.
     */
    protected function compile(): void
    {
        return;
        try {
            // Parse the logo
            $objLogo = FilesModel::findByUuid($this->wem_sg_header_logo);
            $container = System::getContainer();
            $picture = $container->get('contao.image.image_factory')->create(TL_ROOT.'/'.$objLogo->path, deserialize($this->wem_sg_header_logo_size));

            // Check if we want the header to be above
            if ($this->wem_sg_header_above) {
                $this->Template->above = true;
            }

            // Check if we want the header to be sticky
            if ($this->wem_sg_header_sticky) {
                $this->Template->sticky = true;
            }

            // Send data to template
            $this->Template->preset = $this->wem_sg_header_preset;
            $this->Template->logo = $picture;
            $this->Template->alt = $this->wem_sg_header_logo_alt;

            // Check if we want to add content
            if ($this->wem_sg_header_content) {
                $this->Template->content = true;
                $this->Template->content_html = $this->wem_sg_header_content_html;
            }

            // Check if we want to use a module as navigation
            switch ($this->wem_sg_navigation) {
                case 'module':
                    $this->Template->nav = sprintf('{{insert_module::%s}}', $this->wem_sg_navigation_module);
                    break;
                default:
                    $objModel = new ModuleModel();
                    $objModel->type = 'navigation';
                    $objModel->levelOffset = 0;
                    $objModel->showLevel = 3;
                    $objModel->navigationTpl = 'nav_default';
                    $objModel->customTpl = 'mod_navigation_sg';
                    $objModel->cssID = [0 => '', 1 => 'headerFW__nav__inline'];
                    $objModule = new ModuleNavigation($objModel);
                    $this->Template->nav = $objModule->generate();
            }

            // Determine if we are at the root of the website
            global $objPage;
            $this->Template->isRoot = true;

            // Check if the current page is the first page of the current root page
            // It not : we need to retrieve the link to the home page
            $objHomePage = PageModel::findFirstPublishedRegularByPid($objPage->rootId);
            if ($objHomePage->id !== $objPage->id) {
                $this->Template->isRoot = false;
                $this->Template->rootHref = ControllerEvent::generateFrontendUrl($objHomePage->row());
            }
        } catch (Exception $e) {
            $this->Template->blnError = true;
            $this->Template->strError = $e->getMessage();
        }
    }
}
