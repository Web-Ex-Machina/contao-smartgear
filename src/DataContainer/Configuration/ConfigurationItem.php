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
use Contao\Input;
use Contao\LayoutModel;
use Contao\Message;
use Contao\ModuleModel;
use Contao\PageModel;
use WEM\SmartgearBundle\Classes\Dca\Manipulator as DCAManipulator;
use WEM\SmartgearBundle\Classes\StringUtil;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Classes\Utils\ArticleUtil;
use WEM\SmartgearBundle\Classes\Utils\ContentUtil;
use WEM\SmartgearBundle\Classes\Utils\LayoutUtil;
use WEM\SmartgearBundle\Classes\Utils\ModuleUtil;
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
        }

        $objItem->save();
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

    public function manageUserGroupAdministrators(ConfigurationItemModel $objItem, bool $blnForceUserGroupUpdate, DataContainer $dc): ConfigurationItemModel
    {
        if (!empty($objItem->user_group_name)
        && (0 === (int) $dc->activeRecord->tstamp || $blnForceUserGroupUpdate || (0 !== (int) $dc->activeRecord->tstamp && empty($objItem->contao_user_group))) // create mode or forced update
        ) {
            $objUserGroup = UserGroupUtil::createUserGroupAdministrators($objItem->user_group_name, $objItem->contao_user_group ? ['id' => $objItem->contao_user_group] : []);

            $objItem->contao_user_group = $objUserGroup->id;
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

    public function manageMixedSitemap(ConfigurationItemModel $objItem, bool $blnForceModuleUpdate, bool $blnForcePageUpdate, DataContainer $dc): ConfigurationItemModel
    {
        $objItem = $this->manageModuleSitemap($objItem, $blnForceModuleUpdate, $dc);

        return $this->managePageSitemap($objItem, $blnForcePageUpdate, $dc);
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

    public function checkLinkedContaoContentUpdated(DataContainer $dc): void
    {
        if (!$dc->id) {
            return;
        }
        $objItem = ConfigurationItemModel::findOneById($dc->id);

        $dcaManipulator = DCAManipulator::create(ConfigurationItemModel::getTable());
        $addFields = false;

        if ($objItem->contao_module) {
            $objModule = $objItem->getRelated('contao_module');
            if ($objModule
            && (int) $objModule->tstamp > (int) $objItem->tstamp
            ) {
                Message::addInfo('Le module "'.$objModule->name.'" a été mis à jour depuis le '.Date::parse(Config::get('datimFormat'), (int) $objItem->tstamp).'.');
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

        if ($objItem->contao_page) {
            $objPage = $objItem->getRelated('contao_page');
            if ($objPage
            && (int) $objPage->tstamp > (int) $objItem->tstamp
            ) {
                Message::addInfo('La page "'.$objPage->title.'" a été mis à jour depuis le '.Date::parse(Config::get('datimFormat'), (int) $objItem->tstamp).'.');
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

        if ($objItem->contao_user_group) {
            $objUserGroup = $objItem->getRelated('contao_user_group');
            if ($objUserGroup
            && (int) $objUserGroup->tstamp > (int) $objItem->tstamp
            ) {
                Message::addInfo('Le groupe d\'utilisateurs "'.$objUserGroup->name.'" a été mis à jour depuis le '.Date::parse(Config::get('datimFormat'), (int) $objItem->tstamp).'.');
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

        $paletteManipulator = PaletteManipulator::create();
        if ($addFields) {
            $paletteManipulator->addLegend('update_legend');
            if ($dcaManipulator->hasField('update_module')) {
                $paletteManipulator->addField('update_module', 'update_legend');
            }
            if ($dcaManipulator->hasField('update_page')) {
                $paletteManipulator->addField('update_page', 'update_legend');
            }
            if ($dcaManipulator->hasField('update_user_group')) {
                $paletteManipulator->addField('update_user_group', 'update_legend');
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
