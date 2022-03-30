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
        'margin' => ['headline', 'text', 'table', 'rsce_listIcons', 'rsce_quote', 'accordionStart', 'accordionStop', 'accordionSingle', 'sliderStart', 'sliderStop', 'hyperlink', 'image', 'player', 'youtube', 'vimeo', 'downloads', 'rsce_timeline', 'grid-start', 'grid-stop', 'rsce_accordionFW', 'rsce_block-img', 'rsce_counterFW', 'rsce_gridGallery', 'rsce_heroFWStart', 'rsce_heroFWStop', 'rsce_priceCards', 'rsce_sliderFW', 'rsce_tabs', 'rsce_testimonials', 'rsce_notations'],
        'button' => ['hyperlink'],
        'background' => ['headline', 'text', 'rsce_quote'],
        'separator' => ['headline'],
        // no rules associated yet
        'table' => ['table'],
        'accordion' => ['accordionStart', 'accordionStop', 'rsce_accordionFW'],
        'slider' => ['sliderStart', 'sliderStop', 'rsce_sliderFW', 'rsce_testimonials'],
        'image' => ['image', 'rsce_block-img'],
        'hero' => ['rsce_heroFWStart', 'rsce_heroFWStop'],
        'grid' => ['rsce_gridGallery'],
        'priceCards' => ['rsce_priceCards'],
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
        if (null !== $objArchiveBackground
        && null !== $objArchiveButton
        && null !== $objArchiveSeparator
        && null !== $objArchiveMargin
        && null !== $objArchiveTable
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
            && null !== StyleManagerModel::findByAliasAndPid('fwtablehover', $objArchiveTable->id)
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
        } catch (\Exception $e) {
            $result
                ->setStatus(Result::STATUS_FAIL)
                ->addLog($e->getMessage())
            ;
        }

        return $result;
    }

    protected function manageTables(): void
    {
        $contentElements = self::$elements['table'];
        // Buttons
        $objArchive = StyleManagerArchiveModel::findByIdentifier('fwtable') ?? new StyleManagerArchiveModel();
        $objArchive->title = $this->translator->trans('WEMSG.STYLEMANAGER.fwtable.title', [], 'contao_default');
        $objArchive->description = '';
        $objArchive->identifier = 'fwtable';
        $objArchive->groupAlias = 'Framway';
        $objArchive->tstamp = time();
        $objArchive->save();
        // Table - sm
        $cssClasses = [
            ['key' => 'table-sm', 'value' => 'WEMSG.STYLEMANAGER.fwtablesm.label'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwtablesm', 'WEMSG.STYLEMANAGER.fwtablesm.title', $contentElements, $cssClasses);
        $objStyle->save();
        // Table - border
        $cssClasses = [
            ['key' => 'table-bordered', 'value' => 'WEMSG.STYLEMANAGER.fwtableborder.borderedLabel'],
            ['key' => 'table-borderless', 'value' => 'WEMSG.STYLEMANAGER.fwtableborder.borderlessLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwtableborder', 'WEMSG.STYLEMANAGER.fwtableborder.title', $contentElements, $cssClasses);
        $objStyle->save();
        // Table - striped
        $cssClasses = [
            ['key' => 'table-striped', 'value' => 'WEMSG.STYLEMANAGER.fwtablestriped.label'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwtablestriped', 'WEMSG.STYLEMANAGER.fwtablestriped.title', $contentElements, $cssClasses);
        $objStyle->save();
        // Table - hover
        $cssClasses = [
            ['key' => 'table-hover', 'value' => 'WEMSG.STYLEMANAGER.fwtablehover.label'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwtablehover', 'WEMSG.STYLEMANAGER.fwtablehover.title', $contentElements, $cssClasses);
        $objStyle->save();
    }

    protected function manageBackgrounds(): void
    {
        $contentElements = self::$elements['background'];
        // Background
        $objArchive = StyleManagerArchiveModel::findByIdentifier('fwbackground') ?? new StyleManagerArchiveModel();
        $objArchive->title = $this->translator->trans('WEMSG.STYLEMANAGER.fwbackground.title', [], 'contao_default');
        $objArchive->description = '';
        $objArchive->identifier = 'fwbackground';
        $objArchive->groupAlias = 'Framway';
        $objArchive->tstamp = time();
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
                'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwbackgroundcolor.bgColorLabel', [
                    $this->translator->trans('WEMSG.FRAMWAY.COLORS.'.$name, [], 'contao_default'),
                ], 'contao_default'),
                'translated' => true, ];
        }
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwbackgroundcolor', 'WEMSG.STYLEMANAGER.fwbackgroundcolor.title', $contentElements, $cssClasses);
        $objStyle->save();
    }

    protected function manageButtons(): void
    {
        $contentElements = self::$elements['button'];
        // Buttons
        $objArchive = StyleManagerArchiveModel::findByIdentifier('fwbutton') ?? new StyleManagerArchiveModel();
        $objArchive->title = $this->translator->trans('WEMSG.STYLEMANAGER.fwbutton.title', [], 'contao_default');
        $objArchive->description = '';
        $objArchive->identifier = 'fwbutton';
        $objArchive->groupAlias = 'Framway';
        $objArchive->tstamp = time();
        $objArchive->save();
        // Buttons - size
        $cssClasses = [
            ['key' => 'btn', 'value' => 'WEMSG.STYLEMANAGER.fwbuttonsize.sizeLabel'],
            ['key' => 'btn-sm', 'value' => 'WEMSG.STYLEMANAGER.fwbuttonsize.sizeSmLabel'],
            ['key' => 'btn-lg', 'value' => 'WEMSG.STYLEMANAGER.fwbuttonsize.sizeLgLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwbuttonsize', 'WEMSG.STYLEMANAGER.fwbuttonsize.title', $contentElements, $cssClasses);
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
                'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwbuttonbackground.bgColorLabel', [
                    $this->translator->trans('WEMSG.FRAMWAY.COLORS.'.$name, [], 'contao_default'),
                ], 'contao_default'),
                'translated' => true, ];
        }
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwbuttonbackground', 'WEMSG.STYLEMANAGER.fwbuttonbackground.title', $contentElements, $cssClasses);
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
                'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwbuttonborder.bdColorLabel', [
                    $this->translator->trans('WEMSG.FRAMWAY.COLORS.'.$name, [], 'contao_default'),
                ], 'contao_default'), ];
        }
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwbuttonborder', 'WEMSG.STYLEMANAGER.fwbuttonborder.title', $contentElements, $cssClasses);
        $objStyle->save();
    }

    protected function manageSeparators(): void
    {
        $contentElements = self::$elements['separator'];
        // separators
        $objArchive = StyleManagerArchiveModel::findByIdentifier('fwseparator') ?? new StyleManagerArchiveModel();
        $objArchive->title = $this->translator->trans('WEMSG.STYLEMANAGER.fwseparator.title', [], 'contao_default');
        $objArchive->description = '';
        $objArchive->identifier = 'fwseparator';
        $objArchive->groupAlias = 'Framway';
        $objArchive->tstamp = time();
        $objArchive->save();
        // separators - top
        $cssClasses = [
            ['key' => 'sep-top', 'value' => 'WEMSG.STYLEMANAGER.fwseparatortop.label'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwseparatortop', 'WEMSG.STYLEMANAGER.fwseparatortop.title', $contentElements, $cssClasses);
        $objStyle->save();
        // separators - bottom
        $cssClasses = [
            ['key' => 'sep-bottom', 'value' => 'WEMSG.STYLEMANAGER.fwseparatorbottom.label'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwseparatorbottom', 'WEMSG.STYLEMANAGER.fwseparatorbottom.title', $contentElements, $cssClasses);
        $objStyle->save();
        // separators - left
        $cssClasses = [
            ['key' => 'sep-left', 'value' => 'WEMSG.STYLEMANAGER.fwseparatorleft.label'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwseparatorleft', 'WEMSG.STYLEMANAGER.fwseparatorleft.title', $contentElements, $cssClasses);
        $objStyle->save();
        // separators - right
        $cssClasses = [
            ['key' => 'sep-right', 'value' => 'WEMSG.STYLEMANAGER.fwseparatorright.label'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwseparatorright', 'WEMSG.STYLEMANAGER.fwseparatorright.title', $contentElements, $cssClasses);
        $objStyle->save();
    }

    protected function manageMargins(): void
    {
        $contentElements = self::$elements['margin'];
        // margins
        $objArchive = StyleManagerArchiveModel::findByIdentifier('fwmargin') ?? new StyleManagerArchiveModel();
        $objArchive->title = $this->translator->trans('WEMSG.STYLEMANAGER.fwmargin.title', [], 'contao_default');
        $objArchive->description = '';
        $objArchive->identifier = 'fwmargin';
        $objArchive->groupAlias = 'Framway';
        $objArchive->tstamp = time();
        $objArchive->save();
        // margins - top
        $cssClasses = [
            ['key' => 'm-top-0', 'value' => 'WEMSG.STYLEMANAGER.fwmargintop.noLabel'],
            ['key' => 'm-top', 'value' => 'WEMSG.STYLEMANAGER.fwmargintop.label'],
            ['key' => 'm-top-x2', 'value' => 'WEMSG.STYLEMANAGER.fwmargintop.doubleLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwmargintop', 'WEMSG.STYLEMANAGER.fwmargintop.title', $contentElements, $cssClasses);
        $objStyle->save();
        // margins - bottom
        $cssClasses = [
            ['key' => 'm-bottom-0', 'value' => 'WEMSG.STYLEMANAGER.fwmarginbottom.noLabel'],
            ['key' => 'm-bottom', 'value' => 'WEMSG.STYLEMANAGER.fwmarginbottom.label'],
            ['key' => 'm-bottom-x2', 'value' => 'WEMSG.STYLEMANAGER.fwmarginbottom.doubleLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwmarginbottom', 'WEMSG.STYLEMANAGER.fwmarginbottom.title', $contentElements, $cssClasses);
        $objStyle->save();
        // margins - left
        $cssClasses = [
            ['key' => 'm-left-0', 'value' => 'WEMSG.STYLEMANAGER.fwmarginleft.noLabel'],
            ['key' => 'm-left', 'value' => 'WEMSG.STYLEMANAGER.fwmarginleft.label'],
            ['key' => 'm-left-x2', 'value' => 'WEMSG.STYLEMANAGER.fwmarginleft.doubleLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwmarginleft', 'WEMSG.STYLEMANAGER.fwmarginleft.title', $contentElements, $cssClasses);
        $objStyle->save();
        // margins - right
        $cssClasses = [
            ['key' => 'm-right-0', 'value' => 'WEMSG.STYLEMANAGER.fwmarginright.noLabel'],
            ['key' => 'm-right', 'value' => 'WEMSG.STYLEMANAGER.fwmarginright.label'],
            ['key' => 'm-right-x2', 'value' => 'WEMSG.STYLEMANAGER.fwmarginright.doubleLabel'],
        ];
        $objStyle = $this->fillObjStyle($objArchive->id, 'fwmarginright', 'WEMSG.STYLEMANAGER.fwmarginright.title', $contentElements, $cssClasses);
        $objStyle->save();
    }

    private function fillObjStyle($pid, $alias, $titleKey, array $contentElements, array $cssClasses): StyleManagerModel
    {
        $objStyle = StyleManagerModel::findByAliasAndPid($alias, $pid) ?? new StyleManagerModel();
        $objStyle->pid = $pid;
        $objStyle->title = $this->translator->trans($titleKey, [], 'contao_default');
        $objStyle->alias = $alias;
        $objStyle->blankOption = true;
        $objStyle->chosen = true;
        $objStyle->tstamp = time();
        $objStyle->contentElements = serialize($contentElements);
        $objStyle->extendContentElement = true;
        $cssClasses = [
            ['key' => 'table-sm', 'value' => $this->translator->trans($titleKey, [], 'contao_default')],
        ];
        foreach ($cssClasses as $index => $cssClass) {
            if (!\array_key_exists('translated', $cssClass)) {
                $cssClasses[$index] = ['key' => $cssClass['key'], 'value' => $this->translator->trans($cssClass['value'], [], 'contao_default')];
            }
        }
        $objStyle->cssClasses = serialize($cssClasses);

        return $objStyle;
    }
}
