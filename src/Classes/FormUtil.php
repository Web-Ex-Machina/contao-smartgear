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

namespace WEM\SmartgearBundle\Classes;

use Contao\Form;
use Contao\FormModel;
use Contao\Model;
use Contao\PageModel;
use Exception;
use WEM\SmartgearBundle\Exceptions\Module\FormDataManager\FormNotConfiguredToStoreValues;
use WEM\SmartgearBundle\Exceptions\Module\FormDataManager\NoEmailFieldInForm;
use WEM\SmartgearBundle\Model\FormField;

class FormUtil
{
    public static function getPageFromForm(Form $form): ?PageModel
    {
        $objParent = $form->getParent();
        $model = Model::getClassFromTable($objParent->ptable);
        $objGreatParent = $model::findOneById($objParent->pid);

        return PageModel::findOneById($objGreatParent->pid);
    }

    public static function checkFormConfigurationCompliantForFormDataManager($formId): void
    {
        $objForm = FormModel::findById($formId);
        if (!$objForm) {
            throw new Exception('Form not found');
        }

        if (!$objForm->storeViaFormDataManager) {
            throw new FormNotConfiguredToStoreValues($GLOBALS['TL_LANG']['WEMSG']['FDM']['FORM']['notManagedByFDM']);
        }

        $objFormFieldEmail = FormField::findItems(['pid' => $formId, 'name' => 'email']);
        if (!$objFormFieldEmail) {
            throw new NoEmailFieldInForm($GLOBALS['TL_LANG']['WEMSG']['FDM']['FORM']['noEmailField']);
        }
    }

    public static function isFormConfigurationCompliantForFormDataManager($formId): bool
    {
        try {
            self::checkFormConfigurationCompliantForFormDataManager($formId);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
