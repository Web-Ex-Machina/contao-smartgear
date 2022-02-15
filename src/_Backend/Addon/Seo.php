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

namespace WEM\SmartgearBundle\Backend\Addon;

use \Exception;

use WEM\SmartgearBundle\Backend\Block;
use WEM\SmartgearBundle\Backend\BlockInterface;
use WEM\SmartgearBundle\Backend\Util;

/**
 * Back end module "smartgear".
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Seo extends Block implements BlockInterface
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
        $this->type = "addon";
        $this->module = "seo";
        $this->icon = "puzzle-piece";
        $this->title = "SmartGear | Extension | SEO";

        parent::__construct();
    }

    /**
     * Check Module Status
     * @return [String] [Template of the module check status]
     */
    public function getStatus()
    {
        if (!isset($this->bundles['wem-contao-seo'])) {
            $this->messages[] = ['class' => 'tl_error', 'text' => 'L\'extension n\'est pas installé. Veuillez utiliser le <a href="{{env::/}}/contao-manager.phar.php" title="Contao Manager" target="_blank">Contao Manager</a> pour cela.'];

            $this->status = 0;
        } else {
            $this->messages[] = ['class' => 'tl_confirm', 'text' => 'L\'extension est installé et configuré.'];
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
