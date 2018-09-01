<?php

/**
 * SMARTGEAR for Contao Open Source CMS
 *
 * Copyright (c) 2015-2018 Web ex Machina
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
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