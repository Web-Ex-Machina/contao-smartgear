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

namespace WEM\SmartgearBundle\Backend\Component\Core;

use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\ImageSizeModel;
use Contao\LayoutModel;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\StyleSheetModel;
use Contao\ThemeModel;
use NotificationCenter\Model\Language as NotificationLanguageModel;
use NotificationCenter\Model\Message as NotificationMessageModel;
use NotificationCenter\Model\Notification as NotificationModel;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Analyzer\Htaccess as HtaccessAnalyzer;
use WEM\SmartgearBundle\Classes\Backend\Resetter as BackendResetter;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Config\Manager\LocalConfig as LocalConfigManager;

class Resetter extends BackendResetter
{
    /** @var string */
    protected $module = '';
    /** @var string */
    protected $type = '';
    /** @var ConfigurationManager */
    protected $configurationManager;
    /** @var HtaccessAnalyzer */
    protected $htaccessAnalyzer;
    /** @var TranslatorInterface */
    protected $translator;
    /** @var LocalConfigManager */
    protected $localConfigManager;
    /** @var array */
    protected $templatesDirs;
    /** @var array */
    protected $componentsResetters;
    /** @var array */
    protected $modulesResetters;

    /**
     * Generic array of logs.
     *
     * @var array
     */
    protected $logs = [];

    public function __construct(
        ConfigurationManager $configurationManager,
        TranslatorInterface $translator,
        LocalConfigManager $localConfigManager,
        HtaccessAnalyzer $htaccessAnalyzer,
        string $module,
        string $type,
        array $templatesDirs,
        array $componentsResetters,
        array $modulesResetters
    ) {
        parent::__construct($configurationManager, $translator, $module, $type);
        $this->localConfigManager = $localConfigManager;
        $this->htaccessAnalyzer = $htaccessAnalyzer;
        $this->templatesDirs = $templatesDirs;
        $this->componentsResetters = $componentsResetters;
        $this->modulesResetters = $modulesResetters;
    }

    public function reset(bool $keepFramway, bool $keepTemplates, bool $keepThemesModules, bool $keepPages, bool $keepFiles, bool $keepLocalconfig): void
    {
        $itemsResetted = 0;
        // reset everything except what we wanted to keep
        if ($keepFramway) {
            $this->addConfirm($GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['framwayKept']);
        } else {
            $this->resetFramway();
            $this->addConfirm($GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['framwayDeleted']);
            ++$itemsResetted;
        }
        if ($keepTemplates) {
            $this->addConfirm($GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['templatesKept']);
        } else {
            $this->resetTemplates();
            $this->addConfirm($GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['templatesDeleted']);
            ++$itemsResetted;
        }
        if ($keepThemesModules) {
            $this->addConfirm($GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['themes_modulesKept']);
        } else {
            $this->resetThemesModules();
            $this->addConfirm($GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['themes_modulesDeleted']);
            ++$itemsResetted;
        }
        if ($keepPages) {
            $this->addConfirm($GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['pagesKept']);
        } else {
            $this->resetPages();
            $this->addConfirm($GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['pagesDeleted']);
            ++$itemsResetted;
        }
        if ($keepFiles) {
            $this->addConfirm($GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['filesKept']);
        } else {
            $this->resetFiles();
            $this->addConfirm($GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['filesDeleted']);
            ++$itemsResetted;
        }
        if ($keepLocalconfig) {
            $this->addConfirm($GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['localconfigKept']);
        } else {
            $this->resetLocalConfig();
            $this->addConfirm($GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['localconfigDeleted']);
            ++$itemsResetted;
        }
        if (0 !== $itemsResetted) {
            $this->markModulesAndComponentsAsUninstalled();
        }
        $this->disableFramwayAssetsManagementRules();
    }

    protected function markModulesAndComponentsAsUninstalled(): void
    {
        foreach ($this->componentsResetters as $resetter) {
            $resetter->reset('delete');
        }

        foreach ($this->modulesResetters as $resetter) {
            $resetter->reset('delete');
        }

        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        $timestamp = time();
        // mark all modules & components as uninstalled
        $submodulesConfig = $config->getSubmodulesConfigs();
        foreach ($submodulesConfig as $key => $submoduleConfig) {
            $submoduleConfig
                ->setSgInstallComplete(false)
            ;
            $config->setSubmoduleConfig($key, $submoduleConfig);
        }

        $this->configurationManager->save($config);
    }

    protected function resetLocalConfig(): void
    {
        /** @var LocalConfig */
        $config = $this->localConfigManager->load();

        $config->reset();

        $this->localConfigManager->save($config);
    }

    protected function resetFramway(): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        $folder = new \Contao\Folder($config->getSgFramwayPath());
        $folder->delete();
    }

    protected function resetTemplates(): void
    {
        foreach ($this->templatesDirs as $templatesDir) {
            $folder = new \Contao\Folder($templatesDir);
            $folder->delete();
        }
    }

    protected function resetThemesModules(): void
    {
        $modules = ModuleModel::findAll();
        if ($modules) {
            while ($modules->next()) {
                $modules->delete();
            }
        }

        $themes = ThemeModel::findAll();
        if ($themes) {
            while ($themes->next()) {
                $layouts = LayoutModel::findBy('pid', $themes->id);
                if ($layouts) {
                    while ($layouts->next()) {
                        $layouts->delete();
                    }
                }
                $imageSizes = ImageSizeModel::findBy('pid', $themes->id);
                if ($imageSizes) {
                    while ($imageSizes->next()) {
                        $imageSizes->delete();
                    }
                }
                /**  @deprecated in Contao 5.0  */
                $stylesheets = StyleSheetModel::findBy('pid', $themes->id);
                if ($stylesheets) {
                    while ($stylesheets->next()) {
                        $stylesheets->delete();
                    }
                }
                $themes->delete();
            }
        }

        $notifications = NotificationModel::findAll();
        if ($notifications) {
            while ($notifications->next()) {
                $messages = NotificationMessageModel::findBy('pid', $notifications->id);
                if ($messages) {
                    while ($messages->next()) {
                        $languages = NotificationLanguageModel::findBy('pid', $messages->id);
                        if ($languages) {
                            while ($languages->next()) {
                                $languages->delete();
                            }
                        }
                        $messages->delete();
                    }
                }
                $notifications->delete();
            }
        }
    }

    protected function resetPages(): void
    {
        $pages = PageModel::findAll();
        if ($pages) {
            while ($pages->next()) {
                $pages->delete();
            }
        }
        $contents = ContentModel::findAll();
        if ($contents) {
            while ($contents->next()) {
                $contents->delete();
            }
        }
        $articles = ArticleModel::findAll();
        if ($articles) {
            while ($articles->next()) {
                $articles->delete();
            }
        }
    }

    protected function resetFiles(): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        $folder = new \Contao\Folder('files');
        $folder->purge();

        $config->setSgOwnerLogo('');
        $this->configurationManager->save($config);
    }

    protected function disableFramwayAssetsManagementRules(): void
    {
        $this->htaccessAnalyzer->disableFramwayAssetsManagementRules();
    }
}
