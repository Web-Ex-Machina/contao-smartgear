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
use Exception;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Config\Module\FormDataManager\FormDataManager as FormDataManagerConfig;
use WEM\SmartgearBundle\Model\FormField;
use WEM\SmartgearBundle\Model\FormStorage;
use WEM\SmartgearBundle\Model\FormStorageData;

class ProcessFormDataListener
{
    /** @var CoreConfigurationManager */
    protected $coreConfigurationManager;

    public function __construct(
        CoreConfigurationManager $coreConfigurationManager
    ) {
        $this->coreConfigurationManager = $coreConfigurationManager;
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
                $objFormStorage = new FormStorage();

                $objFormStorage->tstamp = time();
                $objFormStorage->createdAt = time();
                $objFormStorage->form = $form->getModel()->id;
                $objFormStorage->status = FormStorage::STATUS_UNREAD;
                $objFormStorage->token = REQUEST_TOKEN;
                $objFormStorage->save();

                if (\array_key_exists('email', $submittedData)) {
                    $this->storeFieldValue('email', $submittedData['email'], $objFormStorage);
                    unset($submittedData['email']);
                }

                foreach ($submittedData as $fieldName => $value) {
                    $this->storeFieldValue($fieldName, $value, $objFormStorage);
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    protected function storeFieldValue(string $fieldName, $value, FormStorage $objFormStorage): FormStorageData
    {
        $objFormField = FormField::findItems(['name' => $fieldName, 'pid' => $objFormStorage->form], 1);
        if (!$objFormField) {
            throw new Exception('Unable to find field "%s" in form "%s"', $fieldName, $objFormStorage->getRelated('form')->name);
        }
        $objFormStorageData = new FormStorageData();
        $objFormStorageData->tstamp = time();
        $objFormStorageData->createdAt = time();
        $objFormStorageData->pid = $objFormStorage->id;
        $objFormStorageData->field = $objFormField->id;
        $objFormStorageData->field_label = $objFormField->label;
        $objFormStorageData->value = $value;
        $objFormStorageData->contains_personal_data = $objFormField->contains_personal_data;
        $objFormStorageData->save();

        return $objFormStorageData;
    }
}
