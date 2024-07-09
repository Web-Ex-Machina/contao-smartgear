<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2024 Web ex Machina
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
use Exception;
use tl_form;
use WEM\SmartgearBundle\Classes\FormUtil;
use WEM\SmartgearBundle\Exceptions\Module\FormDataManager\EmailFieldNotMandatoryInForm;
use WEM\SmartgearBundle\Exceptions\Module\FormDataManager\FormNotConfiguredToStoreValues;
use WEM\SmartgearBundle\Exceptions\Module\FormDataManager\NoEmailFieldInForm;
use WEM\SmartgearBundle\Model\Configuration\ConfigurationItem;
use WEM\SmartgearBundle\Model\FormField;
use WEM\SmartgearBundle\Model\FormStorage;

// class Form extends \tl_form
class Form extends Backend
{
    private readonly \tl_form $parent;

    public function __construct()
    {
        parent::__construct();
        $this->parent = new tl_form();
    }

    public function listItems(array $row, string $label, DataContainer $dc, array $labels): array
    {
        try {
            // check form configuration
            FormUtil::checkFormConfigurationCompliantForFormDataManager($row['id']);

            $nbFormStorage = FormStorage::countItems(['pid' => $row['id']]);

            $labels[1] = FormStorage::countItems(['pid' => $row['id']]);
        } catch (FormNotConfiguredToStoreValues|NoEmailFieldInForm|EmailFieldNotMandatoryInForm|Exception $e) {
            $labels[1] = $e->getMessage();
        }

        return $labels;
    }

    public function onSubmitCallback(DataContainer $dc): void
    {
        // if the form has to be managed by FDM, assign a mandatory email field
        try {
            // check form configuration
            FormUtil::checkFormConfigurationCompliantForFormDataManager($dc->id);
        } catch (FormNotConfiguredToStoreValues) {
            // do nothing
        } catch (NoEmailFieldInForm) {
            // add a mandatory email field
            $objFormFieldEmail = new FormField();
            $objFormFieldEmail->pid = $dc->id;
            $objFormFieldEmail->type = 'text';
            $objFormFieldEmail->rgxp = 'email';
            $objFormFieldEmail->name = 'email';
            $objFormFieldEmail->label = $GLOBALS['TL_LANG']['WEMSG']['FDM']['FORM']['emailFieldLabel'];
            $objFormFieldEmail->placeholder = $GLOBALS['TL_LANG']['WEMSG']['FDM']['FORM']['emailFieldPlaceholder'];
            $objFormFieldEmail->sorting = 32;
            $objFormFieldEmail->mandatory = 1;
            $objFormFieldEmail->tstamp = time();
            $objFormFieldEmail->save();
        } catch (EmailFieldNotMandatoryInForm) {
            // retrieve the email field and make it mandatory
            $objFormFieldEmail = FormField::findItems(['pid' => $dc->id, 'name' => 'email']);
            $objFormFieldEmail->mandatory = 1;
            $objFormFieldEmail->save();
        } catch (Exception $e) {
            $labels[1] = $e->getMessage();
        }
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

        if (Input::get('act') === 'delete' && !$this->canItemBeDeleted((int) Input::get('id'))) {
            throw new AccessDeniedException('Not enough permissions to '.Input::get('act').' form ID '.Input::get('id').'.');
        }
    }

    /**
     * Return the delete form button.
     */
    public function deleteItem(array $row, string $href, string $label, string $title, string $icon, string $attributes): string
    {
        if (!$this->canItemBeDeleted((int) $row['id'])) {
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
        // try {
        //     $formContactConfig = $this->configurationManager->load()->getSgFormContact();
        //     if ($formContactConfig->getSgInstallComplete() && $id === (int) $formContactConfig->getSgFormContact()) {
        //         return true;
        //     }
        // } catch (\Exception $e) {
        // }
        return 0 < ConfigurationItem::countItems(['contao_form' => $id]);
    }

    protected function canItemBeDeleted(int $id): bool
    {
        return $this->User->admin || !$this->isItemUsedBySmartgear($id);
    }
}
