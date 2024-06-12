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

namespace WEM\SmartgearBundle\Backend\Component\Blog\EventListener;

use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Exceptions\File\NotFound as FileNotFoundException;
use WEM\SmartgearBundle\Model\Configuration\ConfigurationItem;

class GenerateBreadcrumbListener
{
    /** @var CoreConfigurationManager */
    protected $coreConfigurationManager;

    public function __construct(
        CoreConfigurationManager $coreConfigurationManager
    ) {
        $this->coreConfigurationManager = $coreConfigurationManager;
    }

    public function __invoke(array $items, \Contao\Module $module): array
    {
        $arrSourceItems = $items;
        // $blogPageId = null;
        // try {
        //     /** @var CoreConfig */
        //     $config = $this->coreConfigurationManager->load();
        //     $blogConfig = $config->getSgBlog();
        //     $blogPageId = $blogConfig->getSgInstallComplete() ? $blogConfig->getSgPage() : $blogPageId;
        // } catch (FileNotFoundException $e) {
        //     //nothing
        // }

        try {
            // Determine if we are at the root of the website
            global $objPage;

            $objConfigurationItemBlog = ConfigurationItem::findItems(['contao_page' => $objPage->id, 'type' => ConfigurationItem::TYPE_MIXED_BLOG], 1);

            // if ((int) $objPage->id === (int) $blogPageId) {
            if ($objConfigurationItemBlog) {
                // get the current tl_news
                $objArticle = \Contao\NewsModel::findPublishedByParentAndIdOrAlias(\Contao\Input::get('auto_item'), [$objConfigurationItemBlog->contao_news_archive]);
                if ($objArticle) {
                    $items[\count($items) - 1]['isActive'] = false;
                    $items[] = [
                        'isActive' => true,
                        'title' => $objArticle->headline,
                        'link' => $objArticle->headline,
                        'href' => \Contao\Environment::get('uri'),
                    ];
                }
            }

            return $items;
        } catch (\Exception) {
            return $arrSourceItems;
        }
    }
}
