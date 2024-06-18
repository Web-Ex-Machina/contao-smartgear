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
use WEM\SmartgearBundle\Model\Configuration\Configuration;
use WEM\SmartgearBundle\Model\Configuration\ConfigurationItem;

class Page extends \tl_page // TODO : Class 'tl_page' is marked as @internal
{
    public function __construct()
    {
        parent::__construct(); // TODO : Class 'parent' is marked as @internal
    }

    /**
     * Check permissions to edit table tl_page.
     *
     * @throws AccessDeniedException
     */
    public function checkPermission(): void
    {
        parent::checkPermission();  // TODO : Class 'parent' is marked as @internal

        if (Input::get('act') === 'delete' && !$this->canItemBeDeleted((int) Input::get('id'))) {
            throw new AccessDeniedException('Not enough permissions to '.Input::get('act').' page ID '.Input::get('id').'.');
        }
    }

    /**
     * Return the delete page button.
     */
    public function deleteItem(array $row, string $href, string $label, string $title, string $icon, string $attributes): string
    {
        if (!$this->canItemBeDeleted((int) $row['id'])) {
            return Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
        }

        return parent::deletePage(...\func_get_args()); // TODO : Method 'deletePage' not found in \tl_page
    }

    /**
     * Check if the page is being used by Smartgear.
     *
     * @param int $id page's ID
     * @throws \Exception
     */
    protected function isItemUsedBySmartgear(int $id): bool
    {
        return 0 < Configuration::countItems(['contao_page_root' => $id])
        || 0 < Configuration::countItems(['contao_page_home' => $id])
        || 0 < Configuration::countItems(['contao_page_404' => $id])
        || 0 < ConfigurationItem::countItems(['contao_page' => $id])
        || 0 < ConfigurationItem::countItems(['contao_page_form' => $id])
        || 0 < ConfigurationItem::countItems(['contao_page_form_sent' => $id]);
    }

    protected function canItemBeDeleted(int $id): bool
    {
        return $this->User->admin || !$this->isItemUsedBySmartgear($id);
    }
}
