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
use WEM\SmartgearBundle\Backend\Component\Core\Resetter;
use WEM\SmartgearBundle\Backup\BackupManager;
use WEM\SmartgearBundle\Backup\Model\Results\CreateResult;
use WEM\SmartgearBundle\Classes\Backend\AbstractStep;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;

class General extends AbstractStep
{
    /** @var ConfigurationManager */
    protected $configurationManager;
    /** @var BackupManager */
    protected $backupManager;
    /** @var Resetter */
    protected $resetter;

    protected $strTemplate = 'be_wem_sg_install_block_reset_step_core_general';

    public function __construct(
        string $module,
        string $type,
        ConfigurationManager $configurationManager,
        BackupManager $backupManager,
        Resetter $resetter
    ) {
        parent::__construct($module, $type);
        $this->configurationManager = $configurationManager;
        $this->backupManager = $backupManager;
        $this->resetter = $resetter;
        $this->title = $GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['Title'];

        $this->addCheckboxField('localconfig', $GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['localconfig'], 'localconfig', false, false, '', '', $GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['localconfigHelp']);
        $this->addCheckboxField('framway', $GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['framway'], 'framway', false, false, '', '', $GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['framwayHelp']);
        $this->addCheckboxField('templates', $GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['templates'], 'templates', false, false, '', '', sprintf($GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['templatesHelp'], implode(',', $this->templatesDirs ?? [])));
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
        $this->reset((bool) Input::post('framway'), (bool) Input::post('templates'), (bool) Input::post('themes_modules'), (bool) Input::post('pages'), (bool) Input::post('files'), (bool) Input::post('localconfig'));
    }

    protected function backup(): void
    {
        /** @var CreateResult */
        $createResult = $this->backupManager->newFromConfigurationReset();
        $this->addConfirm(sprintf($GLOBALS['TL_LANG']['WEMSG']['RESET']['GENERAL']['backupCompleted'], $createResult->getBackup()->getFile()->basename));
    }

    protected function reset(bool $keepFramway, bool $keepTemplates, bool $keepThemesModules, bool $keepPages, bool $keepFiles, bool $keepLocalconfig): void
    {
        $this->resetter->reset($keepFramway, $keepTemplates, $keepThemesModules, $keepPages, $keepFiles, $keepLocalconfig);
        $this->addMessages($this->resetter->getMessages(), $this->module);
    }
}
