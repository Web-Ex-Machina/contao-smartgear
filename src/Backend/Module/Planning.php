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
class Planning extends Block implements BlockInterface
{
    /**
     * Module dependancies
     * @var Array
     */
    protected $require = ["core_core", "core_rsce"];
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->type = "module";
        $this->module = "planning";
        $this->icon = "cogs";
        $this->title = "SmartGear | Module | Planning";

        parent::__construct();
    }

    /**
     * Check Module Status
     * @return [String] [Template of the module check status]
     */
    public function getStatus()
    {
        if (!isset($this->bundles['wem-contao-planning'])) {
            $this->messages[] = ['class' => 'tl_error', 'text' => sprintf($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['BACKEND']['missingPackage'], "Planning")];

            $this->status = 0;
        } elseif (!$this->sgConfig['sgPlanning']) {
            $this->messages[] = ['class' => 'tl_info', 'text' => sprintf($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['BACKEND']['notInstalledPackage'], "Planning")];
            $this->actions[] = ['action'=>'install', 'label'=>'Installer'];

            $this->status = 0;
        } else {
            $this->messages[] = ['class' => 'tl_confirm', 'text' => sprintf($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['BACKEND']['installedPackage'], "Planning")];
            $href = 'contao?do=wem_plannings&act=edit&id='.$this->sgConfig["sgPlanningId"].'&rt='.\RequestToken::get();
            $this->actions[] = ['v'=>2, 'tag'=>'a', 'text'=>'Configurer', 'attrs'=>['href'=>$href, 'title'=>'Configurer', 'class'=>'openContaoModal']];
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
        // Create the planning
        $objPlanning = new \WEM\Planning\Model\Planning();
        $objPlanning->tstamp = time();
        $objPlanning->title = "Planning";
        $objPlanning->alias = "planning";
        $objPlanning->save();

        // Create the module
        $objModule = new \ModuleModel();
        $objModule->tstamp = time();
        $objModule->pid = $this->sgConfig["sgInstallTheme"];
        $objModule->name = "Planning";
        $objModule->type = "wem_planning";
        $objModule->wem_planning = $objPlanning->id;
        $objModule->save();

        // Create the list page
        $intPage = Util::createPageWithModules("Planning", [$objModule->id]);
        
        // And save stuff in config
        Util::updateConfig([
            "sgPlanning"=>1
            ,"sgPlanningId"=>$objPlanning->id
            ,"sgPlanningModule"=>$objModule->id
            ,"sgPlanningPage"=>$intPage
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
        if ($objPlanning = \WEM\Planning\Model\Planning::findByPk($this->sgConfig["sgPlanningId"])) {
            $objPlanning->delete();
        }
        if ($objModule = \ModuleModel::findByPk($this->sgConfig["sgPlanningModule"])) {
            $objModule->delete();
        }
        if ($objPage = \PageModel::findByPk($this->sgConfig["sgPlanningPage"])) {
            $objPage->delete();
        }

        Util::updateConfig([
            "sgPlanning"=>''
            ,"sgPlanningId"=>''
            ,"sgPlanningModule"=>''
            ,"sgPlanningPage"=>''
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
