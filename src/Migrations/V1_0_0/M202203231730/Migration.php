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
        if (null !== $objArchiveBackground
        && null !== $objArchiveButton
        && null !== $objArchiveSeparator
        && null !== $objArchiveMargin
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
        } catch (\Exception $e) {
            $result
                ->setStatus(Result::STATUS_FAIL)
                ->addLog($e->getMessage())
            ;
        }

        return $result;
    }

    protected function manageBackgrounds(): void
    {
        $contentElements = self::$elements['background'];
        // Buttons
        $objArchive = StyleManagerArchiveModel::findByIdentifier('fwbackground') ?? new StyleManagerArchiveModel();
        $objArchive->title = $this->translator->trans('WEMSG.STYLEMANAGER.fwbackground.title', [], 'contao_default');
        $objArchive->description = '';
        $objArchive->identifier = 'fwbackground';
        $objArchive->groupAlias = 'Framway';
        $objArchive->tstamp = time();
        $objArchive->save();
        // Buttons - background
        $objStyle = StyleManagerModel::findByAliasAndPid('fwbackgroundcolor', $objArchive->id) ?? new StyleManagerModel();
        $objStyle->pid = $objArchive->id;
        $objStyle->title = $this->translator->trans('WEMSG.STYLEMANAGER.fwbackgroundcolor.title', [], 'contao_default');
        $objStyle->alias = 'fwbackgroundcolor';
        $objStyle->blankOption = true;
        $objStyle->chosen = true;
        $objStyle->tstamp = time();
        $objStyle->contentElements = serialize($contentElements);
        $objStyle->extendContentElement = true;

        $cssClasses = [
            ['key' => 'bg-primary', 'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwbackgroundcolor.bgPrimaryLabel', [], 'contao_default')],
            ['key' => 'bg-secondary', 'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwbackgroundcolor.bgSecondaryLabel', [], 'contao_default')],
            ['key' => 'bg-success', 'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwbackgroundcolor.bgSuccessLabel', [], 'contao_default')],
            ['key' => 'bg-error', 'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwbackgroundcolor.bgErrorLabel', [], 'contao_default')],
            ['key' => 'bg-warning', 'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwbackgroundcolor.bgWarningLabel', [], 'contao_default')],
        ];
        $colors = $this->configurationThemeManager->load()->getColors();
        foreach ($colors as $name => $hexa) {
            $cssClasses[] = [
                'key' => 'bg-'.$name,
                'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwbackgroundcolor.bgColorLabel', [
                    $this->translator->trans('WEMSG.FRAMWAY.COLORS.'.$name, [], 'contao_default'),
                ], 'contao_default'), ];
        }
        $objStyle->cssClasses = serialize($cssClasses);
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
        $objStyle = StyleManagerModel::findByAliasAndPid('fwbuttonsize', $objArchive->id) ?? new StyleManagerModel();
        $objStyle->pid = $objArchive->id;
        $objStyle->title = $this->translator->trans('WEMSG.STYLEMANAGER.fwbuttonsize.title', [], 'contao_default');
        $objStyle->alias = 'fwbuttonsize';
        $objStyle->blankOption = true;
        $objStyle->chosen = true;
        $objStyle->tstamp = time();
        $objStyle->contentElements = serialize($contentElements);
        $objStyle->extendContentElement = true;
        $objStyle->cssClasses = serialize([
            ['key' => 'btn', 'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwbuttonsize.sizeLabel', [], 'contao_default')],
            ['key' => 'btn-sm', 'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwbuttonsize.sizeSmLabel', [], 'contao_default')],
            ['key' => 'btn-lg', 'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwbuttonsize.sizeLgLabel', [], 'contao_default')],
        ]);
        $objStyle->save();
        // Buttons - background
        $objStyle = StyleManagerModel::findByAliasAndPid('fwbuttonbackground', $objArchive->id) ?? new StyleManagerModel();
        $objStyle->pid = $objArchive->id;
        $objStyle->title = $this->translator->trans('WEMSG.STYLEMANAGER.fwbuttonbackground.title', [], 'contao_default');
        $objStyle->alias = 'fwbuttonbackground';
        $objStyle->blankOption = true;
        $objStyle->chosen = true;
        $objStyle->tstamp = time();
        $objStyle->contentElements = serialize($contentElements);
        $objStyle->extendContentElement = true;

        $cssClasses = [
            ['key' => 'btn-bg-primary', 'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwbuttonbackground.bgPrimaryLabel', [], 'contao_default')],
            ['key' => 'btn-bg-secondary', 'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwbuttonbackground.bgSecondaryLabel', [], 'contao_default')],
            ['key' => 'btn-bg-success', 'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwbuttonbackground.bgSuccessLabel', [], 'contao_default')],
            ['key' => 'btn-bg-error', 'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwbuttonbackground.bgErrorLabel', [], 'contao_default')],
            ['key' => 'btn-bg-warning', 'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwbuttonbackground.bgWarningLabel', [], 'contao_default')],
        ];

        $colors = $this->configurationThemeManager->load()->getColors();
        foreach ($colors as $name => $hexa) {
            $cssClasses[] = [
                'key' => 'btn-bg-'.$name,
                'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwbuttonbackground.bgColorLabel', [
                    $this->translator->trans('WEMSG.FRAMWAY.COLORS.'.$name, [], 'contao_default'),
                ], 'contao_default'), ];
        }
        $objStyle->cssClasses = serialize($cssClasses);
        $objStyle->save();
        // Buttons - border
        $objStyle = StyleManagerModel::findByAliasAndPid('fwbuttonborder', $objArchive->id) ?? new StyleManagerModel();
        $objStyle->pid = $objArchive->id;
        $objStyle->title = $this->translator->trans('WEMSG.STYLEMANAGER.fwbuttonborder.title', [], 'contao_default');
        $objStyle->alias = 'fwbuttonborder';
        $objStyle->blankOption = true;
        $objStyle->chosen = true;
        $objStyle->tstamp = time();
        $objStyle->contentElements = serialize($contentElements);
        $objStyle->extendContentElement = true;

        $cssClasses = [
            ['key' => 'btn-bd-primary', 'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwbuttonborder.bdPrimaryLabel', [], 'contao_default')],
            ['key' => 'btn-bd-secondary', 'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwbuttonborder.bdSecondaryLabel', [], 'contao_default')],
            ['key' => 'btn-bd-success', 'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwbuttonborder.bdSuccessLabel', [], 'contao_default')],
            ['key' => 'btn-bd-error', 'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwbuttonborder.bdErrorLabel', [], 'contao_default')],
            ['key' => 'btn-bd-warning', 'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwbuttonborder.bdWarningLabel', [], 'contao_default')],
        ];
        foreach ($colors as $name => $hexa) {
            $cssClasses[] = [
                'key' => 'btn-bd-'.$name,
                'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwbuttonborder.bdColorLabel', [
                    $this->translator->trans('WEMSG.FRAMWAY.COLORS.'.$name, [], 'contao_default'),
                ], 'contao_default'), ];
        }
        $objStyle->cssClasses = serialize($cssClasses);
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
        $objStyle = StyleManagerModel::findByAliasAndPid('fwseparatortop', $objArchive->id) ?? new StyleManagerModel();
        $objStyle->pid = $objArchive->id;
        $objStyle->title = $this->translator->trans('WEMSG.STYLEMANAGER.fwseparatortop.title', [], 'contao_default');
        $objStyle->alias = 'fwseparatortop';
        $objStyle->blankOption = true;
        $objStyle->chosen = true;
        $objStyle->tstamp = time();
        $objStyle->contentElements = serialize($contentElements);
        $objStyle->extendContentElement = true;
        $objStyle->cssClasses = serialize([
            ['key' => 'sep-top', 'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwseparatortop.label', [], 'contao_default')],
        ]);
        $objStyle->save();
        // separators - bottom
        $objStyle = StyleManagerModel::findByAliasAndPid('fwseparatorbottom', $objArchive->id) ?? new StyleManagerModel();
        $objStyle->pid = $objArchive->id;
        $objStyle->title = $this->translator->trans('WEMSG.STYLEMANAGER.fwseparatorbottom.title', [], 'contao_default');
        $objStyle->alias = 'fwseparatorbottom';
        $objStyle->blankOption = true;
        $objStyle->chosen = true;
        $objStyle->tstamp = time();
        $objStyle->contentElements = serialize($contentElements);
        $objStyle->extendContentElement = true;
        $objStyle->cssClasses = serialize([
            ['key' => 'sep-bottom', 'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwseparatorbottom.label', [], 'contao_default')],
        ]);
        $objStyle->save();
        // separators - left
        $objStyle = StyleManagerModel::findByAliasAndPid('fwseparatorleft', $objArchive->id) ?? new StyleManagerModel();
        $objStyle->pid = $objArchive->id;
        $objStyle->title = $this->translator->trans('WEMSG.STYLEMANAGER.fwseparatorleft.title', [], 'contao_default');
        $objStyle->alias = 'fwseparatorleft';
        $objStyle->blankOption = true;
        $objStyle->chosen = true;
        $objStyle->tstamp = time();
        $objStyle->contentElements = serialize($contentElements);
        $objStyle->extendContentElement = true;
        $objStyle->cssClasses = serialize([
            ['key' => 'sep-left', 'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwseparatorleft.label', [], 'contao_default')],
        ]);
        $objStyle->save();
        // separators - right
        $objStyle = StyleManagerModel::findByAliasAndPid('fwseparatorright', $objArchive->id) ?? new StyleManagerModel();
        $objStyle->pid = $objArchive->id;
        $objStyle->title = $this->translator->trans('WEMSG.STYLEMANAGER.fwseparatorright.title', [], 'contao_default');
        $objStyle->alias = 'fwseparatorright';
        $objStyle->blankOption = true;
        $objStyle->chosen = true;
        $objStyle->tstamp = time();
        $objStyle->contentElements = serialize($contentElements);
        $objStyle->extendContentElement = true;
        $objStyle->cssClasses = serialize([
            ['key' => 'sep-right', 'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwseparatorright.label', [], 'contao_default')],
        ]);
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
        $objStyle = StyleManagerModel::findByAliasAndPid('fwmargintop', $objArchive->id) ?? new StyleManagerModel();
        $objStyle->pid = $objArchive->id;
        $objStyle->title = $this->translator->trans('WEMSG.STYLEMANAGER.fwmargintop.title', [], 'contao_default');
        $objStyle->alias = 'fwmargintop';
        $objStyle->blankOption = true;
        $objStyle->chosen = true;
        $objStyle->tstamp = time();
        $objStyle->contentElements = serialize($contentElements);
        $objStyle->extendContentElement = true;
        $objStyle->cssClasses = serialize([
            ['key' => 'm-top-0', 'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwmargintop.noLabel', [], 'contao_default')],
            ['key' => 'm-top', 'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwmargintop.label', [], 'contao_default')],
            ['key' => 'm-top-x2', 'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwmargintop.doubleLabel', [], 'contao_default')],
        ]);
        $objStyle->save();
        // margins - bottom
        $objStyle = StyleManagerModel::findByAliasAndPid('fwmarginbottom', $objArchive->id) ?? new StyleManagerModel();
        $objStyle->pid = $objArchive->id;
        $objStyle->title = $this->translator->trans('WEMSG.STYLEMANAGER.fwmarginbottom.title', [], 'contao_default');
        $objStyle->alias = 'fwmarginbottom';
        $objStyle->blankOption = true;
        $objStyle->chosen = true;
        $objStyle->tstamp = time();
        $objStyle->contentElements = serialize($contentElements);
        $objStyle->extendContentElement = true;
        $objStyle->cssClasses = serialize([
            ['key' => 'm-bottom-0', 'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwmarginbottom.noLabel', [], 'contao_default')],
            ['key' => 'm-bottom', 'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwmarginbottom.label', [], 'contao_default')],
            ['key' => 'm-bottom-x2', 'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwmarginbottom.doubleLabel', [], 'contao_default')],
        ]);
        $objStyle->save();
        // margins - left
        $objStyle = StyleManagerModel::findByAliasAndPid('fwmarginleft', $objArchive->id) ?? new StyleManagerModel();
        $objStyle->pid = $objArchive->id;
        $objStyle->title = $this->translator->trans('WEMSG.STYLEMANAGER.fwmarginleft.title', [], 'contao_default');
        $objStyle->alias = 'fwmarginleft';
        $objStyle->blankOption = true;
        $objStyle->chosen = true;
        $objStyle->tstamp = time();
        $objStyle->contentElements = serialize($contentElements);
        $objStyle->extendContentElement = true;
        $objStyle->cssClasses = serialize([
            ['key' => 'm-left-0', 'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwmarginleft.noLabel', [], 'contao_default')],
            ['key' => 'm-left', 'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwmarginleft.label', [], 'contao_default')],
            ['key' => 'm-left-x2', 'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwmarginleft.doubleLabel', [], 'contao_default')],
        ]);
        $objStyle->save();
        // margins - right
        $objStyle = StyleManagerModel::findByAliasAndPid('fwmarginright', $objArchive->id) ?? new StyleManagerModel();
        $objStyle->pid = $objArchive->id;
        $objStyle->title = $this->translator->trans('WEMSG.STYLEMANAGER.fwmarginright.title', [], 'contao_default');
        $objStyle->alias = 'fwmarginright';
        $objStyle->blankOption = true;
        $objStyle->chosen = true;
        $objStyle->tstamp = time();
        $objStyle->contentElements = serialize($contentElements);
        $objStyle->extendContentElement = true;
        $objStyle->cssClasses = serialize([
            ['key' => 'm-right-0', 'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwmarginright.noLabel', [], 'contao_default')],
            ['key' => 'm-right', 'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwmarginright.label', [], 'contao_default')],
            ['key' => 'm-right-x2', 'value' => $this->translator->trans('WEMSG.STYLEMANAGER.fwmarginright.doubleLabel', [], 'contao_default')],
        ]);
        $objStyle->save();
    }
}
