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

namespace WEM\SmartgearBundle\Backend\Module\Core\ResetStep;

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
        $this->title = 'Général';

        $this->addCheckboxField('localconfig', 'Configuration locale', 'localconfig', false, false, '', '', 'Configuration supplémentaire dans le fichier <code>congif/config.yml</code>');
        $this->addCheckboxField('framway', 'Framway', 'framway', false, false, '', '', 'Le framway sera supprimé, il faudra le récupérer à nouveau.');
        $this->addCheckboxField('templates', 'Templates', 'templates', false, false, '', '', 'Les dossiers <code>'.implode('</code>,<code>', $this->templatesDirs).'</code> seront supprimés');
        $this->addCheckboxField('themes_modules', 'Thèmes & Modules', 'themes_modules', false, false, '', '', 'Tous les thèmes & modules seront supprimés');
        $this->addCheckboxField('pages', 'Pages', 'pages', false, false, '', '', 'Toutes les pages seront supprimées');
        $this->addCheckboxField('files', 'Fichiers clients', 'files', false, false, '', '', 'Tous les fichiers seront supprimés');
        $this->addCheckboxField('backup', 'Effectuer une sauvegarde avant la réinitialisation', 'backup', true, false, '', '', 'Il est vivement conseillé d\'effectuer une sauvegarde');
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
        $this->addConfirm(sprintf('Backup "%s" effectué.', $createResult->getBackup()->getFile()->basename));
    }

    protected function reset(): void
    {
        // reset everything except what we wanted to keep
        if (Input::post('localconfig')) {
            $this->addConfirm('localconfig conservée');
        } else {
            $this->resetLocalConfig();
            $this->addConfirm('localconfig détruite');
        }
        if (Input::post('framway')) {
            $this->addConfirm('Framway conservé');
        } else {
            $this->resetFramway();
            $this->addConfirm('Framway détruit');
        }
        if (Input::post('templates')) {
            $this->addConfirm('Templates conservés');
        } else {
            $this->resetTemplates();
            $this->addConfirm('Templates détruits');
        }
        if (Input::post('themes_modules')) {
            $this->addConfirm('Themes & Modules conservés');
        } else {
            $this->resetThemesModules();
            $this->addConfirm('Themes & Modules détruits');
        }
        if (Input::post('pages')) {
            $this->addConfirm('Pages conservées');
        } else {
            $this->resetPages();
            $this->addConfirm('Pages détruites');
        }
        if (Input::post('files')) {
            $this->addConfirm('Fichiers conservés');
        } else {
            $this->resetFiles();
            $this->addConfirm('Fichiers détruits');
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
        $modules->delete();
        $themes = ThemeModel::findAll();
        $themes->delete();
    }

    protected function resetPages(): void
    {
        $pages = PageModel::findAll();
        $pages->delete();
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
