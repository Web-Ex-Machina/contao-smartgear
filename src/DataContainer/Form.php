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
use Contao\DataContainer;
use Contao\Image;
use Contao\Input;
use Contao\System;
use Exception;
use tl_form;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\FormUtil;
use WEM\SmartgearBundle\Exceptions\Module\FormDataManager\FormNotConfiguredToStoreValues;
use WEM\SmartgearBundle\Exceptions\Module\FormDataManager\NoEmailFieldInForm;
use WEM\SmartgearBundle\Model\FormStorage;

// class Form extends \tl_form
class Form extends Backend
{
    /** @var CoreConfigurationManager */
    private $configurationManager;
    /** @var Backend */
    private $parent;

    public function __construct()
    {
        parent::__construct();
        $this->configurationManager = System::getContainer()->get('smartgear.config.manager.core');
        $this->parent = new tl_form();
    }

    public function listItems(array $row, string $label, DataContainer $dc, array $labels): array
    {
        try {
            $fdmConfig = $this->configurationManager->load()->getSgFormDataManager();
            if (!$fdmConfig->getSgInstallComplete()) {
                $labels[1] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['fdmNotInstalled'];
            } elseif ($fdmConfig->getSgInstallComplete()) {
                try {
                    // check form configuration
                    FormUtil::checkFormConfigurationCompliantForFormDataManager($row['id']);

                    $nbFormStorage = FormStorage::countItems(['pid' => $row['id']]);

                    $labels[1] = FormStorage::countItems(['pid' => $row['id']]);
                } catch (FormNotConfiguredToStoreValues $e) {
                    $labels[1] = $e->getMessage();
                } catch (NoEmailFieldInForm $e) {
                    $labels[1] = $e->getMessage();
                } catch (Exception $e) {
                    $labels[1] = $e->getMessage();
                }
            }
        } catch (\WEM\SmartgearBundle\Exceptions\File\NotFound $e) {
            $labels[1] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['fdmNotInstalled'];
        } catch (\Exception $e) {
            $labels[1] = '0';
        }

        return $labels;
    }

    /**
     * Check permissions to edit table tl_form.
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

        return $this->parent->deleteForm($row, $href, $label, $title, $icon, $attributes);
    }

    /**
     * Check if the form is being used by Smartgear.
     *
     * @param int $id form's ID
     */
    protected function isItemUsedBySmartgear(int $id): bool
    {
        try {
            $formContactConfig = $this->configurationManager->load()->getSgFormContact();
            if ($formContactConfig->getSgInstallComplete() && $id === (int) $formContactConfig->getSgFormContact()) {
                return true;
            }
        } catch (\Exception $e) {
        }

        return false;
    }
}
