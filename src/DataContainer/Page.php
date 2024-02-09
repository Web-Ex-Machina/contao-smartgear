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
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Model\Configuration\Configuration;
use WEM\SmartgearBundle\Model\Configuration\ConfigurationItem;

class Page extends \tl_page
{
    /** @var CoreConfigurationManager */
    private $configManager;

    public function __construct()
    {
        parent::__construct();
        $this->configManager = System::getContainer()->get('smartgear.config.manager.core');
    }

    /**
     * Check permissions to edit table tl_page.
     *
     * @throws AccessDeniedException
     */
    public function checkPermission(): void
    {
        parent::checkPermission();

        // Check current action
        switch (Input::get('act')) {
            case 'delete':
                if (!$this->canItemBeDeleted((int) Input::get('id'))) {
                    throw new AccessDeniedException('Not enough permissions to '.Input::get('act').' page ID '.Input::get('id').'.');
                }
            break;
        }
    }

    /**
     * Return the delete page button.
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

        return parent::deletePage(...\func_get_args());
    }

    /**
     * Check if the page is being used by Smartgear.
     *
     * @param int $id page's ID
     */
    protected function isItemUsedBySmartgear(int $id): bool
    {
        // try {
        //     /** @var CoreConfig */
        //     $config = $this->configManager->load();
        //     if (\in_array($id, $config->getContaoPagesIdsForAll(), true)) {
        //         return true;
        //     }
        // } catch (\Exception $e) {
        // }
        if (0 < Configuration::countItems(['contao_page_root' => $id])
        || 0 < Configuration::countItems(['contao_page_home' => $id])
        || 0 < Configuration::countItems(['contao_page_404' => $id])
        || 0 < ConfigurationItem::countItems(['contao_page' => $id])
        || 0 < ConfigurationItem::countItems(['contao_page_form' => $id])
        || 0 < ConfigurationItem::countItems(['contao_page_form_sent' => $id])
        ) {
            return true;
        }

        return false;
    }

    protected function canItemBeDeleted(int $id): bool
    {
        return $this->User->admin || !$this->isItemUsedBySmartgear($id);
    }
}
