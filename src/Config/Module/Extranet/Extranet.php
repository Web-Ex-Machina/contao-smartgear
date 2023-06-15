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
    protected $sgContentArticleExtranetHeadline;
    /** @var int */
    protected $sgContentArticleExtranetModuleLoginGuests;
    /** @var int */
    protected $sgContentArticleExtranetGridStartA;
    /** @var int */
    protected $sgContentArticleExtranetGridStartB;
    /** @var int */
    protected $sgContentArticleExtranetModuleLoginLogged;
    /** @var int */
    protected $sgContentArticleExtranetModuleNav;
    /** @var int */
    protected $sgContentArticleExtranetGridStopB;
    /** @var int */
    protected $sgContentArticleExtranetGridStopA;
    /** @var int */
    protected $sgContentArticle401Headline;
    /** @var int */
    protected $sgContentArticle401Text;
    /** @var int */
    protected $sgContentArticle401ModuleLoginGuests;
    /** @var int */
    protected $sgContentArticle403Headline;
    /** @var int */
    protected $sgContentArticle403Text;
    /** @var int */
    protected $sgContentArticle403Hyperlink;
    /** @var int */
    protected $sgContentArticleContentHeadline;
    /** @var int */
    protected $sgContentArticleContentText;
    /** @var int */
    protected $sgContentArticleDataHeadline;
    /** @var int */
    protected $sgContentArticleDataModuleData;
    /** @var int */
    protected $sgContentArticleDataHeadlineCloseAccount;
    /** @var int */
    protected $sgContentArticleDataTextCloseAccount;
    /** @var int */
    protected $sgContentArticleDataModuleCloseAccount;
    /** @var int */
    protected $sgContentArticleDataConfirmHeadline;
    /** @var int */
    protected $sgContentArticleDataConfirmText;
    /** @var int */
    protected $sgContentArticleDataConfirmHyperlink;
    /** @var int */
    protected $sgContentArticlePasswordHeadline;
    /** @var int */
    protected $sgContentArticlePasswordModulePassword;
    /** @var int */
    protected $sgContentArticlePasswordConfirmHeadline;
    /** @var int */
    protected $sgContentArticlePasswordConfirmText;
    /** @var int */
    protected $sgContentArticlePasswordValidateHeadline;
    /** @var int */
    protected $sgContentArticlePasswordValidateModulePassword;
    /** @var int */
    protected $sgContentArticleLogoutModuleLogout;
    /** @var int */
    protected $sgContentArticleSubscribeHeadline;
    /** @var int */
    protected $sgContentArticleSubscribeModuleSubscribe;
    /** @var int */
    protected $sgContentArticleSubscribeConfirmHeadline;
    /** @var int */
    protected $sgContentArticleSubscribeConfirmText;
    /** @var int */
    protected $sgContentArticleSubscribeValidateHeadline;
    /** @var int */
    protected $sgContentArticleSubscribeValidateText;
    /** @var int */
    protected $sgContentArticleSubscribeValidateModuleLoginGuests;
    /** @var int */
    protected $sgContentArticleUnsubscribeHeadline;
    /** @var int */
    protected $sgContentArticleUnsubscribeText;
    /** @var int */
    protected $sgContentArticleUnsubscribeHyperlink;
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
            ->setSgContentArticleExtranetHeadline(null)
            ->setSgContentArticleExtranetModuleLoginGuests(null)
            ->setSgContentArticleExtranetGridStartA(null)
            ->setSgContentArticleExtranetGridStartB(null)
            ->setSgContentArticleExtranetModuleLoginLogged(null)
            ->setSgContentArticleExtranetModuleNav(null)
            ->setSgContentArticleExtranetGridStopB(null)
            ->setSgContentArticleExtranetGridStopA(null)
            ->setSgContentArticle401Headline(null)
            ->setSgContentArticle401Text(null)
            ->setSgContentArticle401ModuleLoginGuests(null)
            ->setSgContentArticle403Headline(null)
            ->setSgContentArticle403Text(null)
            ->setSgContentArticle403Hyperlink(null)
            ->setSgContentArticleContentHeadline(null)
            ->setSgContentArticleContentText(null)
            ->setSgContentArticleDataHeadline(null)
            ->setSgContentArticleDataModuleData(null)
            ->setSgContentArticleDataHeadlineCloseAccount(null)
            ->setSgContentArticleDataTextCloseAccount(null)
            ->setSgContentArticleDataModuleCloseAccount(null)
            ->setSgContentArticleDataConfirmHeadline(null)
            ->setSgContentArticleDataConfirmText(null)
            ->setSgContentArticleDataConfirmHyperlink(null)
            ->setSgContentArticlePasswordHeadline(null)
            ->setSgContentArticlePasswordModulePassword(null)
            ->setSgContentArticlePasswordConfirmHeadline(null)
            ->setSgContentArticlePasswordConfirmText(null)
            ->setSgContentArticlePasswordValidateHeadline(null)
            ->setSgContentArticlePasswordValidateModulePassword(null)
            ->setSgContentArticleLogoutModuleLogout(null)
            ->setSgContentArticleSubscribeHeadline(null)
            ->setSgContentArticleSubscribeModuleSubscribe(null)
            ->setSgContentArticleSubscribeConfirmHeadline(null)
            ->setSgContentArticleSubscribeConfirmText(null)
            ->setSgContentArticleSubscribeValidateHeadline(null)
            ->setSgContentArticleSubscribeValidateText(null)
            ->setSgContentArticleSubscribeValidateModuleLoginGuests(null)
            ->setSgContentArticleUnsubscribeHeadline(null)
            ->setSgContentArticleUnsubscribeText(null)
            ->setSgContentArticleUnsubscribeHyperlink(null)
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
            ->setSgPageUnsubscribeConfirm($json->contao->pages->unsubscribeConfirm ?? null)
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
            ->setSgArticleUnsubscribeConfirm($json->contao->articles->unsubscribeConfirm ?? null)
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
            ->setSgContentArticleExtranetHeadline($json->contao->contents->extranet->headline ?? null)
            ->setSgContentArticleExtranetModuleLoginGuests($json->contao->contents->extranet->moduleLoginGuests ?? null)
            ->setSgContentArticleExtranetGridStartA($json->contao->contents->extranet->gridStartA ?? null)
            ->setSgContentArticleExtranetGridStartB($json->contao->contents->extranet->gridStartB ?? null)
            ->setSgContentArticleExtranetModuleLoginLogged($json->contao->contents->extranet->moduleLoginLogged ?? null)
            ->setSgContentArticleExtranetModuleNav($json->contao->contents->extranet->moduleNav ?? null)
            ->setSgContentArticleExtranetGridStopB($json->contao->contents->extranet->gridStopB ?? null)
            ->setSgContentArticleExtranetGridStopA($json->contao->contents->extranet->gridStopA ?? null)
            ->setSgContentArticle401Headline($json->contao->contents->error401->headline ?? null)
            ->setSgContentArticle401Text($json->contao->contents->error401->text ?? null)
            ->setSgContentArticle401ModuleLoginGuests($json->contao->contents->error401->moduleLoginGuests ?? null)
            ->setSgContentArticle403Headline($json->contao->contents->error403->headline ?? null)
            ->setSgContentArticle403Text($json->contao->contents->error403->text ?? null)
            ->setSgContentArticle403Hyperlink($json->contao->contents->error403->hyperlink ?? null)
            ->setSgContentArticleContentHeadline($json->contao->contents->content->headline ?? null)
            ->setSgContentArticleContentText($json->contao->contents->content->text ?? null)
            ->setSgContentArticleDataHeadline($json->contao->contents->data->headline ?? null)
            ->setSgContentArticleDataModuleData($json->contao->contents->data->moduleData ?? null)
            ->setSgContentArticleDataHeadlineCloseAccount($json->contao->contents->data->headlineCloseAccount ?? null)
            ->setSgContentArticleDataTextCloseAccount($json->contao->contents->data->textCloseAccount ?? null)
            ->setSgContentArticleDataModuleCloseAccount($json->contao->contents->data->moduleCloseAccount ?? null)
            ->setSgContentArticleDataConfirmHeadline($json->contao->contents->dataConfirm->headline ?? null)
            ->setSgContentArticleDataConfirmText($json->contao->contents->dataConfirm->text ?? null)
            ->setSgContentArticleDataConfirmHyperlink($json->contao->contents->dataConfirm->hyperlink ?? null)
            ->setSgContentArticlePasswordHeadline($json->contao->contents->password->headline ?? null)
            ->setSgContentArticlePasswordModulePassword($json->contao->contents->password->modulePassword ?? null)
            ->setSgContentArticlePasswordConfirmHeadline($json->contao->contents->passwordConfirm->headline ?? null)
            ->setSgContentArticlePasswordConfirmText($json->contao->contents->passwordConfirm->text ?? null)
            ->setSgContentArticlePasswordValidateHeadline($json->contao->contents->passwordValidate->headline ?? null)
            ->setSgContentArticlePasswordValidateModulePassword($json->contao->contents->passwordValidate->modulePassword ?? null)
            ->setSgContentArticleLogoutModuleLogout($json->contao->contents->logout->moduleLogout ?? null)
            ->setSgContentArticleSubscribeHeadline($json->contao->contents->subscribe->headline ?? null)
            ->setSgContentArticleSubscribeModuleSubscribe($json->contao->contents->subscribe->moduleSubscribe ?? null)
            ->setSgContentArticleSubscribeConfirmHeadline($json->contao->contents->subscribeConfirm->headline ?? null)
            ->setSgContentArticleSubscribeConfirmText($json->contao->contents->subscribeConfirm->text ?? null)
            ->setSgContentArticleSubscribeValidateHeadline($json->contao->contents->subscribeValidate->headline ?? null)
            ->setSgContentArticleSubscribeValidateText($json->contao->contents->subscribeValidate->text ?? null)
            ->setSgContentArticleSubscribeValidateModuleLoginGuests($json->contao->contents->subscribeValidate->moduleLoginGuests ?? null)
            ->setSgContentArticleUnsubscribeHeadline($json->contao->contents->unsubscribe->headline ?? null)
            ->setSgContentArticleUnsubscribeText($json->contao->contents->unsubscribe->text ?? null)
            ->setSgContentArticleUnsubscribeHyperlink($json->contao->contents->unsubscribe->hyperlink ?? null)

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
        $json->contao->pages->unsubscribeConfirm = $this->getSgPageUnsubscribeConfirm();

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
        $json->contao->articles->unsubscribeConfirm = $this->getSgArticleUnsubscribeConfirm();

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
        $json->contao->contents->extranet = new \stdClass();
        $json->contao->contents->extranet->headline = $this->getSgContentArticleExtranetHeadline();
        $json->contao->contents->extranet->moduleLoginGuests = $this->getSgContentArticleExtranetModuleLoginGuests();
        $json->contao->contents->extranet->gridStartA = $this->getSgContentArticleExtranetGridStartA();
        $json->contao->contents->extranet->gridStartB = $this->getSgContentArticleExtranetGridStartB();
        $json->contao->contents->extranet->moduleLoginLogged = $this->getSgContentArticleExtranetModuleLoginLogged();
        $json->contao->contents->extranet->moduleNav = $this->getSgContentArticleExtranetModuleNav();
        $json->contao->contents->extranet->gridStopB = $this->getSgContentArticleExtranetGridStopB();
        $json->contao->contents->extranet->gridStopA = $this->getSgContentArticleExtranetGridStopA();

        $json->contao->contents->error401 = new \stdClass();
        $json->contao->contents->error401->headline = $this->getSgContentArticle401Headline();
        $json->contao->contents->error401->text = $this->getSgContentArticle401Text();
        $json->contao->contents->error401->moduleLoginGuests = $this->getSgContentArticle401ModuleLoginGuests();

        $json->contao->contents->error403 = new \stdClass();
        $json->contao->contents->error403->headline = $this->getSgContentArticle403Headline();
        $json->contao->contents->error403->text = $this->getSgContentArticle403Text();
        $json->contao->contents->error403->hyperlink = $this->getSgContentArticle403Hyperlink();

        $json->contao->contents->content = new \stdClass();
        $json->contao->contents->content->headline = $this->getSgContentArticleContentHeadline();
        $json->contao->contents->content->text = $this->getSgContentArticleContentText();

        $json->contao->contents->data = new \stdClass();
        $json->contao->contents->data->headline = $this->getSgContentArticleDataHeadline();
        $json->contao->contents->data->moduleData = $this->getSgContentArticleDataModuleData();
        $json->contao->contents->data->headlineCloseAccount = $this->getSgContentArticleDataHeadlineCloseAccount();
        $json->contao->contents->data->textCloseAccount = $this->getSgContentArticleDataTextCloseAccount();
        $json->contao->contents->data->moduleCloseAccount = $this->getSgContentArticleDataModuleCloseAccount();

        $json->contao->contents->dataConfirm = new \stdClass();
        $json->contao->contents->dataConfirm->headline = $this->getSgContentArticleDataConfirmHeadline();
        $json->contao->contents->dataConfirm->text = $this->getSgContentArticleDataConfirmText();
        $json->contao->contents->dataConfirm->hyperlink = $this->getSgContentArticleDataConfirmHyperlink();

        $json->contao->contents->password = new \stdClass();
        $json->contao->contents->password->headline = $this->getSgContentArticlePasswordHeadline();
        $json->contao->contents->password->modulePassword = $this->getSgContentArticlePasswordModulePassword();

        $json->contao->contents->passwordConfirm = new \stdClass();
        $json->contao->contents->passwordConfirm->headline = $this->getSgContentArticlePasswordConfirmHeadline();
        $json->contao->contents->passwordConfirm->text = $this->getSgContentArticlePasswordConfirmText();

        $json->contao->contents->passwordValidate = new \stdClass();
        $json->contao->contents->passwordValidate->headline = $this->getSgContentArticlePasswordValidateHeadline();
        $json->contao->contents->passwordValidate->modulePassword = $this->getSgContentArticlePasswordValidateModulePassword();

        $json->contao->contents->logout = new \stdClass();
        $json->contao->contents->logout->moduleLogout = $this->getSgContentArticleLogoutModuleLogout();

        $json->contao->contents->subscribe = new \stdClass();
        $json->contao->contents->subscribe->headline = $this->getSgContentArticleSubscribeHeadline();
        $json->contao->contents->subscribe->moduleSubscribe = $this->getSgContentArticleSubscribeModuleSubscribe();

        $json->contao->contents->subscribeConfirm = new \stdClass();
        $json->contao->contents->subscribeConfirm->headline = $this->getSgContentArticleSubscribeConfirmHeadline();
        $json->contao->contents->subscribeConfirm->text = $this->getSgContentArticleSubscribeConfirmText();

        $json->contao->contents->subscribeValidate = new \stdClass();
        $json->contao->contents->subscribeValidate->headline = $this->getSgContentArticleSubscribeValidateHeadline();
        $json->contao->contents->subscribeValidate->text = $this->getSgContentArticleSubscribeValidateText();
        $json->contao->contents->subscribeValidate->moduleLoginGuests = $this->getSgContentArticleSubscribeValidateModuleLoginGuests();

        $json->contao->contents->unsubscribe = new \stdClass();
        $json->contao->contents->unsubscribe->headline = $this->getSgContentArticleUnsubscribeHeadline();
        $json->contao->contents->unsubscribe->text = $this->getSgContentArticleUnsubscribeText();
        $json->contao->contents->unsubscribe->hyperlink = $this->getSgContentArticleUnsubscribeHyperlink();

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

    public function getContaoModulesIds(): array
    {
        if (!$this->getSgInstallComplete()
        && (\in_array($this->getSgArchivedMode(), [self::ARCHIVE_MODE_EMPTY, self::ARCHIVE_MODE_DELETE], true))
        ) {
            return [];
        }
        $modules = [
            $this->getSgModuleLogin(),
            $this->getSgModuleLogout(),
            $this->getSgModuleData(),
            $this->getSgModulePassword(),
            $this->getSgModuleNav(),
        ];
        if ($this->getSgCanSubscribe()) {
            $modules[] = $this->getSgModuleSubscribe();
            $modules[] = $this->getSgModuleCloseAccount();
        }

        return $modules;
    }

    public function getContaoPagesIds(): array
    {
        if (!$this->getSgInstallComplete()
        && (\in_array($this->getSgArchivedMode(), [self::ARCHIVE_MODE_EMPTY, self::ARCHIVE_MODE_DELETE], true))
        ) {
            return [];
        }
        $pages = [
            $this->getSgPageExtranet(),
            $this->getSgPage401(),
            $this->getSgPage403(),
            $this->getSgPageContent(),
            $this->getSgPageData(),
            $this->getSgPageDataConfirm(),
            $this->getSgPagePassword(),
            $this->getSgPagePasswordConfirm(),
            $this->getSgPagePasswordValidate(),
            $this->getSgPageLogout(),
        ];
        if ($this->getSgCanSubscribe()) {
            $pages[] = $this->getSgPageSubscribe();
            $pages[] = $this->getSgPageSubscribeConfirm();
            $pages[] = $this->getSgPageSubscribeValidate();
            $pages[] = $this->getSgPageUnsubscribeConfirm();
        }

        return $pages;
    }

    public function getContaoContentsIds(): array
    {
        if (!$this->getSgInstallComplete()
        && (\in_array($this->getSgArchivedMode(), [self::ARCHIVE_MODE_EMPTY, self::ARCHIVE_MODE_DELETE], true))
        ) {
            return [];
        }

        $contents = [
            $this->getSgContentArticleExtranetHeadline(),
            $this->getSgContentArticleExtranetModuleLoginGuests(),
            $this->getSgContentArticleExtranetGridStartA(),
            $this->getSgContentArticleExtranetGridStartB(),
            $this->getSgContentArticleExtranetModuleLoginLogged(),
            $this->getSgContentArticleExtranetModuleNav(),
            $this->getSgContentArticleExtranetGridStopB(),
            $this->getSgContentArticleExtranetGridStopA(),
            $this->getSgContentArticle401Headline(),
            $this->getSgContentArticle401Text(),
            $this->getSgContentArticle401ModuleLoginGuests(),
            $this->getSgContentArticle403Headline(),
            $this->getSgContentArticle403Text(),
            $this->getSgContentArticle403Hyperlink(),
            $this->getSgContentArticleContentHeadline(),
            $this->getSgContentArticleContentText(),
            $this->getSgContentArticleDataHeadline(),
            $this->getSgContentArticleDataModuleData(),
            $this->getSgContentArticleDataHeadlineCloseAccount(),
            $this->getSgContentArticleDataTextCloseAccount(),
            $this->getSgContentArticleDataModuleCloseAccount(),
            $this->getSgContentArticleDataConfirmHeadline(),
            $this->getSgContentArticleDataConfirmText(),
            $this->getSgContentArticleDataConfirmHyperlink(),
            $this->getSgContentArticlePasswordHeadline(),
            $this->getSgContentArticlePasswordModulePassword(),
            $this->getSgContentArticlePasswordConfirmHeadline(),
            $this->getSgContentArticlePasswordConfirmText(),
            $this->getSgContentArticlePasswordValidateHeadline(),
            $this->getSgContentArticlePasswordValidateModulePassword(),
            $this->getSgContentArticleLogoutModuleLogout(),
            $this->getSgContentArticleSubscribeHeadline(),
            $this->getSgContentArticleSubscribeModuleSubscribe(),
            $this->getSgContentArticleSubscribeConfirmHeadline(),
            $this->getSgContentArticleSubscribeConfirmText(),
            $this->getSgContentArticleSubscribeValidateHeadline(),
            $this->getSgContentArticleSubscribeValidateText(),
            $this->getSgContentArticleSubscribeValidateModuleLoginGuests(),
        ];
        if ($this->getSgCanSubscribe()) {
            $contents[] = $this->getSgContentArticleUnsubscribeHeadline();
            $contents[] = $this->getSgContentArticleUnsubscribeText();
            $contents[] = $this->getSgContentArticleUnsubscribeHyperlink();
        }

        return $contents;
    }

    public function getContaoArticlesIds(): array
    {
        if (!$this->getSgInstallComplete()
        && (\in_array($this->getSgArchivedMode(), [self::ARCHIVE_MODE_EMPTY, self::ARCHIVE_MODE_DELETE], true))
        ) {
            return [];
        }
        $articles = [
            $this->getSgArticleExtranet(),
            $this->getSgArticle401(),
            $this->getSgArticle403(),
            $this->getSgArticleContent(),
            $this->getSgArticleData(),
            $this->getSgArticleDataConfirm(),
            $this->getSgArticlePassword(),
            $this->getSgArticlePasswordConfirm(),
            $this->getSgArticlePasswordValidate(),
            $this->getSgArticleLogout(),
        ];
        if ($this->getSgCanSubscribe()) {
            $articles[] = $this->getSgArticleSubscribe();
            $articles[] = $this->getSgArticleSubscribeConfirm();
            $articles[] = $this->getSgArticleSubscribeValidate();
            $articles[] = $this->getSgArticleUnsubscribeConfirm();
        }

        return $articles;
    }

    public function getContaoFoldersIds(): array
    {
        if (!$this->getSgInstallComplete()
        && (\in_array($this->getSgArchivedMode(), [self::ARCHIVE_MODE_EMPTY, self::ARCHIVE_MODE_DELETE], true))
        ) {
            return [];
        }

        return [$this->getSgExtranetFolder()];
    }

    public function getContaoUsersIds(): array
    {
        return [];
    }

    public function getContaoUserGroupsIds(): array
    {
        return [];
    }

    public function getContaoMembersIds(): array
    {
        if (!$this->getSgInstallComplete()
        && (\in_array($this->getSgArchivedMode(), [self::ARCHIVE_MODE_EMPTY, self::ARCHIVE_MODE_DELETE], true))
        ) {
            return [];
        }

        return [$this->getSgMemberExample()];
    }

    public function getContaoMemberGroupsIds(): array
    {
        if (!$this->getSgInstallComplete()
        && (\in_array($this->getSgArchivedMode(), [self::ARCHIVE_MODE_EMPTY, self::ARCHIVE_MODE_DELETE], true))
        ) {
            return [];
        }

        return [$this->getSgMemberGroupMembers()];
    }

    public function resetContaoModulesIds(): void
    {
        $this->setSgModuleData(null);
        $this->setSgModuleLogin(null);
        $this->setSgModuleLogout(null);
        $this->setSgModuleNav(null);
        $this->setSgModulePassword(null);
        $this->setSgModuleSubscribe(null);
        $this->setSgModuleCloseAccount(null);
    }

    public function resetContaoPagesIds(): void
    {
        $this->setSgPage401(null);
        $this->setSgPage403(null);
        $this->setSgPageContent(null);
        $this->setSgPageData(null);
        $this->setSgPageExtranet(null);
        $this->setSgPageLogout(null);
        $this->setSgPagePassword(null);
        $this->setSgPageSubscribe(null);
        $this->setSgPageDataConfirm(null);
        $this->setSgPageExtranetTitle(null);
        $this->setSgPagePasswordConfirm(null);
        $this->setSgPagePasswordValidate(null);
        $this->setSgPageSubscribeConfirm(null);
        $this->setSgPageSubscribeValidate(null);
        $this->setSgPageUnsubscribeConfirm(null);
    }

    public function resetContaoContentsIds(): void
    {
        $this
            ->setSgContentArticleExtranetHeadline(null)
            ->setSgContentArticleExtranetModuleLoginGuests(null)
            ->setSgContentArticleExtranetGridStartA(null)
            ->setSgContentArticleExtranetGridStartB(null)
            ->setSgContentArticleExtranetModuleLoginLogged(null)
            ->setSgContentArticleExtranetModuleNav(null)
            ->setSgContentArticleExtranetGridStopB(null)
            ->setSgContentArticleExtranetGridStopA(null)
            ->setSgContentArticle401Headline(null)
            ->setSgContentArticle401Text(null)
            ->setSgContentArticle401ModuleLoginGuests(null)
            ->setSgContentArticle403Headline(null)
            ->setSgContentArticle403Text(null)
            ->setSgContentArticle403Hyperlink(null)
            ->setSgContentArticleContentHeadline(null)
            ->setSgContentArticleContentText(null)
            ->setSgContentArticleDataHeadline(null)
            ->setSgContentArticleDataModuleData(null)
            ->setSgContentArticleDataHeadlineCloseAccount(null)
            ->setSgContentArticleDataTextCloseAccount(null)
            ->setSgContentArticleDataModuleCloseAccount(null)
            ->setSgContentArticleDataConfirmHeadline(null)
            ->setSgContentArticleDataConfirmText(null)
            ->setSgContentArticleDataConfirmHyperlink(null)
            ->setSgContentArticlePasswordHeadline(null)
            ->setSgContentArticlePasswordModulePassword(null)
            ->setSgContentArticlePasswordConfirmHeadline(null)
            ->setSgContentArticlePasswordConfirmText(null)
            ->setSgContentArticlePasswordValidateHeadline(null)
            ->setSgContentArticlePasswordValidateModulePassword(null)
            ->setSgContentArticleLogoutModuleLogout(null)
            ->setSgContentArticleSubscribeHeadline(null)
            ->setSgContentArticleSubscribeModuleSubscribe(null)
            ->setSgContentArticleSubscribeConfirmHeadline(null)
            ->setSgContentArticleSubscribeConfirmText(null)
            ->setSgContentArticleSubscribeValidateHeadline(null)
            ->setSgContentArticleSubscribeValidateText(null)
            ->setSgContentArticleSubscribeValidateModuleLoginGuests(null)
            ->setSgContentArticleUnsubscribeHeadline(null)
            ->setSgContentArticleUnsubscribeText(null)
            ->setSgContentArticleUnsubscribeHyperlink(null)
        ;
    }

    public function resetContaoArticlesIds(): void
    {
        $this
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
        ;
    }

    public function resetContaoFoldersIds(): void
    {
        $this->setSgExtranetFolder('');
    }

    public function resetContaoUsersIds(): void
    {
    }

    public function resetContaoUserGroupsIds(): void
    {
    }

    public function resetContaoMembersIds(): void
    {
        $this->setSgMemberExample(null);
    }

    public function resetContaoMemberGroupsIds(): void
    {
        $this->setSgMemberGroupMembers(null);
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
    public function getSgContentArticleExtranetHeadline(): ?int
    {
        return $this->sgContentArticleExtranetHeadline;
    }

    public function setSgContentArticleExtranetHeadline(?int $sgContentArticleExtranetHeadline): self
    {
        $this->sgContentArticleExtranetHeadline = $sgContentArticleExtranetHeadline;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticleExtranetModuleLoginGuests(): ?int
    {
        return $this->sgContentArticleExtranetModuleLoginGuests;
    }

    public function setSgContentArticleExtranetModuleLoginGuests(?int $sgContentArticleExtranetModuleLoginGuests): self
    {
        $this->sgContentArticleExtranetModuleLoginGuests = $sgContentArticleExtranetModuleLoginGuests;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticleExtranetGridStartA(): ?int
    {
        return $this->sgContentArticleExtranetGridStartA;
    }

    public function setSgContentArticleExtranetGridStartA(?int $sgContentArticleExtranetGridStartA): self
    {
        $this->sgContentArticleExtranetGridStartA = $sgContentArticleExtranetGridStartA;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticleExtranetGridStartB(): ?int
    {
        return $this->sgContentArticleExtranetGridStartB;
    }

    public function setSgContentArticleExtranetGridStartB(?int $sgContentArticleExtranetGridStartB): self
    {
        $this->sgContentArticleExtranetGridStartB = $sgContentArticleExtranetGridStartB;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticleExtranetModuleLoginLogged(): ?int
    {
        return $this->sgContentArticleExtranetModuleLoginLogged;
    }

    public function setSgContentArticleExtranetModuleLoginLogged(?int $sgContentArticleExtranetModuleLoginLogged): self
    {
        $this->sgContentArticleExtranetModuleLoginLogged = $sgContentArticleExtranetModuleLoginLogged;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticleExtranetModuleNav(): ?int
    {
        return $this->sgContentArticleExtranetModuleNav;
    }

    public function setSgContentArticleExtranetModuleNav(?int $sgContentArticleExtranetModuleNav): self
    {
        $this->sgContentArticleExtranetModuleNav = $sgContentArticleExtranetModuleNav;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticleExtranetGridStopB(): ?int
    {
        return $this->sgContentArticleExtranetGridStopB;
    }

    public function setSgContentArticleExtranetGridStopB(?int $sgContentArticleExtranetGridStopB): self
    {
        $this->sgContentArticleExtranetGridStopB = $sgContentArticleExtranetGridStopB;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticleExtranetGridStopA(): ?int
    {
        return $this->sgContentArticleExtranetGridStopA;
    }

    public function setSgContentArticleExtranetGridStopA(?int $sgContentArticleExtranetGridStopA): self
    {
        $this->sgContentArticleExtranetGridStopA = $sgContentArticleExtranetGridStopA;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticle401Headline(): ?int
    {
        return $this->sgContentArticle401Headline;
    }

    public function setSgContentArticle401Headline(?int $sgContentArticle401Headline): self
    {
        $this->sgContentArticle401Headline = $sgContentArticle401Headline;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticle401Text(): ?int
    {
        return $this->sgContentArticle401Text;
    }

    public function setSgContentArticle401Text(?int $sgContentArticle401Text): self
    {
        $this->sgContentArticle401Text = $sgContentArticle401Text;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticle401ModuleLoginGuests(): ?int
    {
        return $this->sgContentArticle401ModuleLoginGuests;
    }

    public function setSgContentArticle401ModuleLoginGuests(?int $sgContentArticle401ModuleLoginGuests): self
    {
        $this->sgContentArticle401ModuleLoginGuests = $sgContentArticle401ModuleLoginGuests;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticle403Headline(): ?int
    {
        return $this->sgContentArticle403Headline;
    }

    public function setSgContentArticle403Headline(?int $sgContentArticle403Headline): self
    {
        $this->sgContentArticle403Headline = $sgContentArticle403Headline;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticle403Text(): ?int
    {
        return $this->sgContentArticle403Text;
    }

    public function setSgContentArticle403Text(?int $sgContentArticle403Text): self
    {
        $this->sgContentArticle403Text = $sgContentArticle403Text;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticle403Hyperlink(): ?int
    {
        return $this->sgContentArticle403Hyperlink;
    }

    public function setSgContentArticle403Hyperlink(?int $sgContentArticle403Hyperlink): self
    {
        $this->sgContentArticle403Hyperlink = $sgContentArticle403Hyperlink;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticleContentHeadline(): ?int
    {
        return $this->sgContentArticleContentHeadline;
    }

    public function setSgContentArticleContentHeadline(?int $sgContentArticleContentHeadline): self
    {
        $this->sgContentArticleContentHeadline = $sgContentArticleContentHeadline;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticleContentText(): ?int
    {
        return $this->sgContentArticleContentText;
    }

    public function setSgContentArticleContentText(?int $sgContentArticleContentText): self
    {
        $this->sgContentArticleContentText = $sgContentArticleContentText;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticleDataHeadline(): ?int
    {
        return $this->sgContentArticleDataHeadline;
    }

    public function setSgContentArticleDataHeadline(?int $sgContentArticleDataHeadline): self
    {
        $this->sgContentArticleDataHeadline = $sgContentArticleDataHeadline;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticleDataModuleData(): ?int
    {
        return $this->sgContentArticleDataModuleData;
    }

    public function setSgContentArticleDataModuleData(?int $sgContentArticleDataModuleData): self
    {
        $this->sgContentArticleDataModuleData = $sgContentArticleDataModuleData;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticleDataHeadlineCloseAccount(): ?int
    {
        return $this->sgContentArticleDataHeadlineCloseAccount;
    }

    public function setSgContentArticleDataHeadlineCloseAccount(?int $sgContentArticleDataHeadlineCloseAccount): self
    {
        $this->sgContentArticleDataHeadlineCloseAccount = $sgContentArticleDataHeadlineCloseAccount;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticleDataTextCloseAccount(): ?int
    {
        return $this->sgContentArticleDataTextCloseAccount;
    }

    public function setSgContentArticleDataTextCloseAccount(?int $sgContentArticleDataTextCloseAccount): self
    {
        $this->sgContentArticleDataTextCloseAccount = $sgContentArticleDataTextCloseAccount;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticleDataModuleCloseAccount(): ?int
    {
        return $this->sgContentArticleDataModuleCloseAccount;
    }

    public function setSgContentArticleDataModuleCloseAccount(?int $sgContentArticleDataModuleCloseAccount): self
    {
        $this->sgContentArticleDataModuleCloseAccount = $sgContentArticleDataModuleCloseAccount;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticleDataConfirmHeadline(): ?int
    {
        return $this->sgContentArticleDataConfirmHeadline;
    }

    public function setSgContentArticleDataConfirmHeadline(?int $sgContentArticleDataConfirmHeadline): self
    {
        $this->sgContentArticleDataConfirmHeadline = $sgContentArticleDataConfirmHeadline;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticleDataConfirmText(): ?int
    {
        return $this->sgContentArticleDataConfirmText;
    }

    public function setSgContentArticleDataConfirmText(?int $sgContentArticleDataConfirmText): self
    {
        $this->sgContentArticleDataConfirmText = $sgContentArticleDataConfirmText;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticleDataConfirmHyperlink(): ?int
    {
        return $this->sgContentArticleDataConfirmHyperlink;
    }

    public function setSgContentArticleDataConfirmHyperlink(?int $sgContentArticleDataConfirmHyperlink): self
    {
        $this->sgContentArticleDataConfirmHyperlink = $sgContentArticleDataConfirmHyperlink;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticlePasswordHeadline(): ?int
    {
        return $this->sgContentArticlePasswordHeadline;
    }

    public function setSgContentArticlePasswordHeadline(?int $sgContentArticlePasswordHeadline): self
    {
        $this->sgContentArticlePasswordHeadline = $sgContentArticlePasswordHeadline;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticlePasswordModulePassword(): ?int
    {
        return $this->sgContentArticlePasswordModulePassword;
    }

    public function setSgContentArticlePasswordModulePassword(?int $sgContentArticlePasswordModulePassword): self
    {
        $this->sgContentArticlePasswordModulePassword = $sgContentArticlePasswordModulePassword;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticlePasswordConfirmHeadline(): ?int
    {
        return $this->sgContentArticlePasswordConfirmHeadline;
    }

    public function setSgContentArticlePasswordConfirmHeadline(?int $sgContentArticlePasswordConfirmHeadline): self
    {
        $this->sgContentArticlePasswordConfirmHeadline = $sgContentArticlePasswordConfirmHeadline;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticlePasswordConfirmText(): ?int
    {
        return $this->sgContentArticlePasswordConfirmText;
    }

    public function setSgContentArticlePasswordConfirmText(?int $sgContentArticlePasswordConfirmText): self
    {
        $this->sgContentArticlePasswordConfirmText = $sgContentArticlePasswordConfirmText;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticlePasswordValidateHeadline(): ?int
    {
        return $this->sgContentArticlePasswordValidateHeadline;
    }

    public function setSgContentArticlePasswordValidateHeadline(?int $sgContentArticlePasswordValidateHeadline): self
    {
        $this->sgContentArticlePasswordValidateHeadline = $sgContentArticlePasswordValidateHeadline;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticlePasswordValidateModulePassword(): ?int
    {
        return $this->sgContentArticlePasswordValidateModulePassword;
    }

    public function setSgContentArticlePasswordValidateModulePassword(?int $sgContentArticlePasswordValidateModulePassword): self
    {
        $this->sgContentArticlePasswordValidateModulePassword = $sgContentArticlePasswordValidateModulePassword;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticleLogoutModuleLogout(): ?int
    {
        return $this->sgContentArticleLogoutModuleLogout;
    }

    public function setSgContentArticleLogoutModuleLogout(?int $sgContentArticleLogoutModuleLogout): self
    {
        $this->sgContentArticleLogoutModuleLogout = $sgContentArticleLogoutModuleLogout;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticleSubscribeHeadline(): ?int
    {
        return $this->sgContentArticleSubscribeHeadline;
    }

    public function setSgContentArticleSubscribeHeadline(?int $sgContentArticleSubscribeHeadline): self
    {
        $this->sgContentArticleSubscribeHeadline = $sgContentArticleSubscribeHeadline;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticleSubscribeModuleSubscribe(): ?int
    {
        return $this->sgContentArticleSubscribeModuleSubscribe;
    }

    public function setSgContentArticleSubscribeModuleSubscribe(?int $sgContentArticleSubscribeModuleSubscribe): self
    {
        $this->sgContentArticleSubscribeModuleSubscribe = $sgContentArticleSubscribeModuleSubscribe;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticleSubscribeConfirmHeadline(): ?int
    {
        return $this->sgContentArticleSubscribeConfirmHeadline;
    }

    public function setSgContentArticleSubscribeConfirmHeadline(?int $sgContentArticleSubscribeConfirmHeadline): self
    {
        $this->sgContentArticleSubscribeConfirmHeadline = $sgContentArticleSubscribeConfirmHeadline;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticleSubscribeConfirmText(): ?int
    {
        return $this->sgContentArticleSubscribeConfirmText;
    }

    public function setSgContentArticleSubscribeConfirmText(?int $sgContentArticleSubscribeConfirmText): self
    {
        $this->sgContentArticleSubscribeConfirmText = $sgContentArticleSubscribeConfirmText;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticleSubscribeValidateHeadline(): ?int
    {
        return $this->sgContentArticleSubscribeValidateHeadline;
    }

    public function setSgContentArticleSubscribeValidateHeadline(?int $sgContentArticleSubscribeValidateHeadline): self
    {
        $this->sgContentArticleSubscribeValidateHeadline = $sgContentArticleSubscribeValidateHeadline;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticleSubscribeValidateText(): ?int
    {
        return $this->sgContentArticleSubscribeValidateText;
    }

    public function setSgContentArticleSubscribeValidateText(?int $sgContentArticleSubscribeValidateText): self
    {
        $this->sgContentArticleSubscribeValidateText = $sgContentArticleSubscribeValidateText;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticleSubscribeValidateModuleLoginGuests(): ?int
    {
        return $this->sgContentArticleSubscribeValidateModuleLoginGuests;
    }

    public function setSgContentArticleSubscribeValidateModuleLoginGuests(?int $sgContentArticleSubscribeValidateModuleLoginGuests): self
    {
        $this->sgContentArticleSubscribeValidateModuleLoginGuests = $sgContentArticleSubscribeValidateModuleLoginGuests;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticleUnsubscribeHeadline(): ?int
    {
        return $this->sgContentArticleUnsubscribeHeadline;
    }

    public function setSgContentArticleUnsubscribeHeadline(?int $sgContentArticleUnsubscribeHeadline): self
    {
        $this->sgContentArticleUnsubscribeHeadline = $sgContentArticleUnsubscribeHeadline;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticleUnsubscribeText(): ?int
    {
        return $this->sgContentArticleUnsubscribeText;
    }

    public function setSgContentArticleUnsubscribeText(?int $sgContentArticleUnsubscribeText): self
    {
        $this->sgContentArticleUnsubscribeText = $sgContentArticleUnsubscribeText;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getSgContentArticleUnsubscribeHyperlink(): ?int
    {
        return $this->sgContentArticleUnsubscribeHyperlink;
    }

    public function setSgContentArticleUnsubscribeHyperlink(?int $sgContentArticleUnsubscribeHyperlink): self
    {
        $this->sgContentArticleUnsubscribeHyperlink = $sgContentArticleUnsubscribeHyperlink;

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
