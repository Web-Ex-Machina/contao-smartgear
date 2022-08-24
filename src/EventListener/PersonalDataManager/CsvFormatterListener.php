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

namespace WEM\SmartgearBundle\EventListener\PersonalDataManager;

use Contao\FrontendTemplate;
use Contao\MemberGroupModel;
use Contao\Model;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\PersonalDataManagerBundle\Model\PersonalData;
use WEM\SmartgearBundle\Model\FormStorageData;
use WEM\PersonalDataManagerBundle\Model\PersonalData as PersonalDataModel;
use WEM\SmartgearBundle\Model\FormStorage;

class CsvFormatterListener
{
    /** @var TranslatorInterface */
    protected $translator;

    public function __construct(
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
    }

    public function formatSingle(PersonalDataModel $personalData, array $header, array $row): array
    {
        switch ($personalData->ptable) {
            case FormStorageData::getTable():
                $objFormStorageData = FormStorageData::findByPk($personalData->pid);
                $row = [
                    FormStorage::getTable(),
                    $personalData->email,
                    $objFormStorageData->field_label,
                    $personalData->anonymized ? $personalData->value : $objFormStorageData->getValueAsString(),
                    $personalData->anonymized ? $this->translator->trans('WEM.PEDAMA.CSV.columnAnonymizedValueYes', [], 'contao_default') : $this->translator->trans('WEM.PEDAMA.CSV.columnAnonymizedValueNo', [], 'contao_default'),
                ];
                return $row;
            break;
        }

        return $row;
    }
}
