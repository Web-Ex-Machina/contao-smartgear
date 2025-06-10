<?php

declare(strict_types=1);

/*
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2025 Web ex Machina
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
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Backend\Resetter as BackendResetter;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\UserGroupModelUtil;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Config\Module\Extranet\Extranet as ExtranetConfig;
use WEM\SmartgearBundle\Model\Member as MemberModel;
use WEM\SmartgearBundle\Model\Module;
use WEM\SmartgearBundle\Model\NotificationCenter\Language as NotificationMessageLanguage;
use WEM\SmartgearBundle\Model\NotificationCenter\Message as NotificationMessage;
use WEM\SmartgearBundle\Model\NotificationCenter\Notification;

class Resetter extends BackendResetter
{
    protected string $module = '';

    protected string $type = '';

    protected ConfigurationManager $configurationManager;

    protected TranslatorInterface $translator;

    /**
     * Generic array of logs.
     */
    protected array $logs = [];

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
        // reset everything except what we wanted to keep
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $extranetConfig = $config->getSgExtranet();
        if (! $extranetConfig) {
            return;
        }

        $this->resetUserGroupSettings();
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
        if ($member !== null) {
            $member->name = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$member->name, $date, $time], 'contao_default');
            $member->save();
        }

        $memberGroup = MemberGroupModel::findById($extranetConfig->getSgMemberGroupMembers());
        if ($memberGroup !== null) {
            $memberGroup->name = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$memberGroup->name, $date, $time], 'contao_default');
            $memberGroup->save();
        }

        $module = ModuleModel::findById($extranetConfig->getSgModuleData());
        if ($module !== null) {
            $module->name = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$module->name, $date, $time], 'contao_default');
            $module->save();
        }

        $module = ModuleModel::findById($extranetConfig->getSgModuleLogin());
        if ($module !== null) {
            $module->name = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$module->name, $date, $time], 'contao_default');
            $module->save();
        }

        $module = ModuleModel::findById($extranetConfig->getSgModuleLogout());
        if ($module !== null) {
            $module->name = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$module->name, $date, $time], 'contao_default');
            $module->save();
        }

        $module = ModuleModel::findById($extranetConfig->getSgModuleNav());
        if ($module !== null) {
            $module->name = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$module->name, $date, $time], 'contao_default');
            $module->save();
        }

        $module = ModuleModel::findById($extranetConfig->getSgModulePassword());
        if ($module !== null) {
            $module->name = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$module->name, $date, $time], 'contao_default');
            $module->save();
        }

        $module = ModuleModel::findById($extranetConfig->getSgModuleSubscribe());
        if ($module !== null) {
            $module->name = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$module->name, $date, $time], 'contao_default');
            $module->save();
        }

        $module = ModuleModel::findById($extranetConfig->getSgModuleCloseAccount());
        if ($module !== null) {
            $module->name = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$module->name, $date, $time], 'contao_default');
            $module->save();
        }

        foreach ($extranetConfig->getContaoArticlesIds() as $id) {
            $objArticle = ArticleModel::findById($id);
            if ($objArticle) {
                $objArticle->published = false;
                $objArticle->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$objArticle->name, $date, $time], 'contao_default');
                $objArticle->save();
            }
        }

        foreach ($extranetConfig->getContaoModulesIds() as $id) {
            $objModule = ModuleModel::findById($id);
            if ($objModule) {
                $objModule->published = false;
                $objModule->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$objModule->name, $date, $time], 'contao_default');
                $objModule->save();
            }
        }

        $page = PageModel::findById($extranetConfig->getSgPageExtranet());
        if ($page !== null) {
            $page->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$page->title, $date, $time], 'contao_default');
            $page->published = 0;
            $page->save();
        }

        $page = PageModel::findById($extranetConfig->getSgPage401());
        if ($page !== null) {
            $page->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$page->title, $date, $time], 'contao_default');
            $page->published = 0;
            $page->save();
        }

        $page = PageModel::findById($extranetConfig->getSgPage403());
        if ($page !== null) {
            $page->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$page->title, $date, $time], 'contao_default');
            $page->published = 0;
            $page->save();
        }

        $page = PageModel::findById($extranetConfig->getSgPageContent());
        if ($page !== null) {
            $page->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$page->title, $date, $time], 'contao_default');
            $page->published = 0;
            $page->save();
        }

        $page = PageModel::findById($extranetConfig->getSgPageData());
        if ($page !== null) {
            $page->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$page->title, $date, $time], 'contao_default');
            $page->published = 0;
            $page->save();
        }

        $page = PageModel::findById($extranetConfig->getSgPageDataConfirm());
        if ($page !== null) {
            $page->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$page->title, $date, $time], 'contao_default');
            $page->published = 0;
            $page->save();
        }

        $page = PageModel::findById($extranetConfig->getSgPageLogout());
        if ($page !== null) {
            $page->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$page->title, $date, $time], 'contao_default');
            $page->published = 0;
            $page->save();
        }

        $page = PageModel::findById($extranetConfig->getSgPagePassword());
        if ($page !== null) {
            $page->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$page->title, $date, $time], 'contao_default');
            $page->published = 0;
            $page->save();
        }

        $page = PageModel::findById($extranetConfig->getSgPagePasswordConfirm());
        if ($page !== null) {
            $page->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$page->title, $date, $time], 'contao_default');
            $page->published = 0;
            $page->save();
        }

        $page = PageModel::findById($extranetConfig->getSgPagePasswordValidate());
        if ($page !== null) {
            $page->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$page->title, $date, $time], 'contao_default');
            $page->published = 0;
            $page->save();
        }

        $page = PageModel::findById($extranetConfig->getSgPageSubscribe());
        if ($page !== null) {
            $page->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$page->title, $date, $time], 'contao_default');
            $page->published = 0;
            $page->save();
        }

        $page = PageModel::findById($extranetConfig->getSgPageSubscribeConfirm());
        if ($page !== null) {
            $page->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$page->title, $date, $time], 'contao_default');
            $page->published = 0;
            $page->save();
        }

        $page = PageModel::findById($extranetConfig->getSgPageSubscribeValidate());
        if ($page !== null) {
            $page->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$page->title, $date, $time], 'contao_default');
            $page->published = 0;
            $page->save();
        }

        $page = PageModel::findById($extranetConfig->getSgPageUnsubscribeConfirm());
        if ($page !== null) {
            $page->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$page->title, $date, $time], 'contao_default');
            $page->published = 0;
            $page->save();
        }

        $notification = Notification::findById($extranetConfig->getSgNotificationChangeData());
        if ($notification !== null) {
            $notification->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$notification->title, $date, $time], 'contao_default');
            $notification->published = 0;
            $notification->save();
        }

        $notification = Notification::findById($extranetConfig->getSgNotificationPassword());
        if ($notification !== null) {
            $notification->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$notification->title, $date, $time], 'contao_default');
            $notification->published = 0;
            $notification->save();
        }

        $notification = Notification::findById($extranetConfig->getSgNotificationSubscription());
        if ($notification !== null) {
            $notification->title = $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementArchivedAt', [$notification->title, $date, $time], 'contao_default');
            $notification->published = 0;
            $notification->save();
        }

        return $extranetConfig;
    }

    /**
     * @throws \Exception
     */
    protected function archiveModeDelete(ExtranetConfig $extranetConfig): ExtranetConfig
    {
        $objFolder = new \Contao\Folder($extranetConfig->getSgExtranetFolder());
        if ($objFolder) {
            $objFolder->delete();
        }

        // delete pages (articles & contents will be deleted automatically), modules, notifications (message & languages will be deleted automatically), members & memberGroups
        $member = MemberModel::findById($extranetConfig->getSgMemberExample());
        if ($member !== null) {
            $member->delete();
            $extranetConfig->setSgMemberExample(null);
        }

        $memberGroup = MemberGroupModel::findById($extranetConfig->getSgMemberGroupMembers());
        if ($memberGroup !== null) {
            $memberGroup->delete();
            $extranetConfig->setSgMemberGroupMembers(null);
        }

        $module = ModuleModel::findById($extranetConfig->getSgModuleData());
        if ($module !== null) {
            $module->delete();
            $extranetConfig->setSgModuleData(null);
        }

        $module = ModuleModel::findById($extranetConfig->getSgModuleLogin());
        if ($module !== null) {
            $module->delete();
            $extranetConfig->setSgModuleLogin(null);
        }

        $module = ModuleModel::findById($extranetConfig->getSgModuleLogout());
        if ($module !== null) {
            $module->delete();
            $extranetConfig->setSgModuleLogout(null);
        }

        $module = ModuleModel::findById($extranetConfig->getSgModuleNav());
        if ($module !== null) {
            $module->delete();
            $extranetConfig->setSgModuleNav(null);
        }

        $module = ModuleModel::findById($extranetConfig->getSgModulePassword());
        if ($module !== null) {
            $module->delete();
            $extranetConfig->setSgModulePassword(null);
        }

        $module = ModuleModel::findById($extranetConfig->getSgModuleSubscribe());
        if ($module !== null) {
            $module->delete();
            $extranetConfig->setSgModuleSubscribe(null);
        }

        $module = ModuleModel::findById($extranetConfig->getSgModuleCloseAccount());
        if ($module !== null) {
            $module->delete();
            $extranetConfig->setSgModuleCloseAccount(null);
        }

        foreach ($extranetConfig->getContaoArticlesIds() as $id) {
            $objArticle = ArticleModel::findById($id);
            if ($objArticle) {
                $objArticle->delete();
            }
        }

        foreach ($extranetConfig->getContaoContentsIds() as $id) {
            $objContent = ContentModel::findById($id);
            if ($objContent) {
                $objContent->delete();
            }
        }

        $page = PageModel::findById($extranetConfig->getSgPageExtranet());
        if ($page !== null) {
            $page->delete();
            $extranetConfig
                ->setSgPageExtranet(null)
                ->setSgArticleExtranet(null)
                ->setSgContentArticleExtranetHeadline(null)
                ->setSgContentArticleExtranetGridStartA(null)
                ->setSgContentArticleExtranetGridStartB(null)
                ->setSgContentArticleExtranetModuleNav(null)
                ->setSgContentArticleExtranetModuleLoginGuests(null)
                ->setSgContentArticleExtranetModuleLoginLogged(null)
                ->setSgContentArticleExtranetGridStopA(null)
                ->setSgContentArticleExtranetGridStopB(null)
            ;
        }

        $page = PageModel::findById($extranetConfig->getSgPage401());
        if ($page !== null) {
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
        if ($page !== null) {
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
        if ($page !== null) {
            $page->delete();
            $extranetConfig
                ->setSgPageContent(null)
                ->setSgArticleContent(null)
                ->setSgContentArticleContentHeadline(null)
                ->setSgContentArticleContentText(null)
            ;
        }

        $page = PageModel::findById($extranetConfig->getSgPageData());
        if ($page !== null) {
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
        if ($page !== null) {
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
        if ($page !== null) {
            $page->delete();
            $extranetConfig
                ->setSgPageLogout(null)
                ->setSgArticleLogout(null)
                ->setSgContentArticleLogoutModuleLogout(null)
            ;
        }

        $page = PageModel::findById($extranetConfig->getSgPagePassword());
        if ($page !== null) {
            $page->delete();
            $extranetConfig
                ->setSgPagePassword(null)
                ->setSgArticlePassword(null)
                ->setSgContentArticlePasswordHeadline(null)
                ->setSgContentArticlePasswordModulePassword(null)
            ;
        }

        $page = PageModel::findById($extranetConfig->getSgPagePasswordConfirm());
        if ($page !== null) {
            $page->delete();
            $extranetConfig
                ->setSgPagePasswordConfirm(null)
                ->setSgArticlePasswordConfirm(null)
                ->setSgContentArticlePasswordConfirmHeadline(null)
                ->setSgContentArticlePasswordConfirmText(null)
            ;
        }

        $page = PageModel::findById($extranetConfig->getSgPagePasswordValidate());
        if ($page !== null) {
            $page->delete();
            $extranetConfig
                ->setSgPagePasswordValidate(null)
                ->setSgArticlePasswordValidate(null)
                ->setSgContentArticlePasswordValidateHeadline(null)
                ->setSgContentArticlePasswordValidateModulePassword(null)
            ;
        }

        $page = PageModel::findById($extranetConfig->getSgPageSubscribe());
        if ($page !== null) {
            $page->delete();
            $extranetConfig
                ->setSgPageSubscribe(null)
                ->setSgArticleSubscribe(null)
                ->setSgContentArticleSubscribeHeadline(null)
                ->setSgContentArticleSubscribeModuleSubscribe(null)
            ;
        }

        $page = PageModel::findById($extranetConfig->getSgPageSubscribeConfirm());
        if ($page !== null) {
            $page->delete();
            $extranetConfig
                ->setSgPageSubscribeConfirm(null)
                ->setSgArticleSubscribeConfirm(null)
                ->setSgContentArticleSubscribeConfirmHeadline(null)
                ->setSgContentArticleSubscribeConfirmText(null)
            ;
        }

        $page = PageModel::findById($extranetConfig->getSgPageSubscribeValidate());
        if ($page !== null) {
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
        if ($page !== null) {
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
        if ($notificationML !== null) {
            $notificationML->delete();
        }

        $notificationM = NotificationMessage::findById($extranetConfig->getSgNotificationChangeDataMessage());
        if ($notificationM !== null) {
            $notificationM->delete();
        }

        $notification = Notification::findById($extranetConfig->getSgNotificationChangeData());
        if ($notification !== null) {
            $notification->delete();
        }

        $extranetConfig
            ->setSgNotificationChangeData(null)
            ->setSgNotificationChangeDataMessage(null)
            ->setSgNotificationChangeDataMessageLanguage(null)
        ;

        $notificationML = NotificationMessageLanguage::findById($extranetConfig->getSgNotificationPasswordMessageLanguage());
        if ($notificationML !== null) {
            $notificationML->delete();
        }

        $notificationM = NotificationMessage::findById($extranetConfig->getSgNotificationPasswordMessage());
        if ($notificationM !== null) {
            $notificationM->delete();
        }

        $notification = Notification::findById($extranetConfig->getSgNotificationPassword());
        if ($notification !== null) {
            $notification->delete();
        }

        $extranetConfig
            ->setSgNotificationPassword(null)
            ->setSgNotificationPasswordMessage(null)
            ->setSgNotificationPasswordMessageLanguage(null)
        ;

        $notificationML = NotificationMessageLanguage::findById($extranetConfig->getSgNotificationSubscriptionMessageLanguage());
        if ($notificationML !== null) {
            $notificationML->delete();
        }

        $notificationM = NotificationMessage::findById($extranetConfig->getSgNotificationSubscriptionMessage());
        if ($notificationM !== null) {
            $notificationM->delete();
        }

        $notification = Notification::findById($extranetConfig->getSgNotificationSubscription());
        if ($notification !== null) {
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
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $extranetConfig = $config->getSgExtranet();

        $objGroupRedactors = UserGroupModel::findOneById($config->getSgUserGroupRedactors());
        if ($objGroupRedactors) {
            $this->resetUserGroup($objGroupRedactors, $extranetConfig);
        }

        $objGroupAdministrators = UserGroupModel::findOneById($config->getSgUserGroupAdministrators());
        if ($objGroupAdministrators) {
            $this->resetUserGroup($objGroupAdministrators, $extranetConfig);
        }
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
