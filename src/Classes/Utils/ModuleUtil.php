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

namespace WEM\SmartgearBundle\Classes\Utils;

use Contao\ModuleModel;

class ModuleUtil
{
    /**
     * Shortcut for article creation.
     */
    public static function createModule(int $pid, $arrData = []): ModuleModel
    {
        // Create the article
        $objModule = isset($arrData['id']) ? ModuleModel::findById($arrData['id']) ?? new ModuleModel() : new ModuleModel();
        $objModule->tstamp = time();
        $objModule->pid = $pid;

        // Now we get the default values, get the arrData table
        if (!empty($arrData)) {
            foreach ($arrData as $k => $v) {
                $objModule->$k = $v;
            }
        }

        $objModule->save();

        // Return the model
        return $objModule;
    }
}
