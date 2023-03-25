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

namespace WEM\SmartgearBundle\Classes;

use Contao\System;

class CustomLanguageFileLoader
{
    /**
     * Load custom language file.
     */
    public function loadCustomLanguageFile(?string $currentLanguage = null): void
    {
        // check if assets/smartgear/languages/{lang}/custom.json exists
        // if so, include it
        if (!$currentLanguage) {
            $container = System::getContainer();
            $currentLanguage = $container->get('request_stack')->getCurrentRequest()->getLocale();
        }

        $filePath = System::getContainer()->getParameter('kernel.project_dir').'/assets/smartgear/languages/'.$currentLanguage.'/custom.json';
        if (file_exists($filePath)) {
            $content = file_get_contents($filePath);
            if (!$content) {
                return;
            }
            $json = json_decode($content, true);
            if (!$json) {
                return;
            }
            $this->JSONFileToLangArray($json);
        }
    }

    protected function JSONFileToLangArray(array $json): void
    {
        foreach ($json as $key => $value) {
            // check if key is 4 chunks long max
            $keys = explode('.', $key);
            switch (\count($keys)) {
                case 1:
                    $GLOBALS['TL_LANG'][$keys[0]] = $value;
                break;
                case 2:
                    $GLOBALS['TL_LANG'][$keys[0]][$keys[1]] = $value;
                break;
                case 3:
                    $GLOBALS['TL_LANG'][$keys[0]][$keys[1]][$keys[2]] = $value;
                break;
                case 4:
                    $GLOBALS['TL_LANG'][$keys[0]][$keys[1]][$keys[2]][$keys[3]] = $value;
                break;
                default:
                break;
            }
        }
    }
}
