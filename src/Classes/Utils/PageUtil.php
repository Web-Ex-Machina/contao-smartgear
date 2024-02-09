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

namespace WEM\SmartgearBundle\Classes\Utils;

use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\PageModel;
use Contao\System;
use InvalidArgumentException;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Model\Configuration\Configuration;

class PageUtil
{
    /**
     * Shortcut for page creation.
     */
    public static function createPage($strTitle, $intPid = 0, $arrData = []): PageModel
    {
        // Create the page
        if (\array_key_exists('id', $arrData)) {
            $objPage = PageModel::findOneById($arrData['id']);
            if (!$objPage) {
                throw new InvalidArgumentException('La page ayant pour id "'.$arrData['id'].'" n\'existe pas');
            }
        } else {
            $objPage = new PageModel();
        }
        $objPage->tstamp = time();
        $objPage->pid = $intPid;
        if (\array_key_exists('sorting', $arrData)) {
            $objPage->sorting = $arrData['sorting'];
        } elseif (0 !== $intPid) {
            $objPage->sorting = self::getNextAvailablePageSortingByParentPage((int) $intPid);
        } elseif (\array_key_exists('layout', $arrData)) {
            $objPage->sorting = self::getNextAvailablePageSortingByLayout((int) $arrData['layout']);
        }

        $objPage->title = $strTitle;
        // $objPage->alias = StringUtil::generateAlias($objPage->title);
        $objPage->type = 'regular';
        $objPage->pageTitle = $strTitle;
        $objPage->robots = 'index,follow';
        $objPage->sitemap = 'map_default';
        $objPage->published = 1;

        // Now we get the default values, get the arrData table
        if (!empty($arrData)) {
            foreach ($arrData as $k => $v) {
                $objPage->$k = $v;
            }
        }

        $objPage->save();

        \Contao\Controller::loadDataContainer(PageModel::getTable());
        $dc = new \Contao\DC_Table(PageModel::getTable());
        $dc->id = $objPage->id;
        $dc->activeRecord = $objPage;
        $alias = System::getContainer()
            ->get('contao.listener.data_container.page_url')
            ->generateAlias($arrData['alias'] ?? '', $dc)
        ;

        $objPage = PageModel::findById($objPage->id);
        $objPage->alias = $alias;
        $objPage->save();

        // Return the model
        return $objPage;
    }

    public static function createPageRoot(string $strTitle, string $adminEmail, int $layoutId, string $language, ?array $arrData = []): PageModel
    {
        $arrData = array_merge([
            'sorting' => 128,
            'type' => 'root',
            'language' => $language,
            'fallback' => 1,
            'adminEmail' => $adminEmail,
            'createSitemap' => 1,
            'sitemapName' => 'sitemap',
            'useSSL' => 1,
            'includeLayout' => 1,
            'layout' => $layoutId,
            // 'includeChmod' => 1,
            // 'cuser' => $users['webmaster']->id,
            // 'cgroup' => $groups['administrators']->id,
            'chmod' => Configuration::DEFAULT_ROOTPAGE_CHMOD,
            'robotsTxt' => SG_ROBOTSTXT_CONTENT_FULL,
        ], $arrData);

        return self::createPage($strTitle, 0, $arrData);
    }

    public static function createPageHome(string $strTitle, int $pid, ?array $arrData = []): PageModel
    {
        $arrData = array_merge([
            'sorting' => self::getNextAvailablePageSortingByParentPage((int) $pid),
            'alias' => 'index',
            'sitemap' => 'map_default',
            'hide' => 1,
        ], $arrData);

        return self::createPage($strTitle, $pid, $arrData);
    }

    public static function createPage404(string $strTitle, int $pid, ?array $arrData = []): PageModel
    {
        $arrData = array_merge([
            'sorting' => self::getNextAvailablePageSortingByParentPage((int) $pid),
            'sitemap' => 'map_default',
            'hide' => 1,
            'type' => 'error_404',
        ], $arrData);

        return self::createPage($strTitle, $pid, $arrData);
    }

    public static function createPageLegalNotice(string $strTitle, int $pid, ?array $arrData = []): PageModel
    {
        $arrData = array_merge([
            'sorting' => self::getNextAvailablePageSortingByParentPage((int) $pid),
            'sitemap' => 'map_default',
            'hide' => 1,
        ], $arrData);

        return self::createPage($strTitle, $pid, $arrData);
    }

    public static function createPagePrivacyPolitics(string $strTitle, int $pid, ?array $arrData = []): PageModel
    {
        $arrData = array_merge([
            'sorting' => self::getNextAvailablePageSortingByParentPage((int) $pid),
            'sitemap' => 'map_default',
            'hide' => 1,
        ], $arrData);

        return self::createPage($strTitle, $pid, $arrData);
    }

    public static function createPageSitemap(string $strTitle, int $pid, ?array $arrData = []): PageModel
    {
        $arrData = array_merge([
            'sorting' => self::getNextAvailablePageSortingByParentPage((int) $pid),
            'sitemap' => 'map_default',
            // 'description' => sprintf($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PageSitemapDescription'], $config->getSgWebsiteTitle()),
            'hide' => 1,
        ], $arrData);

        return self::createPage($strTitle, $pid, $arrData);
    }

    public static function createPageFaq(string $strTitle, int $pid, ?array $arrData = []): PageModel
    {
        $arrData = array_merge([
            'sorting' => self::getNextAvailablePageSortingByParentPage((int) $pid),
            // 'sitemap' => 'map_default',
            'robots' => 'index,follow',
            'type' => 'regular',
            'published' => 1,
        ], $arrData);

        return self::createPage($strTitle, $pid, $arrData);
    }

    public static function createPageEvents(string $strTitle, int $pid, ?array $arrData = []): PageModel
    {
        $arrData = array_merge([
            'sorting' => self::getNextAvailablePageSortingByParentPage((int) $pid),
            // 'layout' => $rootPage->layout,
            // 'title' => $eventsConfig->getSgPageTitle(),
            'robots' => 'index,follow',
            'type' => 'regular',
            'published' => 1,
        ], $arrData);

        return self::createPage($strTitle, $pid, $arrData);
    }

    public static function createPageBlog(string $strTitle, int $pid, ?array $arrData = []): PageModel
    {
        $arrData = array_merge([
            'sorting' => self::getNextAvailablePageSortingByParentPage((int) $pid),
            'robots' => 'index,follow',
            'type' => 'regular',
            'published' => 1,
            // 'description' => $this->translator->trans('WEMSG.BLOG.INSTALL_GENERAL.pageDescription', [$presetConfig->getSgPageTitle(), $config->getSgWebsiteTitle()], 'contao_default'),
        ], $arrData);

        return self::createPage($strTitle, $pid, $arrData);
    }

    public static function createPageFormContact(string $strTitle, int $pid, ?array $arrData = []): PageModel
    {
        $arrData = array_merge([
            'sorting' => self::getNextAvailablePageSortingByParentPage((int) $pid),
            'type' => 'regular',
            'robots' => 'index,follow',
            // 'description' => $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.pageFormDescription', [$formContactConfig->getSgPageTitle(), $config->getSgWebsiteTitle()], 'contao_default'),
            'published' => 1,
        ], $arrData);

        return self::createPage($strTitle, $pid, $arrData);
    }

    public static function createPageFormContactSent(string $strTitle, int $pid, ?array $arrData = []): PageModel
    {
        $arrData = array_merge([
            'sorting' => self::getNextAvailablePageSortingByParentPage((int) $pid),
            'type' => 'regular',
            'robots' => 'noindex,nofollow',
            // 'description' => $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.pageFormSentDescription', [$formContactConfig->getSgPageTitle(), $config->getSgWebsiteTitle()], 'contao_default'),
            'published' => 1,
            'hide' => 1,
        ], $arrData);

        return self::createPage($strTitle, $pid, $arrData);
    }

    /**
     * Shortcut for page w/ modules creations.
     */
    public static function createPageWithModules($strTitle, $arrModules, $intPid = 0, $arrPageData = [])
    {
        $arrConfig = Util::loadSmartgearConfig();
        if (0 === $intPid) {
            $intPid = $arrConfig['sgInstallRootPage'];
        }

        // Create the page
        $objPage = static::createPage($strTitle, $intPid, $arrPageData);

        // Create the article
        $objArticle = ArticleUtil::createArticle($objPage);

        // Create the contents
        foreach ($arrModules as $intModule) {
            $objContent = ContentUtil::createContent($objArticle, ['type' => 'module', 'module' => $intModule]);
        }

        // Return the page ID
        return $objPage->id;
    }

    /**
     * Shortcut for page w/ texts creations.
     *
     * @param mixed|null $arrHl
     */
    public static function createPageWithText($strTitle, $strText, $intPid = 0, $arrHl = null)
    {
        $arrConfig = Util::loadSmartgearConfig();
        if (0 === $intPid) {
            $intPid = $arrConfig['sgInstallRootPage'];
        }

        // Create the page
        $objPage = static::createPage($strTitle, $intPid);

        // Create the article
        $objArticle = ArticleUtil::createArticle($objPage);

        // Create the content
        $objContent = ContentUtil::createContent($objArticle, ['text' => $strText, 'headline' => $arrHl]);

        // Return the page ID
        return $objPage->id;
    }

    /**
     * Returns the next available page sorting for a page based on its parent page.
     *
     * @param int $parentPageId The page ID
     *
     * @return int The next available sorting
     */
    public static function getNextAvailablePageSortingByParentPage(int $parentPageId): int
    {
        $pidPage = PageModel::findById($parentPageId);
        if (!$pidPage) {
            return 128;
        }
        $pages = PageModel::findBy('pid', $parentPageId, ['order' => 'sorting DESC']);
        if (!$pages) {
            // return (int) $pidPage->sorting + 128;
            return 128;
        }
        $objPage = $pages->first()->current();

        return (int) $objPage->sorting + 128;
    }

    /**
     * Returns the next available sorting for a page based on its layout.
     *
     * @param int $layoutId The layout ID
     *
     * @return int The next available sorting
     */
    public static function getNextAvailablePageSortingByLayout(int $layoutId): int
    {
        $rootPage = PageModel::findBy(['layout = ?', 'type = ?'], [$layoutId, 'root']);
        if (!$rootPage) {
            return 128;
        }

        $pages = PageModel::findBy('pid', $rootPage, ['order' => 'sorting DESC']);
        if (!$pages) {
            // return (int) $rootPage->sorting + 128;
            return 128;
        }
        $objPage = $pages->first()->current();

        return (int) $objPage->sorting + 128;
    }

    public static function emptyPage(int $pageId): void
    {
        $articles = ArticleModel::findBy('pid', $pageId);
        if ($articles) {
            while ($articles->next()) {
                $contents = ContentModel::findBy(['ptable = ?', 'pid = ?'], [ArticleModel::getTable(), $articles->current()->id]);
                if ($contents) {
                    while ($contents->next()) {
                        $contents->delete();
                    }
                }

                $articles->current()->delete();
            }
        }
    }
}
