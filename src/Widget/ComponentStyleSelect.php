<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2023 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

namespace WEM\SmartgearBundle\Widget;

use WEM\SmartgearBundle\Classes\Utils\Configuration\ConfigurationUtil;

class ComponentStyleSelect extends \Oveleon\ContaoComponentStyleManager\ComponentStyleSelect
{
    public function generate(): string
    {
        $content = parent::generate();
        // normal translation keys
        $content = preg_replace_callback(
            '/>([A-Za-z0-9\_\-]+)\.([A-Za-z0-9\_\-]+)\.([A-Za-z0-9\_\-]+)\.([A-Za-z0-9\_\-]+)</',
            function ($match) {
                $translation = sprintf('>%s<', $GLOBALS['TL_LANG'][$match[1]][$match[2]][$match[3]][$match[4]]);

                return $translation ? $translation : implode('.', $match);
            },
            $content
        );
        // combined translation keys (with color)
        $content = preg_replace_callback(
            '/>([A-Za-z0-9\_\-]+)\.([A-Za-z0-9\_\-]+)\.([A-Za-z0-9\_\-]+)\.([A-Za-z0-9\_\-]+) \(([A-Za-z0-9\_\-]+)\.([A-Za-z0-9\_\-]+)\.([A-Za-z0-9\_\-]+)\.([A-Za-z0-9\_\-]+)\)</',
            function ($match) {
                $colorTranslation = $GLOBALS['TL_LANG'][$match[5]][$match[6]][$match[7]][$match[8]] ?? $match[8];

                return sprintf('>%s<', sprintf($GLOBALS['TL_LANG'][$match[1]][$match[2]][$match[3]][$match[4]], $colorTranslation));
            },
            $content
        );

        // normal translation keys for optgroup
        $content = preg_replace_callback(
            '/label\="([\s|&nbsp;]+)([A-Za-z0-9\_\-]+)\.([A-Za-z0-9\_\-]+)\.([A-Za-z0-9\_\-]+)\.([A-Za-z0-9\_\-]+)"/',
            function ($match) {
                $translation = $GLOBALS['TL_LANG'][$match[2]][$match[3]][$match[4]][$match[5]] ?? $match[5];

                return sprintf('label="%s%s"', $match[1], $GLOBALS['TL_LANG'][$match[2]][$match[3]][$match[4]][$match[5]]);
            },
            $content
        );

        $content = preg_replace_callback(
            '|</h3><select([^>]*)>(.*)</select>|U',
            function ($match): string {
                // $match[0] => full match

                // look in the options if we have something color related ?
                if (preg_match('|<option value="(.*)-primary"|', $match[2])
                || preg_match('|<option value="(.*)-red"|', $match[2])
                ) {
                    $objConfiguration = ConfigurationUtil::findConfigurationForItem($this->arrConfiguration['strTable'], (int) $this->arrConfiguration['currentRecord']);
                    $helpA = '<a href="contao/help.php?table='.$this->arrConfiguration['strTable'].'&amp;field='.$this->arrConfiguration['strField'].'&amp;id='.$this->arrConfiguration['currentRecord'].'&amp;framway_path='.($objConfiguration ? $objConfiguration->framway_path : 'assets/framway').'" title="'.\Contao\StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['helpWizard']).'" onclick="Backend.openModalIframe({\'title\':\''.\Contao\StringUtil::specialchars($GLOBALS['TL_LANG']['WEMSG']['FRAMWAY']['COLORS']['helpWizardTitle']).'\',\'url\':this.href});return false">'.\Contao\Image::getHtml('about.svg', $GLOBALS['TL_LANG']['MSC']['helpWizard']).'</a>';

                    return $helpA.'</h3><select'.$match[1].'>'.$match[2].'</select>';
                }

                return '</h3><select'.$match[1].'>'.$match[2].'</select>';
            },
            $content
        );

        $content .= '';

        return $content;
    }
}
