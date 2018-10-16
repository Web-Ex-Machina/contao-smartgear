<?php

/**
 * SMARTGEAR for Contao Open Source CMS
 *
 * Copyright (c) 2015-2018 Web ex Machina
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */

namespace WEM\SmartGear\Module;

use \Exception;

/**
 * Front end module
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Header extends \Module
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_wem_sg_header';


	/**
	 * Display a wildcard in the back end
	 *
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			/** @var \BackendTemplate|object $objTemplate */
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['wem_sg_header'][0]) . ' ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		return parent::generate();
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{
		try{	
			// Parse the logo
			$objLogo = \FilesModel::findByUuid($this->wem_sg_header_logo);
			$container = \System::getContainer();
			$picture = $container->get('contao.image.image_factory')->create(TL_ROOT . '/' . $objLogo->path, deserialize($this->wem_sg_header_logo_size));

			// Check if we want the header to be above
			if($this->wem_sg_header_above)
				$this->Template->above = true;

			// Check if we want the header to be sticky
			if($this->wem_sg_header_sticky)
				$this->Template->sticky = true;
			
			// Send data to template
			$this->Template->preset = $this->wem_sg_header_preset;
			$this->Template->logo = $picture;
			$this->Template->alt = $this->wem_sg_header_logo_alt;

			// Check if we want to add content
			if($this->wem_sg_header_content){
				$this->Template->content = true;
				$this->Template->content_html = $this->wem_sg_header_content_html;
			}

			// Check if we want to use a module as navigation
			switch($this->wem_sg_navigation){
				case 'module':
					$this->Template->nav = sprintf('{{insert_module::%s}}', $this->wem_sg_navigation_module);
				break;
				default:
					$objModel = new \ModuleModel();
					$objModel->type = "navigation";
					$objModel->levelOffset = 0;
					$objModel->showLevel = 3;
					$objModel->navigationTpl = "nav_default";
					$objModule = new \ModuleNavigation($objModel);
					$this->Template->nav = $objModule->generate();
			}

			// Determine if we are at the root of the website
			global $objPage;
			if($objPage->id != \Frontend::getRootPageFromUrl()->id){
				$this->Template->addLink = true;
				$this->Template->href = \Environment::get('base');
			}
		}
		catch(Exception $e){
			$this->Template->blnError = true;
			$this->Template->strError = $e->getMessage();
		}
	}
}