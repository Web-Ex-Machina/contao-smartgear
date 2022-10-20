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

use WEM\PersonalDataManagerBundle\Model\Traits\PersonalDataTrait as PDMTrait;

/**
 * Reads and writes items.
 */
class Member extends \Contao\MemberModel
{
    use PDMTrait;
    /**
     * Default order column.
     *
     * @var string
     */
    protected static $strOrderColumn = 'dateAdded DESC';

    protected static $personalDataFieldsNames = [
        'firstname',
        'lastname',
        'dateOfBirth',
        'gender',
        'company',
        'street',
        'postal',
        'city',
        'state',
        'country',
        'phone',
        'mobile',
        'fax',
    ];
    protected static $personalDataFieldsDefaultValues = [
        'firstname' => 'managed_by_pdm',
        'lastname' => 'managed_by_pdm',
        'dateOfBirth' => '0',
        'gender' => 'managed_by_pdm',
        'company' => 'managed_by_pdm',
        'street' => 'managed_by_pdm',
        'postal' => 'managed_by_pdm',
        'city' => 'managed_by_pdm',
        'state' => 'managed_by_pdm',
        'country' => '00',
        'phone' => 'managed_by_pdm',
        'mobile' => 'managed_by_pdm',
        'fax' => 'managed_by_pdm',
    ];
    protected static $personalDataFieldsAnonymizedValues = [
        'firstname' => 'anonymized',
        'lastname' => 'anonymized',
        'dateOfBirth' => '',
        'gender' => '',
        'company' => '',
        'street' => '',
        'postal' => '',
        'city' => '',
        'state' => '',
        'country' => '',
        'phone' => '',
        'mobile' => '',
        'fax' => '',
    ];
    protected static $personalDataPidField = 'id';
    protected static $personalDataEmailField = 'email';
    protected static $personalDataPtable = 'tl_member';

    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_member';
}
