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

namespace WEM\SmartgearBundle\Backend\Module\FormDataManager\EventListener;

use Contao\Form;
use Contao\FormFieldModel;
use Contao\Model;
use Contao\PageModel;
use Exception;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\FormUtil;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Config\Module\FormDataManager\FormDataManager as FormDataManagerConfig;
use WEM\SmartgearBundle\Model\FormField;
use WEM\SmartgearBundle\Model\FormStorage;
use WEM\SmartgearBundle\Model\FormStorageData;
use WEM\UtilsBundle\Classes\StringUtil;

class ProcessFormDataListener
{
    /** @var CoreConfigurationManager */
    protected $coreConfigurationManager;
    protected $routingCandidates;

    public function __construct(
        CoreConfigurationManager $coreConfigurationManager,
        $routingCandidates
    ) {
        $this->coreConfigurationManager = $coreConfigurationManager;
        $this->routingCandidates = $routingCandidates;
    }

    public function __invoke(
        array $submittedData,
        array $formData,
        ?array $files,
        array $labels,
        Form $form
    ): void {
        try {
            /** @var CoreConfig */
            $coreConfig = $this->coreConfigurationManager->load();
            /** @var FormDataManagerConfig */
            $fdmConfig = $coreConfig->getSgFormDataManager();
            if ($coreConfig->getSgInstallComplete()
            && $fdmConfig->getSgInstallComplete()
            ) {
                // if ((bool) $form->getModel()->storeViaFormDataManager) {
                if (FormUtil::isFormConfigurationCompliantForFormDataManager($form->getModel()->id)) {
                    $objFormStorage = new FormStorage();

                    $objFormStorage->tstamp = time();
                    $objFormStorage->createdAt = time();
                    $objFormStorage->pid = $form->getModel()->id;
                    $objFormStorage->status = FormStorage::STATUS_UNREAD;
                    $objFormStorage->token = REQUEST_TOKEN;
                    $objFormStorage->completion_percentage = $this->calculateCompletionPercentage($submittedData, $files ?? [], $form);
                    $objFormStorage->delay_to_first_interaction = $this->calculateDelayToFirstInteraction($submittedData['fdm[first_appearance]'], $submittedData['fdm[first_interaction]']);
                    $objFormStorage->delay_to_submission = $this->calculateDelayToSubmission($submittedData['fdm[first_interaction]'], $form);
                    $objFormStorage->current_page = (int) $submittedData['fdm[current_page]'];
                    $objFormStorage->current_page_url = $submittedData['fdm[current_page_url]'];
                    $objFormStorage->referer_page = $this->getRefererPageId($submittedData['fdm[referer_page_url]']) ?? 0;
                    $objFormStorage->referer_page_url = $submittedData['fdm[referer_page_url]'];
                    $objFormStorage->save();

                    if (\array_key_exists('email', $submittedData)) {
                        $objFormStorage->sender = $submittedData['email'];
                        $objFormStorage->save();
                        $this->storeFieldValue('email', $submittedData['email'], $objFormStorage);
                        unset($submittedData['email']);
                    }

                    unset($submittedData['fdm[first_appearance]'], $submittedData['fdm[first_interaction]'], $submittedData['fdm[current_page]'], $submittedData['fdm[current_page_url]'], $submittedData['fdm[referer_page_url]']);

                    // empty fields are transmitted
                    foreach ($submittedData as $fieldName => $value) {
                        $this->storeFieldValue($fieldName, $value, $objFormStorage);
                    }
                    $this->storeFilesValues($files ?? [], $objFormStorage);
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    protected function getRefererPageId(string $url): ?int
    {
        $refererPageId = null;
        try {
            $refererPages = $this->routingCandidates->getCandidates(\Symfony\Component\HttpFoundation\Request::create($url));
            if (\count($refererPages) > 0) {
                $objPage = PageModel::findByAlias($refererPages[0]);
                if ($objPage) {
                    $refererPageId = $objPage->id;
                }
            }
        } catch (Exception $e) {
        }

        return $refererPageId;
    }

    protected function calculateDelayToFirstInteraction(string $firstAppearanceMs, string $firstInteractionMs): int
    {
        return (int) $firstInteractionMs - (int) $firstAppearanceMs;
    }

    protected function calculateDelayToSubmission(string $firstInteractionMs, Form $form): int
    {
        return (int) ((int) (microtime(true) * 1000) - (int) $firstInteractionMs);
    }

    protected function calculateCompletionPercentage(array $submittedData, array $files, Form $form): float
    {
        $formFields = FormFieldModel::findPublishedByPid($form->getModel()->id);
        $fieldsTotal = $formFields->count();
        $fieldsCompleted = 0;
        if ($formFields) {
            while ($formFields->next()) {
                $formField = $formFields->current();
                if (\in_array($formField->type, ['captcha', 'submit'], true)) {
                    --$fieldsTotal;
                    continue;
                }
                if ((\array_key_exists($formField->name, $submittedData) || \array_key_exists($formField->name, $files))
                && !empty($submittedData[$formField->name])
                ) {
                    ++$fieldsCompleted;
                }
            }
        }

        return $fieldsCompleted * 100 / $fieldsTotal;
    }

    protected function storeFieldValue(string $fieldName, $value, FormStorage $objFormStorage): FormStorageData
    {
        $objFormField = FormField::findItems(['name' => $fieldName, 'pid' => $objFormStorage->pid], 1);
        if (!$objFormField) {
            throw new Exception(sprintf('Unable to find field "%s" in form "%s"', $fieldName, $objFormStorage->getRelated('pid')->name));
        }
        $objFormStorageData = new FormStorageData();
        $objFormStorageData->tstamp = time();
        $objFormStorageData->createdAt = time();
        $objFormStorageData->pid = $objFormStorage->id;
        $objFormStorageData->field = $objFormField->id;
        $objFormStorageData->field_label = $objFormField->label;
        $objFormStorageData->field_name = $objFormField->name;
        $objFormStorageData->field_type = $objFormField->type;
        $objFormStorageData->value = $this->formatValueToStore($value, $objFormField->current());
        $objFormStorageData->contains_personal_data = $objFormField->contains_personal_data;
        $objFormStorageData->save();

        return $objFormStorageData;
    }

    protected function storeFilesValues(array $files, FormStorage $objFormStorage): array
    {
        $formStorageDatas = [];
        // empty file fields aren't transmitted
        $formFieldsFile = FormField::findItems(['type' => 'upload', 'pid' => $objFormStorage->pid]);
        if (!$formFieldsFile) {
            return $formStorageDatas;
        }
        while ($formFieldsFile->next()) {
            // if (\array_key_exists($formFieldsFile->name, $files)) {
            $formStorageDatas[] = $this->storeFileValue($formFieldsFile->name, $files[$formFieldsFile->name] ?? [], $objFormStorage);
            // }
        }

        return $formStorageDatas;
    }

    protected function storeFileValue(string $fieldName, array $fileData, FormStorage $objFormStorage): ?FormStorageData
    {
        $objFormField = FormField::findItems(['name' => $fieldName, 'pid' => $objFormStorage->pid], 1);
        if (!$objFormField) {
            throw new Exception(sprintf('Unable to find field "%s" in form "%s"', $fieldName, $objFormStorage->getRelated('pid')->name));
        }

        $value = '';
        if (empty($fileData)) {
            $value = FormStorageData::NO_FILE_UPLOADED;
        } elseif ($objFormField->storeFile) {
            $value = $fileData['uuid'];
        } else {
            $value = FormStorageData::FILE_UPLOADED_BUT_NOT_STORED;
        }

        $objFormStorageData = new FormStorageData();
        $objFormStorageData->tstamp = time();
        $objFormStorageData->createdAt = time();
        $objFormStorageData->pid = $objFormStorage->id;
        $objFormStorageData->field = $objFormField->id;
        $objFormStorageData->field_label = $objFormField->label;
        $objFormStorageData->field_name = $objFormField->name;
        $objFormStorageData->field_type = $objFormField->type;
        $objFormStorageData->value = $value;
        $objFormStorageData->contains_personal_data = $objFormField->contains_personal_data;
        $objFormStorageData->save();

        return $objFormStorageData;
    }

    protected function formatValueToStore($submittedValue, Model $objFormField)
    {
        $value = $submittedValue;
        switch ($objFormField->type) {
            case 'radio':
            case 'checkbox':
            case 'select':
                $options = StringUtil::deserialize($objFormField->options);
                $options2 = [];
                foreach ($options as $option) {
                    $options2[$option['value']] = $option;
                }
                $options = $options2;

                if (!\is_array($submittedValue)) {
                    $submittedValue = [$submittedValue];
                }
                $optionsSelected = [];
                foreach ($submittedValue as $submittedValueChunk) {
                    $optionsSelected[$submittedValueChunk] = ['label' => $options[$submittedValueChunk]['label'], 'value' => $submittedValueChunk];
                }
                $value = serialize($optionsSelected);
            break;
        }

        return $value;
    }
}
