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
use Contao\CoreBundle\DataContainer\DataContainerOperation;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\Input;
use WEM\SmartgearBundle\Model\Configuration\ConfigurationItem;

class FaqCategory extends Backend
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Check permissions to edit table tl_faq_category.
     *
     * @throws AccessDeniedException
     */
    public function checkPermission(): void
    {
        if (Input::get('act') === 'delete' && !$this->canItemBeDeleted((int) Input::get('id'))) {
            throw new AccessDeniedException('Not enough permissions to '.Input::get('act').' FAQ Category ID '.Input::get('id').'.');
        }
    }

    /**
     * Return the delete faq_category button.
     */
    public function deleteItem(DataContainerOperation &$config): void
    {
        if (!$this->canItemBeDeleted((int) $config->getRecord()['id'])) {
            $config->disable();
        }
    }

    /**
     * Check if the faq_category is being used by Smartgear.
     *
     * @param int $id faq_category's ID
     */
    protected function isItemUsedBySmartgear(int $id): bool
    {
        // try {
        //     $config = $this->configManager->load();
        //     $faqConfig = $config->getSgFaq();
        //     if ($faqConfig->getSgInstallComplete() && $id === (int) $faqConfig->getSgFaqCategory()) {
        //         return true;
        //     }
        // } catch (\Exception $e) {
        // }
        return 0 < ConfigurationItem::countItems(['contao_faq_category' => $id]);
    }

    protected function canItemBeDeleted(int $id): bool
    {
        return $this->User->admin || !$this->isItemUsedBySmartgear($id);
    }
}
