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

namespace WEM\SmartgearBundle\Migrations\V1_0_0\M202203231730;

use Doctrine\DBAL\Connection;
use Oveleon\ContaoComponentStyleManager\StyleManagerArchiveModel;
use Oveleon\ContaoComponentStyleManager\StyleManagerModel;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\Migration\Result;
use WEM\SmartgearBundle\Classes\Version\Comparator as VersionComparator;
use WEM\SmartgearBundle\Config\Manager\FramwayTheme as ConfigurationThemeManager;
use WEM\SmartgearBundle\Migrations\V1_0_0\MigrationAbstract;

class Migration extends MigrationAbstract
{
    protected static $name = 'Configures CSS classes';
    protected static $description = 'Configures CSS classes available for contents';
    protected static $version = '1.0.0';
    protected static $translation_key = 'WEMSG.MIGRATIONS.V1_0_0_M202203231730';
    /** @var ConfigurationThemeManager */
    protected $configurationThemeManager;

    protected static $elements = [
        'margin' => ['headline', 'text', 'table', 'rsce_listIcons', 'rsce_quote', 'accordionStart', 'accordionStop', 'accordionSingle', 'sliderStart', 'sliderStop', 'hyperlink', 'image', 'player', 'youtube', 'vimeo', 'downloads', 'rsce_timeline', 'grid-start', 'grid-stop', 'rsce_accordionFW', 'rsce_counterFW', 'rsce_gridGallery', 'rsce_heroFWStart', 'rsce_heroFWStop', 'rsce_priceCards', 'rsce_sliderFW', 'rsce_tabs', 'rsce_testimonials', 'rsce_notations'],
        'button' => ['headline', 'hyperlink'],
        // 'button' => ['hyperlink'],
        'background' => ['headline', 'text', 'rsce_quote'],
        'separator' => ['headline'],
        'table' => ['headline', 'table'],
        // 'table' => ['table'],
        'accordion' => ['headline', 'accordionStart', 'accordionStop', 'rsce_accordionFW'],
        // 'accordion' => ['accordionStart', 'accordionStop', 'rsce_accordionFW'],
        'slider' => ['headline', 'sliderStart', 'sliderStop', 'rsce_sliderFW', 'rsce_testimonials'],
        // 'slider' => ['sliderStart', 'sliderStop', 'rsce_sliderFW', 'rsce_testimonials'],
        'image' => ['headline', 'image'],
        // 'image' => ['image'],
        'hero' => ['headline', 'rsce_heroFWStart', 'rsce_heroFWStop'],
        // 'hero' => ['rsce_heroFWStart', 'rsce_heroFWStop'],
        'grid' => ['headline', 'rsce_gridGallery'],
        // 'grid' => ['rsce_gridGallery'],
        'priceCards' => ['headline', 'rsce_priceCards'],
        // 'priceCards' => ['rsce_priceCards'],
    ];

    public function __construct(
        Connection $connection,
        TranslatorInterface $translator,
        CoreConfigurationManager $coreConfigurationManager,
        VersionComparator $versionComparator,
        ConfigurationThemeManager $configurationThemeManager
    ) {
        parent::__construct($connection, $translator, $coreConfigurationManager, $versionComparator);
        $this->configurationThemeManager = $configurationThemeManager;
    }

    public function shouldRun(): Result
    {
        $result = parent::shouldRun();

        if (Result::STATUS_SHOULD_RUN !== $result->getStatus()) {
            return $result;
        }
        $schemaManager = $this->connection->getSchemaManager();
        if (!$schemaManager->tablesExist(['tl_style_manager'])) {
            $result
                ->setStatus(Result::STATUS_FAIL)
                ->addLog($this->translator->trans($this->buildTranslationKey('shouldRunStyleManagerPackageAbsent'), [], 'contao_default'))
            ;

            return $result;
        }

        $objArchiveBackground = StyleManagerArchiveModel::findByIdentifier('fwbackground');
        $objArchiveButton = StyleManagerArchiveModel::findByIdentifier('fwbutton');
        $objArchiveSeparator = StyleManagerArchiveModel::findByIdentifier('fwseparator');
        $objArchiveMargin = StyleManagerArchiveModel::findByIdentifier('fwmargin');
        $objArchiveTable = StyleManagerArchiveModel::findByIdentifier('fwtable');
        $objArchiveImage = StyleManagerArchiveModel::findByIdentifier('fwimage');
        $objArchiveImageRatio = StyleManagerArchiveModel::findByIdentifier('fwimageratio');
        $objArchiveSlider = StyleManagerArchiveModel::findByIdentifier('fwslider');
        $objArchiveSliderNav = StyleManagerArchiveModel::findByIdentifier('fwslidernav');
        $objArchiveSliderImg = StyleManagerArchiveModel::findByIdentifier('fwsliderimg');
        $objArchiveSliderContent = StyleManagerArchiveModel::findByIdentifier('fwslidercontent');
        $objArchiveSliderTitle = StyleManagerArchiveModel::findByIdentifier('fwslidertitle');

        $objArchiveHero = StyleManagerArchiveModel::findByIdentifier('fwhero');
        $objArchiveHeroImg = StyleManagerArchiveModel::findByIdentifier('fwheroimg');
        $objArchiveHeroContent = StyleManagerArchiveModel::findByIdentifier('fwherocontent');
        $objArchiveHeroTitle = StyleManagerArchiveModel::findByIdentifier('fwherotitle');

        $objArchiveGrid = StyleManagerArchiveModel::findByIdentifier('fwgrid');
        $objArchiveGridXL = StyleManagerArchiveModel::findByIdentifier('fwgridxl');
        $objArchiveGridLG = StyleManagerArchiveModel::findByIdentifier('fwgridlg');
        $objArchiveGridMD = StyleManagerArchiveModel::findByIdentifier('fwgridmd');
        $objArchiveGridSM = StyleManagerArchiveModel::findByIdentifier('fwgridsm');
        $objArchiveGridXS = StyleManagerArchiveModel::findByIdentifier('fwgridxs');
        $objArchiveGridXXS = StyleManagerArchiveModel::findByIdentifier('fwgridxxs');

        $objArchiveGridItem = StyleManagerArchiveModel::findByIdentifier('fwgriditem');
        $objArchiveGridItemXL = StyleManagerArchiveModel::findByIdentifier('fwgriditemxl');
        $objArchiveGridItemLG = StyleManagerArchiveModel::findByIdentifier('fwgriditemlg');
        $objArchiveGridItemMD = StyleManagerArchiveModel::findByIdentifier('fwgriditemmd');
        $objArchiveGridItemSM = StyleManagerArchiveModel::findByIdentifier('fwgriditemsm');
        $objArchiveGridItemXS = StyleManagerArchiveModel::findByIdentifier('fwgriditemxs');
        $objArchiveGridItemXXS = StyleManagerArchiveModel::findByIdentifier('fwgriditemxxs');

        $objArchivePriceCard = StyleManagerArchiveModel::findByIdentifier('fwpricecard');

        if (null === $objArchiveBackground
        && null !== $objArchiveButton
        && null !== $objArchiveSeparator
        && null !== $objArchiveMargin
        && null !== $objArchiveTable
        && null !== $objArchiveImage
        && null !== $objArchiveImageRatio
        && null !== $objArchiveSlider
        && null !== $objArchiveSliderNav
        && null !== $objArchiveSliderImg
        && null !== $objArchiveSliderContent
        && null !== $objArchiveSliderTitle
        && null !== $objArchiveHero
        && null !== $objArchiveHeroImg
        && null !== $objArchiveHeroContent
        && null !== $objArchiveHeroTitle
        && null !== $objArchiveGrid
        && null !== $objArchiveGridXL
        && null !== $objArchiveGridLG
        && null !== $objArchiveGridMD
        && null !== $objArchiveGridSM
        && null !== $objArchiveGridXS
        && null !== $objArchiveGridXXS
        && null !== $objArchiveGridItem
        && null !== $objArchiveGridItemXL
        && null !== $objArchiveGridItemLG
        && null !== $objArchiveGridItemMD
        && null !== $objArchiveGridItemSM
        && null !== $objArchiveGridItemXS
        && null !== $objArchiveGridItemXXS
        && null !== $objArchivePriceCard
        ) {
            if (null !== StyleManagerModel::findByAliasAndPid('fwbackgroundcolor', $objArchiveBackground->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwbuttonsize', $objArchiveButton->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwbuttonbackground', $objArchiveButton->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwbuttonborder', $objArchiveButton->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwseparatortop', $objArchiveSeparator->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwseparatorbottom', $objArchiveSeparator->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwseparatorleft', $objArchiveSeparator->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwseparatorright', $objArchiveSeparator->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwmargintop', $objArchiveMargin->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwmarginbottom', $objArchiveMargin->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwmarginleft', $objArchiveMargin->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwmarginright', $objArchiveMargin->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwtablesm', $objArchiveTable->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwtableborder', $objArchiveTable->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwtablestriped', $objArchiveTable->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwpricecardft', $objArchivePriceCard->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwpricecardbg', $objArchivePriceCard->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwpricecardcontent', $objArchivePriceCard->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwpricecardmain', $objArchivePriceCard->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgriditemcolsspan', $objArchiveGridItem->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgriditemrowsspan', $objArchiveGridItem->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgriditemcolsspanxl', $objArchiveGridItemXL->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgriditemrowsspanxl', $objArchiveGridItemXL->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgriditemcolsspanlg', $objArchiveGridItemLG->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgriditemrowsspanlg', $objArchiveGridItemLG->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgriditemcolsspanmd', $objArchiveGridItemMD->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgriditemrowsspanmd', $objArchiveGridItemMD->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgriditemcolsspansm', $objArchiveGridItemSM->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgriditemrowsspansm', $objArchiveGridItemSM->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgriditemcolsspanxs', $objArchiveGridItemXS->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgriditemrowsspanxs', $objArchiveGridItemXS->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgriditemcolsspanxxs', $objArchiveGridItemXXS->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgriditemrowsspanxxs', $objArchiveGridItemXXS->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridgap', $objArchiveGrid->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridcols', $objArchiveGrid->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridrows', $objArchiveGrid->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridcolsxl', $objArchiveGridXL->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridrowsxl', $objArchiveGridXL->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridcolslg', $objArchiveGridLG->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridrowslg', $objArchiveGridLG->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridcolsmd', $objArchiveGridMD->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridrowsmd', $objArchiveGridMD->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridcolssm', $objArchiveGridSM->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridrowssm', $objArchiveGridSM->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridcolsxs', $objArchiveGridXS->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridrowsxs', $objArchiveGridXS->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridcolsxxs', $objArchiveGridXXS->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridrowsxxs', $objArchiveGridXXS->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwheroimgvertical', $objArchiveHeroImg->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwheroimghorizontal', $objArchiveHeroImg->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwherofigureopacity', $objArchiveHeroImg->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwherocontentvertical', $objArchiveHeroContent->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwherocontenthorizontal', $objArchiveHeroContent->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwherotitle', $objArchiveHeroTitle->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwheroft', $objArchiveHeroTitle->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwherocontentbg', $objArchiveHeroContent->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwherocontentbgopacity', $objArchiveHeroContent->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwherowfull', $objArchiveHero->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwheroheightcontent', $objArchiveHeroContent->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwherowidthcontent', $objArchiveHeroContent->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwslidernav', $objArchiveSliderNav->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwslidernavvertical', $objArchiveSliderNav->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwslidernavhorizontal', $objArchiveSliderNav->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwsliderimgvertical', $objArchiveSliderImg->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwsliderimghorizontal', $objArchiveSliderImg->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwsliderimgopacity', $objArchiveSliderImg->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwslidercontentvertical', $objArchiveSliderContent->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwslidercontenthorizontal', $objArchiveSliderContent->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwslidertitle', $objArchiveSliderTitle->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwsliderft', $objArchiveSliderTitle->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwslidercontentbg', $objArchiveSliderContent->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwslidercontentbgopacity', $objArchiveSliderContent->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwsliderwfull', $objArchiveSlider->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwimageratio', $objArchiveImageRatio->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwimagezoom', $objArchiveImage->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwimagefade', $objArchiveImage->id)
            ) {
                $result
                ->setStatus(Result::STATUS_SKIPPED)
                ->addLog($this->translator->trans($this->buildTranslationKey('shouldRunCSSClassesAlreadyInDb'), [], 'contao_default'))
                ;

                return $result;
            }
        }
        $result
            ->addLog($this->translator->trans('WEMSG.MIGRATIONS.shouldBeRun', [], 'contao_default'))
        ;

        return $result;
    }

    public function do(): Result
    {
        $result = $this->shouldRun();
        if (Result::STATUS_SHOULD_RUN !== $result->getStatus()) {
            return $result;
        }
        try {
            $this->manageMargins();
            $result->addLog($this->translator->trans($this->buildTranslationKey('doAddCSSMargins'), [], 'contao_default'));
            $this->manageSeparators();
            $result->addLog($this->translator->trans($this->buildTranslationKey('doAddCSSSeparators'), [], 'contao_default'));
            $this->manageButtons();
            $result->addLog($this->translator->trans($this->buildTranslationKey('doAddCSSButtons'), [], 'contao_default'));
            $this->manageBackgrounds();
            $result->addLog($this->translator->trans($this->buildTranslationKey('doAddCSSBackgrounds'), [], 'contao_default'));
            $this->manageTables();
            $result->addLog($this->translator->trans($this->buildTranslationKey('doAddCSSTables'), [], 'contao_default'));
            $this->manageImages();
            $result->addLog($this->translator->trans($this->buildTranslationKey('doAddCSSImages'), [], 'contao_default'));
            $this->manageSliders();
            $result->addLog($this->translator->trans($this->buildTranslationKey('doAddCSSSliders'), [], 'contao_default'));
            $this->manageHero();
            $result->addLog($this->translator->trans($this->buildTranslationKey('doAddCSSHero'), [], 'contao_default'));
            $this->manageGrids();
            $result->addLog($this->translator->trans($this->buildTranslationKey('doAddCSSGrids'), [], 'contao_default'));
            $this->manageGridItems();
            $result->addLog($this->translator->trans($this->buildTranslationKey('doAddCSSGridItems'), [], 'contao_default'));
            $this->managePriceCards();
            $result->addLog($this->translator->trans($this->buildTranslationKey('doAddCSSPriceCards'), [], 'contao_default'));
        } catch (\Exception $e) {
            $result
                ->setStatus(Result::STATUS_FAIL)
                ->addLog($e->getMessage())
            ;
        }

        return $result;
    }

    protected function managePriceCards(): void
    {
        $contentElements = self::$elements['priceCards'];
        // Price card
        $objArchive = $this->fillObjArchive('fwpricecard', 'WEMSG.STYLEMANAGER.fwpricecard.tabTitle', 'FramwayPriceCard');
        $objArchive->save();

        // Price card - text color
        $cssClasses = [];
        $colors = $this->configurationThemeManager->load()->getColors();
        foreach ($colors as $name => $hexa) {
            $cssClasses[] = [
                'key' => 'ft-'.$name,
                'value' => sprintf('WEMSG.STYLEMANAGER.fwpricecardft.colorLabel (WEMSG.FRAMWAY.COLORS.%s)', $name),
            ];
        }
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwpricecardft', 'WEMSG.STYLEMANAGER.fwpricecardft.title', 'WEMSG.STYLEMANAGER.fwpricecardft.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Price card - bg color
        $cssClasses = [];
        $colors = $this->configurationThemeManager->load()->getColors();
        foreach ($colors as $name => $hexa) {
            $cssClasses[] = [
                'key' => 'bg--'.$name,
                'value' => sprintf('WEMSG.STYLEMANAGER.fwpricecardbg.colorLabel (WEMSG.FRAMWAY.COLORS.%s)', $name),
            ];
        }
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwpricecardbg', 'WEMSG.STYLEMANAGER.fwpricecardbg.title', 'WEMSG.STYLEMANAGER.fwpricecardbg.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Price card - content color
        $cssClasses = [];
        $colors = $this->configurationThemeManager->load()->getColors();
        foreach ($colors as $name => $hexa) {
            $cssClasses[] = [
                'key' => 'content--'.$name,
                'value' => sprintf('WEMSG.STYLEMANAGER.fwpricecardcontent.colorLabel (WEMSG.FRAMWAY.COLORS.%s)', $name),
            ];
        }
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwpricecardcontent', 'WEMSG.STYLEMANAGER.fwpricecardcontent.title', 'WEMSG.STYLEMANAGER.fwpricecardcontent.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Price card - main
        $cssClasses = [
            ['key' => 'main', 'value' => 'WEMSG.STYLEMANAGER.fwpricecardmain.label'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwpricecardmain', 'WEMSG.STYLEMANAGER.fwpricecardmain.title', 'WEMSG.STYLEMANAGER.fwpricecardmain.description', $contentElements, $cssClasses);
        $objStyle->save();
    }

    protected function manageGridItems(): void
    {
        $contentElements = self::$elements['grid'];
        // Grid Items
        $objArchive = $this->fillObjArchive('fwgriditem', 'WEMSG.STYLEMANAGER.fwgriditem.tabTitle', 'FramwayGridItem');
        $objArchive->save();
        $objArchiveXL = $this->fillObjArchive('fwgriditemxl', 'WEMSG.STYLEMANAGER.fwgriditemxl.tabTitle', 'FramwayGridItem');
        $objArchiveXL->save();
        $objArchiveLG = $this->fillObjArchive('fwgriditemlg', 'WEMSG.STYLEMANAGER.fwgriditemlg.tabTitle', 'FramwayGridItem');
        $objArchiveLG->save();
        $objArchiveMD = $this->fillObjArchive('fwgriditemmd', 'WEMSG.STYLEMANAGER.fwgriditemmd.tabTitle', 'FramwayGridItem');
        $objArchiveMD->save();
        $objArchiveSM = $this->fillObjArchive('fwgriditemsm', 'WEMSG.STYLEMANAGER.fwgriditemsm.tabTitle', 'FramwayGridItem');
        $objArchiveSM->save();
        $objArchiveXS = $this->fillObjArchive('fwgriditemxs', 'WEMSG.STYLEMANAGER.fwgriditemxs.tabTitle', 'FramwayGridItem');
        $objArchiveXS->save();
        $objArchiveXXS = $this->fillObjArchive('fwgriditemxxs', 'WEMSG.STYLEMANAGER.fwgriditemxxs.tabTitle', 'FramwayGridItem');
        $objArchiveXXS->save();

        // Grid item - cols
        $cssClasses = [
            ['key' => 'cols-span-1', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspan.1Label'],
            ['key' => 'cols-span-2', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspan.2Label'],
            ['key' => 'cols-span-3', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspan.3Label'],
            ['key' => 'cols-span-4', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspan.4Label'],
            ['key' => 'cols-span-5', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspan.5Label'],
            ['key' => 'cols-span-6', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspan.6Label'],
            ['key' => 'cols-span-7', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspan.7Label'],
            ['key' => 'cols-span-8', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspan.8Label'],
            ['key' => 'cols-span-9', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspan.9Label'],
            ['key' => 'cols-span-10', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspan.10Label'],
            ['key' => 'cols-span-11', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspan.11Label'],
            ['key' => 'cols-span-12', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspan.12Label'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwgriditemcolsspan', 'WEMSG.STYLEMANAGER.fwgriditemcolsspan.title', 'WEMSG.STYLEMANAGER.fwgriditemcolsspan.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Grid item - rows
        $cssClasses = [
            ['key' => 'rows-span-1', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspan.1Label'],
            ['key' => 'rows-span-2', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspan.2Label'],
            ['key' => 'rows-span-3', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspan.3Label'],
            ['key' => 'rows-span-4', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspan.4Label'],
            ['key' => 'rows-span-5', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspan.5Label'],
            ['key' => 'rows-span-6', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspan.6Label'],
            ['key' => 'rows-span-7', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspan.7Label'],
            ['key' => 'rows-span-8', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspan.8Label'],
            ['key' => 'rows-span-9', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspan.9Label'],
            ['key' => 'rows-span-10', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspan.10Label'],
            ['key' => 'rows-span-11', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspan.11Label'],
            ['key' => 'rows-span-12', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspan.12Label'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwgriditemrowsspan', 'WEMSG.STYLEMANAGER.fwgriditemrowsspan.title', 'WEMSG.STYLEMANAGER.fwgriditemrowsspan.description', $contentElements, $cssClasses);
        $objStyle->save();

        // Grid item - colsspanxl
        $cssClasses = [
            ['key' => 'cols-span-xl-1', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxl.1Label'],
            ['key' => 'cols-span-xl-2', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxl.2Label'],
            ['key' => 'cols-span-xl-3', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxl.3Label'],
            ['key' => 'cols-span-xl-4', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxl.4Label'],
            ['key' => 'cols-span-xl-5', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxl.5Label'],
            ['key' => 'cols-span-xl-6', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxl.6Label'],
            ['key' => 'cols-span-xl-7', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxl.7Label'],
            ['key' => 'cols-span-xl-8', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxl.8Label'],
            ['key' => 'cols-span-xl-9', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxl.9Label'],
            ['key' => 'cols-span-xl-10', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxl.10Label'],
            ['key' => 'cols-span-xl-11', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxl.11Label'],
            ['key' => 'cols-span-xl-12', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxl.12Label'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveXL->id, 'fwgriditemcolsspanxl', 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxl.title', 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxl.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Grid item - rowsspanxl
        $cssClasses = [
            ['key' => 'rows-span-xl-1', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxl.1Label'],
            ['key' => 'rows-span-xl-2', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxl.2Label'],
            ['key' => 'rows-span-xl-3', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxl.3Label'],
            ['key' => 'rows-span-xl-4', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxl.4Label'],
            ['key' => 'rows-span-xl-5', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxl.5Label'],
            ['key' => 'rows-span-xl-6', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxl.6Label'],
            ['key' => 'rows-span-xl-7', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxl.7Label'],
            ['key' => 'rows-span-xl-8', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxl.8Label'],
            ['key' => 'rows-span-xl-9', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxl.9Label'],
            ['key' => 'rows-span-xl-10', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxl.10Label'],
            ['key' => 'rows-span-xl-11', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxl.11Label'],
            ['key' => 'rows-span-xl-12', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxl.12Label'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveXL->id, 'fwgriditemrowsspanxl', 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxl.title', 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxl.description', $contentElements, $cssClasses);
        $objStyle->save();

        // Grid item - colsspanlg
        $cssClasses = [
            ['key' => 'cols-span-lg-1', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanlg.1Label'],
            ['key' => 'cols-span-lg-2', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanlg.2Label'],
            ['key' => 'cols-span-lg-3', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanlg.3Label'],
            ['key' => 'cols-span-lg-4', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanlg.4Label'],
            ['key' => 'cols-span-lg-5', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanlg.5Label'],
            ['key' => 'cols-span-lg-6', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanlg.6Label'],
            ['key' => 'cols-span-lg-7', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanlg.7Label'],
            ['key' => 'cols-span-lg-8', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanlg.8Label'],
            ['key' => 'cols-span-lg-9', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanlg.9Label'],
            ['key' => 'cols-span-lg-10', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanlg.10Label'],
            ['key' => 'cols-span-lg-11', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanlg.11Label'],
            ['key' => 'cols-span-lg-12', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanlg.12Label'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveLG->id, 'fwgriditemcolsspanlg', 'WEMSG.STYLEMANAGER.fwgriditemcolsspanlg.title', 'WEMSG.STYLEMANAGER.fwgriditemcolsspanlg.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Grid item - rowsspanlg
        $cssClasses = [
            ['key' => 'rows-span-lg-1', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanlg.1Label'],
            ['key' => 'rows-span-lg-2', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanlg.2Label'],
            ['key' => 'rows-span-lg-3', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanlg.3Label'],
            ['key' => 'rows-span-lg-4', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanlg.4Label'],
            ['key' => 'rows-span-lg-5', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanlg.5Label'],
            ['key' => 'rows-span-lg-6', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanlg.6Label'],
            ['key' => 'rows-span-lg-7', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanlg.7Label'],
            ['key' => 'rows-span-lg-8', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanlg.8Label'],
            ['key' => 'rows-span-lg-9', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanlg.9Label'],
            ['key' => 'rows-span-lg-10', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanlg.10Label'],
            ['key' => 'rows-span-lg-11', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanlg.11Label'],
            ['key' => 'rows-span-lg-12', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanlg.12Label'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveLG->id, 'fwgriditemrowsspanlg', 'WEMSG.STYLEMANAGER.fwgriditemrowsspanlg.title', 'WEMSG.STYLEMANAGER.fwgriditemrowsspanlg.description', $contentElements, $cssClasses);
        $objStyle->save();

        // Grid item - colsspanmd
        $cssClasses = [
            ['key' => 'cols-span-md-1', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanmd.1Label'],
            ['key' => 'cols-span-md-2', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanmd.2Label'],
            ['key' => 'cols-span-md-3', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanmd.3Label'],
            ['key' => 'cols-span-md-4', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanmd.4Label'],
            ['key' => 'cols-span-md-5', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanmd.5Label'],
            ['key' => 'cols-span-md-6', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanmd.6Label'],
            ['key' => 'cols-span-md-7', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanmd.7Label'],
            ['key' => 'cols-span-md-8', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanmd.8Label'],
            ['key' => 'cols-span-md-9', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanmd.9Label'],
            ['key' => 'cols-span-md-10', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanmd.10Label'],
            ['key' => 'cols-span-md-11', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanmd.11Label'],
            ['key' => 'cols-span-md-12', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanmd.12Label'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveMD->id, 'fwgriditemcolsspanmd', 'WEMSG.STYLEMANAGER.fwgriditemcolsspanmd.title', 'WEMSG.STYLEMANAGER.fwgriditemcolsspanmd.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Grid item - rowsspanmd
        $cssClasses = [
            ['key' => 'rows-span-md-1', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanmd.1Label'],
            ['key' => 'rows-span-md-2', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanmd.2Label'],
            ['key' => 'rows-span-md-3', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanmd.3Label'],
            ['key' => 'rows-span-md-4', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanmd.4Label'],
            ['key' => 'rows-span-md-5', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanmd.5Label'],
            ['key' => 'rows-span-md-6', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanmd.6Label'],
            ['key' => 'rows-span-md-7', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanmd.7Label'],
            ['key' => 'rows-span-md-8', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanmd.8Label'],
            ['key' => 'rows-span-md-9', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanmd.9Label'],
            ['key' => 'rows-span-md-10', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanmd.10Label'],
            ['key' => 'rows-span-md-11', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanmd.11Label'],
            ['key' => 'rows-span-md-12', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanmd.12Label'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveMD->id, 'fwgriditemrowsspanmd', 'WEMSG.STYLEMANAGER.fwgriditemrowsspanmd.title', 'WEMSG.STYLEMANAGER.fwgriditemrowsspanmd.description', $contentElements, $cssClasses);
        $objStyle->save();

        // Grid item - colsspansm
        $cssClasses = [
            ['key' => 'cols-span-sm-1', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspansm.1Label'],
            ['key' => 'cols-span-sm-2', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspansm.2Label'],
            ['key' => 'cols-span-sm-3', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspansm.3Label'],
            ['key' => 'cols-span-sm-4', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspansm.4Label'],
            ['key' => 'cols-span-sm-5', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspansm.5Label'],
            ['key' => 'cols-span-sm-6', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspansm.6Label'],
            ['key' => 'cols-span-sm-7', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspansm.7Label'],
            ['key' => 'cols-span-sm-8', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspansm.8Label'],
            ['key' => 'cols-span-sm-9', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspansm.9Label'],
            ['key' => 'cols-span-sm-10', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspansm.10Label'],
            ['key' => 'cols-span-sm-11', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspansm.11Label'],
            ['key' => 'cols-span-sm-12', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspansm.12Label'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveSM->id, 'fwgriditemcolsspansm', 'WEMSG.STYLEMANAGER.fwgriditemcolsspansm.title', 'WEMSG.STYLEMANAGER.fwgriditemcolsspansm.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Grid item - rowsspansm
        $cssClasses = [
            ['key' => 'rows-span-sm-1', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspansm.1Label'],
            ['key' => 'rows-span-sm-2', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspansm.2Label'],
            ['key' => 'rows-span-sm-3', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspansm.3Label'],
            ['key' => 'rows-span-sm-4', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspansm.4Label'],
            ['key' => 'rows-span-sm-5', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspansm.5Label'],
            ['key' => 'rows-span-sm-6', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspansm.6Label'],
            ['key' => 'rows-span-sm-7', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspansm.7Label'],
            ['key' => 'rows-span-sm-8', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspansm.8Label'],
            ['key' => 'rows-span-sm-9', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspansm.9Label'],
            ['key' => 'rows-span-sm-10', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspansm.10Label'],
            ['key' => 'rows-span-sm-11', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspansm.11Label'],
            ['key' => 'rows-span-sm-12', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspansm.12Label'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveSM->id, 'fwgriditemrowsspansm', 'WEMSG.STYLEMANAGER.fwgriditemrowsspansm.title', 'WEMSG.STYLEMANAGER.fwgriditemrowsspansm.description', $contentElements, $cssClasses);
        $objStyle->save();

        // Grid item - colsspanxs
        $cssClasses = [
            ['key' => 'cols-span-xs-1', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxs.1Label'],
            ['key' => 'cols-span-xs-2', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxs.2Label'],
            ['key' => 'cols-span-xs-3', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxs.3Label'],
            ['key' => 'cols-span-xs-4', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxs.4Label'],
            ['key' => 'cols-span-xs-5', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxs.5Label'],
            ['key' => 'cols-span-xs-6', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxs.6Label'],
            ['key' => 'cols-span-xs-7', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxs.7Label'],
            ['key' => 'cols-span-xs-8', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxs.8Label'],
            ['key' => 'cols-span-xs-9', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxs.9Label'],
            ['key' => 'cols-span-xs-10', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxs.10Label'],
            ['key' => 'cols-span-xs-11', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxs.11Label'],
            ['key' => 'cols-span-xs-12', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxs.12Label'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveXS->id, 'fwgriditemcolsspanxs', 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxs.title', 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxs.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Grid item - rowsspanxs
        $cssClasses = [
            ['key' => 'rows-span-xs-1', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxs.1Label'],
            ['key' => 'rows-span-xs-2', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxs.2Label'],
            ['key' => 'rows-span-xs-3', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxs.3Label'],
            ['key' => 'rows-span-xs-4', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxs.4Label'],
            ['key' => 'rows-span-xs-5', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxs.5Label'],
            ['key' => 'rows-span-xs-6', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxs.6Label'],
            ['key' => 'rows-span-xs-7', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxs.7Label'],
            ['key' => 'rows-span-xs-8', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxs.8Label'],
            ['key' => 'rows-span-xs-9', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxs.9Label'],
            ['key' => 'rows-span-xs-10', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxs.10Label'],
            ['key' => 'rows-span-xs-11', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxs.11Label'],
            ['key' => 'rows-span-xs-12', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxs.12Label'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveXS->id, 'fwgriditemrowsspanxs', 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxs.title', 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxs.description', $contentElements, $cssClasses);
        $objStyle->save();

        // Grid item - colsspanxxs
        $cssClasses = [
            ['key' => 'cols-span-xxs-1', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxxs.1Label'],
            ['key' => 'cols-span-xxs-2', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxxs.2Label'],
            ['key' => 'cols-span-xxs-3', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxxs.3Label'],
            ['key' => 'cols-span-xxs-4', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxxs.4Label'],
            ['key' => 'cols-span-xxs-5', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxxs.5Label'],
            ['key' => 'cols-span-xxs-6', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxxs.6Label'],
            ['key' => 'cols-span-xxs-7', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxxs.7Label'],
            ['key' => 'cols-span-xxs-8', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxxs.8Label'],
            ['key' => 'cols-span-xxs-9', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxxs.9Label'],
            ['key' => 'cols-span-xxs-10', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxxs.10Label'],
            ['key' => 'cols-span-xxs-11', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxxs.11Label'],
            ['key' => 'cols-span-xxs-12', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxxs.12Label'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveXXS->id, 'fwgriditemcolsspanxxs', 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxxs.title', 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxxs.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Grid item - rowsspanxxs
        $cssClasses = [
            ['key' => 'rows-span-xxs-1', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxxs.1Label'],
            ['key' => 'rows-span-xxs-2', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxxs.2Label'],
            ['key' => 'rows-span-xxs-3', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxxs.3Label'],
            ['key' => 'rows-span-xxs-4', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxxs.4Label'],
            ['key' => 'rows-span-xxs-5', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxxs.5Label'],
            ['key' => 'rows-span-xxs-6', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxxs.6Label'],
            ['key' => 'rows-span-xxs-7', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxxs.7Label'],
            ['key' => 'rows-span-xxs-8', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxxs.8Label'],
            ['key' => 'rows-span-xxs-9', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxxs.9Label'],
            ['key' => 'rows-span-xxs-10', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxxs.10Label'],
            ['key' => 'rows-span-xxs-11', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxxs.11Label'],
            ['key' => 'rows-span-xxs-12', 'value' => 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxxs.12Label'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveXXS->id, 'fwgriditemrowsspanxxs', 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxxs.title', 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxxs.description', $contentElements, $cssClasses);
        $objStyle->save();
    }

    protected function manageGrids(): void
    {
        $contentElements = self::$elements['grid'];
        // Grid
        $objArchive = $this->fillObjArchive('fwgrid', 'WEMSG.STYLEMANAGER.fwgrid.tabTitle', 'FramwayGrid');
        $objArchive->save();
        $objArchiveXL = $this->fillObjArchive('fwgridxl', 'WEMSG.STYLEMANAGER.fwgridxl.tabTitle', 'FramwayGrid');
        $objArchiveXL->save();
        $objArchiveLG = $this->fillObjArchive('fwgridlg', 'WEMSG.STYLEMANAGER.fwgridlg.tabTitle', 'FramwayGrid');
        $objArchiveLG->save();
        $objArchiveMD = $this->fillObjArchive('fwgridmd', 'WEMSG.STYLEMANAGER.fwgridmd.tabTitle', 'FramwayGrid');
        $objArchiveMD->save();
        $objArchiveSM = $this->fillObjArchive('fwgridsm', 'WEMSG.STYLEMANAGER.fwgridsm.tabTitle', 'FramwayGrid');
        $objArchiveSM->save();
        $objArchiveXS = $this->fillObjArchive('fwgridxs', 'WEMSG.STYLEMANAGER.fwgridxs.tabTitle', 'FramwayGrid');
        $objArchiveXS->save();
        $objArchiveXXS = $this->fillObjArchive('fwgridxxs', 'WEMSG.STYLEMANAGER.fwgridxxs.tabTitle', 'FramwayGrid');
        $objArchiveXXS->save();
        // Grid - gap
        $cssClasses = [
            ['key' => 'gap-0', 'value' => 'WEMSG.STYLEMANAGER.fwgridgap.0Label'],
            ['key' => 'gap-0-em', 'value' => 'WEMSG.STYLEMANAGER.fwgridgap.0emLabel'],
            ['key' => 'gap-0-rem', 'value' => 'WEMSG.STYLEMANAGER.fwgridgap.0remLabel'],
            ['key' => 'gap-1', 'value' => 'WEMSG.STYLEMANAGER.fwgridgap.1Label'],
            ['key' => 'gap-1-em', 'value' => 'WEMSG.STYLEMANAGER.fwgridgap.1emLabel'],
            ['key' => 'gap-1-rem', 'value' => 'WEMSG.STYLEMANAGER.fwgridgap.1remLabel'],
            ['key' => 'gap-2', 'value' => 'WEMSG.STYLEMANAGER.fwgridgap.2Label'],
            ['key' => 'gap-2-em', 'value' => 'WEMSG.STYLEMANAGER.fwgridgap.2emLabel'],
            ['key' => 'gap-2-rem', 'value' => 'WEMSG.STYLEMANAGER.fwgridgap.2remLabel'],
            ['key' => 'gap-3', 'value' => 'WEMSG.STYLEMANAGER.fwgridgap.3Label'],
            ['key' => 'gap-3-em', 'value' => 'WEMSG.STYLEMANAGER.fwgridgap.3emLabel'],
            ['key' => 'gap-3-rem', 'value' => 'WEMSG.STYLEMANAGER.fwgridgap.3remLabel'],
            ['key' => 'gap-4', 'value' => 'WEMSG.STYLEMANAGER.fwgridgap.4Label'],
            ['key' => 'gap-4-em', 'value' => 'WEMSG.STYLEMANAGER.fwgridgap.4emLabel'],
            ['key' => 'gap-4-rem', 'value' => 'WEMSG.STYLEMANAGER.fwgridgap.4remLabel'],
            ['key' => 'gap-5', 'value' => 'WEMSG.STYLEMANAGER.fwgridgap.5Label'],
            ['key' => 'gap-5-em', 'value' => 'WEMSG.STYLEMANAGER.fwgridgap.5emLabel'],
            ['key' => 'gap-5-rem', 'value' => 'WEMSG.STYLEMANAGER.fwgridgap.5remLabel'],
            ['key' => 'gap-6', 'value' => 'WEMSG.STYLEMANAGER.fwgridgap.6Label'],
            ['key' => 'gap-6-em', 'value' => 'WEMSG.STYLEMANAGER.fwgridgap.6emLabel'],
            ['key' => 'gap-6-rem', 'value' => 'WEMSG.STYLEMANAGER.fwgridgap.6remLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwgridgap', 'WEMSG.STYLEMANAGER.fwgridgap.title', 'WEMSG.STYLEMANAGER.fwgridgap.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Grid - cols
        $cssClasses = [
            ['key' => 'cols-1', 'value' => 'WEMSG.STYLEMANAGER.fwgridcols.1Label'],
            ['key' => 'cols-2', 'value' => 'WEMSG.STYLEMANAGER.fwgridcols.2Label'],
            ['key' => 'cols-3', 'value' => 'WEMSG.STYLEMANAGER.fwgridcols.3Label'],
            ['key' => 'cols-4', 'value' => 'WEMSG.STYLEMANAGER.fwgridcols.4Label'],
            ['key' => 'cols-5', 'value' => 'WEMSG.STYLEMANAGER.fwgridcols.5Label'],
            ['key' => 'cols-6', 'value' => 'WEMSG.STYLEMANAGER.fwgridcols.6Label'],
            ['key' => 'cols-7', 'value' => 'WEMSG.STYLEMANAGER.fwgridcols.7Label'],
            ['key' => 'cols-8', 'value' => 'WEMSG.STYLEMANAGER.fwgridcols.8Label'],
            ['key' => 'cols-9', 'value' => 'WEMSG.STYLEMANAGER.fwgridcols.9Label'],
            ['key' => 'cols-10', 'value' => 'WEMSG.STYLEMANAGER.fwgridcols.10Label'],
            ['key' => 'cols-11', 'value' => 'WEMSG.STYLEMANAGER.fwgridcols.11Label'],
            ['key' => 'cols-12', 'value' => 'WEMSG.STYLEMANAGER.fwgridcols.12Label'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwgridcols', 'WEMSG.STYLEMANAGER.fwgridcols.title', 'WEMSG.STYLEMANAGER.fwgridcols.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Grid - rows
        $cssClasses = [
            ['key' => 'rows-1', 'value' => 'WEMSG.STYLEMANAGER.fwgridrows.1Label'],
            ['key' => 'rows-2', 'value' => 'WEMSG.STYLEMANAGER.fwgridrows.2Label'],
            ['key' => 'rows-3', 'value' => 'WEMSG.STYLEMANAGER.fwgridrows.3Label'],
            ['key' => 'rows-4', 'value' => 'WEMSG.STYLEMANAGER.fwgridrows.4Label'],
            ['key' => 'rows-5', 'value' => 'WEMSG.STYLEMANAGER.fwgridrows.5Label'],
            ['key' => 'rows-6', 'value' => 'WEMSG.STYLEMANAGER.fwgridrows.6Label'],
            ['key' => 'rows-7', 'value' => 'WEMSG.STYLEMANAGER.fwgridrows.7Label'],
            ['key' => 'rows-8', 'value' => 'WEMSG.STYLEMANAGER.fwgridrows.8Label'],
            ['key' => 'rows-9', 'value' => 'WEMSG.STYLEMANAGER.fwgridrows.9Label'],
            ['key' => 'rows-10', 'value' => 'WEMSG.STYLEMANAGER.fwgridrows.10Label'],
            ['key' => 'rows-11', 'value' => 'WEMSG.STYLEMANAGER.fwgridrows.11Label'],
            ['key' => 'rows-12', 'value' => 'WEMSG.STYLEMANAGER.fwgridrows.12Label'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwgridrows', 'WEMSG.STYLEMANAGER.fwgridrows.title', 'WEMSG.STYLEMANAGER.fwgridrows.description', $contentElements, $cssClasses);
        $objStyle->save();

        // Grid - colsxl
        $cssClasses = [
            ['key' => 'cols-xl-1', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsxl.1Label'],
            ['key' => 'cols-xl-2', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsxl.2Label'],
            ['key' => 'cols-xl-3', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsxl.3Label'],
            ['key' => 'cols-xl-4', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsxl.4Label'],
            ['key' => 'cols-xl-5', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsxl.5Label'],
            ['key' => 'cols-xl-6', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsxl.6Label'],
            ['key' => 'cols-xl-7', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsxl.7Label'],
            ['key' => 'cols-xl-8', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsxl.8Label'],
            ['key' => 'cols-xl-9', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsxl.9Label'],
            ['key' => 'cols-xl-10', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsxl.10Label'],
            ['key' => 'cols-xl-11', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsxl.11Label'],
            ['key' => 'cols-xl-12', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsxl.12Label'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveXL->id, 'fwgridcolsxl', 'WEMSG.STYLEMANAGER.fwgridcolsxl.title', 'WEMSG.STYLEMANAGER.fwgridcolsxl.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Grid - rowsxl
        $cssClasses = [
            ['key' => 'rows-xl-1', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsxl.1Label'],
            ['key' => 'rows-xl-2', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsxl.2Label'],
            ['key' => 'rows-xl-3', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsxl.3Label'],
            ['key' => 'rows-xl-4', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsxl.4Label'],
            ['key' => 'rows-xl-5', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsxl.5Label'],
            ['key' => 'rows-xl-6', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsxl.6Label'],
            ['key' => 'rows-xl-7', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsxl.7Label'],
            ['key' => 'rows-xl-8', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsxl.8Label'],
            ['key' => 'rows-xl-9', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsxl.9Label'],
            ['key' => 'rows-xl-10', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsxl.10Label'],
            ['key' => 'rows-xl-11', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsxl.11Label'],
            ['key' => 'rows-xl-12', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsxl.12Label'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveXL->id, 'fwgridrowsxl', 'WEMSG.STYLEMANAGER.fwgridrowsxl.title', 'WEMSG.STYLEMANAGER.fwgridrowsxl.description', $contentElements, $cssClasses);
        $objStyle->save();

        // Grid - colslg
        $cssClasses = [
            ['key' => 'cols-lg-1', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolslg.1Label'],
            ['key' => 'cols-lg-2', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolslg.2Label'],
            ['key' => 'cols-lg-3', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolslg.3Label'],
            ['key' => 'cols-lg-4', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolslg.4Label'],
            ['key' => 'cols-lg-5', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolslg.5Label'],
            ['key' => 'cols-lg-6', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolslg.6Label'],
            ['key' => 'cols-lg-7', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolslg.7Label'],
            ['key' => 'cols-lg-8', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolslg.8Label'],
            ['key' => 'cols-lg-9', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolslg.9Label'],
            ['key' => 'cols-lg-10', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolslg.10Label'],
            ['key' => 'cols-lg-11', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolslg.11Label'],
            ['key' => 'cols-lg-12', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolslg.12Label'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveLG->id, 'fwgridcolslg', 'WEMSG.STYLEMANAGER.fwgridcolslg.title', 'WEMSG.STYLEMANAGER.fwgridcolslg.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Grid - rowslg
        $cssClasses = [
            ['key' => 'rows-lg-1', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowslg.1Label'],
            ['key' => 'rows-lg-2', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowslg.2Label'],
            ['key' => 'rows-lg-3', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowslg.3Label'],
            ['key' => 'rows-lg-4', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowslg.4Label'],
            ['key' => 'rows-lg-5', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowslg.5Label'],
            ['key' => 'rows-lg-6', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowslg.6Label'],
            ['key' => 'rows-lg-7', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowslg.7Label'],
            ['key' => 'rows-lg-8', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowslg.8Label'],
            ['key' => 'rows-lg-9', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowslg.9Label'],
            ['key' => 'rows-lg-10', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowslg.10Label'],
            ['key' => 'rows-lg-11', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowslg.11Label'],
            ['key' => 'rows-lg-12', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowslg.12Label'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveLG->id, 'fwgridrowslg', 'WEMSG.STYLEMANAGER.fwgridrowslg.title', 'WEMSG.STYLEMANAGER.fwgridrowslg.description', $contentElements, $cssClasses);
        $objStyle->save();

        // Grid - colsmd
        $cssClasses = [
            ['key' => 'cols-md-1', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsmd.1Label'],
            ['key' => 'cols-md-2', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsmd.2Label'],
            ['key' => 'cols-md-3', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsmd.3Label'],
            ['key' => 'cols-md-4', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsmd.4Label'],
            ['key' => 'cols-md-5', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsmd.5Label'],
            ['key' => 'cols-md-6', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsmd.6Label'],
            ['key' => 'cols-md-7', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsmd.7Label'],
            ['key' => 'cols-md-8', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsmd.8Label'],
            ['key' => 'cols-md-9', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsmd.9Label'],
            ['key' => 'cols-md-10', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsmd.10Label'],
            ['key' => 'cols-md-11', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsmd.11Label'],
            ['key' => 'cols-md-12', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsmd.12Label'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveMD->id, 'fwgridcolsmd', 'WEMSG.STYLEMANAGER.fwgridcolsmd.title', 'WEMSG.STYLEMANAGER.fwgridcolsmd.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Grid - rowsmd
        $cssClasses = [
            ['key' => 'rows-md-1', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsmd.1Label'],
            ['key' => 'rows-md-2', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsmd.2Label'],
            ['key' => 'rows-md-3', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsmd.3Label'],
            ['key' => 'rows-md-4', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsmd.4Label'],
            ['key' => 'rows-md-5', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsmd.5Label'],
            ['key' => 'rows-md-6', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsmd.6Label'],
            ['key' => 'rows-md-7', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsmd.7Label'],
            ['key' => 'rows-md-8', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsmd.8Label'],
            ['key' => 'rows-md-9', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsmd.9Label'],
            ['key' => 'rows-md-10', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsmd.10Label'],
            ['key' => 'rows-md-11', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsmd.11Label'],
            ['key' => 'rows-md-12', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsmd.12Label'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveMD->id, 'fwgridrowsmd', 'WEMSG.STYLEMANAGER.fwgridrowsmd.title', 'WEMSG.STYLEMANAGER.fwgridrowsmd.description', $contentElements, $cssClasses);
        $objStyle->save();

        // Grid - colssm
        $cssClasses = [
            ['key' => 'cols-sm-1', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolssm.1Label'],
            ['key' => 'cols-sm-2', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolssm.2Label'],
            ['key' => 'cols-sm-3', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolssm.3Label'],
            ['key' => 'cols-sm-4', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolssm.4Label'],
            ['key' => 'cols-sm-5', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolssm.5Label'],
            ['key' => 'cols-sm-6', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolssm.6Label'],
            ['key' => 'cols-sm-7', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolssm.7Label'],
            ['key' => 'cols-sm-8', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolssm.8Label'],
            ['key' => 'cols-sm-9', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolssm.9Label'],
            ['key' => 'cols-sm-10', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolssm.10Label'],
            ['key' => 'cols-sm-11', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolssm.11Label'],
            ['key' => 'cols-sm-12', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolssm.12Label'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveSM->id, 'fwgridcolssm', 'WEMSG.STYLEMANAGER.fwgridcolssm.title', 'WEMSG.STYLEMANAGER.fwgridcolssm.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Grid - rowssm
        $cssClasses = [
            ['key' => 'rows-sm-1', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowssm.1Label'],
            ['key' => 'rows-sm-2', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowssm.2Label'],
            ['key' => 'rows-sm-3', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowssm.3Label'],
            ['key' => 'rows-sm-4', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowssm.4Label'],
            ['key' => 'rows-sm-5', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowssm.5Label'],
            ['key' => 'rows-sm-6', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowssm.6Label'],
            ['key' => 'rows-sm-7', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowssm.7Label'],
            ['key' => 'rows-sm-8', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowssm.8Label'],
            ['key' => 'rows-sm-9', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowssm.9Label'],
            ['key' => 'rows-sm-10', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowssm.10Label'],
            ['key' => 'rows-sm-11', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowssm.11Label'],
            ['key' => 'rows-sm-12', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowssm.12Label'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveSM->id, 'fwgridrowssm', 'WEMSG.STYLEMANAGER.fwgridrowssm.title', 'WEMSG.STYLEMANAGER.fwgridrowssm.description', $contentElements, $cssClasses);
        $objStyle->save();

        // Grid - colsxs
        $cssClasses = [
            ['key' => 'cols-xs-1', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsxs.1Label'],
            ['key' => 'cols-xs-2', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsxs.2Label'],
            ['key' => 'cols-xs-3', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsxs.3Label'],
            ['key' => 'cols-xs-4', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsxs.4Label'],
            ['key' => 'cols-xs-5', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsxs.5Label'],
            ['key' => 'cols-xs-6', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsxs.6Label'],
            ['key' => 'cols-xs-7', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsxs.7Label'],
            ['key' => 'cols-xs-8', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsxs.8Label'],
            ['key' => 'cols-xs-9', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsxs.9Label'],
            ['key' => 'cols-xs-10', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsxs.10Label'],
            ['key' => 'cols-xs-11', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsxs.11Label'],
            ['key' => 'cols-xs-12', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsxs.12Label'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveXS->id, 'fwgridcolsxs', 'WEMSG.STYLEMANAGER.fwgridcolsxs.title', 'WEMSG.STYLEMANAGER.fwgridcolsxs.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Grid - rowsxs
        $cssClasses = [
            ['key' => 'rows-xs-1', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsxs.1Label'],
            ['key' => 'rows-xs-2', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsxs.2Label'],
            ['key' => 'rows-xs-3', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsxs.3Label'],
            ['key' => 'rows-xs-4', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsxs.4Label'],
            ['key' => 'rows-xs-5', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsxs.5Label'],
            ['key' => 'rows-xs-6', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsxs.6Label'],
            ['key' => 'rows-xs-7', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsxs.7Label'],
            ['key' => 'rows-xs-8', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsxs.8Label'],
            ['key' => 'rows-xs-9', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsxs.9Label'],
            ['key' => 'rows-xs-10', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsxs.10Label'],
            ['key' => 'rows-xs-11', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsxs.11Label'],
            ['key' => 'rows-xs-12', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsxs.12Label'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveXS->id, 'fwgridrowsxs', 'WEMSG.STYLEMANAGER.fwgridrowsxs.title', 'WEMSG.STYLEMANAGER.fwgridrowsxs.description', $contentElements, $cssClasses);
        $objStyle->save();

        // Grid - colsxxs
        $cssClasses = [
            ['key' => 'cols-xxs-1', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsxxs.1Label'],
            ['key' => 'cols-xxs-2', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsxxs.2Label'],
            ['key' => 'cols-xxs-3', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsxxs.3Label'],
            ['key' => 'cols-xxs-4', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsxxs.4Label'],
            ['key' => 'cols-xxs-5', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsxxs.5Label'],
            ['key' => 'cols-xxs-6', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsxxs.6Label'],
            ['key' => 'cols-xxs-7', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsxxs.7Label'],
            ['key' => 'cols-xxs-8', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsxxs.8Label'],
            ['key' => 'cols-xxs-9', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsxxs.9Label'],
            ['key' => 'cols-xxs-10', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsxxs.10Label'],
            ['key' => 'cols-xxs-11', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsxxs.11Label'],
            ['key' => 'cols-xxs-12', 'value' => 'WEMSG.STYLEMANAGER.fwgridcolsxxs.12Label'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveXXS->id, 'fwgridcolsxxs', 'WEMSG.STYLEMANAGER.fwgridcolsxxs.title', 'WEMSG.STYLEMANAGER.fwgridcolsxxs.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Grid - rowsxxs
        $cssClasses = [
            ['key' => 'rows-xxs-1', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsxxs.1Label'],
            ['key' => 'rows-xxs-2', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsxxs.2Label'],
            ['key' => 'rows-xxs-3', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsxxs.3Label'],
            ['key' => 'rows-xxs-4', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsxxs.4Label'],
            ['key' => 'rows-xxs-5', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsxxs.5Label'],
            ['key' => 'rows-xxs-6', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsxxs.6Label'],
            ['key' => 'rows-xxs-7', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsxxs.7Label'],
            ['key' => 'rows-xxs-8', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsxxs.8Label'],
            ['key' => 'rows-xxs-9', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsxxs.9Label'],
            ['key' => 'rows-xxs-10', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsxxs.10Label'],
            ['key' => 'rows-xxs-11', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsxxs.11Label'],
            ['key' => 'rows-xxs-12', 'value' => 'WEMSG.STYLEMANAGER.fwgridrowsxxs.12Label'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveXXS->id, 'fwgridrowsxxs', 'WEMSG.STYLEMANAGER.fwgridrowsxxs.title', 'WEMSG.STYLEMANAGER.fwgridrowsxxs.description', $contentElements, $cssClasses);
        $objStyle->save();
    }

    protected function manageHero(): void
    {
        $contentElements = self::$elements['hero'];
        // Hero
        $objArchive = $this->fillObjArchive('fwhero', 'WEMSG.STYLEMANAGER.fwhero.tabTitle', 'FramwayHero');
        $objArchive->save();
        $objArchiveImg = $this->fillObjArchive('fwheroimg', 'WEMSG.STYLEMANAGER.fwheroimg.tabTitle', 'FramwayHero');
        $objArchiveImg->save();
        $objArchiveContent = $this->fillObjArchive('fwherocontent', 'WEMSG.STYLEMANAGER.fwherocontent.tabTitle', 'FramwayHero');
        $objArchiveContent->save();
        $objArchiveTitle = $this->fillObjArchive('fwherotitle', 'WEMSG.STYLEMANAGER.fwherotitle.tabTitle', 'FramwayHero');
        $objArchiveTitle->save();
        // Hero - imgvertical
        $cssClasses = [
            ['key' => 'img--top', 'value' => 'WEMSG.STYLEMANAGER.fwheroimgvertical.topLabel'],
            ['key' => 'img--bottom', 'value' => 'WEMSG.STYLEMANAGER.fwheroimgvertical.bottomLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveImg->id, 'fwheroimgvertical', 'WEMSG.STYLEMANAGER.fwheroimgvertical.title', 'WEMSG.STYLEMANAGER.fwheroimgvertical.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Hero - imghorizontal
        $cssClasses = [
            ['key' => 'img--left', 'value' => 'WEMSG.STYLEMANAGER.fwheroimghorizontal.leftLabel'],
            ['key' => 'img--right', 'value' => 'WEMSG.STYLEMANAGER.fwheroimghorizontal.rightLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveImg->id, 'fwheroimghorizontal', 'WEMSG.STYLEMANAGER.fwheroimghorizontal.title', 'WEMSG.STYLEMANAGER.fwheroimghorizontal.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Hero - figureopacity
        $cssClasses = [
            ['key' => 'figure_opacity--0', 'value' => 'WEMSG.STYLEMANAGER.fwherofigureopacity.0Label'],
            ['key' => 'figure_opacity--1', 'value' => 'WEMSG.STYLEMANAGER.fwherofigureopacity.1Label'],
            ['key' => 'figure_opacity--2', 'value' => 'WEMSG.STYLEMANAGER.fwherofigureopacity.2Label'],
            ['key' => 'figure_opacity--3', 'value' => 'WEMSG.STYLEMANAGER.fwherofigureopacity.3Label'],
            ['key' => 'figure_opacity--4', 'value' => 'WEMSG.STYLEMANAGER.fwherofigureopacity.4Label'],
            ['key' => 'figure_opacity--5', 'value' => 'WEMSG.STYLEMANAGER.fwherofigureopacity.5Label'],
            ['key' => 'figure_opacity--6', 'value' => 'WEMSG.STYLEMANAGER.fwherofigureopacity.6Label'],
            ['key' => 'figure_opacity--7', 'value' => 'WEMSG.STYLEMANAGER.fwherofigureopacity.7Label'],
            ['key' => 'figure_opacity--8', 'value' => 'WEMSG.STYLEMANAGER.fwherofigureopacity.8Label'],
            ['key' => 'figure_opacity--9', 'value' => 'WEMSG.STYLEMANAGER.fwherofigureopacity.9Label'],
            ['key' => 'figure_opacity--10', 'value' => 'WEMSG.STYLEMANAGER.fwherofigureopacity.10Label'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveImg->id, 'fwherofigureopacity', 'WEMSG.STYLEMANAGER.fwherofigureopacity.title', 'WEMSG.STYLEMANAGER.fwherofigureopacity.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Hero - contentvertical
        $cssClasses = [
            ['key' => 'content--v--top', 'value' => 'WEMSG.STYLEMANAGER.fwherocontentvertical.topLabel'],
            ['key' => 'content--v--center', 'value' => 'WEMSG.STYLEMANAGER.fwherocontentvertical.centerLabel'],
            ['key' => 'content--v--bottom', 'value' => 'WEMSG.STYLEMANAGER.fwherocontentvertical.bottomLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveContent->id, 'fwherocontentvertical', 'WEMSG.STYLEMANAGER.fwherocontentvertical.title', 'WEMSG.STYLEMANAGER.fwherocontentvertical.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Hero - contenthorizontal
        $cssClasses = [
            ['key' => 'content--h--left', 'value' => 'WEMSG.STYLEMANAGER.fwherocontenthorizontal.leftLabel'],
            ['key' => 'content--h--center', 'value' => 'WEMSG.STYLEMANAGER.fwherocontenthorizontal.centerLabel'],
            ['key' => 'content--h--right', 'value' => 'WEMSG.STYLEMANAGER.fwherocontenthorizontal.rightLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveContent->id, 'fwherocontenthorizontal', 'WEMSG.STYLEMANAGER.fwherocontenthorizontal.title', 'WEMSG.STYLEMANAGER.fwherocontenthorizontal.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Hero - title
        $cssClasses = [
            ['key' => 'title--1', 'value' => 'WEMSG.STYLEMANAGER.fwherotitle.1Label'],
            ['key' => 'title--2', 'value' => 'WEMSG.STYLEMANAGER.fwherotitle.2Label'],
            ['key' => 'title--3', 'value' => 'WEMSG.STYLEMANAGER.fwherotitle.3Label'],
            ['key' => 'title--4', 'value' => 'WEMSG.STYLEMANAGER.fwherotitle.4Label'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveTitle->id, 'fwherotitle', 'WEMSG.STYLEMANAGER.fwherotitle.title', 'WEMSG.STYLEMANAGER.fwherotitle.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Hero - text color
        $cssClasses = [];
        $colors = $this->configurationThemeManager->load()->getColors();
        foreach ($colors as $name => $hexa) {
            $cssClasses[] = [
                'key' => 'ft-'.$name,
                'value' => sprintf('WEMSG.STYLEMANAGER.fwheroft.colorLabel (WEMSG.FRAMWAY.COLORS.%s)', $name),
            ];
        }
        $objStyle = $this->fillObjStyle($objArchiveTitle->id, 'fwheroft', 'WEMSG.STYLEMANAGER.fwheroft.title', 'WEMSG.STYLEMANAGER.fwheroft.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Hero - bg color
        $cssClasses = [];
        $colors = $this->configurationThemeManager->load()->getColors();
        foreach ($colors as $name => $hexa) {
            $cssClasses[] = [
                'key' => 'content_bg--'.$name,
                'value' => sprintf('WEMSG.STYLEMANAGER.fwherocontentbg.colorLabel (WEMSG.FRAMWAY.COLORS.%s)', $name),
            ];
        }
        $objStyle = $this->fillObjStyle($objArchiveContent->id, 'fwherocontentbg', 'WEMSG.STYLEMANAGER.fwherocontentbg.title', 'WEMSG.STYLEMANAGER.fwherocontentbg.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Hero - bgopacity
        $cssClasses = [
            ['key' => 'content_bg_opacity--0', 'value' => 'WEMSG.STYLEMANAGER.fwherocontentbgopacity.0Label'],
            ['key' => 'content_bg_opacity--1', 'value' => 'WEMSG.STYLEMANAGER.fwherocontentbgopacity.1Label'],
            ['key' => 'content_bg_opacity--2', 'value' => 'WEMSG.STYLEMANAGER.fwherocontentbgopacity.2Label'],
            ['key' => 'content_bg_opacity--3', 'value' => 'WEMSG.STYLEMANAGER.fwherocontentbgopacity.3Label'],
            ['key' => 'content_bg_opacity--4', 'value' => 'WEMSG.STYLEMANAGER.fwherocontentbgopacity.4Label'],
            ['key' => 'content_bg_opacity--5', 'value' => 'WEMSG.STYLEMANAGER.fwherocontentbgopacity.5Label'],
            ['key' => 'content_bg_opacity--6', 'value' => 'WEMSG.STYLEMANAGER.fwherocontentbgopacity.6Label'],
            ['key' => 'content_bg_opacity--7', 'value' => 'WEMSG.STYLEMANAGER.fwherocontentbgopacity.7Label'],
            ['key' => 'content_bg_opacity--8', 'value' => 'WEMSG.STYLEMANAGER.fwherocontentbgopacity.8Label'],
            ['key' => 'content_bg_opacity--9', 'value' => 'WEMSG.STYLEMANAGER.fwherocontentbgopacity.9Label'],
            ['key' => 'content_bg_opacity--10', 'value' => 'WEMSG.STYLEMANAGER.fwherocontentbgopacity.10Label'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveContent->id, 'fwherocontentbgopacity', 'WEMSG.STYLEMANAGER.fwherocontentbgopacity.title', 'WEMSG.STYLEMANAGER.fwherocontentbgopacity.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Hero - wfull
        $cssClasses = [
            ['key' => 'w-full', 'value' => 'WEMSG.STYLEMANAGER.fwherowfull.label'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwherowfull', 'WEMSG.STYLEMANAGER.fwherowfull.title', 'WEMSG.STYLEMANAGER.fwherowfull.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Hero - heightcontent
        $cssClasses = [
            ['key' => 'height--content', 'value' => 'WEMSG.STYLEMANAGER.fwheroheightcontent.label'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveContent->id, 'fwheroheightcontent', 'WEMSG.STYLEMANAGER.fwheroheightcontent.title', 'WEMSG.STYLEMANAGER.fwheroheightcontent.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Hero - widthcontent
        $cssClasses = [
            ['key' => 'width--content', 'value' => 'WEMSG.STYLEMANAGER.fwherowidthcontent.label'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveContent->id, 'fwherowidthcontent', 'WEMSG.STYLEMANAGER.fwherowidthcontent.title', 'WEMSG.STYLEMANAGER.fwherowidthcontent.description', $contentElements, $cssClasses);
        $objStyle->save();
    }

    protected function manageSliders(): void
    {
        $contentElements = self::$elements['slider'];
        // Slider
        $objArchive = $this->fillObjArchive('fwslider', 'WEMSG.STYLEMANAGER.fwslider.tabTitle', 'FramwaySlider');
        $objArchive->save();
        $objArchiveNav = $this->fillObjArchive('fwslidernav', 'WEMSG.STYLEMANAGER.fwslidernav.tabTitle', 'FramwaySlider');
        $objArchiveNav->save();
        $objArchiveImg = $this->fillObjArchive('fwsliderimg', 'WEMSG.STYLEMANAGER.fwsliderimg.tabTitle', 'FramwaySlider');
        $objArchiveImg->save();
        $objArchiveContent = $this->fillObjArchive('fwslidercontent', 'WEMSG.STYLEMANAGER.fwslidercontent.tabTitle', 'FramwaySlider');
        $objArchiveContent->save();
        $objArchiveTitle = $this->fillObjArchive('fwslidertitle', 'WEMSG.STYLEMANAGER.fwslidertitle.tabTitle', 'FramwaySlider');
        $objArchiveTitle->save();
        // Slider - nav
        $cssClasses = [
            ['key' => 'nav--below', 'value' => 'WEMSG.STYLEMANAGER.fwslidernav.belowLabel'],
            ['key' => 'nav--hidden', 'value' => 'WEMSG.STYLEMANAGER.fwslidernav.hiddenLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveNav->id, 'fwslidernav', 'WEMSG.STYLEMANAGER.fwslidernav.title', 'WEMSG.STYLEMANAGER.fwslidernav.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Slider - navvertical
        $cssClasses = [
            ['key' => 'nav--top', 'value' => 'WEMSG.STYLEMANAGER.fwslidernavvertical.topLabel'],
            ['key' => 'nav--bottom', 'value' => 'WEMSG.STYLEMANAGER.fwslidernavvertical.bottomLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveNav->id, 'fwslidernavvertical', 'WEMSG.STYLEMANAGER.fwslidernavvertical.title', 'WEMSG.STYLEMANAGER.fwslidernavvertical.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Slider - navhorizontal
        $cssClasses = [
            ['key' => 'nav--left', 'value' => 'WEMSG.STYLEMANAGER.fwslidernavhorizontal.leftLabel'],
            ['key' => 'nav--right', 'value' => 'WEMSG.STYLEMANAGER.fwslidernavhorizontal.rightLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveNav->id, 'fwslidernavhorizontal', 'WEMSG.STYLEMANAGER.fwslidernavhorizontal.title', 'WEMSG.STYLEMANAGER.fwslidernavhorizontal.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Slider - imgvertical
        $cssClasses = [
            ['key' => 'img--top', 'value' => 'WEMSG.STYLEMANAGER.fwsliderimgvertical.topLabel'],
            ['key' => 'img--bottom', 'value' => 'WEMSG.STYLEMANAGER.fwsliderimgvertical.bottomLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveImg->id, 'fwsliderimgvertical', 'WEMSG.STYLEMANAGER.fwsliderimgvertical.title', 'WEMSG.STYLEMANAGER.fwsliderimgvertical.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Slider - imghorizontal
        $cssClasses = [
            ['key' => 'img--left', 'value' => 'WEMSG.STYLEMANAGER.fwsliderimghorizontal.leftLabel'],
            ['key' => 'img--right', 'value' => 'WEMSG.STYLEMANAGER.fwsliderimghorizontal.rightLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveImg->id, 'fwsliderimghorizontal', 'WEMSG.STYLEMANAGER.fwsliderimghorizontal.title', 'WEMSG.STYLEMANAGER.fwsliderimghorizontal.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Slider - imgopacity
        $cssClasses = [
            ['key' => 'img_opacity--0', 'value' => 'WEMSG.STYLEMANAGER.fwsliderimgopacity.0Label'],
            ['key' => 'img_opacity--1', 'value' => 'WEMSG.STYLEMANAGER.fwsliderimgopacity.1Label'],
            ['key' => 'img_opacity--2', 'value' => 'WEMSG.STYLEMANAGER.fwsliderimgopacity.2Label'],
            ['key' => 'img_opacity--3', 'value' => 'WEMSG.STYLEMANAGER.fwsliderimgopacity.3Label'],
            ['key' => 'img_opacity--4', 'value' => 'WEMSG.STYLEMANAGER.fwsliderimgopacity.4Label'],
            ['key' => 'img_opacity--5', 'value' => 'WEMSG.STYLEMANAGER.fwsliderimgopacity.5Label'],
            ['key' => 'img_opacity--6', 'value' => 'WEMSG.STYLEMANAGER.fwsliderimgopacity.6Label'],
            ['key' => 'img_opacity--7', 'value' => 'WEMSG.STYLEMANAGER.fwsliderimgopacity.7Label'],
            ['key' => 'img_opacity--8', 'value' => 'WEMSG.STYLEMANAGER.fwsliderimgopacity.8Label'],
            ['key' => 'img_opacity--9', 'value' => 'WEMSG.STYLEMANAGER.fwsliderimgopacity.9Label'],
            ['key' => 'img_opacity--10', 'value' => 'WEMSG.STYLEMANAGER.fwsliderimgopacity.10Label'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveImg->id, 'fwsliderimgopacity', 'WEMSG.STYLEMANAGER.fwsliderimgopacity.title', 'WEMSG.STYLEMANAGER.fwsliderimgopacity.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Slider - contentvertical
        $cssClasses = [
            ['key' => 'content--v--top', 'value' => 'WEMSG.STYLEMANAGER.fwslidercontentvertical.topLabel'],
            ['key' => 'content--v--center', 'value' => 'WEMSG.STYLEMANAGER.fwslidercontentvertical.centerLabel'],
            ['key' => 'content--v--bottom', 'value' => 'WEMSG.STYLEMANAGER.fwslidercontentvertical.bottomLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveContent->id, 'fwslidercontentvertical', 'WEMSG.STYLEMANAGER.fwslidercontentvertical.title', 'WEMSG.STYLEMANAGER.fwslidercontentvertical.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Slider - contenthorizontal
        $cssClasses = [
            ['key' => 'content--h--left', 'value' => 'WEMSG.STYLEMANAGER.fwslidercontenthorizontal.leftLabel'],
            ['key' => 'content--h--center', 'value' => 'WEMSG.STYLEMANAGER.fwslidercontenthorizontal.centerLabel'],
            ['key' => 'content--h--right', 'value' => 'WEMSG.STYLEMANAGER.fwslidercontenthorizontal.rightLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveContent->id, 'fwslidercontenthorizontal', 'WEMSG.STYLEMANAGER.fwslidercontenthorizontal.title', 'WEMSG.STYLEMANAGER.fwslidercontenthorizontal.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Slider - title
        $cssClasses = [
            ['key' => 'title--1', 'value' => 'WEMSG.STYLEMANAGER.fwslidertitle.1Label'],
            ['key' => 'title--2', 'value' => 'WEMSG.STYLEMANAGER.fwslidertitle.2Label'],
            ['key' => 'title--3', 'value' => 'WEMSG.STYLEMANAGER.fwslidertitle.3Label'],
            ['key' => 'title--4', 'value' => 'WEMSG.STYLEMANAGER.fwslidertitle.4Label'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveTitle->id, 'fwslidertitle', 'WEMSG.STYLEMANAGER.fwslidertitle.title', 'WEMSG.STYLEMANAGER.fwslidertitle.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Slider - text color
        $cssClasses = [];
        $colors = $this->configurationThemeManager->load()->getColors();
        foreach ($colors as $name => $hexa) {
            $cssClasses[] = [
                'key' => 'ft-'.$name,
                'value' => sprintf('WEMSG.STYLEMANAGER.fwsliderft.colorLabel (WEMSG.FRAMWAY.COLORS.%s)', $name),
            ];
        }
        $objStyle = $this->fillObjStyle($objArchiveTitle->id, 'fwsliderft', 'WEMSG.STYLEMANAGER.fwsliderft.title', 'WEMSG.STYLEMANAGER.fwsliderft.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Slider - bg color
        $cssClasses = [];
        $colors = $this->configurationThemeManager->load()->getColors();
        foreach ($colors as $name => $hexa) {
            $cssClasses[] = [
                'key' => 'content_bg--'.$name,
                'value' => sprintf('WEMSG.STYLEMANAGER.fwslidercontentbg.colorLabel (WEMSG.FRAMWAY.COLORS.%s)', $name),
            ];
        }
        $objStyle = $this->fillObjStyle($objArchiveContent->id, 'fwslidercontentbg', 'WEMSG.STYLEMANAGER.fwslidercontentbg.title', 'WEMSG.STYLEMANAGER.fwslidercontentbg.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Slider - bgopacity
        $cssClasses = [
            ['key' => 'content_bg_opacity--0', 'value' => 'WEMSG.STYLEMANAGER.fwslidercontentbgopacity.0Label'],
            ['key' => 'content_bg_opacity--1', 'value' => 'WEMSG.STYLEMANAGER.fwslidercontentbgopacity.1Label'],
            ['key' => 'content_bg_opacity--2', 'value' => 'WEMSG.STYLEMANAGER.fwslidercontentbgopacity.2Label'],
            ['key' => 'content_bg_opacity--3', 'value' => 'WEMSG.STYLEMANAGER.fwslidercontentbgopacity.3Label'],
            ['key' => 'content_bg_opacity--4', 'value' => 'WEMSG.STYLEMANAGER.fwslidercontentbgopacity.4Label'],
            ['key' => 'content_bg_opacity--5', 'value' => 'WEMSG.STYLEMANAGER.fwslidercontentbgopacity.5Label'],
            ['key' => 'content_bg_opacity--6', 'value' => 'WEMSG.STYLEMANAGER.fwslidercontentbgopacity.6Label'],
            ['key' => 'content_bg_opacity--7', 'value' => 'WEMSG.STYLEMANAGER.fwslidercontentbgopacity.7Label'],
            ['key' => 'content_bg_opacity--8', 'value' => 'WEMSG.STYLEMANAGER.fwslidercontentbgopacity.8Label'],
            ['key' => 'content_bg_opacity--9', 'value' => 'WEMSG.STYLEMANAGER.fwslidercontentbgopacity.9Label'],
            ['key' => 'content_bg_opacity--10', 'value' => 'WEMSG.STYLEMANAGER.fwslidercontentbgopacity.10Label'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveContent->id, 'fwslidercontentbgopacity', 'WEMSG.STYLEMANAGER.fwslidercontentbgopacity.title', 'WEMSG.STYLEMANAGER.fwslidercontentbgopacity.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Slider - wfull
        $cssClasses = [
            ['key' => 'w-full', 'value' => 'WEMSG.STYLEMANAGER.fwsliderwfull.label'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwsliderwfull', 'WEMSG.STYLEMANAGER.fwsliderwfull.title', 'WEMSG.STYLEMANAGER.fwsliderwfull.description', $contentElements, $cssClasses);
        $objStyle->save();
    }

    protected function manageImages(): void
    {
        $contentElements = self::$elements['image'];
        // Image
        $objArchiveRatio = $this->fillObjArchive('fwimageratio', 'WEMSG.STYLEMANAGER.fwimageratio.tabTitle', 'FramwayImage');
        $objArchiveRatio->save();
        $objArchive = $this->fillObjArchive('fwimage', 'WEMSG.STYLEMANAGER.fwimage.tabTitle', 'FramwayImage');
        $objArchive->save();
        // Image - ratio
        $cssClasses = [
            ['key' => 'r_16-9', 'value' => 'WEMSG.STYLEMANAGER.fwimageratio.r169Label'],
            ['key' => 'r_2-1', 'value' => 'WEMSG.STYLEMANAGER.fwimageratio.r21Label'],
            ['key' => 'r_1-1', 'value' => 'WEMSG.STYLEMANAGER.fwimageratio.r11Label'],
            ['key' => 'r_1-2', 'value' => 'WEMSG.STYLEMANAGER.fwimageratio.r12Label'],
            ['key' => 'r_4-3', 'value' => 'WEMSG.STYLEMANAGER.fwimageratio.r43Label'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveRatio->id, 'fwimageratio', 'WEMSG.STYLEMANAGER.fwimageratio.title', 'WEMSG.STYLEMANAGER.fwimageratio.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Image - zoom
        $cssClasses = [
            ['key' => 'zoomin', 'value' => 'WEMSG.STYLEMANAGER.fwimagezoom.inLabel'],
            ['key' => 'zoomout', 'value' => 'WEMSG.STYLEMANAGER.fwimagezoom.outLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwimagezoom', 'WEMSG.STYLEMANAGER.fwimagezoom.title', 'WEMSG.STYLEMANAGER.fwimagezoom.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Image - fade
        $cssClasses = [
            ['key' => 'fadetocolor', 'value' => 'WEMSG.STYLEMANAGER.fwimagefade.colorLabel'],
            ['key' => 'fadetogrey', 'value' => 'WEMSG.STYLEMANAGER.fwimagefade.greyLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwimagefade', 'WEMSG.STYLEMANAGER.fwimagefade.title', 'WEMSG.STYLEMANAGER.fwimagefade.description', $contentElements, $cssClasses);
        $objStyle->save();
    }

    protected function manageTables(): void
    {
        $contentElements = self::$elements['table'];
        // Table
        $objArchive = $this->fillObjArchive('fwtable', 'WEMSG.STYLEMANAGER.fwtable.tabTitle', 'FramwayTable');
        $objArchive->save();
        // Table - sm
        $cssClasses = [
            ['key' => 'table-sm', 'value' => 'WEMSG.STYLEMANAGER.fwtablesm.label'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwtablesm', 'WEMSG.STYLEMANAGER.fwtablesm.title', 'WEMSG.STYLEMANAGER.fwtablesm.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Table - border
        $cssClasses = [
            ['key' => 'table-bordered', 'value' => 'WEMSG.STYLEMANAGER.fwtableborder.borderedLabel'],
            ['key' => 'table-borderless', 'value' => 'WEMSG.STYLEMANAGER.fwtableborder.borderlessLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwtableborder', 'WEMSG.STYLEMANAGER.fwtableborder.title', 'WEMSG.STYLEMANAGER.fwtableborder.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Table - striped
        $cssClasses = [
            ['key' => 'table-striped', 'value' => 'WEMSG.STYLEMANAGER.fwtablestriped.label'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwtablestriped', 'WEMSG.STYLEMANAGER.fwtablestriped.title', 'WEMSG.STYLEMANAGER.fwtablestriped.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Table - hover
        $cssClasses = [
            ['key' => 'table-hover', 'value' => 'WEMSG.STYLEMANAGER.fwtablehover.label'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwtablehover', 'WEMSG.STYLEMANAGER.fwtablehover.title', 'WEMSG.STYLEMANAGER.fwtablehover.description', $contentElements, $cssClasses);
        $objStyle->save();
    }

    protected function manageBackgrounds(): void
    {
        $contentElements = self::$elements['background'];
        // Background
        $objArchive = $this->fillObjArchive('fwbackground', 'WEMSG.STYLEMANAGER.fwbackground.tabTitle', 'FramwayBackground');
        $objArchive->save();
        // Background - background
        $cssClasses = [
            ['key' => 'bg-primary', 'value' => 'WEMSG.STYLEMANAGER.fwbackgroundcolor.bgPrimaryLabel'],
            ['key' => 'bg-secondary', 'value' => 'WEMSG.STYLEMANAGER.fwbackgroundcolor.bgSecondaryLabel'],
            ['key' => 'bg-success', 'value' => 'WEMSG.STYLEMANAGER.fwbackgroundcolor.bgSuccessLabel'],
            ['key' => 'bg-error', 'value' => 'WEMSG.STYLEMANAGER.fwbackgroundcolor.bgErrorLabel'],
            ['key' => 'bg-warning', 'value' => 'WEMSG.STYLEMANAGER.fwbackgroundcolor.bgWarningLabel'],
        ];
        $colors = $this->configurationThemeManager->load()->getColors();
        foreach ($colors as $name => $hexa) {
            $cssClasses[] = [
                'key' => 'bg-'.$name,
                'value' => sprintf('WEMSG.STYLEMANAGER.fwbackgroundcolor.bgColorLabel (WEMSG.FRAMWAY.COLORS.%s)', $name),
            ];
        }
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwbackgroundcolor', 'WEMSG.STYLEMANAGER.fwbackgroundcolor.title', 'WEMSG.STYLEMANAGER.fwbackgroundcolor.description', $contentElements, $cssClasses);
        $objStyle->save();
    }

    protected function manageButtons(): void
    {
        $contentElements = self::$elements['button'];
        // Buttons
        $objArchive = $this->fillObjArchive('fwbutton', 'WEMSG.STYLEMANAGER.fwbutton.tabTitle', 'FramwayButton');
        $objArchive->save();
        // Buttons - size
        $cssClasses = [
            ['key' => 'btn', 'value' => 'WEMSG.STYLEMANAGER.fwbuttonsize.sizeLabel'],
            ['key' => 'btn-sm', 'value' => 'WEMSG.STYLEMANAGER.fwbuttonsize.sizeSmLabel'],
            ['key' => 'btn-lg', 'value' => 'WEMSG.STYLEMANAGER.fwbuttonsize.sizeLgLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwbuttonsize', 'WEMSG.STYLEMANAGER.fwbuttonsize.title', 'WEMSG.STYLEMANAGER.fwbuttonsize.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Buttons - background
        $cssClasses = [
            ['key' => 'btn-bg-primary', 'value' => 'WEMSG.STYLEMANAGER.fwbuttonbackground.bgPrimaryLabel'],
            ['key' => 'btn-bg-secondary', 'value' => 'WEMSG.STYLEMANAGER.fwbuttonbackground.bgSecondaryLabel'],
            ['key' => 'btn-bg-success', 'value' => 'WEMSG.STYLEMANAGER.fwbuttonbackground.bgSuccessLabel'],
            ['key' => 'btn-bg-error', 'value' => 'WEMSG.STYLEMANAGER.fwbuttonbackground.bgErrorLabel'],
            ['key' => 'btn-bg-warning', 'value' => 'WEMSG.STYLEMANAGER.fwbuttonbackground.bgWarningLabel'],
        ];

        $colors = $this->configurationThemeManager->load()->getColors();
        foreach ($colors as $name => $hexa) {
            $cssClasses[] = [
                'key' => 'btn-bg-'.$name,
                'value' => sprintf('WEMSG.STYLEMANAGER.fwbuttonbackground.bgColorLabel (WEMSG.FRAMWAY.COLORS.%s)', $name),
            ];
        }
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwbuttonbackground', 'WEMSG.STYLEMANAGER.fwbuttonbackground.title', 'WEMSG.STYLEMANAGER.fwbuttonbackground.description', $contentElements, $cssClasses);
        $objStyle->save();
        // Buttons - border
        $cssClasses = [
            ['key' => 'btn-bd-primary', 'value' => 'WEMSG.STYLEMANAGER.fwbuttonborder.bdPrimaryLabel'],
            ['key' => 'btn-bd-secondary', 'value' => 'WEMSG.STYLEMANAGER.fwbuttonborder.bdSecondaryLabel'],
            ['key' => 'btn-bd-success', 'value' => 'WEMSG.STYLEMANAGER.fwbuttonborder.bdSuccessLabel'],
            ['key' => 'btn-bd-error', 'value' => 'WEMSG.STYLEMANAGER.fwbuttonborder.bdErrorLabel'],
            ['key' => 'btn-bd-warning', 'value' => 'WEMSG.STYLEMANAGER.fwbuttonborder.bdWarningLabel'],
        ];
        foreach ($colors as $name => $hexa) {
            $cssClasses[] = [
                'key' => 'btn-bd-'.$name,
                'value' => sprintf('WEMSG.STYLEMANAGER.fwbuttonborder.bdColorLabel (WEMSG.FRAMWAY.COLORS.%s)', $name),
            ];
        }
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwbuttonborder', 'WEMSG.STYLEMANAGER.fwbuttonborder.title', 'WEMSG.STYLEMANAGER.fwbuttonborder.description', $contentElements, $cssClasses);
        $objStyle->save();
    }

    protected function manageSeparators(): void
    {
        $contentElements = self::$elements['separator'];
        // separators
        $objArchive = $this->fillObjArchive('fwseparator', 'WEMSG.STYLEMANAGER.fwseparator.tabTitle', 'FramwaySeparator');
        $objArchive->save();
        // separators - top
        $cssClasses = [
            ['key' => 'sep-top', 'value' => 'WEMSG.STYLEMANAGER.fwseparatortop.label'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwseparatortop', 'WEMSG.STYLEMANAGER.fwseparatortop.title', 'WEMSG.STYLEMANAGER.fwseparatortop.description', $contentElements, $cssClasses);
        $objStyle->save();
        // separators - bottom
        $cssClasses = [
            ['key' => 'sep-bottom', 'value' => 'WEMSG.STYLEMANAGER.fwseparatorbottom.label'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwseparatorbottom', 'WEMSG.STYLEMANAGER.fwseparatorbottom.title', 'WEMSG.STYLEMANAGER.fwseparatorbottom.description', $contentElements, $cssClasses);
        $objStyle->save();
        // separators - left
        $cssClasses = [
            ['key' => 'sep-left', 'value' => 'WEMSG.STYLEMANAGER.fwseparatorleft.label'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwseparatorleft', 'WEMSG.STYLEMANAGER.fwseparatorleft.title', 'WEMSG.STYLEMANAGER.fwseparatorleft.description', $contentElements, $cssClasses);
        $objStyle->save();
        // separators - right
        $cssClasses = [
            ['key' => 'sep-right', 'value' => 'WEMSG.STYLEMANAGER.fwseparatorright.label'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwseparatorright', 'WEMSG.STYLEMANAGER.fwseparatorright.title', 'WEMSG.STYLEMANAGER.fwseparatorright.description', $contentElements, $cssClasses);
        $objStyle->save();
    }

    protected function manageMargins(): void
    {
        $contentElements = self::$elements['margin'];
        // margins
        $objArchive = $this->fillObjArchive('fwmargin', 'WEMSG.STYLEMANAGER.fwmargin.tabTitle', 'FramwayMargin');
        $objArchive->save();
        // margins - top
        $cssClasses = [
            ['key' => 'm-top-0', 'value' => 'WEMSG.STYLEMANAGER.fwmargintop.noLabel'],
            ['key' => 'm-top', 'value' => 'WEMSG.STYLEMANAGER.fwmargintop.label'],
            ['key' => 'm-top-x2', 'value' => 'WEMSG.STYLEMANAGER.fwmargintop.doubleLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwmargintop', 'WEMSG.STYLEMANAGER.fwmargintop.title', 'WEMSG.STYLEMANAGER.fwmargintop.description', $contentElements, $cssClasses);
        $objStyle->save();
        // margins - bottom
        $cssClasses = [
            ['key' => 'm-bottom-0', 'value' => 'WEMSG.STYLEMANAGER.fwmarginbottom.noLabel'],
            ['key' => 'm-bottom', 'value' => 'WEMSG.STYLEMANAGER.fwmarginbottom.label'],
            ['key' => 'm-bottom-x2', 'value' => 'WEMSG.STYLEMANAGER.fwmarginbottom.doubleLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwmarginbottom', 'WEMSG.STYLEMANAGER.fwmarginbottom.title', 'WEMSG.STYLEMANAGER.fwmarginbottom.description', $contentElements, $cssClasses);
        $objStyle->save();
        // margins - left
        $cssClasses = [
            ['key' => 'm-left-0', 'value' => 'WEMSG.STYLEMANAGER.fwmarginleft.noLabel'],
            ['key' => 'm-left', 'value' => 'WEMSG.STYLEMANAGER.fwmarginleft.label'],
            ['key' => 'm-left-x2', 'value' => 'WEMSG.STYLEMANAGER.fwmarginleft.doubleLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwmarginleft', 'WEMSG.STYLEMANAGER.fwmarginleft.title', 'WEMSG.STYLEMANAGER.fwmarginleft.description', $contentElements, $cssClasses);
        $objStyle->save();
        // margins - right
        $cssClasses = [
            ['key' => 'm-right-0', 'value' => 'WEMSG.STYLEMANAGER.fwmarginright.noLabel'],
            ['key' => 'm-right', 'value' => 'WEMSG.STYLEMANAGER.fwmarginright.label'],
            ['key' => 'm-right-x2', 'value' => 'WEMSG.STYLEMANAGER.fwmarginright.doubleLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwmarginright', 'WEMSG.STYLEMANAGER.fwmarginright.title', 'WEMSG.STYLEMANAGER.fwmarginright.description', $contentElements, $cssClasses);
        $objStyle->save();
    }

    private function fillObjStyle($pid, $alias, $titleKey, string $descriptioneKey, array $contentElements, array $cssClasses): StyleManagerModel
    {
        $objStyle = StyleManagerModel::findByAliasAndPid($alias, $pid) ?? new StyleManagerModel();
        $objStyle->pid = $pid;
        $objStyle->title = $titleKey;
        $objStyle->alias = $alias;
        $objStyle->blankOption = true;
        $objStyle->chosen = true;
        $objStyle->tstamp = time();
        $objStyle->contentElements = serialize($contentElements);
        $objStyle->extendContentElement = true;
        $objStyle->description = $descriptioneKey;
        $objStyle->cssClasses = serialize($cssClasses);

        return $objStyle;
    }

    private function fillObjArchive(string $alias, string $titleKey, ?string $groupAlias = 'Framway'): StyleManagerArchiveModel
    {
        $objArchive = StyleManagerArchiveModel::findByIdentifier($alias);
        if (!$objArchive) {
            $objArchive = new StyleManagerArchiveModel();
        } elseif (\Contao\Model\Collection::class === \get_class($objArchive)) {
            $objArchive = $objArchive->first()->current();
        }

        $objArchive->title = $titleKey;
        $objArchive->identifier = $alias;
        $objArchive->groupAlias = $groupAlias;
        $objArchive->tstamp = time();

        return $objArchive;
    }
}
