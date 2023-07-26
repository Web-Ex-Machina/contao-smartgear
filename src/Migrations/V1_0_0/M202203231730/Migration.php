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
    protected $name = 'Configures CSS classes';
    protected $description = 'Configures CSS classes available for contents';
    protected $version = '1.0.0';
    protected $translation_key = 'WEMSG.MIGRATIONS.V1_0_0_M202203231730';
    /** @var configurationFramwayCombinedManager */
    protected $configurationFramwayCombinedManager;

    protected static $elements = [
        'margin' => ['contentElements' => ['headline', 'text', 'table', 'rsce_listIcons', 'rsce_quote', 'accordionStart', 'accordionSingle', 'sliderStart', 'hyperlink', 'image', 'player', 'youtube', 'vimeo', 'downloads', 'gallery', 'rsce_timeline', 'grid-start', 'rsce_accordion', 'rsce_counter', 'rsce_hero', 'rsce_heroStart', 'rsce_priceCards', 'rsce_slider', 'rsce_tabs', 'rsce_testimonials', 'rsce_ratings', 'rsce_pdfViewer', 'rsce_blockCard']], //, 'accordionStop', 'grid-stop', 'sliderStop' , 'rsce_heroStop', 'rsce_gridGallery'
        'button' => ['contentElements' => ['hyperlink'], 'formFields' => ['submit']],
        'button_manual' => ['contentElements' => ['rsce_pdfViewer']],
        'background' => ['contentElements' => ['headline', 'text', 'rsce_quote']],
        'separator' => ['contentElements' => ['headline']],
        'table' => ['contentElements' => ['table']],
        'accordion' => ['contentElements' => ['accordionStart', 'rsce_accordion']], //, 'accordionStop]'
        'image_other' => ['contentElements' => ['image']],
        'image_ratio' => ['contentElements' => ['image', 'gallery']],
        'blockCard' => ['contentElements' => ['rsce_blockCard']],
        'blockAlignement' => ['contentElements' => ['text', 'hyperlink', 'image', 'player', 'youtube', 'vimeo', 'downloads', 'rsce_*']],
        'grid_gap' => ['contentElements' => ['gallery', 'rsce_listIcons']],
        'grid_columns' => ['contentElements' => ['gallery', 'rsce_listIcons']],
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
        $result = parent::shouldRunWithoutCheckingVersion();

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

        $objArchiveBlockCardText = StyleManagerArchiveModel::findByIdentifier('fwblockcardtext');
        $objArchiveBlockCardBg = StyleManagerArchiveModel::findByIdentifier('fwblockcardbg');

        $objArchiveBlockAlignement = StyleManagerArchiveModel::findByIdentifier('fwblockalignement');
        $objArchiveGridColumns = StyleManagerArchiveModel::findByIdentifier('fwgridcolumns');
        $objArchiveGridGap = StyleManagerArchiveModel::findByIdentifier('fwgridgap');

        if (null === $objArchiveBackground
        && null !== $objArchiveButton
        && null !== $objArchiveButtonManual
        && null !== $objArchiveSeparator
        && null !== $objArchiveMargin
        && null !== $objArchiveTable
        && null !== $objArchiveImage
        && null !== $objArchiveImageRatio
        && null !== $objArchiveBlockCardText
        && null !== $objArchiveBlockCardBg
        && null !== $objArchiveBlockAlignement
        && null !== $objArchiveGridColumns
        && null !== $objArchiveGridGap
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
            && null !== StyleManagerModel::findByAliasAndPid('fwimageratio', $objArchiveImageRatio->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwimagezoom', $objArchiveImage->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwimagefade', $objArchiveImage->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwblockcardft', $objArchiveBlockCardText->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwblockcardtextalign', $objArchiveBlockCardText->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwblockcardcontentbg', $objArchiveBlockCardBg->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwblockcardcontentbgopacity', $objArchiveBlockCardBg->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwblockalignement', $objArchiveBlockAlignement->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridcolumns', $objArchiveGridColumns->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridcolumnsxl', $objArchiveGridColumns->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridcolumnslg', $objArchiveGridColumns->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridcolumnsmd', $objArchiveGridColumns->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridcolumnssm', $objArchiveGridColumns->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridcolumnsxs', $objArchiveGridColumns->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridcolumnsxxs', $objArchiveGridColumns->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwgridgap', $objArchiveGridGap->id)
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
            $this->manageMargins(0);
            $result->addLog($this->translator->trans($this->buildTranslationKey('doAddCSSMargins'), [], 'contao_default'));
            $this->manageSeparators(128);
            $result->addLog($this->translator->trans($this->buildTranslationKey('doAddCSSSeparators'), [], 'contao_default'));
            $this->manageButtons(256);
            $this->manageButtons(384, '_manual', true);
            $result->addLog($this->translator->trans($this->buildTranslationKey('doAddCSSButtons'), [], 'contao_default'));
            $this->manageBackgrounds(512);
            $result->addLog($this->translator->trans($this->buildTranslationKey('doAddCSSBackgrounds'), [], 'contao_default'));
            $this->manageTables(640);
            $result->addLog($this->translator->trans($this->buildTranslationKey('doAddCSSTables'), [], 'contao_default'));
            $this->manageImages(768);
            $this->manageImagesRatio(896);
            $result->addLog($this->translator->trans($this->buildTranslationKey('doAddCSSImages'), [], 'contao_default'));
            $this->manageBlockCard(1024);
            $result->addLog($this->translator->trans($this->buildTranslationKey('doAddCSSBlockCard'), [], 'contao_default'));
            $this->manageBlockAlignement(1152);
            $result->addLog($this->translator->trans($this->buildTranslationKey('doAddCSSBlockAlignement'), [], 'contao_default'));
            $this->manageBlockGridColumns(1280);
            $result->addLog($this->translator->trans($this->buildTranslationKey('doAddCSSGridColumns'), [], 'contao_default'));
            $this->manageBlockGridGap(1408);
            $result->addLog($this->translator->trans($this->buildTranslationKey('doAddCSSGridGap'), [], 'contao_default'));
            $this->deleteUnusedStyles(1536);
            $this->deleteUnusedArchives(1664);
            $this->deleteOrphanStyles(1792);
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

    protected function manageBlockGridColumns(int $sorting, ?string $suffix = '', ?bool $passToTemplate = false): void
    {
        $contentElements = self::$elements['grid_columns'.$suffix];
        $objArchive = $this->fillObjArchive('fwgridcolumns'.$suffix, 'WEMSG.STYLEMANAGER.fwgridcolumns.tabTitle', $sorting, 'FramwayGridColumns');
        $objArchive->save();

        // cols breakpoints default
        $contentElements2 = $contentElements;
        $contentElements2['contentElements'] = array_diff($contentElements2['contentElements'], ['gallery']);
        $cssClasses = $this->buildMultipleCssClasses('d-grid cols-%s', 'fwgridcolumns', 1, 12);
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwgridcolumns'.$suffix, 'WEMSG.STYLEMANAGER.fwgridcolumns.title', 'WEMSG.STYLEMANAGER.fwgridcolumns.description', $contentElements2, $cssClasses, $passToTemplate);
        $objStyle->save();

        // cols breakpoints XL
        $cssClasses = $this->buildMultipleCssClasses('cols-xl-%s', 'fwgridcolumns', 1, 12);
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwgridcolumnsxl'.$suffix, 'WEMSG.STYLEMANAGER.fwgridcolumnsxl.title', 'WEMSG.STYLEMANAGER.fwgridcolumnsxl.description', $contentElements, $cssClasses, $passToTemplate);
        $objStyle->save();

        // cols breakpoints LG
        $cssClasses = $this->buildMultipleCssClasses('cols-lg-%s', 'fwgridcolumns', 1, 12);
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwgridcolumnslg'.$suffix, 'WEMSG.STYLEMANAGER.fwgridcolumnslg.title', 'WEMSG.STYLEMANAGER.fwgridcolumnslg.description', $contentElements, $cssClasses, $passToTemplate);
        $objStyle->save();

        // cols breakpoints MD
        $cssClasses = $this->buildMultipleCssClasses('cols-md-%s', 'fwgridcolumns', 1, 12);
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwgridcolumnsmd'.$suffix, 'WEMSG.STYLEMANAGER.fwgridcolumnsmd.title', 'WEMSG.STYLEMANAGER.fwgridcolumnsmd.description', $contentElements, $cssClasses, $passToTemplate);
        $objStyle->save();

        // cols breakpoints SM
        $cssClasses = $this->buildMultipleCssClasses('cols-sm-%s', 'fwgridcolumns', 1, 12);
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwgridcolumnssm'.$suffix, 'WEMSG.STYLEMANAGER.fwgridcolumnssm.title', 'WEMSG.STYLEMANAGER.fwgridcolumnssm.description', $contentElements, $cssClasses, $passToTemplate);
        $objStyle->save();

        // cols breakpoints XS
        $cssClasses = $this->buildMultipleCssClasses('cols-xs-%s', 'fwgridcolumns', 1, 12);
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwgridcolumnsxs'.$suffix, 'WEMSG.STYLEMANAGER.fwgridcolumnsxs.title', 'WEMSG.STYLEMANAGER.fwgridcolumnsxs.description', $contentElements, $cssClasses, $passToTemplate);
        $objStyle->save();

        // cols breakpoints XXS
        $cssClasses = $this->buildMultipleCssClasses('cols-xxs-%s', 'fwgridcolumns', 1, 12);
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwgridcolumnsxxs'.$suffix, 'WEMSG.STYLEMANAGER.fwgridcolumnsxxs.title', 'WEMSG.STYLEMANAGER.fwgridcolumnsxxs.description', $contentElements, $cssClasses, $passToTemplate);
        $objStyle->save();
    }

    protected function manageBlockGridGap(int $sorting, ?string $suffix = '', ?bool $passToTemplate = false): void
    {
        $contentElements = self::$elements['grid_gap'.$suffix];
        $objArchive = $this->fillObjArchive('fwgridgap'.$suffix, 'WEMSG.STYLEMANAGER.fwgridgap.tabTitle', $sorting, 'FramwayGridGap');
        $objArchive->save();

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
        $objStyle->save();
    }

    protected function manageBlockAlignement(int $sorting, ?string $suffix = '', ?bool $passToTemplate = false): void
    {
        $contentElements = self::$elements['blockAlignement'.$suffix];
        $objArchive = $this->fillObjArchive('fwblockalignement'.$suffix, 'WEMSG.STYLEMANAGER.fwblockalignement.tabTitle', $sorting, 'FramwayBlockAlignement');
        $objArchive->save();

        $cssClasses = [
            ['key' => 'm-left-auto', 'value' => 'WEMSG.STYLEMANAGER.fwblockalignement.leftLabel'],
            ['key' => 'm-right-auto', 'value' => 'WEMSG.STYLEMANAGER.fwblockalignement.rightLabel'],
            ['key' => 'center', 'value' => 'WEMSG.STYLEMANAGER.fwblockalignement.centerLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwblockalignement'.$suffix, 'WEMSG.STYLEMANAGER.fwblockalignement.title', 'WEMSG.STYLEMANAGER.fwblockalignement.description', $contentElements, $cssClasses, $passToTemplate);
        $objStyle->save();
    }

    protected function manageBlockCard(int $sorting, ?string $suffix = '', ?bool $passToTemplate = false): void
    {
        $contentElements = self::$elements['blockCard'.$suffix];
        // Block card
        $objArchiveText = $this->fillObjArchive('fwblockcardtext'.$suffix, 'WEMSG.STYLEMANAGER.fwblockcardtext.tabTitle', $sorting, 'FramwayBlockCard');
        $objArchiveText->save();
        $objArchiveBg = $this->fillObjArchive('fwblockcardbg'.$suffix, 'WEMSG.STYLEMANAGER.fwblockcardbg.tabTitle', $sorting + 32, 'FramwayBlockCard');
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

    protected function manageImages(int $sorting, ?string $suffix = '', ?bool $passToTemplate = false): void
    {
        $contentElements = self::$elements['image_other'.$suffix];
        // Image
        $objArchive = $this->fillObjArchive('fwimage'.$suffix, 'WEMSG.STYLEMANAGER.fwimage.tabTitle', $sorting, 'FramwayImage');
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

    protected function manageImagesRatio(int $sorting, ?string $suffix = '', ?bool $passToTemplate = false): void
    {
        $contentElements = self::$elements['image_ratio'.$suffix];
        // Image
        $objArchive = $this->fillObjArchive('fwimageratio'.$suffix, 'WEMSG.STYLEMANAGER.fwimageratio.tabTitle', $sorting, 'FramwayImage');
        // Image - ratio
        $cssClasses = [
            ['key' => 'r_16-9', 'value' => 'WEMSG.STYLEMANAGER.fwimageratio.r169Label'],
            ['key' => 'r_4-3', 'value' => 'WEMSG.STYLEMANAGER.fwimageratio.r43Label'],
            ['key' => 'r_2-1', 'value' => 'WEMSG.STYLEMANAGER.fwimageratio.r21Label'],
            ['key' => 'r_1-1', 'value' => 'WEMSG.STYLEMANAGER.fwimageratio.r11Label'],
            ['key' => 'r_1-2', 'value' => 'WEMSG.STYLEMANAGER.fwimageratio.r12Label'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwimageratio'.$suffix, 'WEMSG.STYLEMANAGER.fwimageratio.title', 'WEMSG.STYLEMANAGER.fwimageratio.description', $contentElements, $cssClasses, $passToTemplate);
    }

    protected function manageTables(int $sorting, ?string $suffix = '', ?bool $passToTemplate = false): void
    {
        $contentElements = self::$elements['table'.$suffix];
        // Table
        $objArchive = $this->fillObjArchive('fwtable'.$suffix, 'WEMSG.STYLEMANAGER.fwtable.tabTitle', $sorting, 'FramwayTable');
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

    protected function manageBackgrounds(int $sorting, ?string $suffix = '', ?bool $passToTemplate = false): void
    {
        $contentElements = self::$elements['background'.$suffix];
        // Background
        $objArchive = $this->fillObjArchive('fwbackground'.$suffix, 'WEMSG.STYLEMANAGER.fwbackground.tabTitle', $sorting, 'FramwayBackground');
        // Background - background
        $cssClasses = $this->buildMeaningfulColorsCssClasses('bg-%s', 'fwbackgroundcolor');
        $cssClasses = array_merge($cssClasses, $this->buildRawColorsCssClasses('bg-%s', 'fwbackgroundcolor'));
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwbackgroundcolor'.$suffix, 'WEMSG.STYLEMANAGER.fwbackgroundcolor.title', 'WEMSG.STYLEMANAGER.fwbackgroundcolor.description', $contentElements, $cssClasses, $passToTemplate);
    }

    protected function manageButtons(int $sorting, ?string $suffix = '', ?bool $passToTemplate = false): void
    {
        $contentElements = self::$elements['button'.$suffix];
        // Buttons
        $objArchive = $this->fillObjArchive('fwbutton'.$suffix, 'WEMSG.STYLEMANAGER.fwbutton.tabTitle', $sorting, 'FramwayButton');
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

    protected function manageSeparators(int $sorting, ?string $suffix = '', ?bool $passToTemplate = false): void
    {
        $contentElements = self::$elements['separator'.$suffix];
        // separators
        $objArchive = $this->fillObjArchive('fwseparator'.$suffix, 'WEMSG.STYLEMANAGER.fwseparator.tabTitle', $sorting, 'FramwaySeparator');
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

    protected function manageMargins(int $sorting, ?string $suffix = '', ?bool $passToTemplate = false): void
    {
        $contentElements = self::$elements['margin'.$suffix];
        // margins
        $objArchive = $this->fillObjArchive('fwmargin'.$suffix, 'WEMSG.STYLEMANAGER.fwmargin.tabTitle', $sorting, 'FramwayMargin');
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

        return [
            [
                'value' => 'WEMSG.FRAMWAY.COLORS.rawLabel',
                'key' => $cssClasses,
            ],
        ];
    }

    private function buildMeaningfulColorsCssClasses(string $keyPattern, string $translationKeyPart): array
    {
        return [
            [
                'value' => 'WEMSG.FRAMWAY.COLORS.meaningfulLabel',
                'key' => [
                    ['key' => sprintf($keyPattern, 'primary'), 'value' => sprintf('WEMSG.STYLEMANAGER.%s.colorLabel (WEMSG.FRAMWAY.COLORS.%s)', $translationKeyPart, 'primary')],
                    ['key' => sprintf($keyPattern, 'secondary'), 'value' => sprintf('WEMSG.STYLEMANAGER.%s.colorLabel (WEMSG.FRAMWAY.COLORS.%s)', $translationKeyPart, 'secondary')],
                    ['key' => sprintf($keyPattern, 'success'), 'value' => sprintf('WEMSG.STYLEMANAGER.%s.colorLabel (WEMSG.FRAMWAY.COLORS.%s)', $translationKeyPart, 'success')],
                    ['key' => sprintf($keyPattern, 'error'), 'value' => sprintf('WEMSG.STYLEMANAGER.%s.colorLabel (WEMSG.FRAMWAY.COLORS.%s)', $translationKeyPart, 'error')],
                    ['key' => sprintf($keyPattern, 'warning'), 'value' => sprintf('WEMSG.STYLEMANAGER.%s.colorLabel (WEMSG.FRAMWAY.COLORS.%s)', $translationKeyPart, 'warning')],
                ],
            ],
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
            $objStyle->contentElements = $this->manageContentElements($contentElements['contentElements']);
            $objStyle->extendContentElement = true;
        }
        if (\array_key_exists('formFields', $contentElements)) {
            $objStyle->formFields = $this->manageFormFields($contentElements['formFields']);
            $objStyle->extendFormFields = true;
        }

        $objStyle->description = $descriptioneKey;
        $objStyle->cssClasses = serialize($cssClasses);
        $objStyle->passToTemplate = $passToTemplate;
        $objStyle->save();

        $this->styleAliasToKeep[$objStyle->id] = $alias;

        return $objStyle;
    }

    private function fillObjArchive(string $identifier, string $titleKey, int $sorting, ?string $groupAlias = 'Framway'): StyleManagerArchiveModel
    {
        $objArchive = StyleManagerArchiveModel::findByIdentifier($identifier);
        if (!$objArchive) {
            $objArchive = new StyleManagerArchiveModel();
        } elseif (\Contao\Model\Collection::class === \get_class($objArchive)) {
            $objArchive = $objArchive->first()->current();
        }

        $objArchive->title = $titleKey;
        $objArchive->identifier = $identifier;
        $objArchive->sorting = $sorting;
        $objArchive->groupAlias = $groupAlias;
        $objArchive->tstamp = time();
        $objArchive->save();

        $this->archiveIdentifierToKeep[$objArchive->id] = $identifier;

        return $objArchive;
    }

    private function manageContentElements(array $contentElements): string
    {
        if (\in_array('*', $contentElements, true)) {
            unset($contentElements[array_search('*', $contentElements, true)]);
            // insert all content elements
            foreach ($GLOBALS['TL_CTE'] as $group => $elements) {
                foreach ($elements as $key => $classPath) {
                    if ('rsce_' !== substr($key, 0, 5)) {
                        $contentElements[] = $key;
                    }
                }
            }
        }
        if (\in_array('rsce_*', $contentElements, true)) {
            unset($contentElements[array_search('rsce_*', $contentElements, true)]);
            // insert all RSCE elements
            foreach ($GLOBALS['TL_CTE'] as $group => $elements) {
                foreach ($elements as $key => $classPath) {
                    if ('rsce_' === substr($key, 0, 5)) {
                        $contentElements[] = $key;
                    }
                }
            }
        }

        return serialize($contentElements);
    }

    private function manageFormFields(array $formFields): string
    {
        if (\in_array('fe_*', $formFields, true)) {
            unset($formFields[array_search('fe_*', $formFields, true)]);
            // insert all form fields
            foreach ($GLOBALS['TL_FFL'] as $group => $elements) {
                foreach ($elements as $key => $classPath) {
                    if ('rsce_' !== substr($key, 0, 5)) {
                        $formFields[] = $key;
                    }
                }
            }
        }
        if (\in_array('fe_rsce_*', $formFields, true)) {
            unset($formFields[array_search('fe_rsce_*', $formFields, true)]);
            // insert all RSCE elements
            foreach ($GLOBALS['TL_FFL'] as $group => $elements) {
                foreach ($elements as $key => $classPath) {
                    if ('rsce_' === substr($key, 0, 5)) {
                        $formFields[] = $key;
                    }
                }
            }
        }
        if (\in_array('be_*', $formFields, true)) {
            unset($formFields[array_search('be_*', $formFields, true)]);
            // insert all form fields
            foreach ($GLOBALS['BE_FFL'] as $group => $elements) {
                foreach ($elements as $key => $classPath) {
                    if ('rsce_' !== substr($key, 0, 5)) {
                        $formFields[] = $key;
                    }
                }
            }
        }
        if (\in_array('be_rsce_*', $formFields, true)) {
            unset($formFields[array_search('be_rsce_*', $formFields, true)]);
            // insert all RSCE elements
            foreach ($GLOBALS['BE_FFL'] as $group => $elements) {
                foreach ($elements as $key => $classPath) {
                    if ('rsce_' === substr($key, 0, 5)) {
                        $formFields[] = $key;
                    }
                }
            }
        }

        return serialize($formFields);
    }
}
