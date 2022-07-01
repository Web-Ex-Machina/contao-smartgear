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

namespace WEM\SmartgearBundle\DataContainer;

use Contao\Backend;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\Image;
use Contao\Input;
use Contao\System;
use tl_content;
use tl_content_calendar;
use tl_content_news;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;

// class Content extends \tl_content
class Content extends Backend
{
    /** @var CoreConfigurationManager */
    private $configManager;
    /** @var Backend */
    private $parent;

    public function __construct()
    {
        parent::__construct();
        $this->configManager = System::getContainer()->get('smartgear.config.manager.core');
        switch (Input::get('do')) {
            case 'news':
                $this->parent = new tl_content_news();
            break;
            case 'calendar':
                $this->parent = new tl_content_calendar();
            break;
            default:
                $this->parent = new tl_content();
        }
    }

    /**
     * Check permissions to edit table tl_content.
     *
     * @throws AccessDeniedException
     */
    public function checkPermission(): void
    {
        // parent::checkPermission();
        $this->parent->checkPermission();

        // Check current action
        switch (Input::get('act')) {
            case 'delete':
                if ($this->isItemUsedBySmartgear((int) Input::get('id'))) {
                    throw new AccessDeniedException('Not enough permissions to '.Input::get('act').' content ID '.Input::get('id').'.');
                }
            break;
        }
    }

    /**
     * Return the delete content button.
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
        if ($this->isItemUsedBySmartgear((int) $row['id'])) {
            return Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
        }

        // return parent::deleteElement($row, $href, $label, $title, $icon, $attributes);
        if (method_exists($this->parent, 'deleteElement')) {
            return $this->parent->deleteElement($row, $href, $label, $title, $icon, $attributes);
        }

        return (new tl_content())->deleteElement($row, $href, $label, $title, $icon, $attributes);
    }

    /**
     * Check if the content is being used by Smartgear.
     *
     * @param int $id content's ID
     */
    protected function isItemUsedBySmartgear(int $id): bool
    {
        try {
            /** @var CoreConfig */
            $config = $this->configManager->load();
            if (\in_array($id, $config->getContaoContentsIdsForAll(), true)) {
                return true;
            }
        } catch (\Exception $e) {
        }

        return false;
    }
}
