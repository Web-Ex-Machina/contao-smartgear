<?php

declare(strict_types=1);

/*
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2025 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

namespace WEM\SmartgearBundle\DataContainer;

use Contao\Backend;
use Contao\Input;
use Contao\CoreBundle\DataContainer\DataContainerOperation;
use Contao\CoreBundle\Exception\AccessDeniedException;
use WEM\SmartgearBundle\Model\Configuration\ConfigurationItem;

// class Form extends \tl_form
class Form extends Backend
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Check permissions to edit table tl_form.
     *
     * @throws AccessDeniedException
     */
    public function checkPermission(): void
    {
        if (Input::get('act') === 'delete' && !$this->canItemBeDeleted((int) Input::get('id'))) {
            throw new AccessDeniedException('Not enough permissions to '.Input::get('act').' form ID '.Input::get('id').'.');
        }
    }

    /**
     * Return the delete form button.
     */
    public function deleteItem(DataContainerOperation &$config): void
    {
        if (!$this->canItemBeDeleted((int) $config->getRecord()['id'])) {
            $config->disable();
        }
    }

    /**
     * Check if the form is being used by Smartgear.
     *
     * @param int $id form's ID
     * @throws \Exception
     */
    protected function isItemUsedBySmartgear(int $id): bool
    {
        // try {
        //     $formContactConfig = $this->configurationManager->load()->getSgFormContact();
        //     if ($formContactConfig->getSgInstallComplete() && $id === (int) $formContactConfig->getSgFormContact()) {
        //         return true;
        //     }
        // } catch (\Exception $e) {
        // }
        return 0 < ConfigurationItem::countItems(['contao_form' => $id]);
    }

    protected function canItemBeDeleted(int $id): bool
    {
        return (null !== $this->User && $this->User->admin) || !$this->isItemUsedBySmartgear($id);
    }
}
