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

class Layout extends Backend
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Check permissions to edit table tl_layout.
     *
     * @throws AccessDeniedException
     */
    public function checkPermission(): void
    {
        if (Input::get('act') === 'delete' && !$this->canItemBeDeleted((int) Input::get('id'))) {
            throw new AccessDeniedException('Not enough permissions to '.Input::get('act').' layout ID '.Input::get('id').'.');
        }
    }

    /**
     * Return the delete layout button.
     */
    public function deleteItem(DataContainerOperation &$config): void
    {
        if (!$this->canItemBeDeleted((int) $config->getRecord()['id'])) {
            $config->disable();
        }
    }

    /**
     * Check if the layout is being used by Smartgear.
     *
     * @param int $id layout's ID
     */
    protected function isItemUsedBySmartgear(int $id): bool
    {
        // try {
        //     $config = $this->configManager->load();
        //     if ($config->getSgInstallComplete()
        //     && (
        //         $id === (int) $config->getSgLayoutStandard()
        //         || $id === (int) $config->getSgLayoutFullwidth()
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
