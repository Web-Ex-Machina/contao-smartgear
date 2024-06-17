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

namespace WEM\SmartgearBundle\Backend\Component\Core\EventListener;

use Contao\Module;
use WEM\SmartgearBundle\Model\Member as MemberModel;

class CreateNewUserListener
{
    public function __invoke(string $userId, array $data, Module $module): void
    {
        $objMember = MemberModel::findByPk($userId);
        foreach (array_keys($data) as $field) {
            if ($objMember->isFieldInPersonalDataFieldsNames($field)) {
                $objMember->markModified($field);
            }
        }

        $objMember->save(); // will automatically triggers the encryption of personal data
    }
}
