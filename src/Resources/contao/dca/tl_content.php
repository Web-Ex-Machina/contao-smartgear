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

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\Image;
use Contao\Input;
use Contao\System;

$GLOBALS['TL_DCA']['tl_content']['config']['onload_callback'][] = ['tl_wem_sg_content', 'checkPermission'];
$GLOBALS['TL_DCA']['tl_content']['list']['operations']['delete']['button_callback'] = ['tl_wem_sg_content', 'deleteElement'];

$GLOBALS['TL_DCA']['tl_content']['fields']['customTpl']['options_callback'] = static function (Contao\DataContainer $dc) {
    return WEM\SmartgearBundle\Override\Controller::getTemplateGroup('ce_'.$dc->activeRecord->type.'_', [], 'ce_'.$dc->activeRecord->type);
};
$GLOBALS['TL_DCA']['tl_content']['fields']['customTpl']['eval']['includeBlankOption'] = true;
updatePaletteHeadline();
updatePaletteText();
updatePaletteTable();
updatePaletteAccordion();
updatePaletteSlider();
updatePaletteHyperlink();
updatePaletteImage();
updatePalettePlayer();
function updatePaletteHeadline(): void
{
    PaletteManipulator::create()
        ->removeField('customTpl')
        ->removeField('guests')
        ->removeField('cssID')
        ->applyToPalette('headline', 'tl_content')
    ;
}

function updatePaletteText(): void
{
    PaletteManipulator::create()
        ->removeField('headline')
        ->removeField('customTpl')
        ->removeField('guests')
        ->removeField('cssID')
        ->applyToPalette('text', 'tl_content')
    ;
    PaletteManipulator::create()
        ->removeField('imagemargin')
        ->applyToSubpalette('addImage', 'tl_content')
    ;
}

function updatePaletteTable(): void
{
    PaletteManipulator::create()
        ->removeField('headline')
        ->removeField('customTpl')
        ->removeField('guests')
        ->removeField('cssID')
        ->removeField('sortable')
        ->applyToPalette('table', 'tl_content')
    ;
}

function updatePaletteAccordion(): void
{
    PaletteManipulator::create()
        ->removeField('headline')
        ->removeField('customTpl')
        ->removeField('guests')
        ->removeField('cssID')
        ->removeField('mooStyle')
        ->removeField('mooClasses')
        ->applyToPalette('accordionStart', 'tl_content')
    ;
    PaletteManipulator::create()
        ->removeField('headline')
        ->removeField('customTpl')
        ->removeField('guests')
        ->removeField('cssID')
        ->removeField('mooStyle')
        ->removeField('mooClasses')
        ->applyToPalette('accordionStop', 'tl_content')
    ;
}

function updatePaletteSlider(): void
{
    // PaletteManipulator::create()
        // ->removeField('headline')
    //     ->removeField('customTpl')
    //     ->removeField('guests')
    //     ->removeField('cssID')
    //     ->removeField('mooStyle')
    //     ->removeField('mooClasses')
    //     ->applyToPalette('sliderStart', 'tl_content')
    // ;
    // PaletteManipulator::create()
        // ->removeField('headline')
    //     ->removeField('customTpl')
    //     ->removeField('guests')
    //     ->removeField('cssID')
    //     ->removeField('mooStyle')
    //     ->removeField('mooClasses')
    //     ->applyToPalette('sliderStop', 'tl_content')
    // ;
}

function updatePaletteHyperlink(): void
{
    PaletteManipulator::create()
        ->removeField('headline')
        ->removeField('customTpl')
        ->removeField('guests')
        ->removeField('cssID')
        ->removeField('embed')
        ->removeField('rel')
        ->removeField('useImage')
        ->applyToPalette('hyperlink', 'tl_content')
    ;
}

function updatePaletteImage(): void
{
    PaletteManipulator::create()
        ->removeField('headline')
        ->removeField('customTpl')
        ->removeField('guests')
        ->removeField('cssID')
        ->removeField('imagemargin')
        ->applyToPalette('image', 'tl_content')
    ;
}

function updatePalettePlayer(): void
{
    // PaletteManipulator::create()
        // ->removeField('headline')
    //     ->removeField('customTpl')
    //     ->removeField('guests')
    //     ->removeField('cssID')
    //     ->applyToPalette('player', 'tl_content')
    // ;
}
/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @property News $News
 */
class tl_wem_sg_content extends tl_content
{
    /**
     * Check permissions to edit table tl_content.
     *
     * @throws AccessDeniedException
     */
    public function checkPermission(): void
    {
        parent::checkPermission();

        // Check current action
        switch (Input::get('act')) {
            case 'delete':
                if ($this->isContentUsedBySmartgear((int) Input::get('id'))) {
                    throw new AccessDeniedException('Not enough permissions to '.Input::get('act').' content ID '.Input::get('id').'.');
                }
            break;
        }
    }

    /**
     * Return the delete content button.
     *
     * @param array  $row
     * @param string $href
     * @param string $label
     * @param string $title
     * @param string $icon
     * @param string $attributes
     *
     * @return string
     */
    public function deleteElement($row, $href, $label, $title, $icon, $attributes)
    {
        if ($this->isContentUsedBySmartgear((int) $row['id'])) {
            return Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
        }

        return parent::deleteElement($row, $href, $label, $title, $icon, $attributes);
    }

    /**
     * Check if the content is being used by Smartgear.
     *
     * @param int $id content's ID
     */
    protected function isContentUsedBySmartgear(int $id): bool
    {
        $configManager = System::getContainer()->get('smartgear.config.manager.core');
        try {
            $config = $configManager->load();
            if ($config->getSgInstallComplete()
            && (
                $id === (int) $config->getSgContent404Headline()
                || $id === (int) $config->getSgContent404Sitemap()
                || $id === (int) $config->getSgContentLegalNotice()
                || $id === (int) $config->getSgContentPrivacyPolitics()
                || $id === (int) $config->getSgContentSitemap()
            )
            ) {
                return true;
            }
            $blogConfig = $config->getSgBlog();
            if ($blogConfig->getSgInstallComplete()
            && (
                $id === (int) $blogConfig->getSgContentHeadline()
                || $id === (int) $blogConfig->getSgContentList()
            )
            ) {
                return true;
            }
            $eventsConfig = $config->getSgEvents();
            if ($eventsConfig->getSgInstallComplete()
            && (
                $id === (int) $eventsConfig->getSgContentHeadline()
                || $id === (int) $eventsConfig->getSgContentList()
            )
            ) {
                return true;
            }
        } catch (\Exception $e) {
        }

        return false;
    }
}
