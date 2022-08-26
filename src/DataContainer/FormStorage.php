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

use Contao\BackendTemplate;
use Contao\Config;
use Contao\DataContainer;
use Contao\Date;
use Contao\FormModel;
use Contao\System;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Model\FormStorageData;

class FormStorage
{
    /** @var TranslatorInterface */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function listItems(array $row): array
    {
        $objForm = FormModel::findById($row['pid']);
        $objFormStorageDataEmail = FormStorageData::findItems(['pid' => $row['id'], 'field_label' => 'Email'], 1);

        return [
            $objForm ? $objForm->title : $row['pid'],
            Date::parse(Config::get('datimFormat'), (int) $row['tstamp']),
            $this->translator->trans(sprintf('tl_sm_form_storage.status.%s', $row['status']), [], 'contao_default'),
            $objFormStorageDataEmail ? $objFormStorageDataEmail->value : 'NR',
        ];
    }

    public function showData(DataContainer $dc, string $extendedLabel): string
    {
        System::loadLanguageFile('tl_sm_form_storage_data');
        $formStorageDatas = FormStorageData::findItems(['pid' => $dc->id]);
        $arrFormStorageDatas = [];
        $objTemplate = new BackendTemplate('be_wem_sg_widget_fdm_form_storage_data');

        if ($formStorageDatas) {
            while ($formStorageDatas->next()) {
                $arrFormStorageDatas[$formStorageDatas->id] = $formStorageDatas->current()->row();
                $arrFormStorageDatas[$formStorageDatas->id]['value'] = $formStorageDatas->current()->getValueAsString();
            }
        }
        $objTemplate->arrFormStorageDatas = $arrFormStorageDatas;

        return $objTemplate->parse();
    }

    public function onShowCallback(array $modalData, array $recordData, DataContainer $dc): array
    {
        $formStorageDatas = FormStorageData::findItems(['pid' => $dc->id]);
        if ($formStorageDatas) {
            $modalData[FormStorageData::getTable()] = [0 => []];
            while ($formStorageDatas->next()) {
                $modalData[FormStorageData::getTable()][0][$formStorageDatas->field_label] = $formStorageDatas->current()->getValueAsString();
            }
        }

        return $modalData;
    }
}
