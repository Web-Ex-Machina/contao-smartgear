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

use Contao\Backend;
use Contao\BackendUser;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\DataContainer;
use Contao\Image;
use Contao\Input;
use Contao\StringUtil;
use Contao\System;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Model\SocialNetwork as SocialNetworkModel;
use WEM\SmartgearBundle\Security\SmartgearPermissions;

class SocialNetworkCategory extends Backend
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
        parent::__construct();
        $this->import(BackendUser::class, 'User');
    }

    public function listItems(array $row, string $label, DataContainer $dc, array $labels): string
    {
        return $this->translator->trans($row['name'], [], 'contao_default');
    }

    public function groupCallback(string $group, string $mode, string $field, array $row, DataContainer $dc): string
    {
        // return $this->translator->trans($row['name'], [], 'contao_default');
        return $this->translator->trans($row['name'], [], 'contao_default');
    }

    /**
     * Check permissions to edit table tl_sm_social_network_category.
     *
     * @throws AccessDeniedException
     */
    public function checkPermission(): void
    {
        if (Input::get('act') === 'delete' && !$this->canItemBeDeleted((int) Input::get('id'))) {
            throw new AccessDeniedException('Not enough permissions to '.Input::get('act').' social network category ID '.Input::get('id').'.');
        }
    }

    /**
     * Return the edit header button.
     */
    public function editHeader(array $row, string $href, string $label, string $title, string $icon, string $attributes): string
    {
        return System::getContainer()->get('security.helper')->isGranted(SmartgearPermissions::SOCIALLINK_EXPERT) ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ' : Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
    }

    /**
     * Return the delete social network category button.
     */
    public function deleteItem(array $row, string $href, string $label, string $title, string $icon, string $attributes): string
    {
        if (!$this->canItemBeDeleted((int) $row['id'])) {
            return Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
        }

        return '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
    }

    /**
     * Check if the social network category is being used by Smartgear.
     *
     * @param int $id social network category's ID
     */
    protected function isItemUsed(int $id): bool
    {
        return SocialNetworkModel::countBy('pid', $id) > 0;
    }

    protected function canItemBeDeleted(int $id): bool
    {
        return $this->User->admin || !$this->isItemUsed($id);
    }
}
