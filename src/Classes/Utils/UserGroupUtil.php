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

use Contao\ImageSizeModel;
use Contao\UserGroupModel;
use WEM\SmartgearBundle\Classes\UserGroupModelUtil;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Model\Configuration\Configuration;
use WEM\SmartgearBundle\Model\Configuration\ConfigurationItem;

class UserGroupUtil
{
    /**
     * Shortcut for UserGroup creation.
     */
    public static function createUserGroupAdministrators(string $strName, ?array $arrData = []): UserGroupModel
    {
        // Create the UserGroup
        $objUserGroup = isset($arrData['id']) ? UserGroupModel::findById($arrData['id']) ?? new UserGroupModel() : new UserGroupModel();
        $objUserGroup->tstamp = time();
        $objUserGroup->name = $strName;
        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);
        $userGroupManipulator
            ->addAllowedModules(['page', 'article', 'form', 'files', 'nc_notifications', 'user', 'log', 'maintenance', 'wem_sg_social_link', 'wem_sg_social_link_config_categories', 'wem_sg_dashboard'])
            ->addAllowedFields(self::getCorePermissions())
            ->addAllowedImageSizes(['proportional'])
            // ->addAllowedFilemounts([$objFolderClientFiles->uuid, $objFolderClientLogos->uuid])
            ->addAllowedFileOperationPermissions(['f1', 'f2', 'f3', 'f4'])
            ->addAllowedElements([
                'headline',
                'text',
                'html',
                'table',
                'rsce_listIcons',
                'rsce_quote',
                'accordionStart',
                'accordionStop',
                'hyperlink',
                'image',
                'player',
                'youtube',
                'vimeo',
                'downloads',
                'module',
                // 'rsce_timeline',
                'grid-start',
                'grid-stop',
                'rsce_accordion',
                'rsce_counter',
                'rsce_hero',
                'rsce_heroStart',
                'rsce_heroStop',
                'rsce_ratings',
                'rsce_priceCards',
                'rsce_slider',
                'rsce_tabs',
                // 'rsce_testimonials',
                'rsce_pdfViewer',
                'rsce_blockCard',
                'form',
                'gallery',
            ])
            ->removeAllowedElements(['rsce_timeline', 'rsce_testimonials'])
            ->addAllowedFormFields(array_keys($GLOBALS['TL_FFL']))
            ->addAllowedFieldsByTables(['tl_form', 'tl_form_field'])
            ->addAllowedFormPermissions(['create', 'delete'])
        ;
        $objUserGroup->modules = serialize(['page', 'article', 'form', 'files', 'nc_notifications', 'user', 'log', 'maintenance', 'wem_sg_social_link', 'wem_sg_social_link_config_categories', 'wem_sg_dashboard']);
        $objUserGroup = $userGroupManipulator->getUserGroup();

        // Now we get the default values, get the arrData table
        if (!empty($arrData)) {
            foreach ($arrData as $k => $v) {
                $objUserGroup->$k = $v;
            }
        }

        $objUserGroup->save();

        // Return the model
        return $objUserGroup;
    }

    /**
     * Shortcut for UserGroup creation.
     */
    public static function createUserGroupRedactors(string $strName, $arrData = []): UserGroupModel
    {
        // Create the UserGroup
        $objUserGroup = isset($arrData['id']) ? UserGroupModel::findById($arrData['id']) ?? new UserGroupModel() : new UserGroupModel();
        $objUserGroup->tstamp = time();
        $objUserGroup->name = $strName;
        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);
        $userGroupManipulator
            ->addAllowedModules(['article', 'files', 'form', 'wem_sg_social_link', 'wem_sg_dashboard'])
            ->addAllowedFields(self::getCorePermissions())
            ->addAllowedImageSizes(['proportional'])
            // ->addAllowedFilemounts([$objFolderClientFiles->uuid, $objFolderClientLogos->uuid])
            ->addAllowedFileOperationPermissions(['f1', 'f2', 'f3', 'f4'])
            ->addAllowedElements([
                'headline',
                'text',
                'html',
                'table',
                'rsce_listIcons',
                'rsce_quote',
                'accordionStart',
                'accordionStop',
                'hyperlink',
                'image',
                'player',
                'youtube',
                'vimeo',
                'downloads',
                'module',
                // 'rsce_timeline',
                'grid-start',
                'grid-stop',
                'rsce_accordion',
                'rsce_counter',
                'rsce_hero',
                'rsce_heroStart',
                'rsce_heroStop',
                'rsce_ratings',
                'rsce_priceCards',
                'rsce_slider',
                'rsce_tabs',
                // 'rsce_testimonials',
                'rsce_pdfViewer',
                'rsce_blockCard',
                'form',
                'gallery',
            ])
            ->removeAllowedElements(['rsce_timeline', 'rsce_testimonials'])
            ->addAllowedFormFields(array_keys($GLOBALS['TL_FFL']))
            ->addAllowedFieldsByTables(['tl_form', 'tl_form_field'])
            ->addAllowedFormPermissions(['create', 'delete'])
        ;
        $objUserGroup = $userGroupManipulator->getUserGroup();

        // Now we get the default values, get the arrData table
        if (!empty($arrData)) {
            foreach ($arrData as $k => $v) {
                $objUserGroup->$k = $v;
            }
        }

        $objUserGroup->save();

        // Return the model
        return $objUserGroup;
    }

    /**
     * Return permissions concerned by this component.
     *
     * @return array
     */
    public static function getCorePermissions()
    {
        return [
            0 => 'tl_article::title',
            1 => 'tl_article::alias',
            2 => 'tl_article::cssID',
            3 => 'tl_article::published',
            4 => 'tl_article::start',
            5 => 'tl_article::stop',
            6 => 'tl_content::type',
            7 => 'tl_content::headline',
            8 => 'tl_content::text',
            9 => 'tl_content::addImage',
            10 => 'tl_content::overwriteMeta',
            11 => 'tl_content::singleSRC',
            12 => 'tl_content::alt',
            13 => 'tl_content::imageTitle',
            14 => 'tl_content::size',
            15 => 'tl_content::imagemargin',
            16 => 'tl_content::imageUrl',
            17 => 'tl_content::fullsize',
            18 => 'tl_content::caption',
            19 => 'tl_content::floating',
            20 => 'tl_content::html',
            21 => 'tl_content::listtype',
            22 => 'tl_content::listitems',
            23 => 'tl_content::tableitems',
            24 => 'tl_content::summary',
            25 => 'tl_content::thead',
            26 => 'tl_content::tfoot',
            27 => 'tl_content::tleft',
            28 => 'tl_content::sortable',
            29 => 'tl_content::sortIndex',
            30 => 'tl_content::sortOrder',
            31 => 'tl_content::mooHeadline',
            32 => 'tl_content::mooStyle',
            33 => 'tl_content::mooClasses',
            34 => 'tl_content::highlight',
            35 => 'tl_content::code',
            36 => 'tl_content::url',
            37 => 'tl_content::target',
            38 => 'tl_content::overwriteLink',
            39 => 'tl_content::titleText',
            40 => 'tl_content::linkTitle',
            41 => 'tl_content::embed',
            42 => 'tl_content::rel',
            43 => 'tl_content::useImage',
            44 => 'tl_content::multiSRC',
            45 => 'tl_content::useHomeDir',
            46 => 'tl_content::perRow',
            47 => 'tl_content::perPage',
            48 => 'tl_content::numberOfItems',
            49 => 'tl_content::sortBy',
            50 => 'tl_content::metaIgnore',
            51 => 'tl_content::galleryTpl',
            52 => 'tl_content::customTpl',
            53 => 'tl_content::playerSRC',
            54 => 'tl_content::youtube',
            55 => 'tl_content::vimeo',
            56 => 'tl_content::posterSRC',
            57 => 'tl_content::playerSize',
            58 => 'tl_content::playerOptions',
            59 => 'tl_content::playerStart',
            60 => 'tl_content::playerStop',
            61 => 'tl_content::playerCaption',
            62 => 'tl_content::playerAspect',
            63 => 'tl_content::playerPreload',
            64 => 'tl_content::playerColor',
            65 => 'tl_content::youtubeOptions',
            66 => 'tl_content::vimeoOptions',
            67 => 'tl_content::sliderDelay',
            68 => 'tl_content::sliderSpeed',
            69 => 'tl_content::sliderStartSlide',
            70 => 'tl_content::sliderContinuous',
            71 => 'tl_content::cteAlias',
            72 => 'tl_content::articleAlias',
            73 => 'tl_content::article',
            74 => 'tl_content::form',
            75 => 'tl_content::module',
            76 => 'tl_content::protected',
            77 => 'tl_content::groups',
            78 => 'tl_content::guests',
            79 => 'tl_content::cssID',
            80 => 'tl_content::invisible',
            81 => 'tl_content::start',
            82 => 'tl_content::stop',
            83 => 'tl_content::rsce_data',
            84 => 'tl_content::grid_preset',
            85 => 'tl_content::grid_row_class',
            86 => 'tl_content::grid_rows',
            87 => 'tl_content::grid_cols',
            88 => 'tl_content::grid_items',
            89 => 'tl_nc_language::language',
            90 => 'tl_nc_language::fallback',
            91 => 'tl_nc_language::recipients',
            92 => 'tl_nc_language::attachment_tokens',
            93 => 'tl_nc_language::attachments',
            94 => 'tl_nc_language::attachment_templates',
            95 => 'tl_nc_language::email_sender_name',
            96 => 'tl_nc_language::email_sender_address',
            97 => 'tl_nc_language::email_recipient_cc',
            98 => 'tl_nc_language::email_recipient_bcc',
            99 => 'tl_nc_language::email_replyTo',
            100 => 'tl_nc_language::email_subject',
            101 => 'tl_nc_language::email_mode',
            102 => 'tl_nc_language::email_text',
            103 => 'tl_nc_language::email_html',
            104 => 'tl_nc_language::email_external_images',
            105 => 'tl_nc_language::file_name',
            106 => 'tl_nc_language::file_storage_mode',
            107 => 'tl_nc_language::file_content',
            108 => 'tl_page::title',
            109 => 'tl_page::alias',
            110 => 'tl_page::type',
            111 => 'tl_page::pageTitle',
            112 => 'tl_page::language',
            113 => 'tl_page::robots',
            114 => 'tl_page::description',
            115 => 'tl_page::redirect',
            116 => 'tl_page::jumpTo',
            117 => 'tl_page::redirectBack',
            118 => 'tl_page::url',
            119 => 'tl_page::target',
            120 => 'tl_page::noSearch',
            121 => 'tl_page::sitemap',
            122 => 'tl_page::hide',
            123 => 'tl_page::published',
            124 => 'tl_page::start',
            125 => 'tl_page::stop',
            126 => 'tl_user::username',
            127 => 'tl_user::name',
            128 => 'tl_user::email',
            129 => 'tl_user::language',
            130 => 'tl_user::backendTheme',
            131 => 'tl_user::fullscreen',
            132 => 'tl_user::uploader',
            133 => 'tl_user::showHelp',
            134 => 'tl_user::thumbnails',
            135 => 'tl_user::useRTE',
            136 => 'tl_user::useCE',
            137 => 'tl_user::password',
            138 => 'tl_user::pwChange',
            139 => 'tl_user::admin',
            140 => 'tl_user::groups',
            141 => 'tl_user::inherit',
            142 => 'tl_user::modules',
            143 => 'tl_user::themes',
            144 => 'tl_user::pagemounts',
            145 => 'tl_user::alpty',
            146 => 'tl_user::filemounts',
            147 => 'tl_user::fop',
            148 => 'tl_user::imageSizes',
            149 => 'tl_user::forms',
            150 => 'tl_user::formp',
            151 => 'tl_user::amg',
            152 => 'tl_user::disable',
            153 => 'tl_user::start',
            154 => 'tl_user::stop',
            155 => 'tl_user::session',
            156 => 'tl_content::grid_gap',
            157 => 'tl_article::styleManager',
            158 => 'tl_content::styleManager',
        ];
    }

    public static function updateAddUserGroupSettingsAccordingToConfiguration(UserGroupModel $objUserGroup, Configuration $objConfiguration): UserGroupModel
    {
        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);

        if ($objConfiguration->contao_module_sitemap) {
            $userGroupManipulator
                ->addAllowedModules([$objConfiguration->contao_module_sitemap])
            ;
        }
        if ($objConfiguration->contao_page_root) {
            $userGroupManipulator
                ->addAllowedPagemounts([$objConfiguration->contao_page_root])
            ;
        }
        if ($objConfiguration->contao_page_404) {
            $userGroupManipulator
                ->addAllowedPagemounts([$objConfiguration->contao_page_404])
            ;
        }
        if ($objConfiguration->contao_page_home) {
            $userGroupManipulator
                ->addAllowedPagemounts([$objConfiguration->contao_page_home])
            ;
        }
        Util::log(__METHOD__);
        Util::log('$objConfiguration->contao_theme : '.$objConfiguration->contao_theme);
        if ($objConfiguration->contao_theme) {
            $imageSizes = ImageSizeModel::findBy('pid', $objConfiguration->contao_theme);
            if ($imageSizes) {
                while ($imageSizes->next()) {
                    Util::log($imageSizes->id);
                    $userGroupManipulator
                        ->addAllowedImageSizes([$imageSizes->id])
                    ;
                }
            }
        }

        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->newp = serialize(['create', 'delete']);
        $objUserGroup->save();

        return $objUserGroup;
    }

    public static function updateAddUserGroupSettingsAccordingToConfigurationItem(UserGroupModel $objUserGroup, ConfigurationItem $objConfigurationItem): UserGroupModel
    {
        switch ($objConfigurationItem->type) {
            case ConfigurationItem::TYPE_PAGE_SITEMAP:
                $objUserGroup = self::updateAddUserGroupSettingsPageSitemap($objUserGroup, $objConfigurationItem);
            break;
            case ConfigurationItem::TYPE_PAGE_LEGAL_NOTICE:
                $objUserGroup = self::updateAddUserGroupSettingsPageLegalNotice($objUserGroup, $objConfigurationItem);
            break;
            case ConfigurationItem::TYPE_PAGE_PRIVACY_POLITICS:
                $objUserGroup = self::updateAddUserGroupSettingsPagePrivacyPolitics($objUserGroup, $objConfigurationItem);
            break;
            case ConfigurationItem::TYPE_MODULE_BREADCRUMB:
                $objUserGroup = self::updateAddUserGroupSettingsModuleBreadcrumb($objUserGroup, $objConfigurationItem);
            break;
            case ConfigurationItem::TYPE_MODULE_WEM_SG_HEADER:
                $objUserGroup = self::updateAddUserGroupSettingsModuleWemSgHeader($objUserGroup, $objConfigurationItem);
            break;
            case ConfigurationItem::TYPE_MODULE_WEM_SG_FOOTER:
                $objUserGroup = self::updateAddUserGroupSettingsModuleWemSgFooter($objUserGroup, $objConfigurationItem);
            break;
            case ConfigurationItem::TYPE_MODULE_WEM_SG_SOCIAL_NETWORKS:
                $objUserGroup = self::updateAddUserGroupSettingsModuleWemSgSocialNetworks($objUserGroup, $objConfigurationItem);
            break;
            case ConfigurationItem::TYPE_MIXED_SITEMAP:
                $objUserGroup = self::updateAddUserGroupSettingsSitemap($objUserGroup, $objConfigurationItem);
            break;
            case ConfigurationItem::TYPE_MIXED_BLOG:
                $objUserGroup = self::updateAddUserGroupSettingsBlog($objUserGroup, $objConfigurationItem);
            break;
            case ConfigurationItem::TYPE_MIXED_EVENTS:
                $objUserGroup = self::updateAddUserGroupSettingsEvents($objUserGroup, $objConfigurationItem);
            break;
            case ConfigurationItem::TYPE_MIXED_FAQ:
                $objUserGroup = self::updateAddUserGroupSettingsFaq($objUserGroup, $objConfigurationItem);
            break;
            case ConfigurationItem::TYPE_MIXED_FORM_CONTACT:
                $objUserGroup = self::updateAddUserGroupSettingsFormContact($objUserGroup, $objConfigurationItem);
            break;
        }

        return $objUserGroup;
    }

    public static function updateAddUserGroupSettingsPageSitemap(UserGroupModel $objUserGroup, ConfigurationItem $objConfigurationItem): UserGroupModel
    {
        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);
        $userGroupManipulator
            ->addAllowedPagemounts([$objConfigurationItem->contao_page])
        ;

        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->save();

        return $objUserGroup;
    }

    public static function updateAddUserGroupSettingsPageLegalNotice(UserGroupModel $objUserGroup, ConfigurationItem $objConfigurationItem): UserGroupModel
    {
        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);
        $userGroupManipulator
            ->addAllowedPagemounts([$objConfigurationItem->contao_page])
        ;

        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->save();

        return $objUserGroup;
    }

    public static function updateAddUserGroupSettingsPagePrivacyPolitics(UserGroupModel $objUserGroup, ConfigurationItem $objConfigurationItem): UserGroupModel
    {
        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);
        $userGroupManipulator
            ->addAllowedPagemounts([$objConfigurationItem->contao_page])
        ;

        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->save();

        return $objUserGroup;
    }

    public static function updateAddUserGroupSettingsModuleBreadcrumb(UserGroupModel $objUserGroup, ConfigurationItem $objConfigurationItem): UserGroupModel
    {
        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);
        $userGroupManipulator
            ->addAllowedModules([$objConfigurationItem->contao_module])
        ;

        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->save();

        return $objUserGroup;
    }

    public static function updateAddUserGroupSettingsModuleWemSgHeader(UserGroupModel $objUserGroup, ConfigurationItem $objConfigurationItem): UserGroupModel
    {
        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);
        $userGroupManipulator
            ->addAllowedModules([$objConfigurationItem->contao_module])
        ;

        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->save();

        return $objUserGroup;
    }

    public static function updateAddUserGroupSettingsModuleWemSgFooter(UserGroupModel $objUserGroup, ConfigurationItem $objConfigurationItem): UserGroupModel
    {
        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);
        $userGroupManipulator
            ->addAllowedModules([$objConfigurationItem->contao_module])
        ;

        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->save();

        return $objUserGroup;
    }

    public static function updateAddUserGroupSettingsModuleWemSgSocialNetworks(UserGroupModel $objUserGroup, ConfigurationItem $objConfigurationItem): UserGroupModel
    {
        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);
        $userGroupManipulator
            ->addAllowedModules([$objConfigurationItem->contao_module])
        ;

        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->save();

        return $objUserGroup;
    }

    public static function updateAddUserGroupSettingsSitemap(UserGroupModel $objUserGroup, ConfigurationItem $objConfigurationItem): UserGroupModel
    {
        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);
        $userGroupManipulator
            ->addAllowedPagemounts([$objConfigurationItem->contao_page])
        ;

        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->save();

        return $objUserGroup;
    }

    public static function updateAddUserGroupSettingsBlog(UserGroupModel $objUserGroup, ConfigurationItem $objConfigurationItem): UserGroupModel
    {
        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);
        $userGroupManipulator
            ->addAllowedModules(['news'])
            ->addAllowedNewsArchive([$objConfigurationItem->contao_news_archive])
            // ->addAllowedFilemounts([$objFolder->uuid])
            ->addAllowedFieldsByTables(['tl_news'])
            ->addAllowedPagemounts($objConfigurationItem->contao_page)
        ;

        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->newp = serialize(['create', 'delete']);
        $objUserGroup->save();

        return $objUserGroup;
    }

    public static function updateAddUserGroupSettingsEvents(UserGroupModel $objUserGroup, ConfigurationItem $objConfigurationItem): UserGroupModel
    {
        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);
        $userGroupManipulator
            ->addAllowedModules(['calendar'])
            ->addAllowedCalendar([$objConfigurationItem->contao_calendar])
            // ->addAllowedFilemounts([$objFolder->uuid])
            ->addAllowedFieldsByTables(['tl_calendar_events'])
            ->addAllowedPagemounts($objConfigurationItem->contao_page)
        ;
        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->calendarp = serialize(['create', 'delete']);
        $objUserGroup->save();

        return $objUserGroup;
    }

    public static function updateAddUserGroupSettingsFaq(UserGroupModel $objUserGroup, ConfigurationItem $objConfigurationItem): UserGroupModel
    {
        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);
        $userGroupManipulator
            ->addAllowedModules(['faq'])
            ->addAllowedFaq([$objConfigurationItem->contao_faq_category])
            // ->addAllowedFilemounts([$objFolder->uuid])
            ->addAllowedFieldsByTables(['tl_faq'])
            ->addAllowedPagemounts($objConfigurationItem->contao_page)
            // ->addAllowedModules(Module::getTypesByIds($faqConfig->getContaoModulesIds()))
        ;
        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->faqp = serialize(['create', 'delete']);
        $objUserGroup->save();

        return $objUserGroup;
    }

    public static function updateAddUserGroupSettingsFormContact(UserGroupModel $objUserGroup, ConfigurationItem $objConfigurationItem): UserGroupModel
    {
        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);
        $userGroupManipulator
            ->addAllowedModules(['form'])
            ->addAllowedForms([$objConfigurationItem->contao_form])
            ->addAllowedFormFields(['text', 'textarea', 'captcha', 'submit'])
            ->addAllowedFieldsByTables(['tl_form', 'tl_form_field'])
            ->addAllowedPagemounts([$objConfigurationItem->contao_page_form, $objConfigurationItem->contao_page_form_sent])
        ;
        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->formp = serialize(['create', 'delete']);
        $objUserGroup->save();

        return $objUserGroup;
    }

    public static function updateRemoveUserGroupSettingsAccordingToConfigurationItem(UserGroupModel $objUserGroup, ConfigurationItem $objConfigurationItem): UserGroupModel
    {
        $objUserGroup = self::updateRemoveUserGroupSettingsCommon($objUserGroup, $objConfigurationItem);
        switch ($objConfigurationItem->type) {
            case ConfigurationItem::TYPE_MIXED_BLOG:
                $objUserGroup = self::updateRemoveUserGroupSettingsBlog($objUserGroup, $objConfigurationItem);
            break;
            case ConfigurationItem::TYPE_MIXED_EVENTS:
                $objUserGroup = self::updateRemoveUserGroupSettingsEvents($objUserGroup, $objConfigurationItem);
            break;
            case ConfigurationItem::TYPE_MIXED_FAQ:
                $objUserGroup = self::updateRemoveUserGroupSettingsFaq($objUserGroup, $objConfigurationItem);
            break;
            case ConfigurationItem::TYPE_MIXED_FORM_CONTACT:
                $objUserGroup = self::updateRemoveUserGroupSettingsFormContact($objUserGroup, $objConfigurationItem);
            break;
        }

        return $objUserGroup;
    }

    public static function updateRemoveUserGroupSettingsBlog(UserGroupModel $objUserGroup, ConfigurationItem $objConfigurationItem): UserGroupModel
    {
        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);

        $objUserGroup = $userGroupManipulator->getUserGroup();
        // $objUserGroup->newp = serialize(['create', 'delete']); // remove those rights ?
        $objUserGroup->save();

        return $objUserGroup;
    }

    public static function updateRemoveUserGroupSettingsEvents(UserGroupModel $objUserGroup, ConfigurationItem $objConfigurationItem): UserGroupModel
    {
        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);

        $objUserGroup = $userGroupManipulator->getUserGroup();
        // $objUserGroup->calendarp = serialize(['create', 'delete']); // remove those rights ?
        $objUserGroup->save();

        return $objUserGroup;
    }

    public static function updateRemoveUserGroupSettingsFaq(UserGroupModel $objUserGroup, ConfigurationItem $objConfigurationItem): UserGroupModel
    {
        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);

        $objUserGroup = $userGroupManipulator->getUserGroup();
        // $objUserGroup->faqp = serialize(['create', 'delete']); // remove those rights ?
        $objUserGroup->save();

        return $objUserGroup;
    }

    public static function updateRemoveUserGroupSettingsFormContact(UserGroupModel $objUserGroup, ConfigurationItem $objConfigurationItem): UserGroupModel
    {
        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);

        $objUserGroup = $userGroupManipulator->getUserGroup();
        // $objUserGroup->formp = serialize(['create', 'delete']); // remove those rights ?
        $objUserGroup->save();

        return $objUserGroup;
    }

    public static function updateRemoveUserGroupSettingsCommon(UserGroupModel $objUserGroup, ConfigurationItem $objConfigurationItem): UserGroupModel
    {
        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);

        // PAGES
        $nbOtherItemSameProperty = ConfigurationItem::countItems([
            'not_id' => $objConfigurationItem->id,
            // 'pid' => $objConfigurationItem->pid,
            'contao_page' => $objConfigurationItem->contao_page,
        ]);
        if (0 === $nbOtherItemSameProperty) {
            $userGroupManipulator
                ->removeAllowedPagemounts([$objConfigurationItem->contao_page])
            ;
        }
        $nbOtherItemSameProperty = ConfigurationItem::countItems([
            'not_id' => $objConfigurationItem->id,
            // 'pid' => $objConfigurationItem->pid,
            'contao_page_form' => $objConfigurationItem->contao_page_form,
        ]);
        if (0 === $nbOtherItemSameProperty) {
            $userGroupManipulator
                ->removeAllowedPagemounts([$objConfigurationItem->contao_page_form])
            ;
        }
        $nbOtherItemSameProperty = ConfigurationItem::countItems([
            'not_id' => $objConfigurationItem->id,
            // 'pid' => $objConfigurationItem->pid,
            'contao_page_form_sent' => $objConfigurationItem->contao_page_form_sent,
        ]);
        if (0 === $nbOtherItemSameProperty) {
            $userGroupManipulator
                ->removeAllowedPagemounts([$objConfigurationItem->contao_page_form_sent])
            ;
        }

        // MODULES
        $nbOtherItemSameProperty = ConfigurationItem::countItems([
            'not_id' => $objConfigurationItem->id,
            // 'pid' => $objConfigurationItem->pid,
            'contao_module' => $objConfigurationItem->contao_module,
        ]);
        if (0 === $nbOtherItemSameProperty) {
            $userGroupManipulator
                ->removeAllowedModules([$objConfigurationItem->contao_module])
            ;
        }
        $nbOtherItemSameProperty = ConfigurationItem::countItems([
            'not_id' => $objConfigurationItem->id,
            // 'pid' => $objConfigurationItem->pid,
            'contao_module_reader' => $objConfigurationItem->contao_module_reader,
        ]);
        if (0 === $nbOtherItemSameProperty) {
            $userGroupManipulator
                ->removeAllowedModules([$objConfigurationItem->contao_module_reader])
            ;
        }
        $nbOtherItemSameProperty = ConfigurationItem::countItems([
            'not_id' => $objConfigurationItem->id,
            // 'pid' => $objConfigurationItem->pid,
            'contao_module_list' => $objConfigurationItem->contao_module_list,
        ]);
        if (0 === $nbOtherItemSameProperty) {
            $userGroupManipulator
                ->removeAllowedModules([$objConfigurationItem->contao_module_list])
            ;
        }
        $nbOtherItemSameProperty = ConfigurationItem::countItems([
            'not_id' => $objConfigurationItem->id,
            // 'pid' => $objConfigurationItem->pid,
            'contao_module_calendar' => $objConfigurationItem->contao_module_calendar,
        ]);
        if (0 === $nbOtherItemSameProperty) {
            $userGroupManipulator
                ->removeAllowedModules([$objConfigurationItem->contao_module_calendar])
            ;
        }
        $nbOtherItemSameProperty = ConfigurationItem::countItems([
            'not_id' => $objConfigurationItem->id,
            // 'pid' => $objConfigurationItem->pid,
            'contao_module' => $objConfigurationItem->contao_module,
        ]);
        if (0 === $nbOtherItemSameProperty) {
            $userGroupManipulator
                ->removeAllowedModules([$objConfigurationItem->contao_module])
            ;
        }
        // FORMS
        $nbOtherItemSameProperty = ConfigurationItem::countItems([
            'not_id' => $objConfigurationItem->id,
            // 'pid' => $objConfigurationItem->pid,
            'contao_form' => $objConfigurationItem->contao_form,
        ]);
        if (0 === $nbOtherItemSameProperty) {
            $userGroupManipulator
                ->removeAllowedForms([$objConfigurationItem->contao_form])
            ;
        }
        // NEWS ARCHIVE
        $nbOtherItemSameProperty = ConfigurationItem::countItems([
            'not_id' => $objConfigurationItem->id,
            // 'pid' => $objConfigurationItem->pid,
            'contao_news_archive' => $objConfigurationItem->contao_news_archive,
        ]);
        if (0 === $nbOtherItemSameProperty) {
            $userGroupManipulator
                ->removeAllowedNewsArchive([$objConfigurationItem->contao_news_archive])
            ;
        }
        // FAQ CATEGORY
        $nbOtherItemSameProperty = ConfigurationItem::countItems([
            'not_id' => $objConfigurationItem->id,
            // 'pid' => $objConfigurationItem->pid,
            'contao_faq_category' => $objConfigurationItem->contao_faq_category,
        ]);
        if (0 === $nbOtherItemSameProperty) {
            $userGroupManipulator
                ->removeAllowedFaq([$objConfigurationItem->contao_faq_category])
            ;
        }
        // CALENDAR
        $nbOtherItemSameProperty = ConfigurationItem::countItems([
            'not_id' => $objConfigurationItem->id,
            // 'pid' => $objConfigurationItem->pid,
            'contao_calendar' => $objConfigurationItem->contao_calendar,
        ]);
        if (0 === $nbOtherItemSameProperty) {
            $userGroupManipulator
                ->removeAllowedCalendar([$objConfigurationItem->contao_calendar])
            ;
        }

        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->save();

        return $objUserGroup;
    }
}
