<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2021 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

namespace WEM\SmartgearBundle\Backend;

use Exception;
use Symfony\Component\Process\Exception\ProcessFailedException;
use WEM\SmartgearBundle\Classes\Config\Manager as CoreConfigurationManager;
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

        // If there is nothing setup, trigger Smartgear Install
        if (!$coreConfig->getSgInstallComplete()) {
            // load the core block which will take care of his installation itself²
            $this->getActiveStep();
            $this->Template->steps = $this->parseInstallSteps();

            $blocks['install'][$this->strActiveStep] = $this->getInstallBlock();
            $this->Template->blocks = $blocks;
        } else {
            // Load the updater
            $this->getUpdater();

            // Load buttons
            $this->getBackupManagerButton();

            // Parse Smartgear components
            foreach ($this->modules as $type => $blocks) {
                foreach ($blocks as $block) {
                    $objModule = Util::findAndCreateObject($type, $block);
                    $arrBlocks[$type][] = $objModule->parse();
                }
            }

            // Send blocks to template
            $this->Template->blocks = $arrBlocks;
        }

        // Send msc data to template
        $this->Template->request = \Environment::get('request');
        $this->Template->token = \RequestToken::get();
        $this->Template->websiteTitle = \Config::get('websiteTitle');
    }
}
