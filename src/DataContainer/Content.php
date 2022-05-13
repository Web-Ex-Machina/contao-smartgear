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

class Content extends \tl_content
{
    /** @var CoreConfigurationManager */
    private $configManager;

    public function __construct()
    {
        parent::__construct();
        $this->configManager = System::getContainer()->get('smartgear.config.manager.core');
    }

    /**
     * Check permissions to edit table tl_content.
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

        return parent::deleteElement($row, $href, $label, $title, $icon, $attributes);
    }

    /**
     * Check if the content is being used by Smartgear.
     *
     * @param int $id content's ID
     */
    protected function isItemUsedBySmartgear(int $id): bool
    {
        try {
            $config = $this->configManager->load();
            if ($config->getSgInstallComplete()
            && (
                $id === (int) $config->getSgContent404Headline()
                || $id === (int) $config->getSgContent404Sitemap()
                || $id === (int) $config->getSgContentLegalNotice()
                || $id === (int) $config->getSgContentPrivacyPolitics()
                || $id === (int) $config->getSgContentSitemap()
            )
            ) {
                return true;
            }
            $blogConfig = $config->getSgBlog();
            if ($blogConfig->getSgInstallComplete()
            && (
                $id === (int) $blogConfig->getSgContentHeadline()
                || $id === (int) $blogConfig->getSgContentList()
            )
            ) {
                return true;
            }
            $eventsConfig = $config->getSgEvents();
            if ($eventsConfig->getSgInstallComplete()
            && (
                $id === (int) $eventsConfig->getSgContentHeadline()
                || $id === (int) $eventsConfig->getSgContentList()
            )
            ) {
                return true;
            }
            $faqConfig = $config->getSgFaq();
            if ($faqConfig->getSgInstallComplete() && $id === (int) $faqConfig->getSgContent()) {
                return true;
            }
            $formContactConfig = $config->getSgFormContact();
            if ($formContactConfig->getSgInstallComplete()
            && (
                $id === (int) $formContactConfig->getSgContentHeadlineArticleForm()
                || $id === (int) $formContactConfig->getSgContentFormArticleForm()
                || $id === (int) $formContactConfig->getSgContentHeadlineArticleFormSent()
                || $id === (int) $formContactConfig->getSgContentTextArticleFormSent()
            )
            ) {
                return true;
            }
        } catch (\Exception $e) {
        }

        return false;
    }
}
