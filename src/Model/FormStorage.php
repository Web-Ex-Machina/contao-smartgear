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
class FormStorage extends CoreModel
{
    public const STATUS_UNREAD = 'unread';
    public const STATUS_READ = 'read';
    public const STATUS_SPAM = 'spam';
    public const STATUS_OK = 'ok';
    public const STATUS_REPLIED = 'replied';
    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_sm_form_storage';

    public function getSender(): ?string
    {
        if (!empty($this->sender)) {
            return $this->sender;
        }

        $formStorageDatas = FormStorageData::findItems(['pid' => $this->id, 'field_name' => 'email'], 1);
        if (!$formStorageDatas) {
            return null;
        }

        return $formStorageDatas->first()->current()->getValueAsString();
    }
}
