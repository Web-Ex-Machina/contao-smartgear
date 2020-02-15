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

namespace WEM\SmartGear\Hooks;

/**
 * Class ParseTemplateHook
 *
 * Handle Smartgear getPageLayout hooks
 */
class ParseTemplateHook
{
    /**
     * Get default Smartgear template instead of default Contao
     * if there is no override found
     *
     * @param Template $objTemplate [Template to parse]
     *
     * @return Void
     */
    public function overrideDefaultTemplate($objTemplate): void
    {
        global $objPage;

        $file = $objTemplate->getName() . '.html5';
        $rootDir = \System::getContainer()->getParameter('kernel.project_dir');
        $sgDir = 'templates/smartgear';

        // If we have set up a memory, reset the system so each template will 
        // check in the real folder first
        if($objPage->templateGroupMemory) {
            $objPage->templateGroup = $objPage->templateGroupMemory;
        }

        // If the template does not exists in the objPage template group
        // BUT it exists in the templates/smartgear folder
        // We are updating the objPage template folder by the smartgear one
        if (!file_exists($rootDir . '/' . $objPage->templateGroup . '/' . $file)
            && file_exists($rootDir . '/' . $sgDir . '/' . $file)) {
            $objPage->templateGroupMemory = $objPage->templateGroup; // Trick to reset the system for the next iteration of the hook
            $objPage->templateGroup = $sgDir;
        }
    }
}
