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
        'margin' => ['contentElements' => ['headline', 'text', 'table', 'rsce_listIcons', 'rsce_quote', 'accordionStart', 'accordionSingle', 'sliderStart', 'hyperlink', 'image', 'player', 'youtube', 'vimeo', 'downloads', 'rsce_timeline', 'grid-start', 'rsce_accordion', 'rsce_counter', 'rsce_hero', 'rsce_heroStart', 'rsce_pricecards', 'rsce_slider', 'rsce_tabs', 'rsce_testimonials', 'rsce_ratings', 'rsce_pdfViewer', 'rsce_blockCard']], //, 'accordionStop', 'grid-stop', 'sliderStop' , 'rsce_heroStop', 'rsce_gridGallery'
        'button' => ['contentElements' => ['hyperlink'], 'formFields' => ['submit']],
        'button_manual' => ['contentElements' => ['rsce_pdfViewer']],
        'background' => ['contentElements' => ['headline', 'text', 'rsce_quote']],
        'separator' => ['contentElements' => ['headline']],
        'table' => ['contentElements' => ['table']],
        'accordion' => ['contentElements' => ['accordionStart', 'rsce_accordion']], //, 'accordionStop]'
        'image_other' => ['contentElements' => ['image']],
        'image_ratio' => ['contentElements' => ['image']],
        'hero' => ['contentElements' => ['rsce_hero', 'rsce_heroStart']], //'rsce_heroStop]'
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

        $objArchiveHero = StyleManagerArchiveModel::findByIdentifier('fwhero');
        $objArchiveHeroImg = StyleManagerArchiveModel::findByIdentifier('fwheroimg');

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
        && null !== $objArchiveHero
        && null !== $objArchiveHeroImg
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
            && null !== StyleManagerModel::findByAliasAndPid('fwheroimgvertical', $objArchiveHeroImg->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwheroimghorizontal', $objArchiveHeroImg->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwheroft', $objArchiveHero->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwherocontentbg', $objArchiveHero->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwherocontentbgopacity', $objArchiveHero->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwimageratio', $objArchiveImageRatio->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwimagezoom', $objArchiveImage->id)
            && null !== StyleManagerModel::findByAliasAndPid('fwimagefade', $objArchiveImage->id)
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
            $this->manageHero();
            $result->addLog($this->translator->trans($this->buildTranslationKey('doAddCSSHero'), [], 'contao_default'));
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
