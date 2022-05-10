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
use WEM\SmartgearBundle\Classes\Dca\Manipulator as DCAManipulator;

DCAManipulator::create('tl_faq_category')
    ->addConfigOnloadCallback('tl_wem_sg_faq_category', 'checkPermission')
    ->setListOperationsDeleteButtonCallback('tl_wem_sg_faq_category', 'deleteCategory')
;

/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @property News $News
 */
class tl_wem_sg_faq_category extends tl_faq_category
{
    /**
     * Check permissions to edit table tl_faq_category.
     *
     * @throws AccessDeniedException
     */
    public function checkPermission(): void
    {
        parent::checkPermission();

        // Check current action
        switch (Input::get('act')) {
            case 'delete':
                if ($this->isFaqCategoryUsedBySmartgear((int) Input::get('id'))) {
                    throw new AccessDeniedException('Not enough permissions to '.Input::get('act').' faq_category ID '.Input::get('id').'.');
                }
            break;
        }
    }

    /**
     * Return the delete faq_category button.
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
    public function deleteCategory($row, $href, $label, $title, $icon, $attributes)
    {
        if ($this->isFaqCategoryUsedBySmartgear((int) $row['id'])) {
            return Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
        }

        return parent::deleteCategory($row, $href, $label, $title, $icon, $attributes);
    }

    /**
     * Check if the faq_category is being used by Smartgear.
     *
     * @param int $id faq_category's ID
     */
    protected function isFaqCategoryUsedBySmartgear(int $id): bool
    {
        $configManager = System::getContainer()->get('smartgear.config.manager.core');
        try {
            $config = $configManager->load();
            $faqConfig = $config->getSgFaq();
            if ($faqConfig->getSgInstallComplete() && $id === (int) $faqConfig->getSgFaqCategory()) {
                return true;
            }
        } catch (\Exception $e) {
        }

        return false;
    }
}
