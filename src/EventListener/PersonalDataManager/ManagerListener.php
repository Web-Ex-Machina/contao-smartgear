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

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\FilesModel;
use Contao\Validator;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\PersonalDataManagerBundle\Model\PersonalData;
use WEM\SmartgearBundle\Model\FormStorageData;

#[AsHook('getFileByPidAndPtableAndEmailAndField','getFileByPidAndPtableAndEmailAndField',-1)]
#[AsHook('isPersonalDataLinkedToFile','isPersonalDataLinkedToFile',-1)]
class ManagerListener
{
    public function __construct(protected TranslatorInterface $translator)
    {
    }

    public function getFileByPidAndPtableAndEmailAndField(int $pid, string $ptable, string $email, string $field, PersonalData $personalData, $value, ?FilesModel $objFileModel): ?FilesModel
    {
        if ($ptable === FormStorageData::getTable()) {
            $objFormStorageData = FormStorageData::findByPk($pid);
            if ($objFormStorageData && $objFormStorageData->field_type === 'upload' && Validator::isStringUuid($objFormStorageData->value)) {
                $objFileModel = FilesModel::findByUuid($objFormStorageData->value);
            }
        }

        return $objFileModel;
    }

    public function isPersonalDataLinkedToFile(PersonalData $personalData, bool $isLinkedToFile): bool
    {
        if ($personalData->ptable === FormStorageData::getTable()) {
            $objFormStorageData = FormStorageData::findByPk($personalData->pid);
            if ($objFormStorageData && $objFormStorageData->field_type === 'upload' && Validator::isStringUuid($objFormStorageData->value)) {
                $isLinkedToFile = true;
            }
        }

        return $isLinkedToFile;
    }
}
