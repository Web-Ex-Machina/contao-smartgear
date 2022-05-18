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
use Contao\ContentModel;
use Contao\Module;
use Exception;

/**
 * Front end module.
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class GridBuilder extends Module
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'mod_wem_sg_gridbuilder';
    protected $contentElementId;

    /**
     * Display a wildcard in the back end.
     *
     * @return string
     */
    public function generate()
    {
        if (TL_MODE === 'BE') {
            // How can I retrieve all tl_content having this module as PID and render them in their BE tpl ?
            $items = ContentModel::findBy(['pid = ?', 'ptable = ?'], [$this->contentElementId, 'tl_content']);
            /** @var BackendTemplate|object $objTemplate */
            $objTemplate = new BackendTemplate('be_wem_sg_gridbuilder');

            $objTemplate->wildcard = '### '.utf8_strtoupper($GLOBALS['TL_LANG']['MOD']['wem_sg_gridbuilder'][0]).' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->items = $items;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id='.$this->id;
            $objTemplate->contentElement = $this->getCurrentContentElement();

            return $objTemplate->parse();
        }
        $this->loadDataContainer('tl_content');

        return parent::generate();
    }

    /**
     * @param ?int $contentElementId
     */
    public function setContentElementId(?int $contentElementId): self
    {
        $this->contentElementId = $contentElementId;

        return $this;
    }

    /**
     * Generate the module.
     */
    protected function compile(): void
    {
        try {
            $this->Template->contentElement = $this->getCurrentContentElement();
            $this->Template->items = ContentModel::findBy(['pid = ?', 'ptable = ?'], [$this->contentElementId, 'tl_content']);
        } catch (Exception $e) {
            $this->Template->blnError = true;
            $this->Template->strError = $e->getMessage();
        }
    }

    protected function getCurrentContentElement()
    {
        return ContentModel::findById($this->contentElementId);
    }
}
