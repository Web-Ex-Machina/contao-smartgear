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
            if (null === StyleManagerModel::findByAliasAndPid('fwbackgroundcolor', $objArchiveBackground->id)
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
        /** @todo : update those elements when they have been renamed ? */
        $contentElements = ['headline', 'text'];
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
        /** @todo : update those elements when they have been renamed ? */
        $contentElements = ['hyperlink'];
        // Buttons
        $objArchive = StyleManagerArchiveModel::findByIdentifier('fwbutton') ?? new StyleManagerArchiveModel();
        $objArchive->title = 'Framway - Buttons';
        $objArchive->description = '';
        $objArchive->identifier = 'fwbutton';
        $objArchive->groupAlias = 'Framway';
        $objArchive->tstamp = time();
        $objArchive->save();
        // Buttons - size
        $objStyle = StyleManagerModel::findByAliasAndPid('fwbuttonsize', $objArchive->id) ?? new StyleManagerModel();
        $objStyle->pid = $objArchive->id;
        $objStyle->title = 'Framway - Buttons - Size';
        $objStyle->alias = 'fwbuttonsize';
        $objStyle->blankOption = true;
        $objStyle->chosen = true;
        $objStyle->tstamp = time();
        $objStyle->contentElements = serialize($contentElements);
        $objStyle->extendContentElement = true;
        $objStyle->cssClasses = serialize([
            ['key' => 'btn', 'value' => 'Transforme l\'élément en bouton'],
            ['key' => 'btn-sm', 'value' => 'Transforme l\'élément en bouton de petite taille'],
            ['key' => 'btn-lg', 'value' => 'Transforme l\'élément en bouton de grande taille'],
        ]);
        $objStyle->save();
        // Buttons - background
        $objStyle = StyleManagerModel::findByAliasAndPid('fwbuttonbackground', $objArchive->id) ?? new StyleManagerModel();
        $objStyle->pid = $objArchive->id;
        $objStyle->title = 'Framway - Buttons - Background';
        $objStyle->alias = 'fwbuttonbackground';
        $objStyle->blankOption = true;
        $objStyle->chosen = true;
        $objStyle->tstamp = time();
        $objStyle->contentElements = serialize($contentElements);
        $objStyle->extendContentElement = true;

        /* @todo : make UtilFramway method to get colors */
        $objStyle->cssClasses = serialize([
            ['key' => 'btn-bg-primary', 'value' => 'Transforme l\'élément en bouton avec un background de couleur primaire'],
            ['key' => 'btn-bg-secondary', 'value' => 'Transforme l\'élément en bouton avec un background de couleur secondaire'],
            ['key' => 'btn-bg-success', 'value' => 'Transforme l\'élément en bouton avec un background correspondant à la couleur utilisée pour signaler un succès'],
            ['key' => 'btn-bg-error', 'value' => 'Transforme l\'élément en bouton avec un background correspondant à la couleur utilisée pour signaler une erreur'],
            ['key' => 'btn-bg-warning', 'value' => 'Transforme l\'élément en bouton avec un background correspondant à la couleur utilisée pour signaler un problème'],
        ]);
        $objStyle->save();
        // Buttons - border
        $objStyle = StyleManagerModel::findByAliasAndPid('fwbuttonborder', $objArchive->id) ?? new StyleManagerModel();
        $objStyle->pid = $objArchive->id;
        $objStyle->title = 'Framway - Buttons - Border';
        $objStyle->alias = 'fwbuttonborder';
        $objStyle->blankOption = true;
        $objStyle->chosen = true;
        $objStyle->tstamp = time();
        $objStyle->contentElements = serialize($contentElements);
        $objStyle->extendContentElement = true;

        /* @todo : make UtilFramway method to get colors */
        $objStyle->cssClasses = serialize([
            ['key' => 'btn-bd-primary', 'value' => 'Transforme l\'élément en bouton avec une bordure de couleur primaire'],
            ['key' => 'btn-bd-secondary', 'value' => 'Transforme l\'élément en bouton avec une bordure de couleur secondaire'],
            ['key' => 'btn-bd-success', 'value' => 'Transforme l\'élément en bouton avec une bordure correspondant à la couleur utilisée pour signaler un succès'],
            ['key' => 'btn-bd-error', 'value' => 'Transforme l\'élément en bouton avec une bordure correspondant à la couleur utilisée pour signaler une erreur'],
            ['key' => 'btn-bd-warning', 'value' => 'Transforme l\'élément en bouton avec une bordure correspondant à la couleur utilisée pour signaler un problème'],
        ]);
        $objStyle->save();
    }

    protected function manageSeparators(): void
    {
        /** @todo : update those elements when they have been renamed ? */
        $contentElements = ['headline'];
        // separators
        $objArchive = StyleManagerArchiveModel::findByIdentifier('fwseparator') ?? new StyleManagerArchiveModel();
        $objArchive->title = 'Framway - Separators';
        $objArchive->description = '';
        $objArchive->identifier = 'fwseparator';
        $objArchive->groupAlias = 'Framway';
        $objArchive->tstamp = time();
        $objArchive->save();
        // separators - top
        $objStyle = StyleManagerModel::findByAliasAndPid('fwseparatortop', $objArchive->id) ?? new StyleManagerModel();
        $objStyle->pid = $objArchive->id;
        $objStyle->title = 'Framway - Separators - Top';
        $objStyle->alias = 'fwseparatortop';
        $objStyle->blankOption = true;
        $objStyle->chosen = true;
        $objStyle->tstamp = time();
        $objStyle->contentElements = serialize($contentElements);
        $objStyle->extendContentElement = true;
        $objStyle->cssClasses = serialize([
            ['key' => 'sep-top', 'value' => 'Ajoute une bordure séparatrice en haut de l\'élément'],
        ]);
        $objStyle->save();
        // separators - bottom
        $objStyle = StyleManagerModel::findByAliasAndPid('fwseparatorbottom', $objArchive->id) ?? new StyleManagerModel();
        $objStyle->pid = $objArchive->id;
        $objStyle->title = 'Framway - Separators - Bottom';
        $objStyle->alias = 'fwseparatorbottom';
        $objStyle->blankOption = true;
        $objStyle->chosen = true;
        $objStyle->tstamp = time();
        $objStyle->contentElements = serialize($contentElements);
        $objStyle->extendContentElement = true;
        $objStyle->cssClasses = serialize([
            ['key' => 'sep-bottom', 'value' => 'Ajoute une bordure séparatrice en bas de l\'élément'],
        ]);
        $objStyle->save();
        // separators - left
        $objStyle = StyleManagerModel::findByAliasAndPid('fwseparatorleft', $objArchive->id) ?? new StyleManagerModel();
        $objStyle->pid = $objArchive->id;
        $objStyle->title = 'Framway - Separators - Left';
        $objStyle->alias = 'fwseparatorleft';
        $objStyle->blankOption = true;
        $objStyle->chosen = true;
        $objStyle->tstamp = time();
        $objStyle->contentElements = serialize($contentElements);
        $objStyle->extendContentElement = true;
        $objStyle->cssClasses = serialize([
            ['key' => 'sep-left', 'value' => 'Ajoute une bordure séparatrice à gauche de l\'élément'],
        ]);
        $objStyle->save();
        // separators - right
        $objStyle = StyleManagerModel::findByAliasAndPid('fwseparatorright', $objArchive->id) ?? new StyleManagerModel();
        $objStyle->pid = $objArchive->id;
        $objStyle->title = 'Framway - Separators - Right';
        $objStyle->alias = 'fwseparatorright';
        $objStyle->blankOption = true;
        $objStyle->chosen = true;
        $objStyle->tstamp = time();
        $objStyle->contentElements = serialize($contentElements);
        $objStyle->extendContentElement = true;
        $objStyle->cssClasses = serialize([
            ['key' => 'sep-right', 'value' => 'Ajoute une bordure séparatrice à droite de l\'élément'],
        ]);
        $objStyle->save();
    }

    protected function manageMargins(): void
    {
        /** @todo : update those elements when they have been renamed ? */
        $contentElements = ['headline', 'text', 'table', 'rsce_listIcons', 'rsce_quote', 'accordionStart', 'accordionStop', 'sliderStart', 'sliderStop', 'hyperlink', 'image', 'player', 'youtube', 'vimeo', 'rsce_timeline', 'grid-start', 'grid-stop', 'rsce_accordionFW', 'rsce_block-img', 'rsce_counterFW', 'rsce_gridGallery', 'rsce_heroFWStart', 'rsce_heroFWStop', 'rsce_priceCards', 'rsce_sliderFW', 'rsce_tabs', 'rsce_testimonials'];
        // margins
        $objArchive = StyleManagerArchiveModel::findByIdentifier('fwmargin') ?? new StyleManagerArchiveModel();
        $objArchive->title = 'Framway - Margins';
        $objArchive->description = '';
        $objArchive->identifier = 'fwmargin';
        $objArchive->groupAlias = 'Framway';
        $objArchive->tstamp = time();
        $objArchive->save();
        // margins - top
        $objStyle = StyleManagerModel::findByAliasAndPid('fwmargintop', $objArchive->id) ?? new StyleManagerModel();
        $objStyle->pid = $objArchive->id;
        $objStyle->title = 'Framway - Margins - Top';
        $objStyle->alias = 'fwmargintop';
        $objStyle->blankOption = true;
        $objStyle->chosen = true;
        $objStyle->tstamp = time();
        $objStyle->contentElements = serialize($contentElements);
        $objStyle->extendContentElement = true;
        $objStyle->cssClasses = serialize([
            ['key' => 'm-top-0', 'value' => 'Retire la marge en haut de l\'élément'],
            ['key' => 'm-top', 'value' => 'Applique une marge en haut de l\'élément'],
            ['key' => 'm-top-x2', 'value' => 'Applique une marge doublée en haut de l\'élément'],
        ]);
        $objStyle->save();
        // margins - bottom
        $objStyle = StyleManagerModel::findByAliasAndPid('fwmarginbottom', $objArchive->id) ?? new StyleManagerModel();
        $objStyle->pid = $objArchive->id;
        $objStyle->title = 'Framway - Margins - Bottom';
        $objStyle->alias = 'fwmarginbottom';
        $objStyle->blankOption = true;
        $objStyle->chosen = true;
        $objStyle->tstamp = time();
        $objStyle->contentElements = serialize($contentElements);
        $objStyle->extendContentElement = true;
        $objStyle->cssClasses = serialize([
            ['key' => 'm-bottom-0', 'value' => 'Retire la marge en bas de l\'élément'],
            ['key' => 'm-bottom', 'value' => 'Applique une marge en bas de l\'élément'],
            ['key' => 'm-bottom-x2', 'value' => 'Applique une marge doublée en bas de l\'élément'],
        ]);
        $objStyle->save();
        // margins - left
        $objStyle = StyleManagerModel::findByAliasAndPid('fwmarginleft', $objArchive->id) ?? new StyleManagerModel();
        $objStyle->pid = $objArchive->id;
        $objStyle->title = 'Framway - Margins - Left';
        $objStyle->alias = 'fwmarginleft';
        $objStyle->blankOption = true;
        $objStyle->chosen = true;
        $objStyle->tstamp = time();
        $objStyle->contentElements = serialize($contentElements);
        $objStyle->extendContentElement = true;
        $objStyle->cssClasses = serialize([
            ['key' => 'm-left-0', 'value' => 'Retire la marge à gauche de l\'élément'],
            ['key' => 'm-left', 'value' => 'Applique une marge à gauche de l\'élément'],
            ['key' => 'm-left-x2', 'value' => 'Applique une marge doublée à gauche de l\'élément'],
        ]);
        $objStyle->save();
        // margins - right
        $objStyle = StyleManagerModel::findByAliasAndPid('fwmarginright', $objArchive->id) ?? new StyleManagerModel();
        $objStyle->pid = $objArchive->id;
        $objStyle->title = 'Framway - Margins - Right';
        $objStyle->alias = 'fwmarginright';
        $objStyle->blankOption = true;
        $objStyle->chosen = true;
        $objStyle->tstamp = time();
        $objStyle->contentElements = serialize($contentElements);
        $objStyle->extendContentElement = true;
        $objStyle->cssClasses = serialize([
            ['key' => 'm-right-0', 'value' => 'Retire la marge à droite de l\'élément'],
            ['key' => 'm-right', 'value' => 'Applique une marge à droite de l\'élément'],
            ['key' => 'm-right-x2', 'value' => 'Applique une marge doublée à droite de l\'élément'],
        ]);
        $objStyle->save();
    }
}
