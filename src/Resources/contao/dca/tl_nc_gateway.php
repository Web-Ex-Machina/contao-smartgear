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

$GLOBALS['TL_DCA']['tl_nc_gateway']['config']['onload_callback'] = ['tl_wem_sg_notification_gateway', 'checkPermission'];
$GLOBALS['TL_DCA']['tl_nc_gateway']['list']['operations']['delete']['button_callback'] = ['tl_wem_sg_notification_gateway', 'deleteNotificationGateway'];

class tl_wem_sg_notification_gateway
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
                if ($this->isNotificationGatewayUsedBySmartgear((int) Input::get('id'))) {
                    throw new AccessDeniedException('Not enough permissions to '.Input::get('act').' notification gateway ID '.Input::get('id').'.');
                }
            break;
        }
    }

    /**
     * Return the delete notification gateway button.
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
    public function deleteNotificationGateway($row, $href, $label, $title, $icon, $attributes)
    {
        if ($this->isNotificationGatewayUsedBySmartgear((int) $row['id'])) {
            return Image::getHtml(preg_replace('/\.gif$/i', '_.gif', $icon)).' '; // yup, gif not svg
        }

        return '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
    }

    /**
     * Check if the notification gateway is being used by Smartgear.
     *
     * @param int $id Notification gateway's ID
     */
    protected function isNotificationGatewayUsedBySmartgear(int $id): bool
    {
        $configManager = System::getContainer()->get('smartgear.config.manager.core');
        try {
            $config = $configManager->load();
            if ($config->getSgInstallComplete() && $id === (int) $config->getSgNotificationGatewayEmail()) {
                return true;
            }
        } catch (\Exception $e) {
        }

        return false;
    }
}
