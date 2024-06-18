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
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\Image;
use Contao\Input;
use Contao\StringUtil;
use tl_theme;
use WEM\SmartgearBundle\Model\Configuration\Configuration;

class Theme extends \tl_theme
{
    /** @var Backend */ //TODO : Bon typage ??
    private $parent;

    public function __construct()
    {
        parent::__construct(); // TODO : Class 'parent' is marked as @internal
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
            // TODO : Method 'checkPermission' not found in \tl_theme
        }

        if (Input::get('act') === 'delete') {
            if (!$this->canItemBeDeleted((int) Input::get('id'))) {
                throw new AccessDeniedException('Not enough permissions to '.Input::get('act').' theme ID '.Input::get('id').'.');
            }
        }
    }

    /**
     * Return the delete theme button.
     */
    public function deleteItem(array $row, string $href, string $label, string $title, string $icon, string $attributes): string
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
     * @throws \Exception
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
