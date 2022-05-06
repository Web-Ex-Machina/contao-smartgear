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

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\Image;
use Contao\Input;
use Contao\System;
use WEM\SmartgearBundle\Security\SmartgearPermissions;

$GLOBALS['TL_DCA']['tl_user_group']['config']['onload_callback'] = ['tl_wem_sg_user_group', 'checkPermission'];
$GLOBALS['TL_DCA']['tl_user_group']['list']['operations']['delete']['button_callback'] = ['tl_wem_sg_user_group', 'deleteUserGroup'];
$GLOBALS['TL_DCA']['tl_user_group']['fields']['smartgear_permissions'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['multiple' => true, 'helpwizard' => true],
    'options' => [
        $GLOBALS['TL_LANG']['WEMSG']['SECURITY']['CORE']['optGroup'] => [
            // SmartgearPermissions::CORE_SIMPLE => &$GLOBALS['TL_LANG']['WEMSG']['SECURITY']['CORE']['simple'],
            SmartgearPermissions::CORE_EXPERT => &$GLOBALS['TL_LANG']['WEMSG']['SECURITY']['CORE']['expert'],
        ],
        $GLOBALS['TL_LANG']['WEMSG']['SECURITY']['BLOG']['optGroup'] => [
            // SmartgearPermissions::BLOG_SIMPLE => &$GLOBALS['TL_LANG']['WEMSG']['SECURITY']['BLOG']['simple'],
            SmartgearPermissions::BLOG_EXPERT => &$GLOBALS['TL_LANG']['WEMSG']['SECURITY']['BLOG']['expert'],
        ],
        $GLOBALS['TL_LANG']['WEMSG']['SECURITY']['EVENTS']['optGroup'] => [
            // SmartgearPermissions::BLOG_SIMPLE => &$GLOBALS['TL_LANG']['WEMSG']['SECURITY']['BLOG']['simple'],
            SmartgearPermissions::EVENTS_EXPERT => &$GLOBALS['TL_LANG']['WEMSG']['SECURITY']['EVENTS']['expert'],
        ],
    ],
    'sql' => ['type' => 'blob', 'notnull' => false],
    'explanation' => 'smartgear_permissions',
];

PaletteManipulator::create()
    ->addLegend('smartgear_permissions_legend', null)
    ->addField('smartgear_permissions', 'smartgear_permissions_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_user_group')
;

class tl_wem_sg_user_group extends tl_user_group
{
    /**
     * Check permissions to edit table tl_user.
     *
     * @throws AccessDeniedException
     */
    public function checkPermission(): void
    {
        // Check current action
        switch (Input::get('act')) {
            case 'delete':
                if ($this->isUserGroupUsedBySmartgear((int) Input::get('id'))) {
                    throw new AccessDeniedException('Not enough permissions to '.Input::get('act').' user ID '.Input::get('id').'.');
                }
            break;
        }
    }

    /**
     * Return the delete user button.
     *
     * @param array  $row
     * @param string $href
     * @param string $label
     * @param string $title
     * @param string $icon
     * @param string $attributes
     *
     * @return string
     */
    public function deleteUserGroup($row, $href, $label, $title, $icon, $attributes)
    {
        if ($this->isUserGroupUsedBySmartgear((int) $row['id'])) {
            return Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
        }

        return '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
    }

    /**
     * Check if the user is being used by Smartgear.
     *
     * @param int $id user's ID
     */
    protected function isUserGroupUsedBySmartgear(int $id): bool
    {
        $configManager = System::getContainer()->get('smartgear.config.manager.core');
        try {
            $config = $configManager->load();
            if ($config->getSgInstallComplete()
            && (
                $id === (int) $config->getSgUsergroupWebmasters()
                || $id === (int) $config->getSgUsergroupAdministrators()
            )
            ) {
                return true;
            }
        } catch (\Exception $e) {
        }

        return false;
    }
}
