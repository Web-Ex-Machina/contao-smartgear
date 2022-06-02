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
use WEM\SmartgearBundle\Config\Manager\FramwayCombined as configurationFramwayCombinedManager;
use WEM\SmartgearBundle\Migrations\V1_0_0\MigrationAbstract;

class Migration extends MigrationAbstract
{
    protected static $name = 'Configures CSS classes';
    protected static $description = 'Configures CSS classes available for contents';
    protected static $version = '1.0.0';
    protected static $translation_key = 'WEMSG.MIGRATIONS.V1_0_0_M202203231730';
    /** @var configurationFramwayCombinedManager */
    protected $configurationFramwayCombinedManager;

    protected static $elements = [
        'margin' => ['contentElements' => ['headline', 'text', 'table', 'rsce_listIcons', 'rsce_quote', 'accordionStart', 'accordionSingle', 'sliderStart', 'hyperlink', 'image', 'player', 'youtube', 'vimeo', 'downloads', 'rsce_timeline', 'grid-start', 'rsce_accordion', 'rsce_counter', 'rsce_hero', 'rsce_heroStart', 'rsce_priceCards', 'rsce_slider', 'rsce_tabs', 'rsce_testimonials', 'rsce_notations', 'rsce_pdfViewer', 'rsce_blockCard']], //, 'accordionStop', 'grid-stop', 'sliderStop' , 'rsce_heroStop', 'rsce_gridGallery'
        'button' => ['contentElements' => ['hyperlink'], 'formFields' => ['submit']],
        'button_manual' => ['contentElements' => ['rsce_pdfViewer']],
        'background' => ['contentElements' => ['headline', 'text', 'rsce_quote']],
        'separator' => ['contentElements' => ['headline']],
        'table' => ['contentElements' => ['table']],
        'accordion' => ['contentElements' => ['accordionStart', 'rsce_accordion']], //, 'accordionStop]'
        'slider' => ['contentElements' => ['sliderStart', 'rsce_slider', 'rsce_testimonials']], //, 'sliderStop]'
        'slider_image_manual' => ['contentElements' => ['rsce_slider']], //, 'sliderStop]'
        'image_other' => ['contentElements' => ['image']],
        'image_ratio' => ['contentElements' => ['image', 'rsce_quote']],
        'hero' => ['contentElements' => ['rsce_hero', 'rsce_heroStart']], //'rsce_heroStop]'
        'grid_manual' => ['contentElements' => ['rsce_priceCards']], // 'rsce_gridGallery]'
        'griditems_manual' => ['contentElements' => ['rsce_priceCards']], // 'rsce_gridGallery]'
        'priceCards_manual' => ['contentElements' => ['rsce_priceCards']],
        'quote' => ['contentElements' => ['rsce_quote']],
        'blockCard' => ['contentElements' => ['rsce_blockCard']],
    ];
    /** @var array */
    private $archiveIdentifierToKeep = [];
    /** @var array */
    private $styleAliasToKeep = [];

    public function __construct(
        Connection $connection,
        TranslatorInterface $translator,
        CoreConfigurationManager $coreConfigurationManager,
        VersionComparator $versionComparator,
        configurationFramwayCombinedManager $configurationFramwayCombinedManager
    ) {
        parent::__construct($connection, $translator, $coreConfigurationManager, $versionComparator);
        $this->configurationFramwayCombinedManager = $configurationFramwayCombinedManager;
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
        $objArchiveButtonManual = StyleManagerArchiveModel::findByIdentifier('fwbutton_manual');
        $objArchiveSeparator = StyleManagerArchiveModel::findByIdentifier('fwseparator');
        $objArchiveMargin = StyleManagerArchiveModel::findByIdentifier('fwmargin');
        $objArchiveTable = StyleManagerArchiveModel::findByIdentifier('fwtable');
        $objArchiveImage = StyleManagerArchiveModel::findByIdentifier('fwimage');
        $objArchiveImageRatio = StyleManagerArchiveModel::findByIdentifier('fwimageratio');
        $objArchiveSlider = StyleManagerArchiveModel::findByIdentifier('fwslider');
        $objArchiveSliderNav = StyleManagerArchiveModel::findByIdentifier('fwslidernav');
        $objArchiveSliderContent = StyleManagerArchiveModel::findByIdentifier('fwslidercontent');
        $objArchiveSliderTitle = StyleManagerArchiveModel::findByIdentifier('fwslidertitle');
        $objArchiveSliderImgManual = StyleManagerArchiveModel::findByIdentifier('fwsliderimg_manual');

        $objArchiveHero = StyleManagerArchiveModel::findByIdentifier('fwhero');
        $objArchiveHeroImg = StyleManagerArchiveModel::findByIdentifier('fwheroimg');

        $objArchiveGridManual = StyleManagerArchiveModel::findByIdentifier('fwgrid_manual');
        $objArchiveGridXLManual = StyleManagerArchiveModel::findByIdentifier('fwgridxl_manual');
        $objArchiveGridLGManual = StyleManagerArchiveModel::findByIdentifier('fwgridlg_manual');
        $objArchiveGridMDManual = StyleManagerArchiveModel::findByIdentifier('fwgridmd_manual');
        $objArchiveGridSMManual = StyleManagerArchiveModel::findByIdentifier('fwgridsm_manual');
        $objArchiveGridXSManual = StyleManagerArchiveModel::findByIdentifier('fwgridxs_manual');
        $objArchiveGridXXSManual = StyleManagerArchiveModel::findByIdentifier('fwgridxxs_manual');

        $objArchiveGridItemManual = StyleManagerArchiveModel::findByIdentifier('fwgriditem_manual');
        $objArchiveGridItemXLManual = StyleManagerArchiveModel::findByIdentifier('fwgriditemxl_manual');
        $objArchiveGridItemLGManual = StyleManagerArchiveModel::findByIdentifier('fwgriditemlg_manual');
        $objArchiveGridItemMDManual = StyleManagerArchiveModel::findByIdentifier('fwgriditemmd_manual');
        $objArchiveGridItemSMManual = StyleManagerArchiveModel::findByIdentifier('fwgriditemsm_manual');
        $objArchiveGridItemXSManual = StyleManagerArchiveModel::findByIdentifier('fwgriditemxs_manual');
        $objArchiveGridItemXXSManual = StyleManagerArchiveModel::findByIdentifier('fwgriditemxxs_manual');

        $objArchivePriceCardManual = StyleManagerArchiveModel::findByIdentifier('fwpricecard_manual');
        $objArchiveQuote = StyleManagerArchiveModel::findByIdentifier('fwquote');

        $objArchiveBlockCardText = StyleManagerArchiveModel::findByIdentifier('fwblockcardtext');
        $objArchiveBlockCardBg = StyleManagerArchiveModel::findByIdentifier('fwblockcardbg');

        if (null === $objArchiveBackground
        && null !== $objArchiveButton
        && null !== $objArchiveButtonManual
        && null !== $objArchiveSeparator
        && null !== $objArchiveMargin
        && null !== $objArchiveTable
        && null !== $objArchiveImage
        && null !== $objArchiveImageRatio
        && null !== $objArchiveSlider
        && null !== $objArchiveSliderNav
        && null !== $objArchiveSliderImgManual
        && null !== $objArchiveSliderContent
        && null !== $objArchiveSliderTitle
        && null !== $objArchiveHero
        && null !== $objArchiveHeroImg
        && null !== $objArchiveGridManual
        && null !== $objArchiveGridXLManual
        && null !== $objArchiveGridLGManual
        && null !== $objArchiveGridMDManual
        && null !== $objArchiveGridSMManual
        && null !== $objArchiveGridXSManual
        && null !== $objArchiveGridXXSManual
        && null !== $objArchiveGridItemManual
        && null !== $objArchiveGridItemXLManual
        && null !== $objArchiveGridItemLGManual
        && null !== $objArchiveGridItemMDManual
        && null !== $objArchiveGridItemSMManual
        && null !== $objArchiveGridItemXSManual
        && null !== $objArchiveGridItemXXSManual
        && null !== $objArchivePriceCardManual
        && null !== $objArchiveQuote
        && null !== $objArchiveBlockCardText
        && null !== $objArchiveBlockCardBg
        ) {
            if (null !== StyleManagerModel::findByAliasAndPid('fwbackgroundcolor', $objArchiveBackground->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwbuttonsize', $objArchiveButton->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwbuttonbackground', $objArchiveButton->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwbuttonborder', $objArchiveButton->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwbuttonsize', $objArchiveButtonManual->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwbuttonbackground', $objArchiveButtonManual->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwbuttonborder', $objArchiveButtonManual->id)
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
            && null !== StyleManagerModel::findByAliasAndPid('fwpricecardft', $objArchivePriceCardManual->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwpricecardbg', $objArchivePriceCardManual->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwpricecardcontent', $objArchivePriceCardManual->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwpricecardmain', $objArchivePriceCardManual->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgriditemcolsspan', $objArchiveGridItemManual->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgriditemrowsspan', $objArchiveGridItemManual->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgriditemcolsspanxl', $objArchiveGridItemXLManual->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgriditemrowsspanxl', $objArchiveGridItemXLManual->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgriditemcolsspanlg', $objArchiveGridItemLGManual->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgriditemrowsspanlg', $objArchiveGridItemLGManual->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgriditemcolsspanmd', $objArchiveGridItemMDManual->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgriditemrowsspanmd', $objArchiveGridItemMDManual->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgriditemcolsspansm', $objArchiveGridItemSMManual->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgriditemrowsspansm', $objArchiveGridItemSMManual->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgriditemcolsspanxs', $objArchiveGridItemXSManual->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgriditemrowsspanxs', $objArchiveGridItemXSManual->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgriditemcolsspanxxs', $objArchiveGridItemXXSManual->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgriditemrowsspanxxs', $objArchiveGridItemXXSManual->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridgap', $objArchiveGridManual->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridcols', $objArchiveGridManual->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridrows', $objArchiveGridManual->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridcolsxl', $objArchiveGridXLManual->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridrowsxl', $objArchiveGridXLManual->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridcolslg', $objArchiveGridLGManual->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridrowslg', $objArchiveGridLGManual->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridcolsmd', $objArchiveGridMDManual->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridrowsmd', $objArchiveGridMDManual->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridcolssm', $objArchiveGridSMManual->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridrowssm', $objArchiveGridSMManual->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridcolsxs', $objArchiveGridXSManual->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridrowsxs', $objArchiveGridXSManual->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridcolsxxs', $objArchiveGridXXSManual->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridrowsxxs', $objArchiveGridXXSManual->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwheroimgvertical', $objArchiveHeroImg->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwheroimghorizontal', $objArchiveHeroImg->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwheroft', $objArchiveHero->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwherocontentbg', $objArchiveHero->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwherocontentbgopacity', $objArchiveHero->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwslidernav', $objArchiveSliderNav->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwslidernavvertical', $objArchiveSliderNav->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwslidernavhorizontal', $objArchiveSliderNav->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwsliderimgvertical', $objArchiveSliderImgManual->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwsliderimghorizontal', $objArchiveSliderImgManual->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwsliderimgopacity', $objArchiveSliderImgManual->id)
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
            && null !== StyleManagerModel::findByAliasAndPid('fwquoteimgh', $objArchiveQuote->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwquoteimgv', $objArchiveQuote->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwblockcardft', $objArchiveBlockCardText->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwblockcardtextalign', $objArchiveBlockCardText->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwblockcardcontentbg', $objArchiveBlockCardBg->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwblockcardcontentbgopacity', $objArchiveBlockCardBg->id)
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
            $this->manageButtons('_manual', true);
            $result->addLog($this->translator->trans($this->buildTranslationKey('doAddCSSButtons'), [], 'contao_default'));
            $this->manageBackgrounds();
            $result->addLog($this->translator->trans($this->buildTranslationKey('doAddCSSBackgrounds'), [], 'contao_default'));
            $this->manageTables();
            $result->addLog($this->translator->trans($this->buildTranslationKey('doAddCSSTables'), [], 'contao_default'));
            $this->manageImages();
            $this->manageImagesRatio();
            $result->addLog($this->translator->trans($this->buildTranslationKey('doAddCSSImages'), [], 'contao_default'));
            $this->manageSliders();
            $this->manageSlidersImages('_manual', true);
            $result->addLog($this->translator->trans($this->buildTranslationKey('doAddCSSSliders'), [], 'contao_default'));
            $this->manageHero();
            $result->addLog($this->translator->trans($this->buildTranslationKey('doAddCSSHero'), [], 'contao_default'));
            $this->manageGrids('_manual', true);
            $result->addLog($this->translator->trans($this->buildTranslationKey('doAddCSSGrids'), [], 'contao_default'));
            $this->manageGridItems('_manual', true);
            $result->addLog($this->translator->trans($this->buildTranslationKey('doAddCSSGridItems'), [], 'contao_default'));
            $this->managePriceCards('_manual', true);
            $result->addLog($this->translator->trans($this->buildTranslationKey('doAddCSSPriceCards'), [], 'contao_default'));
            $this->manageQuote();
            $result->addLog($this->translator->trans($this->buildTranslationKey('doAddCSSQuotes'), [], 'contao_default'));
            $this->manageBlockCard();
            $result->addLog($this->translator->trans($this->buildTranslationKey('doAddCSSBlockCard'), [], 'contao_default'));
            $this->deleteUnusedStyles();
            $this->deleteUnusedArchives();
            $this->deleteOrphanStyles();
            $result
                ->setStatus(Result::STATUS_SUCCESS)
            ;
        } catch (\Exception $e) {
            $result
                ->setStatus(Result::STATUS_FAIL)
                ->addLog($e->getMessage())
            ;
        }

        return $result;
    }

    protected function deleteUnusedStyles(): void
    {
        $styles = StyleManagerModel::findAll();

        if ($styles) {
            foreach ($styles as $style) {
                if ('fw' === substr($style->alias, 0, 2)
                && (
                    !\in_array($style->alias, $this->styleAliasToKeep, true)
                    ||
                    (
                        \in_array($style->alias, $this->styleAliasToKeep, true)
                        &&
                        !\array_key_exists($style->id, $this->styleAliasToKeep)
                    )
                )
                ) {
                    $style->delete();
                }
            }
        }
    }

    protected function deleteUnusedArchives(): void
    {
        $archives = StyleManagerArchiveModel::findAll();

        if ($archives) {
            foreach ($archives as $archive) {
                if ('fw' === substr($archive->identifier, 0, 2)
                && (
                    !\in_array($archive->identifier, $this->archiveIdentifierToKeep, true)
                    ||
                    (
                        \in_array($archive->identifier, $this->archiveIdentifierToKeep, true)
                        &&
                        !\array_key_exists($archive->id, $this->archiveIdentifierToKeep)
                    )
                )
                ) {
                    $archive->delete();
                }
            }
        }
    }

    protected function deleteOrphanStyles(): void
    {
        $styles = StyleManagerModel::findAll();
        $archives = StyleManagerArchiveModel::findAll();
        $archivesIds = $archives->fetchEach('id');

        if ($styles) {
            foreach ($styles as $style) {
                if ('fw' === substr($style->alias, 0, 2) && !\in_array($style->pid, $archivesIds, true)) {
                    $style->delete();
                }
            }
        }
    }

    protected function manageBlockCard(?string $suffix = '', ?bool $passToTemplate = false): void
    {
        $contentElements = self::$elements['blockCard'.$suffix];
        // Block card
        $objArchiveText = $this->fillObjArchive('fwblockcardtext'.$suffix, 'WEMSG.STYLEMANAGER.fwblockcardtext.tabTitle', 'FramwayBlockCard');
        $objArchiveText->save();
        $objArchiveBg = $this->fillObjArchive('fwblockcardbg'.$suffix, 'WEMSG.STYLEMANAGER.fwblockcardbg.tabTitle', 'FramwayBlockCard');
        $objArchiveBg->save();

        // Block card - text color
        $cssClasses = $this->buildMeaningfulColorsCssClasses('ft-%s', 'fwblockcardft');
        $cssClasses = array_merge($cssClasses, $this->buildRawColorsCssClasses('ft-%s', 'fwblockcardft'));
        $objStyle = $this->fillObjStyle($objArchiveText->id, 'fwblockcardft'.$suffix, 'WEMSG.STYLEMANAGER.fwblockcardft.title', 'WEMSG.STYLEMANAGER.fwblockcardft.description', $contentElements, $cssClasses, $passToTemplate);
        $objStyle->save();

        // Block card - text align
        $cssClasses = [
            ['key' => 'txt-center', 'value' => 'WEMSG.STYLEMANAGER.fwblockcardtextalign.centerLabel'],
            ['key' => 'txt-right', 'value' => 'WEMSG.STYLEMANAGER.fwblockcardtextalign.rightLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveText->id, 'fwblockcardtextalign'.$suffix, 'WEMSG.STYLEMANAGER.fwblockcardtextalign.title', 'WEMSG.STYLEMANAGER.fwblockcardtextalign.description', $contentElements, $cssClasses, $passToTemplate);
        $objStyle->save();

        // Block card - bg color
        $cssClasses = $this->buildMeaningfulColorsCssClasses('content__bg--%s', 'fwblockcardcontentbg');
        $cssClasses = array_merge($cssClasses, $this->buildRawColorsCssClasses('content__bg--%s', 'fwblockcardcontentbg'));
        $objStyle = $this->fillObjStyle($objArchiveBg->id, 'fwblockcardcontentbg'.$suffix, 'WEMSG.STYLEMANAGER.fwblockcardcontentbg.title', 'WEMSG.STYLEMANAGER.fwblockcardcontentbg.description', $contentElements, $cssClasses, $passToTemplate);
        $objStyle->save();

        // Block card - bgopacity
        $cssClasses = $this->buildMultipleCssClasses('content__bg__opacity--%s', 'fwblockcardcontentbgopacity', 1, 10);
        $objStyle = $this->fillObjStyle($objArchiveBg->id, 'fwblockcardcontentbgopacity'.$suffix, 'WEMSG.STYLEMANAGER.fwblockcardcontentbgopacity.title', 'WEMSG.STYLEMANAGER.fwblockcardcontentbgopacity.description', $contentElements, $cssClasses, $passToTemplate);
        $objStyle->save();
    }

    protected function manageQuote(?string $suffix = '', ?bool $passToTemplate = false): void
    {
        $contentElements = self::$elements['quote'.$suffix];
        // Quote
        $objArchive = $this->fillObjArchive('fwquote'.$suffix, 'WEMSG.STYLEMANAGER.fwquote.tabTitle', 'FramwayQuote');

        // Quote - imgh
        $cssClasses = [
            ['key' => 'img--left', 'value' => 'WEMSG.STYLEMANAGER.fwquoteimgh.leftLabel'],
            ['key' => 'img--right', 'value' => 'WEMSG.STYLEMANAGER.fwquoteimgh.rightLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwquoteimgh'.$suffix, 'WEMSG.STYLEMANAGER.fwquoteimgh.title', 'WEMSG.STYLEMANAGER.fwquoteimgh.description', $contentElements, $cssClasses, $passToTemplate);

        // Quote - imgv
        $cssClasses = [
            ['key' => 'img--top', 'value' => 'WEMSG.STYLEMANAGER.fwquoteimgv.topLabel'],
            ['key' => 'img--bottom', 'value' => 'WEMSG.STYLEMANAGER.fwquoteimgv.bottomLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwquoteimgv'.$suffix, 'WEMSG.STYLEMANAGER.fwquoteimgv.title', 'WEMSG.STYLEMANAGER.fwquoteimgv.description', $contentElements, $cssClasses, $passToTemplate);
    }

    protected function managePriceCards(?string $suffix = '', ?bool $passToTemplate = false): void
    {
        $contentElements = self::$elements['priceCards'.$suffix];
        // Price card
        $objArchive = $this->fillObjArchive('fwpricecard'.$suffix, 'WEMSG.STYLEMANAGER.fwpricecard.tabTitle', 'FramwayPriceCard');

        // Price card - text color
        $cssClasses = $this->buildMeaningfulColorsCssClasses('ft-%s', 'fwpricecardft');
        $cssClasses = array_merge($cssClasses, $this->buildRawColorsCssClasses('ft-%s', 'fwpricecardft'));
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwpricecardft'.$suffix, 'WEMSG.STYLEMANAGER.fwpricecardft.title', 'WEMSG.STYLEMANAGER.fwpricecardft.description', $contentElements, $cssClasses, $passToTemplate);
        // Price card - bg color
        $cssClasses = $this->buildMeaningfulColorsCssClasses('bg--%s', 'fwpricecardbg');
        $cssClasses = array_merge($cssClasses, $this->buildRawColorsCssClasses('bg--%s', 'fwpricecardbg'));
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwpricecardbg'.$suffix, 'WEMSG.STYLEMANAGER.fwpricecardbg.title', 'WEMSG.STYLEMANAGER.fwpricecardbg.description', $contentElements, $cssClasses, $passToTemplate);
        // Price card - content color
        $cssClasses = $this->buildMeaningfulColorsCssClasses('content--%s', 'fwpricecardcontent');
        $cssClasses = array_merge($cssClasses, $this->buildRawColorsCssClasses('content--%s', 'fwpricecardcontent'));
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwpricecardcontent'.$suffix, 'WEMSG.STYLEMANAGER.fwpricecardcontent.title', 'WEMSG.STYLEMANAGER.fwpricecardcontent.description', $contentElements, $cssClasses, $passToTemplate);
        // Price card - main
        $cssClasses = [
            ['key' => 'main', 'value' => 'WEMSG.STYLEMANAGER.fwpricecardmain.label'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwpricecardmain'.$suffix, 'WEMSG.STYLEMANAGER.fwpricecardmain.title', 'WEMSG.STYLEMANAGER.fwpricecardmain.description', $contentElements, $cssClasses, $passToTemplate);
    }

    protected function manageGridItems(?string $suffix = '', ?bool $passToTemplate = false): void
    {
        $contentElements = self::$elements['griditems'.$suffix];
        // Grid Items
        $objArchive = $this->fillObjArchive('fwgriditem'.$suffix, 'WEMSG.STYLEMANAGER.fwgriditem.tabTitle', 'FramwayGridItem');
        $objArchiveXL = $this->fillObjArchive('fwgriditemxl'.$suffix, 'WEMSG.STYLEMANAGER.fwgriditemxl.tabTitle', 'FramwayGridItem');
        $objArchiveLG = $this->fillObjArchive('fwgriditemlg'.$suffix, 'WEMSG.STYLEMANAGER.fwgriditemlg.tabTitle', 'FramwayGridItem');
        $objArchiveMD = $this->fillObjArchive('fwgriditemmd'.$suffix, 'WEMSG.STYLEMANAGER.fwgriditemmd.tabTitle', 'FramwayGridItem');
        $objArchiveSM = $this->fillObjArchive('fwgriditemsm'.$suffix, 'WEMSG.STYLEMANAGER.fwgriditemsm.tabTitle', 'FramwayGridItem');
        $objArchiveXS = $this->fillObjArchive('fwgriditemxs'.$suffix, 'WEMSG.STYLEMANAGER.fwgriditemxs.tabTitle', 'FramwayGridItem');
        $objArchiveXXS = $this->fillObjArchive('fwgriditemxxs'.$suffix, 'WEMSG.STYLEMANAGER.fwgriditemxxs.tabTitle', 'FramwayGridItem');

        // Grid item - cols
        $cssClasses = $this->buildMultipleCssClasses('cols-span-%s', 'fwgriditemcolsspan', 1, 12);
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwgriditemcolsspan'.$suffix, 'WEMSG.STYLEMANAGER.fwgriditemcolsspan.title', 'WEMSG.STYLEMANAGER.fwgriditemcolsspan.description', $contentElements, $cssClasses, $passToTemplate);
        // Grid item - rows
        $cssClasses = $this->buildMultipleCssClasses('rows-span-%s', 'fwgriditemrowsspan', 1, 12);
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwgriditemrowsspan'.$suffix, 'WEMSG.STYLEMANAGER.fwgriditemrowsspan.title', 'WEMSG.STYLEMANAGER.fwgriditemrowsspan.description', $contentElements, $cssClasses, $passToTemplate);

        // Grid item - colsspanxl
        $cssClasses = $this->buildMultipleCssClasses('cols-span-xl-%s', 'fwgriditemcolsspanxl', 1, 12);
        $objStyle = $this->fillObjStyle($objArchiveXL->id, 'fwgriditemcolsspanxl'.$suffix, 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxl.title', 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxl.description', $contentElements, $cssClasses, $passToTemplate);
        // Grid item - rowsspanxl
        $cssClasses = $this->buildMultipleCssClasses('rows-span-xl-%s', 'fwgriditemrowsspanxl', 1, 12);
        $objStyle = $this->fillObjStyle($objArchiveXL->id, 'fwgriditemrowsspanxl'.$suffix, 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxl.title', 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxl.description', $contentElements, $cssClasses, $passToTemplate);

        // Grid item - colsspanlg
        $cssClasses = $this->buildMultipleCssClasses('cols-span-lg-%s', 'fwgriditemcolsspanlg', 1, 12);
        $objStyle = $this->fillObjStyle($objArchiveLG->id, 'fwgriditemcolsspanlg'.$suffix, 'WEMSG.STYLEMANAGER.fwgriditemcolsspanlg.title', 'WEMSG.STYLEMANAGER.fwgriditemcolsspanlg.description', $contentElements, $cssClasses, $passToTemplate);
        // Grid item - rowsspanlg
        $cssClasses = $this->buildMultipleCssClasses('rows-span-lg-%s', 'fwgriditemrowsspanlg', 1, 12);
        $objStyle = $this->fillObjStyle($objArchiveLG->id, 'fwgriditemrowsspanlg'.$suffix, 'WEMSG.STYLEMANAGER.fwgriditemrowsspanlg.title', 'WEMSG.STYLEMANAGER.fwgriditemrowsspanlg.description', $contentElements, $cssClasses, $passToTemplate);

        // Grid item - colsspanmd
        $cssClasses = $this->buildMultipleCssClasses('cols-span-md-%s', 'fwgriditemcolsspanmd', 1, 12);
        $objStyle = $this->fillObjStyle($objArchiveMD->id, 'fwgriditemcolsspanmd'.$suffix, 'WEMSG.STYLEMANAGER.fwgriditemcolsspanmd.title', 'WEMSG.STYLEMANAGER.fwgriditemcolsspanmd.description', $contentElements, $cssClasses, $passToTemplate);
        // Grid item - rowsspanmd
        $cssClasses = $this->buildMultipleCssClasses('rows-span-md-%s', 'fwgriditemrowsspanmd', 1, 12);
        $objStyle = $this->fillObjStyle($objArchiveMD->id, 'fwgriditemrowsspanmd'.$suffix, 'WEMSG.STYLEMANAGER.fwgriditemrowsspanmd.title', 'WEMSG.STYLEMANAGER.fwgriditemrowsspanmd.description', $contentElements, $cssClasses, $passToTemplate);

        // Grid item - colsspansm
        $cssClasses = $this->buildMultipleCssClasses('cols-span-sm-%s', 'fwgriditemcolsspansm', 1, 12);
        $objStyle = $this->fillObjStyle($objArchiveSM->id, 'fwgriditemcolsspansm'.$suffix, 'WEMSG.STYLEMANAGER.fwgriditemcolsspansm.title', 'WEMSG.STYLEMANAGER.fwgriditemcolsspansm.description', $contentElements, $cssClasses, $passToTemplate);
        // Grid item - rowsspansm
        $cssClasses = $this->buildMultipleCssClasses('rows-span-sm-%s', 'fwgriditemrowsspansm', 1, 12);
        $objStyle = $this->fillObjStyle($objArchiveSM->id, 'fwgriditemrowsspansm'.$suffix, 'WEMSG.STYLEMANAGER.fwgriditemrowsspansm.title', 'WEMSG.STYLEMANAGER.fwgriditemrowsspansm.description', $contentElements, $cssClasses, $passToTemplate);

        // Grid item - colsspanxs
        $cssClasses = $this->buildMultipleCssClasses('cols-span-xs-%s', 'fwgriditemcolsspanxs', 1, 12);
        $objStyle = $this->fillObjStyle($objArchiveXS->id, 'fwgriditemcolsspanxs'.$suffix, 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxs.title', 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxs.description', $contentElements, $cssClasses, $passToTemplate);
        // Grid item - rowsspanxs
        $cssClasses = $this->buildMultipleCssClasses('rows-span-xs-%s', 'fwgriditemrowsspanxs', 1, 12);
        $objStyle = $this->fillObjStyle($objArchiveXS->id, 'fwgriditemrowsspanxs'.$suffix, 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxs.title', 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxs.description', $contentElements, $cssClasses, $passToTemplate);

        // Grid item - colsspanxxs
        $cssClasses = $this->buildMultipleCssClasses('cols-span-xxs-%s', 'fwgriditemcolsspanxxs', 1, 12);
        $objStyle = $this->fillObjStyle($objArchiveXXS->id, 'fwgriditemcolsspanxxs'.$suffix, 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxxs.title', 'WEMSG.STYLEMANAGER.fwgriditemcolsspanxxs.description', $contentElements, $cssClasses, $passToTemplate);
        // Grid item - rowsspanxxs
        $cssClasses = $this->buildMultipleCssClasses('rows-span-xxs-%s', 'fwgriditemrowsspanxxs', 1, 12);
        $objStyle = $this->fillObjStyle($objArchiveXXS->id, 'fwgriditemrowsspanxxs'.$suffix, 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxxs.title', 'WEMSG.STYLEMANAGER.fwgriditemrowsspanxxs.description', $contentElements, $cssClasses, $passToTemplate);
    }

    protected function manageGrids(?string $suffix = '', ?bool $passToTemplate = false): void
    {
        $contentElements = self::$elements['grid'.$suffix];
        // Grid
        $objArchive = $this->fillObjArchive('fwgrid'.$suffix, 'WEMSG.STYLEMANAGER.fwgrid.tabTitle', 'FramwayGrid');
        $objArchiveXL = $this->fillObjArchive('fwgridxl'.$suffix, 'WEMSG.STYLEMANAGER.fwgridxl.tabTitle', 'FramwayGrid');
        $objArchiveLG = $this->fillObjArchive('fwgridlg'.$suffix, 'WEMSG.STYLEMANAGER.fwgridlg.tabTitle', 'FramwayGrid');
        $objArchiveMD = $this->fillObjArchive('fwgridmd'.$suffix, 'WEMSG.STYLEMANAGER.fwgridmd.tabTitle', 'FramwayGrid');
        $objArchiveSM = $this->fillObjArchive('fwgridsm'.$suffix, 'WEMSG.STYLEMANAGER.fwgridsm.tabTitle', 'FramwayGrid');
        $objArchiveXS = $this->fillObjArchive('fwgridxs'.$suffix, 'WEMSG.STYLEMANAGER.fwgridxs.tabTitle', 'FramwayGrid');
        $objArchiveXXS = $this->fillObjArchive('fwgridxxs'.$suffix, 'WEMSG.STYLEMANAGER.fwgridxxs.tabTitle', 'FramwayGrid');
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
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwgridgap'.$suffix, 'WEMSG.STYLEMANAGER.fwgridgap.title', 'WEMSG.STYLEMANAGER.fwgridgap.description', $contentElements, $cssClasses, $passToTemplate);
        // Grid - cols
        $cssClasses = $this->buildMultipleCssClasses('cols-%s', 'fwgridcols', 1, 12);
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwgridcols'.$suffix, 'WEMSG.STYLEMANAGER.fwgridcols.title', 'WEMSG.STYLEMANAGER.fwgridcols.description', $contentElements, $cssClasses, $passToTemplate);
        // Grid - rows
        $cssClasses = $this->buildMultipleCssClasses('rows-%s', 'fwgridrows', 1, 12);
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwgridrows'.$suffix, 'WEMSG.STYLEMANAGER.fwgridrows.title', 'WEMSG.STYLEMANAGER.fwgridrows.description', $contentElements, $cssClasses, $passToTemplate);

        // Grid - colsxl
        $cssClasses = $this->buildMultipleCssClasses('cols-xl-%s', 'fwgridcolsxl', 1, 12);
        $objStyle = $this->fillObjStyle($objArchiveXL->id, 'fwgridcolsxl'.$suffix, 'WEMSG.STYLEMANAGER.fwgridcolsxl.title', 'WEMSG.STYLEMANAGER.fwgridcolsxl.description', $contentElements, $cssClasses, $passToTemplate);
        // Grid - rowsxl
        $cssClasses = $this->buildMultipleCssClasses('rows-xl-%s', 'fwgridrowsxl', 1, 12);
        $objStyle = $this->fillObjStyle($objArchiveXL->id, 'fwgridrowsxl'.$suffix, 'WEMSG.STYLEMANAGER.fwgridrowsxl.title', 'WEMSG.STYLEMANAGER.fwgridrowsxl.description', $contentElements, $cssClasses, $passToTemplate);

        // Grid - colslg
        $cssClasses = $this->buildMultipleCssClasses('cols-lg-%s', 'fwgridcolslg', 1, 12);
        $objStyle = $this->fillObjStyle($objArchiveLG->id, 'fwgridcolslg'.$suffix, 'WEMSG.STYLEMANAGER.fwgridcolslg.title', 'WEMSG.STYLEMANAGER.fwgridcolslg.description', $contentElements, $cssClasses, $passToTemplate);
        // Grid - rowslg
        $cssClasses = $this->buildMultipleCssClasses('rows-lg-%s', 'fwgridrowslg', 1, 12);
        $objStyle = $this->fillObjStyle($objArchiveLG->id, 'fwgridrowslg'.$suffix, 'WEMSG.STYLEMANAGER.fwgridrowslg.title', 'WEMSG.STYLEMANAGER.fwgridrowslg.description', $contentElements, $cssClasses, $passToTemplate);

        // Grid - colsmd
        $cssClasses = $this->buildMultipleCssClasses('cols-md-%s', 'fwgridcolsmd', 1, 12);
        $objStyle = $this->fillObjStyle($objArchiveMD->id, 'fwgridcolsmd'.$suffix, 'WEMSG.STYLEMANAGER.fwgridcolsmd.title', 'WEMSG.STYLEMANAGER.fwgridcolsmd.description', $contentElements, $cssClasses, $passToTemplate);
        // Grid - rowsmd
        $cssClasses = $this->buildMultipleCssClasses('rows-md-%s', 'fwgridrowsmd', 1, 12);
        $objStyle = $this->fillObjStyle($objArchiveMD->id, 'fwgridrowsmd'.$suffix, 'WEMSG.STYLEMANAGER.fwgridrowsmd.title', 'WEMSG.STYLEMANAGER.fwgridrowsmd.description', $contentElements, $cssClasses, $passToTemplate);

        // Grid - colssm
        $cssClasses = $this->buildMultipleCssClasses('cols-sm-%s', 'fwgridcolssm', 1, 12);
        $objStyle = $this->fillObjStyle($objArchiveSM->id, 'fwgridcolssm'.$suffix, 'WEMSG.STYLEMANAGER.fwgridcolssm.title', 'WEMSG.STYLEMANAGER.fwgridcolssm.description', $contentElements, $cssClasses, $passToTemplate);
        // Grid - rowssm
        $cssClasses = $this->buildMultipleCssClasses('rows-sm-%s', 'fwgridrowssm', 1, 12);
        $objStyle = $this->fillObjStyle($objArchiveSM->id, 'fwgridrowssm'.$suffix, 'WEMSG.STYLEMANAGER.fwgridrowssm.title', 'WEMSG.STYLEMANAGER.fwgridrowssm.description', $contentElements, $cssClasses, $passToTemplate);

        // Grid - colsxs
        $cssClasses = $this->buildMultipleCssClasses('cols-xs-%s', 'fwgridcolsxs', 1, 12);
        $objStyle = $this->fillObjStyle($objArchiveXS->id, 'fwgridcolsxs'.$suffix, 'WEMSG.STYLEMANAGER.fwgridcolsxs.title', 'WEMSG.STYLEMANAGER.fwgridcolsxs.description', $contentElements, $cssClasses, $passToTemplate);
        // Grid - rowsxs
        $cssClasses = $this->buildMultipleCssClasses('rows-xs-%s', 'fwgridrowsxs', 1, 12);
        $objStyle = $this->fillObjStyle($objArchiveXS->id, 'fwgridrowsxs'.$suffix, 'WEMSG.STYLEMANAGER.fwgridrowsxs.title', 'WEMSG.STYLEMANAGER.fwgridrowsxs.description', $contentElements, $cssClasses, $passToTemplate);

        // Grid - colsxxs
        $cssClasses = $this->buildMultipleCssClasses('cols-xxs-%s', 'fwgridcolsxxs', 1, 12);
        $objStyle = $this->fillObjStyle($objArchiveXXS->id, 'fwgridcolsxxs'.$suffix, 'WEMSG.STYLEMANAGER.fwgridcolsxxs.title', 'WEMSG.STYLEMANAGER.fwgridcolsxxs.description', $contentElements, $cssClasses, $passToTemplate);
        // Grid - rowsxxs
        $cssClasses = $this->buildMultipleCssClasses('rows-xxs-%s', 'fwgridrowsxxs', 1, 12);
        $objStyle = $this->fillObjStyle($objArchiveXXS->id, 'fwgridrowsxxs'.$suffix, 'WEMSG.STYLEMANAGER.fwgridrowsxxs.title', 'WEMSG.STYLEMANAGER.fwgridrowsxxs.description', $contentElements, $cssClasses, $passToTemplate);
    }

    protected function manageHero(?string $suffix = '', ?bool $passToTemplate = false): void
    {
        $contentElements = self::$elements['hero'.$suffix];
        // Hero
        $objArchive = $this->fillObjArchive('fwhero'.$suffix, 'WEMSG.STYLEMANAGER.fwhero.tabTitle', 'FramwayHero');
        $objArchiveImg = $this->fillObjArchive('fwheroimg'.$suffix, 'WEMSG.STYLEMANAGER.fwheroimg.tabTitle', 'FramwayHero');
        // Hero - imgvertical
        $cssClasses = [
            ['key' => 'img--top', 'value' => 'WEMSG.STYLEMANAGER.fwheroimgvertical.topLabel'],
            ['key' => 'img--bottom', 'value' => 'WEMSG.STYLEMANAGER.fwheroimgvertical.bottomLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveImg->id, 'fwheroimgvertical'.$suffix, 'WEMSG.STYLEMANAGER.fwheroimgvertical.title', 'WEMSG.STYLEMANAGER.fwheroimgvertical.description', $contentElements, $cssClasses, $passToTemplate);
        // Hero - imghorizontal
        $cssClasses = [
            ['key' => 'img--left', 'value' => 'WEMSG.STYLEMANAGER.fwheroimghorizontal.leftLabel'],
            ['key' => 'img--right', 'value' => 'WEMSG.STYLEMANAGER.fwheroimghorizontal.rightLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveImg->id, 'fwheroimghorizontal'.$suffix, 'WEMSG.STYLEMANAGER.fwheroimghorizontal.title', 'WEMSG.STYLEMANAGER.fwheroimghorizontal.description', $contentElements, $cssClasses, $passToTemplate);

        // Hero - text color
        $cssClasses = $this->buildMeaningfulColorsCssClasses('ft-%s', 'fwheroft');
        $cssClasses = array_merge($cssClasses, $this->buildRawColorsCssClasses('ft-%s', 'fwheroft'));
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwheroft'.$suffix, 'WEMSG.STYLEMANAGER.fwheroft.title', 'WEMSG.STYLEMANAGER.fwheroft.description', $contentElements, $cssClasses, $passToTemplate);
        // Hero - bg color
        $cssClasses = $this->buildMeaningfulColorsCssClasses('content__bg--%s', 'fwherocontentbg');
        $cssClasses = array_merge($cssClasses, $this->buildRawColorsCssClasses('content__bg--%s', 'fwherocontentbg'));
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwherocontentbg'.$suffix, 'WEMSG.STYLEMANAGER.fwherocontentbg.title', 'WEMSG.STYLEMANAGER.fwherocontentbg.description', $contentElements, $cssClasses, $passToTemplate);
        // Hero - bgopacity
        $cssClasses = $this->buildMultipleCssClasses('content__bg__opacity--%s', 'fwherocontentbgopacity', 1, 10);
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwherocontentbgopacity'.$suffix, 'WEMSG.STYLEMANAGER.fwherocontentbgopacity.title', 'WEMSG.STYLEMANAGER.fwherocontentbgopacity.description', $contentElements, $cssClasses, $passToTemplate);
    }

    protected function manageSlidersImages(?string $suffix = '', ?bool $passToTemplate = false): void
    {
        $contentElements = self::$elements['slider_image'.$suffix];
        // Slider
        $objArchiveImg = $this->fillObjArchive('fwsliderimg'.$suffix, 'WEMSG.STYLEMANAGER.fwsliderimg.tabTitle', 'FramwaySlider');
        // Slider - imgvertical
        $cssClasses = [
            ['key' => 'img--top', 'value' => 'WEMSG.STYLEMANAGER.fwsliderimgvertical.topLabel'],
            ['key' => 'img--bottom', 'value' => 'WEMSG.STYLEMANAGER.fwsliderimgvertical.bottomLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveImg->id, 'fwsliderimgvertical'.$suffix, 'WEMSG.STYLEMANAGER.fwsliderimgvertical.title', 'WEMSG.STYLEMANAGER.fwsliderimgvertical.description', $contentElements, $cssClasses, $passToTemplate);
        // Slider - imghorizontal
        $cssClasses = [
            ['key' => 'img--left', 'value' => 'WEMSG.STYLEMANAGER.fwsliderimghorizontal.leftLabel'],
            ['key' => 'img--right', 'value' => 'WEMSG.STYLEMANAGER.fwsliderimghorizontal.rightLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveImg->id, 'fwsliderimghorizontal'.$suffix, 'WEMSG.STYLEMANAGER.fwsliderimghorizontal.title', 'WEMSG.STYLEMANAGER.fwsliderimghorizontal.description', $contentElements, $cssClasses, $passToTemplate);
        // Slider - imgopacity
        $cssClasses = $this->buildMultipleCssClasses('img_opacity--%s', 'fwsliderimgopacity', 1, 10);
        $objStyle = $this->fillObjStyle($objArchiveImg->id, 'fwsliderimgopacity'.$suffix, 'WEMSG.STYLEMANAGER.fwsliderimgopacity.title', 'WEMSG.STYLEMANAGER.fwsliderimgopacity.description', $contentElements, $cssClasses, $passToTemplate);
    }

    protected function manageSliders(?string $suffix = '', ?bool $passToTemplate = false): void
    {
        $contentElements = self::$elements['slider'.$suffix];
        // Slider
        $objArchive = $this->fillObjArchive('fwslider'.$suffix, 'WEMSG.STYLEMANAGER.fwslider.tabTitle', 'FramwaySlider');
        $objArchiveNav = $this->fillObjArchive('fwslidernav'.$suffix, 'WEMSG.STYLEMANAGER.fwslidernav.tabTitle', 'FramwaySlider');
        $objArchiveContent = $this->fillObjArchive('fwslidercontent'.$suffix, 'WEMSG.STYLEMANAGER.fwslidercontent.tabTitle', 'FramwaySlider');
        $objArchiveTitle = $this->fillObjArchive('fwslidertitle'.$suffix, 'WEMSG.STYLEMANAGER.fwslidertitle.tabTitle', 'FramwaySlider');
        // Slider - nav
        $cssClasses = [
            ['key' => 'nav--below', 'value' => 'WEMSG.STYLEMANAGER.fwslidernav.belowLabel'],
            ['key' => 'nav--hidden', 'value' => 'WEMSG.STYLEMANAGER.fwslidernav.hiddenLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveNav->id, 'fwslidernav'.$suffix, 'WEMSG.STYLEMANAGER.fwslidernav.title', 'WEMSG.STYLEMANAGER.fwslidernav.description', $contentElements, $cssClasses, $passToTemplate);
        // Slider - navvertical
        $cssClasses = [
            ['key' => 'nav--top', 'value' => 'WEMSG.STYLEMANAGER.fwslidernavvertical.topLabel'],
            ['key' => 'nav--bottom', 'value' => 'WEMSG.STYLEMANAGER.fwslidernavvertical.bottomLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveNav->id, 'fwslidernavvertical'.$suffix, 'WEMSG.STYLEMANAGER.fwslidernavvertical.title', 'WEMSG.STYLEMANAGER.fwslidernavvertical.description', $contentElements, $cssClasses, $passToTemplate);
        // Slider - navhorizontal
        $cssClasses = [
            ['key' => 'nav--left', 'value' => 'WEMSG.STYLEMANAGER.fwslidernavhorizontal.leftLabel'],
            ['key' => 'nav--right', 'value' => 'WEMSG.STYLEMANAGER.fwslidernavhorizontal.rightLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveNav->id, 'fwslidernavhorizontal'.$suffix, 'WEMSG.STYLEMANAGER.fwslidernavhorizontal.title', 'WEMSG.STYLEMANAGER.fwslidernavhorizontal.description', $contentElements, $cssClasses, $passToTemplate);
        // Slider - contentvertical
        $cssClasses = [
            ['key' => 'content--v--top', 'value' => 'WEMSG.STYLEMANAGER.fwslidercontentvertical.topLabel'],
            ['key' => 'content--v--center', 'value' => 'WEMSG.STYLEMANAGER.fwslidercontentvertical.centerLabel'],
            ['key' => 'content--v--bottom', 'value' => 'WEMSG.STYLEMANAGER.fwslidercontentvertical.bottomLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveContent->id, 'fwslidercontentvertical'.$suffix, 'WEMSG.STYLEMANAGER.fwslidercontentvertical.title', 'WEMSG.STYLEMANAGER.fwslidercontentvertical.description', $contentElements, $cssClasses, $passToTemplate);
        // Slider - contenthorizontal
        $cssClasses = [
            ['key' => 'content--h--left', 'value' => 'WEMSG.STYLEMANAGER.fwslidercontenthorizontal.leftLabel'],
            ['key' => 'content--h--center', 'value' => 'WEMSG.STYLEMANAGER.fwslidercontenthorizontal.centerLabel'],
            ['key' => 'content--h--right', 'value' => 'WEMSG.STYLEMANAGER.fwslidercontenthorizontal.rightLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchiveContent->id, 'fwslidercontenthorizontal'.$suffix, 'WEMSG.STYLEMANAGER.fwslidercontenthorizontal.title', 'WEMSG.STYLEMANAGER.fwslidercontenthorizontal.description', $contentElements, $cssClasses, $passToTemplate);
        // Slider - title
        $cssClasses = $this->buildMultipleCssClasses('title--%s', 'fwslidertitle', 1, 4);
        $objStyle = $this->fillObjStyle($objArchiveTitle->id, 'fwslidertitle'.$suffix, 'WEMSG.STYLEMANAGER.fwslidertitle.title', 'WEMSG.STYLEMANAGER.fwslidertitle.description', $contentElements, $cssClasses, $passToTemplate);
        // Slider - text color
        $cssClasses = $this->buildMeaningfulColorsCssClasses('ft-%s', 'fwsliderft');
        $cssClasses = array_merge($cssClasses, $this->buildRawColorsCssClasses('ft-%s', 'fwsliderft'));
        $objStyle = $this->fillObjStyle($objArchiveTitle->id, 'fwsliderft'.$suffix, 'WEMSG.STYLEMANAGER.fwsliderft.title', 'WEMSG.STYLEMANAGER.fwsliderft.description', $contentElements, $cssClasses, $passToTemplate);
        // Slider - bg color
        $cssClasses = $this->buildMeaningfulColorsCssClasses('content__bg--%s', 'fwslidercontentbg');
        $cssClasses = array_merge($cssClasses, $this->buildRawColorsCssClasses('content__bg--%s', 'fwslidercontentbg'));
        $objStyle = $this->fillObjStyle($objArchiveContent->id, 'fwslidercontentbg'.$suffix, 'WEMSG.STYLEMANAGER.fwslidercontentbg.title', 'WEMSG.STYLEMANAGER.fwslidercontentbg.description', $contentElements, $cssClasses, $passToTemplate);
        // Slider - bgopacity
        $cssClasses = $this->buildMultipleCssClasses('content__bg__opacity--%s', 'fwslidercontentbgopacity', 1, 10);
        $objStyle = $this->fillObjStyle($objArchiveContent->id, 'fwslidercontentbgopacity'.$suffix, 'WEMSG.STYLEMANAGER.fwslidercontentbgopacity.title', 'WEMSG.STYLEMANAGER.fwslidercontentbgopacity.description', $contentElements, $cssClasses, $passToTemplate);
        // Slider - wfull
        $cssClasses = [
            ['key' => 'w-full', 'value' => 'WEMSG.STYLEMANAGER.fwsliderwfull.label'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwsliderwfull'.$suffix, 'WEMSG.STYLEMANAGER.fwsliderwfull.title', 'WEMSG.STYLEMANAGER.fwsliderwfull.description', $contentElements, $cssClasses, $passToTemplate);
    }

    protected function manageImages(?string $suffix = '', ?bool $passToTemplate = false): void
    {
        $contentElements = self::$elements['image_other'.$suffix];
        // Image
        $objArchive = $this->fillObjArchive('fwimage'.$suffix, 'WEMSG.STYLEMANAGER.fwimage.tabTitle', 'FramwayImage');
        // Image - zoom
        $cssClasses = [
            ['key' => 'zoomin', 'value' => 'WEMSG.STYLEMANAGER.fwimagezoom.inLabel'],
            ['key' => 'zoomout', 'value' => 'WEMSG.STYLEMANAGER.fwimagezoom.outLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwimagezoom'.$suffix, 'WEMSG.STYLEMANAGER.fwimagezoom.title', 'WEMSG.STYLEMANAGER.fwimagezoom.description', $contentElements, $cssClasses, $passToTemplate);
        // Image - fade
        $cssClasses = [
            ['key' => 'fadetocolor', 'value' => 'WEMSG.STYLEMANAGER.fwimagefade.colorLabel'],
            ['key' => 'fadetogrey', 'value' => 'WEMSG.STYLEMANAGER.fwimagefade.greyLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwimagefade'.$suffix, 'WEMSG.STYLEMANAGER.fwimagefade.title', 'WEMSG.STYLEMANAGER.fwimagefade.description', $contentElements, $cssClasses, $passToTemplate);
    }

    protected function manageImagesRatio(?string $suffix = '', ?bool $passToTemplate = false): void
    {
        $contentElements = self::$elements['image_ratio'.$suffix];
        // Image
        $objArchive = $this->fillObjArchive('fwimageratio'.$suffix, 'WEMSG.STYLEMANAGER.fwimageratio.tabTitle', 'FramwayImage');
        // Image - ratio
        $cssClasses = [
            ['key' => 'r_16-9', 'value' => 'WEMSG.STYLEMANAGER.fwimageratio.r169Label'],
            ['key' => 'r_2-1', 'value' => 'WEMSG.STYLEMANAGER.fwimageratio.r21Label'],
            ['key' => 'r_1-1', 'value' => 'WEMSG.STYLEMANAGER.fwimageratio.r11Label'],
            ['key' => 'r_1-2', 'value' => 'WEMSG.STYLEMANAGER.fwimageratio.r12Label'],
            ['key' => 'r_4-3', 'value' => 'WEMSG.STYLEMANAGER.fwimageratio.r43Label'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwimageratio'.$suffix, 'WEMSG.STYLEMANAGER.fwimageratio.title', 'WEMSG.STYLEMANAGER.fwimageratio.description', $contentElements, $cssClasses, $passToTemplate);
    }

    protected function manageTables(?string $suffix = '', ?bool $passToTemplate = false): void
    {
        $contentElements = self::$elements['table'.$suffix];
        // Table
        $objArchive = $this->fillObjArchive('fwtable'.$suffix, 'WEMSG.STYLEMANAGER.fwtable.tabTitle', 'FramwayTable');
        // Table - sm
        $cssClasses = [
            ['key' => 'table-sm', 'value' => 'WEMSG.STYLEMANAGER.fwtablesm.label'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwtablesm'.$suffix, 'WEMSG.STYLEMANAGER.fwtablesm.title', 'WEMSG.STYLEMANAGER.fwtablesm.description', $contentElements, $cssClasses, $passToTemplate);
        // Table - border
        $cssClasses = [
            ['key' => 'table-bordered', 'value' => 'WEMSG.STYLEMANAGER.fwtableborder.borderedLabel'],
            ['key' => 'table-borderless', 'value' => 'WEMSG.STYLEMANAGER.fwtableborder.borderlessLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwtableborder'.$suffix, 'WEMSG.STYLEMANAGER.fwtableborder.title', 'WEMSG.STYLEMANAGER.fwtableborder.description', $contentElements, $cssClasses, $passToTemplate);
        // Table - striped
        $cssClasses = [
            ['key' => 'table-striped', 'value' => 'WEMSG.STYLEMANAGER.fwtablestriped.label'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwtablestriped'.$suffix, 'WEMSG.STYLEMANAGER.fwtablestriped.title', 'WEMSG.STYLEMANAGER.fwtablestriped.description', $contentElements, $cssClasses, $passToTemplate);
        // Table - hover
        $cssClasses = [
            ['key' => 'table-hover', 'value' => 'WEMSG.STYLEMANAGER.fwtablehover.label'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwtablehover'.$suffix, 'WEMSG.STYLEMANAGER.fwtablehover.title', 'WEMSG.STYLEMANAGER.fwtablehover.description', $contentElements, $cssClasses, $passToTemplate);
    }

    protected function manageBackgrounds(?string $suffix = '', ?bool $passToTemplate = false): void
    {
        $contentElements = self::$elements['background'.$suffix];
        // Background
        $objArchive = $this->fillObjArchive('fwbackground'.$suffix, 'WEMSG.STYLEMANAGER.fwbackground.tabTitle', 'FramwayBackground');
        // Background - background
        $cssClasses = $this->buildMeaningfulColorsCssClasses('bg-%s', 'fwbackgroundcolor');
        $cssClasses = array_merge($cssClasses, $this->buildRawColorsCssClasses('bg-%s', 'fwbackgroundcolor'));
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwbackgroundcolor'.$suffix, 'WEMSG.STYLEMANAGER.fwbackgroundcolor.title', 'WEMSG.STYLEMANAGER.fwbackgroundcolor.description', $contentElements, $cssClasses, $passToTemplate);
    }

    protected function manageButtons(?string $suffix = '', ?bool $passToTemplate = false): void
    {
        $contentElements = self::$elements['button'.$suffix];
        // Buttons
        $objArchive = $this->fillObjArchive('fwbutton'.$suffix, 'WEMSG.STYLEMANAGER.fwbutton.tabTitle', 'FramwayButton');
        // Buttons - size
        $cssClasses = [
            ['key' => 'btn', 'value' => 'WEMSG.STYLEMANAGER.fwbuttonsize.sizeLabel'],
            ['key' => 'btn-sm', 'value' => 'WEMSG.STYLEMANAGER.fwbuttonsize.sizeSmLabel'],
            ['key' => 'btn-lg', 'value' => 'WEMSG.STYLEMANAGER.fwbuttonsize.sizeLgLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwbuttonsize'.$suffix, 'WEMSG.STYLEMANAGER.fwbuttonsize.title', 'WEMSG.STYLEMANAGER.fwbuttonsize.description', $contentElements, $cssClasses, $passToTemplate);
        // Buttons - background
        $cssClasses = $this->buildMeaningfulColorsCssClasses('btn-bg-%s', 'fwbuttonbackground');
        $cssClasses = array_merge($cssClasses, $this->buildRawColorsCssClasses('btn-bg-%s', 'fwbuttonbackground'));
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwbuttonbackground'.$suffix, 'WEMSG.STYLEMANAGER.fwbuttonbackground.title', 'WEMSG.STYLEMANAGER.fwbuttonbackground.description', $contentElements, $cssClasses, $passToTemplate);
        // Buttons - border
        $cssClasses = $this->buildMeaningfulColorsCssClasses('btn-bd-%s', 'fwbuttonborder');
        $cssClasses = array_merge($cssClasses, $this->buildRawColorsCssClasses('btn-bd-%s', 'fwbuttonborder'));
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwbuttonborder'.$suffix, 'WEMSG.STYLEMANAGER.fwbuttonborder.title', 'WEMSG.STYLEMANAGER.fwbuttonborder.description', $contentElements, $cssClasses, $passToTemplate);
    }

    protected function manageSeparators(?string $suffix = '', ?bool $passToTemplate = false): void
    {
        $contentElements = self::$elements['separator'.$suffix];
        // separators
        $objArchive = $this->fillObjArchive('fwseparator'.$suffix, 'WEMSG.STYLEMANAGER.fwseparator.tabTitle', 'FramwaySeparator');
        // separators - top
        $cssClasses = [
            ['key' => 'sep-top', 'value' => 'WEMSG.STYLEMANAGER.fwseparatortop.label'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwseparatortop'.$suffix, 'WEMSG.STYLEMANAGER.fwseparatortop.title', 'WEMSG.STYLEMANAGER.fwseparatortop.description', $contentElements, $cssClasses, $passToTemplate);
        // separators - bottom
        $cssClasses = [
            ['key' => 'sep-bottom', 'value' => 'WEMSG.STYLEMANAGER.fwseparatorbottom.label'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwseparatorbottom'.$suffix, 'WEMSG.STYLEMANAGER.fwseparatorbottom.title', 'WEMSG.STYLEMANAGER.fwseparatorbottom.description', $contentElements, $cssClasses, $passToTemplate);
        // separators - left
        $cssClasses = [
            ['key' => 'sep-left', 'value' => 'WEMSG.STYLEMANAGER.fwseparatorleft.label'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwseparatorleft'.$suffix, 'WEMSG.STYLEMANAGER.fwseparatorleft.title', 'WEMSG.STYLEMANAGER.fwseparatorleft.description', $contentElements, $cssClasses, $passToTemplate);
        // separators - right
        $cssClasses = [
            ['key' => 'sep-right', 'value' => 'WEMSG.STYLEMANAGER.fwseparatorright.label'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwseparatorright'.$suffix, 'WEMSG.STYLEMANAGER.fwseparatorright.title', 'WEMSG.STYLEMANAGER.fwseparatorright.description', $contentElements, $cssClasses, $passToTemplate);
    }

    protected function manageMargins(?string $suffix = '', ?bool $passToTemplate = false): void
    {
        $contentElements = self::$elements['margin'.$suffix];
        // margins
        $objArchive = $this->fillObjArchive('fwmargin'.$suffix, 'WEMSG.STYLEMANAGER.fwmargin.tabTitle', 'FramwayMargin');
        // margins - top
        $cssClasses = [
            ['key' => 'm-top-0', 'value' => 'WEMSG.STYLEMANAGER.fwmargintop.noLabel'],
            ['key' => 'm-top', 'value' => 'WEMSG.STYLEMANAGER.fwmargintop.label'],
            ['key' => 'm-top-x2', 'value' => 'WEMSG.STYLEMANAGER.fwmargintop.doubleLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwmargintop'.$suffix, 'WEMSG.STYLEMANAGER.fwmargintop.title', 'WEMSG.STYLEMANAGER.fwmargintop.description', $contentElements, $cssClasses, $passToTemplate);
        // margins - bottom
        $cssClasses = [
            ['key' => 'm-bottom-0', 'value' => 'WEMSG.STYLEMANAGER.fwmarginbottom.noLabel'],
            ['key' => 'm-bottom', 'value' => 'WEMSG.STYLEMANAGER.fwmarginbottom.label'],
            ['key' => 'm-bottom-x2', 'value' => 'WEMSG.STYLEMANAGER.fwmarginbottom.doubleLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwmarginbottom'.$suffix, 'WEMSG.STYLEMANAGER.fwmarginbottom.title', 'WEMSG.STYLEMANAGER.fwmarginbottom.description', $contentElements, $cssClasses, $passToTemplate);
        // margins - left
        $cssClasses = [
            ['key' => 'm-left-0', 'value' => 'WEMSG.STYLEMANAGER.fwmarginleft.noLabel'],
            ['key' => 'm-left', 'value' => 'WEMSG.STYLEMANAGER.fwmarginleft.label'],
            ['key' => 'm-left-x2', 'value' => 'WEMSG.STYLEMANAGER.fwmarginleft.doubleLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwmarginleft'.$suffix, 'WEMSG.STYLEMANAGER.fwmarginleft.title', 'WEMSG.STYLEMANAGER.fwmarginleft.description', $contentElements, $cssClasses, $passToTemplate);
        // margins - right
        $cssClasses = [
            ['key' => 'm-right-0', 'value' => 'WEMSG.STYLEMANAGER.fwmarginright.noLabel'],
            ['key' => 'm-right', 'value' => 'WEMSG.STYLEMANAGER.fwmarginright.label'],
            ['key' => 'm-right-x2', 'value' => 'WEMSG.STYLEMANAGER.fwmarginright.doubleLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwmarginright'.$suffix, 'WEMSG.STYLEMANAGER.fwmarginright.title', 'WEMSG.STYLEMANAGER.fwmarginright.description', $contentElements, $cssClasses, $passToTemplate);
    }

    private function buildRawColorsCssClasses(string $keyPattern, string $translationKeyPart): array
    {
        $cssClasses = [];
        $colors = $this->configurationFramwayCombinedManager->load()->getColors();
        foreach ($colors as $name => $hexa) {
            $cssClasses[] = [
                'key' => sprintf($keyPattern, $name),
                'value' => sprintf('WEMSG.STYLEMANAGER.%s.colorLabel (WEMSG.FRAMWAY.COLORS.%s)', $translationKeyPart, $name),
            ];
        }

        return $cssClasses;
    }

    private function buildMeaningfulColorsCssClasses(string $keyPattern, string $translationKeyPart): array
    {
        return [
            ['key' => sprintf($keyPattern, 'primary'), 'value' => sprintf('WEMSG.STYLEMANAGER.%s.colorLabel (WEMSG.FRAMWAY.COLORS.%s)', $translationKeyPart, 'primary')],
            ['key' => sprintf($keyPattern, 'secondary'), 'value' => sprintf('WEMSG.STYLEMANAGER.%s.colorLabel (WEMSG.FRAMWAY.COLORS.%s)', $translationKeyPart, 'secondary')],
            ['key' => sprintf($keyPattern, 'success'), 'value' => sprintf('WEMSG.STYLEMANAGER.%s.colorLabel (WEMSG.FRAMWAY.COLORS.%s)', $translationKeyPart, 'success')],
            ['key' => sprintf($keyPattern, 'error'), 'value' => sprintf('WEMSG.STYLEMANAGER.%s.colorLabel (WEMSG.FRAMWAY.COLORS.%s)', $translationKeyPart, 'error')],
            ['key' => sprintf($keyPattern, 'warning'), 'value' => sprintf('WEMSG.STYLEMANAGER.%s.colorLabel (WEMSG.FRAMWAY.COLORS.%s)', $translationKeyPart, 'warning')],
        ];
    }

    private function buildMultipleCssClasses(string $keyPattern, string $translationKeyPart, int $startValue, int $maxValue): array
    {
        $cssClasses = [];
        for ($i = $startValue; $i <= $maxValue; ++$i) {
            $cssClasses[] = [
                'key' => sprintf($keyPattern, (string) $i),
                'value' => sprintf('WEMSG.STYLEMANAGER.%s.%sLabel', $translationKeyPart, (string) $i),
            ];
        }

        return $cssClasses;
    }

    private function fillObjStyle($pid, $alias, $titleKey, string $descriptioneKey, array $contentElements, array $cssClasses, bool $passToTemplate): StyleManagerModel
    {
        $objStyle = StyleManagerModel::findByAliasAndPid($alias, $pid) ?? new StyleManagerModel();
        $objStyle->pid = $pid;
        $objStyle->title = $titleKey;
        $objStyle->alias = $alias;
        $objStyle->blankOption = true;
        $objStyle->chosen = true;
        $objStyle->tstamp = time();
        if (\array_key_exists('contentElements', $contentElements)) {
            $objStyle->contentElements = serialize($contentElements['contentElements']);
            $objStyle->extendContentElement = true;
        }
        if (\array_key_exists('formFields', $contentElements)) {
            $objStyle->formFields = serialize($contentElements['formFields']);
            $objStyle->extendFormFields = true;
        }

        $objStyle->description = $descriptioneKey;
        $objStyle->cssClasses = serialize($cssClasses);
        $objStyle->passToTemplate = $passToTemplate;
        $objStyle->save();

        $this->styleAliasToKeep[$objStyle->id] = $alias;

        return $objStyle;
    }

    private function fillObjArchive(string $identifier, string $titleKey, ?string $groupAlias = 'Framway'): StyleManagerArchiveModel
    {
        $objArchive = StyleManagerArchiveModel::findByIdentifier($identifier);
        if (!$objArchive) {
            $objArchive = new StyleManagerArchiveModel();
        } elseif (\Contao\Model\Collection::class === \get_class($objArchive)) {
            $objArchive = $objArchive->first()->current();
        }

        $objArchive->title = $titleKey;
        $objArchive->identifier = $identifier;
        $objArchive->groupAlias = $groupAlias;
        $objArchive->tstamp = time();
        $objArchive->save();

        $this->archiveIdentifierToKeep[$objArchive->id] = $identifier;

        return $objArchive;
    }
}
