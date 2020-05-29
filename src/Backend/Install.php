<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2020 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

namespace WEM\SmartgearBundle\Backend;

use Exception;

/**
 * Back end module "smartgear".
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Install extends \BackendModule
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

    /**
     * Available modules.
     *
     * @var array
     */
    protected $modules = [
        'core' => ['core', 'templates']
        //,"component" => ["header"]
        , 'module' => ['blog', 'calendar', 'faq', 'forms', 'newsletter', 'locations', 'portfolio', 'planning'], 'addon' => ['formsubmissions', 'seo', 'conditionalnotifications'],
    ];

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
        if (\Input::post('TL_WEM_AJAX') && 'be_smartgear' === \Input::post('wem_module')) {
            try {
                // Check if we get all the params we need first
                if (!\Input::post('type') || !\Input::post('module') || !\Input::post('action')) {
                    throw new Exception('Missing one or several arguments : type/module/action');
                }

                $objModule = Util::findAndCreateObject(\Input::post('type'), \Input::post('module'));

                // Check if the method asked exists
                if (!method_exists($objModule, $strAction)) {
                    throw new Exception(sprintf('Unknown method %s in Class %s', $strAction, \get_class($objModule)));
                }

                // Just make sure we return a response in the asked format, if no format sent, we assume it's JSON.
                if ('html' === \Input::post('format')) {
                    echo $objModule->$strAction();
                    die;
                }

                // Launch the action and store the result
                $arrResponse = $objModule->$strAction();
                $arrResponse['logs'] = $objModule->logs;
            } catch (Exception $e) {
                $arrResponse = ['status' => 'error', 'msg' => $e->getMessage(), 'trace' => $e->getTrace()];
            }

            // Add Request Token to JSON answer and return
            $arrResponse['rt'] = \RequestToken::get();
            echo json_encode($arrResponse);
            die;
        }
        if (\Input::post('TL_WEM_AJAX') && 'be_smartgear_update' === \Input::post('wem_module')) {
            try {
                $objUpdater = new Updater();
                if ($objUpdater->runUpdate(\Input::post('action'))) {
                    $arrResponse = ['status' => 'success', 'msg' => 'La mise à jour '.\Input::post('action').' a été appliquée avec succès !'];
                } else {
                    throw new \Exception("Une erreur est survenue - et on est pas passé dans l'exception comme prévu");
                }
            } catch (Exception $e) {
                $arrResponse = ['status' => 'error', 'msg' => $e->getMessage(), 'trace' => $e->getTrace()];
            }

            // Add Request Token to JSON answer and return
            $arrResponse['rt'] = \RequestToken::get();
            echo json_encode($arrResponse);
            die;
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

        // Backup manager
        if ('backupmanager' === \Input::get('key')) {
            $this->Template = new \BackendTemplate('be_wem_sg_backupmanager');

            $objService = \System::getContainer()->get('smartgear.backend.backupservice');

            if ('new' === \Input::get('act')) {
                // Retrieve and list all the files to save
                $strDir = TL_ROOT.'/web/bundles/wemsmartgear/contao_files';
                $files = Util::getFileList($strDir);

                foreach ($files as &$f) {
                    $f = str_replace($strDir.'/', '', $f);
                }

                $objService->save($files);

                // Add Message
                \Message::addConfirmation('Backup effectué');

                // And redirect
                \Controller::redirect(str_replace('&act=new', '', \Environment::get('request')));
            } else if('download' == \Input::get('act')) {
                $objFile = new \File(\Input::get('file'));
                $objFile->sendToBrowser();
            }

            $arrBackups = $objService->list('files/backups');

            if (!$arrBackups) {
                $this->Template->empty = true;
            } else {
                $this->Template->empty = false;
                $this->Template->backups = $arrBackups;
            }

            // Back button
            $this->Template->backButtonHref = str_replace('&key=backupmanager', '', \Environment::get('request'));
            $this->Template->backButtonTitle = \StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']);
            $this->Template->backButtonButton = $GLOBALS['TL_LANG']['MSC']['backBT'];

            // New backup button
            $this->Template->newBackUpButtonHref = $this->addToUrl('&act=new');
            $this->Template->newBackUpButtonTitle = \StringUtil::specialchars($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['BACKUPMANAGER']['newBackUpBTTitle']);
            $this->Template->newBackUpButtonButton = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['BACKUPMANAGER']['newBackUpBT'];

            return;
        }

        // Catch Modal Calls
        if ('modal' === \Input::get('act')) {
            // Catch Errors
            if (!\Input::get('type')) {
                throw new Exception('Absence du paramètre type');
            }
            if (!\Input::get('module')) {
                throw new Exception('Absence du paramètre module');
            }
            if (!\Input::get('function')) {
                throw new Exception('Absence du paramètre function');
            }

            // Load the good block
            $objModule = Util::findAndCreateObject(\Input::get('type'), \Input::get('module'));
            $this->Template = $objModule->{\Input::get('function')}();

            return;
        }

        // Load the updater
        $objUpdater = new Updater();

        // If we catch an update to run, call it,
        // if return true, redirect to the Smartgear dashboard
        if (\Input::get('sgUpdate')) {
            if ($objUpdater->runUpdate(\Input::get('sgUpdate'))) {
                $this->redirect(str_replace('&sgUpdate='.\Input::get('sgUpdate'), '', \Environment::get('request')));
            }
        }

        // Fetch Smartgear updates
        if (false === $objUpdater->shouldBeUpdated()) {
            \Message::addConfirmation(sprintf('Smartgear v%s trouvé, installé et à jour !', $objUpdater->getCurrentVersion()));
        } else {
            $updates = [];
            if (!empty($objUpdater->updates)) {
                $updates[] = '<ul>';
                foreach ($objUpdater->updates as $strFunction) {
                    $updates[] = sprintf('<li data-update="%s">Update %s</li>', $strFunction, $strFunction);
                }
                $updates[] = '</ul>';
            }

            // @todo : Coder l'appel de la fonction trouvée, en AJAX ou pas.
            \Message::addRaw(
                sprintf(
                    '<div class="tl_info">Il y a une différence de version entre le Smartgear installé (%s) et le package trouvé (%s).%s%s</div>',
                    $objUpdater->getCurrentVersion() ?: 'NR',
                    $objUpdater->getPackageVersion() ?: 'NR',
                    implode('', $updates),
                    !empty($updates) ? '<br><button title="Appliquer toutes les updates" class="tl_submit sgUpdateAll">Appliquer</button>' : ''
                )
            );
        }

        // Backup manager button
        $this->Template->backupManagerBtnHref = $this->addToUrl('&key=backupmanager');
        $this->Template->backupManagerBtnTitle = \StringUtil::specialchars($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['BACKUPMANAGER']['backupManagerBTTitle']);
        $this->Template->backupManagerBtnButton = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['BACKUPMANAGER']['backupManagerBT'];

        // Back button
        $this->Template->backButtonHref = \Environment::get('request');
        $this->Template->backButtonTitle = \StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']);
        $this->Template->backButtonButton = $GLOBALS['TL_LANG']['MSC']['backBT'];

        // Parse Smartgear components
        foreach ($this->modules as $type => $blocks) {
            foreach ($blocks as $block) {
                $objModule = Util::findAndCreateObject($type, $block);
                $arrBlocks[$type][] = $objModule->parse();
            }
        }

        // Send msc data to template
        $this->Template->request = \Environment::get('request');
        $this->Template->token = \RequestToken::get();
        $this->Template->websiteTitle = \Config::get('websiteTitle');
        $this->Template->blocks = $arrBlocks;
    }
}
