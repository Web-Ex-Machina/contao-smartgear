<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2023 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

namespace WEM\SmartgearBundle\DataContainer;

use Contao\BackendUser;
use Contao\Config;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\Image;
use Contao\Input;
use Contao\StringUtil;
use Contao\System;
use NotificationCenter\tl_nc_gateway;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Model\Configuration\Configuration;

class NotificationGateway extends tl_nc_gateway
{
    public function __construct()
    {
        $this->import(BackendUser::class, 'User');
    }

    /**
     * Check permissions to edit table tl_nc_gateway.
     *
     * @throws AccessDeniedException
     */
    public function checkPermission(): void
    {
        if (Input::get('act') === 'delete') {
            if (!$this->canItemBeDeleted((int) Input::get('id'))) {
                throw new AccessDeniedException('Not enough permissions to '.Input::get('act').' notification gateway ID '.Input::get('id').'.');
            }
        }
    }

    /**
     * Return the delete notification gateway button.
     */
    public function deleteItem(array $row, string $href, string $label, string $title, string $icon, string $attributes): string
    {
        if (!$this->canItemBeDeleted((int) $row['id'])) {
            return Image::getHtml(preg_replace('/\.gif$/i', '_.gif', $icon)).' '; // yup, gif not svg
        }

        return '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
    }

    /**
     * Check if the notification gateway is being used by Smartgear.
     *
     * @param int $id notification gateway's ID
     */
    protected function isItemUsedBySmartgear(int $id): bool
    {
        // try {
        //     $config = $this->configManager->load();
        //     if ($config->getSgInstallComplete() && $id === (int) $config->getSgNotificationGatewayEmail()) {
        //         return true;
        //     }
        // } catch (\Exception $e) {
        // }

        if (0 < Configuration::countItems(['email_gateway' => $id])
        || (int) $id === (int) Config::get('wem_sg_support_form_gateway')
        ) {
            return true;
        }

        return false;
    }

    protected function canItemBeDeleted(int $id): bool
    {
        return $this->User->admin || !$this->isItemUsedBySmartgear($id);
    }
}
