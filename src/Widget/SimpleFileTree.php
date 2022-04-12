<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2022 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

namespace WEM\SmartgearBundle\Widget;

class SimpleFileTree extends \Contao\FileTree
{
    public function generate(): string
    {
        $str = parent::generate();

        $str = preg_replace('/\.post\({(.*)}\);/', '', $str);

        $str = preg_replace('/"callback": function\(table, value\) {([\S\s]*)}([\S\s]*)}\);([\S\s]*)}\);/', '"callback": function(table, value) {document.getElementById("ctrl_'.$this->strId.'").value = value;document.getElementById("ctrl_'.$this->strId.'_result").innerHTML = value;} }); });', $str);

        return preg_replace('/<\/a>([\S\s]*)<\/p>/', '</a><span id="ctrl_'.$this->strId.'_result">'.$this->varValue.'</span></p>', $str);
    }
}
