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
        if ('sg' === $key && 'extranet' === substr($elements[1], 0, 8)) {
            /** @var CoreConfig */
            $config = $this->coreConfigurationManager->load();
            /** @var ExtranetConfig */
            $extranetConfig = $config->getSgExtranet();

            if (!$extranetConfig->getSgInstallComplete()) {
                return static::NOT_HANDLED;
            }

            switch ($elements[1]) {
                case 'extranet_installComplete':
                    return $extranetConfig->getSgInstallComplete() ? '1' : '0';
                break;
                case 'extranet_extranetFolder':
                    return $extranetConfig->getSgExtranetFolder();
                break;
                case 'extranet_canSubscribe':
                    return $extranetConfig->getSgCanSubscribe() ? '1' : '0';
                break;
                case 'extranet_memberExample':
                    return $extranetConfig->getSgMemberExample();
                break;
                case 'extranet_memberGroupMembers':
                    return $extranetConfig->getSgMemberGroupMembers();
                break;
                case 'extranet_memberGroupMembersTitle':
                    return $extranetConfig->getSgMemberGroupMembersTitle();
                break;
                case 'extranet_pageExtranetTitle':
                    return $extranetConfig->getSgPageExtranetTitle();
                break;
                case 'extranet_pageExtranet':
                    return $extranetConfig->getSgPageExtranet();
                break;
                case 'extranet_page401':
                    return $extranetConfig->getSgPage401();
                break;
                case 'extranet_page403':
                    return $extranetConfig->getSgPage403();
                break;
                case 'extranet_pageContent':
                    return $extranetConfig->getSgPageContent();
                break;
                case 'extranet_pageData':
                    return $extranetConfig->getSgPageData();
                break;
                case 'extranet_pageDataConfirm':
                    return $extranetConfig->getSgPageDataConfirm();
                break;
                case 'extranet_pagePassword':
                    return $extranetConfig->getSgPagePassword();
                break;
                case 'extranet_pagePasswordConfirm':
                    return $extranetConfig->getSgPagePasswordConfirm();
                break;
                case 'extranet_pagePasswordValidate':
                    return $extranetConfig->getSgPagePasswordValidate();
                break;
                case 'extranet_pageLogout':
                    return $extranetConfig->getSgPageLogout();
                break;
                case 'extranet_pageSubscribe':
                    return $extranetConfig->getSgPageSubscribe();
                break;
                case 'extranet_pageSubscribeConfirm':
                    return $extranetConfig->getSgPageSubscribeConfirm();
                break;
                case 'extranet_pageSubscribeValidate':
                    return $extranetConfig->getSgPageSubscribeValidate();
                break;
                case 'extranet_pageUnsubscribeConfirm':
                    return $extranetConfig->getSgPageUnsubscribeConfirm();
                break;
                case 'extranet_articleExtranet':
                    return $extranetConfig->getSgArticleExtranet();
                break;
                case 'extranet_article401':
                    return $extranetConfig->getSgArticle401();
                break;
                case 'extranet_article403':
                    return $extranetConfig->getSgArticle403();
                break;
                case 'extranet_articleContent':
                    return $extranetConfig->getSgArticleContent();
                break;
                case 'extranet_articleData':
                    return $extranetConfig->getSgArticleData();
                break;
                case 'extranet_articleDataConfirm':
                    return $extranetConfig->getSgArticleDataConfirm();
                break;
                case 'extranet_articlePassword':
                    return $extranetConfig->getSgArticlePassword();
                break;
                case 'extranet_articlePasswordConfirm':
                    return $extranetConfig->getSgArticlePasswordConfirm();
                break;
                case 'extranet_articlePasswordValidate':
                    return $extranetConfig->getSgArticlePasswordValidate();
                break;
                case 'extranet_articleLogout':
                    return $extranetConfig->getSgArticleLogout();
                break;
                case 'extranet_articleSubscribe':
                    return $extranetConfig->getSgArticleSubscribe();
                break;
                case 'extranet_articleSubscribeConfirm':
                    return $extranetConfig->getSgArticleSubscribeConfirm();
                break;
                case 'extranet_articleSubscribeValidate':
                    return $extranetConfig->getSgArticleSubscribeValidate();
                break;
                case 'extranet_articleUnsubscribeConfirm':
                    return $extranetConfig->getSgArticleUnsubscribeConfirm();
                break;
                case 'extranet_moduleLogin':
                    return $extranetConfig->getSgModuleLogin();
                break;
                case 'extranet_moduleLogout':
                    return $extranetConfig->getSgModuleLogout();
                break;
                case 'extranet_moduleData':
                    return $extranetConfig->getSgModuleData();
                break;
                case 'extranet_modulePassword':
                    return $extranetConfig->getSgModulePassword();
                break;
                case 'extranet_moduleNav':
                    return $extranetConfig->getSgModuleNav();
                break;
                case 'extranet_moduleSubscribe':
                    return $extranetConfig->getSgModuleSubscribe();
                break;
                case 'extranet_moduleCloseAccount':
                    return $extranetConfig->getSgModuleCloseAccount();
                break;
                case 'extranet_notificationChangeData':
                    return $extranetConfig->getSgNotificationChangeData();
                break;
                case 'extranet_notificationChangeDataMessage':
                    return $extranetConfig->getSgNotificationChangeDataMessage();
                break;
                case 'extranet_notificationChangeDataMessageLanguage':
                    return $extranetConfig->getSgNotificationChangeDataMessageLanguage();
                break;
                case 'extranet_notificationPassword':
                    return $extranetConfig->getSgNotificationPassword();
                break;
                case 'extranet_notificationPasswordMessage':
                    return $extranetConfig->getSgNotificationPasswordMessage();
                break;
                case 'extranet_notificationPasswordMessageLanguage':
                    return $extranetConfig->getSgNotificationPasswordMessageLanguage();
                break;
                case 'extranet_notificationSubscription':
                    return $extranetConfig->getSgNotificationSubscription();
                break;
                case 'extranet_notificationSubscriptionMessage':
                    return $extranetConfig->getSgNotificationSubscriptionMessage();
                break;
                case 'extranet_notificationSubscriptionMessageLanguage':
                    return $extranetConfig->getSgNotificationSubscriptionMessageLanguage();
                break;
                case 'extranet_contentArticleExtranetHeadline':
                    return $extranetConfig->getSgContentArticleExtranetHeadline();
                break;
                case 'extranet_contentArticleExtranetModuleLoginGuests':
                    return $extranetConfig->getSgContentArticleExtranetModuleLoginGuests();
                break;
                case 'extranet_contentArticleExtranetGridStartA':
                    return $extranetConfig->getSgContentArticleExtranetGridStartA();
                break;
                case 'extranet_contentArticleExtranetGridStartB':
                    return $extranetConfig->getSgContentArticleExtranetGridStartB();
                break;
                case 'extranet_contentArticleExtranetModuleLoginLogged':
                    return $extranetConfig->getSgContentArticleExtranetModuleLoginLogged();
                break;
                case 'extranet_contentArticleExtranetModuleNav':
                    return $extranetConfig->getSgContentArticleExtranetModuleNav();
                break;
                case 'extranet_contentArticleExtranetGridStopB':
                    return $extranetConfig->getSgContentArticleExtranetGridStopB();
                break;
                case 'extranet_contentArticleExtranetGridStopA':
                    return $extranetConfig->getSgContentArticleExtranetGridStopA();
                break;
                case 'extranet_contentArticle401Headline':
                    return $extranetConfig->getSgContentArticle401Headline();
                break;
                case 'extranet_contentArticle401Text':
                    return $extranetConfig->getSgContentArticle401Text();
                break;
                case 'extranet_contentArticle401ModuleLoginGuests':
                    return $extranetConfig->getSgContentArticle401ModuleLoginGuests();
                break;
                case 'extranet_contentArticle403Headline':
                    return $extranetConfig->getSgContentArticle403Headline();
                break;
                case 'extranet_contentArticle403Text':
                    return $extranetConfig->getSgContentArticle403Text();
                break;
                case 'extranet_contentArticle403Hyperlink':
                    return $extranetConfig->getSgContentArticle403Hyperlink();
                break;
                case 'extranet_contentArticleContentHeadline':
                    return $extranetConfig->getSgContentArticleContentHeadline();
                break;
                case 'extranet_contentArticleContentText':
                    return $extranetConfig->getSgContentArticleContentText();
                break;
                case 'extranet_contentArticleDataHeadline':
                    return $extranetConfig->getSgContentArticleDataHeadline();
                break;
                case 'extranet_contentArticleDataModuleData':
                    return $extranetConfig->getSgContentArticleDataModuleData();
                break;
                case 'extranet_contentArticleDataHeadlineCloseAccount':
                    return $extranetConfig->getSgContentArticleDataHeadlineCloseAccount();
                break;
                case 'extranet_contentArticleDataTextCloseAccount':
                    return $extranetConfig->getSgContentArticleDataTextCloseAccount();
                break;
                case 'extranet_contentArticleDataModuleCloseAccount':
                    return $extranetConfig->getSgContentArticleDataModuleCloseAccount();
                break;
                case 'extranet_contentArticleDataConfirmHeadline':
                    return $extranetConfig->getSgContentArticleDataConfirmHeadline();
                break;
                case 'extranet_contentArticleDataConfirmText':
                    return $extranetConfig->getSgContentArticleDataConfirmText();
                break;
                case 'extranet_contentArticleDataConfirmHyperlink':
                    return $extranetConfig->getSgContentArticleDataConfirmHyperlink();
                break;
                case 'extranet_contentArticlePasswordHeadline':
                    return $extranetConfig->getSgContentArticlePasswordHeadline();
                break;
                case 'extranet_contentArticlePasswordModulePassword':
                    return $extranetConfig->getSgContentArticlePasswordModulePassword();
                break;
                case 'extranet_contentArticlePasswordConfirmHeadline':
                    return $extranetConfig->getSgContentArticlePasswordConfirmHeadline();
                break;
                case 'extranet_contentArticlePasswordConfirmText':
                    return $extranetConfig->getSgContentArticlePasswordConfirmText();
                break;
                case 'extranet_contentArticlePasswordValidateHeadline':
                    return $extranetConfig->getSgContentArticlePasswordValidateHeadline();
                break;
                case 'extranet_contentArticlePasswordValidateModulePassword':
                    return $extranetConfig->getSgContentArticlePasswordValidateModulePassword();
                break;
                case 'extranet_contentArticleLogoutModuleLogout':
                    return $extranetConfig->getSgContentArticleLogoutModuleLogout();
                break;
                case 'extranet_contentArticleSubscribeHeadline':
                    return $extranetConfig->getSgContentArticleSubscribeHeadline();
                break;
                case 'extranet_contentArticleSubscribeModuleSubscribe':
                    return $extranetConfig->getSgContentArticleSubscribeModuleSubscribe();
                break;
                case 'extranet_contentArticleSubscribeConfirmHeadline':
                    return $extranetConfig->getSgContentArticleSubscribeConfirmHeadline();
                break;
                case 'extranet_contentArticleSubscribeConfirmText':
                    return $extranetConfig->getSgContentArticleSubscribeConfirmText();
                break;
                case 'extranet_contentArticleSubscribeValidateHeadline':
                    return $extranetConfig->getSgContentArticleSubscribeValidateHeadline();
                break;
                case 'extranet_contentArticleSubscribeValidateText':
                    return $extranetConfig->getSgContentArticleSubscribeValidateText();
                break;
                case 'extranet_contentArticleSubscribeValidateModuleLoginGuests':
                    return $extranetConfig->getSgContentArticleSubscribeValidateModuleLoginGuests();
                break;
                case 'extranet_contentArticleUnsubscribeHeadline':
                    return $extranetConfig->getSgContentArticleUnsubscribeHeadline();
                break;
                case 'extranet_contentArticleUnsubscribeText':
                    return $extranetConfig->getSgContentArticleUnsubscribeText();
                break;
                case 'extranet_contentArticleUnsubscribeHyperlink':
                    return $extranetConfig->getSgContentArticleUnsubscribeHyperlink();
                break;
                case 'extranet_archived':
                    return $extranetConfig->getSgArchived() ? '1' : '0';
                break;
                case 'extranet_archivedAt':
                    return $extranetConfig->getSgArchivedAt();
                break;
                case 'extranet_archivedMode':
                    return $extranetConfig->getSgArchivedMode();
                break;
                default:
                return static::NOT_HANDLED;
            }
        }

        return static::NOT_HANDLED;
    }
}
