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

namespace WEM\SmartgearBundle\Model;

use Exception;
use WEM\PersonalDataManagerBundle\Model\Traits\PersonalDataTrait as PDMTrait;
use WEM\UtilsBundle\Model\Model as CoreModel;

/**
 * Reads and writes items.
 */
class FormStorageData extends CoreModel
{
    use PDMTrait;

    protected static $personalDataFieldsNames = [
        'value',
    ];
    protected static $personalDataFieldsDefaultValues = [
        'value' => 'managed_by_pdm',
    ];
    protected static $personalDataFieldsAnonymizedValues = [
        'value' => 'anonymized',
    ];
    protected static $personalDataPidField = 'id';
    protected static $personalDataEmailField = 'email';
    protected static $personalDataPtable = 'tl_sm_form_storage_data';
    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_sm_form_storage_data';

    public function getPersonalDataEmailFieldValue(): string
    {
        $objFDS = self::findItems(['pid' => $this->pid, 'field_label' => 'email'], 1);
        if (!$objFDS) {
            throw new Exception('Unable to find the email field');
        }

        return $objFDS->value;
    }

    public function shouldManagePersonalData(): bool
    {
        return (bool) $this->contains_personal_data;
    }
}
