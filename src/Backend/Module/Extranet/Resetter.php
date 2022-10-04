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

namespace WEM\SmartgearBundle\Backend\Module\Extranet;

use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\FilesModel;
use Contao\MemberGroupModel;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\UserGroupModel;
use NotificationCenter\Model\Language as NotificationMessageLanguage;
use NotificationCenter\Model\Message as NotificationMessage;
use NotificationCenter\Model\Notification;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Backend\Resetter as BackendResetter;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\UserGroupModelUtil;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Config\Module\Extranet\Extranet as ExtranetConfig;
use WEM\SmartgearBundle\Model\Member as MemberModel;
use WEM\SmartgearBundle\Model\Module;

class Resetter extends BackendResetter
{
    /** @var string */
    protected $module = '';
    /** @var string */
    protected $type = '';
    /** @var ConfigurationManager */
    protected $configurationManager;
    /** @var TranslatorInterface */
    protected $translator;

    /**
     * Generic array of logs.
     *
     * @var array
     */
    protected $logs = [];

    public function __construct(
        ConfigurationManager $configurationManager,
        TranslatorInterface $translator,
        string $module,
        string $type
    ) {
        parent::__construct($configurationManager, $translator, $module, $type);
    }

    public function reset(string $mode): void
    {
        $this->resetUserGroupSettings();
        // reset everything except what we wanted to keep
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var ExtranetConfig */
        $extranetConfig = $config->getSgExtranet();
        $archiveTimestamp = time();

        switch ($mode) {
            case ExtranetConfig::ARCHIVE_MODE_ARCHIVE:
                $this->archiveModeArchive($extranetConfig, $archiveTimestamp);
            break;
            case ExtranetConfig::ARCHIVE_MODE_KEEP:
            break;
            case ExtranetConfig::ARCHIVE_MODE_DELETE:
                $this->archiveModeDelete($extranetConfig);
            break;
            default:
                throw new \InvalidArgumentException($this->translator->trans('WEMSG.EXTRANET.RESET.deleteModeUnknown', [], 'contao_default'));
            break;
        }

        $extranetConfig->setSgArchived(true)
            ->setSgArchivedMode($mode)
            ->setSgArchivedAt($archiveTimestamp)
        ;

        $config->setSgExtranet($extranetConfig);

        $this->configurationManager->save($config);
    }

    protected function archiveModeArchive(ExtranetConfig $extranetConfig, int $archiveTimestamp): ExtranetConfig
    {
        $objFolder = new \Contao\Folder($extranetConfig->getSgExtranetFolder());
        if ($objFolder) {
            $objFolder->renameTo(sprintf('files/archives/extranet-%s', (string) $archiveTimestamp));
        }
        $dateTime = (new \DateTime())->setTimestamp($archiveTimestamp);
        $date = $dateTime->format($GLOBALS['TL_CONFIG']['dateFormat']);
        $time = $dateTime->format($GLOBALS['TL_CONFIG']['timeFormat']);

        // archive pages, articles, modules, contents, notifications, members & memberGroups
        $member = MemberModel::findById($extranetConfig->getSgMemberExample());
        if (null !== $member) {
            $member->name = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$member->name, $date, $time], 'contao_default');
            $member->save();
        }
        $memberGroup = MemberGroupModel::findById($extranetConfig->getSgMemberGroupMembers());
        if (null !== $memberGroup) {
            $memberGroup->name = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$memberGroup->name, $date, $time], 'contao_default');
            $memberGroup->save();
        }
        $module = ModuleModel::findById($extranetConfig->getSgModuleData());
        if (null !== $module) {
            $module->name = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$module->name, $date, $time], 'contao_default');
            $module->save();
        }
        $module = ModuleModel::findById($extranetConfig->getSgModuleLogin());
        if (null !== $module) {
            $module->name = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$module->name, $date, $time], 'contao_default');
            $module->save();
        }
        $module = ModuleModel::findById($extranetConfig->getSgModuleLogout());
        if (null !== $module) {
            $module->name = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$module->name, $date, $time], 'contao_default');
            $module->save();
        }
        $module = ModuleModel::findById($extranetConfig->getSgModuleNav());
        if (null !== $module) {
            $module->name = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$module->name, $date, $time], 'contao_default');
            $module->save();
        }
        $module = ModuleModel::findById($extranetConfig->getSgModulePassword());
        if (null !== $module) {
            $module->name = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$module->name, $date, $time], 'contao_default');
            $module->save();
        }
        $module = ModuleModel::findById($extranetConfig->getSgModuleSubscribe());
        if (null !== $module) {
            $module->name = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$module->name, $date, $time], 'contao_default');
            $module->save();
        }
        $module = ModuleModel::findById($extranetConfig->getSgModuleCloseAccount());
        if (null !== $module) {
            $module->name = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$module->name, $date, $time], 'contao_default');
            $module->save();
        }

        foreach ($extranetConfig->getContaoArticlesIds() as $id) {
            $objArticle = ArticleModel::findByPk($id);
            if ($objArticle) {
                $objArticle->published = false;
                $objArticle->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$objArticle->name, $date, $time], 'contao_default');
                $objArticle->save();
            }
        }

        foreach ($extranetConfig->getContaoModulesIds() as $id) {
            $objModule = ModuleModel::findByPk($id);
            if ($objModule) {
                $objModule->published = false;
                $objModule->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$objModule->name, $date, $time], 'contao_default');
                $objModule->save();
            }
        }

        $page = PageModel::findById($extranetConfig->getSgPageExtranet());
        if (null !== $page) {
            $page->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$page->title, $date, $time], 'contao_default');
            $page->published = 0;
            $page->save();
        }
        $page = PageModel::findById($extranetConfig->getSgPage401());
        if (null !== $page) {
            $page->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$page->title, $date, $time], 'contao_default');
            $page->published = 0;
            $page->save();
        }
        $page = PageModel::findById($extranetConfig->getSgPage403());
        if (null !== $page) {
            $page->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$page->title, $date, $time], 'contao_default');
            $page->published = 0;
            $page->save();
        }
        $page = PageModel::findById($extranetConfig->getSgPageContent());
        if (null !== $page) {
            $page->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$page->title, $date, $time], 'contao_default');
            $page->published = 0;
            $page->save();
        }
        $page = PageModel::findById($extranetConfig->getSgPageData());
        if (null !== $page) {
            $page->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$page->title, $date, $time], 'contao_default');
            $page->published = 0;
            $page->save();
        }
        $page = PageModel::findById($extranetConfig->getSgPageDataConfirm());
        if (null !== $page) {
            $page->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$page->title, $date, $time], 'contao_default');
            $page->published = 0;
            $page->save();
        }
        $page = PageModel::findById($extranetConfig->getSgPageLogout());
        if (null !== $page) {
            $page->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$page->title, $date, $time], 'contao_default');
            $page->published = 0;
            $page->save();
        }
        $page = PageModel::findById($extranetConfig->getSgPagePassword());
        if (null !== $page) {
            $page->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$page->title, $date, $time], 'contao_default');
            $page->published = 0;
            $page->save();
        }
        $page = PageModel::findById($extranetConfig->getSgPagePasswordConfirm());
        if (null !== $page) {
            $page->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$page->title, $date, $time], 'contao_default');
            $page->published = 0;
            $page->save();
        }
        $page = PageModel::findById($extranetConfig->getSgPagePasswordValidate());
        if (null !== $page) {
            $page->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$page->title, $date, $time], 'contao_default');
            $page->published = 0;
            $page->save();
        }
        $page = PageModel::findById($extranetConfig->getSgPageSubscribe());
        if (null !== $page) {
            $page->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$page->title, $date, $time], 'contao_default');
            $page->published = 0;
            $page->save();
        }
        $page = PageModel::findById($extranetConfig->getSgPageSubscribeConfirm());
        if (null !== $page) {
            $page->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$page->title, $date, $time], 'contao_default');
            $page->published = 0;
            $page->save();
        }
        $page = PageModel::findById($extranetConfig->getSgPageSubscribeValidate());
        if (null !== $page) {
            $page->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$page->title, $date, $time], 'contao_default');
            $page->published = 0;
            $page->save();
        }
        $page = PageModel::findById($extranetConfig->getSgPageUnsubscribeConfirm());
        if (null !== $page) {
            $page->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$page->title, $date, $time], 'contao_default');
            $page->published = 0;
            $page->save();
        }

        $notification = Notification::findById($extranetConfig->getSgNotificationChangeData());
        if (null !== $notification) {
            $notification->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$notification->title, $date, $time], 'contao_default');
            $notification->published = 0;
            $notification->save();
        }

        $notification = Notification::findById($extranetConfig->getSgNotificationPassword());
        if (null !== $notification) {
            $notification->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$notification->title, $date, $time], 'contao_default');
            $notification->published = 0;
            $notification->save();
        }

        $notification = Notification::findById($extranetConfig->getSgNotificationSubscription());
        if (null !== $notification) {
            $notification->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$notification->title, $date, $time], 'contao_default');
            $notification->published = 0;
            $notification->save();
        }

        return $extranetConfig;
    }

    protected function archiveModeDelete(ExtranetConfig $extranetConfig): ExtranetConfig
    {
        $objFolder = new \Contao\Folder($extranetConfig->getSgExtranetFolder());
        if ($objFolder) {
            $objFolder->delete();
        }
        // delete pages (articles & contents will be deleted automatically), modules, notifications (message & languages will be deleted automatically), members & memberGroups
        $member = MemberModel::findById($extranetConfig->getSgMemberExample());
        if (null !== $member) {
            $member->delete();
            $extranetConfig->setSgMemberExample(null);
        }
        $memberGroup = MemberGroupModel::findById($extranetConfig->getSgMemberGroupMembers());
        if (null !== $memberGroup) {
            $memberGroup->delete();
            $extranetConfig->setSgMemberGroupMembers(null);
        }
        $module = ModuleModel::findById($extranetConfig->getSgModuleData());
        if (null !== $module) {
            $module->delete();
            $extranetConfig->setSgModuleData(null);
        }
        $module = ModuleModel::findById($extranetConfig->getSgModuleLogin());
        if (null !== $module) {
            $module->delete();
            $extranetConfig->setSgModuleLogin(null);
        }
        $module = ModuleModel::findById($extranetConfig->getSgModuleLogout());
        if (null !== $module) {
            $module->delete();
            $extranetConfig->setSgModuleLogout(null);
        }
        $module = ModuleModel::findById($extranetConfig->getSgModuleNav());
        if (null !== $module) {
            $module->delete();
            $extranetConfig->setSgModuleNav(null);
        }
        $module = ModuleModel::findById($extranetConfig->getSgModulePassword());
        if (null !== $module) {
            $module->delete();
            $extranetConfig->setSgModulePassword(null);
        }
        $module = ModuleModel::findById($extranetConfig->getSgModuleSubscribe());
        if (null !== $module) {
            $module->delete();
            $extranetConfig->setSgModuleSubscribe(null);
        }
        $module = ModuleModel::findById($extranetConfig->getSgModuleCloseAccount());
        if (null !== $module) {
            $module->delete();
            $extranetConfig->setSgModuleCloseAccount(null);
        }

        foreach ($extranetConfig->getContaoArticlesIds() as $id) {
            $objArticle = ArticleModel::findByPk($id);
            if ($objArticle) {
                $objArticle->delete();
            }
        }

        foreach ($extranetConfig->getContaoContentsIds() as $id) {
            $objContent = ContentModel::findByPk($id);
            if ($objContent) {
                $objContent->delete();
            }
        }

        $page = PageModel::findById($extranetConfig->getSgPageExtranet());
        if (null !== $page) {
            $page->delete();
            $extranetConfig
                ->setSgPageExtranet(null)
                ->setSgArticleExtranet(null)
                ->setSgContentArticleExtranetHeadline(null)
                ->setSgContentArticleExtranetGridStartA(null)
                ->setSgContentArticleExtranetGridStartB(null)
                ->setSgContentArticleExtranetText(null)
                ->setSgContentArticleExtranetModuleNav(null)
                ->setSgContentArticleExtranetModuleLoginGuests(null)
                ->setSgContentArticleExtranetModuleLoginLogged(null)
                ->setSgContentArticleExtranetGridStopA(null)
                ->setSgContentArticleExtranetGridStopB(null)
            ;
        }
        $page = PageModel::findById($extranetConfig->getSgPage401());
        if (null !== $page) {
            $page->delete();
            $extranetConfig
                ->setSgPage401(null)
                ->setSgArticle401(null)
                ->setSgContentArticle401Headline(null)
                ->setSgContentArticle401Text(null)
                ->setSgContentArticle401ModuleLoginGuests(null)
            ;
        }
        $page = PageModel::findById($extranetConfig->getSgPage403());
        if (null !== $page) {
            $page->delete();
            $extranetConfig
                ->setSgPage403(null)
                ->setSgArticle403(null)
                ->setSgContentArticle403Headline(null)
                ->setSgContentArticle403Text(null)
                ->setSgContentArticle403Hyperlink(null)
            ;
        }
        $page = PageModel::findById($extranetConfig->getSgPageContent());
        if (null !== $page) {
            $page->delete();
            $extranetConfig
                ->setSgPageContent(null)
                ->setSgArticleContent(null)
                ->setSgContentArticleContentHeadline(null)
                ->setSgContentArticleContentText(null)
            ;
        }
        $page = PageModel::findById($extranetConfig->getSgPageData());
        if (null !== $page) {
            $page->delete();
            $extranetConfig
                ->setSgPageData(null)
                ->setSgArticleData(null)
                ->setSgContentArticleDataHeadline(null)
                ->setSgContentArticleDataModuleData(null)
                ->setSgContentArticleDataHeadlineCloseAccount(null)
                ->setSgContentArticleDataTextCloseAccount(null)
                ->setSgContentArticleDataModuleCloseAccount(null)
            ;
        }
        $page = PageModel::findById($extranetConfig->getSgPageDataConfirm());
        if (null !== $page) {
            $page->delete();
            $extranetConfig
                ->setSgPageDataConfirm(null)
                ->setSgArticleDataConfirm(null)
                ->setSgContentArticleDataConfirmHeadline(null)
                ->setSgContentArticleDataConfirmText(null)
                ->setSgContentArticleDataConfirmHyperlink(null)
            ;
        }
        $page = PageModel::findById($extranetConfig->getSgPageLogout());
        if (null !== $page) {
            $page->delete();
            $extranetConfig
                ->setSgPageLogout(null)
                ->setSgArticleLogout(null)
                ->setSgContentArticleLogoutModuleLogout(null)
            ;
        }
        $page = PageModel::findById($extranetConfig->getSgPagePassword());
        if (null !== $page) {
            $page->delete();
            $extranetConfig
                ->setSgPagePassword(null)
                ->setSgArticlePassword(null)
                ->setSgContentArticlePasswordHeadline(null)
                ->setSgContentArticlePasswordModulePassword(null)
            ;
        }
        $page = PageModel::findById($extranetConfig->getSgPagePasswordConfirm());
        if (null !== $page) {
            $page->delete();
            $extranetConfig
                ->setSgPagePasswordConfirm(null)
                ->setSgArticlePasswordConfirm(null)
                ->setSgContentArticlePasswordConfirmHeadline(null)
                ->setSgContentArticlePasswordConfirmText(null)
            ;
        }
        $page = PageModel::findById($extranetConfig->getSgPagePasswordValidate());
        if (null !== $page) {
            $page->delete();
            $extranetConfig
                ->setSgPagePasswordValidate(null)
                ->setSgArticlePasswordValidate(null)
                ->setSgContentArticlePasswordValidateHeadline(null)
                ->setSgContentArticlePasswordValidateModulePassword(null)
            ;
        }
        $page = PageModel::findById($extranetConfig->getSgPageSubscribe());
        if (null !== $page) {
            $page->delete();
            $extranetConfig
                ->setSgPageSubscribe(null)
                ->setSgArticleSubscribe(null)
                ->setSgContentArticleSubscribeHeadline(null)
                ->setSgContentArticleSubscribeModuleSubscribe(null)
            ;
        }
        $page = PageModel::findById($extranetConfig->getSgPageSubscribeConfirm());
        if (null !== $page) {
            $page->delete();
            $extranetConfig
                ->setSgPageSubscribeConfirm(null)
                ->setSgArticleSubscribeConfirm(null)
                ->setSgContentArticleSubscribeConfirmHeadline(null)
                ->setSgContentArticleSubscribeConfirmText(null)
            ;
        }
        $page = PageModel::findById($extranetConfig->getSgPageSubscribeValidate());
        if (null !== $page) {
            $page->delete();
            $extranetConfig
                ->setSgPageSubscribeValidate(null)
                ->setSgArticleSubscribeValidate(null)
                ->setSgContentArticleSubscribeValidateHeadline(null)
                ->setSgContentArticleSubscribeValidateText(null)
                ->setSgContentArticleSubscribeValidateModuleLoginGuests(null)
            ;
        }
        $page = PageModel::findById($extranetConfig->getSgPageUnsubscribeConfirm());
        if (null !== $page) {
            $page->delete();
            $extranetConfig
                ->setSgPageUnsubscribeConfirm(null)
                ->setSgArticleUnsubscribeConfirm(null)
                ->setSgContentArticleUnsubscribeHeadline(null)
                ->setSgContentArticleUnsubscribeText(null)
                ->setSgContentArticleUnsubscribeHyperlink(null)
            ;
        }

        $notificationML = NotificationMessageLanguage::findById($extranetConfig->getSgNotificationChangeDataMessageLanguage());
        if (null !== $notificationML) {
            $notificationML->delete();
        }
        $notificationM = NotificationMessage::findById($extranetConfig->getSgNotificationChangeDataMessage());
        if (null !== $notificationM) {
            $notificationM->delete();
        }
        $notification = Notification::findById($extranetConfig->getSgNotificationChangeData());
        if (null !== $notification) {
            $notification->delete();
        }
        $extranetConfig
            ->setSgNotificationChangeData(null)
            ->setSgNotificationChangeDataMessage(null)
            ->setSgNotificationChangeDataMessageLanguage(null)
        ;

        $notificationML = NotificationMessageLanguage::findById($extranetConfig->getSgNotificationPasswordMessageLanguage());
        if (null !== $notificationML) {
            $notificationML->delete();
        }
        $notificationM = NotificationMessage::findById($extranetConfig->getSgNotificationPasswordMessage());
        if (null !== $notificationM) {
            $notificationM->delete();
        }
        $notification = Notification::findById($extranetConfig->getSgNotificationPassword());
        if (null !== $notification) {
            $notification->delete();
        }
        $extranetConfig
            ->setSgNotificationPassword(null)
            ->setSgNotificationPasswordMessage(null)
            ->setSgNotificationPasswordMessageLanguage(null)
        ;

        $notificationML = NotificationMessageLanguage::findById($extranetConfig->getSgNotificationSubscriptionMessageLanguage());
        if (null !== $notificationML) {
            $notificationML->delete();
        }
        $notificationM = NotificationMessage::findById($extranetConfig->getSgNotificationSubscriptionMessage());
        if (null !== $notificationM) {
            $notificationM->delete();
        }
        $notification = Notification::findById($extranetConfig->getSgNotificationSubscription());
        if (null !== $notification) {
            $notification->delete();
        }
        $extranetConfig
            ->setSgNotificationSubscription(null)
            ->setSgNotificationSubscriptionMessage(null)
            ->setSgNotificationSubscriptionMessageLanguage(null)
        ;

        return $extranetConfig;
    }

    protected function resetUserGroupSettings(): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var ExtranetConfig */
        $extranetConfig = $config->getSgExtranet();

        $this->resetUserGroup(UserGroupModel::findOneById($config->getSgUserGroupRedactors()), $extranetConfig);
        $this->resetUserGroup(UserGroupModel::findOneById($config->getSgUserGroupAdministrators()), $extranetConfig);
    }

    protected function resetUserGroup(UserGroupModel $objUserGroup, ExtranetConfig $extranetConfig): void
    {
        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);
        $userGroupManipulator
            ->removeAllowedModules(['member', 'mgroup'])
            ->removeAllowedPagemounts($extranetConfig->getContaoPagesIds())
            ->removeAllowedModules(Module::getTypesByIds($extranetConfig->getContaoModulesIds()))
        ;

        $objFolder = FilesModel::findByPath($extranetConfig->getSgExtranetFolder());
        if ($objFolder) {
            $userGroupManipulator->removeAllowedFilemounts([$objFolder->uuid]);
        }

        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->save();
    }
}
