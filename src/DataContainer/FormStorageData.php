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

namespace WEM\SmartgearBundle\DataContainer;

use Contao\FormFieldModel;
use WEM\SmartgearBundle\Model\FormStorage;

class FormStorageData
{
    public function listItems(array $row): string
    {
        $objFormStorage = FormStorage::findByPk($row['pid']);
        $objForm = $objFormStorage->getRelated('form');
        $objFormField = FormFieldModel::findById($row['field']);
        // todo, uncrypt personal data if needed
        return sprintf('<div><b>Field</b> : %s<br /><b>Value</b> : %s</div>', $objFormField->label, $row['value']);
    }
}
