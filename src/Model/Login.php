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

use WEM\UtilsBundle\Model\Model as CoreModel;

/**
 * Reads and writes items.
 */
class Login extends CoreModel
{
    public const CONTEXT_BE = 'BE';

    public const CONTEXT_FE = 'FE';

    /**
     * Search fields.
     */
    public static array $arrSearchFields = ['tstamp', 'hash'];

    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_sm_login';
}
