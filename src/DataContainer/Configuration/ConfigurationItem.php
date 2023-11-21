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

namespace WEM\SmartgearBundle\DataContainer\Configuration;

use Contao\DataContainer;
use Contao\ModuleModel;
use Contao\PageModel;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Classes\Utils\ArticleUtil;
use WEM\SmartgearBundle\Classes\Utils\ContentUtil;
use WEM\SmartgearBundle\Classes\Utils\ModuleUtil;
use WEM\SmartgearBundle\Classes\Utils\PageUtil;
use WEM\SmartgearBundle\DataContainer\Core;
use WEM\SmartgearBundle\Model\Configuration\Configuration as ConfigurationModel;
use WEM\SmartgearBundle\Model\Configuration\ConfigurationItem as ConfigurationItemModel;

class ConfigurationItem extends Core
{
    public function __construct()
    {
        parent::__construct();
    }

    public function onsubmitCallback(DataContainer $dc): void
    {
        $objItem = ConfigurationItemModel::findOneById($dc->activeRecord->id);

        switch ($objItem->type) {
            case ConfigurationItemModel::TYPE_PAGE_LEGAL_NOTICE:
                $objItem = $this->managePageLegalNotice($objItem);
            break;
            case ConfigurationItemModel::TYPE_PAGE_PRIVACY_POLITICS:
                $objItem = $this->managePagePrivacyPolitics($objItem);
            break;
            case ConfigurationItemModel::TYPE_PAGE_SITEMAP:
                $objItem = $this->managePageSitemap($objItem);
            break;
        }

        $objItem->save();
    }

    public function managePageLegalNotice(ConfigurationItemModel $objItem): ConfigurationItemModel
    {
        if (!empty($objItem->page_name) && !empty($objItem->content_template)) {
            /** @var ConfigurationModel */
            $objConfiguration = $objItem->getRelated('pid');
            $objPage = null;
            if (!empty($objItem->contao_page)) {
                $objPage = PageModel::findByPk($objItem->contao_page);
                if ($objPage) {
                    PageUtil::emptyPage($objItem->contao_page);
                }
            }

            $objPage = PageUtil::createPageLegalNotice($objItem->page_name, (int) $objConfiguration->contao_page_root, $objItem->contao_page ? ['id' => $objItem->contao_page] : []);

            $objArticle = ArticleUtil::createArticle($objPage);

            $objContent = ContentUtil::createContent($objArticle, [
                'headline' => serialize([
                    'unit' => 'h1',
                    'value' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PageLegalNoticeHeadline'],
                ]),
                'text' => ContentUtil::buildContentLegalNotice($objItem->content_template, $objConfiguration),
            ]);

            $objItem->contao_page = $objPage->id;
        }

        return $objItem;
    }

    public function managePagePrivacyPolitics(ConfigurationItemModel $objItem): ConfigurationItemModel
    {
        if (!empty($objItem->page_name) && !empty($objItem->content_template)) {
            /** @var ConfigurationModel */
            $objConfiguration = $objItem->getRelated('pid');
            $objPage = null;
            if (!empty($objItem->contao_page)) {
                $objPage = PageModel::findByPk($objItem->contao_page);
                if ($objPage) {
                    PageUtil::emptyPage($objItem->contao_page);
                }
            }

            $objPage = PageUtil::createPagePrivacyPolitics($objItem->page_name, (int) $objConfiguration->contao_page_root, $objItem->contao_page ? ['id' => $objItem->contao_page] : []);

            $objArticle = ArticleUtil::createArticle($objPage);

            $pageLegalNoticeAbsoluteUrl = '';
            $configurationItemLegalNotices = ConfigurationItemModel::findItems(['pid' => $objItem->pid, 'type' => ConfigurationItemModel::TYPE_PAGE_LEGAL_NOTICE]);
            if ($configurationItemLegalNotices) {
                $objCILegalNotice = $configurationItemLegalNotices->current();
                /** @var PageModel */
                $objPageLegalNotice = $objCILegalNotice->getRelated('contao_page');
                if ($objPageLegalNotice) {
                    $pageLegalNoticeAbsoluteUrl = $objPageLegalNotice->getAbsoluteUrl();
                }
            }

            $objContent = ContentUtil::createContent($objArticle, [
                'text' => ContentUtil::buildContentPrivacyPolitics($objItem->content_template, $pageLegalNoticeAbsoluteUrl, $objConfiguration),
            ]);

            $objItem->contao_page = $objPage->id;
        }

        return $objItem;
    }

    public function managePageSitemap(ConfigurationItemModel $objItem): ConfigurationItemModel
    {
        if (!empty($objItem->page_name) && !empty($objItem->contao_module)) {
            /** @var ConfigurationModel */
            $objConfiguration = $objItem->getRelated('pid');
            $objPage = null;
            if (!empty($objItem->contao_page)) {
                $objPage = PageModel::findByPk($objItem->contao_page);
                if ($objPage) {
                    PageUtil::emptyPage($objItem->contao_page);
                }
            }

            $objPage = PageUtil::createPageSitemap($objItem->page_name, (int) $objConfiguration->contao_page_root, $objItem->contao_page ? ['id' => $objItem->contao_page] : []);

            $objArticle = ArticleUtil::createArticle($objPage);

            // $objModuleSitemap = $objConfiguration->getRelated('contao_module_sitemap');
            $objModuleSitemap = ModuleModel::findByPk($objItem->contao_module);
            if (!$objModuleSitemap) {
                $objModuleSitemap = ModuleUtil::createModuleSitemap((int) $objConfiguration->contao_theme, ['id' => $objItem->contao_module]);
            }

            ContentUtil::createContent($objArticle, [
                'headline' => serialize(['unit' => 'h1', 'value' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PageSitemapHeadline']]),
            ]);

            ContentUtil::createContent($objArticle, [
                'type' => 'module',
                'module' => $objModuleSitemap->id,
            ]);

            $objItem->contao_page = $objPage->id;
        }

        return $objItem;
    }

    public function contentTemplateOptionsCallback(DataContainer $dc): array
    {
        $arrOptions = [];

        switch ($dc->activeRecord->type) {
            case ConfigurationItemModel::TYPE_PAGE_LEGAL_NOTICE:
                $arrOptions = Util::getFileListByLanguages(Util::getPublicOrWebDirectory().'/bundles/wemsmartgear/examples/legal-notice');
            break;
            case ConfigurationItemModel::TYPE_PAGE_PRIVACY_POLITICS:
                $arrOptions = Util::getFileListByLanguages(Util::getPublicOrWebDirectory().'/bundles/wemsmartgear/examples/privacy-politics');
            break;
        }

        return ['-'] + $arrOptions;
    }
}
