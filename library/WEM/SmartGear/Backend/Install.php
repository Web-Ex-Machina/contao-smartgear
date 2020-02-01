<?php

/**
 * SMARTGEAR for Contao Open Source CMS
 *
 * Copyright (c) 2015-2019 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

namespace WEM\SmartGear\Backend;

use Exception;

use WEM\SmartGear\Backend\Module as ModulePath;

/**
 * Back end module "smartgear".
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Install extends \BackendModule
{

    /**
     * Template
     *
     * @var string
     */
    protected $strTemplate = 'be_wem_sg_install';

    /**
     * Logs
     *
     * @var array
     */
    protected $arrLogs = array();

    /**
     * Module basepath
     *
     * @var string
     */
    protected $strBasePath = 'system/modules/wem-contao-smartgear';

    /**
     * Available modules
     *
     * @var array
     */
    protected $modules = [
        "core" => ["core", "templates", "rsce", "tinymce"]
        //,"component" => ["header"]
        ,"module" => ["blog", "calendar", "faq", "forms", "newsletter", "locations", "portfolio", "planning"]
        ,"addon" => ["formsubmissions", "seo", "conditionalnotifications"]
    ];

    /**
     * Generate the module
     *
     * @return void
     *
     * @throws Exception
     */
    protected function compile()
    {
        // Add WEM styles to template
        $GLOBALS['TL_CSS'][] = $this->strBasePath.'/assets/backend/wemsg.css';
        
        // Catch Modal Calls
        if ("modal" == \Input::get('act')) {
            // Catch Errors
            if (!\Input::get('type')) {
                throw new Exception("Absence du paramètre type");
            }
            if (!\Input::get('module')) {
                throw new Exception("Absence du paramètre module");
            }
            if (!\Input::get('function')) {
                throw new Exception("Absence du paramètre function");
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
                $this->redirect(str_replace("&sgUpdate=".\Input::get('sgUpdate'), "", \Environment::get('request')));
            }
        }

        // Fetch Smartgear updates
        if (false === $objUpdater->shouldBeUpdated()) {
            \Message::addConfirmation(sprintf("Smartgear v%s trouvé, installé et à jour !", $objUpdater->getCurrentVersion()));
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
                    $objUpdater->getCurrentVersion() ?: "NR",
                    $objUpdater->getPackageVersion() ?: "NR",
                    implode('', $updates),
                    !empty($updates) ? '<br><button title="Appliquer toutes les updates" class="tl_submit sgUpdateAll">Appliquer</button>' : ''
                )
            );
        }

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
        $this->Template->websiteTitle = \Config::get("websiteTitle");
        $this->Template->blocks = $arrBlocks;
    }

    /**
     * Process AJAX actions
     *
     * @param [String] $strAction - Ajax action wanted
     *
     * @return String - Ajax response, as String or JSON
     */
    public function processAjaxRequest($strAction)
    {
        // Catch AJAX Requests
        if (\Input::post('TL_WEM_AJAX') && 'be_smartgear' == \Input::post('wem_module')) {
            try {
                // Check if we get all the params we need first
                if (!\Input::post('type') || !\Input::post('module') || !\Input::post('action')) {
                    throw new Exception("Missing one or several arguments : type/module/action");
                }
                
                $objModule = Util::findAndCreateObject(\Input::post('type'), \Input::post('module'));

                // Check if the method asked exists
                if (!method_exists($objModule, $strAction)) {
                    throw new Exception(sprintf("Unknown method %s in Class %s", $strAction, get_class($objModule)));
                }

                // Just make sure we return a response in the asked format, if no format sent, we assume it's JSON.
                if ("html" == \Input::post('format')) {
                    echo $objModule->$strAction();
                    die;
                }

                // Launch the action and store the result
                $arrResponse = $objModule->$strAction();
                $arrResponse["logs"] = $objModule->logs;
            } catch (Exception $e) {
                $arrResponse = ["status"=>"error", "msg"=>$e->getMessage(), "trace"=>$e->getTrace()];
            }

            // Add Request Token to JSON answer and return
            $arrResponse["rt"] = \RequestToken::get();
            echo json_encode($arrResponse);
            die;
        } else if(\Input::post('TL_WEM_AJAX') && 'be_smartgear_update' == \Input::post('wem_module')) {
            try {
                $objUpdater = new Updater();
                if($objUpdater->runUpdate(\Input::post('action'))) {
                    $arrResponse = ['status'=>"success", "msg" => "La mise à jour ".\Input::post('action')." a été appliquée avec succès !"];
                } else {
                    throw new \Exception("Une erreur est survenue - et on est pas passé dans l'exception comme prévu");
                }
            } catch (Exception $e) {
                $arrResponse = ["status"=>"error", "msg"=>$e->getMessage(), "trace"=>$e->getTrace()];
            }

            // Add Request Token to JSON answer and return
            $arrResponse["rt"] = \RequestToken::get();
            echo json_encode($arrResponse);
            die;
        }
    }
}
