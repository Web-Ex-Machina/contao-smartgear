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

use WEM\SmartgearBundle\Model\Member as MemberModell;

class CreateNewUserListener
{
    public function __construct()
    {
    }

    public function __invoke(string $userId, array $data, \Contao\Module $module): void
    {
        $objMember = MemberModell::findByPk($userId);
        // foreach ($data as $field => $value) {
        //     if ($objMember->isFieldInPersonalDataFieldsNames($field)) {
        //         $objMember->markModified($field);
        //     }
        // }
        dump(\get_class($objMember));
        $objMember->save(); // will automatically triggers the encryption of personal data
        dump($objMember);
        exit();
    }
}
