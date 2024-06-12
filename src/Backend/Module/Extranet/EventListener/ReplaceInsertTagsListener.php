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

namespace WEM\SmartgearBundle\Backend\Module\Extranet\EventListener;

use WEM\SmartgearBundle\Classes\Backend\Component\EventListener\ReplaceInsertTagsListener as AbstractReplaceInsertTagsListener;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Config\Module\Extranet\Extranet as ExtranetConfig;

class ReplaceInsertTagsListener extends AbstractReplaceInsertTagsListener
{
    /** @var CoreConfigurationManager */
    protected $coreConfigurationManager;

    public function __construct(
        CoreConfigurationManager $coreConfigurationManager
    ) {
        $this->coreConfigurationManager = $coreConfigurationManager;
    }

    /**
     * Handles Smartgear insert tags.
     *
     * @see https://docs.contao.org/dev/reference/hooks/replaceInsertTags/
     *
     * @param string $insertTag   the unknown insert tag
     * @param bool   $useCache    indicates if we are supposed to cache
     * @param string $cachedValue the cached replacement for this insert tag (if there is any)
     * @param array  $flags       an array of flags used with this insert tag
     * @param array  $tags        contains the result of spliting the page’s content in order to replace the insert tags
     * @param array  $cache       the cached replacements of insert tags found on the page so far
     * @param int    $_rit        counter used while iterating over the parts in $tags
     * @param int    $_cnt        number of elements in $tags
     *
     * @return string|false if the tags isn't managed by this class, return false
     */
    public function onReplaceInsertTags(
        string $insertTag,
        bool $useCache,
        string $cachedValue,
        array $flags,
        array $tags,
        array $cache,
        int $_rit,
        int $_cnt
    ) {
        $elements = explode('::', $insertTag);
        $key = strtolower($elements[0]);
        if ('sg' === $key && str_starts_with($elements[1], 'extranet')) {
            return static::NOT_HANDLED;
            /** @var CoreConfig */
            $config = $this->coreConfigurationManager->load();
            /** @var ExtranetConfig */
            $extranetConfig = $config->getSgExtranet();

            if (!$extranetConfig->getSgInstallComplete()) {
                return static::NOT_HANDLED;
            }

            return match ($elements[1]) {
                'extranet_installComplete' => $extranetConfig->getSgInstallComplete() ? '1' : '0',
                'extranet_extranetFolder' => $extranetConfig->getSgExtranetFolder(),
                'extranet_canSubscribe' => $extranetConfig->getSgCanSubscribe() ? '1' : '0',
                'extranet_memberExample' => $extranetConfig->getSgMemberExample(),
                'extranet_memberGroupMembers' => $extranetConfig->getSgMemberGroupMembers(),
                'extranet_memberGroupMembersTitle' => $extranetConfig->getSgMemberGroupMembersTitle(),
                'extranet_pageExtranetTitle' => $extranetConfig->getSgPageExtranetTitle(),
                'extranet_pageExtranet' => $extranetConfig->getSgPageExtranet(),
                'extranet_page401' => $extranetConfig->getSgPage401(),
                'extranet_page403' => $extranetConfig->getSgPage403(),
                'extranet_pageContent' => $extranetConfig->getSgPageContent(),
                'extranet_pageData' => $extranetConfig->getSgPageData(),
                'extranet_pageDataConfirm' => $extranetConfig->getSgPageDataConfirm(),
                'extranet_pagePassword' => $extranetConfig->getSgPagePassword(),
                'extranet_pagePasswordConfirm' => $extranetConfig->getSgPagePasswordConfirm(),
                'extranet_pagePasswordValidate' => $extranetConfig->getSgPagePasswordValidate(),
                'extranet_pageLogout' => $extranetConfig->getSgPageLogout(),
                'extranet_pageSubscribe' => $extranetConfig->getSgPageSubscribe(),
                'extranet_pageSubscribeConfirm' => $extranetConfig->getSgPageSubscribeConfirm(),
                'extranet_pageSubscribeValidate' => $extranetConfig->getSgPageSubscribeValidate(),
                'extranet_pageUnsubscribeConfirm' => $extranetConfig->getSgPageUnsubscribeConfirm(),
                'extranet_articleExtranet' => $extranetConfig->getSgArticleExtranet(),
                'extranet_article401' => $extranetConfig->getSgArticle401(),
                'extranet_article403' => $extranetConfig->getSgArticle403(),
                'extranet_articleContent' => $extranetConfig->getSgArticleContent(),
                'extranet_articleData' => $extranetConfig->getSgArticleData(),
                'extranet_articleDataConfirm' => $extranetConfig->getSgArticleDataConfirm(),
                'extranet_articlePassword' => $extranetConfig->getSgArticlePassword(),
                'extranet_articlePasswordConfirm' => $extranetConfig->getSgArticlePasswordConfirm(),
                'extranet_articlePasswordValidate' => $extranetConfig->getSgArticlePasswordValidate(),
                'extranet_articleLogout' => $extranetConfig->getSgArticleLogout(),
                'extranet_articleSubscribe' => $extranetConfig->getSgArticleSubscribe(),
                'extranet_articleSubscribeConfirm' => $extranetConfig->getSgArticleSubscribeConfirm(),
                'extranet_articleSubscribeValidate' => $extranetConfig->getSgArticleSubscribeValidate(),
                'extranet_articleUnsubscribeConfirm' => $extranetConfig->getSgArticleUnsubscribeConfirm(),
                'extranet_moduleLogin' => $extranetConfig->getSgModuleLogin(),
                'extranet_moduleLogout' => $extranetConfig->getSgModuleLogout(),
                'extranet_moduleData' => $extranetConfig->getSgModuleData(),
                'extranet_modulePassword' => $extranetConfig->getSgModulePassword(),
                'extranet_moduleNav' => $extranetConfig->getSgModuleNav(),
                'extranet_moduleSubscribe' => $extranetConfig->getSgModuleSubscribe(),
                'extranet_moduleCloseAccount' => $extranetConfig->getSgModuleCloseAccount(),
                'extranet_notificationChangeData' => $extranetConfig->getSgNotificationChangeData(),
                'extranet_notificationChangeDataMessage' => $extranetConfig->getSgNotificationChangeDataMessage(),
                'extranet_notificationChangeDataMessageLanguage' => $extranetConfig->getSgNotificationChangeDataMessageLanguage(),
                'extranet_notificationPassword' => $extranetConfig->getSgNotificationPassword(),
                'extranet_notificationPasswordMessage' => $extranetConfig->getSgNotificationPasswordMessage(),
                'extranet_notificationPasswordMessageLanguage' => $extranetConfig->getSgNotificationPasswordMessageLanguage(),
                'extranet_notificationSubscription' => $extranetConfig->getSgNotificationSubscription(),
                'extranet_notificationSubscriptionMessage' => $extranetConfig->getSgNotificationSubscriptionMessage(),
                'extranet_notificationSubscriptionMessageLanguage' => $extranetConfig->getSgNotificationSubscriptionMessageLanguage(),
                'extranet_contentArticleExtranetHeadline' => $extranetConfig->getSgContentArticleExtranetHeadline(),
                'extranet_contentArticleExtranetModuleLoginGuests' => $extranetConfig->getSgContentArticleExtranetModuleLoginGuests(),
                'extranet_contentArticleExtranetGridStartA' => $extranetConfig->getSgContentArticleExtranetGridStartA(),
                'extranet_contentArticleExtranetGridStartB' => $extranetConfig->getSgContentArticleExtranetGridStartB(),
                'extranet_contentArticleExtranetModuleLoginLogged' => $extranetConfig->getSgContentArticleExtranetModuleLoginLogged(),
                'extranet_contentArticleExtranetModuleNav' => $extranetConfig->getSgContentArticleExtranetModuleNav(),
                'extranet_contentArticleExtranetGridStopB' => $extranetConfig->getSgContentArticleExtranetGridStopB(),
                'extranet_contentArticleExtranetGridStopA' => $extranetConfig->getSgContentArticleExtranetGridStopA(),
                'extranet_contentArticle401Headline' => $extranetConfig->getSgContentArticle401Headline(),
                'extranet_contentArticle401Text' => $extranetConfig->getSgContentArticle401Text(),
                'extranet_contentArticle401ModuleLoginGuests' => $extranetConfig->getSgContentArticle401ModuleLoginGuests(),
                'extranet_contentArticle403Headline' => $extranetConfig->getSgContentArticle403Headline(),
                'extranet_contentArticle403Text' => $extranetConfig->getSgContentArticle403Text(),
                'extranet_contentArticle403Hyperlink' => $extranetConfig->getSgContentArticle403Hyperlink(),
                'extranet_contentArticleContentHeadline' => $extranetConfig->getSgContentArticleContentHeadline(),
                'extranet_contentArticleContentText' => $extranetConfig->getSgContentArticleContentText(),
                'extranet_contentArticleDataHeadline' => $extranetConfig->getSgContentArticleDataHeadline(),
                'extranet_contentArticleDataModuleData' => $extranetConfig->getSgContentArticleDataModuleData(),
                'extranet_contentArticleDataHeadlineCloseAccount' => $extranetConfig->getSgContentArticleDataHeadlineCloseAccount(),
                'extranet_contentArticleDataTextCloseAccount' => $extranetConfig->getSgContentArticleDataTextCloseAccount(),
                'extranet_contentArticleDataModuleCloseAccount' => $extranetConfig->getSgContentArticleDataModuleCloseAccount(),
                'extranet_contentArticleDataConfirmHeadline' => $extranetConfig->getSgContentArticleDataConfirmHeadline(),
                'extranet_contentArticleDataConfirmText' => $extranetConfig->getSgContentArticleDataConfirmText(),
                'extranet_contentArticleDataConfirmHyperlink' => $extranetConfig->getSgContentArticleDataConfirmHyperlink(),
                'extranet_contentArticlePasswordHeadline' => $extranetConfig->getSgContentArticlePasswordHeadline(),
                'extranet_contentArticlePasswordModulePassword' => $extranetConfig->getSgContentArticlePasswordModulePassword(),
                'extranet_contentArticlePasswordConfirmHeadline' => $extranetConfig->getSgContentArticlePasswordConfirmHeadline(),
                'extranet_contentArticlePasswordConfirmText' => $extranetConfig->getSgContentArticlePasswordConfirmText(),
                'extranet_contentArticlePasswordValidateHeadline' => $extranetConfig->getSgContentArticlePasswordValidateHeadline(),
                'extranet_contentArticlePasswordValidateModulePassword' => $extranetConfig->getSgContentArticlePasswordValidateModulePassword(),
                'extranet_contentArticleLogoutModuleLogout' => $extranetConfig->getSgContentArticleLogoutModuleLogout(),
                'extranet_contentArticleSubscribeHeadline' => $extranetConfig->getSgContentArticleSubscribeHeadline(),
                'extranet_contentArticleSubscribeModuleSubscribe' => $extranetConfig->getSgContentArticleSubscribeModuleSubscribe(),
                'extranet_contentArticleSubscribeConfirmHeadline' => $extranetConfig->getSgContentArticleSubscribeConfirmHeadline(),
                'extranet_contentArticleSubscribeConfirmText' => $extranetConfig->getSgContentArticleSubscribeConfirmText(),
                'extranet_contentArticleSubscribeValidateHeadline' => $extranetConfig->getSgContentArticleSubscribeValidateHeadline(),
                'extranet_contentArticleSubscribeValidateText' => $extranetConfig->getSgContentArticleSubscribeValidateText(),
                'extranet_contentArticleSubscribeValidateModuleLoginGuests' => $extranetConfig->getSgContentArticleSubscribeValidateModuleLoginGuests(),
                'extranet_contentArticleUnsubscribeHeadline' => $extranetConfig->getSgContentArticleUnsubscribeHeadline(),
                'extranet_contentArticleUnsubscribeText' => $extranetConfig->getSgContentArticleUnsubscribeText(),
                'extranet_contentArticleUnsubscribeHyperlink' => $extranetConfig->getSgContentArticleUnsubscribeHyperlink(),
                'extranet_archived' => $extranetConfig->getSgArchived() ? '1' : '0',
                'extranet_archivedAt' => $extranetConfig->getSgArchivedAt(),
                'extranet_archivedMode' => $extranetConfig->getSgArchivedMode(),
                default => static::NOT_HANDLED,
            };
        }

        return static::NOT_HANDLED;
    }
}
