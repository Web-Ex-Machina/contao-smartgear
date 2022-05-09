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

use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\Image;
use Contao\Input;
use Contao\System;

$GLOBALS['TL_DCA']['tl_user']['config']['onload_callback'][] = ['tl_wem_sg_user', 'checkPermission'];
$GLOBALS['TL_DCA']['tl_user']['list']['operations']['delete']['button_callback'] = ['tl_wem_sg_user', 'deleteUser'];

class tl_wem_sg_user extends tl_user
{
    /**
     * Check permissions to edit table tl_user.
     *
     * @throws AccessDeniedException
     */
    public function checkPermission(): void
    {
        parent::checkPermission();

        // Check current action
        switch (Input::get('act')) {
            case 'delete':
                if ($this->isUserUsedBySmartgear((int) Input::get('id'))) {
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
    public function deleteUser($row, $href, $label, $title, $icon, $attributes)
    {
        if ($this->isUserUsedBySmartgear((int) $row['id'])) {
            return Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
        }

        return parent::deleteUser($row, $href, $label, $title, $icon, $attributes);
    }

    /**
     * Check if the user is being used by Smartgear.
     *
     * @param int $id user's ID
     */
    protected function isUserUsedBySmartgear(int $id): bool
    {
        $configManager = System::getContainer()->get('smartgear.config.manager.core');
        try {
            $config = $configManager->load();
            if ($config->getSgInstallComplete() && $id === (int) $config->getSgUserWebmaster()) {
                return true;
            }
        } catch (\Exception $e) {
        }

        return false;
    }
}
