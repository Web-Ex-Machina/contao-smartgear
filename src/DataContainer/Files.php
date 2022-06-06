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
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;

class Files extends \tl_files
{
    /** @var CoreConfigurationManager */
    private $configManager;

    public function __construct()
    {
        parent::__construct();
        $this->configManager = System::getContainer()->get('smartgear.config.manager.core');
    }

    /**
     * Check permissions to edit table tl_files.
     *
     * @throws AccessDeniedException
     */
    public function checkPermission(): void
    {
        parent::checkPermission();

        // Check current action
        switch (Input::get('act')) {
            case 'delete':
                if ($this->isItemUsedBySmartgear(Input::get('id'))) {
                    throw new AccessDeniedException('Not enough permissions to '.Input::get('act').' files ID '.Input::get('id').'.');
                }
            break;
        }
    }

    /**
     * Return the delete files button.
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
        if ($this->isItemUsedBySmartgear($row['id'])) {
            return Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
        }

        return parent::deleteFile($row, $href, $label, $title, $icon, $attributes);
    }

    /**
     * Check if the files is being used by Smartgear.
     */
    protected function isItemUsedBySmartgear(string $id): bool
    {
        try {
            /** @var CoreConfig */
            $config = $this->configManager->load();
            if ($config->getSgInstallComplete()
            && (
                CoreConfig::DEFAULT_CLIENT_FILES_FOLDER === $id
                || CoreConfig::DEFAULT_CLIENT_LOGOS_FOLDER === $id
                || $config->getSgOwnerLogo() === $id
            )
            ) {
                return true;
            }
            $blogConfig = $config->getSgBlog();
            if ($blogConfig->getSgInstallComplete() && $id === $blogConfig->getCurrentPreset()->getSgNewsFolder()) {
                return true;
            }
            $eventsConfig = $config->getSgEvents();
            if ($eventsConfig->getSgInstallComplete() && $id === $eventsConfig->getSgEventsFolder()) {
                return true;
            }
            $faqConfig = $config->getSgFaq();
            if ($faqConfig->getSgInstallComplete() && $id === $faqConfig->getSgFaqFolder()) {
                return true;
            }
            $extranetConfig = $config->getSgExtranet();
            if ($extranetConfig->getSgInstallComplete() && $id === $extranetConfig->getSgExtranetFolder()) {
                return true;
            }
        } catch (\Exception $e) {
        }

        return false;
    }
}
