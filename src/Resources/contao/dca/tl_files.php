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

use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\Image;
use Contao\Input;
use Contao\System;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;

$GLOBALS['TL_DCA']['tl_files']['config']['onload_callback'][] = ['tl_wem_sg_files', 'checkPermission'];
$GLOBALS['TL_DCA']['tl_files']['list']['operations']['delete']['button_callback'] = ['tl_wem_sg_files', 'deleteFile'];

/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @property News $News
 */
class tl_wem_sg_files extends tl_files
{
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
                if ($this->isFileUsedBySmartgear(Input::get('id'))) {
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
    public function deleteFile($row, $href, $label, $title, $icon, $attributes)
    {
        if ($this->isFileUsedBySmartgear($row['id'])) {
            return Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
        }

        return parent::deleteFile($row, $href, $label, $title, $icon, $attributes);
    }

    /**
     * Check if the files is being used by Smartgear.
     *
     * @param int $id files's ID
     */
    protected function isFileUsedBySmartgear($id): bool
    {
        $configManager = System::getContainer()->get('smartgear.config.manager.core');
        try {
            $config = $configManager->load();
            if ($config->getSgInstallComplete()
            && (
                CoreConfig::DEFAULT_CLIENT_FILES_FOLDER === $id
                || CoreConfig::DEFAULT_CLIENT_LOGOS_FOLDER === $id
            )
            ) {
                return true;
            }
            $blogConfig = $config->getSgBlog();
            if ($blogConfig->getSgInstallComplete() && $id === $blogConfig->getCurrentPreset()->getSgNewsFolder()) {
                return true;
            }
            $eventsConfig = $config->getSgEvents();
            if ($eventsConfig->getSgInstallComplete() && $id === $eventsConfig->getSgEventsFolder()) {
                return true;
            }
        } catch (\Exception $e) {
        }

        return false;
    }
}
