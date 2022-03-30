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
        ->removeField('customTpl')
        ->removeField('guests')
        ->removeField('cssID')
        ->removeField('mooStyle')
        ->removeField('mooClasses')
        ->applyToPalette('accordionStart', 'tl_content')
    ;
    PaletteManipulator::create()
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
    //     ->removeField('customTpl')
    //     ->removeField('guests')
    //     ->removeField('cssID')
    //     ->removeField('mooStyle')
    //     ->removeField('mooClasses')
    //     ->applyToPalette('sliderStart', 'tl_content')
    // ;
    // PaletteManipulator::create()
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
    //     ->removeField('customTpl')
    //     ->removeField('guests')
    //     ->removeField('cssID')
    //     ->applyToPalette('player', 'tl_content')
    // ;
}
