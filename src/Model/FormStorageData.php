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

use Contao\Database;
use Contao\FilesModel;
use Exception;
use WEM\PersonalDataManagerBundle\Model\Traits\PersonalDataTrait as PDMTrait;
use WEM\SmartgearBundle\Classes\StringUtil;
use WEM\UtilsBundle\Model\Model as CoreModel;

/**
 * Reads and writes items.
 */
class FormStorageData extends CoreModel
{
    use PDMTrait;

    public const NO_FILE_UPLOADED = 'no_file_uploaded';
    public const FILE_UPLOADED_BUT_NOT_STORED = 'file_uploaded_but_not_stored';

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
        $objFS = FormStorage::findItems(['id' => $this->pid]);
        if ($objFS && !empty($objFS->sender)) {
            return $objFS->sender;
        }
        $objFDS = self::findItems(['pid' => $this->pid, 'field_name' => 'email'], 1);
        if (!$objFDS) {
            throw new Exception('Unable to find the email field');
        }

        return $objFDS->value;
    }

    public function shouldManagePersonalData(): bool
    {
        return (bool) $this->contains_personal_data;
    }

    public function getValueAsString(): string
    {
        return StringUtil::getFormStorageDataValueAsString($this->value);
    }

    public function getValueAsStringFormatted(): string
    {
        $value = $this->getValueAsString();
        switch ($this->field_type) {
            case 'textarea':
            case 'textareacustom':
                $value = nl2br($value ?? '');
            break;
            case 'upload':
                switch ($value) {
                    case self::NO_FILE_UPLOADED:
                        $value = 'AUCUN FICHIER TRANSMIS';
                    break;
                    case self::FILE_UPLOADED_BUT_NOT_STORED:
                        $value = 'FICHIER TRANSMIS MAIS NON ENREGISTRé';
                    break;
                    default:
                        // we should have an UUID here
                        $objFile = FilesModel::findByUuid($value);
                        if (!$objFile) {
                            $value = 'FICHIER TRANSMIS INTROUVABLE';
                        } else {
                            $value = $objFile->path;
                        }
                    break;
                }
            break;
        }

        return $value;
    }

    public static function deleteAll(): void
    {
        $objStatement = Database::getInstance()->prepare(sprintf('DELETE FROM %s', self::getTable()));
        $objResult = $objStatement->execute();

        $manager = \Contao\System::getContainer()->get('wem.personal_data_manager.service.personal_data_manager');
        $manager->deleteByPtable(self::getTable());
    }
}
