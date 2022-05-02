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

$GLOBALS['TL_DCA']['tl_news_archive']['list']['operations']['delete']['button_callback'] = ['tl_wem_sg_news_archive', 'deleteArchive'];

/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @property News $News
 */
class tl_wem_sg_news_archive extends tl_news_archive
{
    /**
     * Check permissions to edit table tl_news_archive.
     *
     * @throws AccessDeniedException
     */
    public function checkPermission(): void
    {
        parent::checkPermission();

        // Check current action
        switch (Input::get('act')) {
            case 'delete':
                if ($this->isArchiveUsedBySmartgear((int) Input::get('id'))) {
                    throw new AccessDeniedException('Not enough permissions to '.Input::get('act').' news archive ID '.Input::get('id').'.');
                }
            break;
        }
    }

    /**
     * Return the delete archive button.
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
    public function deleteArchive($row, $href, $label, $title, $icon, $attributes)
    {
        if ($this->isArchiveUsedBySmartgear((int) $row['id'])) {
            return Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon));
        }

        return parent::deleteArchive($row, $href, $label, $title, $icon, $attributes);
    }

    /**
     * Check if the news archive is being used by Smartgear.
     *
     * @param int $id News archive's ID
     */
    protected function isArchiveUsedBySmartgear(int $id): bool
    {
        $configManager = System::getContainer()->get('smartgear.config.manager.core');
        try {
            $blogConfig = $configManager->load()->getSgBlog();
            if ($blogConfig->getSgInstallComplete() && $id === (int) $blogConfig->getSgNewsArchive()) {
                return true;
            }
        } catch (\Exception $e) {
        }

        return false;
    }
}
