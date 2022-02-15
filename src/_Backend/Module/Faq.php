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

namespace WEM\SmartgearBundle\Backend\Module;

use \Exception;

use WEM\SmartgearBundle\Backend\Block;
use WEM\SmartgearBundle\Backend\BlockInterface;
use WEM\SmartgearBundle\Backend\Util;

/**
 * Back end module "smartgear".
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Faq extends Block implements BlockInterface
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
        $this->type = "module";
        $this->module = "faq";
        $this->icon = "cogs";
        $this->title = "SmartGear | Module | FAQ";

        parent::__construct();
    }

    /**
     * Check Module Status
     * @return [String] [Template of the module check status]
     */
    public function getStatus()
    {
        if (!isset($this->bundles['ContaoFaqBundle'])) {
            $this->messages[] = ['class' => 'tl_error', 'text' => 'Le module FAQ n\'est pas installé. Veuillez utiliser le <a href="{{env::/}}/contao-manager.phar.php" title="Contao Manager" target="_blank">Contao Manager</a> pour cela.'];
            $this->status = 0;
        } elseif (!$this->sgConfig['sgFAQInstall'] || 0 === \FaqCategoryModel::countById($this->sgConfig['sgFAQ'])) {
            $this->messages[] = ['class' => 'tl_info', 'text' => 'Le module FAQ est installé, mais pas configuré.'];
            $this->actions[] = ['action'=>'install', 'label'=>'Installer'];
            $this->status = 0;
        } else {
            $this->messages[] = ['class' => 'tl_confirm', 'text' => 'Le module FAQ est installé et configuré.'];
            $this->actions[] = ['action'=>'reset', 'label'=>'Réinitialiser'];
            $this->actions[] = ['action'=>'remove', 'label'=>'Supprimer'];
            $this->status = 1;
        }
    }

    /**
     * Setup the module
     */
    public function install()
    {
        // Make sure the template is here before doing anything
        if (!file_exists("templates/smartgear/mod_faqpage.html5")) {
            $objFiles = \Files::getInstance();
            $objFiles->copy("web/bundles/wemsmartgear/templates_files/mod_faqpage.html5", "templates/smartgear/mod_faqpage.html5");
        }

        // Create the archive
        $objFAQ = new \FaqCategoryModel();
        $objFAQ->tstamp = time();
        $objFAQ->title = "FAQ";
        $objFAQ->headline = "FAQ";
        $objFAQ->save();

        // Create the reader module
        $objModule = new \ModuleModel();
        $objModule->tstamp = time();
        $objModule->pid = $this->sgConfig["sgInstallTheme"];
        $objModule->name = "FAQ";
        $objModule->type = "faqpage";
        $objModule->faq_categories = serialize([0=>$objFAQ->id]);
        $objModule->customTpl = 'mod_faqpage';
        $objModule->save();

        // Create the page
        $intPage = Util::createPageWithModules("FAQ", [$objModule->id]);
        
        // And save stuff in config
        Util::updateConfig([
            "sgFAQInstall"=>1
            ,"sgFAQ"=>$objFAQ->id
            ,"sgFAQModule"=>$objModule->id
            ,"sgFAQPage"=>$intPage
        ]);

        // And return an explicit status with some instructions
        return [
            "toastr" => [
                "status"=>"success"
                ,"msg"=>"La configuration du module a été effectuée avec succès."
            ]
            ,"callbacks" => [
                0 => [
                    "method" => "refreshBlock"
                    ,"args"  => ["block-".$this->type."-".$this->module]
                ]
            ]
        ];
    }

    /**
     * Remove the module
     */
    public function remove()
    {
        if ($objFAQ = \FaqCategoryModel::findByPk($this->sgConfig["sgFAQ"])) {
            $objFAQ->delete();
        }
        if ($objModule = \ModuleModel::findByPk($this->sgConfig["sgFAQModule"])) {
            $objModule->delete();
        }
        if ($objPage = \PageModel::findByPk($this->sgConfig["sgFAQPage"])) {
            $objPage->delete();
        }

        Util::updateConfig([
            "sgFAQInstall"=>''
            ,"sgFAQ"=>''
            ,"sgFAQModule"=>''
            ,"sgFAQPage"=>''
        ]);

        // And return an explicit status with some instructions
        return [
            "toastr" => [
                "status"=>"success"
                ,"msg"=>"La suppression du module a été effectuée avec succès."
            ]
            ,"callbacks" => [
                0 => [
                    "method" => "refreshBlock"
                    ,"args"  => ["block-".$this->type."-".$this->module]
                ]
            ]
        ];
    }
}
