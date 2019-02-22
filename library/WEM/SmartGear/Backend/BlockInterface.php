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

/**
 * Interface for Smartgear modules install tool
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
interface BlockInterface
{
    public function getStatus();
    public function install();
    public function reset();
    public function remove();
    public function parse();
}
