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

use Contao\Environment;
use Contao\Input;
use Contao\Model\Collection;
use Contao\Module;
use Contao\NewsModel;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Model\Configuration\ConfigurationItem;

class GenerateBreadcrumbListener
{
    public function __construct(protected CoreConfigurationManager $coreConfigurationManager)
    {
    }

    public function __invoke(array $items, Module $module): array
    {
        $arrSourceItems = $items;
        // $blogPageId = null;
        // try {
        //     /** @var CoreConfig $config */
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
            if ($objConfigurationItemBlog instanceof Collection) {
                // get the current tl_news
                $objArticle = NewsModel::findPublishedByParentAndIdOrAlias(Input::get('auto_item'), [$objConfigurationItemBlog->contao_news_archive]);
                if ($objArticle) {
                    $items[\count($items) - 1]['isActive'] = false;
                    $items[] = [
                        'isActive' => true,
                        'title' => $objArticle->headline,
                        'link' => $objArticle->headline,
                        'href' => Environment::get('uri'),
                    ];
                }
            }

            return $items;
        } catch (\Exception) {
            return $arrSourceItems;
        }
    }
}
