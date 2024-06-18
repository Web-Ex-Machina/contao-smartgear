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
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\Image;
use Contao\Input;
use Contao\StringUtil;
use Contao\System;
use NotificationCenter\tl_nc_message;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;

class NotificationMessage extends tl_nc_message
{
    public function __construct()
    {
        $this->import(BackendUser::class, 'User');
    }

    /**
     * Check permissions to edit table tl_nc_message.
     *
     * @throws AccessDeniedException
     */
    public function checkPermission(): void
    {
        if (Input::get('act') === 'delete') {
            if (!$this->canItemBeDeleted((int) Input::get('id'))) {
                throw new AccessDeniedException('Not enough permissions to '.Input::get('act').' notification message ID '.Input::get('id').'.');
            }
        }
    }

    /**
     * Return the delete notification message button.
     */
    public function deleteItem(array $row, string $href, string $label, string $title, string $icon, string $attributes): string
    {
        if (!$this->canItemBeDeleted((int) $row['id'])) {
            return Image::getHtml(preg_replace('/\.gif$/i', '_.gif', $icon)).' '; // yup, gif not svg
        }

        return '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
    }

    /**
     * Check if the notification message is being used by Smartgear.
     *
     * @param int $id notification message's ID
     */
    protected function isItemUsedBySmartgear(int $id): bool
    {
        // try {
        //     /** @var CoreConfig $config */
        //     $config = $this->configManager->load();
        //     $formContactConfig = $config->getSgFormContact();
        //     if ($formContactConfig->getSgInstallComplete()
        //     &&
        //     (
        //         $id === (int) $formContactConfig->getSgNotificationMessageUser()
        //         || $id === (int) $formContactConfig->getSgNotificationMessageAdmin()
        //     )
        //     ) {
        //         return true;
        //     }
        //     $extranetConfig = $config->getSgExtranet();
        //     if ($extranetConfig->getSgInstallComplete()
        //     &&
        //     (
        //         $id === (int) $extranetConfig->getSgNotificationPasswordMessage()
        //         || $id === (int) $extranetConfig->getSgNotificationChangeDataMessage()
        //         || $id === (int) $extranetConfig->getSgNotificationSubscriptionMessage()
        //     )
        //     ) {
        //         return true;
        //     }
        // } catch (\Exception $e) {
        // }

        return false;
    }

    protected function canItemBeDeleted(int $id): bool
    {
        return $this->User->admin || !$this->isItemUsedBySmartgear($id);
    }
}
