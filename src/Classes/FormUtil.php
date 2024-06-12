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

use Contao\FormModel;
use Exception;
use WEM\SmartgearBundle\Exceptions\Module\FormDataManager\EmailFieldNotMandatoryInForm;
use WEM\SmartgearBundle\Exceptions\Module\FormDataManager\FormNotConfiguredToStoreValues;
use WEM\SmartgearBundle\Exceptions\Module\FormDataManager\NoEmailFieldInForm;
use WEM\SmartgearBundle\Model\FormField;

class FormUtil
{
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

        if (!$objFormFieldEmail->mandatory) {
            throw new EmailFieldNotMandatoryInForm($GLOBALS['TL_LANG']['WEMSG']['FDM']['FORM']['emailFieldPresentButNotMandatory']);
        }
    }

    public static function isFormConfigurationCompliantForFormDataManager($formId): bool
    {
        try {
            self::checkFormConfigurationCompliantForFormDataManager($formId);

            return true;
        } catch (Exception) {
            return false;
        }
    }
}
