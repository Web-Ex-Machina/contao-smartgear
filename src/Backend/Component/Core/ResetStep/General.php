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

namespace WEM\SmartgearBundle\Backend\Component\Core\ResetStep;

use Contao\Input;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\ThemeModel;
use WEM\SmartgearBundle\Backup\BackupManager;
use WEM\SmartgearBundle\Backup\Model\Results\CreateResult;
use WEM\SmartgearBundle\Classes\Backend\AbstractStep;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Config\Core as CoreConfig;
use WEM\SmartgearBundle\Config\LocalConfig;
use WEM\SmartgearBundle\Config\Manager\LocalConfig as LocalConfigManager;

class General extends AbstractStep
{
    /** @var ConfigurationManager */
    protected $configurationManager;
    /** @var LocalConfigManager */
    protected $localConfigManager;
    /** @var BackupManager */
    protected $backupManager;
    /** @var array */
    protected $templatesDirs;

    protected $strTemplate = 'be_wem_sg_install_block_reset_step_core_general';

    public function __construct(
        string $module,
        string $type,
        ConfigurationManager $configurationManager,
        LocalConfigManager $localConfigManager,
        BackupManager $backupManager,
        array $templatesDirs
    ) {
        parent::__construct($module, $type);
        $this->configurationManager = $configurationManager;
        $this->localConfigManager = $localConfigManager;
        $this->backupManager = $backupManager;
        $this->templatesDirs = $templatesDirs;
        $this->title = $GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['Title'];

        $this->addCheckboxField('localconfig', $GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['localconfig'], 'localconfig', false, false, '', '', $GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['localconfigHelp']);
        $this->addCheckboxField('framway', $GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['framway'], 'framway', false, false, '', '', $GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['framwayHelp']);
        $this->addCheckboxField('templates', $GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['templates'], 'templates', false, false, '', '', sprintf($GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['templatesHelp'], implode('</code>,<code>', $this->templatesDirs)));
        $this->addCheckboxField('themes_modules', $GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['themes_modules'], 'themes_modules', false, false, '', '', $GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['themes_modulesHelp']);
        $this->addCheckboxField('pages', $GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['pages'], 'pages', false, false, '', '', $GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['pagesHelp']);
        $this->addCheckboxField('files', $GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['files'], 'files', false, false, '', '', $GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['filesHelp']);
        $this->addCheckboxField('backup', $GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['backup'], 'backup', true, false, '', '', $GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['backupHelp']);
    }

    public function isStepValid(): bool
    {
        // check if the step is correct

        return true;
    }

    public function do(): void
    {
        // do what is meant to be done in this step
        $this->backup();
        $this->reset();
    }

    protected function backup(): void
    {
        /** @var CreateResult */
        $createResult = $this->backupManager->newFromConfigurationReset();
        $this->addConfirm(sprintf($GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['backupCompleted'], $createResult->getBackup()->getFile()->basename));
    }

    protected function reset(): void
    {
        // reset everything except what we wanted to keep
        if (Input::post('localconfig')) {
            $this->addConfirm($GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['localconfigKept']);
        } else {
            $this->resetLocalConfig();
            $this->addConfirm($GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['localconfigDeleted']);
        }
        if (Input::post('framway')) {
            $this->addConfirm($GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['framwayKept']);
        } else {
            $this->resetFramway();
            $this->addConfirm($GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['framwayDeleted']);
        }
        if (Input::post('templates')) {
            $this->addConfirm($GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['templatesKept']);
        } else {
            $this->resetTemplates();
            $this->addConfirm($GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['templatesDeleted']);
        }
        if (Input::post('themes_modules')) {
            $this->addConfirm($GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['themes_modulesKept']);
        } else {
            $this->resetThemesModules();
            $this->addConfirm($GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['themes_modulesDeleted']);
        }
        if (Input::post('pages')) {
            $this->addConfirm($GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['pagesKept']);
        } else {
            $this->resetPages();
            $this->addConfirm($GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['pagesDeleted']);
        }
        if (Input::post('files')) {
            $this->addConfirm($GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['filesKept']);
        } else {
            $this->resetFiles();
            $this->addConfirm($GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['filesDeleted']);
        }
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
            $modules->delete();
        }
        $themes = ThemeModel::findAll();
        if ($themes) {
            $themes->delete();
        }
    }

    protected function resetPages(): void
    {
        $pages = PageModel::findAll();
        if ($pages) {
            $pages->delete();
        }
    }

    protected function resetFiles(): void
    {
        $folder = new \Contao\Folder('files');
        $folder->purge();

        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        $config->setSgOwnerLogo('');
        $this->configurationManager->save($config);
    }
}
