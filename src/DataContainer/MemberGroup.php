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

use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\Image;
use Contao\Input;
use Contao\StringUtil;
use Contao\System;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Model\Configuration\ConfigurationItem;

class MemberGroup extends \tl_member_group
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Check permissions to edit table member.
     *
     * @throws AccessDeniedException
     */
    public function checkPermission(): void
    {
        if (Input::get('act') === 'delete' && !$this->canItemBeDeleted((int) Input::get('id'))) {
            throw new AccessDeniedException('Not enough permissions to '.Input::get('act').' member group ID '.Input::get('id').'.');
        }
    }

    /**
     * Return the delete member button.
     */
    public function deleteItem(array $row, string $href, string $label, string $title, string $icon, string $attributes): string
    {
        if (!$this->canItemBeDeleted((int) $row['id'])) {
            return Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
        }

        return '<a href="'.$href.'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
    }

    /**
     * Check if the member is being used by Smartgear.
     *
     * @param int $id member's ID
     */
    protected function isItemUsedBySmartgear(int $id): bool
    {
        // try {
        //     /** @var CoreConfig $config */
        //     $config = $this->configManager->load();
        //     if (\in_array($id, $config->getContaoMemberGroupsIdsForAll(), true)) {
        //         return true;
        //     }
        // } catch (\Exception $e) {
        // }

        if (0 < ConfigurationItem::countItems(['contao_user_group' => $id])) {
            return true;
        }

        return false;
    }

    protected function canItemBeDeleted(int $id): bool
    {
        return $this->User->admin || !$this->isItemUsedBySmartgear($id);
    }
}
