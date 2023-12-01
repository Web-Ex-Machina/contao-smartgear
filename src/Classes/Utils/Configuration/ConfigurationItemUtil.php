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

namespace WEM\SmartgearBundle\Classes\Utils\Configuration;

use Contao\FormFieldModel;
use Contao\FormModel;
use Contao\ModuleModel;
use Contao\PageModel;
use WEM\SmartgearBundle\Classes\StringUtil;
use WEM\SmartgearBundle\Classes\Utils\ArticleUtil;
use WEM\SmartgearBundle\Classes\Utils\CalendarUtil;
use WEM\SmartgearBundle\Classes\Utils\ContentUtil;
use WEM\SmartgearBundle\Classes\Utils\FaqCategoryUtil;
use WEM\SmartgearBundle\Classes\Utils\FormFieldUtil;
use WEM\SmartgearBundle\Classes\Utils\FormUtil;
use WEM\SmartgearBundle\Classes\Utils\LayoutUtil;
use WEM\SmartgearBundle\Classes\Utils\ModuleUtil;
use WEM\SmartgearBundle\Classes\Utils\NewsArchiveUtil;
use WEM\SmartgearBundle\Classes\Utils\Notification\NcNotificationMessageLanguageUtil;
use WEM\SmartgearBundle\Classes\Utils\Notification\NcNotificationMessageUtil;
use WEM\SmartgearBundle\Classes\Utils\Notification\NcNotificationUtil;
use WEM\SmartgearBundle\Classes\Utils\PageUtil;
use WEM\SmartgearBundle\Classes\Utils\UserGroupUtil;
use WEM\SmartgearBundle\Model\Configuration\ConfigurationItem as ConfigurationItemModel;

class ConfigurationItemUtil
{
    public static function createEverythingFromConfigurationItem(ConfigurationItemModel $objItem, ?array $arrUpdates = [], ?int $tstamp = null): ConfigurationItemModel
    {
        switch ($objItem->type) {
            case ConfigurationItemModel::TYPE_PAGE_LEGAL_NOTICE:
                $objItem = self::managePageLegalNotice($objItem, (bool) $arrUpdates['update_page'], $tstamp);
                break;
            case ConfigurationItemModel::TYPE_PAGE_PRIVACY_POLITICS:
                $objItem = self::managePagePrivacyPolitics($objItem, (bool) $arrUpdates['update_page'], $tstamp);
                break;
            case ConfigurationItemModel::TYPE_PAGE_SITEMAP:
                $objItem = self::managePageSitemap($objItem, (bool) $arrUpdates['update_page'], $tstamp);
                break;
            case ConfigurationItemModel::TYPE_USER_GROUP_ADMINISTRATORS:
                $objItem = self::manageUserGroupAdministrators($objItem, (bool) $arrUpdates['update_user_group'], $tstamp);
                break;
            case ConfigurationItemModel::TYPE_USER_GROUP_REDACTORS:
                $objItem = self::manageUserGroupRedactors($objItem, (bool) $arrUpdates['update_user_group'], $tstamp);
                break;
            case ConfigurationItemModel::TYPE_MODULE_WEM_SG_HEADER:
                $objItem = self::manageModuleWemSgHeader($objItem, (bool) $arrUpdates['update_module'], $tstamp);
                break;
            case ConfigurationItemModel::TYPE_MODULE_WEM_SG_FOOTER:
                $objItem = self::manageModuleWemSgFooter($objItem, (bool) $arrUpdates['update_module'], $tstamp);
                break;
            case ConfigurationItemModel::TYPE_MODULE_BREADCRUMB:
                $objItem = self::manageModuleBreadcrumb($objItem, (bool) $arrUpdates['update_module'], $tstamp);
                break;
            case ConfigurationItemModel::TYPE_MODULE_WEM_SG_SOCIAL_NETWORKS:
                $objItem = self::manageModuleWemSgSocialNetworks($objItem, (bool) $arrUpdates['update_module'], $tstamp);
                break;
            case ConfigurationItemModel::TYPE_MIXED_SITEMAP:
                $objItem = self::manageMixedSitemap($objItem, (bool) $arrUpdates['update_module'], (bool) $arrUpdates['update_page'], $tstamp);
                break;
            case ConfigurationItemModel::TYPE_MIXED_FAQ:
                $objItem = self::manageMixedFaq($objItem, (bool) $arrUpdates['update_module'], (bool) $arrUpdates['update_page'], (bool) $arrUpdates['update_faq_category'], $tstamp);
                break;
            case ConfigurationItemModel::TYPE_MIXED_EVENTS:
                $objItem = self::manageMixedEvents($objItem, (bool) $arrUpdates['update_module_list'], (bool) $arrUpdates['update_module_reader'], (bool) $arrUpdates['update_module_calendar'], (bool) $arrUpdates['update_page'], (bool) $arrUpdates['update_calendar'], $tstamp);
                break;
            case ConfigurationItemModel::TYPE_MIXED_BLOG:
                $objItem = self::manageMixedBlog($objItem, (bool) $arrUpdates['update_module_list'], (bool) $arrUpdates['update_module_reader'], (bool) $arrUpdates['update_page'], (bool) $arrUpdates['update_news_archive'], $tstamp);
                break;
            case ConfigurationItemModel::TYPE_MIXED_FORM_CONTACT:
                $objItem = self::manageMixedFormContact($objItem, (bool) $arrUpdates['update_page_form'], (bool) $arrUpdates['update_page_form_sent'], (bool) $arrUpdates['update_form'], (bool) $arrUpdates['update_notification'], $tstamp);
                break;
        }

        if ((bool) $arrUpdates['update_user_group_permission']) {
            self::updateAddUserGroupSettingsAccordingToConfiguration($objItem, $tstamp);
        }

        $objItem->save();

        return $objItem;
    }

    public static function updateAddUserGroupSettingsAccordingToConfiguration(ConfigurationItemModel $objItem, ?int $tstamp = null): void
    {
        /** @var ConfigurationModel */
        // $objConfiguration = $objItem->getRelated('pid');

        $subConfigurationItems = ConfigurationItemModel::findItems(['pid' => $objItem->pid, 'type' => ConfigurationItemModel::TYPES_USER_GROUP]);
        if ($subConfigurationItems) {
            while ($subConfigurationItems->next()) {
                if ($objUserGroup = $subConfigurationItems->getRelated('contao_user_group')) {
                    UserGroupUtil::updateAddUserGroupSettingsAccordingToConfigurationItem($objUserGroup, $objItem);
                }
            }
        }
    }

    public static function managePageLegalNotice(ConfigurationItemModel $objItem, bool $blnForcePageUpdate, ?int $tstamp = null): ConfigurationItemModel
    {
        if (!empty($objItem->page_name) && !empty($objItem->content_template)
        && (0 === (int) $tstamp || $blnForcePageUpdate || (0 !== (int) $tstamp && empty($objItem->contao_page))) // create mode or forced update
        ) {
            /** @var ConfigurationModel */
            $objConfiguration = $objItem->getRelated('pid');
            $objPage = null;
            if (!empty($objItem->contao_page)) {
                $objPage = PageModel::findByPk($objItem->contao_page);
                if ($objPage) {
                    PageUtil::emptyPage((int) $objItem->contao_page);
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

    public static function managePagePrivacyPolitics(ConfigurationItemModel $objItem, bool $blnForcePageUpdate, ?int $tstamp = null): ConfigurationItemModel
    {
        if (!empty($objItem->page_name) && !empty($objItem->content_template)
        && (0 === (int) $tstamp || $blnForcePageUpdate || (0 !== (int) $tstamp && empty($objItem->contao_page))) // create mode or forced update
        ) {
            /** @var ConfigurationModel */
            $objConfiguration = $objItem->getRelated('pid');
            $objPage = null;
            if (!empty($objItem->contao_page)) {
                $objPage = PageModel::findByPk($objItem->contao_page);
                if ($objPage) {
                    PageUtil::emptyPage((int) $objItem->contao_page);
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

    public static function managePageSitemap(ConfigurationItemModel $objItem, bool $blnForcePageUpdate, ?int $tstamp = null): ConfigurationItemModel
    {
        // create + nothing => nothing
        // create + page name + no page => create page
        // create + page name + page => update page
        // create + no page name + page => nothing
        // update + nothing => nothing
        // update + page name + no page => create page
        // update + page name + page => update IF blnForcePageUpdate
        // update + no page name + page => nothing

        if (!empty($objItem->page_name)
        && !empty($objItem->contao_module) // should not be empty, as it must have been created beforehand if left empty in form
        && (0 === (int) $tstamp || $blnForcePageUpdate || (0 !== (int) $tstamp && empty($objItem->contao_page))) // create mode or forced update
        ) {
            /** @var ConfigurationModel */
            $objConfiguration = $objItem->getRelated('pid');
            $objPage = null;
            if (!empty($objItem->contao_page)) {
                $objPage = PageModel::findByPk($objItem->contao_page);
                if ($objPage) {
                    PageUtil::emptyPage((int) $objItem->contao_page);
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

    public static function managePageFaqCreate(ConfigurationItemModel $objItem, bool $blnForcePageUpdate, ?int $tstamp = null): ConfigurationItemModel
    {
        // create + nothing => nothing
        // create + page name + no page => create page
        // create + page name + page => update page
        // create + no page name + page => nothing
        // update + nothing => nothing
        // update + page name + no page => create page
        // update + page name + page => update IF blnForcePageUpdate
        // update + no page name + page => nothing

        if (!empty($objItem->page_name)
        && (0 === (int) $tstamp || $blnForcePageUpdate || (0 !== (int) $tstamp && empty($objItem->contao_page))) // create mode or forced update
        ) {
            /** @var ConfigurationModel */
            $objConfiguration = $objItem->getRelated('pid');
            $objPage = null;
            if (!empty($objItem->contao_page)) {
                $objPage = PageModel::findByPk($objItem->contao_page);
                if ($objPage) {
                    PageUtil::emptyPage((int) $objItem->contao_page);
                }
            }

            $objPage = PageUtil::createPageFaq($objItem->page_name, (int) $objConfiguration->contao_page_root, $objItem->contao_page ? ['id' => $objItem->contao_page] : []);

            $objItem->contao_page = $objPage->id;
        }

        return $objItem;
    }

    public static function managePageFaqFill(ConfigurationItemModel $objItem, bool $blnForcePageUpdate, ?int $tstamp = null): ConfigurationItemModel
    {
        // create + nothing => nothing
        // create + page name + no page => create page
        // create + page name + page => update page
        // create + no page name + page => nothing
        // update + nothing => nothing
        // update + page name + no page => create page
        // update + page name + page => update IF blnForcePageUpdate
        // update + no page name + page => nothing

        if (!empty($objItem->contao_page)
        && !empty($objItem->contao_module) // should not be empty, as it must have been created beforehand if left empty in form
        && (
            0 === (int) $tstamp
            || $blnForcePageUpdate
            || (
                0 !== (int) $tstamp
                && empty($objItem->contao_page))
            ) // create mode or forced update
        ) {
            /** @var ConfigurationModel */
            $objConfiguration = $objItem->getRelated('pid');
            $objPage = PageModel::findByPk($objItem->contao_page);

            $objArticle = ArticleUtil::createArticle($objPage);

            $objModuleFaq = ModuleModel::findByPk($objItem->contao_module);

            ContentUtil::createContent($objArticle, [
                'headline' => serialize(['unit' => 'h1', 'value' => $objItem->page_name]),
            ]);

            ContentUtil::createContent($objArticle, [
                'type' => 'module',
                'module' => $objModuleFaq->id,
            ]);
        }

        return $objItem;
    }

    public static function managePageEventsCreate(ConfigurationItemModel $objItem, bool $blnForcePageUpdate, ?int $tstamp = null): ConfigurationItemModel
    {
        // create + nothing => nothing
        // create + page name + no page => create page
        // create + page name + page => update page
        // create + no page name + page => nothing
        // update + nothing => nothing
        // update + page name + no page => create page
        // update + page name + page => update IF blnForcePageUpdate
        // update + no page name + page => nothing

        if (!empty($objItem->page_name)
        && (0 === (int) $tstamp || $blnForcePageUpdate || (0 !== (int) $tstamp && empty($objItem->contao_page))) // create mode or forced update
        ) {
            /** @var ConfigurationModel */
            $objConfiguration = $objItem->getRelated('pid');
            $objPage = null;
            if (!empty($objItem->contao_page)) {
                $objPage = PageModel::findByPk($objItem->contao_page);
                if ($objPage) {
                    PageUtil::emptyPage((int) $objItem->contao_page);
                }
            }

            $objPage = PageUtil::createPageEvents($objItem->page_name, (int) $objConfiguration->contao_page_root, $objItem->contao_page ? ['id' => $objItem->contao_page] : []);

            $objItem->contao_page = $objPage->id;
        }

        return $objItem;
    }

    public static function managePageEventsFill(ConfigurationItemModel $objItem, bool $blnForcePageUpdate, ?int $tstamp = null): ConfigurationItemModel
    {
        // create + nothing => nothing
        // create + page name + no page => create page
        // create + page name + page => update page
        // create + no page name + page => nothing
        // update + nothing => nothing
        // update + page name + no page => create page
        // update + page name + page => update IF blnForcePageUpdate
        // update + no page name + page => nothing

        if (!empty($objItem->contao_page)
        && !empty($objItem->contao_module_reader) // should not be empty, as it must have been created beforehand if left empty in form
        && !empty($objItem->contao_module_list) // should not be empty, as it must have been created beforehand if left empty in form
        && !empty($objItem->contao_module_calendar) // should not be empty, as it must have been created beforehand if left empty in form
        && (0 === (int) $tstamp || $blnForcePageUpdate || (0 !== (int) $tstamp && empty($objItem->contao_page))) // create mode or forced update
        ) {
            /** @var ConfigurationModel */
            $objConfiguration = $objItem->getRelated('pid');

            $objPage = PageModel::findByPk($objItem->contao_page);

            $objArticle = ArticleUtil::createArticle($objPage);

            $objModuleList = ModuleModel::findByPk($objItem->contao_module_list);

            ContentUtil::createContent($objArticle, [
                'headline' => serialize(['unit' => 'h1', 'value' => $objItem->page_name]),
            ]);

            ContentUtil::createContent($objArticle, [
                'type' => 'module',
                'module' => $objModuleList->id,
            ]);
        }

        return $objItem;
    }

    public static function managePageBlogCreate(ConfigurationItemModel $objItem, bool $blnForcePageUpdate, ?int $tstamp = null): ConfigurationItemModel
    {
        // create + nothing => nothing
        // create + page name + no page => create page
        // create + page name + page => update page
        // create + no page name + page => nothing
        // update + nothing => nothing
        // update + page name + no page => create page
        // update + page name + page => update IF blnForcePageUpdate
        // update + no page name + page => nothing

        if (!empty($objItem->page_name)
        && (0 === (int) $tstamp || $blnForcePageUpdate || (0 !== (int) $tstamp && empty($objItem->contao_page))) // create mode or forced update
        ) {
            /** @var ConfigurationModel */
            $objConfiguration = $objItem->getRelated('pid');
            $objPage = null;
            if (!empty($objItem->contao_page)) {
                $objPage = PageModel::findByPk($objItem->contao_page);
                if ($objPage) {
                    PageUtil::emptyPage((int) $objItem->contao_page);
                }
            }

            $objPage = PageUtil::createPageBlog($objItem->page_name, (int) $objConfiguration->contao_page_root, $objItem->contao_page ? ['id' => $objItem->contao_page] : []);

            $objItem->contao_page = $objPage->id;
        }

        return $objItem;
    }

    public static function managePageBlogFill(ConfigurationItemModel $objItem, bool $blnForcePageUpdate, ?int $tstamp = null): ConfigurationItemModel
    {
        // create + nothing => nothing
        // create + page name + no page => create page
        // create + page name + page => update page
        // create + no page name + page => nothing
        // update + nothing => nothing
        // update + page name + no page => create page
        // update + page name + page => update IF blnForcePageUpdate
        // update + no page name + page => nothing

        if (!empty($objItem->contao_page)
        && !empty($objItem->contao_module_reader) // should not be empty, as it must have been created beforehand if left empty in form
        && !empty($objItem->contao_module_list) // should not be empty, as it must have been created beforehand if left empty in form
        && (0 === (int) $tstamp || $blnForcePageUpdate || (0 !== (int) $tstamp && empty($objItem->contao_page))) // create mode or forced update
        ) {
            /** @var ConfigurationModel */
            $objConfiguration = $objItem->getRelated('pid');
            $objPage = PageModel::findByPk($objItem->contao_page);

            $objArticle = ArticleUtil::createArticle($objPage);

            $objModuleList = ModuleModel::findByPk($objItem->contao_module_list);

            ContentUtil::createContent($objArticle, [
                'headline' => serialize(['unit' => 'h1', 'value' => $objItem->page_name]),
            ]);

            ContentUtil::createContent($objArticle, [
                'type' => 'module',
                'module' => $objModuleList->id,
            ]);
        }

        return $objItem;
    }

    public static function managePageFormContactCreate(ConfigurationItemModel $objItem, bool $blnForcePageUpdate, ?int $tstamp = null): ConfigurationItemModel
    {
        // create + nothing => nothing
        // create + page name + no page => create page
        // create + page name + page => update page
        // create + no page name + page => nothing
        // update + nothing => nothing
        // update + page name + no page => create page
        // update + page name + page => update IF blnForcePageUpdate
        // update + no page name + page => nothing

        if (!empty($objItem->page_form_name)
        && (0 === (int) $tstamp || $blnForcePageUpdate || (0 !== (int) $tstamp && empty($objItem->contao_page_form))) // create mode or forced update
        ) {
            /** @var ConfigurationModel */
            $objConfiguration = $objItem->getRelated('pid');
            $objPage = null;
            if (!empty($objItem->contao_page_form)) {
                $objPage = PageModel::findByPk($objItem->contao_page_form);
                if ($objPage) {
                    PageUtil::emptyPage((int) $objItem->contao_page_form);
                }
            }

            $objPage = PageUtil::createPageFormContact($objItem->page_form_name, (int) $objConfiguration->contao_page_root, $objItem->contao_page_form ? ['id' => $objItem->contao_page_form] : []);

            $objItem->contao_page_form = $objPage->id;
        }

        return $objItem;
    }

    public static function managePageFormContactFill(ConfigurationItemModel $objItem, bool $blnForcePageUpdate, ?int $tstamp = null): ConfigurationItemModel
    {
        // create + nothing => nothing
        // create + page name + no page => create page
        // create + page name + page => update page
        // create + no page name + page => nothing
        // update + nothing => nothing
        // update + page name + no page => create page
        // update + page name + page => update IF blnForcePageUpdate
        // update + no page name + page => nothing

        if (!empty($objItem->contao_page_form)
        && !empty($objItem->contao_form) // should not be empty, as it must have been created beforehand if left empty in form
        && (0 === (int) $tstamp || $blnForcePageUpdate || (0 !== (int) $tstamp && empty($objItem->contao_page_form))) // create mode or forced update
        ) {
            /** @var ConfigurationModel */
            $objConfiguration = $objItem->getRelated('pid');
            $objPage = PageModel::findByPk($objItem->contao_page_form);

            $objArticle = ArticleUtil::createArticle($objPage);

            $objForm = FormModel::findByPk($objItem->contao_form);

            ContentUtil::createContent($objArticle, [
                'headline' => serialize(['unit' => 'h1', 'value' => $objItem->page_form_name]),
                'cssID' => ',sep-bottom',
            ]);

            ContentUtil::createContent($objArticle, [
                'type' => 'form',
                'form' => $objForm->id,
            ]);
        }

        return $objItem;
    }

    public static function managePageFormContactSentCreate(ConfigurationItemModel $objItem, bool $blnForcePageUpdate, ?int $tstamp = null): ConfigurationItemModel
    {
        // create + nothing => nothing
        // create + page name + no page => create page
        // create + page name + page => update page
        // create + no page name + page => nothing
        // update + nothing => nothing
        // update + page name + no page => create page
        // update + page name + page => update IF blnForcePageUpdate
        // update + no page name + page => nothing

        if (!empty($objItem->page_form_sent_name)
        && (0 === (int) $tstamp || $blnForcePageUpdate || (0 !== (int) $tstamp && empty($objItem->contao_page_form_sent))) // create mode or forced update
        ) {
            /** @var ConfigurationModel */
            $objConfiguration = $objItem->getRelated('pid');
            $objPage = null;
            if (!empty($objItem->contao_page_form_sent)) {
                $objPage = PageModel::findByPk($objItem->contao_page_form_sent);
                if ($objPage) {
                    PageUtil::emptyPage((int) $objItem->contao_page_form_sent);
                }
            }

            $objPage = PageUtil::createPageFormContactSent($objItem->page_form_sent_name, (int) $objConfiguration->contao_page_root, $objItem->contao_page_form_sent ? ['id' => $objItem->contao_page_form_sent] : []);

            $objItem->contao_page_form_sent = $objPage->id;
        }

        return $objItem;
    }

    public static function managePageFormContactSentFill(ConfigurationItemModel $objItem, bool $blnForcePageUpdate, ?int $tstamp = null): ConfigurationItemModel
    {
        // create + nothing => nothing
        // create + page name + no page => create page
        // create + page name + page => update page
        // create + no page name + page => nothing
        // update + nothing => nothing
        // update + page name + no page => create page
        // update + page name + page => update IF blnForcePageUpdate
        // update + no page name + page => nothing

        if (!empty($objItem->contao_page_form_sent)
        && (0 === (int) $tstamp || $blnForcePageUpdate || (0 !== (int) $tstamp && empty($objItem->contao_page_form_sent))) // create mode or forced update
        ) {
            /** @var ConfigurationModel */
            $objConfiguration = $objItem->getRelated('pid');
            $objPage = PageModel::findByPk($objItem->contao_page_form_sent);

            $objArticle = ArticleUtil::createArticle($objPage);

            ContentUtil::createContent($objArticle, [
                'headline' => serialize(['unit' => 'h1', 'value' => $objItem->page_form_sent_name]),
                'cssID' => ',sep-bottom',
            ]);

            ContentUtil::createContent($objArticle, [
                // 'text' => self::translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.contentTextPageFormSent', [], 'contao_default'),
                'text' => $GLOBALS['TL_LANG']['WEMSG']['FORMCONTACT']['INSTALL_GENERAL']['contentTextPageFormSent'],
            ]);
        }

        return $objItem;
    }

    public static function manageUserGroupAdministrators(ConfigurationItemModel $objItem, bool $blnForceUserGroupUpdate, ?int $tstamp = null): ConfigurationItemModel
    {
        if (!empty($objItem->user_group_name)
        && (0 === (int) $tstamp || $blnForceUserGroupUpdate || (0 !== (int) $tstamp && empty($objItem->contao_user_group))) // create mode or forced update
        ) {
            $objUserGroup = UserGroupUtil::createUserGroupAdministrators($objItem->user_group_name, $objItem->contao_user_group ? ['id' => $objItem->contao_user_group] : []);

            $objItem->contao_user_group = $objUserGroup->id;

            UserGroupUtil::updateAddUserGroupSettingsAccordingToConfiguration($objUserGroup, $objItem->getRelated('pid'));
        }

        return $objItem;
    }

    public static function manageUserGroupRedactors(ConfigurationItemModel $objItem, bool $blnForceUserGroupUpdate, ?int $tstamp = null): ConfigurationItemModel
    {
        if (!empty($objItem->user_group_name)
        && (0 === (int) $tstamp || $blnForceUserGroupUpdate || (0 !== (int) $tstamp && empty($objItem->contao_user_group))) // create mode or forced update
        ) {
            $objUserGroup = UserGroupUtil::createUserGroupRedactors($objItem->user_group_name, $objItem->contao_user_group ? ['id' => $objItem->contao_user_group] : []);

            $objItem->contao_user_group = $objUserGroup->id;

            UserGroupUtil::updateAddUserGroupSettingsAccordingToConfiguration($objUserGroup, $objItem->getRelated('pid'));
        }

        return $objItem;
    }

    public static function manageModuleWemSgHeader(ConfigurationItemModel $objItem, bool $blnForceModuleUpdate, ?int $tstamp = null): ConfigurationItemModel
    {
        if (!empty($objItem->module_name) && !empty($objItem->singleSRC)
        && (0 === (int) $tstamp || $blnForceModuleUpdate || (0 !== (int) $tstamp && empty($objItem->contao_module))) // create mode or forced update
        ) {
            /** @var ConfigurationModel */
            $objConfiguration = $objItem->getRelated('pid');

            // create the navigation module associated
            if (!empty($objItem->contao_module)) {
                // get the module
                $objModule = ModuleModel::findByPk($objItem->contao_module);
            }
            if ($objModule) {
                // get the nav module associated
                $objModuleNav = ModuleModel::findByPk($objModule->wem_sg_header_nav_module);
                $objModuleNav->name = $objItem->module_name.' - Nav';
                $objModuleNav->save();
            } else {
                // create the nav module
                $objModuleNav = ModuleUtil::createModuleNav($objConfiguration->contao_theme, ['name' => $objItem->module_name.' - Nav']);
            }

            $objModule = ModuleUtil::createModuleWemSgHeader(
                (int) $objConfiguration->contao_theme,
                (int) $objModuleNav->id,
                array_merge(
                    [
                        'name' => $objItem->module_name,
                        'singleSRC' => $objItem->singleSRC,
                    ],
                    $objItem->contao_module ? ['id' => $objItem->contao_module] : []
                )
            );
            $objItem->contao_module = $objModule->id;
        }

        // update selected layouts
        $contaoLayoutsToUpdate = [];
        if (\is_array($contao_layout_to_update)) {
            $contaoLayoutsToUpdate = $contao_layout_to_update;
        } else {
            $contaoLayoutsToUpdate = StringUtil::deserialize($objItem->contao_layout_to_update, true);
        }
        $contaoLayoutsToUpdate = StringUtil::deserialize($objItem->contao_layout_to_update, true);
        foreach ($contaoLayoutsToUpdate as $layoutId) {
            LayoutUtil::replaceHeader((int) $layoutId, (int) $objItem->contao_module);
        }

        return $objItem;
    }

    public static function manageModuleWemSgFooter(ConfigurationItemModel $objItem, bool $blnForceModuleUpdate, ?int $tstamp = null): ConfigurationItemModel
    {
        if (!empty($objItem->module_name) && !empty($objItem->content_template)
        && (0 === (int) $tstamp || $blnForceModuleUpdate || (0 !== (int) $tstamp && empty($objItem->contao_module))) // create mode or forced update
        ) {
            /** @var ConfigurationModel */
            $objConfiguration = $objItem->getRelated('pid');

            $objModule = ModuleUtil::createModuleWemSgFooter(
                (int) $objConfiguration->contao_theme,
                array_merge(
                    [
                        'name' => $objItem->module_name,
                        'html' => ContentUtil::buildContentWemSgFooter($objItem->content_template),
                    ],
                    $objItem->contao_module ? ['id' => $objItem->contao_module] : []
                )
            );
            $objItem->contao_module = $objModule->id;
        }

        // update selected layouts
        $contaoLayoutsToUpdate = [];
        if (\is_array($contao_layout_to_update)) {
            $contaoLayoutsToUpdate = $contao_layout_to_update;
        } else {
            $contaoLayoutsToUpdate = StringUtil::deserialize($objItem->contao_layout_to_update, true);
        }
        foreach ($contaoLayoutsToUpdate as $layoutId) {
            LayoutUtil::replaceFooter((int) $layoutId, (int) $objItem->contao_module);
        }

        return $objItem;
    }

    public static function manageModuleBreadcrumb(ConfigurationItemModel $objItem, bool $blnForceModuleUpdate, ?int $tstamp = null): ConfigurationItemModel
    {
        if (!empty($objItem->module_name)
        && (0 === (int) $tstamp || $blnForceModuleUpdate || (0 !== (int) $tstamp && empty($objItem->contao_module))) // create mode or forced update
        ) {
            /** @var ConfigurationModel */
            $objConfiguration = $objItem->getRelated('pid');

            $objModule = ModuleUtil::createModuleBreadcrumb(
                (int) $objConfiguration->contao_theme,
                array_merge(
                    [
                        'name' => $objItem->module_name,
                    ],
                    $objItem->contao_module ? ['id' => $objItem->contao_module] : []
                )
            );
            $objItem->contao_module = $objModule->id;
        }

        // update selected layouts
        $contaoLayoutsToUpdate = [];
        if (\is_array($contao_layout_to_update)) {
            $contaoLayoutsToUpdate = $contao_layout_to_update;
        } else {
            $contaoLayoutsToUpdate = StringUtil::deserialize($objItem->contao_layout_to_update, true);
        }
        foreach ($contaoLayoutsToUpdate as $layoutId) {
            LayoutUtil::replaceBreadcrumb((int) $layoutId, (int) $objItem->contao_module);
        }

        return $objItem;
    }

    public static function manageModuleWemSgSocialNetworks(ConfigurationItemModel $objItem, bool $blnForceModuleUpdate, ?int $tstamp = null): ConfigurationItemModel
    {
        if (!empty($objItem->module_name)
        && (0 === (int) $tstamp || $blnForceModuleUpdate || (0 !== (int) $tstamp && empty($objItem->contao_module))) // create mode or forced update
        ) {
            /** @var ConfigurationModel */
            $objConfiguration = $objItem->getRelated('pid');

            $objModule = ModuleUtil::createModuleWemSgSocialLink(
                (int) $objConfiguration->contao_theme,
                array_merge(
                    [
                        'name' => $objItem->module_name,
                    ],
                    $objItem->contao_module ? ['id' => $objItem->contao_module] : []
                )
            );
            $objItem->contao_module = $objModule->id;
        }

        return $objItem;
    }

    public static function manageModuleSitemap(ConfigurationItemModel $objItem, bool $blnForceModuleUpdate, ?int $tstamp = null): ConfigurationItemModel
    {
        if (!empty($objItem->module_name)
        && (0 === (int) $tstamp || $blnForceModuleUpdate || (0 !== (int) $tstamp && empty($objItem->contao_module))) // create mode or forced update
        ) {
            /** @var ConfigurationModel */
            $objConfiguration = $objItem->getRelated('pid');

            $objModule = ModuleUtil::createModuleSitemap(
                (int) $objConfiguration->contao_theme,
                array_merge(
                    [
                        'name' => $objItem->module_name,
                    ],
                    $objItem->contao_module ? ['id' => $objItem->contao_module] : []
                )
            );
            $objItem->contao_module = $objModule->id;
        }

        return $objItem;
    }

    public static function manageModuleFaq(ConfigurationItemModel $objItem, bool $blnForceModuleUpdate, ?int $tstamp = null): ConfigurationItemModel
    {
        if (!empty($objItem->module_name)
        && (0 === (int) $tstamp || $blnForceModuleUpdate || (0 !== (int) $tstamp && empty($objItem->contao_module))) // create mode or forced update
        ) {
            /** @var ConfigurationModel */
            $objConfiguration = $objItem->getRelated('pid');

            $objModule = ModuleUtil::createModuleFaq(
                (int) $objConfiguration->contao_theme,
                (int) $objItem->contao_faq_category,
                array_merge(
                    [
                        'name' => $objItem->module_name,
                    ],
                    $objItem->contao_module ? ['id' => $objItem->contao_module] : []
                )
            );
            $objItem->contao_module = $objModule->id;
        }

        return $objItem;
    }

    public static function manageModuleEventsList(ConfigurationItemModel $objItem, bool $blnForceModuleUpdate, ?int $tstamp = null): ConfigurationItemModel
    {
        if (!empty($objItem->module_list_name)
        && (0 === (int) $tstamp || $blnForceModuleUpdate || (0 !== (int) $tstamp && empty($objItem->contao_module_list))) // create mode or forced update
        ) {
            /** @var ConfigurationModel */
            $objConfiguration = $objItem->getRelated('pid');

            $objModule = ModuleUtil::createModuleEventsList(
                (int) $objConfiguration->contao_theme,
                (int) $objItem->contao_calendar,
                (int) $objItem->contao_module_reader,
                array_merge(
                    [
                        'name' => $objItem->module_list_name,
                        'perPage' => $objItem->module_list_perPage,
                    ],
                    $objItem->contao_module_list ? ['id' => $objItem->contao_module_list] : []
                )
            );
            $objItem->contao_module_list = $objModule->id;
        }

        return $objItem;
    }

    public static function manageModuleEventsReader(ConfigurationItemModel $objItem, bool $blnForceModuleUpdate, ?int $tstamp = null): ConfigurationItemModel
    {
        if (!empty($objItem->module_reader_name)
        && (0 === (int) $tstamp || $blnForceModuleUpdate || (0 !== (int) $tstamp && empty($objItem->contao_module_reader))) // create mode or forced update
        ) {
            /** @var ConfigurationModel */
            $objConfiguration = $objItem->getRelated('pid');

            $objModule = ModuleUtil::createModuleEventsReader(
                (int) $objConfiguration->contao_theme,
                (int) $objItem->contao_calendar,
                array_merge(
                    [
                        'name' => $objItem->module_reader_name,
                    ],
                    $objItem->contao_module_reader ? ['id' => $objItem->contao_module_reader] : []
                )
            );
            $objItem->contao_module_reader = $objModule->id;
        }

        return $objItem;
    }

    public static function manageModuleEventsCalendar(ConfigurationItemModel $objItem, bool $blnForceModuleUpdate, ?int $tstamp = null): ConfigurationItemModel
    {
        if (!empty($objItem->module_calendar_name)
        && (0 === (int) $tstamp || $blnForceModuleUpdate || (0 !== (int) $tstamp && empty($objItem->contao_module_calendar))) // create mode or forced update
        ) {
            /** @var ConfigurationModel */
            $objConfiguration = $objItem->getRelated('pid');

            $objModule = ModuleUtil::createModuleEventsCalendar(
                (int) $objConfiguration->contao_theme,
                (int) $objItem->contao_calendar,
                (int) $objItem->contao_module_reader,
                array_merge(
                    [
                        'name' => $objItem->module_calendar_name,
                    ],
                    $objItem->contao_module_calendar ? ['id' => $objItem->contao_module_calendar] : []
                )
            );
            $objItem->contao_module_calendar = $objModule->id;
        }

        return $objItem;
    }

    public static function manageModuleBlogList(ConfigurationItemModel $objItem, bool $blnForceModuleUpdate, ?int $tstamp = null): ConfigurationItemModel
    {
        if (!empty($objItem->module_list_name)
        && (0 === (int) $tstamp || $blnForceModuleUpdate || (0 !== (int) $tstamp && empty($objItem->contao_module_list))) // create mode or forced update
        ) {
            /** @var ConfigurationModel */
            $objConfiguration = $objItem->getRelated('pid');

            $objModule = ModuleUtil::createModuleBlogList(
                (int) $objConfiguration->contao_theme,
                (int) $objItem->contao_news_archive,
                (int) $objItem->contao_module_reader,
                array_merge(
                    [
                        'name' => $objItem->module_list_name,
                        'perPage' => $objItem->module_list_perPage,
                    ],
                    $objItem->contao_module_list ? ['id' => $objItem->contao_module_list] : []
                )
            );
            $objItem->contao_module_list = $objModule->id;
        }

        return $objItem;
    }

    public static function manageModuleBlogReader(ConfigurationItemModel $objItem, bool $blnForceModuleUpdate, ?int $tstamp = null): ConfigurationItemModel
    {
        if (!empty($objItem->module_reader_name)
        && (0 === (int) $tstamp || $blnForceModuleUpdate || (0 !== (int) $tstamp && empty($objItem->contao_module_reader))) // create mode or forced update
        ) {
            /** @var ConfigurationModel */
            $objConfiguration = $objItem->getRelated('pid');

            $objModule = ModuleUtil::createModuleBlogReader(
                (int) $objConfiguration->contao_theme,
                (int) $objItem->contao_news_archive,
                array_merge(
                    [
                        'name' => $objItem->module_reader_name,
                    ],
                    $objItem->contao_module_reader ? ['id' => $objItem->contao_module_reader] : []
                )
            );
            $objItem->contao_module_reader = $objModule->id;
        }

        return $objItem;
    }

    public static function manageFaqCategory(ConfigurationItemModel $objItem, bool $blnForceFaqUpdate, ?int $tstamp = null): ConfigurationItemModel
    {
        if (!empty($objItem->faq_category_name)
        && (0 === (int) $tstamp || $blnForceFaqUpdate || (0 !== (int) $tstamp && empty($objItem->contao_faq_category))) // create mode or forced update
        ) {
            /** @var ConfigurationModel */
            $objConfiguration = $objItem->getRelated('pid');

            // $arrGroups = [];

            // $UGAs = ConfigurationItemModel::findItems(['pid' => $objItem->pid, 'type' => ConfigurationItemModel::TYPE_USER_GROUP_ADMINISTRATORS]);
            // if ($UGAs) {
            //     while ($UGAs->next()) {
            //         $arrGroups[] = $UGAs->id;
            //     }
            // }
            // $UGRs = ConfigurationItemModel::findItems(['pid' => $objItem->pid, 'type' => ConfigurationItemModel::TYPE_USER_GROUP_REDACTORS]);
            // if ($UGRs) {
            //     while ($UGRs->next()) {
            //         $arrGroups[] = $UGRs->id;
            //     }
            // }

            $objFaqCategory = FaqCategoryUtil::createFaqCategory(
                $objItem->faq_category_name,
                (int) $objItem->contao_page,
                array_merge(
                    // !empty($arrGroups) ? ['groups' => serialize($arrGroups)] : [],
                    $objItem->contao_faq_category ? ['id' => $objItem->contao_faq_category] : []
                )
            );
            $objItem->contao_faq_category = $objFaqCategory->id;
        }

        return $objItem;
    }

    public static function manageCalendar(ConfigurationItemModel $objItem, bool $blnForceCalUpdate, ?int $tstamp = null): ConfigurationItemModel
    {
        if (!empty($objItem->calendar_name)
        && (0 === (int) $tstamp || $blnForceCalUpdate || (0 !== (int) $tstamp && empty($objItem->contao_calendar))) // create mode or forced update
        ) {
            /** @var ConfigurationModel */
            $objConfiguration = $objItem->getRelated('pid');

            // $arrGroups = [];

            // $UGAs = ConfigurationItemModel::findItems(['pid' => $objItem->pid, 'type' => ConfigurationItemModel::TYPE_USER_GROUP_ADMINISTRATORS]);
            // if ($UGAs) {
            //     while ($UGAs->next()) {
            //         $arrGroups[] = $UGAs->id;
            //     }
            // }
            // $UGRs = ConfigurationItemModel::findItems(['pid' => $objItem->pid, 'type' => ConfigurationItemModel::TYPE_USER_GROUP_REDACTORS]);
            // if ($UGRs) {
            //     while ($UGRs->next()) {
            //         $arrGroups[] = $UGRs->id;
            //     }
            // }

            $objCalFee = CalendarUtil::createCalendar(
                $objItem->calendar_name,
                (int) $objItem->contao_page,
                array_merge(
                    // !empty($arrGroups) ? ['groups' => serialize($arrGroups)] : [],
                    $objItem->contao_calendar ? ['id' => $objItem->contao_calendar] : []
                )
            );
            $objItem->contao_calendar = $objCalFee->id;
        }

        return $objItem;
    }

    public static function manageNewsArchive(ConfigurationItemModel $objItem, bool $blnForceNewsArchiveUpdate, ?int $tstamp = null): ConfigurationItemModel
    {
        if (!empty($objItem->news_archive_name)
        && (0 === (int) $tstamp || $blnForceNewsArchiveUpdate || (0 !== (int) $tstamp && empty($objItem->contao_news_archive))) // create mode or forced update
        ) {
            /** @var ConfigurationModel */
            $objConfiguration = $objItem->getRelated('pid');

            // $arrGroups = [];

            // $UGAs = ConfigurationItemModel::findItems(['pid' => $objItem->pid, 'type' => ConfigurationItemModel::TYPE_USER_GROUP_ADMINISTRATORS]);
            // if ($UGAs) {
            //     while ($UGAs->next()) {
            //         $arrGroups[] = $UGAs->id;
            //     }
            // }
            // $UGRs = ConfigurationItemModel::findItems(['pid' => $objItem->pid, 'type' => ConfigurationItemModel::TYPE_USER_GROUP_REDACTORS]);
            // if ($UGRs) {
            //     while ($UGRs->next()) {
            //         $arrGroups[] = $UGRs->id;
            //     }
            // }

            $objCalFee = NewsArchiveUtil::createNewsArchive(
                $objItem->news_archive_name,
                (int) $objItem->contao_page,
                array_merge(
                    // !empty($arrGroups) ? ['groups' => serialize($arrGroups)] : [],
                    $objItem->contao_news_archive ? ['id' => $objItem->contao_news_archive] : []
                )
            );
            $objItem->contao_news_archive = $objCalFee->id;
        }

        return $objItem;
    }

    public static function manageFormFormContact(ConfigurationItemModel $objItem, bool $blnForceFormUpdate, ?int $tstamp = null): ConfigurationItemModel
    {
        if (!empty($objItem->form_name)
        && (0 === (int) $tstamp || $blnForceFormUpdate || (0 !== (int) $tstamp && empty($objItem->contao_form))) // create mode or forced update
        ) {
            /** @var ConfigurationModel */
            $objConfiguration = $objItem->getRelated('pid');

            // $arrGroups = [];

            // $UGAs = ConfigurationItemModel::findItems(['pid' => $objItem->pid, 'type' => ConfigurationItemModel::TYPE_USER_GROUP_ADMINISTRATORS]);
            // if ($UGAs) {
            //     while ($UGAs->next()) {
            //         $arrGroups[] = $UGAs->id;
            //     }
            // }
            // $UGRs = ConfigurationItemModel::findItems(['pid' => $objItem->pid, 'type' => ConfigurationItemModel::TYPE_USER_GROUP_REDACTORS]);
            // if ($UGRs) {
            //     while ($UGRs->next()) {
            //         $arrGroups[] = $UGRs->id;
            //     }
            // }

            $objForm = FormUtil::createFormFormContact(
                $objItem->form_name,
                (int) $objItem->contao_page_form_sent,
                (int) $objItem->contao_notification,
                array_merge(
                    ['storeViaFormDataManager' => true],
                    $objItem->contao_form ? ['id' => $objItem->contao_form] : []
                )
            );

            //empty form ?
            //fill it with fields
            FormFieldUtil::createFormField((int) $objForm->id, array_merge([
                'sorting' => 128,
                'type' => 'text',
                'name' => 'name',
                'label' => $GLOBALS['TL_LANG']['WEMSG']['FORMCONTACT']['INSTALL_GENERAL']['labelFormInputName'],
                'placeholder' => $GLOBALS['TL_LANG']['WEMSG']['FORMCONTACT']['INSTALL_GENERAL']['placeholderFormInputName'],
                'mandatory' => 1,
                'contains_personal_data' => true,
            ],
            ($objFF = FormFieldModel::findOneBy(['pid = ?', 'name = ?'], [$objForm->id, 'name'])) ? ['id' => $objFF->id] : []
            ));

            FormFieldUtil::createFormField((int) $objForm->id, array_merge([
                'sorting' => 256,
                'type' => 'text',
                'name' => 'email',
                'label' => $GLOBALS['TL_LANG']['WEMSG']['FORMCONTACT']['INSTALL_GENERAL']['labelFormInputEmail'],
                'placeholder' => $GLOBALS['TL_LANG']['WEMSG']['FORMCONTACT']['INSTALL_GENERAL']['placeholderFormInputEmail'],
                'mandatory' => 1,
                'rgxp' => 'email',
                'tstamp' => time(),
            ],
            ($objFF = FormFieldModel::findOneBy(['pid = ?', 'name = ?'], [$objForm->id, 'email'])) ? ['id' => $objFF->id] : []
            ));

            FormFieldUtil::createFormField((int) $objForm->id, array_merge([
                'sorting' => 384,
                'type' => 'textarea',
                'name' => 'message',
                'label' => $GLOBALS['TL_LANG']['WEMSG']['FORMCONTACT']['INSTALL_GENERAL']['labelFormInputMessage'],
                'placeholder' => $GLOBALS['TL_LANG']['WEMSG']['FORMCONTACT']['INSTALL_GENERAL']['placeholderFormInputMessage'],
                'mandatory' => 1,
                'contains_personal_data' => true,
            ],
            ($objFF = FormFieldModel::findOneBy(['pid = ?', 'name = ?'], [$objForm->id, 'message'])) ? ['id' => $objFF->id] : []
            ));

            FormFieldUtil::createFormField((int) $objForm->id, array_merge([
                'sorting' => 512,
                'type' => 'checkbox',
                'name' => 'consent_data_treatment',
                'options' => serialize([['value' => 1, 'label' => $GLOBALS['TL_LANG']['WEMSG']['FORMCONTACT']['INSTALL_GENERAL']['optionLabelFormInputConsentDataTreatment']]]),
                'mandatory' => true,
            ],
            ($objFF = FormFieldModel::findOneBy(['pid = ?', 'name = ?'], [$objForm->id, 'consent_data_treatment'])) ? ['id' => $objFF->id] : []
            ));

            FormFieldUtil::createFormField((int) $objForm->id, array_merge([
                'sorting' => 896,
                'type' => 'checkbox',
                'name' => 'consent_data_save',
                'options' => serialize([['value' => 1, 'label' => $GLOBALS['TL_LANG']['WEMSG']['FORMCONTACT']['INSTALL_GENERAL']['optionLabelFormInputConsentDataSave']]]),
                'mandatory' => 1,
                // 'invisible' => !$config->getSgFormDataManager()->getSgInstallComplete(),
                'invisible' => false,
            ],
            ($objFF = FormFieldModel::findOneBy(['pid = ?', 'name = ?'], [$objForm->id, 'consent_data_save'])) ? ['id' => $objFF->id] : []
            ));

            FormFieldUtil::createFormField((int) $objForm->id, array_merge([
                'sorting' => 1152,
                'type' => 'captcha',
                'name' => 'captcha',
                'label' => $GLOBALS['TL_LANG']['WEMSG']['FORMCONTACT']['INSTALL_GENERAL']['labelFormInputCaptcha'],
                'mandatory' => 1,
            ],
            ($objFF = FormFieldModel::findOneBy(['pid = ?', 'name = ?'], [$objForm->id, 'captcha'])) ? ['id' => $objFF->id] : []
            ));

            FormFieldUtil::createFormField((int) $objForm->id, array_merge([
                'sorting' => 1280,
                'type' => 'submit',
                'name' => 'submit',
                'slabel' => $GLOBALS['TL_LANG']['WEMSG']['FORMCONTACT']['INSTALL_GENERAL']['labelFormInputSubmit'],
                'mandatory' => 1,
            ],
            ($objFF = FormFieldModel::findOneBy(['pid = ?', 'name = ?'], [$objForm->id, 'submit'])) ? ['id' => $objFF->id] : []
            ));

            $objItem->contao_form = $objForm->id;
        }

        return $objItem;
    }

    public static function manageNotificationFormContactSent(ConfigurationItemModel $objItem, bool $blnForceNotificationUpdate, ?int $tstamp = null): ConfigurationItemModel
    {
        if (!empty($objItem->notification_name)
        && (0 === (int) $tstamp || $blnForceNotificationUpdate || (0 !== (int) $tstamp && empty($objItem->contao_notification))) // create mode or forced update
        ) {
            /** @var ConfigurationModel */
            $objConfiguration = $objItem->getRelated('pid');
            if ($objConfiguration->email_gateway) {
                $objNotification = NcNotificationUtil::createFormContactSentNotification(
                $objItem->notification_name
            );

                $objMessageUser = NcNotificationMessageUtil::createContactFormSentNotificationMessageUser(
                (int) $objConfiguration->email_gateway,
                $objNotification->type,
                (int) $objNotification->id,
                []
            );

                $objMessageAdmin = NcNotificationMessageUtil::createContactFormSentNotificationMessageAdmin(
                (int) $objConfiguration->email_gateway,
                $objNotification->type,
                (int) $objNotification->id,
                []
            );

                $objMessageUserLanguage = NcNotificationMessageLanguageUtil::createContactFormSentNotificationMessageUserLanguage(
                (int) $objMessageUser->id,
                $objItem->form_name,
                $objConfiguration->title,
                $objConfiguration->language,
                true,
                []
            );

                $objMessageAdminLanguage = NcNotificationMessageLanguageUtil::createContactFormSentNotificationMessageAdminLanguage(
                (int) $objMessageAdmin->id,
                $objItem->form_name,
                $objConfiguration->title,
                $objConfiguration->legal_owner_email,
                $objConfiguration->language,
                true,
                []
            );

                $objItem->contao_notification = $objNotification->id;
            }
        }

        return $objItem;
    }

    public static function manageMixedSitemap(ConfigurationItemModel $objItem, bool $blnForceModuleUpdate, bool $blnForcePageUpdate, ?int $tstamp = null): ConfigurationItemModel
    {
        $objItem = self::manageModuleSitemap($objItem, $blnForceModuleUpdate, $tstamp);

        return self::managePageSitemap($objItem, $blnForcePageUpdate, $tstamp);
    }

    public static function manageMixedFaq(ConfigurationItemModel $objItem, bool $blnForceModuleUpdate, bool $blnForcePageUpdate, bool $blnForceFAQUpdate, ?int $tstamp = null): ConfigurationItemModel
    {
        $oldPageId = $objItem->contao_page;
        $objItem = self::managePageFaqCreate($objItem, $blnForcePageUpdate, $tstamp);
        $objItem = self::manageFaqCategory($objItem, $blnForceFAQUpdate, $tstamp);
        $objItem = self::manageModuleFaq($objItem, $blnForceModuleUpdate, $tstamp);

        return self::managePageFaqFill($objItem, $blnForcePageUpdate || (int) $oldPageId !== (int) $objItem->contao_page, $tstamp);
    }

    public static function manageMixedEvents(ConfigurationItemModel $objItem, bool $blnForceModuleListUpdate, bool $blnForceModuleReaderUpdate, bool $blnForceModuleCalendarUpdate, bool $blnForcePageUpdate, bool $blnForceCalendarUpdate, ?int $tstamp = null): ConfigurationItemModel
    {
        $oldPageId = $objItem->contao_page;
        $objItem = self::managePageEventsCreate($objItem, $blnForcePageUpdate, $tstamp);
        $objItem = self::manageCalendar($objItem, $blnForceCalendarUpdate, $tstamp);
        $objItem = self::manageModuleEventsReader($objItem, $blnForceModuleReaderUpdate, $tstamp);
        $objItem = self::manageModuleEventsList($objItem, $blnForceModuleListUpdate, $tstamp);
        $objItem = self::manageModuleEventsCalendar($objItem, $blnForceModuleCalendarUpdate, $tstamp);

        return self::managePageEventsFill($objItem, $blnForcePageUpdate || (int) $oldPageId !== (int) $objItem->contao_page, $tstamp);
    }

    public static function manageMixedBlog(ConfigurationItemModel $objItem, bool $blnForceModuleListUpdate, bool $blnForceModuleReaderUpdate, bool $blnForcePageUpdate, bool $blnForceNewsArchiveUpdate, ?int $tstamp = null): ConfigurationItemModel
    {
        $oldPageId = $objItem->contao_page;
        $objItem = self::managePageBlogCreate($objItem, $blnForcePageUpdate, $tstamp);
        $objItem = self::manageNewsArchive($objItem, $blnForceNewsArchiveUpdate, $tstamp);
        $objItem = self::manageModuleBlogReader($objItem, $blnForceModuleReaderUpdate, $tstamp);
        $objItem = self::manageModuleBlogList($objItem, $blnForceModuleListUpdate, $tstamp);

        return self::managePageBlogFill($objItem, $blnForcePageUpdate || (int) $oldPageId !== (int) $objItem->contao_page, $tstamp);
    }

    public static function manageMixedFormContact(ConfigurationItemModel $objItem, bool $blnForcePageFormUpdate, bool $blnForcePageFormSentUpdate, bool $blnForceFormUpdate, bool $blnForceNotificationUpdate, ?int $tstamp = null): ConfigurationItemModel
    {
        $oldPageId = $objItem->contao_page;
        $objItem = self::managePageFormContactCreate($objItem, $blnForcePageFormUpdate, $tstamp);
        $objItem = self::managePageFormContactSentCreate($objItem, $blnForcePageFormSentUpdate, $tstamp);
        $objItem = self::manageFormFormContact($objItem, $blnForceFormUpdate, $tstamp);
        $objItem = self::manageNotificationFormContactSent($objItem, $blnForceNotificationUpdate, $tstamp);

        $objItem = self::managePageFormContactFill($objItem, $blnForcePageFormUpdate || (int) $oldPageId !== (int) $objItem->contao_page, $tstamp);

        return self::managePageFormContactSentFill($objItem, $blnForcePageFormSentUpdate || (int) $oldPageId !== (int) $objItem->contao_page, $tstamp);
    }
}
