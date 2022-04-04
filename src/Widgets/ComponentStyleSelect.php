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

namespace WEM\SmartgearBundle\Widgets;

class ComponentStyleSelect extends \Oveleon\ContaoComponentStyleManager\ComponentStyleSelect
{
    public function generate()
    {
        $content = parent::generate();

        // normal translation keys, selected
        $content = preg_replace_callback(
            '/<option value="([^\"]*)"([\s]*)(selected)>([A-Za-z0-9\_\-]+).([A-Za-z0-9\_\-]+).([A-Za-z0-9\_\-]+).([A-Za-z0-9\_\-]+)<\/option>/',
            function ($match) {
                return sprintf('<option value="%s" selected>%s</option>', $match[1], $GLOBALS['TL_LANG'][$match[4]][$match[5]][$match[6]][$match[7]]);
            },
            $content
        );
        // normal translation keys, not selected
        $content = preg_replace_callback(
            '/<option value="([^\"]*)"?>([A-Za-z0-9\_\-]+).([A-Za-z0-9\_\-]+).([A-Za-z0-9\_\-]+).([A-Za-z0-9\_\-]+)<\/option>/',
            function ($match) {
                return sprintf('<option value="%s" selected>%s</option>', $match[1], $GLOBALS['TL_LANG'][$match[2]][$match[3]][$match[4]][$match[5]]);
            },
            $content
        );

        // combined translation keys (with color), selected
        $content = preg_replace_callback(
            '/<option value="([^\"]*)"([\s]*)(selected)>([A-Za-z0-9\_\-]+).([A-Za-z0-9\_\-]+).([A-Za-z0-9\_\-]+).([A-Za-z0-9\_\-]+) \(([A-Za-z0-9\_\-]+).([A-Za-z0-9\_\-]+).([A-Za-z0-9\_\-]+).([A-Za-z0-9\_\-]+)\)<\/option>/',
            function ($match) {
                return sprintf('<option value="%s" selected>%s</option>', $match[1], sprintf($GLOBALS['TL_LANG'][$match[4]][$match[5]][$match[6]][$match[7]], $GLOBALS['TL_LANG'][$match[8]][$match[9]][$match[10]][$match[11]]));
            },
            $content
        );
        // combined translation keys (with color), not selected
        return preg_replace_callback(
            '/<option value="([^\"]*)"?>([A-Za-z0-9\_\-]+).([A-Za-z0-9\_\-]+).([A-Za-z0-9\_\-]+).([A-Za-z0-9\_\-]+) \(([A-Za-z0-9\_\-]+).([A-Za-z0-9\_\-]+).([A-Za-z0-9\_\-]+).([A-Za-z0-9\_\-]+)\)<\/option>/',
            function ($match) {
                return sprintf('<option value="%s" selected>%s</option>', $match[1], sprintf($GLOBALS['TL_LANG'][$match[2]][$match[3]][$match[4]][$match[5]], $GLOBALS['TL_LANG'][$match[6]][$match[7]][$match[8]][$match[9]]));
            },
            $content
        );
    }
}
