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
use Contao\CoreBundle\DataContainer\DataContainerOperation;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\Input;
use WEM\SmartgearBundle\Model\Configuration\Configuration;
use WEM\SmartgearBundle\Model\Configuration\ConfigurationItem;

class Page extends Backend
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Check permissions to edit table tl_page.
     *
     * @throws AccessDeniedException
     */
    public function checkPermission(): void
    {
        if (Input::get('act') === 'delete' && !$this->canItemBeDeleted((int) Input::get('id'))) {
            throw new AccessDeniedException('Not enough permissions to '.Input::get('act').' page ID '.Input::get('id').'.');
        }
    }

    /**
     * Return the delete page button.
     */
    public function deleteItem(DataContainerOperation &$config): void
    {
        if (!$this->canItemBeDeleted((int) $config->getRecord()['id'])) {
            $config->disable();
        }
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
