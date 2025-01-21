<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2025 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

namespace WEM\SmartgearBundle\Backend\Component\Core\EventListener;

use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Model\Member as MemberModel;

class CreateNewUserListener
{
    /** @var CoreConfigurationManager */
    protected $coreConfigurationManager;

    public function __construct(
        CoreConfigurationManager $coreConfigurationManager
    ) {
        $this->coreConfigurationManager = $coreConfigurationManager;
    }

    public function __invoke(string $userId, array $data, \Contao\Module $module): void
    {
        try {
            /** @var CoreConfiguration */
            $coreConfig = $this->coreConfigurationManager->load();
        } catch (\Exception $e) {
            $coreConfig = null;
        }

        if ($coreConfig
        && $coreConfig->getSgUsePdmForMembers()
        ) {
            $objMember = MemberModel::findByPk($userId);
            foreach ($data as $field => $value) {
                if ($objMember->isFieldInPersonalDataFieldsNames($field)) {
                    $objMember->markModified($field);
                }
            }
            $objMember->save(); // will automatically triggers the encryption of personal data
        }
    }
}
