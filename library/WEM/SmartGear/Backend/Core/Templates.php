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

namespace WEM\SmartGear\Backend\Core;

use \Exception;

use WEM\SmartGear\Backend\Block;
use WEM\SmartGear\Backend\BlockInterface;
use WEM\SmartGear\Backend\Util;

/**
 * Back end module "smartgear".
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Templates extends Block implements BlockInterface
{
    /**
     * Module dependancies
     * @var Array
     */
    protected $require = ["core_core"];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->type = "core";
        $this->module = "templates";
        $this->icon = "exclamation-triangle";
        $this->title = "Smartgear | Core | Templates";

        $this->objFiles = \Files::getInstance();

        parent::__construct();
    }

    /**
     * Check Smartgear Status
     * @return [String] [Template of the module check status]
     */
    public function getStatus()
    {
        $this->messages[] = ['class' => 'tl_info', 'text' => 'Cette section permet d\'importer les templates Contao utilisés par Smartgear.'];
    
        // Check the install status
        if (1 === $this->sgConfig["sgInstallTemplates"] || !empty(scandir(TL_ROOT."/templates/smartgear"))) {
            $this->status = 1;

            // Compare the source folder and the existing one to check if an update should be done
            if ($this->shouldBeUpdated()) {
                $this->messages[] = ['class' => 'tl_new', 'text' => 'Il y a une différence entre les deux répertoires. Mettez à jour via le bouton ci-dessous'];
                $this->actions[] = ['action'=>'reset', 'label'=>'Mettre à jour les templates Contao'];
            } else {
                $this->messages[] = ['class' => 'tl_confirm', 'text' => 'Les templates Contao sont à jour.'];
                $this->actions[] = ['action'=>'reset', 'label'=>'Réinitialiser les templates Contao'];
            }

            $this->actions[] = ['action'=>'remove', 'label'=>'Supprimer les templates Contao'];
        } else {
            $this->actions[] = ['action'=>'install', 'label'=>'Importer les templates Contao'];
            $this->status = 0;
        }
    }

    /**
     * Install Contao templates
     */
    public function install()
    {
        try {
            $this->objFiles->rcopy("system/modules/wem-contao-smartgear/assets/templates_files/smartgear", "templates/smartgear");
            $this->logs[] = ["status"=>"tl_confirm", "msg"=>"Les templates Contao ont été importés."];

            // Update config
            Util::updateConfig(["sgInstallTemplates"=>1]);

            // And return an explicit status with some instructions
            return [
                "toastr" => [
                    "status"=>"success"
                    ,"msg"=>"L'installation des templates Contao a été effectuée avec succès."
                ]
                ,"callbacks" => [
                    0 => [
                        "method" => "refreshBlock"
                        ,"args"  => ["block-".$this->type."-".$this->module]
                    ]
                ]
            ];
        } catch (Exception $e) {
            $this->remove();
            throw $e;
        }
    }

    /**
     * Remove Contao templates
     */
    public function remove()
    {
        try {
            $this->objFiles->rrdir("templates/smartgear");
            $this->logs[] = ["status"=>"tl_confirm", "msg"=>"Les templates Contao ont été supprimés."];

            // Update config
            Util::updateConfig(["sgInstallTemplates"=>0]);

            // And return an explicit status with some instructions
            return [
                "toastr" => [
                    "status"=>"success"
                    ,"msg"=>"La désinstallation des templates Contao a été effectuée avec succès."
                ]
                ,"callbacks" => [
                    0 => [
                        "method" => "refreshBlock"
                        ,"args"  => ["block-".$this->type."-".$this->module]
                    ]
                ]
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Calculate the difference between Contao templates Source Folder and the installed one
     * @return [Boolean] [True means should be updated. Crazy uh ?]
     */
    protected function shouldBeUpdated()
    {
        try {
            clearstatcache();
            $arrSrcFolder = scandir(TL_ROOT."/system/modules/wem-contao-smartgear/assets/templates_files/smartgear");
            $arrFolder = scandir(TL_ROOT."/templates/smartgear");

            // Then, loop on the files contents and check if there is a diff between them
            $blnUpdate = false;
            foreach ($arrSrcFolder as $smartgear) {
                if ($smartgear == "." || $smartgear == "..") {
                    continue;
                }

                if (!file_exists(TL_ROOT."/templates/smartgear/".$smartgear)) {
                    $this->messages[] = ['class' => 'tl_new', 'text' => 'Fichier à importer : '.$smartgear];
                    $blnUpdate = true;
                } elseif (md5_file(TL_ROOT."/system/modules/wem-contao-smartgear/assets/templates_files/smartgear/".$smartgear) !== md5_file(TL_ROOT."/templates/smartgear/".$smartgear)) {
                    $this->messages[] = ['class' => 'tl_new', 'text' => 'Fichier à mettre à jour : '.$smartgear];
                    $blnUpdate = true;
                }
            }

            // Fallback, return false
            return $blnUpdate;
        } catch (Exception $e) {
            return true;
        }
    }
}
