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

class ComponentStyleSelect extends \Oveleon\ContaoComponentStyleManager\ComponentStyleSelect
{
    public function generate(): string
    {
        $content = parent::generate();
        // normal translation keys
        $content = preg_replace_callback(
            '/>([A-Za-z0-9\_\-]+)\.([A-Za-z0-9\_\-]+)\.([A-Za-z0-9\_\-]+)\.([A-Za-z0-9\_\-]+)</',
            function ($match) {
                return sprintf('>%s<', $GLOBALS['TL_LANG'][$match[1]][$match[2]][$match[3]][$match[4]]);
            },
            $content
        );
        // combined translation keys (with color)
        $content = preg_replace_callback(
            '/>([A-Za-z0-9\_\-]+)\.([A-Za-z0-9\_\-]+)\.([A-Za-z0-9\_\-]+)\.([A-Za-z0-9\_\-]+) \(([A-Za-z0-9\_\-]+)\.([A-Za-z0-9\_\-]+)\.([A-Za-z0-9\_\-]+)\.([A-Za-z0-9\_\-]+)\)</',
            function ($match) {
                return sprintf('>%s<', sprintf($GLOBALS['TL_LANG'][$match[1]][$match[2]][$match[3]][$match[4]], $GLOBALS['TL_LANG'][$match[5]][$match[6]][$match[7]][$match[8]]));
            },
            $content
        );
        dump($this);
        $content = preg_replace_callback(
            '</label>',
            function ($match) {
                return 'a href="contao/help.php?table='.$this->arrConfiguration['strTable'].'&amp;field='.$this->arrConfiguration['strField'].'" title="'.\Contao\StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['helpWizard']).'" onclick="Backend.openModalIframe({\'title\':\'xxxxxx\',\'url\':this.href});return false">'.\Contao\Image::getHtml('about.svg', $GLOBALS['TL_LANG']['MSC']['helpWizard']).'</a></label';
            },
            $content
        );

        $content .= '';

        return $content;
    }
}
