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
use Exception;
use WEM\SmartgearBundle\Classes\Util;

class Files extends Backend
{
    public function __construct()
    {
        parent::__construct();
    }

    public function uploadWarningMessage(): void
    {
        $strText = '';
        try {
            $strText = Util::getLocalizedTemplateContent('{public_or_web}/bundles/wemsmartgear/backend/tl_files/{lang}/upload_warning.html5', \Contao\BackendUser::getInstance()->language, '{public_or_web}/bundles/wemsmartgear/backend/tl_files/fr/upload_warning.html5');
        } catch (Exception) {
            // do nothing
        }

        if ($strText !== '' && $strText !== '0') {
            \Contao\Message::addInfo($strText);
        }
    }

    /**
     * Check permissions to edit table tl_files.
     *
     * @throws AccessDeniedException
     */
    public function checkPermission(): void
    {
        if (Input::get('act') === 'delete' && !$this->canItemBeDeleted((int) Input::get('id'))) {
            throw new AccessDeniedException('Not enough permissions to '.Input::get('act').' files ID '.Input::get('id').'.');
        }
    }

    /**
     * Return the delete files button.
     */
    public function deleteItem(DataContainerOperation &$config): void
    {
        if (!$this->canItemBeDeleted((int) $config->getRecord()['id'])) {
            $config->disable();
        }
    }

    /**
     * Check if the files is being used by Smartgear.
     */
    protected function isItemUsedBySmartgear(int $id): bool
    {
        // try {
        //     /** @var CoreConfig $config */
        //     $config = $this->configManager->load();
        //     if (\in_array($id, $config->getContaoFoldersIdsForAll(), true)) {
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
