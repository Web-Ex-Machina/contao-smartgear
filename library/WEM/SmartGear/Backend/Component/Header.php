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

namespace WEM\SmartGear\Backend\Component;

use \Exception;

use WEM\SmartGear\Backend\Block;
use WEM\SmartGear\Backend\BlockInterface;
use WEM\SmartGear\Backend\Util;

/**
 * Back end module "smartgear".
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Header extends Block implements BlockInterface
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
        $this->type = "component";
        $this->module = "header";
        $this->icon = "object-group";
        $this->title = "SmartGear | Composant | Header";

        parent::__construct();
    }

    /**
     * Check Module Status
     * @return [String] [Template of the module check status]
     */
    public function getStatus()
    {
        if (!$this->sgConfig['sgComponentHeader'] || 0 === \ModuleModel::countById($this->sgConfig['sgComponentHeaderId'])) {
            $this->messages[] = ['class' => 'tl_info', 'text' => 'Le composant Header n\'est pas installé.'];
            $this->actions[] = ['action'=>'install', 'label'=>'Installer'];
            $this->status = 0;
        } else {
            $this->messages[] = ['class' => 'tl_confirm', 'text' => 'Le composant Header est installé et configuré.'];
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
        // Create the reader module
        $objReaderModule = new \ModuleModel();
        $objReaderModule->tstamp = time();
        $objReaderModule->pid = $this->sgConfig["sgInstallTheme"];
        $objReaderModule->name = "Événements - Reader";
        $objReaderModule->type = "eventreader";
        $objReaderModule->cal_calendar = serialize([0=>$objCalendar->id]);
        $objReaderModule->cal_template = 'event_full';
        $objReaderModule->imgSize = serialize([0=>1000,1=>"",2=>"proportional"]);
        $objReaderModule->save();
    }

    /**
     * Remove the module
     */
    public function remove()
    {
    }
}
