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

DCAManipulator::create('tl_article')
    ->addConfigOnloadCallback('tl_wem_sg_article', 'checkPermission')
    ->setListOperationsDeleteButtonCallback('tl_wem_sg_article', 'deleteArticle')
;

/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @property News $News
 */
class tl_wem_sg_article extends tl_article
{
    /**
     * Check permissions to edit table tl_article.
     *
     * @throws AccessDeniedException
     */
    public function checkPermission(): void
    {
        parent::checkPermission();

        // Check current action
        switch (Input::get('act')) {
            case 'delete':
                if ($this->isArticleUsedBySmartgear((int) Input::get('id'))) {
                    throw new AccessDeniedException('Not enough permissions to '.Input::get('act').' article ID '.Input::get('id').'.');
                }
            break;
        }
    }

    /**
     * Return the delete article button.
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
    public function deleteArticle($row, $href, $label, $title, $icon, $attributes)
    {
        if ($this->isArticleUsedBySmartgear((int) $row['id'])) {
            return Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
        }

        return parent::deleteArticle($row, $href, $label, $title, $icon, $attributes);
    }

    /**
     * Check if the article is being used by Smartgear.
     *
     * @param int $id article's ID
     */
    protected function isArticleUsedBySmartgear(int $id): bool
    {
        $configManager = System::getContainer()->get('smartgear.config.manager.core');
        try {
            $config = $configManager->load();
            if ($config->getSgInstallComplete()
            && (
                $id === (int) $config->getSgArticleHome()
                || $id === (int) $config->getSgArticle404()
                || $id === (int) $config->getSgArticleLegalNotice()
                || $id === (int) $config->getSgArticlePrivacyPolitics()
                || $id === (int) $config->getSgArticleSitemap()
            )
            ) {
                return true;
            }
            $blogConfig = $config->getSgBlog();
            if ($blogConfig->getSgInstallComplete() && $id === (int) $blogConfig->getSgArticle()) {
                return true;
            }
            $eventsConfig = $config->getSgEvents();
            if ($eventsConfig->getSgInstallComplete() && $id === (int) $eventsConfig->getSgArticle()) {
                return true;
            }
        } catch (\Exception $e) {
        }

        return false;
    }
}
