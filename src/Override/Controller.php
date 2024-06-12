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

namespace WEM\SmartgearBundle\Override;

use WEM\SmartgearBundle\Classes\Util;

/**
 * Override Controller Contao Class.
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Controller extends \Contao\Controller
{
    /**
     * Return all template files of a particular group as array.
     *
     * @param string $strPrefix           The template name prefix (e.g. "ce_")
     * @param array  $arrAdditionalMapper An additional mapper array
     * @param string $strDefaultTemplate  An optional default template
     *
     * @return array An array of template names
     */
    public static function getTemplateGroup($strPrefix, array $arrAdditionalMapper = [], $strDefaultTemplate = '')
    {
        Util::log('getTemplateGroup');
        $arrTemplates = parent::getTemplateGroup($strPrefix, $arrAdditionalMapper, $strDefaultTemplate);

        $strGlobPrefix = $strPrefix;

        // Backwards compatibility (see #725)
        if (str_ends_with($strGlobPrefix, '_')) {
            $strGlobPrefix = substr($strGlobPrefix, 0, -1).'[_-]';
        }
        $projectDir = \System::getContainer()->getParameter('kernel.project_dir');

        $arrSGTemplates = parent::braceGlob($projectDir.'/templates/smartgear/'.$strGlobPrefix.'*.html5');
        $arrNewTemplates = [];
        if (!empty($arrSGTemplates) && \is_array($arrSGTemplates)) {
            foreach ($arrSGTemplates as $strFile) {
                $strTemplate = basename($strFile, strrchr($strFile, '.'));
                $arrNewTemplates[$strTemplate][] = 'Dossier Smartgear';
            }
        }

        // Show the template sources (see #6875)
        foreach ($arrNewTemplates as $k => $v) {
            $v = array_filter($v, static fn($a) => 'root' !== $a);

            if (empty($v)) {
                $arrNewTemplates[$k] = $k;
            } else {
                $arrNewTemplates[$k] = $k.' ('.implode(', ', $v).')';
            }
        }

        // Merge
        $arrTemplates = array_merge($arrTemplates, $arrNewTemplates);

        // Sort the template names
        ksort($arrTemplates);

        return $arrTemplates;
    }
}
