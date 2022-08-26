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
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Model\FormStorage;
use WEM\SmartgearBundle\Model\FormStorageData as FormStorageDataModel;

class FormStorageData
{
    /** @var TranslatorInterface */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function listItems(array $row): string
    {
        $objFormStorageData = FormStorageDataModel::findByPk($row['id']);
        $objFormStorage = FormStorage::findByPk($row['pid']);
        $objFormField = FormFieldModel::findById($row['field']);

        return sprintf('<div><b>%s</b> : %s<br /><b>%s</b> : %s</div>', $this->translator->trans('tl_sm_form_storage_data.field.0', [], 'contao_default'), $objFormField ? $objFormField->label : $objFormStorageData->field_label, $this->translator->trans('tl_sm_form_storage_data.value.0', [], 'contao_default'), $objFormStorageData->getValueAsString());
    }
}
