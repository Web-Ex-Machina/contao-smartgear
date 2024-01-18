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
use tl_theme;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Model\Configuration\Configuration;

class Theme extends \tl_theme
{
    /** @var CoreConfigurationManager */
    private $configManager;
    /** @var Backend */
    private $parent;

    public function __construct()
    {
        parent::__construct();
        $this->configManager = System::getContainer()->get('smartgear.config.manager.core');
        $this->parent = new tl_theme();
    }

    /**
     * Check permissions to edit table theme.
     *
     * @throws AccessDeniedException
     */
    public function checkPermission(): void
    {
        if (method_exists($this->parent, 'checkPermission')) {
            // parent function removed in commit https://github.com/contao/contao/commit/68b169eca43e4fc7ef3dddc7336b0c84905dec92
            parent::checkPermission();
        }

        // Check current action
        switch (Input::get('act')) {
            case 'delete':
                if (!$this->canItemBeDeleted((int) Input::get('id'))) {
                    throw new AccessDeniedException('Not enough permissions to '.Input::get('act').' theme ID '.Input::get('id').'.');
                }
            break;
        }
    }

    /**
     * Return the delete theme button.
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
        if (!$this->canItemBeDeleted((int) $row['id'])) {
            return Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
        }

        return '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
    }

    /**
     * Check if the theme is being used by Smartgear.
     *
     * @param int $id theme's ID
     */
    protected function isItemUsedBySmartgear(int $id): bool
    {
        // try {
        //     $config = $this->configManager->load();
        //     if ($config->getSgInstallComplete() && $id === (int) $config->getSgTheme()) {
        //         return true;
        //     }
        // } catch (\Exception $e) {
        // }
        if (0 < Configuration::countItems(['contao_theme' => $id])) {
            return true;
        }

        return false;
    }

    protected function canItemBeDeleted(int $id): bool
    {
        return $this->User->admin || !$this->isItemUsedBySmartgear($id);
    }
}
