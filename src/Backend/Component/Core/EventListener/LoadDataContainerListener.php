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

namespace WEM\SmartgearBundle\Backend\Component\Core\EventListener;

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\ModuleModel;
use Symfony\Component\Security\Core\Security;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\Dca\Manipulator as DCAManipulator;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Exceptions\File\NotFound as FileNotFoundException;
use WEM\SmartgearBundle\Security\SmartgearPermissions;

class LoadDataContainerListener
{
    /** @var Security */
    protected $security;
    /** @var CoreConfigurationManager */
    protected $coreConfigurationManager;
    /** @var DCAManipulator */
    protected $dcaManipulator;
    /** @var string */
    protected $do;

    public function __construct(
        Security $security,
        CoreConfigurationManager $coreConfigurationManager,
        DCAManipulator $dcaManipulator
    ) {
        $this->security = $security;
        $this->coreConfigurationManager = $coreConfigurationManager;
        $this->dcaManipulator = $dcaManipulator;
    }

    public function __invoke(string $table): void
    {
        try {
            /* @var CoreConfig */
            // $config = $this->coreConfigurationManager->load();
            $this->dcaManipulator->setTable($table);
            switch ($table) {
                case 'tl_content':
                    if (!$this->security->isGranted('contao_user.smartgear_permissions', SmartgearPermissions::CORE_EXPERT)
                    ) {
                        // do not display grid_gap settings
                        $this->dcaManipulator->removeFields(['grid_gap']);
                        $this->dcaManipulator->setFieldEvalProperty('cssID', 'tl_class', 'hidden');
                        unset($GLOBALS['TL_DCA']['tl_content']['fields']['size']['eval']['includeBlankOption']); // yep, we have to remove the key to remove the blank option...
                        $this->dcaManipulator->setFieldEvalProperty('size', 'mandatory', 'true');
                        $this->updatePaletteHeadline();
                        $this->updatePaletteText();
                        $this->updatePaletteTable();
                        $this->updatePaletteAccordion();
                        $this->updatePaletteHyperlink();
                        $this->updatePaletteImage();
                        $this->updatePalettePlayer();
                        $this->updatePaletteYoutube();
                        $this->updatePaletteVimeo();
                        $this->updatePaletteDownloads();
                        $this->updatePaletteGallery();
                    }
                    $this->dcaManipulator->addFieldSaveCallback('headline', [\WEM\SmartgearBundle\DataContainer\Content::class, 'cleanHeadline']);
                    $this->dcaManipulator->addFieldSaveCallback('text', [\WEM\SmartgearBundle\DataContainer\Content::class, 'cleanText']);
                    $this->dcaManipulator->setFieldEvalProperty('sortBy', 'tl_class', 'hidden');
                break;
                case 'tl_module':
                    $nbChangeLanguageModules = ModuleModel::countByType('changelanguage');
                    if (0 === (int) $nbChangeLanguageModules) {
                        // do not display lang_selector settings
                        $this->dcaManipulator->removeFields(['wem_sg_header_add_lang_selector', 'wem_sg_header_lang_selector_bg', 'wem_sg_header_lang_selector_module']);
                    }
                break;
                case 'tl_nc_language':
                    // if ($config->getSgInstallComplete()) {
                        $this->dcaManipulator->removeFields(['attachment_templates']);
                    // }
                break;
            }
        } catch (FileNotFoundException $e) {
            //nothing
        }
    }

    public function setDo(string $do): self
    {
        $this->do = $do;

        return $this;
    }

    protected function updatePaletteHeadline(): void
    {
        PaletteManipulator::create()
        ->removeField('customTpl')
        ->applyToPalette('headline', 'tl_content')
    ;
    }

    protected function updatePaletteText(): void
    {
        PaletteManipulator::create()
        ->removeField('headline')
        ->removeField('customTpl')
        ->applyToPalette('text', 'tl_content')
    ;
        PaletteManipulator::create()
        ->removeField('imagemargin')
        ->applyToSubpalette('addImage', 'tl_content')
    ;
    }

    protected function updatePaletteTable(): void
    {
        PaletteManipulator::create()
        ->removeField('headline')
        ->removeField('customTpl')
        ->removeField('sortable')
        ->applyToPalette('table', 'tl_content')
    ;
    }

    protected function updatePaletteAccordion(): void
    {
        PaletteManipulator::create()
        ->removeField('headline')
        ->removeField('customTpl')
        ->removeField('mooStyle')
        ->removeField('mooClasses')
        ->applyToPalette('accordionStart', 'tl_content')
    ;
        PaletteManipulator::create()
        ->removeField('headline')
        ->removeField('customTpl')
        ->removeField('mooStyle')
        ->removeField('mooClasses')
        ->applyToPalette('accordionStop', 'tl_content')
    ;
    }

    protected function updatePaletteHyperlink(): void
    {
        PaletteManipulator::create()
        ->removeField('headline')
        ->removeField('customTpl')
        ->removeField('embed')
        ->removeField('rel')
        ->removeField('useImage')
        ->applyToPalette('hyperlink', 'tl_content')
    ;
    }

    protected function updatePaletteImage(): void
    {
        PaletteManipulator::create()
        ->removeField('headline')
        ->removeField('customTpl')
        ->removeField('imagemargin')
        ->applyToPalette('image', 'tl_content')
    ;
    }

    protected function updatePalettePlayer(): void
    {
        PaletteManipulator::create()
        ->removeField('headline')
        ->removeField('customTpl')
        ->applyToPalette('player', 'tl_content')
    ;
    }

    protected function updatePaletteYoutube(): void
    {
        PaletteManipulator::create()
        ->removeField('headline')
        ->removeField('customTpl')
        ->applyToPalette('youtube', 'tl_content')
    ;
    }

    protected function updatePaletteVimeo(): void
    {
        PaletteManipulator::create()
        ->removeField('headline')
        ->removeField('customTpl')
        ->applyToPalette('vimeo', 'tl_content')
    ;
    }

    protected function updatePaletteDownloads(): void
    {
        PaletteManipulator::create()
        ->removeField('headline')
        ->removeField('customTpl')
        ->applyToPalette('downloads', 'tl_content')
    ;
    }

    protected function updatePaletteGallery(): void
    {
        PaletteManipulator::create()
        ->removeField('headline')
        ->removeField('metaIgnore')
        ->removeField('imagemargin')
        ->removeField('perPage')
        ->removeField('numberOfItems')
        ->applyToPalette('gallery', 'tl_content')
    ;
    }
}
