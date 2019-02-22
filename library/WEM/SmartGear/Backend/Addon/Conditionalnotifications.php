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

namespace WEM\SmartGear\Backend\Addon;

use \Exception;

use WEM\SmartGear\Backend\Block;
use WEM\SmartGear\Backend\BlockInterface;
use WEM\SmartGear\Backend\Util;

/**
 * Back end module "smartgear".
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Conditionalnotifications extends Block implements BlockInterface
{
    /**
     * Module dependancies
     * @var Array
     */
    protected $require = ["core_core", "module_forms"];
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->type = "addon";
        $this->module = "conditionalnotifications";
        $this->icon = "puzzle-piece";
        $this->title = "SmartGear | Extension | Notifications de formulaire conditionnelles";

        parent::__construct();
    }

    /**
     * Check Module Status
     * @return [String] [Template of the module check status]
     */
    public function getStatus()
    {
        if (!isset($this->bundles['wem-contao-form-conditional-notifications'])) {
            $this->messages[] = ['class' => 'tl_error', 'text' => sprintf($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['BACKEND']['missingPackage'], "contao-form-conditional-notifications")];

            $this->status = 0;
        } else {
            $this->messages[] = ['class' => 'tl_confirm', 'text' => sprintf($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['BACKEND']['installedPackage'], "contao-form-conditional-notifications")];
            $this->status = 1;
        }
    }

    /**
     * Setup the module
     */
    public function install()
    {
    }

    /**
     * Remove the module
     */
    public function remove()
    {
    }
}
