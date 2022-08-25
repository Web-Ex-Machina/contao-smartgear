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

use Contao\Config;
use Contao\Date;
use Contao\MemberGroupModel;
use Contao\Model;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\PersonalDataManagerBundle\Model\PersonalData;
use WEM\PersonalDataManagerBundle\Service\PersonalDataManagerUi;
use WEM\SmartgearBundle\Classes\StringUtil;
use WEM\SmartgearBundle\Model\Form;
use WEM\SmartgearBundle\Model\FormStorage;
use WEM\SmartgearBundle\Model\FormStorageData;

class UiListener
{
    /** @var TranslatorInterface */
    protected $translator;
    /** @var personalDataManagerUi */
    protected $personalDataManagerUi;

    public function __construct(
        TranslatorInterface $translator,
        personalDataManagerUi $personalDataManagerUi
    ) {
        $this->translator = $translator;
        $this->personalDataManagerUi = $personalDataManagerUi;
    }

    public function renderSingleItem(int $pid, string $ptable, string $email, array $personalDatas, Model $originalModel, string $buffer): string
    {
        switch ($ptable) {
            case FormStorageData::getTable():
                // I would need to rebuild everything from here ...
                $objFormStorageData = FormStorageData::findOneBy('id', $pid);
                $objFormStorage = $objFormStorageData->getRelated('pid');

                $objForm = $objFormStorage->getRelated('pid');
                $arrPersonalDatas = $this->getPersonalDataForFormStorage($objFormStorage);

                return $this->personalDataManagerUi->formatSingleItem((int) $arrPersonalDatas[0]->id, FormStorage::getTable(), $email, $arrPersonalDatas[1], $arrPersonalDatas[0]);
            break;
        }

        return $buffer;
    }

    public function renderSingleItemTitle(int $pid, string $ptable, string $email, array $personalDatas, Model $originalModel, string $buffer): string
    {
        switch ($ptable) {
            case 'tl_member':
                $buffer = 'Member';
            break;
            case FormStorageData::getTable():
                $objFormStorageData = FormStorageData::findOneBy('id', $pid);
                $objFormStorage = $objFormStorageData->getRelated('pid');
                $objForm = $objFormStorage->getRelated('pid');
                $buffer = sprintf('%s %s', 'Formulaire', $objForm->title);
            break;
            case FormStorage::getTable():
                $objFormStorage = FormStorage::findOneBy('id', $pid);
                $objForm = $objFormStorage->getRelated('pid');
                $buffer = sprintf('%s %s', 'Formulaire', $objForm->title);
            break;
        }

        return $buffer;
    }

    public function renderSingleItemBodyOriginalModel(int $pid, string $ptable, string $email, array $personalDatas, Model $originalModel, string $buffer): string
    {
        switch ($ptable) {
            case FormStorageData::getTable():
                // make the original model a form storage with all needed fields
                $objFormStorageData = FormStorageData::findById($pid);
                $objFormStorage = FormStorage::findById($objFormStorageData->pid);
                $arrPersonalDatas = $this->getPersonalDataForFormStorage($objFormStorageData);

                $buffer = $this->personalDataManagerUi->formatSingleItemBodyOriginalModel($pid, $ptable, $email, $arrPersonalDatas[1], $arrPersonalDatas[0]);
            break;
        }

        return $buffer;
    }

    public function renderSingleItemBodyOriginalModelSingle(int $pid, string $ptable, string $email, string $field, $value, array $personalDatas, Model $originalModel, string $buffer): string
    {
        switch ($ptable) {
            case 'tl_member':
                switch ($field) {
                    case 'id':
                    case 'tstamp':
                    case 'password':
                    case 'dateAdded':
                    case 'lastLogin':
                    case 'loginAttempts':
                    case 'locked':
                    case 'session':
                    case 'secret':
                    case 'backupCodes':
                    case 'trustedTokenVersion':
                    case 'currentLogin':
                        $buffer = '';
                    break;
                }
            break;
            case FormStorage::getTable():
                switch ($field) {
                    case 'id':
                    case 'tstamp':
                    case 'status':
                    case 'token':
                    case 'completion_percentage':
                    case 'delay_to_submission':
                    case 'delay_to_first_interaction':
                    case 'note':
                        $buffer = '';
                    break;
                }
            break;
            case FormStorageData::getTable():
                switch ($field) {
                    case 'id':
                    case 'field':
                    case 'contains_personal_data':
                    case 'field_name':
                    case 'field_label':
                    case 'value':
                    case 'tstamp':
                        $buffer = '';
                    break;
                }
            break;
        }

        return $buffer;
    }

    public function renderSingleItemBodyOriginalModelSingleFieldValue(int $pid, string $ptable, string $email, string $field, $value, array $personalDatas, Model $originalModel, string $buffer): string
    {
        if (empty($buffer)) {
            return sprintf('<i>%s</i>', $this->translator->trans('WEM.SMARTGEAR.DEFAULT.NotFilled', [], 'contao_default'));
        }

        switch ($ptable) {
            case 'tl_member':
                switch ($field) {
                    case 'login':
                        $buffer = sprintf('<input type="checkbox" readonly %s />', true === (bool) $value ? 'checked' : '');
                    break;
                    case 'groups':
                        $groupIds = unserialize($value);
                        $buffer = '<ul>';
                        foreach ($groupIds as $groupId) {
                            $objGroup = MemberGroupModel::findById($groupId);
                            $buffer .= sprintf('<li>- %s</li>', null !== $objGroup ? $objGroup->name : $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementUnknown', [], 'contao_default'));
                        }
                        $buffer .= '<ul>';
                    break;
                }
            break;
            case FormStorage::getTable():
                switch ($field) {
                    case 'pid':
                        $objFormStorage = FormStorage::findOneBy('id', $pid);
                        $objForm = $objFormStorage->getRelated('pid');
                        $buffer = $objForm->title;
                    break;
                    case 'createdAt':
                        $buffer = Date::parse(Config::get('datimFormat'), (int) $value);
                    break;
                }
            break;
            case FormStorageData::getTable():
                switch ($field) {
                    case 'pid':
                        $objFormStorageData = FormStorageData::findOneBy('id', $pid);
                        $objFormStorage = $objFormStorageData->getRelated('pid');
                        $objForm = $objFormStorage->getRelated('pid');
                        $buffer = $objForm->title;
                    break;
                    case 'createdAt':
                        $buffer = Date::parse(Config::get('datimFormat'), (int) $value);
                    break;
                }
            break;
        }

        return $buffer;
    }

    public function renderSingleItemBodyPersonalData(int $pid, string $ptable, string $email, array $personalDatas, Model $originalModel, string $buffer): string
    {
        switch ($ptable) {
            case FormStorageData::getTable():
                // make the original model a form storage with all needed fields
                $objFormStorageData = FormStorageData::findById($pid);
                $objFormStorage = FormStorage::findById($objFormStorageData->pid);
                $arrPersonalDatas = $this->getPersonalDataForFormStorage($objFormStorageData);

                $buffer = $this->personalDataManagerUi->formatSingleItemBodyPersonalData($pid, $ptable, $email, $arrPersonalDatas[1], $arrPersonalDatas[0]);
            break;
        }

        return $buffer;
    }

    public function renderSingleItemBodyPersonalDataSingleFieldValue(int $pid, string $ptable, string $email, PersonalData $personalData, array $personalDatas, Model $originalModel, string $buffer): string
    {
        switch ($ptable) {
            case FormStorage::getTable():
            case FormStorageData::getTable():
                $buffer = StringUtil::getFormStorageDataValueAsString($this->personalDataManagerUi->formatSingleItemBodyPersonalDataSingleFieldValue($pid, $ptable, $email, $personalData, $personalDatas, $originalModel));
            break;
            default:
                if (empty($buffer)) {
                    return sprintf('<i>%s</i>', $this->translator->trans('WEM.SMARTGEAR.DEFAULT.NotFilled', [], 'contao_default'));
                }
            break;
        }

        return $buffer;
    }

    public function renderSingleItemBodyPersonalDataSingleFieldLabel(int $pid, string $ptable, string $email, PersonalData $personalData, array $personalDatas, Model $originalModel, string $buffer): string
    {
        switch ($ptable) {
            case FormStorage::getTable():
            case FormStorageData::getTable():
                $buffer = $personalData->field_label ?? $buffer;
            break;
        }

        return $buffer;
    }

    public function renderSingleItemBodyOriginalModelSingleFieldLabel(int $pid, string $ptable, string $email, string $field, $value, array $personalDatas, Model $originalModel, string $buffer): string
    {
        switch ($ptable) {
            case FormStorageData::getTable():
                if (sprintf('%s.%s.0', $ptable, $field) === $buffer) {
                    return $field;
                }
            break;
        }

        return $buffer;
    }

    public function renderSingleItemBodyPersonalDataSingle(int $pid, string $ptable, string $email, PersonalData $personalData, array $personalDatas, Model $originalModel, string $buffer): string
    {
        switch ($ptable) {
            case FormStorage::getTable():
                $buffer = $this->personalDataManagerUi->formatSingleItemBodyPersonalDataSingle((int) $personalData->pid, $personalData->ptable, $email, $personalData, $personalDatas, $originalModel);
            break;
        }

        return $buffer;
    }

    protected function getPersonalDataForFormStorage($objFormStorage): array
    {
        $formStorageDatas = FormStorageData::findItems(['pid' => $objFormStorage->id]);
        // make personalDatas all personal datas attached to this form
        $arrPersonalDatas = [];
        if ($formStorageDatas) {
            while ($formStorageDatas->next()) {
                // $objFormStorage->{$formStorageDatas->field_name} = $formStorageDatas->current()->getValueAsString();
                $objPersonalData = PersonalData::findOneByPidAndPTableAndField($formStorageDatas->id, FormStorageData::getTable(), 'value');
                if ($objPersonalData) {
                    $objPersonalData = $objPersonalData->current();
                    $arrPersonalDataValues = $objPersonalData->row();
                    $arrPersonalDataValues['field_label'] = $formStorageDatas->field_label;
                    $objPersonalData->setRow($arrPersonalDataValues);
                    $arrPersonalDatas[] = $objPersonalData;
                }
            }
        }

        return [$objFormStorage, $arrPersonalDatas];
    }
}
