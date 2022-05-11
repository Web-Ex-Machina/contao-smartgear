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

use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\Image;
use Contao\Input;
use Contao\System;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;

class Form extends \tl_form
{
    /** @var CoreConfigurationManager */
    private $configManager;

    public function __construct()
    {
        parent::__construct();
        $this->configManager = System::getContainer()->get('smartgear.config.manager.core');
    }

    /**
     * Check permissions to edit table tl_form.
     *
     * @throws AccessDeniedException
     */
    public function checkPermission(): void
    {
        parent::checkPermission();

        // Check current action
        switch (Input::get('act')) {
            case 'delete':
                if ($this->isItemUsedBySmartgear((int) Input::get('id'))) {
                    throw new AccessDeniedException('Not enough permissions to '.Input::get('act').' form ID '.Input::get('id').'.');
                }
            break;
        }
    }

    /**
     * Return the delete form button.
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

        return parent::deleteForm($row, $href, $label, $title, $icon, $attributes);
    }

    /**
     * Check if the form is being used by Smartgear.
     *
     * @param int $id form's ID
     */
    protected function isItemUsedBySmartgear(int $id): bool
    {
        try {
            $formContactConfig = $this->configManager->load()->getSgFormContact();
            if ($formContactConfig->getSgInstallComplete() && $id === (int) $formContactConfig->getSgFormContact()) {
                return true;
            }
        } catch (\Exception $e) {
        }

        return false;
    }
}
