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

namespace WEM\SmartgearBundle\Config\Module\Extranet;

use WEM\SmartgearBundle\Classes\Config\ConfigModuleInterface;

class Extranet implements ConfigModuleInterface
{
    public const ARCHIVE_MODE_EMPTY = '';
    public const ARCHIVE_MODE_ARCHIVE = 'archive';
    public const ARCHIVE_MODE_KEEP = 'keep';
    public const ARCHIVE_MODE_DELETE = 'delete';
    public const ARCHIVE_MODES_ALLOWED = [
        self::ARCHIVE_MODE_EMPTY,
        self::ARCHIVE_MODE_ARCHIVE,
        self::ARCHIVE_MODE_KEEP,
        self::ARCHIVE_MODE_DELETE,
    ];
    public const DEFAULT_FOLDER_PATH = 'files/extranet';
    public const DEFAULT_ARCHIVE_MODE = self::ARCHIVE_MODE_EMPTY;
    public const DEFAULT_CAN_SUBSCRIBE = false;
    public const DEFAULT_MEMBER_GROUP_MEMBERS_TITLE = 'Membres';
    public const DEFAULT_PAGE_EXTRANET_TITLE = 'Espace Membre';

    /** @var bool */
    protected $sgInstallComplete = false;
    /** @var string */
    protected $sgExtranetFolder = self::DEFAULT_FOLDER_PATH;
    /** @var bool */
    protected $sgCanSubscribe = self::DEFAULT_CAN_SUBSCRIBE;
    /** @var int */
    protected $sgMemberExample;
    /** @var int */
    protected $sgMemberGroupMembers;
    /** @var string */
    protected $sgMemberGroupMembersTitle = self::DEFAULT_MEMBER_GROUP_MEMBERS_TITLE;
    /** @var string */
    protected $sgPageExtranetTitle = self::DEFAULT_PAGE_EXTRANET_TITLE;
    /** @var int */
    protected $sgPageExtranet;
    /** @var int */
    protected $sgPage401;
    /** @var int */
    protected $sgPage403;
    /** @var int */
    protected $sgPageContent;
    /** @var int */
    protected $sgPageData;
    /** @var int */
    protected $sgPageDataConfirm;
    /** @var int */
    protected $sgPagePassword;
    /** @var int */
    protected $sgPagePasswordConfirm;
    /** @var int */
    protected $sgPagePasswordValidate;
    /** @var int */
    protected $sgPageLogout;
    /** @var int */
    protected $sgPageSubscribe;
    /** @var int */
    protected $sgPageSubscribeConfirm;
    /** @var int */
    protected $sgPageSubscribeValidate;
    /** @var int */
    protected $sgPageUnsubscribeConfirm;
    /** @var int */
    protected $sgArticleExtranet;
    /** @var int */
    protected $sgArticle401;
    /** @var int */
    protected $sgArticle403;
    /** @var int */
    protected $sgArticleContent;
    /** @var int */
    protected $sgArticleData;
    /** @var int */
    protected $sgArticleDataConfirm;
    /** @var int */
    protected $sgArticlePassword;
    /** @var int */
    protected $sgArticlePasswordConfirm;
    /** @var int */
    protected $sgArticlePasswordValidate;
    /** @var int */
    protected $sgArticleLogout;
    /** @var int */
    protected $sgArticleSubscribe;
    /** @var int */
    protected $sgArticleSubscribeConfirm;
    /** @var int */
    protected $sgArticleSubscribeValidate;
    /** @var int */
    protected $sgArticleUnsubscribeConfirm;
    /** @var int */
    protected $sgModuleLogin;
    /** @var int */
    protected $sgModuleLogout;
    /** @var int */
    protected $sgModuleData;
    /** @var int */
    protected $sgModulePassword;
    /** @var int */
    protected $sgModuleNav;
    /** @var int */
    protected $sgModuleSubscribe;
    /** @var int */
    protected $sgModuleCloseAccount;
    /** @var int */
    protected $sgNotificationChangeData;
    /** @var int */
    protected $sgNotificationChangeDataMessage;
    /** @var int */
    protected $sgNotificationChangeDataMessageLanguage;
    /** @var int */
    protected $sgNotificationPassword;
    /** @var int */
    protected $sgNotificationPasswordMessage;
    /** @var int */
    protected $sgNotificationPasswordMessageLanguage;
    /** @var int */
    protected $sgNotificationSubscription;
    /** @var int */
    protected $sgNotificationSubscriptionMessage;
    /** @var int */
    protected $sgNotificationSubscriptionMessageLanguage;

    /** @var int */
    protected $sgContentHeadlineArticleExtranet;
    /** @var int */
    protected $sgContentModuleLoginGuestsArticleExtranet;
    /** @var int */
    protected $sgContentGridStartAArticleExtranet;
    /** @var int */
    protected $sgContentGridStartBArticleExtranet;
    /** @var int */
    protected $sgContentModuleLoginLoggedArticleExtranet;
    /** @var int */
    protected $sgContentModuleNavArticleExtranet;
    /** @var int */
    protected $sgContentGridStopBArticleExtranet;
    /** @var int */
    protected $sgContentTextArticleExtranet;
    /** @var int */
    protected $sgContentGridStopAArticleExtranet;
    /** @var int */
    protected $sgContentHeadlineArticle401;
    /** @var int */
    protected $sgContentTextArticle401;
    /** @var int */
    protected $sgContentModuleLoginGuestsArticle401;
    /** @var int */
    protected $sgContentHeadlineArticle403;
    /** @var int */
    protected $sgContentTextArticle403;
    /** @var int */
    protected $sgContentHyperlinkArticle403;
    /** @var int */
    protected $sgContentHeadlineArticleContent;
    /** @var int */
    protected $sgContentTextArticleContent;
    /** @var int */
    protected $sgContentHeadlineArticleData;
    /** @var int */
    protected $sgContentModuleDataArticleData;
    /** @var int */
    protected $sgContentHeadlineCloseAccountArticleData;
    /** @var int */
    protected $sgContentTextCloseAccountArticleData;
    /** @var int */
    protected $sgContentModuleCloseAccountArticleData;
    /** @var int */
    protected $sgContentHeadlineArticleDataConfirm;
    /** @var int */
    protected $sgContentTextArticleDataConfirm;
    /** @var int */
    protected $sgContentHyperlinkArticleDataConfirm;
    /** @var int */
    protected $sgContentHeadlineArticlePassword;
    /** @var int */
    protected $sgContentModulePasswordArticlePassword;
    /** @var int */
    protected $sgContentHeadlineArticlePasswordConfirm;
    /** @var int */
    protected $sgContentTextArticlePasswordConfirm;
    /** @var int */
    protected $sgContentHeadlineArticlePasswordValidate;
    /** @var int */
    protected $sgContentModulePasswordArticlePasswordValidate;
    /** @var int */
    protected $sgContentModuleLogoutArticleLogout;
    /** @var int */
    protected $sgContentHeadlineArticleSubscribe;
    /** @var int */
    protected $sgContentModuleSubscribeArticleSubscribe;
    /** @var int */
    protected $sgContentHeadlineArticleSubscribeConfirm;
    /** @var int */
    protected $sgContentTextArticleSubscribeConfirm;
    /** @var int */
    protected $sgContentHeadlineArticleSubscribeValidate;
    /** @var int */
    protected $sgContentTextArticleSubscribeValidate;
    /** @var int */
    protected $sgContentModuleLoginGuestsArticleSubscribeValidate;
    /** @var int */
    protected $sgContentHeadlineArticleUnsubscribe;
    /** @var int */
    protected $sgContentTextArticleUnsubscribe;
    /** @var int */
    protected $sgContentHyperlinkArticleUnsubscribe;

    /** @var bool */
    protected $sgArchived = false;
    /** @var int */
    protected $sgArchivedAt = 0;
    /** @var string */
    protected $sgArchivedMode = self::DEFAULT_ARCHIVE_MODE;

    public function reset(): self
    {
        $this->setSgInstallComplete(false)
            ->setSgExtranetFolder(self::DEFAULT_FOLDER_PATH)
            ->setSgCanSubscribe(self::DEFAULT_CAN_SUBSCRIBE)
            ->setSgMemberGroupMembersTitle(self::DEFAULT_MEMBER_GROUP_MEMBERS_TITLE)
            ->setSgPageExtranetTitle(self::DEFAULT_PAGE_EXTRANET_TITLE)
            ->setSgMemberExample(null)
            ->setSgMemberGroupMembers(null)
            ->setSgPageExtranet(null)
            ->setSgPage401(null)
            ->setSgPage403(null)
            ->setSgPageContent(null)
            ->setSgPageData(null)
            ->setSgPageDataConfirm(null)
            ->setSgPagePassword(null)
            ->setSgPagePasswordConfirm(null)
            ->setSgPagePasswordValidate(null)
            ->setSgPageLogout(null)
            ->setSgPageSubscribe(null)
            ->setSgPageSubscribeConfirm(null)
            ->setSgPageSubscribeValidate(null)
            ->setSgPageUnsubscribeConfirm(null)
            ->setSgArticleExtranet(null)
            ->setSgArticle401(null)
            ->setSgArticle403(null)
            ->setSgArticleContent(null)
            ->setSgArticleData(null)
            ->setSgArticleDataConfirm(null)
            ->setSgArticlePassword(null)
            ->setSgArticlePasswordConfirm(null)
            ->setSgArticlePasswordValidate(null)
            ->setSgArticleLogout(null)
            ->setSgArticleSubscribe(null)
            ->setSgArticleSubscribeConfirm(null)
            ->setSgArticleSubscribeValidate(null)
            ->setSgArticleUnsubscribeConfirm(null)
            ->setSgModuleLogin(null)
            ->setSgModuleLogout(null)
            ->setSgModuleData(null)
            ->setSgModulePassword(null)
            ->setSgModuleNav(null)
            ->setSgModuleSubscribe(null)
            ->setSgModuleCloseAccount(null)
            ->setSgNotificationChangeData(null)
            ->setSgNotificationChangeDataMessage(null)
            ->setSgNotificationChangeDataMessageLanguage(null)
            ->setSgNotificationPassword(null)
            ->setSgNotificationPasswordMessage(null)
            ->setSgNotificationPasswordMessageLanguage(null)
            ->setSgNotificationSubscription(null)
            ->setSgNotificationSubscriptionMessage(null)
            ->setSgNotificationSubscriptionMessageLanguage(null)

            ->setSgContentHeadlineArticleExtranet(null)
            ->setSgContentModuleLoginGuestsArticleExtranet(null)
            ->setSgContentGridStartAArticleExtranet(null)
            ->setSgContentGridStartBArticleExtranet(null)
            ->setSgContentModuleLoginLoggedArticleExtranet(null)
            ->setSgContentModuleNavArticleExtranet(null)
            ->setSgContentGridStopBArticleExtranet(null)
            ->setSgContentTextArticleExtranet(null)
            ->setSgContentGridStopAArticleExtranet(null)
            ->setSgContentHeadlineArticle401(null)
            ->setSgContentTextArticle401(null)
            ->setSgContentModuleLoginGuestsArticle401(null)
            ->setSgContentHeadlineArticle403(null)
            ->setSgContentTextArticle403(null)
            ->setSgContentHyperlinkArticle403(null)
            ->setSgContentHeadlineArticleContent(null)
            ->setSgContentTextArticleContent(null)
            ->setSgContentHeadlineArticleData(null)
            ->setSgContentModuleDataArticleData(null)
            ->setSgContentHeadlineCloseAccountArticleData(null)
            ->setSgContentTextCloseAccountArticleData(null)
            ->setSgContentModuleCloseAccountArticleData(null)
            ->setSgContentHeadlineArticleDataConfirm(null)
            ->setSgContentTextArticleDataConfirm(null)
            ->setSgContentHyperlinkArticleDataConfirm(null)
            ->setSgContentHeadlineArticlePassword(null)
            ->setSgContentModulePasswordArticlePassword(null)
            ->setSgContentHeadlineArticlePasswordConfirm(null)
            ->setSgContentTextArticlePasswordConfirm(null)
            ->setSgContentHeadlineArticlePasswordValidate(null)
            ->setSgContentModulePasswordArticlePasswordValidate(null)
            ->setSgContentModuleLogoutArticleLogout(null)
            ->setSgContentHeadlineArticleSubscribe(null)
            ->setSgContentModuleSubscribeArticleSubscribe(null)
            ->setSgContentHeadlineArticleSubscribeConfirm(null)
            ->setSgContentTextArticleSubscribeConfirm(null)
            ->setSgContentHeadlineArticleSubscribeValidate(null)
            ->setSgContentTextArticleSubscribeValidate(null)
            ->setSgContentModuleLoginGuestsArticleSubscribeValidate(null)
            ->setSgContentHeadlineArticleUnsubscribe(null)
            ->setSgContentTextArticleUnsubscribe(null)
            ->setSgContentHyperlinkArticleUnsubscribe(null)

            ->setSgArchived(false)
            ->setSgArchivedAt(0)
            ->setSgArchivedMode(self::DEFAULT_ARCHIVE_MODE)
        ;

        return $this;
    }

    public function import(\stdClass $json): self
    {
        $this->setSgInstallComplete($json->installComplete ?? false)
            ->setSgExtranetFolder($json->extranet_folder ?? self::DEFAULT_FOLDER_PATH)
            ->setSgCanSubscribe($json->canSubscribe ?? false)
            ->setSgMemberGroupMembersTitle($json->memberGroupMembersTitle ?? self::DEFAULT_MEMBER_GROUP_MEMBERS_TITLE)
            ->setSgPageExtranetTitle($json->pageExtranetTitle ?? self::DEFAULT_PAGE_EXTRANET_TITLE)
            ->setSgMemberExample($json->contao->members->example ?? null)
            ->setSgMemberGroupMembers($json->contao->memberGroups->members ?? null)
            ->setSgPageExtranet($json->contao->pages->extranet ?? null)
            ->setSgPage401($json->contao->pages->error401 ?? null)
            ->setSgPage403($json->contao->pages->error403 ?? null)
            ->setSgPageContent($json->contao->pages->content ?? null)
            ->setSgPageData($json->contao->pages->data ?? null)
            ->setSgPageDataConfirm($json->contao->pages->dataConfirm ?? null)
            ->setSgPagePassword($json->contao->pages->password ?? null)
            ->setSgPagePasswordConfirm($json->contao->pages->passwordConfirm ?? null)
            ->setSgPagePasswordValidate($json->contao->pages->passwordValidate ?? null)
            ->setSgPageLogout($json->contao->pages->logout ?? null)
            ->setSgPageSubscribe($json->contao->pages->subscribe ?? null)
            ->setSgPageSubscribeConfirm($json->contao->pages->subscribeConfirm ?? null)
            ->setSgPageSubscribeValidate($json->contao->pages->subscribeValidate ?? null)
            ->setSgPageUnsubscribeConfirm($json->contao->pages->unsubscribe ?? null)
            ->setSgArticleExtranet($json->contao->articles->extranet ?? null)
            ->setSgArticle401($json->contao->articles->error401 ?? null)
            ->setSgArticle403($json->contao->articles->error403 ?? null)
            ->setSgArticleContent($json->contao->articles->content ?? null)
            ->setSgArticleData($json->contao->articles->data ?? null)
            ->setSgArticleDataConfirm($json->contao->articles->dataConfirm ?? null)
            ->setSgArticlePassword($json->contao->articles->password ?? null)
            ->setSgArticlePasswordConfirm($json->contao->articles->passwordConfirm ?? null)
            ->setSgArticlePasswordValidate($json->contao->articles->passwordValidate ?? null)
            ->setSgArticleLogout($json->contao->articles->logout ?? null)
            ->setSgArticleSubscribe($json->contao->articles->subscribe ?? null)
            ->setSgArticleSubscribeConfirm($json->contao->articles->subscribeConfirm ?? null)
            ->setSgArticleSubscribeValidate($json->contao->articles->subscribeValidate ?? null)
            ->setSgArticleUnsubscribeConfirm($json->contao->articles->unsubscribe ?? null)
            ->setSgModuleLogin($json->contao->modules->login ?? null)
            ->setSgModuleLogout($json->contao->modules->logout ?? null)
            ->setSgModuleData($json->contao->modules->data ?? null)
            ->setSgModulePassword($json->contao->modules->password ?? null)
            ->setSgModuleNav($json->contao->modules->nav ?? null)
            ->setSgModuleSubscribe($json->contao->modules->subscribe ?? null)
            ->setSgModuleCloseAccount($json->contao->modules->closeAccount ?? null)
            ->setSgNotificationChangeData($json->contao->notifications->changeData ?? null)
            ->setSgNotificationChangeDataMessage($json->contao->notificationMessages->changeData ?? null)
            ->setSgNotificationChangeDataMessageLanguage($json->contao->notificationMessagesLanguages->changeData ?? null)
            ->setSgNotificationPassword($json->contao->notifications->password ?? null)
            ->setSgNotificationPasswordMessage($json->contao->notificationMessages->password ?? null)
            ->setSgNotificationPasswordMessageLanguage($json->contao->notificationMessagesLanguages->password ?? null)
            ->setSgNotificationSubscription($json->contao->notifications->subscription ?? null)
            ->setSgNotificationSubscriptionMessage($json->contao->notificationMessages->subscription ?? null)
            ->setSgNotificationSubscriptionMessageLanguage($json->contao->notificationMessagesLanguages->subscription ?? null)
            ->setSgContentHeadlineArticleExtranet($json->contao->contents->extranet->headline ?? null)
            ->setSgContentModuleLoginGuestsArticleExtranet($json->contao->contents->extranet->moduleLoginGuests ?? null)
            ->setSgContentGridStartAArticleExtranet($json->contao->contents->extranet->gridStartA ?? null)
            ->setSgContentGridStartBArticleExtranet($json->contao->contents->extranet->gridStartB ?? null)
            ->setSgContentModuleLoginLoggedArticleExtranet($json->contao->contents->extranet->moduleLoginLogged ?? null)
            ->setSgContentModuleNavArticleExtranet($json->contao->contents->extranet->moduleNav ?? null)
            ->setSgContentGridStopBArticleExtranet($json->contao->contents->extranet->gridStopB ?? null)
            ->setSgContentTextArticleExtranet($json->contao->contents->extranet->text ?? null)
            ->setSgContentGridStopAArticleExtranet($json->contao->contents->extranet->gridStopA ?? null)
            ->setSgContentHeadlineArticle401($json->contao->contents->error401->headline ?? null)
            ->setSgContentTextArticle401($json->contao->contents->error401->text ?? null)
            ->setSgContentModuleLoginGuestsArticle401($json->contao->contents->error401->moduleLoginGuests ?? null)
            ->setSgContentHeadlineArticle403($json->contao->contents->error403->headline ?? null)
            ->setSgContentTextArticle403($json->contao->contents->error403->text ?? null)
            ->setSgContentHyperlinkArticle403($json->contao->contents->error403->hyperlink ?? null)
            ->setSgContentHeadlineArticleContent($json->contao->contents->content->headline ?? null)
            ->setSgContentTextArticleContent($json->contao->contents->content->text ?? null)
            ->setSgContentHeadlineArticleData($json->contao->contents->data->headline ?? null)
            ->setSgContentModuleDataArticleData($json->contao->contents->data->moduleData ?? null)
            ->setSgContentHeadlineCloseAccountArticleData($json->contao->contents->data->headline ?? null)
            ->setSgContentTextCloseAccountArticleData($json->contao->contents->data->text ?? null)
            ->setSgContentModuleCloseAccountArticleData($json->contao->contents->data->moduleCloseAccount ?? null)
            ->setSgContentHeadlineArticleDataConfirm($json->contao->contents->dataConfirm->headline ?? null)
            ->setSgContentTextArticleDataConfirm($json->contao->contents->dataConfirm->text ?? null)
            ->setSgContentHyperlinkArticleDataConfirm($json->contao->contents->dataConfirm->hyperlink ?? null)
            ->setSgContentHeadlineArticlePassword($json->contao->contents->password->headline ?? null)
            ->setSgContentModulePasswordArticlePassword($json->contao->contents->password->modulePassword ?? null)
            ->setSgContentHeadlineArticlePasswordConfirm($json->contao->contents->passwordConfirm->headline ?? null)
            ->setSgContentTextArticlePasswordConfirm($json->contao->contents->passwordConfirm->text ?? null)
            ->setSgContentHeadlineArticlePasswordValidate($json->contao->contents->passwordValidate->headline ?? null)
            ->setSgContentModulePasswordArticlePasswordValidate($json->contao->contents->passwordValidate->modulePassword ?? null)
            ->setSgContentModuleLogoutArticleLogout($json->contao->contents->logout->moduleLogout ?? null)
            ->setSgContentHeadlineArticleSubscribe($json->contao->contents->subscribe->headline ?? null)
            ->setSgContentModuleSubscribeArticleSubscribe($json->contao->contents->subscribe->moduleSubscribe ?? null)
            ->setSgContentHeadlineArticleSubscribeConfirm($json->contao->contents->subscribeConfirm->headline ?? null)
            ->setSgContentTextArticleSubscribeConfirm($json->contao->contents->subscribeConfirm->text ?? null)
            ->setSgContentHeadlineArticleSubscribeValidate($json->contao->contents->subscribeValidate->headline ?? null)
            ->setSgContentTextArticleSubscribeValidate($json->contao->contents->subscribeValidate->text ?? null)
            ->setSgContentModuleLoginGuestsArticleSubscribeValidate($json->contao->contents->subscribeValidate->moduleLoginGuests ?? null)
            ->setSgContentHeadlineArticleUnsubscribe($json->contao->contents->unsubscribe->headline ?? null)
            ->setSgContentTextArticleUnsubscribe($json->contao->contents->unsubscribe->text ?? null)
            ->setSgContentHyperlinkArticleUnsubscribe($json->contao->contents->unsubscribe->hyperlink ?? null)

            ->setSgArchived($json->archived->status ?? false)
            ->setSgArchivedAt($json->archived->at ?? 0)
            ->setSgArchivedMode($json->archived->mode ?? self::DEFAULT_ARCHIVE_MODE)
        ;

        return $this;
    }

    public function export(): \stdClass
    {
        $json = new \stdClass();
        $json->installComplete = $this->getSgInstallComplete();
        $json->extranet_folder = $this->getSgExtranetFolder();
        $json->canSubscribe = $this->getSgCanSubscribe();
        $json->memberGroupMembersTitle = $this->getSgMemberGroupMembersTitle();
        $json->pageExtranetTitle = $this->getSgPageExtranetTitle();

        $json->contao = new \stdClass();

        $json->contao->pages = new \stdClass();
        $json->contao->pages->extranet = $this->getSgPageExtranet();
        $json->contao->pages->error401 = $this->getSgPage401();
        $json->contao->pages->error403 = $this->getSgPage403();
        $json->contao->pages->content = $this->getSgPageContent();
        $json->contao->pages->data = $this->getSgPageData();
        $json->contao->pages->dataConfirm = $this->getSgPageDataConfirm();
        $json->contao->pages->password = $this->getSgPagePassword();
        $json->contao->pages->passwordConfirm = $this->getSgPagePasswordConfirm();
        $json->contao->pages->passwordValidate = $this->getSgPagePasswordValidate();
        $json->contao->pages->logout = $this->getSgPageLogout();
        $json->contao->pages->subscribe = $this->getSgPageSubscribe();
        $json->contao->pages->subscribeConfirm = $this->getSgPageSubscribeConfirm();
        $json->contao->pages->subscribeValidate = $this->getSgPageSubscribeValidate();
        $json->contao->pages->unsubscribe = $this->getSgPageUnsubscribeConfirm();

        $json->contao->articles = new \stdClass();
        $json->contao->articles->extranet = $this->getSgArticleExtranet();
        $json->contao->articles->error401 = $this->getSgArticle401();
        $json->contao->articles->error403 = $this->getSgArticle403();
        $json->contao->articles->content = $this->getSgArticleContent();
        $json->contao->articles->data = $this->getSgArticleData();
        $json->contao->articles->dataConfirm = $this->getSgArticleDataConfirm();
        $json->contao->articles->password = $this->getSgArticlePassword();
        $json->contao->articles->passwordConfirm = $this->getSgArticlePasswordConfirm();
        $json->contao->articles->passwordValidate = $this->getSgArticlePasswordValidate();
        $json->contao->articles->logout = $this->getSgArticleLogout();
        $json->contao->articles->subscribe = $this->getSgArticleSubscribe();
        $json->contao->articles->subscribeConfirm = $this->getSgArticleSubscribeConfirm();
        $json->contao->articles->subscribeValidate = $this->getSgArticleSubscribeValidate();
        $json->contao->articles->unsubscribe = $this->getSgArticleUnsubscribeConfirm();

        $json->contao->modules = new \stdClass();
        $json->contao->modules->login = $this->getSgModuleLogin();
        $json->contao->modules->logout = $this->getSgModuleLogout();
        $json->contao->modules->data = $this->getSgModuleData();
        $json->contao->modules->password = $this->getSgModulePassword();
        $json->contao->modules->nav = $this->getSgModuleNav();
        $json->contao->modules->subscribe = $this->getSgModuleSubscribe();
        $json->contao->modules->closeAccount = $this->getSgModuleCloseAccount();

        $json->contao->notifications = new \stdClass();
        $json->contao->notifications->changeData = $this->getSgNotificationChangeData();
        $json->contao->notifications->password = $this->getSgNotificationPassword();
        $json->contao->notifications->subscription = $this->getSgNotificationSubscription();

        $json->contao->notificationMessages = new \stdClass();
        $json->contao->notificationMessages->changeData = $this->getSgNotificationChangeDataMessage();
        $json->contao->notificationMessages->password = $this->getSgNotificationPasswordMessage();
        $json->contao->notificationMessages->subscription = $this->getSgNotificationSubscriptionMessage();

        $json->contao->notificationMessagesLanguages = new \stdClass();
        $json->contao->notificationMessagesLanguages->changeData = $this->getSgNotificationChangeDataMessageLanguage();
        $json->contao->notificationMessagesLanguages->password = $this->getSgNotificationPasswordMessageLanguage();
        $json->contao->notificationMessagesLanguages->subscription = $this->getSgNotificationSubscriptionMessageLanguage();

        $json->contao->contents = new \stdClass();
        $json->contao->contents->extranet->headline = $this->getSgContentHeadlineArticleExtranet();
        $json->contao->contents->extranet->moduleLoginGuests = $this->getSgContentModuleLoginGuestsArticleExtranet();
        $json->contao->contents->extranet->gridStartA = $this->getSgContentGridStartAArticleExtranet();
        $json->contao->contents->extranet->gridStartB = $this->getSgContentGridStartBArticleExtranet();
        $json->contao->contents->extranet->moduleLoginLogged = $this->getSgContentModuleLoginLoggedArticleExtranet();
        $json->contao->contents->extranet->moduleNav = $this->getSgContentModuleNavArticleExtranet();
        $json->contao->contents->extranet->gridStopB = $this->getSgContentGridStopBArticleExtranet();
        $json->contao->contents->extranet->text = $this->getSgContentTextArticleExtranet();
        $json->contao->contents->extranet->gridStopA = $this->getSgContentGridStopAArticleExtranet();
        $json->contao->contents->error401->headline = $this->getSgContentHeadlineArticle401();
        $json->contao->contents->error401->text = $this->getSgContentTextArticle401();
        $json->contao->contents->error401->moduleLoginGuests = $this->getSgContentModuleLoginGuestsArticle401();
        $json->contao->contents->error403->headline = $this->getSgContentHeadlineArticle403();
        $json->contao->contents->error403->text = $this->getSgContentTextArticle403();
        $json->contao->contents->error403->hyperlink = $this->getSgContentHyperlinkArticle403();
        $json->contao->contents->content->headline = $this->getSgContentHeadlineArticleContent();
        $json->contao->contents->content->text = $this->getSgContentTextArticleContent();
        $json->contao->contents->data->headline = $this->getSgContentHeadlineArticleData();
        $json->contao->contents->data->moduleData = $this->getSgContentModuleDataArticleData();
        $json->contao->contents->data->headline = $this->getSgContentHeadlineCloseAccountArticleData();
        $json->contao->contents->data->text = $this->getSgContentTextCloseAccountArticleData();
        $json->contao->contents->data->moduleCloseAccount = $this->getSgContentModuleCloseAccountArticleData();
        $json->contao->contents->dataConfirm->headline = $this->getSgContentHeadlineArticleDataConfirm();
        $json->contao->contents->dataConfirm->text = $this->getSgContentTextArticleDataConfirm();
        $json->contao->contents->dataConfirm->hyperlink = $this->getSgContentHyperlinkArticleDataConfirm();
        $json->contao->contents->password->headline = $this->getSgContentHeadlineArticlePassword();
        $json->contao->contents->password->modulePassword = $this->getSgContentModulePasswordArticlePassword();
        $json->contao->contents->passwordConfirm->headline = $this->getSgContentHeadlineArticlePasswordConfirm();
        $json->contao->contents->passwordConfirm->text = $this->getSgContentTextArticlePasswordConfirm();
        $json->contao->contents->passwordValidate->headline = $this->getSgContentHeadlineArticlePasswordValidate();
        $json->contao->contents->passwordValidate->modulePassword = $this->getSgContentModulePasswordArticlePasswordValidate();
        $json->contao->contents->logout->moduleLogout = $this->getSgContentModuleLogoutArticleLogout();
        $json->contao->contents->subscribe->headline = $this->getSgContentHeadlineArticleSubscribe();
        $json->contao->contents->subscribe->moduleSubscribe = $this->getSgContentModuleSubscribeArticleSubscribe();
        $json->contao->contents->subscribeConfirm->headline = $this->getSgContentHeadlineArticleSubscribeConfirm();
        $json->contao->contents->subscribeConfirm->text = $this->getSgContentTextArticleSubscribeConfirm();
        $json->contao->contents->subscribeValidate->headline = $this->getSgContentHeadlineArticleSubscribeValidate();
        $json->contao->contents->subscribeValidate->text = $this->getSgContentTextArticleSubscribeValidate();
        $json->contao->contents->subscribeValidate->moduleLoginGuests = $this->getSgContentModuleLoginGuestsArticleSubscribeValidate();
        $json->contao->contents->unsubscribe->headline = $this->getSgContentHeadlineArticleUnsubscribe();
        $json->contao->contents->unsubscribe->text = $this->getSgContentTextArticleUnsubscribe();
        $json->contao->contents->unsubscribe->hyperlink = $this->getSgContentHyperlinkArticleUnsubscribe();

        $json->contao->members = new \stdClass();
        $json->contao->members->example = $this->getSgMemberExample();

        $json->contao->memberGroups = new \stdClass();
        $json->contao->memberGroups->members = $this->getSgMemberGroupMembers();

        $json->archived = new \stdClass();
        $json->archived->status = $this->getSgArchived();
        $json->archived->at = $this->getSgArchivedAt();
        $json->archived->mode = $this->getSgArchivedMode();

        return $json;
    }

    public function getSgInstallComplete(): bool
    {
        return $this->sgInstallComplete;
    }

    public function setSgInstallComplete(bool $sgInstallComplete): self
    {
        $this->sgInstallComplete = $sgInstallComplete;

        return $this;
    }

    public function getSgArchived(): bool
    {
        return $this->sgArchived;
    }

    public function setSgArchived(bool $sgArchived): self
    {
        $this->sgArchived = $sgArchived;

        return $this;
    }

    public function getSgArchivedAt(): int
    {
        return $this->sgArchivedAt;
    }

    public function setSgArchivedAt(int $sgArchivedAt): self
    {
        $this->sgArchivedAt = $sgArchivedAt;

        return $this;
    }

    public function getSgArchivedMode(): string
    {
        return $this->sgArchivedMode;
    }

    public function setSgArchivedMode(string $sgArchivedMode): self
    {
        if (!\in_array($sgArchivedMode, static::ARCHIVE_MODES_ALLOWED, true)) {
            throw new \InvalidArgumentException(sprintf('Invalid archive mode "%s" given', $sgArchivedMode));
        }
        $this->sgArchivedMode = $sgArchivedMode;

        return $this;
    }

    public function getSgCanSubscribe(): bool
    {
        return $this->sgCanSubscribe;
    }

    public function setSgCanSubscribe(bool $sgCanSubscribe): self
    {
        $this->sgCanSubscribe = $sgCanSubscribe;

        return $this;
    }

    public function getSgMemberExample(): ?int
    {
        return $this->sgMemberExample;
    }

    public function setSgMemberExample(?int $sgMemberExample): self
    {
        $this->sgMemberExample = $sgMemberExample;

        return $this;
    }

    public function getSgMemberGroupMembers(): ?int
    {
        return $this->sgMemberGroupMembers;
    }

    public function setSgMemberGroupMembers(?int $sgMemberGroupMembers): self
    {
        $this->sgMemberGroupMembers = $sgMemberGroupMembers;

        return $this;
    }

    public function getSgPageExtranet(): ?int
    {
        return $this->sgPageExtranet;
    }

    public function setSgPageExtranet(?int $sgPageExtranet): self
    {
        $this->sgPageExtranet = $sgPageExtranet;

        return $this;
    }

    public function getSgPage401(): ?int
    {
        return $this->sgPage401;
    }

    public function setSgPage401(?int $sgPage401): self
    {
        $this->sgPage401 = $sgPage401;

        return $this;
    }

    public function getSgPage403(): ?int
    {
        return $this->sgPage403;
    }

    public function setSgPage403(?int $sgPage403): self
    {
        $this->sgPage403 = $sgPage403;

        return $this;
    }

    public function getSgPageContent(): ?int
    {
        return $this->sgPageContent;
    }

    public function setSgPageContent(?int $sgPageContent): self
    {
        $this->sgPageContent = $sgPageContent;

        return $this;
    }

    public function getSgPageData(): ?int
    {
        return $this->sgPageData;
    }

    public function setSgPageData(?int $sgPageData): self
    {
        $this->sgPageData = $sgPageData;

        return $this;
    }

    public function getSgPagePassword(): ?int
    {
        return $this->sgPagePassword;
    }

    public function setSgPagePassword(?int $sgPagePassword): self
    {
        $this->sgPagePassword = $sgPagePassword;

        return $this;
    }

    public function getSgPageLogout(): ?int
    {
        return $this->sgPageLogout;
    }

    public function setSgPageLogout(?int $sgPageLogout): self
    {
        $this->sgPageLogout = $sgPageLogout;

        return $this;
    }

    public function getSgPageSubscribe(): ?int
    {
        return $this->sgPageSubscribe;
    }

    public function setSgPageSubscribe(?int $sgPageSubscribe): self
    {
        $this->sgPageSubscribe = $sgPageSubscribe;

        return $this;
    }

    public function getSgPageDataConfirm(): ?int
    {
        return $this->sgPageDataConfirm;
    }

    public function setSgPageDataConfirm(?int $sgPageDataConfirm): self
    {
        $this->sgPageDataConfirm = $sgPageDataConfirm;

        return $this;
    }

    public function getSgPagePasswordConfirm(): ?int
    {
        return $this->sgPagePasswordConfirm;
    }

    public function setSgPagePasswordConfirm(?int $sgPagePasswordConfirm): self
    {
        $this->sgPagePasswordConfirm = $sgPagePasswordConfirm;

        return $this;
    }

    public function getSgPagePasswordValidate(): ?int
    {
        return $this->sgPagePasswordValidate;
    }

    public function setSgPagePasswordValidate(?int $sgPagePasswordValidate): self
    {
        $this->sgPagePasswordValidate = $sgPagePasswordValidate;

        return $this;
    }

    public function getSgPageSubscribeConfirm(): ?int
    {
        return $this->sgPageSubscribeConfirm;
    }

    public function setSgPageSubscribeConfirm(?int $sgPageSubscribeConfirm): self
    {
        $this->sgPageSubscribeConfirm = $sgPageSubscribeConfirm;

        return $this;
    }

    public function getSgPageSubscribeValidate(): ?int
    {
        return $this->sgPageSubscribeValidate;
    }

    public function setSgPageSubscribeValidate(?int $sgPageSubscribeValidate): self
    {
        $this->sgPageSubscribeValidate = $sgPageSubscribeValidate;

        return $this;
    }

    public function getSgPageUnsubscribeConfirm(): ?int
    {
        return $this->sgPageUnsubscribeConfirm;
    }

    public function setSgPageUnsubscribeConfirm(?int $sgPageUnsubscribeConfirm): self
    {
        $this->sgPageUnsubscribeConfirm = $sgPageUnsubscribeConfirm;

        return $this;
    }

    public function getSgArticleExtranet(): ?int
    {
        return $this->sgArticleExtranet;
    }

    public function setSgArticleExtranet(?int $sgArticleExtranet): self
    {
        $this->sgArticleExtranet = $sgArticleExtranet;

        return $this;
    }

    public function getSgArticle401(): ?int
    {
        return $this->sgArticle401;
    }

    public function setSgArticle401(?int $sgArticle401): self
    {
        $this->sgArticle401 = $sgArticle401;

        return $this;
    }

    public function getSgArticle403(): ?int
    {
        return $this->sgArticle403;
    }

    public function setSgArticle403(?int $sgArticle403): self
    {
        $this->sgArticle403 = $sgArticle403;

        return $this;
    }

    public function getSgArticleContent(): ?int
    {
        return $this->sgArticleContent;
    }

    public function setSgArticleContent(?int $sgArticleContent): self
    {
        $this->sgArticleContent = $sgArticleContent;

        return $this;
    }

    public function getSgArticleData(): ?int
    {
        return $this->sgArticleData;
    }

    public function setSgArticleData(?int $sgArticleData): self
    {
        $this->sgArticleData = $sgArticleData;

        return $this;
    }

    public function getSgArticleDataConfirm(): ?int
    {
        return $this->sgArticleDataConfirm;
    }

    public function setSgArticleDataConfirm(?int $sgArticleDataConfirm): self
    {
        $this->sgArticleDataConfirm = $sgArticleDataConfirm;

        return $this;
    }

    public function getSgArticlePassword(): ?int
    {
        return $this->sgArticlePassword;
    }

    public function setSgArticlePassword(?int $sgArticlePassword): self
    {
        $this->sgArticlePassword = $sgArticlePassword;

        return $this;
    }

    public function getSgArticlePasswordConfirm(): ?int
    {
        return $this->sgArticlePasswordConfirm;
    }

    public function setSgArticlePasswordConfirm(?int $sgArticlePasswordConfirm): self
    {
        $this->sgArticlePasswordConfirm = $sgArticlePasswordConfirm;

        return $this;
    }

    public function getSgArticlePasswordValidate(): ?int
    {
        return $this->sgArticlePasswordValidate;
    }

    public function setSgArticlePasswordValidate(?int $sgArticlePasswordValidate): self
    {
        $this->sgArticlePasswordValidate = $sgArticlePasswordValidate;

        return $this;
    }

    public function getSgArticleLogout(): ?int
    {
        return $this->sgArticleLogout;
    }

    public function setSgArticleLogout(?int $sgArticleLogout): self
    {
        $this->sgArticleLogout = $sgArticleLogout;

        return $this;
    }

    public function getSgArticleSubscribe(): ?int
    {
        return $this->sgArticleSubscribe;
    }

    public function setSgArticleSubscribe(?int $sgArticleSubscribe): self
    {
        $this->sgArticleSubscribe = $sgArticleSubscribe;

        return $this;
    }

    public function getSgArticleSubscribeConfirm(): ?int
    {
        return $this->sgArticleSubscribeConfirm;
    }

    public function setSgArticleSubscribeConfirm(?int $sgArticleSubscribeConfirm): self
    {
        $this->sgArticleSubscribeConfirm = $sgArticleSubscribeConfirm;

        return $this;
    }

    public function getSgArticleSubscribeValidate(): ?int
    {
        return $this->sgArticleSubscribeValidate;
    }

    public function setSgArticleSubscribeValidate(?int $sgArticleSubscribeValidate): self
    {
        $this->sgArticleSubscribeValidate = $sgArticleSubscribeValidate;

        return $this;
    }

    public function getSgArticleUnsubscribeConfirm(): ?int
    {
        return $this->sgArticleUnsubscribeConfirm;
    }

    public function setSgArticleUnsubscribeConfirm(?int $sgArticleUnsubscribeConfirm): self
    {
        $this->sgArticleUnsubscribeConfirm = $sgArticleUnsubscribeConfirm;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgModuleLogin(): ?int
    {
        return $this->sgModuleLogin;
    }

    /**
     * @param ?int $sgModuleLogin
     */
    public function setSgModuleLogin(?int $sgModuleLogin): self
    {
        $this->sgModuleLogin = $sgModuleLogin;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgModuleLogout(): ?int
    {
        return $this->sgModuleLogout;
    }

    /**
     * @param ?int $sgModuleLogout
     */
    public function setSgModuleLogout(?int $sgModuleLogout): self
    {
        $this->sgModuleLogout = $sgModuleLogout;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgModuleData(): ?int
    {
        return $this->sgModuleData;
    }

    /**
     * @param ?int $sgModuleData
     */
    public function setSgModuleData(?int $sgModuleData): self
    {
        $this->sgModuleData = $sgModuleData;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgModulePassword(): ?int
    {
        return $this->sgModulePassword;
    }

    /**
     * @param ?int $sgModulePassword
     */
    public function setSgModulePassword(?int $sgModulePassword): self
    {
        $this->sgModulePassword = $sgModulePassword;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgModuleNav(): ?int
    {
        return $this->sgModuleNav;
    }

    /**
     * @param ?int $sgModuleNav
     */
    public function setSgModuleNav(?int $sgModuleNav): self
    {
        $this->sgModuleNav = $sgModuleNav;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgModuleSubscribe(): ?int
    {
        return $this->sgModuleSubscribe;
    }

    /**
     * @param ?int $sgModuleSubscribe
     */
    public function setSgModuleSubscribe(?int $sgModuleSubscribe): self
    {
        $this->sgModuleSubscribe = $sgModuleSubscribe;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgModuleCloseAccount(): ?int
    {
        return $this->sgModuleCloseAccount;
    }

    /**
     * @param ?int $sgModuleCloseAccount
     */
    public function setSgModuleCloseAccount(?int $sgModuleCloseAccount): self
    {
        $this->sgModuleCloseAccount = $sgModuleCloseAccount;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgNotificationChangeData(): ?int
    {
        return $this->sgNotificationChangeData;
    }

    /**
     * @param ?int $sgNotificationChangeData
     */
    public function setSgNotificationChangeData(?int $sgNotificationChangeData): self
    {
        $this->sgNotificationChangeData = $sgNotificationChangeData;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgNotificationChangeDataMessage(): ?int
    {
        return $this->sgNotificationChangeDataMessage;
    }

    /**
     * @param ?int $sgNotificationChangeDataMessage
     */
    public function setSgNotificationChangeDataMessage(?int $sgNotificationChangeDataMessage): self
    {
        $this->sgNotificationChangeDataMessage = $sgNotificationChangeDataMessage;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgNotificationChangeDataMessageLanguage(): ?int
    {
        return $this->sgNotificationChangeDataMessageLanguage;
    }

    /**
     * @param ?int $sgNotificationChangeDataMessageLanguage
     */
    public function setSgNotificationChangeDataMessageLanguage(?int $sgNotificationChangeDataMessageLanguage): self
    {
        $this->sgNotificationChangeDataMessageLanguage = $sgNotificationChangeDataMessageLanguage;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgNotificationPassword(): ?int
    {
        return $this->sgNotificationPassword;
    }

    /**
     * @param ?int $sgNotificationPassword
     */
    public function setSgNotificationPassword(?int $sgNotificationPassword): self
    {
        $this->sgNotificationPassword = $sgNotificationPassword;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgNotificationPasswordMessage(): ?int
    {
        return $this->sgNotificationPasswordMessage;
    }

    /**
     * @param ?int $sgNotificationPasswordMessage
     */
    public function setSgNotificationPasswordMessage(?int $sgNotificationPasswordMessage): self
    {
        $this->sgNotificationPasswordMessage = $sgNotificationPasswordMessage;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgNotificationPasswordMessageLanguage(): ?int
    {
        return $this->sgNotificationPasswordMessageLanguage;
    }

    /**
     * @param ?int $sgNotificationPasswordMessageLanguage
     */
    public function setSgNotificationPasswordMessageLanguage(?int $sgNotificationPasswordMessageLanguage): self
    {
        $this->sgNotificationPasswordMessageLanguage = $sgNotificationPasswordMessageLanguage;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgNotificationSubscription(): ?int
    {
        return $this->sgNotificationSubscription;
    }

    /**
     * @param ?int $sgNotificationSubscription
     */
    public function setSgNotificationSubscription(?int $sgNotificationSubscription): self
    {
        $this->sgNotificationSubscription = $sgNotificationSubscription;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgNotificationSubscriptionMessage(): ?int
    {
        return $this->sgNotificationSubscriptionMessage;
    }

    /**
     * @param ?int $sgNotificationSubscriptionMessage
     */
    public function setSgNotificationSubscriptionMessage(?int $sgNotificationSubscriptionMessage): self
    {
        $this->sgNotificationSubscriptionMessage = $sgNotificationSubscriptionMessage;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgNotificationSubscriptionMessageLanguage(): ?int
    {
        return $this->sgNotificationSubscriptionMessageLanguage;
    }

    /**
     * @param ?int $sgNotificationSubscriptionMessageLanguage
     */
    public function setSgNotificationSubscriptionMessageLanguage(?int $sgNotificationSubscriptionMessageLanguage): self
    {
        $this->sgNotificationSubscriptionMessageLanguage = $sgNotificationSubscriptionMessageLanguage;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentHeadlineArticleExtranet(): ?int
    {
        return $this->sgContentHeadlineArticleExtranet;
    }

    public function setSgContentHeadlineArticleExtranet(?int $sgContentHeadlineArticleExtranet): self
    {
        $this->sgContentHeadlineArticleExtranet = $sgContentHeadlineArticleExtranet;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentModuleLoginGuestsArticleExtranet(): ?int
    {
        return $this->sgContentModuleLoginGuestsArticleExtranet;
    }

    public function setSgContentModuleLoginGuestsArticleExtranet(?int $sgContentModuleLoginGuestsArticleExtranet): self
    {
        $this->sgContentModuleLoginGuestsArticleExtranet = $sgContentModuleLoginGuestsArticleExtranet;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentGridStartAArticleExtranet(): ?int
    {
        return $this->sgContentGridStartAArticleExtranet;
    }

    public function setSgContentGridStartAArticleExtranet(?int $sgContentGridStartAArticleExtranet): self
    {
        $this->sgContentGridStartAArticleExtranet = $sgContentGridStartAArticleExtranet;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentGridStartBArticleExtranet(): ?int
    {
        return $this->sgContentGridStartBArticleExtranet;
    }

    public function setSgContentGridStartBArticleExtranet(?int $sgContentGridStartBArticleExtranet): self
    {
        $this->sgContentGridStartBArticleExtranet = $sgContentGridStartBArticleExtranet;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentModuleLoginLoggedArticleExtranet(): ?int
    {
        return $this->sgContentModuleLoginLoggedArticleExtranet;
    }

    public function setSgContentModuleLoginLoggedArticleExtranet(?int $sgContentModuleLoginLoggedArticleExtranet): self
    {
        $this->sgContentModuleLoginLoggedArticleExtranet = $sgContentModuleLoginLoggedArticleExtranet;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentModuleNavArticleExtranet(): ?int
    {
        return $this->sgContentModuleNavArticleExtranet;
    }

    public function setSgContentModuleNavArticleExtranet(?int $sgContentModuleNavArticleExtranet): self
    {
        $this->sgContentModuleNavArticleExtranet = $sgContentModuleNavArticleExtranet;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentGridStopBArticleExtranet(): ?int
    {
        return $this->sgContentGridStopBArticleExtranet;
    }

    public function setSgContentGridStopBArticleExtranet(?int $sgContentGridStopBArticleExtranet): self
    {
        $this->sgContentGridStopBArticleExtranet = $sgContentGridStopBArticleExtranet;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentTextArticleExtranet(): ?int
    {
        return $this->sgContentTextArticleExtranet;
    }

    public function setSgContentTextArticleExtranet(?int $sgContentTextArticleExtranet): self
    {
        $this->sgContentTextArticleExtranet = $sgContentTextArticleExtranet;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentGridStopAArticleExtranet(): ?int
    {
        return $this->sgContentGridStopAArticleExtranet;
    }

    public function setSgContentGridStopAArticleExtranet(?int $sgContentGridStopAArticleExtranet): self
    {
        $this->sgContentGridStopAArticleExtranet = $sgContentGridStopAArticleExtranet;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentHeadlineArticle401(): ?int
    {
        return $this->sgContentHeadlineArticle401;
    }

    public function setSgContentHeadlineArticle401(?int $sgContentHeadlineArticle401): self
    {
        $this->sgContentHeadlineArticle401 = $sgContentHeadlineArticle401;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentTextArticle401(): ?int
    {
        return $this->sgContentTextArticle401;
    }

    public function setSgContentTextArticle401(?int $sgContentTextArticle401): self
    {
        $this->sgContentTextArticle401 = $sgContentTextArticle401;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentModuleLoginGuestsArticle401(): ?int
    {
        return $this->sgContentModuleLoginGuestsArticle401;
    }

    public function setSgContentModuleLoginGuestsArticle401(?int $sgContentModuleLoginGuestsArticle401): self
    {
        $this->sgContentModuleLoginGuestsArticle401 = $sgContentModuleLoginGuestsArticle401;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentHeadlineArticle403(): ?int
    {
        return $this->sgContentHeadlineArticle403;
    }

    public function setSgContentHeadlineArticle403(?int $sgContentHeadlineArticle403): self
    {
        $this->sgContentHeadlineArticle403 = $sgContentHeadlineArticle403;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentTextArticle403(): ?int
    {
        return $this->sgContentTextArticle403;
    }

    public function setSgContentTextArticle403(?int $sgContentTextArticle403): self
    {
        $this->sgContentTextArticle403 = $sgContentTextArticle403;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentHyperlinkArticle403(): ?int
    {
        return $this->sgContentHyperlinkArticle403;
    }

    public function setSgContentHyperlinkArticle403(?int $sgContentHyperlinkArticle403): self
    {
        $this->sgContentHyperlinkArticle403 = $sgContentHyperlinkArticle403;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentHeadlineArticleContent(): ?int
    {
        return $this->sgContentHeadlineArticleContent;
    }

    public function setSgContentHeadlineArticleContent(?int $sgContentHeadlineArticleContent): self
    {
        $this->sgContentHeadlineArticleContent = $sgContentHeadlineArticleContent;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentTextArticleContent(): ?int
    {
        return $this->sgContentTextArticleContent;
    }

    public function setSgContentTextArticleContent(?int $sgContentTextArticleContent): self
    {
        $this->sgContentTextArticleContent = $sgContentTextArticleContent;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentHeadlineArticleData(): ?int
    {
        return $this->sgContentHeadlineArticleData;
    }

    public function setSgContentHeadlineArticleData(?int $sgContentHeadlineArticleData): self
    {
        $this->sgContentHeadlineArticleData = $sgContentHeadlineArticleData;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentModuleDataArticleData(): ?int
    {
        return $this->sgContentModuleDataArticleData;
    }

    public function setSgContentModuleDataArticleData(?int $sgContentModuleDataArticleData): self
    {
        $this->sgContentModuleDataArticleData = $sgContentModuleDataArticleData;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentHeadlineCloseAccountArticleData(): ?int
    {
        return $this->sgContentHeadlineCloseAccountArticleData;
    }

    public function setSgContentHeadlineCloseAccountArticleData(?int $sgContentHeadlineCloseAccountArticleData): self
    {
        $this->sgContentHeadlineCloseAccountArticleData = $sgContentHeadlineCloseAccountArticleData;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentTextCloseAccountArticleData(): ?int
    {
        return $this->sgContentTextCloseAccountArticleData;
    }

    public function setSgContentTextCloseAccountArticleData(?int $sgContentTextCloseAccountArticleData): self
    {
        $this->sgContentTextCloseAccountArticleData = $sgContentTextCloseAccountArticleData;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentModuleCloseAccountArticleData(): ?int
    {
        return $this->sgContentModuleCloseAccountArticleData;
    }

    public function setSgContentModuleCloseAccountArticleData(?int $sgContentModuleCloseAccountArticleData): self
    {
        $this->sgContentModuleCloseAccountArticleData = $sgContentModuleCloseAccountArticleData;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentHeadlineArticleDataConfirm(): ?int
    {
        return $this->sgContentHeadlineArticleDataConfirm;
    }

    public function setSgContentHeadlineArticleDataConfirm(?int $sgContentHeadlineArticleDataConfirm): self
    {
        $this->sgContentHeadlineArticleDataConfirm = $sgContentHeadlineArticleDataConfirm;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentTextArticleDataConfirm(): ?int
    {
        return $this->sgContentTextArticleDataConfirm;
    }

    public function setSgContentTextArticleDataConfirm(?int $sgContentTextArticleDataConfirm): self
    {
        $this->sgContentTextArticleDataConfirm = $sgContentTextArticleDataConfirm;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentHyperlinkArticleDataConfirm(): ?int
    {
        return $this->sgContentHyperlinkArticleDataConfirm;
    }

    public function setSgContentHyperlinkArticleDataConfirm(?int $sgContentHyperlinkArticleDataConfirm): self
    {
        $this->sgContentHyperlinkArticleDataConfirm = $sgContentHyperlinkArticleDataConfirm;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentHeadlineArticlePassword(): ?int
    {
        return $this->sgContentHeadlineArticlePassword;
    }

    public function setSgContentHeadlineArticlePassword(?int $sgContentHeadlineArticlePassword): self
    {
        $this->sgContentHeadlineArticlePassword = $sgContentHeadlineArticlePassword;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentModulePasswordArticlePassword(): ?int
    {
        return $this->sgContentModulePasswordArticlePassword;
    }

    public function setSgContentModulePasswordArticlePassword(?int $sgContentModulePasswordArticlePassword): self
    {
        $this->sgContentModulePasswordArticlePassword = $sgContentModulePasswordArticlePassword;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentHeadlineArticlePasswordConfirm(): ?int
    {
        return $this->sgContentHeadlineArticlePasswordConfirm;
    }

    public function setSgContentHeadlineArticlePasswordConfirm(?int $sgContentHeadlineArticlePasswordConfirm): self
    {
        $this->sgContentHeadlineArticlePasswordConfirm = $sgContentHeadlineArticlePasswordConfirm;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentTextArticlePasswordConfirm(): ?int
    {
        return $this->sgContentTextArticlePasswordConfirm;
    }

    public function setSgContentTextArticlePasswordConfirm(?int $sgContentTextArticlePasswordConfirm): self
    {
        $this->sgContentTextArticlePasswordConfirm = $sgContentTextArticlePasswordConfirm;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentHeadlineArticlePasswordValidate(): ?int
    {
        return $this->sgContentHeadlineArticlePasswordValidate;
    }

    public function setSgContentHeadlineArticlePasswordValidate(?int $sgContentHeadlineArticlePasswordValidate): self
    {
        $this->sgContentHeadlineArticlePasswordValidate = $sgContentHeadlineArticlePasswordValidate;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentModulePasswordArticlePasswordValidate(): ?int
    {
        return $this->sgContentModulePasswordArticlePasswordValidate;
    }

    public function setSgContentModulePasswordArticlePasswordValidate(?int $sgContentModulePasswordArticlePasswordValidate): self
    {
        $this->sgContentModulePasswordArticlePasswordValidate = $sgContentModulePasswordArticlePasswordValidate;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentModuleLogoutArticleLogout(): ?int
    {
        return $this->sgContentModuleLogoutArticleLogout;
    }

    public function setSgContentModuleLogoutArticleLogout(?int $sgContentModuleLogoutArticleLogout): self
    {
        $this->sgContentModuleLogoutArticleLogout = $sgContentModuleLogoutArticleLogout;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentHeadlineArticleSubscribe(): ?int
    {
        return $this->sgContentHeadlineArticleSubscribe;
    }

    public function setSgContentHeadlineArticleSubscribe(?int $sgContentHeadlineArticleSubscribe): self
    {
        $this->sgContentHeadlineArticleSubscribe = $sgContentHeadlineArticleSubscribe;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentModuleSubscribeArticleSubscribe(): ?int
    {
        return $this->sgContentModuleSubscribeArticleSubscribe;
    }

    public function setSgContentModuleSubscribeArticleSubscribe(?int $sgContentModuleSubscribeArticleSubscribe): self
    {
        $this->sgContentModuleSubscribeArticleSubscribe = $sgContentModuleSubscribeArticleSubscribe;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentHeadlineArticleSubscribeConfirm(): ?int
    {
        return $this->sgContentHeadlineArticleSubscribeConfirm;
    }

    public function setSgContentHeadlineArticleSubscribeConfirm(?int $sgContentHeadlineArticleSubscribeConfirm): self
    {
        $this->sgContentHeadlineArticleSubscribeConfirm = $sgContentHeadlineArticleSubscribeConfirm;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentTextArticleSubscribeConfirm(): ?int
    {
        return $this->sgContentTextArticleSubscribeConfirm;
    }

    public function setSgContentTextArticleSubscribeConfirm(?int $sgContentTextArticleSubscribeConfirm): self
    {
        $this->sgContentTextArticleSubscribeConfirm = $sgContentTextArticleSubscribeConfirm;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentHeadlineArticleSubscribeValidate(): ?int
    {
        return $this->sgContentHeadlineArticleSubscribeValidate;
    }

    public function setSgContentHeadlineArticleSubscribeValidate(?int $sgContentHeadlineArticleSubscribeValidate): self
    {
        $this->sgContentHeadlineArticleSubscribeValidate = $sgContentHeadlineArticleSubscribeValidate;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentTextArticleSubscribeValidate(): ?int
    {
        return $this->sgContentTextArticleSubscribeValidate;
    }

    public function setSgContentTextArticleSubscribeValidate(?int $sgContentTextArticleSubscribeValidate): self
    {
        $this->sgContentTextArticleSubscribeValidate = $sgContentTextArticleSubscribeValidate;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentModuleLoginGuestsArticleSubscribeValidate(): ?int
    {
        return $this->sgContentModuleLoginGuestsArticleSubscribeValidate;
    }

    public function setSgContentModuleLoginGuestsArticleSubscribeValidate(?int $sgContentModuleLoginGuestsArticleSubscribeValidate): self
    {
        $this->sgContentModuleLoginGuestsArticleSubscribeValidate = $sgContentModuleLoginGuestsArticleSubscribeValidate;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentHeadlineArticleUnsubscribe(): ?int
    {
        return $this->sgContentHeadlineArticleUnsubscribe;
    }

    public function setSgContentHeadlineArticleUnsubscribe(?int $sgContentHeadlineArticleUnsubscribe): self
    {
        $this->sgContentHeadlineArticleUnsubscribe = $sgContentHeadlineArticleUnsubscribe;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentTextArticleUnsubscribe(): ?int
    {
        return $this->sgContentTextArticleUnsubscribe;
    }

    public function setSgContentTextArticleUnsubscribe(?int $sgContentTextArticleUnsubscribe): self
    {
        $this->sgContentTextArticleUnsubscribe = $sgContentTextArticleUnsubscribe;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentHyperlinkArticleUnsubscribe(): ?int
    {
        return $this->sgContentHyperlinkArticleUnsubscribe;
    }

    public function setSgContentHyperlinkArticleUnsubscribe(?int $sgContentHyperlinkArticleUnsubscribe): self
    {
        $this->sgContentHyperlinkArticleUnsubscribe = $sgContentHyperlinkArticleUnsubscribe;

        return $this;
    }

    public function getSgMemberGroupMembersTitle(): ?string
    {
        return $this->sgMemberGroupMembersTitle;
    }

    public function setSgMemberGroupMembersTitle(?string $sgMemberGroupMembersTitle): self
    {
        $this->sgMemberGroupMembersTitle = $sgMemberGroupMembersTitle;

        return $this;
    }

    public function getSgPageExtranetTitle(): ?string
    {
        return $this->sgPageExtranetTitle;
    }

    public function setSgPageExtranetTitle(?string $sgPageExtranetTitle): self
    {
        $this->sgPageExtranetTitle = $sgPageExtranetTitle;

        return $this;
    }

    public function getSgExtranetFolder(): ?string
    {
        return $this->sgExtranetFolder;
    }

    public function setSgExtranetFolder(?string $sgExtranetFolder): self
    {
        $this->sgExtranetFolder = $sgExtranetFolder;

        return $this;
    }
}
