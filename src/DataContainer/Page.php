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
                if ($this->isItemUsedBySmartgear((int) Input::get('id'))) {
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
        if ($this->isItemUsedBySmartgear((int) $row['id'])) {
            return Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
        }

        return parent::deletePage($row, $href, $label, $title, $icon, $attributes);
    }

    /**
     * Check if the page is being used by Smartgear.
     *
     * @param int $id page's ID
     */
    protected function isItemUsedBySmartgear(int $id): bool
    {
        try {
            $config = $this->configManager->load();
            if ($config->getSgInstallComplete()
            && (
                $id === (int) $config->getSgPageRoot()
                || $id === (int) $config->getSgPageHome()
                || $id === (int) $config->getSgPage404()
                || $id === (int) $config->getSgPageLegalNotice()
                || $id === (int) $config->getSgPagePrivacyPolitics()
                || $id === (int) $config->getSgPageSitemap()
            )
            ) {
                return true;
            }
            $blogConfig = $config->getSgBlog();
            if ($blogConfig->getSgInstallComplete() && $id === (int) $blogConfig->getSgPage()) {
                return true;
            }
            $eventsConfig = $config->getSgEvents();
            if ($eventsConfig->getSgInstallComplete() && $id === (int) $eventsConfig->getSgPage()) {
                return true;
            }
            $faqConfig = $config->getSgFaq();
            if ($faqConfig->getSgInstallComplete() && $id === (int) $faqConfig->getSgPage()) {
                return true;
            }
            $formContactConfig = $config->getSgFormContact();
            if ($formContactConfig->getSgInstallComplete()
            && (
                $id === (int) $formContactConfig->getSgPageForm()
                || $id === (int) $formContactConfig->getSgPageFormSent()
            )
            ) {
                return true;
            }
        } catch (\Exception $e) {
        }

        return false;
    }
}