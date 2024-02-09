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

use Contao\FormModel;
use WEM\SmartgearBundle\Classes\StringUtil;

class FormUtil
{
    /**
     * Shortcut for Form creation.
     */
    public static function createForm(string $title, ?int $jumpTo = null, ?array $arrData = []): FormModel
    {
        // Create the Form
        $objForm = isset($arrData['id']) ? FormModel::findById($arrData['id']) ?? new FormModel() : new FormModel();
        $objForm->tstamp = time();
        $objForm->title = $title;
        $objForm->alias = StringUtil::generateAlias($title);
        $objForm->jumpTo = $jumpTo;

        // Now we get the default values, get the arrData table
        if (!empty($arrData)) {
            foreach ($arrData as $k => $v) {
                $objForm->$k = $v;
            }
        }

        $objForm->save();

        // Return the model
        return $objForm;
    }

    public static function createFormFormContact(string $title, ?int $jumpTo = null, ?int $ncNotificationId = null, ?array $arrData = []): FormModel
    {
        return self::createForm($title, $jumpTo, array_merge([
            'nc_notification' => $ncNotificationId,
        ], $arrData));
    }
}
