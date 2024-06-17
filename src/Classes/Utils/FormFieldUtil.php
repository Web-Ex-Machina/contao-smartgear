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

use Contao\FormFieldModel;

class FormFieldUtil
{
    /**
     * Shortcut for FormField creation.
     */
    public static function createFormField(int $pid, ?array $arrData = []): FormFieldModel
    {
        // Create the FormField
        $objFormField = isset($arrData['id']) ? FormFieldModel::findById($arrData['id']) ?? new FormFieldModel() : new FormFieldModel();
        $objFormField->tstamp = time();
        $objFormField->pid = $pid;

        // Now we get the default values, get the arrData table
        if ($arrData !== null && $arrData !== []) {
            foreach ($arrData as $k => $v) {
                $objFormField->$k = $v;
            }
        }

        $objFormField->save();

        // Return the model
        return $objFormField;
    }
}
