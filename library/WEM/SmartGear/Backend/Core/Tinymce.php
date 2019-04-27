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
class Tinymce extends Block implements BlockInterface
{
    /**
     * Module dependancies
     * @var Array
     */
    protected $require = ["core_core"];

    /**
     * TinyMCE Plugins
     * @var Array [Contains plugins as key and the path as value]
     */
    protected $tinymce_plugins = [];

    /**
     * TinyMCE location
     * @var String [Basepath where TinyMCE is stored in the Contao installation]
     */
    protected $tinymce_basepath = "assets/tinymce4/js";

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->type = "core";
        $this->module = "tinymce";
        $this->icon = "exclamation-triangle";
        $this->title = "Smartgear | Core | TinyMCE";

        $this->objFiles = \Files::getInstance();

        // Get the available plugins and paths
        $plugins = glob("system/modules/wem-contao-smartgear/assets/tinymce/plugins/*", GLOB_ONLYDIR);
        if (!empty($plugins)) {
            foreach ($plugins as $path) {
                $parts = explode('/', $path);
                $this->tinymce_plugins[$parts[sizeof($parts)-1]] = $path;
            }
        }

        parent::__construct();
    }

    /**
     * Check TinyMCE Status
     * @return [String] [Template of the module check status]
     */
    public function getStatus()
    {
        $this->messages[] = ['class' => 'tl_info', 'text' => 'Cette section permet d\'importer les plugins et la configuration TinyMCE utilisés par Smartgear.'];
        
        if (!empty($this->tinymce_plugins)) {
            $this->messages[] = ['class' => 'tl_info', 'text' => sprintf('Plugins disponibles : %s', implode(', ', array_keys($this->tinymce_plugins)))];
        }
        
        // Check the install status
        if (1 === $this->sgConfig["sgTinyMCEConfig"]) {
            $this->status = 1;

            // Compare the source folder and the existing one to check if an update should be done
            if ($this->shouldBeUpdated()) {
                $this->messages[] = ['class' => 'tl_new', 'text' => 'Il y a une différence entre les répertoires. Mettez à jour via le bouton ci-dessous'];
                $this->actions[] = ['action'=>'reset', 'label'=>'Mettre à jour les fichiers TinyMCE'];
            } else {
                $this->messages[] = ['class' => 'tl_confirm', 'text' => 'Les éléments TinyMCE sont à jour.'];
                $this->actions[] = ['action'=>'reset', 'label'=>'Réinitialiser les fichiers TinyMCE'];
            }

            $this->actions[] = ['action'=>'remove', 'label'=>'Supprimer les fichiers TinyMCE'];
        } else {
            $this->actions[] = ['action'=>'install', 'label'=>'Importer les fichiers TinyMCE'];
            $this->status = 0;
        }
    }

    /**
     * Install TinyMCE
     */
    public function install()
    {
        try {
            $this->objFiles->copy("system/modules/wem-contao-smartgear/assets/tinymce/be_tinyMCE.html5", "templates/be_tinyMCE.html5");
            $this->logs[] = ["status"=>"tl_confirm", "msg"=>"La configuration TinyMCE a été importée."];

            // Update config
            Util::updateConfig(["sgTinyMCEConfig"=>1]);

            // Store every plugin who've been copied
            if (!empty($this->tinymce_plugins)) {
                $arrPlugins = [];
                foreach ($this->tinymce_plugins as $k => $v) {
                    $this->objFiles->rcopy($v, $this->tinymce_basepath."/plugins/".$k);
                    $arrPlugins[$k] = $v;
                }

                // Update config
                Util::updateConfig(["sgTinyMCEPlugins"=>$arrPlugins]);
            }

            // And return an explicit status with some instructions
            return [
                "toastr" => $this->callback("toastr", ["success", "La configuration TinyMCE a été importée avec succès."])
                ,"callbacks" => [$this->callback("refreshBlock")]
            ];
        } catch (Exception $e) {
            $this->remove();
            throw $e;
        }
    }

    /**
     * Remove TinyMCE
     */
    public function remove()
    {
        try {
            if (file_exists("templates/be_tinyMCE.html5")) {
                $this->objFiles->delete("templates/be_tinyMCE.html5");
            }
            $this->logs[] = ["status"=>"tl_confirm", "msg"=>"La configuration TinyMCE a été supprimée."];

            // Update config
            Util::updateConfig(["sgTinyMCEConfig"=>0]);

            // Remove every plugin who've been copied and stored in the current config
            if (!empty($this->tinymce_plugins)) {
                foreach ($this->tinymce_plugins as $k => $v) {
                    if (file_exists($this->tinymce_basepath."/plugins/".$plugin)) {
                        $this->objFiles->rrdir($this->tinymce_basepath."/plugins/".$k);
                    }
                }

                Util::updateConfig(["sgTinyMCEPlugins"=>""]);
            }

            // And return an explicit status with some instructions
            return [
                "toastr" => $this->callback("toastr", ["success", "La désinstallation de la configuration TinyMCE a été effectuée avec succès."])
                ,"callbacks" => [$this->callback("refreshBlock")]
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Calculate the difference between RSCE Source Folder and Smartgear installed one
     * @return [Boolean] [True means should be updated. Crazy uh ?]
     */
    protected function shouldBeUpdated()
    {
        try {
            clearstatcache();

            // 1st check if there is a file be_tinyMCE.html5
            if (!file_exists(TL_ROOT."/templates/be_tinyMCE.html5")) {
                return true;
            }

            // 2nd, if be_tinyMCE exists, we will compare the size of the two files, if the file has been updated, it is most likely their sizes will be different
            if (filesize(TL_ROOT."/system/modules/wem-contao-smartgear/assets/tinymce/be_tinyMCE.html5") !== filesize(TL_ROOT."/templates/be_tinyMCE.html5")) {
                return true;
            }

            // 3rd, check if there is differences between installed plugins in the config and in reality
            if (!empty($this->tinymce_plugins)) {
                foreach ($this->tinymce_plugins as $plugin => $path) {
                    if (!file_exists(TL_ROOT."/".$this->tinymce_basepath."/plugins/".$plugin) && !is_dir(TL_ROOT."/".$this->tinymce_basepath."/plugins/".$plugin)) {
                        return true;
                    }
                }
            }
            
            // Else, return false
            return false;
        } catch (Exception $e) {
            return true;
        }
    }
}
