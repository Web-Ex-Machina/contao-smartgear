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

namespace WEM\SmartgearBundle\DataContainer;

use Contao\BackendUser;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\Image;
use Contao\Input;
use Contao\StringUtil;
use Contao\System;
use NotificationCenter\tl_nc_gateway;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;

class NotificationGateway extends tl_nc_gateway
{
    /** @var CoreConfigurationManager */
    private $configManager;

    public function __construct()
    {
        $this->import(BackendUser::class, 'User');
        $this->configManager = System::getContainer()->get('smartgear.config.manager.core');
    }

    /**
     * Check permissions to edit table tl_nc_gateway.
     *
     * @throws AccessDeniedException
     */
    public function checkPermission(): void
    {
        // Check current action
        switch (Input::get('act')) {
            case 'delete':
                if (!$this->canItemBeDeleted((int) Input::get('id'))) {
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
    public function deleteItem($row, $href, $label, $title, $icon, $attributes)
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
        try {
            $config = $this->configManager->load();
            if ($config->getSgInstallComplete() && $id === (int) $config->getSgNotificationGatewayEmail()) {
                return true;
            }
        } catch (\Exception $e) {
        }

        return false;
    }

    protected function canItemBeDeleted(int $id): bool
    {
        return $this->User->admin || !$this->isItemUsedBySmartgear($id);
    }
}
