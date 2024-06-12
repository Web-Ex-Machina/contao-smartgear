<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2024 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

namespace WEM\SmartgearBundle\Backend;

use Contao\Environment;
use Contao\Input;
use Contao\Message;
use Contao\System;
use Exception;
use WEM\SmartgearBundle\Backup\BackupManager;
use WEM\SmartgearBundle\Classes\StringUtil;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Exceptions\Backup\ManagerException;
use WEM\SmartgearBundle\Override\Controller;

/**
 * Back end module "smartgear".
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Backup extends \Contao\BackendModule
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'be_wem_sg_backupmanager';

    /**
     * Module basepath.
     *
     * @var string
     */
    protected $strBasePath = 'bundles/wemsmartgear';

    /** @var BackupManager */
    protected $backupManager;

    public function __construct($dc = null)
    {
        parent::__construct($dc);
        $this->backupManager = System::getContainer()->get('smartgear.backup.backup_manager');
        $this->objSession = System::getContainer()->get('session'); // Init session
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

        $memoryLimitInBytes = Util::formatPhpMemoryLimitToBytes(ini_get('memory_limit'));
        if ($memoryLimitInBytes < 0) {
            Message::addInfo(sprintf($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['BACKUPMANAGER']['messageChunkSizeNoLimitDefined'], Util::humanReadableFilesize($this->backupManager->getChunkSizeInBytes(), 0)));
        } else {
            Message::addInfo(sprintf($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['BACKUPMANAGER']['messageChunkSize'], Util::humanReadableFilesize($this->backupManager->getChunkSizeInBytes(), 0)));
        }

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
            } catch (ManagerException) {
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
        // $this->getBackButton(str_replace('&key=backupmanager', '', Environment::get('request')));

        // New backup button
        $this->Template->newBackUpButtonHref = $this->addToUrl('&act=new');
        $this->Template->newBackUpButtonTitle = StringUtil::specialchars($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['BACKUPMANAGER']['newBackUpBTTitle']);
        $this->Template->newBackUpButtonButton = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['BACKUPMANAGER']['newBackUpBT'];
    }

    protected function getBackupManagerButton(): void
    {
        $this->Template->backupManagerBtnHref = $this->addToUrl('&key=backupmanager');
        $this->Template->backupManagerBtnTitle = StringUtil::specialchars($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['BACKUPMANAGER']['backupManagerBTTitle']);
        $this->Template->backupManagerBtnButton = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['BACKUPMANAGER']['backupManagerBT'];
    }
}
