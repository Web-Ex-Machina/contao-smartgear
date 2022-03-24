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

namespace WEM\SmartgearBundle\Backend;

use Contao\BackendTemplate;
use Contao\Config;
use Contao\Environment;
use Contao\Input;
use Contao\Message;
use Contao\RequestToken;
use Contao\System;
use Exception;
use WEM\SmartgearBundle\Backup\BackupManager;
use WEM\SmartgearBundle\Classes\Command\Util as CommandUtil;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Exceptions\Backup\ManagerException;
use WEM\SmartgearBundle\Exceptions\File\NotFound as FileNotFoundException;
use WEM\SmartgearBundle\Override\Controller;
use WEM\SmartgearBundle\Update\UpdateManager;
use WEM\UtilsBundle\Classes\StringUtil;

/**
 * Back end module "smartgear".
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Smartgear extends \Contao\BackendModule
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'be_wem_sg_install';

    /**
     * Logs.
     *
     * @var array
     */
    protected $arrLogs = [];

    /**
     * Module basepath.
     *
     * @var string
     */
    protected $strBasePath = 'bundles/wemsmartgear';

    protected $modules = ['module' => ['core']];
    /** @var BackupManager */
    protected $backupManager;
    /** @var UpdateManager */
    protected $updateManager;
    /** @var CommandUtil */
    protected $commandUtil;

    public function __construct($dc = null)
    {
        parent::__construct($dc);
        $this->backupManager = System::getContainer()->get('smartgear.backup.backup_manager');
        $this->updateManager = System::getContainer()->get('smartgear.update.update_manager');
        $this->coreConfigurationManager = System::getContainer()->get('smartgear.config.manager.core');
        $this->commandUtil = System::getContainer()->get('smartgear.classes.command.util');
        $this->objSession = System::getContainer()->get('session'); // Init session
    }

    /**
     * Process AJAX actions.
     *
     * @param [String] $strAction - Ajax action wanted
     *
     * @return string - Ajax response, as String or JSON
     */
    public function processAjaxRequest($strAction)
    {
        // Catch AJAX Requests
        if (Input::post('TL_WEM_AJAX') && 'be_smartgear' === Input::post('wem_module')) {
            try {
                switch (Input::post('action')) {
                    case 'executeCmd':
                        if (!Input::post('cmd')) {
                            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['AJAX']['COMMAND']['messageCmdNotSpecified']);
                        }

                        try {
                            $arrResponse['status'] = 'success';
                            $arrResponse['msg'] = sprintf($GLOBALS['TL_LANG']['WEMSG']['AJAX']['COMMAND']['messageSuccess'], Input::post('cmd'));
                            $arrResponse['output'] = $this->commandUtil->executeCmd(Input::post('cmd'));
                            // } catch (ProcessFailedException $e) {
                        } catch (Exception $e) {
                            throw $e;
                        }
                        break;
                    case 'executeCmdPhp':
                        if (!Input::post('cmd')) {
                            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['AJAX']['COMMAND']['messageCmdNotSpecified']);
                        }

                        try {
                            $arrResponse['status'] = 'success';
                            $arrResponse['msg'] = sprintf($GLOBALS['TL_LANG']['WEMSG']['AJAX']['COMMAND']['messageSuccess'], Input::post('cmd'));
                            $arrResponse['output'] = $this->commandUtil->executeCmdPHP(Input::post('cmd'));
                            // } catch (ProcessFailedException $e) {
                        } catch (Exception $e) {
                            throw $e;
                        }
                        break;
                    case 'executeCmdLive':
                        if (!Input::post('cmd')) {
                            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['AJAX']['COMMAND']['messageCmdNotSpecified']);
                        }

                        $arrResponse['status'] = 'success';
                        $arrResponse['msg'] = sprintf($GLOBALS['TL_LANG']['WEMSG']['AJAX']['COMMAND']['messageSuccess'], Input::post('cmd'));
                        $res = $this->commandUtil->executeCmdLive(Input::post('cmd'));
                        $arrResponse['output'] = $res;
                        // exit();
                    break;

                    default:
                        // Check if we get all the params we need first
                        if (!Input::post('type') || !Input::post('module') || !Input::post('action')) {
                            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['AJAX']['SUBBLOCK']['messageParameterMissing']);
                        }

                        $objBlock = System::getContainer()->get('smartgear.backend.module.'.Input::post('module').'.block');
                        if ('parse' === Input::post('action')) {
                            echo $objBlock->processAjaxRequest();
                            exit();
                        }
                        $arrResponse = $objBlock->processAjaxRequest();
                        $arrResponse['logs'] = $objBlock->getLogs();
                }
            } catch (Exception $e) {
                $arrResponse = ['status' => 'error', 'msg' => $e->getMessage(), 'trace' => $e->getTrace()];
            }

            // Add Request Token to JSON answer and return
            $arrResponse['rt'] = RequestToken::get();
            echo json_encode($arrResponse);
            exit;
        }
    }

    /**
     * Generate the module.
     *
     * @throws Exception
     */
    protected function compile(): void
    {
        // Add WEM styles to template
        $GLOBALS['TL_CSS'][] = $this->strBasePath.'/backend/wemsg.css';
        try {
            $coreConfig = $this->coreConfigurationManager->load();
        } catch (FileNotFoundException $e) {
            $coreConfig = $this->coreConfigurationManager->new();
            $save = $this->coreConfigurationManager->save($coreConfig);
        }

        if ('backupmanager' === Input::get('key')) {
            $this->getBackupManager();

            return;
        }

        if ('updatemanager' === Input::get('key')) {
            $this->getUpdateManager();

            return;
        }
        // Catch Modal Calls
        if ('modal' === Input::get('act')) {
            // Catch Errors
            if (!Input::get('type')) {
                throw new Exception($GLOBALS['TL_LANG']['WEMSG']['AJAX']['SUBBLOCK']['messageParameterTypeMissing']);
            }
            if (!Input::get('module')) {
                throw new Exception($GLOBALS['TL_LANG']['WEMSG']['AJAX']['SUBBLOCK']['messageParameterModuleMissing']);
            }
            if (!Input::get('function')) {
                throw new Exception($GLOBALS['TL_LANG']['WEMSG']['AJAX']['SUBBLOCK']['messageParameterFunctionMissing']);
            }

            // Load the good block
            $objModule = Util::findAndCreateObject(Input::get('type'), Input::get('module'));
            $this->Template = $objModule->{Input::get('function')}();

            return;
        }

        // If there is nothing setup, trigger Smartgear Install
        if (!$coreConfig->getSgInstallComplete()) {
            $coreBlock = System::getContainer()->get('smartgear.backend.module.core.block');
            $arrBlocks[$coreBlock->getType()][] = $coreBlock->parse();
        } else {
            // Retrieve number of updates to play if session key is undefined
            // @todo : find a way to update this value after an update by the Contao-Manager
            if ($this->objSession->get('wem_sg_update_to_play_number')) {
                $listResults = $this->updateManager->list();
                $this->Template->update_to_play_number = $listResults->getNumbersOfUpdatesToPlay();
                $this->objSession->set('wem_sg_update_to_play_number', $this->Template->update_to_play_number);
            }

            // Load buttons
            $this->getBackupManagerButton();
            $this->getUpdateManagerButton();

            // Parse Smartgear components
            foreach ($this->modules as $type => $blocks) {
                foreach ($blocks as $block) {
                    $objModule = $this->getContainer()->get('smartgear.backend.'.$type.'.'.$block.'.block');
                    $arrBlocks[$type][] = $objModule->parse();
                }
            }
        }
        // Send blocks to template
        $this->Template->blocks = $arrBlocks;

        // Send msc data to template
        $this->Template->request = Environment::get('request');
        $this->Template->token = RequestToken::get();
        $this->Template->websiteTitle = Config::get('websiteTitle');
        $this->Template->version = $this->coreConfigurationManager->load()->getSgVersion();
    }

    /**
     * Backup manager behaviour.
     */
    protected function getBackupManager(): void
    {
        $this->Template = new BackendTemplate('be_wem_sg_backupmanager');
        if ('new' === Input::get('act')) {
            try {
                set_time_limit(0);
                $start = microtime(true);
                $result = $this->backupManager->newFromUI();
                $end = microtime(true);

                $this->objSession->set('wem_sg_backup_create_result', $result);

                // Add Message
                Message::addConfirmation(sprintf($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['BACKUPMANAGER']['messageNewBackUpDone'], $result->getBackup()->getFile()->basename, ($end - $start)));
            } catch (ManagerException $e) {
                Message::addError($e->getMessage());
            }
            // And redirect
            Controller::redirect(str_replace('&act=new', '', Environment::get('request')));
        } elseif ('restore' === Input::get('act')) {
            try {
                set_time_limit(0);
                $start = microtime(true);
                $result = $this->backupManager->restore(Input::get('backup'));
                $end = microtime(true);

                $this->objSession->set('wem_sg_backup_restore_result', $result);

                // Add Message
                Message::addConfirmation(sprintf($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['BACKUPMANAGER']['messageRestoreBackUpDone'], $result->getBackup()->getFile()->basename, ($end - $start)));
            } catch (ManagerException $e) {
                Message::addError($e->getMessage());
            }
            // And redirect
            Controller::redirect(str_replace('&act=restore&backup='.Input::get('backup'), '', Environment::get('request')));
        } elseif ('delete' === Input::get('act')) {
            try {
                if ($this->backupManager->delete(Input::get('backup'))) {
                    // Add Message
                    Message::addConfirmation(sprintf($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['BACKUPMANAGER']['messageDeleteBackUpSuccess'], Input::get('backup')));
                } else {
                    // Add Message
                    Message::addError(sprintf($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['BACKUPMANAGER']['messageDeleteBackUpError'], Input::get('backup')));
                }
            } catch (ManagerException $e) {
                Message::addError(sprintf($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['BACKUPMANAGER']['messageDeleteBackUpError'], Input::get('backup')));
            }

            // And redirect
            Controller::redirect(str_replace('&act=delete&backup='.Input::get('backup'), '', Environment::get('request')));
        } elseif ('download' === Input::get('act')) {
            $objFile = $this->backupManager->get(Input::get('backup'));
            $objFile->sendToBrowser();
        }

        // Retrieve eventual logs
        if ($this->objSession->get('wem_sg_backup_restore_result')) {
            $this->Template->restore_result = $this->objSession->get('wem_sg_backup_restore_result');
            $this->objSession->set('wem_sg_backup_restore_result', '');
        }
        if ($this->objSession->get('wem_sg_backup_create_result')) {
            $this->Template->create_result = $this->objSession->get('wem_sg_backup_create_result');
            $this->objSession->set('wem_sg_backup_create_result', '');
        }

        // Retrieve backups
        $page = Input::get('page') ?? 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $listResults = $this->backupManager->list(
            $limit,
            $offset,
            Input::get('before'),
            Input::get('after'),
        );
        if (!$listResults) {
            $this->Template->empty = true;
        } else {
            $this->Template->empty = false;
            $this->Template->backups = $listResults;
        }

        $objPagination = new \Contao\Pagination($listResults->getTotal(), $listResults->getLimit());
        $this->Template->pagination = $objPagination->generate("\n  ");

        // Back button
        $this->getBackButton(str_replace('&key=backupmanager', '', Environment::get('request')));

        // New backup button
        $this->Template->newBackUpButtonHref = $this->addToUrl('&act=new');
        $this->Template->newBackUpButtonTitle = StringUtil::specialchars($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['BACKUPMANAGER']['newBackUpBTTitle']);
        $this->Template->newBackUpButtonButton = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['BACKUPMANAGER']['newBackUpBT'];
    }

    /**
     * Update manager behaviour.
     */
    protected function getUpdateManager(): void
    {
        $this->Template = new BackendTemplate('be_wem_sg_updatemanager');
        if ('play' === Input::get('act')) {
            try {
                set_time_limit(0);
                $result = $this->updateManager->update();
                $this->objSession->set('wem_sg_update_update_result', $result);

                // Add Message
                Message::addConfirmation($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['UPDATEMANAGER']['messagePlayUpdatesDone']);
            } catch (Exception $e) {
                Message::addError($e->getMessage());
            }

            // And redirect
            Controller::redirect(str_replace('&act=play', '', Environment::get('request')));
        }

        // Retrieve eventual logs
        if ($this->objSession->get('wem_sg_update_update_result')) {
            $this->Template->update_result = $this->objSession->get('wem_sg_update_update_result');
            $this->objSession->set('wem_sg_update_update_result', '');
        }

        // Retrieve updates
        $listResults = $this->updateManager->list();
        $this->objSession->set('wem_sg_update_to_play_number', $listResults->getNumbersOfUpdatesToPlay());
        if (!$listResults) {
            $this->Template->empty = true;
        } else {
            $this->Template->empty = false;
            $this->Template->updates = $listResults;
        }

        // Back button
        $this->getBackButton(str_replace('&key=updatemanager', '', Environment::get('request')));

        // play updates button
        $this->Template->playUpdatesButtonHref = $this->addToUrl('&act=play');
        $this->Template->playUpdatesButtonTitle = StringUtil::specialchars($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['UPDATEMANAGER']['playUpdatesBTTitle']);
        $this->Template->playUpdatesButtonButton = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['UPDATEMANAGER']['playUpdatesBT'];
    }

    protected function getBackButton($strHref = ''): void
    {
        // Back button
        $this->Template->backButtonHref = $strHref ?: Environment::get('request');
        $this->Template->backButtonTitle = StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']);
        $this->Template->backButtonButton = $GLOBALS['TL_LANG']['MSC']['backBT'];
    }

    protected function getBackupManagerButton(): void
    {
        // Backup manager button
        $this->Template->backupManagerBtnHref = $this->addToUrl('&key=backupmanager');
        $this->Template->backupManagerBtnTitle = StringUtil::specialchars($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['BACKUPMANAGER']['backupManagerBTTitle']);
        $this->Template->backupManagerBtnButton = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['BACKUPMANAGER']['backupManagerBT'];
    }

    protected function getUpdateManagerButton(): void
    {
        // Backup manager button
        $this->Template->updateManagerBtnHref = $this->addToUrl('&key=updatemanager');
        $this->Template->updateManagerBtnTitle = StringUtil::specialchars($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['UPDATEMANAGER']['updateManagerBTTitle']);
        $this->Template->updateManagerBtnButton = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['UPDATEMANAGER']['updateManagerBT'];
    }
}
