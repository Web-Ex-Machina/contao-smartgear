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

namespace WEM\SmartGear\Backend\Module;

use \Exception;

use WEM\SmartGear\Backend\Block;
use WEM\SmartGear\Backend\BlockInterface;
use WEM\SmartGear\Backend\Util;

/**
 * Back end module "smartgear".
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Portfolio extends Block implements BlockInterface
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
        $this->module = "portfolio";
        $this->icon = "cogs";
        $this->title = "SmartGear | Module | Portfolio";

        parent::__construct();
    }

    /**
     * Check Module Status
     * @return [String] [Template of the module check status]
     */
    public function getStatus()
    {
        if (!isset($this->bundles['wem-contao-portfolio'])) {
            $this->messages[] = ['class' => 'tl_error', 'text' => 'Le module Portfolio n\'est pas installé. Veuillez utiliser le <a href="{{env::/}}/contao-manager.phar.php" title="Contao Manager" target="_blank">Contao Manager</a> pour cela.'];

            $this->status = 0;
        } elseif (!$this->sgConfig['sgPortfolio']) {
            $this->messages[] = ['class' => 'tl_info', 'text' => 'Le module est installé, mais pas configuré.'];
            $this->actions[] = ['action'=>'install', 'label'=>'Installer'];

            $this->status = 0;
        } else {
            $this->messages[] = ['class' => 'tl_confirm', 'text' => 'Le module est installé et configuré.'];
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
        // Create the module
        $objListModule = new \ModuleModel();
        $objListModule->tstamp = time();
        $objListModule->pid = $this->sgConfig["sgInstallTheme"];
        $objListModule->name = "Portfolio - Liste";
        $objListModule->type = "wem_portfolio_list";
        $objListModule->save();

        // Create the module
        $objReaderModule = new \ModuleModel();
        $objReaderModule->tstamp = time();
        $objReaderModule->pid = $this->sgConfig["sgInstallTheme"];
        $objReaderModule->name = "Portfolio - Lecteur";
        $objReaderModule->type = "wem_portfolio_reader";
        $objReaderModule->save();

        // Create the list page
        $intPage = Util::createPageWithModules("Portfolio", [$objListModule->id, $objReaderModule->id]);
        
        // And save stuff in config
        Util::updateConfig([
            "sgPortfolio"=>1
            ,"sgPortfolioModuleList"=>$objListModule->id
            ,"sgPortfolioModuleReader"=>$objReaderModule->id
            ,"sgPortfolioPage"=>$intPage
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
        if ($objModule = \ModuleModel::findByPk($this->sgConfig["sgPortfolioModuleList"])) {
            $objModule->delete();
        }
        if ($objModule = \ModuleModel::findByPk($this->sgConfig["sgPortfolioModuleReader"])) {
            $objModule->delete();
        }
        if ($objPage = \PageModel::findByPk($this->sgConfig["sgPortfolioPage"])) {
            $objPage->delete();
        }

        Util::updateConfig([
            "sgPortfolio"=>''
            ,"sgPortfolioModuleList"=>''
            ,"sgPortfolioModuleReader"=>''
            ,"sgPortfolioPage"=>''
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
