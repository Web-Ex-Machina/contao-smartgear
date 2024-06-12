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
use Contao\System;
use Exception;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;

class Files extends \tl_files
{
    /** @var CoreConfigurationManager */
    private $configManager;

    public function __construct()
    {
        parent::__construct();
        $this->configManager = System::getContainer()->get('smartgear.config.manager.core');
    }

    public function uploadWarningMessage(): void
    {
        $strText = '';
        try {
            $strText = Util::getLocalizedTemplateContent('{public_or_web}/bundles/wemsmartgear/backend/tl_files/{lang}/upload_warning.html5', \Contao\BackendUser::getInstance()->language, '{public_or_web}/bundles/wemsmartgear/backend/tl_files/fr/upload_warning.html5');
        } catch (Exception) {
            // do nothing
        }
        if (!empty($strText)) {
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
        parent::checkPermission();

        // Check current action
        switch (Input::get('act')) {
            case 'delete':
                if (!$this->canItemBeDeleted(Input::get('id'))) {
                    throw new AccessDeniedException('Not enough permissions to '.Input::get('act').' files ID '.Input::get('id').'.');
                }
            break;
        }
    }

    /**
     * Return the delete files button.
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
        if (!$this->canItemBeDeleted($row['id'])) {
            return Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
        }

        return parent::deleteFile($row, $href, $label, $title, $icon, $attributes);
    }

    /**
     * Check if the files is being used by Smartgear.
     */
    protected function isItemUsedBySmartgear(string $id): bool
    {
        // try {
        //     /** @var CoreConfig */
        //     $config = $this->configManager->load();
        //     if (\in_array($id, $config->getContaoFoldersIdsForAll(), true)) {
        //         return true;
        //     }
        // } catch (\Exception $e) {
        // }

        return false;
    }

    protected function canItemBeDeleted(string $id): bool
    {
        return $this->User->admin || !$this->isItemUsedBySmartgear($id);
    }
}
