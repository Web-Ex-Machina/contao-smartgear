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

namespace WEM\SmartgearBundle\Backend;

use Contao\BackendModule;
use Contao\BackendTemplate;
use Contao\DataContainer;
use Contao\Environment;
use Contao\Input;
use Contao\Message;
use Contao\System;
use Exception;
use WEM\SmartgearBundle\Classes\StringUtil;
use WEM\SmartgearBundle\Override\Controller;
use WEM\SmartgearBundle\Update\UpdateManager;

/**
 * Back end module "smartgear".
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Update extends BackendModule
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'be_wem_sg_updatemanager';

    /**
     * Logs.
     */
    protected array $arrLogs = [];

    protected mixed $objSession;

    /**
     * Module basepath.
     */
    protected string $strBasePath = 'bundles/wemsmartgear';

    // protected $modules = ['module' => ['extranet', 'form_data_manager'], 'component' => ['core', 'blog', 'events', 'faq', 'form_contact']];
    protected array $modules = ['module' => [], 'component' => []];

    protected null|UpdateManager $updateManager;

    public function __construct(DataContainer|null $dc = null)
    {
        parent::__construct($dc);
        $this->updateManager = System::getContainer()->get('smartgear.update.update_manager');
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

        $this->Template = new BackendTemplate('be_wem_sg_updatemanager');
        if ('play' === Input::get('act')) {
            try {
                set_time_limit(0);
                $result = $this->updateManager->update((bool) Input::get('backup'));
                $this->objSession->set('wem_sg_update_update_result', $result);

                // Add Message
                Message::addConfirmation($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['UPDATEMANAGER']['messagePlayUpdatesDone']);
            } catch (Exception $e) {
                Message::addError($e->getMessage());
            }

            // And redirect
            Controller::redirect(str_replace(['&act=play', '&backup=1', '&backup=0'], '', Environment::get('request')));
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
        // $this->getBackButton(str_replace('&key=updatemanager', '', Environment::get('request')));

        // play updates button
        $this->Template->playUpdatesWithoutBackupButtonHref = $this->addToUrl('&act=play&backup=0');
        $this->Template->playUpdatesWithoutBackupButtonTitle = StringUtil::specialchars($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['UPDATEMANAGER']['playUpdatesWithoutBackupBTTitle']);
        $this->Template->playUpdatesWithoutBackupButtonButton = sprintf($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['UPDATEMANAGER']['playUpdatesWithoutBackupBT'], \Contao\Image::getHtml('important.svg'));
        $this->Template->playUpdatesWithBackupButtonHref = $this->addToUrl('&act=play&backup=1');
        $this->Template->playUpdatesWithBackupButtonTitle = StringUtil::specialchars($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['UPDATEMANAGER']['playUpdatesWithBackupBTTitle']);
        $this->Template->playUpdatesWithBackupButtonButton = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['UPDATEMANAGER']['playUpdatesWithBackupBT'];
    }

    protected function getUpdateManagerButton(): void
    {
        $this->Template->updateManagerBtnHref = $this->addToUrl('&key=updatemanager');
        $this->Template->updateManagerBtnTitle = StringUtil::specialchars($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['UPDATEMANAGER']['updateManagerBTTitle']);
        $this->Template->updateManagerBtnButton = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['UPDATEMANAGER']['updateManagerBT'];
    }
}
