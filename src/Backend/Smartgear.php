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

use Contao\Config;
use Contao\Environment;
use Contao\Input;
use Contao\RequestToken;
use Contao\System;
use Exception;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Exceptions\File\NotFound as FileNotFoundException;

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
                            throw new Exception('Missing one arguments : cmd');
                        }

                        try {
                            $arrResponse['status'] = 'success';
                            $arrResponse['msg'] = sprintf('La commande %s a été executée avec succès', Input::post('cmd'));
                            $arrResponse['output'] = Util::executeCmd(Input::post('cmd'));
                            // } catch (ProcessFailedException $e) {
                        } catch (Exception $e) {
                            throw $e;
                        }
                        break;
                    case 'executeCmdPhp':
                        if (!Input::post('cmd')) {
                            throw new Exception('Missing one arguments : cmd');
                        }

                        try {
                            $arrResponse['status'] = 'success';
                            $arrResponse['msg'] = sprintf('La commande %s a été executée avec succès', Input::post('cmd'));
                            $arrResponse['output'] = Util::executeCmdPHP(Input::post('cmd'));
                            // } catch (ProcessFailedException $e) {
                        } catch (Exception $e) {
                            throw $e;
                        }
                        break;
                    case 'executeCmdLive':
                        if (!Input::post('cmd')) {
                            throw new Exception('Missing one arguments : cmd');
                        }

                        $arrResponse['status'] = 'success';
                        $arrResponse['msg'] = sprintf('La commande %s a été executée avec succès', Input::post('cmd'));
                        $res = Util::executeCmdLive(Input::post('cmd'));
                        $arrResponse['output'] = $res;
                        // exit();
                    break;

                    // case 'getSteps':
                    //     $this->getActiveStep();

                    //     echo $this->parseInstallSteps();
                    //     exit;
                    //     break;

                    // case 'setStep':
                    //     if (!\in_array(Input::post('step'), $this->modules['install'], true)) {
                    //         throw new \Exception('Step inconnue : '.Input::post('step'));
                    //     }

                    //     $objSession = \System::getContainer()->get('session');
                    //     $objSession->set('sg_install_step', Input::post('step'));
                    //     break;

                    // case 'getNextStep':
                    //     $arrNextStep = $this->getNextInstallStep($this->strActiveStep);

                    //     $arrResponse['status'] = 'success';
                    //     $arrResponse['step'] = $arrNextStep;

                    //     $objSession = \System::getContainer()->get('session');
                    //     $objSession->set('sg_install_step', $arrNextStep['name']);
                    //     $this->strActiveStep = $arrNextStep['name'];

                    //     break;

                    default:
                        // Check if we get all the params we need first
                        if (!Input::post('type') || !Input::post('module') || !Input::post('action')) {
                            throw new Exception('Missing one or several arguments : type/module/action');
                        }

                        $objBlock = System::getContainer()->get('smartgear.backend.module.'.Input::post('module').'.block');
                        if ('parse' === Input::post('action')) {
                            echo $objBlock->processAjaxRequest();
                            exit();
                        }
                        $arrResponse = $objBlock->processAjaxRequest();
                        $arrResponse['logs'] = $objBlock->getLogs();

                        // $objModule = Util::findAndCreateObject(Input::post('type'), Input::post('module'));

                        // // Check if the method asked exists
                        // if (!method_exists($objModule, $strAction)) {
                        //     throw new Exception(sprintf('Unknown method %s in Class %s', $strAction, \get_class($objModule)));
                        // }

                        // // Just make sure we return a response in the asked format, if no format sent, we assume it's JSON.
                        // if ('html' === Input::post('format')) {
                        //     echo $objModule->$strAction();
                        //     exit;
                        // }

                        // // Launch the action and store the result
                        // $arrResponse = $objModule->$strAction();
                        // $arrResponse['logs'] = $objModule->logs;
                }
            } catch (Exception $e) {
                $arrResponse = ['status' => 'error', 'msg' => $e->getMessage(), 'trace' => $e->getTrace()];
            }

            // Add Request Token to JSON answer and return
            $arrResponse['rt'] = \RequestToken::get();
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
        $coreConfifManager = $this->getContainer()->get('smartgear.classes.config.manager.core');
        try {
            $coreConfig = $coreConfifManager->load();
        } catch (FileNotFoundException $e) {
            $coreConfig = $coreConfifManager->new();
        }

        // Catch Modal Calls
        if ('modal' === Input::get('act')) {
            // Catch Errors
            if (!Input::get('type')) {
                throw new Exception('Absence du paramètre type');
            }
            if (!Input::get('module')) {
                throw new Exception('Absence du paramètre module');
            }
            if (!Input::get('function')) {
                throw new Exception('Absence du paramètre function');
            }

            // Load the good block
            $objModule = Util::findAndCreateObject(Input::get('type'), Input::get('module'));
            $this->Template = $objModule->{Input::get('function')}();

            return;
        }

        // If there is nothing setup, trigger Smartgear Install
        if (!$coreConfig->getSgInstallComplete()) {
            // load the core block which will take care of his installation itself
            // $this->getActiveStep();
            // $this->Template->steps = $this->parseInstallSteps();

            // $blocks['install'][$this->strActiveStep] = $this->getInstallBlock();
            // $this->Template->blocks = $blocks;
            $coreBlock = System::getContainer()->get('smartgear.backend.module.core.block');
            // $this->Template = $coreBlock->parse();
            $arrBlocks[$coreBlock->getType()][] = $coreBlock->parse();
        } else {
            // // Load the updater
            // $this->getUpdater();

            // // Load buttons
            // $this->getBackupManagerButton();

            // Parse Smartgear components
            foreach ($this->modules as $type => $blocks) {
                foreach ($blocks as $block) {
                    $objModule = $this->getContainer()->get('smartgear.backend.'.$type.'.'.$block.'.block'); //Util::findAndCreateObject($type, $block);
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
    }
}
