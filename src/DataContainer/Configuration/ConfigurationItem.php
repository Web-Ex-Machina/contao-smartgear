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

use Contao\Config;
use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\DataContainer;
use Contao\Date;
use Contao\FormFieldModel;
use Contao\FormModel;
use Contao\Input;
use Contao\LayoutModel;
use Contao\Message;
use Contao\ModuleModel;
use Contao\PageModel;
use WEM\SmartgearBundle\Classes\Dca\Manipulator as DCAManipulator;
use WEM\SmartgearBundle\Classes\StringUtil;
use WEM\SmartgearBundle\Classes\Util;
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
use WEM\SmartgearBundle\DataContainer\Core;
use WEM\SmartgearBundle\Model\Configuration\Configuration as ConfigurationModel;
use WEM\SmartgearBundle\Model\Configuration\ConfigurationItem as ConfigurationItemModel;

class ConfigurationItem extends Core
{
    public function __construct()
    {
        parent::__construct();
    }

    // public function listItems(array $row, string $label, DataContainer $dc, array $labels): array
    public function listItems(array $row, string $label, DataContainer $dc): string
    {
        $objItem = ConfigurationItemModel::findItems(['id' => $row['id']], 1);

        $arrData = [];

        switch ($objItem->type) {
            case ConfigurationItemModel::TYPE_PAGE_LEGAL_NOTICE:
            case ConfigurationItemModel::TYPE_PAGE_PRIVACY_POLITICS:
                if ($objItem->contao_page) {
                    $objPage = $objItem->getRelated('contao_page');
                    $arrData['contao_page'] = $objPage ? $objPage->title : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_page'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                break;
            case ConfigurationItemModel::TYPE_PAGE_SITEMAP:
                if ($objItem->contao_page) {
                    $objPage = $objItem->getRelated('contao_page');
                    $arrData['contao_page'] = $objPage ? $objPage->title : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_page'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }

                if ($objItem->contao_module) {
                    $objModule = $objItem->getRelated('contao_module');
                    $arrData['contao_module'] = $objModule ? $objModule->name : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_module'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                break;
            case ConfigurationItemModel::TYPE_USER_GROUP_ADMINISTRATORS:
            case ConfigurationItemModel::TYPE_USER_GROUP_REDACTORS:
                if ($objItem->contao_user_group) {
                    $objUserGroup = $objItem->getRelated('contao_user_group');
                    $arrData['contao_user_group'] = $objUserGroup ? $objUserGroup->name : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_user_group'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                break;
            case ConfigurationItemModel::TYPE_MODULE_WEM_SG_HEADER:
                if ($objItem->contao_module) {
                    $objModule = $objItem->getRelated('contao_module');
                    $arrData['contao_module'] = $objModule ? $objModule->name : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_module'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                break;
            case ConfigurationItemModel::TYPE_MODULE_WEM_SG_FOOTER:
                if ($objItem->contao_module) {
                    $objModule = $objItem->getRelated('contao_module');
                    $arrData['contao_module'] = $objModule ? $objModule->name : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_module'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                break;
            case ConfigurationItemModel::TYPE_MODULE_BREADCRUMB:
                if ($objItem->contao_module) {
                    $objModule = $objItem->getRelated('contao_module');
                    $arrData['contao_module'] = $objModule ? $objModule->name : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_module'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                break;
            case ConfigurationItemModel::TYPE_MODULE_WEM_SG_SOCIAL_NETWORKS:
                if ($objItem->contao_module) {
                    $objModule = $objItem->getRelated('contao_module');
                    $arrData['contao_module'] = $objModule ? $objModule->name : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_module'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                break;
            case ConfigurationItemModel::TYPE_MIXED_SITEMAP:
                if ($objItem->contao_page) {
                    $objPage = $objItem->getRelated('contao_page');
                    $arrData['contao_page'] = $objPage ? $objPage->title : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_page'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                if ($objItem->contao_module) {
                    $objModule = $objItem->getRelated('contao_module');
                    $arrData['contao_module'] = $objModule ? $objModule->name : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_module'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                break;
            case ConfigurationItemModel::TYPE_MIXED_FAQ:
                if ($objItem->contao_page) {
                    $objPage = $objItem->getRelated('contao_page');
                    $arrData['contao_page'] = $objPage ? $objPage->title : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_page'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                if ($objItem->contao_module) {
                    $objModule = $objItem->getRelated('contao_module');
                    $arrData['contao_module'] = $objModule ? $objModule->name : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_module'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                if ($objItem->contao_faq_category) {
                    $objFaqCategory = $objItem->getRelated('contao_faq_category');
                    $arrData['contao_faq_category'] = $objFaqCategory ? $objFaqCategory->title : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_faq_category'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                break;
            case ConfigurationItemModel::TYPE_MIXED_EVENTS:
                if ($objItem->contao_page) {
                    $objPage = $objItem->getRelated('contao_page');
                    $arrData['contao_page'] = $objPage ? $objPage->title : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_page'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                if ($objItem->contao_module_list) {
                    $objModule = $objItem->getRelated('contao_module_list');
                    $arrData['contao_module_list'] = $objModule ? $objModule->name : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_module_list'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                if ($objItem->contao_module_reader) {
                    $objModule = $objItem->getRelated('contao_module_reader');
                    $arrData['contao_module_reader'] = $objModule ? $objModule->name : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_module_reader'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                if ($objItem->contao_module_calendar) {
                    $objModule = $objItem->getRelated('contao_module_calendar');
                    $arrData['contao_module_calendar'] = $objModule ? $objModule->name : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_module_calendar'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                if ($objItem->contao_calendar) {
                    $objCalendar = $objItem->getRelated('contao_calendar');
                    $arrData['contao_calendar'] = $objCalendar ? $objCalendar->title : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_calendar'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                break;
            case ConfigurationItemModel::TYPE_MIXED_BLOG:
                if ($objItem->contao_page) {
                    $objPage = $objItem->getRelated('contao_page');
                    $arrData['contao_page'] = $objPage ? $objPage->title : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_page'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                if ($objItem->contao_module_list) {
                    $objModule = $objItem->getRelated('contao_module_list');
                    $arrData['contao_module_list'] = $objModule ? $objModule->name : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_module_list'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                if ($objItem->contao_module_reader) {
                    $objModule = $objItem->getRelated('contao_module_reader');
                    $arrData['contao_module_reader'] = $objModule ? $objModule->name : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_module_reader'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                if ($objItem->contao_news_archive) {
                    $objNewsArchive = $objItem->getRelated('contao_news_archive');
                    $arrData['contao_news_archive'] = $objNewsArchive ? $objNewsArchive->title : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_news_archive'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                break;
            case ConfigurationItemModel::TYPE_MIXED_FORM_CONTACT:
                if ($objItem->contao_page_form) {
                    $objPage = $objItem->getRelated('contao_page_form');
                    $arrData['contao_page_form'] = $objPage ? $objPage->title : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_page_form'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                if ($objItem->contao_page_form_sent) {
                    $objPage = $objItem->getRelated('contao_page_form_sent');
                    $arrData['contao_page_form_sent'] = $objPage ? $objPage->title : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_page_form_sent'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                if ($objItem->contao_form) {
                    $objForm = $objItem->getRelated('contao_form');
                    $arrData['contao_form'] = $objForm ? $objForm->title : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_form'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                if ($objItem->contao_notification) {
                    $objNotification = $objItem->getRelated('contao_notification');
                    $arrData['contao_notification'] = $objNotification ? $objNotification->title : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_notification'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                break;
        }

        $labels = [];
        foreach ($arrData as $property => $value) {
            $labels[] = '<strong>'.$GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()][$property][0].' :</strong> '.$value;
        }

        return implode('<br />', $labels);
    }

    public function onloadCallback(DataContainer $dc): void
    {
        if ('edit' === Input::get('act')) {
            // check contents linked tstamp against the configuration item one
            $this->checkLinkedContaoContentUpdated($dc);
            $this->checkRequiredConditions($dc);
        }
    }

    public function onsubmitCallback(DataContainer $dc): void
    {
        // only do that if it is a real save, not a reload
        if ('auto' === Input::post('SUBMIT_TYPE')) {
            return;
        }

        $objItem = ConfigurationItemModel::findOneById($dc->activeRecord->id);
        $objItem->refresh(); // otherwise $dc->activeRecord is the only version with updated values

        switch ($objItem->type) {
            case ConfigurationItemModel::TYPE_PAGE_LEGAL_NOTICE:
                $objItem = $this->managePageLegalNotice($objItem, (bool) Input::post('update_page'), $dc);
                break;
            case ConfigurationItemModel::TYPE_PAGE_PRIVACY_POLITICS:
                $objItem = $this->managePagePrivacyPolitics($objItem, (bool) Input::post('update_page'), $dc);
                break;
            case ConfigurationItemModel::TYPE_PAGE_SITEMAP:
                $objItem = $this->managePageSitemap($objItem, (bool) Input::post('update_page'), $dc);
                break;
            case ConfigurationItemModel::TYPE_USER_GROUP_ADMINISTRATORS:
                $objItem = $this->manageUserGroupAdministrators($objItem, (bool) Input::post('update_user_group'), $dc);
                break;
            case ConfigurationItemModel::TYPE_USER_GROUP_REDACTORS:
                $objItem = $this->manageUserGroupRedactors($objItem, (bool) Input::post('update_user_group'), $dc);
                break;
            case ConfigurationItemModel::TYPE_MODULE_WEM_SG_HEADER:
                $objItem = $this->manageModuleWemSgHeader($objItem, (bool) Input::post('update_module'), $dc);
                break;
            case ConfigurationItemModel::TYPE_MODULE_WEM_SG_FOOTER:
                $objItem = $this->manageModuleWemSgFooter($objItem, (bool) Input::post('update_module'), $dc);
                break;
            case ConfigurationItemModel::TYPE_MODULE_BREADCRUMB:
                $objItem = $this->manageModuleBreadcrumb($objItem, (bool) Input::post('update_module'), $dc);
                break;
            case ConfigurationItemModel::TYPE_MODULE_WEM_SG_SOCIAL_NETWORKS:
                $objItem = $this->manageModuleWemSgSocialNetworks($objItem, (bool) Input::post('update_module'), $dc);
                break;
            case ConfigurationItemModel::TYPE_MIXED_SITEMAP:
                $objItem = $this->manageMixedSitemap($objItem, (bool) Input::post('update_module'), (bool) Input::post('update_page'), $dc);
                break;
            case ConfigurationItemModel::TYPE_MIXED_FAQ:
                $objItem = $this->manageMixedFaq($objItem, (bool) Input::post('update_module'), (bool) Input::post('update_page'), (bool) Input::post('update_faq_category'), $dc);
                break;
            case ConfigurationItemModel::TYPE_MIXED_EVENTS:
                $objItem = $this->manageMixedEvents($objItem, (bool) Input::post('update_module_list'), (bool) Input::post('update_module_reader'), (bool) Input::post('update_module_calendar'), (bool) Input::post('update_page'), (bool) Input::post('update_calendar'), $dc);
                break;
            case ConfigurationItemModel::TYPE_MIXED_BLOG:
                $objItem = $this->manageMixedBlog($objItem, (bool) Input::post('update_module_list'), (bool) Input::post('update_module_reader'), (bool) Input::post('update_page'), (bool) Input::post('update_news_archive'), $dc);
                break;
            case ConfigurationItemModel::TYPE_MIXED_FORM_CONTACT:
                $objItem = $this->manageMixedFormContact($objItem, (bool) Input::post('update_page_form'), (bool) Input::post('update_page_form_sent'), (bool) Input::post('update_form'), (bool) Input::post('update_notification'), $dc);
                break;
        }

        if ((bool) Input::post('update_user_group_permission')) {
            $this->updateAddUserGroupSettingsAccordingToConfiguration($objItem, $dc);
        }

        $objItem->save();
    }

    public function updateAddUserGroupSettingsAccordingToConfiguration(ConfigurationItemModel $objItem, DataContainer $dc): void
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

    public function managePageLegalNotice(ConfigurationItemModel $objItem, bool $blnForcePageUpdate, DataContainer $dc): ConfigurationItemModel
    {
        if (!empty($objItem->page_name) && !empty($objItem->content_template)
        && (0 === (int) $dc->activeRecord->tstamp || $blnForcePageUpdate || (0 !== (int) $dc->activeRecord->tstamp && empty($objItem->contao_page))) // create mode or forced update
        ) {
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

    public function managePagePrivacyPolitics(ConfigurationItemModel $objItem, bool $blnForcePageUpdate, DataContainer $dc): ConfigurationItemModel
    {
        if (!empty($objItem->page_name) && !empty($objItem->content_template)
        && (0 === (int) $dc->activeRecord->tstamp || $blnForcePageUpdate || (0 !== (int) $dc->activeRecord->tstamp && empty($objItem->contao_page))) // create mode or forced update
        ) {
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

    public function managePageSitemap(ConfigurationItemModel $objItem, bool $blnForcePageUpdate, DataContainer $dc): ConfigurationItemModel
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
        && (0 === (int) $dc->activeRecord->tstamp || $blnForcePageUpdate || (0 !== (int) $dc->activeRecord->tstamp && empty($objItem->contao_page))) // create mode or forced update
        ) {
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

    public function managePageFaqCreate(ConfigurationItemModel $objItem, bool $blnForcePageUpdate, DataContainer $dc): ConfigurationItemModel
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
        && (0 === (int) $dc->activeRecord->tstamp || $blnForcePageUpdate || (0 !== (int) $dc->activeRecord->tstamp && empty($objItem->contao_page))) // create mode or forced update
        ) {
            /** @var ConfigurationModel */
            $objConfiguration = $objItem->getRelated('pid');
            $objPage = null;
            if (!empty($objItem->contao_page)) {
                $objPage = PageModel::findByPk($objItem->contao_page);
                if ($objPage) {
                    PageUtil::emptyPage($objItem->contao_page);
                }
            }

            $objPage = PageUtil::createPageFaq($objItem->page_name, (int) $objConfiguration->contao_page_root, $objItem->contao_page ? ['id' => $objItem->contao_page] : []);

            $objItem->contao_page = $objPage->id;
        }

        return $objItem;
    }

    public function managePageFaqFill(ConfigurationItemModel $objItem, bool $blnForcePageUpdate, DataContainer $dc): ConfigurationItemModel
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
            0 === (int) $dc->activeRecord->tstamp
            || $blnForcePageUpdate
            || (
                0 !== (int) $dc->activeRecord->tstamp
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

    public function managePageEventsCreate(ConfigurationItemModel $objItem, bool $blnForcePageUpdate, DataContainer $dc): ConfigurationItemModel
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
        && (0 === (int) $dc->activeRecord->tstamp || $blnForcePageUpdate || (0 !== (int) $dc->activeRecord->tstamp && empty($objItem->contao_page))) // create mode or forced update
        ) {
            /** @var ConfigurationModel */
            $objConfiguration = $objItem->getRelated('pid');
            $objPage = null;
            if (!empty($objItem->contao_page)) {
                $objPage = PageModel::findByPk($objItem->contao_page);
                if ($objPage) {
                    PageUtil::emptyPage($objItem->contao_page);
                }
            }

            $objPage = PageUtil::createPageEvents($objItem->page_name, (int) $objConfiguration->contao_page_root, $objItem->contao_page ? ['id' => $objItem->contao_page] : []);

            $objItem->contao_page = $objPage->id;
        }

        return $objItem;
    }

    public function managePageEventsFill(ConfigurationItemModel $objItem, bool $blnForcePageUpdate, DataContainer $dc): ConfigurationItemModel
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
        && (0 === (int) $dc->activeRecord->tstamp || $blnForcePageUpdate || (0 !== (int) $dc->activeRecord->tstamp && empty($objItem->contao_page))) // create mode or forced update
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

    public function managePageBlogCreate(ConfigurationItemModel $objItem, bool $blnForcePageUpdate, DataContainer $dc): ConfigurationItemModel
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
        && (0 === (int) $dc->activeRecord->tstamp || $blnForcePageUpdate || (0 !== (int) $dc->activeRecord->tstamp && empty($objItem->contao_page))) // create mode or forced update
        ) {
            /** @var ConfigurationModel */
            $objConfiguration = $objItem->getRelated('pid');
            $objPage = null;
            if (!empty($objItem->contao_page)) {
                $objPage = PageModel::findByPk($objItem->contao_page);
                if ($objPage) {
                    PageUtil::emptyPage($objItem->contao_page);
                }
            }

            $objPage = PageUtil::createPageBlog($objItem->page_name, (int) $objConfiguration->contao_page_root, $objItem->contao_page ? ['id' => $objItem->contao_page] : []);

            $objItem->contao_page = $objPage->id;
        }

        return $objItem;
    }

    public function managePageBlogFill(ConfigurationItemModel $objItem, bool $blnForcePageUpdate, DataContainer $dc): ConfigurationItemModel
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
        && (0 === (int) $dc->activeRecord->tstamp || $blnForcePageUpdate || (0 !== (int) $dc->activeRecord->tstamp && empty($objItem->contao_page))) // create mode or forced update
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

    public function managePageFormContactCreate(ConfigurationItemModel $objItem, bool $blnForcePageUpdate, DataContainer $dc): ConfigurationItemModel
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
        && (0 === (int) $dc->activeRecord->tstamp || $blnForcePageUpdate || (0 !== (int) $dc->activeRecord->tstamp && empty($objItem->contao_page_form))) // create mode or forced update
        ) {
            /** @var ConfigurationModel */
            $objConfiguration = $objItem->getRelated('pid');
            $objPage = null;
            if (!empty($objItem->contao_page_form)) {
                $objPage = PageModel::findByPk($objItem->contao_page_form);
                if ($objPage) {
                    PageUtil::emptyPage($objItem->contao_page_form);
                }
            }

            $objPage = PageUtil::createPageFormContact($objItem->page_form_name, (int) $objConfiguration->contao_page_root, $objItem->contao_page_form ? ['id' => $objItem->contao_page_form] : []);

            $objItem->contao_page_form = $objPage->id;
        }

        return $objItem;
    }

    public function managePageFormContactFill(ConfigurationItemModel $objItem, bool $blnForcePageUpdate, DataContainer $dc): ConfigurationItemModel
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
        && (0 === (int) $dc->activeRecord->tstamp || $blnForcePageUpdate || (0 !== (int) $dc->activeRecord->tstamp && empty($objItem->contao_page_form))) // create mode or forced update
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

    public function managePageFormContactSentCreate(ConfigurationItemModel $objItem, bool $blnForcePageUpdate, DataContainer $dc): ConfigurationItemModel
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
        && (0 === (int) $dc->activeRecord->tstamp || $blnForcePageUpdate || (0 !== (int) $dc->activeRecord->tstamp && empty($objItem->contao_page_form_sent))) // create mode or forced update
        ) {
            /** @var ConfigurationModel */
            $objConfiguration = $objItem->getRelated('pid');
            $objPage = null;
            if (!empty($objItem->contao_page_form_sent)) {
                $objPage = PageModel::findByPk($objItem->contao_page_form_sent);
                if ($objPage) {
                    PageUtil::emptyPage($objItem->contao_page_form_sent);
                }
            }

            $objPage = PageUtil::createPageFormContactSent($objItem->page_form_sent_name, (int) $objConfiguration->contao_page_root, $objItem->contao_page_form_sent ? ['id' => $objItem->contao_page_form_sent] : []);

            $objItem->contao_page_form_sent = $objPage->id;
        }

        return $objItem;
    }

    public function managePageFormContactSentFill(ConfigurationItemModel $objItem, bool $blnForcePageUpdate, DataContainer $dc): ConfigurationItemModel
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
        && (0 === (int) $dc->activeRecord->tstamp || $blnForcePageUpdate || (0 !== (int) $dc->activeRecord->tstamp && empty($objItem->contao_page_form_sent))) // create mode or forced update
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
                // 'text' => $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.contentTextPageFormSent', [], 'contao_default'),
                'text' => $GLOBALS['TL_LANG']['WEMSG']['FORMCONTACT']['INSTALL_GENERAL']['contentTextPageFormSent'],
            ]);
        }

        return $objItem;
    }

    public function manageUserGroupAdministrators(ConfigurationItemModel $objItem, bool $blnForceUserGroupUpdate, DataContainer $dc): ConfigurationItemModel
    {
        if (!empty($objItem->user_group_name)
        && (0 === (int) $dc->activeRecord->tstamp || $blnForceUserGroupUpdate || (0 !== (int) $dc->activeRecord->tstamp && empty($objItem->contao_user_group))) // create mode or forced update
        ) {
            $objUserGroup = UserGroupUtil::createUserGroupAdministrators($objItem->user_group_name, $objItem->contao_user_group ? ['id' => $objItem->contao_user_group] : []);

            $objItem->contao_user_group = $objUserGroup->id;

            UserGroupUtil::updateAddUserGroupSettingsAccordingToConfiguration($objUserGroup, $objItem->getRelated('pid'));
        }

        return $objItem;
    }

    public function manageUserGroupRedactors(ConfigurationItemModel $objItem, bool $blnForceUserGroupUpdate, DataContainer $dc): ConfigurationItemModel
    {
        if (!empty($objItem->user_group_name)
        && (0 === (int) $dc->activeRecord->tstamp || $blnForceUserGroupUpdate || (0 !== (int) $dc->activeRecord->tstamp && empty($objItem->contao_user_group))) // create mode or forced update
        ) {
            $objUserGroup = UserGroupUtil::createUserGroupRedactors($objItem->user_group_name, $objItem->contao_user_group ? ['id' => $objItem->contao_user_group] : []);

            $objItem->contao_user_group = $objUserGroup->id;

            UserGroupUtil::updateAddUserGroupSettingsAccordingToConfiguration($objUserGroup, $objItem->getRelated('pid'));
        }

        return $objItem;
    }

    public function manageModuleWemSgHeader(ConfigurationItemModel $objItem, bool $blnForceModuleUpdate, DataContainer $dc): ConfigurationItemModel
    {
        if (!empty($objItem->module_name) && !empty($objItem->singleSRC)
        && (0 === (int) $dc->activeRecord->tstamp || $blnForceModuleUpdate || (0 !== (int) $dc->activeRecord->tstamp && empty($objItem->contao_module))) // create mode or forced update
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
        if (\is_array($dc->activeRecord->contao_layout_to_update)) {
            $contaoLayoutsToUpdate = $dc->activeRecord->contao_layout_to_update;
        } else {
            $contaoLayoutsToUpdate = StringUtil::deserialize($objItem->contao_layout_to_update, true);
        }
        $contaoLayoutsToUpdate = StringUtil::deserialize($objItem->contao_layout_to_update, true);
        foreach ($contaoLayoutsToUpdate as $layoutId) {
            LayoutUtil::replaceHeader((int) $layoutId, (int) $objItem->contao_module);
        }

        return $objItem;
    }

    public function manageModuleWemSgFooter(ConfigurationItemModel $objItem, bool $blnForceModuleUpdate, DataContainer $dc): ConfigurationItemModel
    {
        if (!empty($objItem->module_name) && !empty($objItem->content_template)
        && (0 === (int) $dc->activeRecord->tstamp || $blnForceModuleUpdate || (0 !== (int) $dc->activeRecord->tstamp && empty($objItem->contao_module))) // create mode or forced update
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
        if (\is_array($dc->activeRecord->contao_layout_to_update)) {
            $contaoLayoutsToUpdate = $dc->activeRecord->contao_layout_to_update;
        } else {
            $contaoLayoutsToUpdate = StringUtil::deserialize($objItem->contao_layout_to_update, true);
        }
        foreach ($contaoLayoutsToUpdate as $layoutId) {
            LayoutUtil::replaceFooter((int) $layoutId, (int) $objItem->contao_module);
        }

        return $objItem;
    }

    public function manageModuleBreadcrumb(ConfigurationItemModel $objItem, bool $blnForceModuleUpdate, DataContainer $dc): ConfigurationItemModel
    {
        if (!empty($objItem->module_name)
        && (0 === (int) $dc->activeRecord->tstamp || $blnForceModuleUpdate || (0 !== (int) $dc->activeRecord->tstamp && empty($objItem->contao_module))) // create mode or forced update
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
        if (\is_array($dc->activeRecord->contao_layout_to_update)) {
            $contaoLayoutsToUpdate = $dc->activeRecord->contao_layout_to_update;
        } else {
            $contaoLayoutsToUpdate = StringUtil::deserialize($objItem->contao_layout_to_update, true);
        }
        foreach ($contaoLayoutsToUpdate as $layoutId) {
            LayoutUtil::replaceBreadcrumb((int) $layoutId, (int) $objItem->contao_module);
        }

        return $objItem;
    }

    public function manageModuleWemSgSocialNetworks(ConfigurationItemModel $objItem, bool $blnForceModuleUpdate, DataContainer $dc): ConfigurationItemModel
    {
        if (!empty($objItem->module_name)
        && (0 === (int) $dc->activeRecord->tstamp || $blnForceModuleUpdate || (0 !== (int) $dc->activeRecord->tstamp && empty($objItem->contao_module))) // create mode or forced update
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

    public function manageModuleSitemap(ConfigurationItemModel $objItem, bool $blnForceModuleUpdate, DataContainer $dc): ConfigurationItemModel
    {
        if (!empty($objItem->module_name)
        && (0 === (int) $dc->activeRecord->tstamp || $blnForceModuleUpdate || (0 !== (int) $dc->activeRecord->tstamp && empty($objItem->contao_module))) // create mode or forced update
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

    public function manageModuleFaq(ConfigurationItemModel $objItem, bool $blnForceModuleUpdate, DataContainer $dc): ConfigurationItemModel
    {
        if (!empty($objItem->module_name)
        && (0 === (int) $dc->activeRecord->tstamp || $blnForceModuleUpdate || (0 !== (int) $dc->activeRecord->tstamp && empty($objItem->contao_module))) // create mode or forced update
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

    public function manageModuleEventsList(ConfigurationItemModel $objItem, bool $blnForceModuleUpdate, DataContainer $dc): ConfigurationItemModel
    {
        if (!empty($objItem->module_list_name)
        && (0 === (int) $dc->activeRecord->tstamp || $blnForceModuleUpdate || (0 !== (int) $dc->activeRecord->tstamp && empty($objItem->contao_module_list))) // create mode or forced update
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

    public function manageModuleEventsReader(ConfigurationItemModel $objItem, bool $blnForceModuleUpdate, DataContainer $dc): ConfigurationItemModel
    {
        if (!empty($objItem->module_reader_name)
        && (0 === (int) $dc->activeRecord->tstamp || $blnForceModuleUpdate || (0 !== (int) $dc->activeRecord->tstamp && empty($objItem->contao_module_reader))) // create mode or forced update
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

    public function manageModuleEventsCalendar(ConfigurationItemModel $objItem, bool $blnForceModuleUpdate, DataContainer $dc): ConfigurationItemModel
    {
        if (!empty($objItem->module_calendar_name)
        && (0 === (int) $dc->activeRecord->tstamp || $blnForceModuleUpdate || (0 !== (int) $dc->activeRecord->tstamp && empty($objItem->contao_module_calendar))) // create mode or forced update
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

    public function manageModuleBlogList(ConfigurationItemModel $objItem, bool $blnForceModuleUpdate, DataContainer $dc): ConfigurationItemModel
    {
        if (!empty($objItem->module_list_name)
        && (0 === (int) $dc->activeRecord->tstamp || $blnForceModuleUpdate || (0 !== (int) $dc->activeRecord->tstamp && empty($objItem->contao_module_list))) // create mode or forced update
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

    public function manageModuleBlogReader(ConfigurationItemModel $objItem, bool $blnForceModuleUpdate, DataContainer $dc): ConfigurationItemModel
    {
        if (!empty($objItem->module_reader_name)
        && (0 === (int) $dc->activeRecord->tstamp || $blnForceModuleUpdate || (0 !== (int) $dc->activeRecord->tstamp && empty($objItem->contao_module_reader))) // create mode or forced update
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

    public function manageFaqCategory(ConfigurationItemModel $objItem, bool $blnForceFaqUpdate, DataContainer $dc): ConfigurationItemModel
    {
        if (!empty($objItem->faq_category_name)
        && (0 === (int) $dc->activeRecord->tstamp || $blnForceFaqUpdate || (0 !== (int) $dc->activeRecord->tstamp && empty($objItem->contao_faq_category))) // create mode or forced update
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

    public function manageCalendar(ConfigurationItemModel $objItem, bool $blnForceCalUpdate, DataContainer $dc): ConfigurationItemModel
    {
        if (!empty($objItem->calendar_name)
        && (0 === (int) $dc->activeRecord->tstamp || $blnForceCalUpdate || (0 !== (int) $dc->activeRecord->tstamp && empty($objItem->contao_calendar))) // create mode or forced update
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

    public function manageNewsArchive(ConfigurationItemModel $objItem, bool $blnForceNewsArchiveUpdate, DataContainer $dc): ConfigurationItemModel
    {
        if (!empty($objItem->news_archive_name)
        && (0 === (int) $dc->activeRecord->tstamp || $blnForceNewsArchiveUpdate || (0 !== (int) $dc->activeRecord->tstamp && empty($objItem->contao_news_archive))) // create mode or forced update
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

    public function manageFormFormContact(ConfigurationItemModel $objItem, bool $blnForceFormUpdate, DataContainer $dc): ConfigurationItemModel
    {
        if (!empty($objItem->form_name)
        && (0 === (int) $dc->activeRecord->tstamp || $blnForceFormUpdate || (0 !== (int) $dc->activeRecord->tstamp && empty($objItem->contao_form))) // create mode or forced update
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

    public function manageNotificationFormContactSent(ConfigurationItemModel $objItem, bool $blnForceNotificationUpdate, DataContainer $dc): ConfigurationItemModel
    {
        if (!empty($objItem->notification_name)
        && (0 === (int) $dc->activeRecord->tstamp || $blnForceNotificationUpdate || (0 !== (int) $dc->activeRecord->tstamp && empty($objItem->contao_notification))) // create mode or forced update
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
                $objConfiguration->owner_email,
                $objConfiguration->language,
                true,
                []
            );

                $objItem->contao_notification = $objNotification->id;
            }
        }

        return $objItem;
    }

    public function manageMixedSitemap(ConfigurationItemModel $objItem, bool $blnForceModuleUpdate, bool $blnForcePageUpdate, DataContainer $dc): ConfigurationItemModel
    {
        $objItem = $this->manageModuleSitemap($objItem, $blnForceModuleUpdate, $dc);

        return $this->managePageSitemap($objItem, $blnForcePageUpdate, $dc);
    }

    public function manageMixedFaq(ConfigurationItemModel $objItem, bool $blnForceModuleUpdate, bool $blnForcePageUpdate, bool $blnForceFAQUpdate, DataContainer $dc): ConfigurationItemModel
    {
        $oldPageId = $objItem->contao_page;
        $objItem = $this->managePageFaqCreate($objItem, $blnForcePageUpdate, $dc);
        $objItem = $this->manageFaqCategory($objItem, $blnForceFAQUpdate, $dc);
        $objItem = $this->manageModuleFaq($objItem, $blnForceModuleUpdate, $dc);

        return $this->managePageFaqFill($objItem, $blnForcePageUpdate || (int) $oldPageId !== (int) $objItem->contao_page, $dc);
    }

    public function manageMixedEvents(ConfigurationItemModel $objItem, bool $blnForceModuleListUpdate, bool $blnForceModuleReaderUpdate, bool $blnForceModuleCalendarUpdate, bool $blnForcePageUpdate, bool $blnForceCalendarUpdate, DataContainer $dc): ConfigurationItemModel
    {
        $oldPageId = $objItem->contao_page;
        $objItem = $this->managePageEventsCreate($objItem, $blnForcePageUpdate, $dc);
        $objItem = $this->manageCalendar($objItem, $blnForceCalendarUpdate, $dc);
        $objItem = $this->manageModuleEventsReader($objItem, $blnForceModuleReaderUpdate, $dc);
        $objItem = $this->manageModuleEventsList($objItem, $blnForceModuleListUpdate, $dc);
        $objItem = $this->manageModuleEventsCalendar($objItem, $blnForceModuleCalendarUpdate, $dc);

        return $this->managePageEventsFill($objItem, $blnForcePageUpdate || (int) $oldPageId !== (int) $objItem->contao_page, $dc);
    }

    public function manageMixedBlog(ConfigurationItemModel $objItem, bool $blnForceModuleListUpdate, bool $blnForceModuleReaderUpdate, bool $blnForcePageUpdate, bool $blnForceNewsArchiveUpdate, DataContainer $dc): ConfigurationItemModel
    {
        $oldPageId = $objItem->contao_page;
        $objItem = $this->managePageBlogCreate($objItem, $blnForcePageUpdate, $dc);
        $objItem = $this->manageNewsArchive($objItem, $blnForceNewsArchiveUpdate, $dc);
        $objItem = $this->manageModuleBlogReader($objItem, $blnForceModuleReaderUpdate, $dc);
        $objItem = $this->manageModuleBlogList($objItem, $blnForceModuleListUpdate, $dc);

        return $this->managePageBlogFill($objItem, $blnForcePageUpdate || (int) $oldPageId !== (int) $objItem->contao_page, $dc);
    }

    public function manageMixedFormContact(ConfigurationItemModel $objItem, bool $blnForcePageFormUpdate, bool $blnForcePageFormSentUpdate, bool $blnForceFormUpdate, bool $blnForceNotificationUpdate, DataContainer $dc): ConfigurationItemModel
    {
        $oldPageId = $objItem->contao_page;
        $objItem = $this->managePageFormContactCreate($objItem, $blnForcePageFormUpdate, $dc);
        $objItem = $this->managePageFormContactSentCreate($objItem, $blnForcePageFormSentUpdate, $dc);
        $objItem = $this->manageFormFormContact($objItem, $blnForceFormUpdate, $dc);
        $objItem = $this->manageNotificationFormContactSent($objItem, $blnForceNotificationUpdate, $dc);

        $objItem = $this->managePageFormContactFill($objItem, $blnForcePageFormUpdate || (int) $oldPageId !== (int) $objItem->contao_page, $dc);

        return $this->managePageFormContactSentFill($objItem, $blnForcePageFormSentUpdate || (int) $oldPageId !== (int) $objItem->contao_page, $dc);
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
            case ConfigurationItemModel::TYPE_MODULE_WEM_SG_FOOTER:
                $arrOptions = Util::getFileListByLanguages(Util::getPublicOrWebDirectory().'/bundles/wemsmartgear/examples/footer');
                break;
        }

        return ['-'] + $arrOptions;
    }

    public function contaoModuleOptionsCallback(DataContainer $dc): array
    {
        $arrOptions = [];

        $objItem = ConfigurationItemModel::findOneById($dc->activeRecord->id);
        /** @var ConfigurationModel */
        $objConfiguration = $objItem->getRelated('pid');

        $modules = ModuleModel::findByPid($objConfiguration->contao_theme);
        if ($modules) {
            while ($modules->next()) {
                $arrOptions[$modules->type][$modules->id] = $modules->name;
            }
        }

        return $arrOptions;
    }

    public function contaoLayoutToUpdateOptionsCallback(DataContainer $dc): array
    {
        $arrOptions = [];

        $objItem = ConfigurationItemModel::findOneById($dc->activeRecord->id);
        /** @var ConfigurationModel */
        $objConfiguration = $objItem->getRelated('pid');

        switch ($dc->activeRecord->type) {
            case ConfigurationItemModel::TYPE_MODULE_WEM_SG_HEADER:
            case ConfigurationItemModel::TYPE_MODULE_WEM_SG_FOOTER:
            case ConfigurationItemModel::TYPE_MODULE_BREADCRUMB:
                $layouts = LayoutModel::findByPid($objConfiguration->contao_theme);
                if ($layouts) {
                    while ($layouts->next()) {
                        $arrOptions[$layouts->id] = $layouts->name;
                    }
                }
                // $arrOptions = Util::getFileListByLanguages(Util::getPublicOrWebDirectory().'/bundles/wemsmartgear/examples/legal-notice');
                break;
        }

        return $arrOptions;
    }

    public function checkRequiredConditions(DataContainer $dc): void
    {
        if (!$dc->id) {
            return;
        }
        $objItem = ConfigurationItemModel::findOneById($dc->id);
        $objConfiguration = $objItem->getRelated('pid');
        switch ($objItem->type) {
            case ConfigurationItemModel::TYPE_MIXED_FORM_CONTACT:
                if (empty($objConfiguration->email_gateway)) {
                    Message::addError('La configuration ne stipule aucune passerelle email : il ne sera pas possible de créer la notification si besoin.');
                }
            break;
        }
    }

    public function checkLinkedContaoContentUpdated(DataContainer $dc): void
    {
        if (!$dc->id) {
            return;
        }
        $objItem = ConfigurationItemModel::findOneById($dc->id);

        $dcaManipulator = DCAManipulator::create(ConfigurationItemModel::getTable());
        $addFields = false;

        if ($objItem->contao_page) {
            $objPage = $objItem->getRelated('contao_page');
            if ($objPage
            && (int) $objPage->tstamp > (int) $objItem->tstamp
            ) {
                Message::addInfo(sprintf($GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['contao_page_updated_outside_sg_configuration'], $objPage->title, $objPage->id, Date::parse(Config::get('datimFormat'), (int) $objItem->tstamp)));
            }
            if (0 !== (int) $objItem->tstamp) {
                $dcaManipulator
                    ->addField('update_page', [
                        'label' => &$GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['update_page'],
                        'inputType' => 'checkbox',
                        'save_callback' => [function ($val) {return ''; }], // so Contao does not try to save this fake field
                        'eval' => ['doNotSaveEmpty' => true], // so Contao does not try to save this fake field
                    ])
                ;
                $addFields = true;
            }
        }

        if ($objItem->contao_page_form) {
            $objPage = $objItem->getRelated('contao_page_form');
            if ($objPage
            && (int) $objPage->tstamp > (int) $objItem->tstamp
            ) {
                Message::addInfo(sprintf($GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['contao_page_form_updated_outside_sg_configuration'], $objPage->title, $objPage->id, Date::parse(Config::get('datimFormat'), (int) $objItem->tstamp)));
            }
            if (0 !== (int) $objItem->tstamp) {
                $dcaManipulator
                    ->addField('update_page_form', [
                        'label' => &$GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['update_page_form'],
                        'inputType' => 'checkbox',
                        'save_callback' => [function ($val) {return ''; }], // so Contao does not try to save this fake field
                        'eval' => ['doNotSaveEmpty' => true], // so Contao does not try to save this fake field
                    ])
                ;
                $addFields = true;
            }
        }

        if ($objItem->contao_page_form_sent) {
            $objPage = $objItem->getRelated('contao_page_form_sent');
            if ($objPage
            && (int) $objPage->tstamp > (int) $objItem->tstamp
            ) {
                Message::addInfo(sprintf($GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['contao_page_form_sent_updated_outside_sg_configuration'], $objPage->title, $objPage->id, Date::parse(Config::get('datimFormat'), (int) $objItem->tstamp)));
            }
            if (0 !== (int) $objItem->tstamp) {
                $dcaManipulator
                    ->addField('update_page_form_sent', [
                        'label' => &$GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['update_page_form_sent'],
                        'inputType' => 'checkbox',
                        'save_callback' => [function ($val) {return ''; }], // so Contao does not try to save this fake field
                        'eval' => ['doNotSaveEmpty' => true], // so Contao does not try to save this fake field
                    ])
                ;
                $addFields = true;
            }
        }

        if ($objItem->contao_module) {
            $objModule = $objItem->getRelated('contao_module');
            if ($objModule
            && (int) $objModule->tstamp > (int) $objItem->tstamp
            ) {
                Message::addInfo(sprintf($GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['contao_module_updated_outside_sg_configuration'], $objModule->name, $objModule->id, Date::parse(Config::get('datimFormat'), (int) $objItem->tstamp)));
            }
            if (0 !== (int) $objItem->tstamp) {
                $dcaManipulator
                    ->addField('update_module', [
                        'label' => &$GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['update_module'],
                        'inputType' => 'checkbox',
                        'save_callback' => [function ($val) {return ''; }], // so Contao does not try to save this fake field
                        'eval' => ['doNotSaveEmpty' => true], // so Contao does not try to save this fake field
                    ])
                ;
            }
            $addFields = true;
        }

        if ($objItem->contao_module_list) {
            $objModule = $objItem->getRelated('contao_module_list');
            if ($objModule
            && (int) $objModule->tstamp > (int) $objItem->tstamp
            ) {
                Message::addInfo(sprintf($GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['contao_module_list_updated_outside_sg_configuration'], $objModule->name, $objModule->id, Date::parse(Config::get('datimFormat'), (int) $objItem->tstamp)));
            }
            if (0 !== (int) $objItem->tstamp) {
                $dcaManipulator
                    ->addField('update_module_list', [
                        'label' => &$GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['update_module_list'],
                        'inputType' => 'checkbox',
                        'save_callback' => [function ($val) {return ''; }], // so Contao does not try to save this fake field
                        'eval' => ['doNotSaveEmpty' => true], // so Contao does not try to save this fake field
                    ])
                ;
            }
            $addFields = true;
        }

        if ($objItem->contao_module_reader) {
            $objModule = $objItem->getRelated('contao_module_reader');
            if ($objModule
            && (int) $objModule->tstamp > (int) $objItem->tstamp
            ) {
                Message::addInfo(sprintf($GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['contao_module_reader_updated_outside_sg_configuration'], $objModule->name, $objModule->id, Date::parse(Config::get('datimFormat'), (int) $objItem->tstamp)));
            }
            if (0 !== (int) $objItem->tstamp) {
                $dcaManipulator
                    ->addField('update_module_reader', [
                        'label' => &$GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['update_module_reader'],
                        'inputType' => 'checkbox',
                        'save_callback' => [function ($val) {return ''; }], // so Contao does not try to save this fake field
                        'eval' => ['doNotSaveEmpty' => true], // so Contao does not try to save this fake field
                    ])
                ;
            }
            $addFields = true;
        }

        if ($objItem->contao_module_calendar) {
            $objModule = $objItem->getRelated('contao_module_calendar');
            if ($objModule
            && (int) $objModule->tstamp > (int) $objItem->tstamp
            ) {
                Message::addInfo(sprintf($GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['contao_module_calendar_updated_outside_sg_configuration'], $objModule->name, $objModule->id, Date::parse(Config::get('datimFormat'), (int) $objItem->tstamp)));
            }
            if (0 !== (int) $objItem->tstamp) {
                $dcaManipulator
                    ->addField('update_module_calendar', [
                        'label' => &$GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['update_module_calendar'],
                        'inputType' => 'checkbox',
                        'save_callback' => [function ($val) {return ''; }], // so Contao does not try to save this fake field
                        'eval' => ['doNotSaveEmpty' => true], // so Contao does not try to save this fake field
                    ])
                ;
            }
            $addFields = true;
        }

        if ($objItem->contao_user_group) {
            $objUserGroup = $objItem->getRelated('contao_user_group');
            if ($objUserGroup
            && (int) $objUserGroup->tstamp > (int) $objItem->tstamp
            ) {
                Message::addInfo(sprintf($GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['contao_user_group_updated_outside_sg_configuration'], $objUserGroup->name, $objUserGroup->id, Date::parse(Config::get('datimFormat'), (int) $objItem->tstamp)));
            }
            if (0 !== (int) $objItem->tstamp) {
                $dcaManipulator
                    ->addField('update_user_group', [
                        'label' => &$GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['update_user_group'],
                        'inputType' => 'checkbox',
                        'save_callback' => [function ($val) {return ''; }], // so Contao does not try to save this fake field
                        'eval' => ['doNotSaveEmpty' => true], // so Contao does not try to save this fake field
                    ])
                ;
                $addFields = true;
            }
        }

        if ($objItem->contao_faq_category) {
            $objFaqCat = $objItem->getRelated('contao_faq_category');
            if ($objFaqCat
            && (int) $objFaqCat->tstamp > (int) $objItem->tstamp
            ) {
                Message::addInfo(sprintf($GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['contao_faq_category_updated_outside_sg_configuration'], $objFaqCat->name, $objFaqCat->id, Date::parse(Config::get('datimFormat'), (int) $objItem->tstamp)));
            }
            if (0 !== (int) $objItem->tstamp) {
                $dcaManipulator
                    ->addField('update_faq_category', [
                        'label' => &$GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['update_faq_category'],
                        'inputType' => 'checkbox',
                        'save_callback' => [function ($val) {return ''; }], // so Contao does not try to save this fake field
                        'eval' => ['doNotSaveEmpty' => true], // so Contao does not try to save this fake field
                    ])
                ;
                $addFields = true;
            }
        }

        if ($objItem->contao_calendar) {
            $objCal = $objItem->getRelated('contao_calendar');
            if ($objCal
            && (int) $objCal->tstamp > (int) $objItem->tstamp
            ) {
                Message::addInfo(sprintf($GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['contao_calendar_updated_outside_sg_configuration'], $objCal->name, $objCal->id, Date::parse(Config::get('datimFormat'), (int) $objItem->tstamp)));
            }
            if (0 !== (int) $objItem->tstamp) {
                $dcaManipulator
                    ->addField('update_calendar', [
                        'label' => &$GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['update_calendar'],
                        'inputType' => 'checkbox',
                        'save_callback' => [function ($val) {return ''; }], // so Contao does not try to save this fake field
                        'eval' => ['doNotSaveEmpty' => true], // so Contao does not try to save this fake field
                    ])
                ;
                $addFields = true;
            }
        }

        if ($objItem->contao_news_archive) {
            $objNewsArch = $objItem->getRelated('contao_news_archive');
            if ($objNewsArch
            && (int) $objNewsArch->tstamp > (int) $objItem->tstamp
            ) {
                Message::addInfo(sprintf($GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['contao_news_archive_updated_outside_sg_configuration'], $objNewsArch->title, $objNewsArch->id, Date::parse(Config::get('datimFormat'), (int) $objItem->tstamp)));
            }
            if (0 !== (int) $objItem->tstamp) {
                $dcaManipulator
                    ->addField('update_news_archive', [
                        'label' => &$GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['update_news_archive'],
                        'inputType' => 'checkbox',
                        'save_callback' => [function ($val) {return ''; }], // so Contao does not try to save this fake field
                        'eval' => ['doNotSaveEmpty' => true], // so Contao does not try to save this fake field
                    ])
                ;
                $addFields = true;
            }
        }

        if ($objItem->contao_form) {
            $objForm = $objItem->getRelated('contao_form');
            if ($objForm
            && (int) $objForm->tstamp > (int) $objItem->tstamp
            ) {
                Message::addInfo(sprintf($GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['contao_form_updated_outside_sg_configuration'], $objForm->title, $objForm->id, Date::parse(Config::get('datimFormat'), (int) $objItem->tstamp)));
            }
            if (0 !== (int) $objItem->tstamp) {
                $dcaManipulator
                    ->addField('update_form', [
                        'label' => &$GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['update_form'],
                        'inputType' => 'checkbox',
                        'save_callback' => [function ($val) {return ''; }], // so Contao does not try to save this fake field
                        'eval' => ['doNotSaveEmpty' => true], // so Contao does not try to save this fake field
                    ])
                ;
                $addFields = true;
            }
        }

        if ($objItem->contao_notification) {
            $objNcNotif = $objItem->getRelated('contao_notification');
            if ($objNcNotif
            && (int) $objNcNotif->tstamp > (int) $objItem->tstamp
            ) {
                Message::addInfo(sprintf($GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['contao_notification_updated_outside_sg_configuration'], $objNcNotif->title, $objNcNotif->id, Date::parse(Config::get('datimFormat'), (int) $objItem->tstamp)));
            }
            if (0 !== (int) $objItem->tstamp) {
                $dcaManipulator
                    ->addField('update_notification', [
                        'label' => &$GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['update_notification'],
                        'inputType' => 'checkbox',
                        'save_callback' => [function ($val) {return ''; }], // so Contao does not try to save this fake field
                        'eval' => ['doNotSaveEmpty' => true], // so Contao does not try to save this fake field
                    ])
                ;
                $addFields = true;
            }
        }

        if (\in_array($objItem->type, array_merge(ConfigurationItemModel::TYPES_MIXED, ConfigurationItemModel::TYPES_PAGE, ConfigurationItemModel::TYPES_MODULE), true)
        && 0 < ConfigurationItemModel::countItems(['pid' => $objItem->pid, 'type' => ConfigurationItemModel::TYPES_USER_GROUP])
        ) {
            $dcaManipulator
                ->addField('update_user_group_permission', [
                    'label' => &$GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['update_user_group_permission'],
                    'inputType' => 'checkbox',
                    'save_callback' => [function ($val) {return ''; }], // so Contao does not try to save this fake field
                    'eval' => ['doNotSaveEmpty' => true], // so Contao does not try to save this fake field
                ])
            ;
            $addFields = true;
        }

        $paletteManipulator = PaletteManipulator::create();
        if ($addFields) {
            $paletteManipulator->addLegend('update_legend');
            if ($dcaManipulator->hasField('update_page')) {
                $paletteManipulator->addField('update_page', 'update_legend');
            }
            if ($dcaManipulator->hasField('update_page_form')) {
                $paletteManipulator->addField('update_page_form', 'update_legend');
            }
            if ($dcaManipulator->hasField('update_page_form_sent')) {
                $paletteManipulator->addField('update_page_form_sent', 'update_legend');
            }
            if ($dcaManipulator->hasField('update_module')) {
                $paletteManipulator->addField('update_module', 'update_legend');
            }
            if ($dcaManipulator->hasField('update_module_list')) {
                $paletteManipulator->addField('update_module_list', 'update_legend');
            }
            if ($dcaManipulator->hasField('update_module_reader')) {
                $paletteManipulator->addField('update_module_reader', 'update_legend');
            }
            if ($dcaManipulator->hasField('update_module_calendar')) {
                $paletteManipulator->addField('update_module_calendar', 'update_legend');
            }
            if ($dcaManipulator->hasField('update_user_group')) {
                $paletteManipulator->addField('update_user_group', 'update_legend');
            }
            if ($dcaManipulator->hasField('update_faq_category')) {
                $paletteManipulator->addField('update_faq_category', 'update_legend');
            }
            if ($dcaManipulator->hasField('update_calendar')) {
                $paletteManipulator->addField('update_calendar', 'update_legend');
            }
            if ($dcaManipulator->hasField('update_news_archive')) {
                $paletteManipulator->addField('update_news_archive', 'update_legend');
            }
            if ($dcaManipulator->hasField('update_form')) {
                $paletteManipulator->addField('update_form', 'update_legend');
            }
            if ($dcaManipulator->hasField('update_notification')) {
                $paletteManipulator->addField('update_notification', 'update_legend');
            }
            if ($dcaManipulator->hasField('update_user_group_permission')) {
                $paletteManipulator->addField('update_user_group_permission', 'update_legend');
            }
            $paletteManipulator->applyToPalette('default', ConfigurationItemModel::getTable());
        }
    }

    // public function contaoLayoutToUpdateLoadCallback($value, DataContainer $dc)
    // {
    //     $arrOptions = [];

    //     $objItem = ConfigurationItemModel::findOneById($dc->activeRecord->id);
    //     /** @var ConfigurationModel */
    //     $objConfiguration = $objItem->getRelated('pid');
    //     $layouts = LayoutModel::findByPid($objConfiguration->contao_theme);
    //     if (!$layouts) {
    //         return [];
    //     }

    //     $arrLayouts = $layouts->fetchAll();

    //     switch ($dc->activeRecord->type) {
    //         case ConfigurationItemModel::TYPE_MODULE_WEM_SG_HEADER:
    //             foreach ($arrLayouts as $layout) {
    //                 $arrModules = StringUtil::deserialize($layout['modules'], true);
    //                 foreach ($arrModules as $moduleLayout) {
    //                     if('header' === $moduleLayout['col']){
    //                         $objModule = ModuleModel::findById($moduleLayout['mod']);
    //                     }
    //                 }
    //             }
    //         break;
    //         case ConfigurationItemModel::TYPE_MODULE_WEM_SG_FOOTER:
    //         break;
    //         case ConfigurationItemModel::TYPE_MODULE_BREADCRUMB:
    //         break;
    //     }

    //     return $arrOptions;
    // }
}
