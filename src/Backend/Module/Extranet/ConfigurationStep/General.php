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

namespace WEM\SmartgearBundle\Backend\Module\Extranet\ConfigurationStep;

use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\CoreBundle\String\HtmlDecoder;
use Contao\FilesModel;
use Contao\Input;
use Contao\MemberGroupModel;
use Contao\MemberModel;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\UserGroupModel;
use Exception;
use NotificationCenter\Model\Gateway as GatewayModel;
use NotificationCenter\Model\Language as NotificationLanguageModel;
use NotificationCenter\Model\Message as NotificationMessageModel;
use NotificationCenter\Model\Notification as NotificationModel;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\GridBundle\Classes\GridStartManipulator;
use WEM\SmartgearBundle\Classes\Backend\ConfigurationStep;
use WEM\SmartgearBundle\Classes\Command\Util as CommandUtil;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\UserGroupModelUtil;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Config\Module\Extranet\Extranet as ExtranetConfig;

class General extends ConfigurationStep
{
    /** @var TranslatorInterface */
    protected $translator;
    /** @var ConfigurationManager */
    protected $configurationManager;
    /** @var CommandUtil */
    protected $commandUtil;
    /** @var HtmlDecoder */
    protected $htmlDecoder;
    /** @var string */
    protected $language;

    public function __construct(
        string $module,
        string $type,
        TranslatorInterface $translator,
        ConfigurationManager $configurationManager,
        CommandUtil $commandUtil,
        HtmlDecoder $htmlDecoder
    ) {
        parent::__construct($module, $type);
        $this->translator = $translator;
        $this->configurationManager = $configurationManager;
        $this->commandUtil = $commandUtil;
        $this->htmlDecoder = $htmlDecoder;
        $this->language = \Contao\BackendUser::getInstance()->language;

        $this->title = $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.title', [], 'contao_default');
        /** @var ExtranetConfig */
        $config = $this->configurationManager->load()->getSgExtranet();

        $this->addTextField('groupTitle', $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.groupTitle', [], 'contao_default'), $config->getSgMemberGroupMembersTitle(), true);

        $this->addTextField('pageTitle', $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.pageTitle', [], 'contao_default'), $config->getSgPageExtranetTitle(), true);

        $this->addCheckboxField('canSubscribe', $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.canSubscribe', [], 'contao_default'), '1', $config->getSgCanSubscribe());
    }

    public function isStepValid(): bool
    {
        // check if the step is correct
        if (null === Input::post('groupTitle')) {
            throw new Exception($this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.groupTitleMissing', [], 'contao_default'));
        }
        if (null === Input::post('pageTitle')) {
            throw new Exception($this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.pageTitleMissing', [], 'contao_default'));
        }

        return true;
    }

    public function do(): void
    {
        // do what is meant to be done in this step
        $this->updateModuleConfiguration(Input::post('groupTitle'), Input::post('pageTitle'), (bool) Input::post('canSubscribe'));

        $this->createFolder();
        $memberGroups = $this->createMemberGroups(Input::post('groupTitle'));
        $members = $this->createMembers($memberGroups);

        $pages = $this->createPages(Input::post('pageTitle'), $memberGroups);
        $articles = $this->createArticles($pages);

        $notifications = $this->createNotifications();
        $notificationGatewayMessages = $this->createNotificationsMessages($notifications);
        $notificationGatewayMessagesLanguages = $this->createNotificationsMessagesLanguages($notificationGatewayMessages);

        $modules = $this->createModules($pages, $notifications, $memberGroups);
        $contents = $this->createContents($pages, $articles, $modules, $memberGroups);

        // clean subscription related content if users can't subscribe
        if (false === (bool) Input::post('canSubscribe')) {
            $this->cleanSubscriptionRelatedEntities();
        }

        $this->updateModuleConfigurationAfterGenerations($pages, $articles, $modules, $contents, $members, $memberGroups, $notifications, $notificationGatewayMessages, $notificationGatewayMessagesLanguages);
        $this->updateUserGroups($modules);

        $this->commandUtil->executeCmdPHP('cache:clear');
        $this->commandUtil->executeCmdPHP('contao:symlinks');
    }

    protected function cleanSubscriptionRelatedEntities(): void
    {
        // destruction, destruction, destruction !!!
    }

    protected function updateModuleConfiguration(string $groupTitle, string $pageTitle, bool $canSubscribe): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var ExtranetConfig */
        $extranetConfig = $config->getSgExtranet();

        $extranetConfig
            ->setSgMemberGroupMembersTitle($groupTitle)
            ->setSgPageExtranetTitle($pageTitle)
            ->setSgCanSubscribe($canSubscribe)
            ->setSgArchived(false)
            ->setSgArchivedMode(ExtranetConfig::ARCHIVE_MODE_EMPTY)
            ->setSgArchivedAt(0)
        ;
        $config->setSgExtranet($extranetConfig);

        $this->configurationManager->save($config);
    }

    protected function createFolder(): void
    {
        $objFolder = new \Contao\Folder(ExtranetConfig::DEFAULT_FOLDER_PATH);
        $objFolder->unprotect();
    }

    protected function createPageExtranet(PageModel $rootPage, CoreConfig $config, ExtranetConfig $extranetConfig, string $title): PageModel
    {
        $page = PageModel::findById($extranetConfig->getSgPageExtranet());

        return Util::createPage($extranetConfig->getSgPageExtranetTitle(), 0, array_merge([
            'pid' => $rootPage->id,
            'sorting' => ((int) $rootPage->sorting) + 128,
            'layout' => $rootPage->layout,
            'title' => $title,
            'type' => 'regular',
            'pageTitle' => $title,
            'robots' => 'index,follow',
            'description' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.pageExtranetDescription', [$extranetConfig->getSgPageExtranetTitle(), $config->getSgWebsiteTitle()], 'contao_default'),
            'published' => 1,
        ], null !== $page ? ['id' => $page->id] : []));
    }

    protected function createPageError401(PageModel $rootPage, CoreConfig $config, ExtranetConfig $extranetConfig): PageModel
    {
        $page = PageModel::findById($extranetConfig->getSgPage401());
        $error404Page = PageModel::findById($config->getSgPage404());

        return Util::createPage($this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.pageError401Title', [], 'contao_default'), 0, array_merge([
            'pid' => $rootPage->id,
            'sorting' => ((int) $error404Page->sorting) + 128,
            'layout' => $rootPage->layout,
            'type' => 'error_401',
            'robots' => 'noindex,nofollow',
            'published' => 1,
        ], null !== $page ? ['id' => $page->id] : []));
    }

    protected function createPageError403(PageModel $rootPage, CoreConfig $config, ExtranetConfig $extranetConfig, int $sorting401): PageModel
    {
        $page = PageModel::findById($extranetConfig->getSgPage403());
        $error404Page = PageModel::findById($config->getSgPage404());

        return Util::createPage($this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.pageError403Title', [], 'contao_default'), 0, array_merge([
            'pid' => $rootPage->id,
            'sorting' => $sorting401 + 128,
            'layout' => $rootPage->layout,
            'type' => 'error_403',
            'robots' => 'noindex,nofollow',
            'published' => 1,
        ], null !== $page ? ['id' => $page->id] : []));
    }

    protected function createPageContent(PageModel $rootPage, CoreConfig $config, ExtranetConfig $extranetConfig, array $groups): PageModel
    {
        $page = PageModel::findById($extranetConfig->getSgPageContent());

        return Util::createPage($this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.pageContentTitle', [], 'contao_default'), 0, array_merge([
            'pid' => $rootPage->id,
            'sorting' => 128,
            'layout' => $rootPage->layout,
            'type' => 'regular',
            'robots' => 'noindex,nofollow',
            'protected' => 1,
            'groups' => serialize([$groups['member']]),
            'noSearch' => 1,
            'published' => 1,
            'sitemap' => 'map_never',
        ], null !== $page ? ['id' => $page->id] : []));
    }

    protected function createPageData(PageModel $rootPage, CoreConfig $config, ExtranetConfig $extranetConfig, array $groups): PageModel
    {
        $page = PageModel::findById($extranetConfig->getSgPageData());

        return Util::createPage($this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.pageDataTitle', [], 'contao_default'), 0, array_merge([
            'pid' => $rootPage->id,
            'sorting' => 256,
            'layout' => $rootPage->layout,
            'type' => 'regular',
            'robots' => 'noindex,nofollow',
            'protected' => 1,
            'groups' => serialize([$groups['member']]),
            'noSearch' => 1,
            'published' => 1,
            'sitemap' => 'map_never',
        ], null !== $page ? ['id' => $page->id] : []));
    }

    protected function createPageDataConfirm(PageModel $rootPage, CoreConfig $config, ExtranetConfig $extranetConfig, array $groups): PageModel
    {
        $page = PageModel::findById($extranetConfig->getSgPageDataConfirm());

        return Util::createPage($this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.pageDataConfirmTitle', [], 'contao_default'), 0, array_merge([
            'pid' => $rootPage->id,
            'sorting' => 256,
            'layout' => $rootPage->layout,
            'type' => 'regular',
            'robots' => 'noindex,nofollow',
            'protected' => 1,
            'groups' => serialize([$groups['member']]),
            'noSearch' => 1,
            'published' => 1,
            'sitemap' => 'map_never',
        ], null !== $page ? ['id' => $page->id] : []));
    }

    protected function createPagePassword(PageModel $rootPage, CoreConfig $config, ExtranetConfig $extranetConfig): PageModel
    {
        $page = PageModel::findById($extranetConfig->getSgPagePassword());

        return Util::createPage($this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.pagePasswordTitle', [], 'contao_default'), 0, array_merge([
            'pid' => $rootPage->id,
            'sorting' => 384,
            'layout' => $rootPage->layout,
            'type' => 'regular',
            'robots' => 'noindex,nofollow',
            'noSearch' => 1,
            'published' => 1,
            'hide' => 1,
            'sitemap' => 'map_never',
        ], null !== $page ? ['id' => $page->id] : []));
    }

    protected function createPagePasswordConfirm(PageModel $rootPage, CoreConfig $config, ExtranetConfig $extranetConfig): PageModel
    {
        $page = PageModel::findById($extranetConfig->getSgPagePasswordConfirm());

        return Util::createPage($this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.pagePasswordConfirmTitle', [], 'contao_default'), 0, array_merge([
            'pid' => $rootPage->id,
            'sorting' => 128,
            'layout' => $rootPage->layout,
            'type' => 'regular',
            'robots' => 'noindex,nofollow',
            'noSearch' => 1,
            'published' => 1,
            'hide' => 1,
            'sitemap' => 'map_never',
        ], null !== $page ? ['id' => $page->id] : []));
    }

    protected function createPagePasswordValidate(PageModel $rootPage, CoreConfig $config, ExtranetConfig $extranetConfig): PageModel
    {
        $page = PageModel::findById($extranetConfig->getSgPagePasswordValidate());

        return Util::createPage($this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.pagePasswordValidateTitle', [], 'contao_default'), 0, array_merge([
            'pid' => $rootPage->id,
            'sorting' => 256,
            'layout' => $rootPage->layout,
            'type' => 'regular',
            'robots' => 'noindex,nofollow',
            'noSearch' => 1,
            'published' => 1,
            'hide' => 1,
            'sitemap' => 'map_never',
        ], null !== $page ? ['id' => $page->id] : []));
    }

    protected function createPageLogout(PageModel $rootPage, CoreConfig $config, ExtranetConfig $extranetConfig): PageModel
    {
        $page = PageModel::findById($extranetConfig->getSgPageLogout());

        return Util::createPage($this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.pageLogoutTitle', [], 'contao_default'), 0, array_merge([
            'pid' => $rootPage->id,
            'sorting' => 512,
            'layout' => $rootPage->layout,
            'type' => 'regular',
            'robots' => 'noindex,nofollow',
            'noSearch' => 1,
            'published' => 1,
            'hide' => 1,
            'sitemap' => 'map_never',
        ], null !== $page ? ['id' => $page->id] : []));
    }

    protected function createPageSubscribe(PageModel $rootPage, CoreConfig $config, ExtranetConfig $extranetConfig): ?PageModel
    {
        $page = PageModel::findById($extranetConfig->getSgPageSubscribe());

        if (!$extranetConfig->getSgCanSubscribe()) {
            if (null !== $page) {
                $page->delete();
            }

            return null;
        }

        return Util::createPage($this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.pageSubscribeTitle', [], 'contao_default'), 0, array_merge([
            'pid' => $rootPage->id,
            'sorting' => 640,
            'layout' => $rootPage->layout,
            'type' => 'regular',
            'robots' => 'noindex,nofollow',
            'noSearch' => 1,
            'published' => 1,
            'hide' => 1,
            'guests' => 1,
        ], null !== $page ? ['id' => $page->id] : []));
    }

    protected function createPageSubscribeConfirm(?PageModel $rootPage, CoreConfig $config, ExtranetConfig $extranetConfig): ?PageModel
    {
        $page = PageModel::findById($extranetConfig->getSgPageSubscribeConfirm());

        if (!$extranetConfig->getSgCanSubscribe()) {
            if (null !== $page) {
                $page->delete();
            }

            return null;
        }

        return Util::createPage($this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.pageSubscribeConfirmTitle', [], 'contao_default'), 0, array_merge([
            'pid' => $rootPage->id,
            'sorting' => 128,
            'layout' => $rootPage->layout,
            'type' => 'regular',
            'robots' => 'noindex,nofollow',
            'noSearch' => 1,
            'published' => 1,
            'hide' => 1,
            'guests' => 1,
            'sitemap' => 'map_never',
        ], null !== $page ? ['id' => $page->id] : []));
    }

    protected function createPageSubscribeValidate(?PageModel $rootPage, CoreConfig $config, ExtranetConfig $extranetConfig): ?PageModel
    {
        $page = PageModel::findById($extranetConfig->getSgPageSubscribeValidate());

        if (!$extranetConfig->getSgCanSubscribe()) {
            if (null !== $page) {
                $page->delete();
            }

            return null;
        }

        return Util::createPage($this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.pageSubscribeValidateTitle', [], 'contao_default'), 0, array_merge([
            'pid' => $rootPage->id,
            'sorting' => 256,
            'layout' => $rootPage->layout,
            'type' => 'regular',
            'robots' => 'noindex,nofollow',
            'noSearch' => 1,
            'published' => 1,
            'hide' => 1,
            'guests' => 1,
            'sitemap' => 'map_never',
        ], null !== $page ? ['id' => $page->id] : []));
    }

    protected function createPageUnsubscribeConfirm(?PageModel $rootPage, CoreConfig $config, ExtranetConfig $extranetConfig): ?PageModel
    {
        $page = PageModel::findById($extranetConfig->getSgPageUnsubscribeConfirm());

        if (!$extranetConfig->getSgCanSubscribe()) {
            if (null !== $page) {
                $page->delete();
            }

            return null;
        }

        return Util::createPage($this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.pageUnsubscribeTitle', [], 'contao_default'), 0, array_merge([
            'pid' => $rootPage->id,
            'sorting' => 768,
            'layout' => $rootPage->layout,
            'type' => 'regular',
            'robots' => 'noindex,nofollow',
            'noSearch' => 1,
            'published' => 1,
            'hide' => 1,
        ], null !== $page ? ['id' => $page->id] : []));
    }

    protected function createPages(string $pageTitle, array $groups): array
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var ExtranetConfig */
        $extranetConfig = $config->getSgExtranet();
        $rootPage = PageModel::findById($config->getSgPageRoot());

        $pages = [];
        $pages['extranet'] = $this->createPageExtranet($rootPage, $config, $extranetConfig, $pageTitle);
        $pages['error401'] = $this->createPageError401($rootPage, $config, $extranetConfig);
        $pages['error403'] = $this->createPageError403($rootPage, $config, $extranetConfig, (int) $pages['error401']->sorting);
        $pages['content'] = $this->createPageContent($pages['extranet'], $config, $extranetConfig, $groups);
        $pages['data'] = $this->createPageData($pages['extranet'], $config, $extranetConfig, $groups);
        $pages['dataConfirm'] = $this->createPageDataConfirm($pages['data'], $config, $extranetConfig, $groups);
        $pages['password'] = $this->createPagePassword($pages['extranet'], $config, $extranetConfig);
        $pages['passwordConfirm'] = $this->createPagePasswordConfirm($pages['password'], $config, $extranetConfig);
        $pages['passwordValidate'] = $this->createPagePasswordValidate($pages['password'], $config, $extranetConfig);
        $pages['logout'] = $this->createPageLogout($pages['extranet'], $config, $extranetConfig);
        $pages['subscribe'] = $this->createPageSubscribe($pages['extranet'], $config, $extranetConfig);
        $pages['subscribeConfirm'] = $this->createPageSubscribeConfirm($pages['subscribe'], $config, $extranetConfig);
        $pages['subscribeValidate'] = $this->createPageSubscribeValidate($pages['subscribe'], $config, $extranetConfig);
        $pages['unsubscribeConfirm'] = $this->createPageUnsubscribeConfirm($pages['subscribe'], $config, $extranetConfig);

        return $pages;
    }

    protected function createArticleExtranet(PageModel $page, ExtranetConfig $extranetConfig): ArticleModel
    {
        $article = ArticleModel::findById($extranetConfig->getSgArticleExtranet());

        return Util::createArticle($page, array_merge([
            'title' => $extranetConfig->getSgPageExtranetTitle(),
        ], null !== $article ? ['id' => $article->id] : []));
    }

    protected function createArticleError401(PageModel $page, ExtranetConfig $extranetConfig): ArticleModel
    {
        $article = ArticleModel::findById($extranetConfig->getSgArticle401());

        return Util::createArticle($page, array_merge([
            'title' => $page->title,
        ], null !== $article ? ['id' => $article->id] : []));
    }

    protected function createArticleError403(PageModel $page, ExtranetConfig $extranetConfig): ArticleModel
    {
        $article = ArticleModel::findById($extranetConfig->getSgArticle403());

        return Util::createArticle($page, array_merge([
            'title' => $page->title,
        ], null !== $article ? ['id' => $article->id] : []));
    }

    protected function createArticleContent(PageModel $page, ExtranetConfig $extranetConfig): ArticleModel
    {
        $article = ArticleModel::findById($extranetConfig->getSgArticleContent());

        return Util::createArticle($page, array_merge([
            'title' => $page->title,
        ], null !== $article ? ['id' => $article->id] : []));
    }

    protected function createArticleData(PageModel $page, ExtranetConfig $extranetConfig): ArticleModel
    {
        $article = ArticleModel::findById($extranetConfig->getSgArticleData());

        return Util::createArticle($page, array_merge([
            'title' => $page->title,
        ], null !== $article ? ['id' => $article->id] : []));
    }

    protected function createArticleDataConfirm(PageModel $page, ExtranetConfig $extranetConfig): ArticleModel
    {
        $article = ArticleModel::findById($extranetConfig->getSgArticleDataConfirm());

        return Util::createArticle($page, array_merge([
            'title' => $page->title,
        ], null !== $article ? ['id' => $article->id] : []));
    }

    protected function createArticlePassword(PageModel $page, ExtranetConfig $extranetConfig): ArticleModel
    {
        $article = ArticleModel::findById($extranetConfig->getSgArticlePassword());

        return Util::createArticle($page, array_merge([
            'title' => $page->title,
        ], null !== $article ? ['id' => $article->id] : []));
    }

    protected function createArticlePasswordConfirm(PageModel $page, ExtranetConfig $extranetConfig): ArticleModel
    {
        $article = ArticleModel::findById($extranetConfig->getSgArticlePasswordConfirm());

        return Util::createArticle($page, array_merge([
            'title' => $page->title,
        ], null !== $article ? ['id' => $article->id] : []));
    }

    protected function createArticlePasswordValidate(PageModel $page, ExtranetConfig $extranetConfig): ArticleModel
    {
        $article = ArticleModel::findById($extranetConfig->getSgArticlePasswordValidate());

        return Util::createArticle($page, array_merge([
            'title' => $page->title,
        ], null !== $article ? ['id' => $article->id] : []));
    }

    protected function createArticleLogout(PageModel $page, ExtranetConfig $extranetConfig): ArticleModel
    {
        $article = ArticleModel::findById($extranetConfig->getSgArticleLogout());

        return Util::createArticle($page, array_merge([
            'title' => $page->title,
        ], null !== $article ? ['id' => $article->id] : []));
    }

    protected function createArticleSubscribe(?PageModel $page, ExtranetConfig $extranetConfig): ?ArticleModel
    {
        $article = ArticleModel::findById($extranetConfig->getSgArticleSubscribe());

        if (!$extranetConfig->getSgCanSubscribe()) {
            if (null !== $article) {
                $article->delete();
            }

            return null;
        }

        return Util::createArticle($page, array_merge([
            'title' => $page->title,
        ], null !== $article ? ['id' => $article->id] : []));
    }

    protected function createArticleSubscribeConfirm(?PageModel $page, ExtranetConfig $extranetConfig): ?ArticleModel
    {
        $article = ArticleModel::findById($extranetConfig->getSgArticleSubscribeConfirm());

        if (!$extranetConfig->getSgCanSubscribe()) {
            if (null !== $article) {
                $article->delete();
            }

            return null;
        }

        return Util::createArticle($page, array_merge([
            'title' => $page->title,
        ], null !== $article ? ['id' => $article->id] : []));
    }

    protected function createArticleSubscribeValidate(?PageModel $page, ExtranetConfig $extranetConfig): ?ArticleModel
    {
        $article = ArticleModel::findById($extranetConfig->getSgArticleSubscribeValidate());

        if (!$extranetConfig->getSgCanSubscribe()) {
            if (null !== $article) {
                $article->delete();
            }

            return null;
        }

        return Util::createArticle($page, array_merge([
            'title' => $page->title,
        ], null !== $article ? ['id' => $article->id] : []));
    }

    protected function createArticlUnsubscribeConfirm(?PageModel $page, ExtranetConfig $extranetConfig): ?ArticleModel
    {
        $article = ArticleModel::findById($extranetConfig->getSgArticleUnsubscribeConfirm());

        if (!$extranetConfig->getSgCanSubscribe()) {
            if (null !== $article) {
                $article->delete();
            }

            return null;
        }

        return Util::createArticle($page, array_merge([
            'title' => $page->title,
        ], null !== $article ? ['id' => $article->id] : []));
    }

    protected function createArticles(array $pages): array
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var ExtranetConfig */
        $extranetConfig = $config->getSgExtranet();

        return [
            'extranet' => $this->createArticleExtranet($pages['extranet'], $extranetConfig),
            'error401' => $this->createArticleError401($pages['error401'], $extranetConfig),
            'error403' => $this->createArticleError403($pages['error403'], $extranetConfig),
            'content' => $this->createArticleContent($pages['content'], $extranetConfig),
            'data' => $this->createArticleData($pages['data'], $extranetConfig),
            'dataConfirm' => $this->createArticleDataConfirm($pages['dataConfirm'], $extranetConfig),
            'password' => $this->createArticlePassword($pages['password'], $extranetConfig),
            'passwordConfirm' => $this->createArticlePasswordConfirm($pages['passwordConfirm'], $extranetConfig),
            'passwordValidate' => $this->createArticlePasswordValidate($pages['passwordValidate'], $extranetConfig),
            'logout' => $this->createArticleLogout($pages['logout'], $extranetConfig),
            'subscribe' => $this->createArticleSubscribe($pages['subscribe'], $extranetConfig),
            'subscribeConfirm' => $this->createArticleSubscribeConfirm($pages['subscribeConfirm'], $extranetConfig),
            'subscribeValidate' => $this->createArticleSubscribeValidate($pages['subscribeValidate'], $extranetConfig),
            'unsubscribeConfirm' => $this->createArticlUnsubscribeConfirm($pages['unsubscribeConfirm'], $extranetConfig),
        ];
    }

    protected function createModuleLogin(CoreConfig $config, ExtranetConfig $extranetConfig): ModuleModel
    {
        $module = new ModuleModel();

        if (null !== $extranetConfig->getSgModuleLogin()) {
            $moduleListOld = ModuleModel::findById($extranetConfig->getSgModuleLogin());
            if ($moduleListOld) {
                $moduleListOld->delete();
            }
            $module->id = $extranetConfig->getSgModuleLogin();
        }
        $module->name = $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.moduleLoginName', [], 'contao_default');
        $module->pid = $config->getSgTheme();
        $module->type = 'login';
        $module->autologin = 1;
        $module->redirectBack = 1;
        $module->tstamp = time();
        $module->save();

        return $module;
    }

    protected function createModuleLogout(CoreConfig $config, ExtranetConfig $extranetConfig, PageModel $page): ModuleModel
    {
        $module = new ModuleModel();

        if (null !== $extranetConfig->getSgModuleLogout()) {
            $moduleListOld = ModuleModel::findById($extranetConfig->getSgModuleLogout());
            if ($moduleListOld) {
                $moduleListOld->delete();
            }
            $module->id = $extranetConfig->getSgModuleLogout();
        }
        $module->name = $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.moduleLogoutName', [], 'contao_default');
        $module->pid = $config->getSgTheme();
        $module->type = 'logout';
        $module->jumpTo = $page->id;
        $module->tstamp = time();
        $module->save();

        return $module;
    }

    protected function createModuleData(CoreConfig $config, ExtranetConfig $extranetConfig, PageModel $page, NotificationModel $notification): ModuleModel
    {
        $module = new ModuleModel();

        if (null !== $extranetConfig->getSgModuleData()) {
            $moduleListOld = ModuleModel::findById($extranetConfig->getSgModuleData());
            if ($moduleListOld) {
                $moduleListOld->delete();
            }
            $module->id = $extranetConfig->getSgModuleData();
        }
        $module->name = $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.moduleDataName', [], 'contao_default');
        $module->pid = $config->getSgTheme();
        $module->type = 'personalData';
        $module->jumpTo = $page->id;
        $module->editable = serialize(['firstname', 'lastname', 'email', 'username', 'password']);
        $module->nc_notification = $notification->id;
        $module->tstamp = time();
        $module->save();

        return $module;
    }

    protected function createModulePassword(CoreConfig $config, ExtranetConfig $extranetConfig, PageModel $pageConfirm, PageModel $pageValidate, NotificationModel $notification): ModuleModel
    {
        $module = new ModuleModel();

        if (null !== $extranetConfig->getSgModulePassword()) {
            $moduleListOld = ModuleModel::findById($extranetConfig->getSgModulePassword());
            if ($moduleListOld) {
                $moduleListOld->delete();
            }
            $module->id = $extranetConfig->getSgModulePassword();
        }
        $module->name = $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.modulePasswordName', [], 'contao_default');
        $module->pid = $config->getSgTheme();
        $module->type = 'lostPasswordNotificationCenter';
        $module->reg_skipName = 1;
        $module->jumpTo = $pageConfirm->id;
        $module->reg_jumpTo = $pageValidate->id;
        $module->nc_notification = $notification->id;
        $module->tstamp = time();
        $module->save();

        return $module;
    }

    protected function createModuleNav(CoreConfig $config, ExtranetConfig $extranetConfig, PageModel $page): ModuleModel
    {
        $module = new ModuleModel();

        if (null !== $extranetConfig->getSgModuleNav()) {
            $moduleListOld = ModuleModel::findById($extranetConfig->getSgModuleNav());
            if ($moduleListOld) {
                $moduleListOld->delete();
            }
            $module->id = $extranetConfig->getSgModuleNav();
        }
        $module->name = $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.moduleNavName', [], 'contao_default');
        $module->pid = $config->getSgTheme();
        $module->type = 'navigation';
        $module->levelOffset = 1;
        $module->defineRoot = 1;
        $module->rootPage = $page->id;
        $module->tstamp = time();
        $module->save();

        return $module;
    }

    protected function createModuleSubscribe(CoreConfig $config, ExtranetConfig $extranetConfig, ?PageModel $pageConfirm, ?PageModel $pageValidate, ?NotificationModel $notification, MemberGroupModel $group): ?ModuleModel
    {
        $module = new ModuleModel();

        if (null !== $extranetConfig->getSgModuleSubscribe()) {
            $moduleListOld = ModuleModel::findById($extranetConfig->getSgModuleSubscribe());
            if ($moduleListOld) {
                $moduleListOld->delete();
            }
            $module->id = $extranetConfig->getSgModuleSubscribe();
        }

        if (!$extranetConfig->getSgCanSubscribe()) {
            return null;
        }

        $module->name = $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.moduleSubscribeName', [], 'contao_default');
        $module->pid = $config->getSgTheme();
        $module->type = 'registration';
        $module->jumpTo = $pageConfirm->id;
        $module->reg_jumpTo = $pageValidate->id;
        $module->nc_notification = $notification->id;
        $module->reg_allowLogin = 1;
        $module->editable = serialize(['firstname', 'lastname', 'email', 'username', 'password']);
        $module->reg_groups = serialize([$group->id]);
        $module->tstamp = time();
        $module->save();

        return $module;
    }

    protected function createModuleCloseAccount(CoreConfig $config, ExtranetConfig $extranetConfig, ?PageModel $page): ?ModuleModel
    {
        $module = new ModuleModel();

        if (null !== $extranetConfig->getSgModuleCloseAccount()) {
            $moduleListOld = ModuleModel::findById($extranetConfig->getSgModuleCloseAccount());
            if ($moduleListOld) {
                $moduleListOld->delete();
            }
            $module->id = $extranetConfig->getSgModuleCloseAccount();
        }

        if (!$extranetConfig->getSgCanSubscribe()) {
            return null;
        }

        $module->name = $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.moduleCloseAccountName', [], 'contao_default');
        $module->pid = $config->getSgTheme();
        $module->type = 'closeAccount';
        $module->jumpTo = $page->id;
        $module->reg_close = 'close_delete';
        $module->tstamp = time();
        $module->save();

        return $module;
    }

    protected function createModules(array $pages, array $notifications, array $groups): array
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var ExtranetConfig */
        $extranetConfig = $config->getSgExtranet();

        return [
            'login' => $this->createModuleLogin($config, $extranetConfig),
            'logout' => $this->createModuleLogout($config, $extranetConfig, $pages['extranet']),
            'data' => $this->createModuleData($config, $extranetConfig, $pages['dataConfirm'], $notifications['changeData']),
            'password' => $this->createModulePassword($config, $extranetConfig, $pages['passwordConfirm'], $pages['passwordValidate'], $notifications['password']),
            'nav' => $this->createModuleNav($config, $extranetConfig, $pages['extranet']),
            'subscribe' => $this->createModuleSubscribe($config, $extranetConfig, $pages['subscribeConfirm'], $pages['subscribeValidate'], $notifications['subscription'], $groups['members']),
            'closeAccount' => $this->createModuleCloseAccount($config, $extranetConfig, $pages['unsubscribeConfirm']),
        ];
    }

    protected function createContentsArticleExtranet(ExtranetConfig $extranetConfig, PageModel $page, ArticleModel $article, array $modules, MemberGroupModel $group): array
    {
        $headline = ContentModel::findById($extranetConfig->getSgContentArticleExtranetHeadline());
        $moduleLoginGuests = ContentModel::findById($extranetConfig->getSgContentArticleExtranetModuleLoginGuests());
        $gridStartA = ContentModel::findById($extranetConfig->getSgContentArticleExtranetGridStartA());
        $gridStartB = ContentModel::findById($extranetConfig->getSgContentArticleExtranetGridStartB());
        $moduleLoginLogged = ContentModel::findById($extranetConfig->getSgContentArticleExtranetModuleLoginLogged());
        $moduleNav = ContentModel::findById($extranetConfig->getSgContentArticleExtranetModuleNav());
        $gridStopB = ContentModel::findById($extranetConfig->getSgContentArticleExtranetGridStopB());
        $text = ContentModel::findById($extranetConfig->getSgContentArticleExtranetText());
        $gridStopA = ContentModel::findById($extranetConfig->getSgContentArticleExtranetGridStopA());

        $headline = Util::createContent($article, array_merge([
            'type' => 'headline',
            'headline' => serialize(['unit' => 'h1', 'value' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleExtranetHeadline', [], 'contao_default')]),
            'cssID' => ',sep-bottom',
            'sorting' => 128,
        ], null !== $headline ? ['id' => $headline->id] : []));

        $moduleLoginGuests = Util::createContent($article, array_merge([
            'type' => 'module',
            'module' => $modules['login']->id,
            'guests' => 1,
            'sorting' => 256,
        ], ['id' => null !== $moduleLoginGuests ? $moduleLoginGuests->id : null]));

        $gridStartA = Util::createContent($article, array_merge([
            'type' => 'grid-start',
            'protected' => 1,
            'groups' => serialize([$group->id]),
            'sorting' => 384,
        ], ['id' => null !== $gridStartA ? $gridStartA->id : null]));

        $gridStartB = Util::createContent($article, array_merge([
            'type' => 'grid-start',
            'protected' => 1,
            'groups' => serialize([$group->id]),
            'sorting' => 512,
        ], ['id' => null !== $gridStartB ? $gridStartB->id : null]));

        $moduleLoginLogged = Util::createContent($article, array_merge([
            'type' => 'module',
            'module' => $modules['login']->id,
            'protected' => 1,
            'groups' => serialize([$group->id]),
            'sorting' => 640,
        ], ['id' => null !== $moduleLoginLogged ? $moduleLoginLogged->id : null]));

        $moduleNav = Util::createContent($article, array_merge([
            'type' => 'module',
            'module' => $modules['nav']->id,
            'protected' => 1,
            'groups' => serialize([$group->id]),
            'sorting' => 768,
        ], ['id' => null !== $moduleNav ? $moduleNav->id : null]));

        $gridStopB = Util::createContent($article, array_merge([
            'type' => 'grid-stop',
            'sorting' => 896,
        ], ['id' => null !== $gridStopB ? $gridStopB->id : null]));

        $text = Util::createContent($article, array_merge([
            'type' => 'text',
            'text' => 'random',
            'sorting' => 1024,
        ], ['id' => null !== $text ? $text->id : null]));

        $gridStopA = Util::createContent($article, array_merge([
            'type' => 'grid-stop',
            'sorting' => 1152,
        ], ['id' => null !== $gridStopA ? $gridStopA->id : null]));

        /** @todo : update $gridStartA to apply some style to its fifth element (aka Leeloo) */
        $gsm = GridStartManipulator::create($gridStartA);
        $gsm
            ->recalculateElements()
            ->setGridColsAll(3)
            ->setGridColsSm(1)
            ->setGridItemsSettingsForItemAndPropertyAndResolution((int) $text->id, GridStartManipulator::PROPERTY_COLS, GridStartManipulator::RESOLUTION_ALL, 'cols-span-2')
        ;

        $gridStartA = $gsm->getGridStart();
        $gridStartA->save();
        // no need to recalculate GridStartB elements as it is a nested grid from GridStartA
        $gsm = GridStartManipulator::create($gridStartB);
        $gsm
            ->setGridColsAll(1)
        ;

        $gridStartB = $gsm->getGridStart();
        $gridStartB->save();

        return [
            'headline' => $headline,
            'moduleLoginGuests' => $moduleLoginGuests,
            'gridStartA' => $gridStartA,
            'gridStartB' => $gridStartB,
            'moduleLoginLogged' => $moduleLoginLogged,
            'moduleNav' => $moduleNav,
            'gridStopA' => $gridStopA,
            'text' => $text,
            'gridStopB' => $gridStopB,
        ];
    }

    protected function createContentsArticle401(ExtranetConfig $extranetConfig, PageModel $page, ArticleModel $article, array $modules): array
    {
        $headline = ContentModel::findById($extranetConfig->getSgContentArticle401Headline());
        $text = ContentModel::findById($extranetConfig->getSgContentArticle401Text());
        $moduleLoginGuests = ContentModel::findById($extranetConfig->getSgContentArticle401ModuleLoginGuests());

        $headline = Util::createContent($article, array_merge([
            'type' => 'headline',
            'headline' => serialize(['unit' => 'h1', 'value' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticle401Headline', [], 'contao_default')]),
            'cssID' => ',sep-bottom',
        ], null !== $headline ? ['id' => $headline->id] : []));

        $text = Util::createContent($article, array_merge([
            'type' => 'text',
            'text' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticle401Text', [], 'contao_default'),
            'cssID' => ',sep-bottom',
        ], null !== $text ? ['id' => $text->id] : []));

        $moduleLoginGuests = Util::createContent($article, array_merge([
            'type' => 'module',
            'module' => $modules['login']->id,
            'guests' => 1,
        ], ['id' => null !== $moduleLoginGuests ? $moduleLoginGuests->id : null]));

        return [
            'headline' => $headline,
            'text' => $text,
            'moduleLoginGuests' => $moduleLoginGuests,
        ];
    }

    protected function createContentsArticle403(ExtranetConfig $extranetConfig, PageModel $page, ArticleModel $article, PageModel $pageExtranet): array
    {
        $headline = ContentModel::findById($extranetConfig->getSgContentArticle403Headline());
        $text = ContentModel::findById($extranetConfig->getSgContentArticle403Text());
        $hyperlink = ContentModel::findById($extranetConfig->getSgContentArticle403Hyperlink());

        $headline = Util::createContent($article, array_merge([
            'type' => 'headline',
            'headline' => serialize(['unit' => 'h1', 'value' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticle403Headline', [], 'contao_default')]),
            'cssID' => ',sep-bottom',
        ], null !== $headline ? ['id' => $headline->id] : []));

        $text = Util::createContent($article, array_merge([
            'type' => 'text',
            'text' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticle403Text', [], 'contao_default'),
            'cssID' => ',sep-bottom',
        ], null !== $text ? ['id' => $text->id] : []));

        $hyperlink = Util::createContent($article, array_merge([
            'type' => 'hyperlink',
            'url' => sprintf('{{link_url::%s}}', $pageExtranet->id),
            'linkTitle' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticle403Hyperlink', [], 'contao_default'),
            'titleText' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticle403Hyperlink', [], 'contao_default'),
        ], ['id' => null !== $hyperlink ? $hyperlink->id : null]));

        return [
            'headline' => $headline,
            'text' => $text,
            'hyperlink' => $hyperlink,
        ];
    }

    protected function createContentsArticleContent(ExtranetConfig $extranetConfig, PageModel $page, ArticleModel $article): array
    {
        $headline = ContentModel::findById($extranetConfig->getSgContentArticleContentHeadline());
        $text = ContentModel::findById($extranetConfig->getSgContentArticleContentText());

        $headline = Util::createContent($article, array_merge([
            'type' => 'headline',
            'headline' => serialize(['unit' => 'h1', 'value' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleContentHeadline', [], 'contao_default')]),
            'cssID' => ',sep-bottom',
        ], null !== $headline ? ['id' => $headline->id] : []));

        $text = Util::createContent($article, array_merge([
            'type' => 'text',
            'text' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleContentText', [], 'contao_default'),
            'cssID' => ',sep-bottom',
        ], null !== $text ? ['id' => $text->id] : []));

        return [
            'headline' => $headline,
            'text' => $text,
        ];
    }

    protected function createContentsArticleData(ExtranetConfig $extranetConfig, PageModel $page, ArticleModel $article, array $modules): array
    {
        $headline = ContentModel::findById($extranetConfig->getSgContentArticleDataHeadline());
        $moduleData = ContentModel::findById($extranetConfig->getSgContentArticleDataModuleData());
        $headlineCloseAccount = ContentModel::findById($extranetConfig->getSgContentArticleDataHeadlineCloseAccount());
        $textCloseAccount = ContentModel::findById($extranetConfig->getSgContentArticleDataTextCloseAccount());
        $moduleCloseAccount = ContentModel::findById($extranetConfig->getSgContentArticleDataModuleCloseAccount());

        $headline = Util::createContent($article, array_merge([
            'type' => 'headline',
            'headline' => serialize(['unit' => 'h1', 'value' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleDataHeadline', [], 'contao_default')]),
            'cssID' => ',sep-bottom',
        ], null !== $headline ? ['id' => $headline->id] : []));

        $moduleData = Util::createContent($article, array_merge([
            'type' => 'module',
            'module' => $modules['data']->id,
        ], ['id' => null !== $moduleData ? $moduleData->id : null]));

        if ($extranetConfig->getSgCanSubscribe()) {
            $headlineCloseAccount = Util::createContent($article, array_merge([
                'type' => 'headline',
                'headline' => serialize(['unit' => 'h1', 'value' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleDataHeadlineCloseAccount', [], 'contao_default')]),
                'cssID' => ',sep-bottom',
            ], null !== $headlineCloseAccount ? ['id' => $headlineCloseAccount->id] : []));

            $textCloseAccount = Util::createContent($article, array_merge([
                'type' => 'text',
                'text' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleDataTextCloseAccount', [], 'contao_default'),
                'cssID' => ',sep-bottom',
            ], null !== $textCloseAccount ? ['id' => $textCloseAccount->id] : []));

            $moduleCloseAccount = Util::createContent($article, array_merge([
                'type' => 'module',
                'module' => $modules['closeAccount']->id,
            ], ['id' => null !== $moduleCloseAccount ? $moduleCloseAccount->id : null]));
        } else {
            if (null !== $headlineCloseAccount) {
                $headlineCloseAccount->delete();
                $headlineCloseAccount = null;
            }
            if (null !== $textCloseAccount) {
                $textCloseAccount->delete();
                $textCloseAccount = null;
            }
            if (null !== $moduleCloseAccount) {
                $moduleCloseAccount->delete();
                $moduleCloseAccount = null;
            }
        }

        return [
            'headline' => $headline,
            'moduleData' => $moduleData,
            'headlineCloseAccount' => $headlineCloseAccount,
            'textCloseAccount' => $textCloseAccount,
            'moduleCloseAccount' => $moduleCloseAccount,
        ];
    }

    protected function createContentsArticleDataConfirm(ExtranetConfig $extranetConfig, PageModel $page, ArticleModel $article, PageModel $pageExtranet): array
    {
        $headline = ContentModel::findById($extranetConfig->getSgContentArticleDataConfirmHeadline());
        $text = ContentModel::findById($extranetConfig->getSgContentArticleDataConfirmText());
        $hyperlink = ContentModel::findById($extranetConfig->getSgContentArticleDataConfirmHyperlink());

        $headline = Util::createContent($article, array_merge([
            'type' => 'headline',
            'headline' => serialize(['unit' => 'h1', 'value' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleDataConfirmHeadline', [], 'contao_default')]),
            'cssID' => ',sep-bottom',
        ], null !== $headline ? ['id' => $headline->id] : []));

        $text = Util::createContent($article, array_merge([
            'type' => 'text',
            'text' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleDataConfirmText', [], 'contao_default'),
            'cssID' => ',sep-bottom',
        ], null !== $text ? ['id' => $text->id] : []));

        $hyperlink = Util::createContent($article, array_merge([
            'type' => 'hyperlink',
            'url' => sprintf('{{link_url::%s}}', $pageExtranet->id),
            'linkTitle' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleDataConfirmHyperlink', [], 'contao_default'),
            'titleText' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleDataConfirmHyperlink', [], 'contao_default'),
        ], ['id' => null !== $hyperlink ? $hyperlink->id : null]));

        return [
            'headline' => $headline,
            'text' => $text,
            'hyperlink' => $hyperlink,
        ];
    }

    protected function createContentsArticlePassword(ExtranetConfig $extranetConfig, PageModel $page, ArticleModel $article, array $modules): array
    {
        $headline = ContentModel::findById($extranetConfig->getSgContentArticlePasswordHeadline());
        $modulePassword = ContentModel::findById($extranetConfig->getSgContentArticlePasswordModulePassword());

        $headline = Util::createContent($article, array_merge([
            'type' => 'headline',
            'headline' => serialize(['unit' => 'h1', 'value' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticlePasswordHeadline', [], 'contao_default')]),
            'cssID' => ',sep-bottom',
        ], null !== $headline ? ['id' => $headline->id] : []));

        $modulePassword = Util::createContent($article, array_merge([
            'type' => 'module',
            'module' => $modules['password']->id,
        ], ['id' => null !== $modulePassword ? $modulePassword->id : null]));

        return [
            'headline' => $headline,
            'modulePassword' => $modulePassword,
        ];
    }

    protected function createContentsArticlePasswordConfirm(ExtranetConfig $extranetConfig, PageModel $page, ArticleModel $article): array
    {
        $headline = ContentModel::findById($extranetConfig->getSgContentArticlePasswordConfirmHeadline());
        $text = ContentModel::findById($extranetConfig->getSgContentArticlePasswordConfirmText());

        $headline = Util::createContent($article, array_merge([
            'type' => 'headline',
            'headline' => serialize(['unit' => 'h1', 'value' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticlePasswordConfirmHeadline', [], 'contao_default')]),
            'cssID' => ',sep-bottom',
        ], null !== $headline ? ['id' => $headline->id] : []));

        $text = Util::createContent($article, array_merge([
            'type' => 'text',
            'text' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticlePasswordConfirmText', [], 'contao_default'),
            'cssID' => ',sep-bottom',
        ], null !== $text ? ['id' => $text->id] : []));

        return [
            'headline' => $headline,
            'text' => $text,
        ];
    }

    protected function createContentsArticlePasswordValidate(ExtranetConfig $extranetConfig, PageModel $page, ArticleModel $article, array $modules): array
    {
        $headline = ContentModel::findById($extranetConfig->getSgContentArticlePasswordValidateHeadline());
        $modulePassword = ContentModel::findById($extranetConfig->getSgContentArticlePasswordValidateModulePassword());

        $headline = Util::createContent($article, array_merge([
            'type' => 'headline',
            'headline' => serialize(['unit' => 'h1', 'value' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticlePasswordValidateHeadline', [], 'contao_default')]),
            'cssID' => ',sep-bottom',
        ], null !== $headline ? ['id' => $headline->id] : []));

        $modulePassword = Util::createContent($article, array_merge([
            'type' => 'module',
            'module' => $modules['password']->id,
        ], ['id' => null !== $modulePassword ? $modulePassword->id : null]));

        return [
            'headline' => $headline,
            'modulePassword' => $modulePassword,
        ];
    }

    protected function createContentsArticleLogout(ExtranetConfig $extranetConfig, PageModel $page, ArticleModel $article, array $modules): array
    {
        $moduleLogout = ContentModel::findById($extranetConfig->getSgContentArticleLogoutModuleLogout());

        $moduleLogout = Util::createContent($article, array_merge([
            'type' => 'module',
            'module' => $modules['logout']->id,
        ], ['id' => null !== $moduleLogout ? $moduleLogout->id : null]));

        return [
            'moduleLogout' => $moduleLogout,
        ];
    }

    protected function createContentsArticleSubscribe(ExtranetConfig $extranetConfig, ?PageModel $page, ?ArticleModel $article, array $modules): array
    {
        $headline = ContentModel::findById($extranetConfig->getSgContentArticleSubscribeHeadline());
        $moduleSubscribe = ContentModel::findById($extranetConfig->getSgContentArticleSubscribeModuleSubscribe());

        if ($extranetConfig->getSgCanSubscribe()) {
            $headline = Util::createContent($article, array_merge([
                'type' => 'headline',
                'headline' => serialize(['unit' => 'h1', 'value' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleSubscribeHeadline', [], 'contao_default')]),
                'cssID' => ',sep-bottom',
            ], null !== $headline ? ['id' => $headline->id] : []));

            $moduleSubscribe = Util::createContent($article, array_merge([
                'type' => 'module',
                'module' => $modules['subscribe']->id,
            ], ['id' => null !== $moduleSubscribe ? $moduleSubscribe->id : null]));
        } else {
            if (null !== $headline) {
                $headline->delete();
                $headline = null;
            }
            if (null !== $moduleSubscribe) {
                $moduleSubscribe->delete();
                $moduleSubscribe = null;
            }
        }

        return [
            'headline' => $headline,
            'moduleSubscribe' => $moduleSubscribe,
        ];
    }

    protected function createContentsArticleSubscribeConfirm(ExtranetConfig $extranetConfig, ?PageModel $page, ?ArticleModel $article): array
    {
        $headline = ContentModel::findById($extranetConfig->getSgContentArticleSubscribeConfirmHeadline());
        $text = ContentModel::findById($extranetConfig->getSgContentArticleSubscribeConfirmText());

        if ($extranetConfig->getSgCanSubscribe()) {
            $headline = Util::createContent($article, array_merge([
                'type' => 'headline',
                'headline' => serialize(['unit' => 'h1', 'value' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleSubscribeConfirmHeadline', [], 'contao_default')]),
                'cssID' => ',sep-bottom',
            ], null !== $headline ? ['id' => $headline->id] : []));

            $text = Util::createContent($article, array_merge([
                'type' => 'text',
                'text' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleSubscribeConfirmText', [], 'contao_default'),
                'cssID' => ',sep-bottom',
            ], null !== $text ? ['id' => $text->id] : []));
        } else {
            if (null !== $headline) {
                $headline->delete();
                $headline = null;
            }
            if (null !== $text) {
                $text->delete();
                $text = null;
            }
        }

        return [
            'headline' => $headline,
            'text' => $text,
        ];
    }

    protected function createContentsArticleSubscribeValidate(ExtranetConfig $extranetConfig, ?PageModel $page, ?ArticleModel $article, array $modules): array
    {
        $headline = ContentModel::findById($extranetConfig->getSgContentArticleSubscribeValidateHeadline());
        $text = ContentModel::findById($extranetConfig->getSgContentArticleSubscribeValidateText());
        $moduleLoginGuests = ContentModel::findById($extranetConfig->getSgContentArticleSubscribeValidateModuleLoginGuests());

        if ($extranetConfig->getSgCanSubscribe()) {
            $headline = Util::createContent($article, array_merge([
                'type' => 'headline',
                'headline' => serialize(['unit' => 'h1', 'value' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleSubscribeValidateHeadline', [], 'contao_default')]),
                'cssID' => ',sep-bottom',
            ], null !== $headline ? ['id' => $headline->id] : []));

            $text = Util::createContent($article, array_merge([
                'type' => 'text',
                'text' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleSubscribeValidateText', [], 'contao_default'),
                'cssID' => ',sep-bottom',
            ], null !== $text ? ['id' => $text->id] : []));

            $moduleLoginGuests = Util::createContent($article, array_merge([
                'type' => 'module',
                'module' => $modules['login']->id,
                'guests' => 1,
            ], ['id' => null !== $moduleLoginGuests ? $moduleLoginGuests->id : null]));
        } else {
            if (null !== $headline) {
                $headline->delete();
                $headline = null;
            }
            if (null !== $text) {
                $text->delete();
                $text = null;
            }
            if (null !== $moduleLoginGuests) {
                $moduleLoginGuests->delete();
                $moduleLoginGuests = null;
            }
        }

        return [
            'headline' => $headline,
            'text' => $text,
            'moduleLoginGuests' => $moduleLoginGuests,
        ];
    }

    protected function createContentsArticleUnsubscribe(ExtranetConfig $extranetConfig, ?PageModel $page, ?ArticleModel $article, PageModel $pageExtranet, array $modules): array
    {
        $headline = ContentModel::findById($extranetConfig->getSgContentArticleUnsubscribeHeadline());
        $text = ContentModel::findById($extranetConfig->getSgContentArticleUnsubscribeText());
        $hyperlink = ContentModel::findById($extranetConfig->getSgContentArticleUnsubscribeHyperlink());
        if ($extranetConfig->getSgCanSubscribe()) {
            $headline = Util::createContent($article, array_merge([
                'type' => 'headline',
                'headline' => serialize(['unit' => 'h1', 'value' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleUnsubscribeHeadline', [], 'contao_default')]),
                'cssID' => ',sep-bottom',
            ], null !== $headline ? ['id' => $headline->id] : []));

            $text = Util::createContent($article, array_merge([
                'type' => 'text',
                'text' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleUnsubscribeText', [], 'contao_default'),
                'cssID' => ',sep-bottom',
            ], null !== $text ? ['id' => $text->id] : []));

            $hyperlink = Util::createContent($article, array_merge([
                'type' => 'hyperlink',
                'url' => sprintf('{{link_url::%s}}', $pageExtranet->id),
                'linkTitle' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleUnsubscribeHyperlink', [], 'contao_default'),
                'titleText' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleUnsubscribeHyperlink', [], 'contao_default'),
            ], ['id' => null !== $hyperlink ? $hyperlink->id : null]));
        } else {
            if (null !== $headline) {
                $headline->delete();
                $headline = null;
            }
            if (null !== $text) {
                $text->delete();
                $text = null;
            }
            if (null !== $hyperlink) {
                $hyperlink->delete();
                $hyperlink = null;
            }
        }

        return [
            'headline' => $headline,
            'text' => $text,
            'hyperlink' => $hyperlink,
        ];
    }

    protected function createContents(array $pages, array $articles, array $modules, array $groups): array
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        $extranetConfig = $config->getSgExtranet();

        return [
            'extranet' => $this->createContentsArticleExtranet($extranetConfig, $pages['extranet'], $articles['extranet'], $modules, $groups['members']),
            'error401' => $this->createContentsArticle401($extranetConfig, $pages['error401'], $articles['error401'], $modules),
            'error403' => $this->createContentsArticle403($extranetConfig, $pages['error403'], $articles['error403'], $pages['extranet']),
            'content' => $this->createContentsArticleContent($extranetConfig, $pages['content'], $articles['content']),
            'data' => $this->createContentsArticleData($extranetConfig, $pages['data'], $articles['data'], $modules),
            'dataConfirm' => $this->createContentsArticleDataConfirm($extranetConfig, $pages['dataConfirm'], $articles['dataConfirm'], $pages['extranet']),
            'password' => $this->createContentsArticlePassword($extranetConfig, $pages['password'], $articles['password'], $modules),
            'passwordConfirm' => $this->createContentsArticlePasswordConfirm($extranetConfig, $pages['passwordConfirm'], $articles['passwordConfirm']),
            'passwordValidate' => $this->createContentsArticlePasswordValidate($extranetConfig, $pages['passwordValidate'], $articles['passwordValidate'], $modules),
            'logout' => $this->createContentsArticleLogout($extranetConfig, $pages['logout'], $articles['logout'], $modules),
            'subscribe' => $this->createContentsArticleSubscribe($extranetConfig, $pages['subscribe'], $articles['subscribe'], $modules),
            'subscribeConfirm' => $this->createContentsArticleSubscribeConfirm($extranetConfig, $pages['subscribeConfirm'], $articles['subscribeConfirm']),
            'subscribeValidate' => $this->createContentsArticleSubscribeValidate($extranetConfig, $pages['subscribeValidate'], $articles['subscribeValidate'], $modules),
            'unsubscribe' => $this->createContentsArticleUnsubscribe($extranetConfig, $pages['unsubscribeConfirm'], $articles['unsubscribeConfirm'], $pages['extranet'], $modules),
        ];
    }

    protected function createMembers(array $groups): array
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        $extranetConfig = $config->getSgExtranet();

        $objUser = null !== $extranetConfig->getSgMemberExample()
                    ? MemberModel::findOneById($extranetConfig->getSgMemberExample()) ?? new MemberModel()
                    : MemberModel::findOneByUsername('test@webexmachina.fr') ?? new MemberModel()
;
        $objUser->tstamp = time();
        $objUser->dateAdded = time();
        $objUser->firstname = 'John';
        $objUser->lastname = 'Doe';
        $objUser->email = 'test@webexmachina.fr';
        $objUser->login = 1;
        $objUser->groups = serialize([0 => $groups['members']->id]);
        $objUser->username = 'test@webexmachina.fr';
        $objUser->password = password_hash('webexmachina69', \PASSWORD_DEFAULT);
        $objUser->save();

        return ['example' => $objUser];
    }

    protected function createMemberGroups(string $groupTitle): array
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        $extranetConfig = $config->getSgExtranet();

        if (null !== $extranetConfig->getSgMemberGroupMembers()) {
            $objUserGroup = MemberGroupModel::findOneById($extranetConfig->getSgMemberGroupMembers()) ?? new MemberGroupModel();
        } else {
            $objUserGroup = new MemberGroupModel();
        }
        $objUserGroup->tstamp = time();
        $objUserGroup->name = $groupTitle;
        $objUserGroup->save();

        return [
            'members' => $objUserGroup,
        ];
    }

    protected function createNotificationChangeData(CoreConfig $config, ExtranetConfig $extranetConfig): NotificationModel
    {
        $nc = NotificationModel::findOneById($extranetConfig->getSgNotificationChangeData()) ?? new NotificationModel();
        $nc->tstamp = time();
        $nc->title = $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.notificationChangeDataTitle', [], 'contao_default');
        $nc->type = 'member_personaldata';
        $nc->save();

        return $nc;
    }

    protected function createNotificationPassword(CoreConfig $config, ExtranetConfig $extranetConfig): NotificationModel
    {
        $nc = NotificationModel::findOneById($extranetConfig->getSgNotificationPassword()) ?? new NotificationModel();
        $nc->tstamp = time();
        $nc->title = $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.notificationPasswordTitle', [], 'contao_default');
        $nc->type = 'member_password';
        $nc->save();

        return $nc;
    }

    protected function createNotificationSubscription(CoreConfig $config, ExtranetConfig $extranetConfig): ?NotificationModel
    {
        $nc = NotificationModel::findOneById($extranetConfig->getSgNotificationSubscription()) ?? new NotificationModel();

        if (!$extranetConfig->getSgCanSubscribe()) {
            if (null !== $nc) {
                $nc->delete();
            }

            return null;
        }

        $nc->tstamp = time();
        $nc->title = $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.notificationSubscriptionTitle', [], 'contao_default');
        $nc->type = 'member_password';
        $nc->save();

        return $nc;
    }

    protected function createNotifications(): array
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        $extranetConfig = $config->getSgExtranet();

        return [
            'changeData' => $this->createNotificationChangeData($config, $extranetConfig),
            'password' => $this->createNotificationPassword($config, $extranetConfig),
            'subscription' => $this->createNotificationSubscription($config, $extranetConfig),
        ];
    }

    protected function createNotificationsMessagesChangeData(CoreConfig $config, ExtranetConfig $extranetConfig, NotificationModel $notification, GatewayModel $gateway): NotificationMessageModel
    {
        $nm = NotificationMessageModel::findOneById($extranetConfig->getSgNotificationChangeDataMessage()) ?? new NotificationMessageModel();
        $nm->pid = $notification->id;
        $nm->gateway = $config->getSgNotificationGatewayEmail();
        $nm->gateway_type = 'email';
        $nm->tstamp = time();
        $nm->title = $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.notificationGatewayMessageChangeDataTitle', [], 'contao_default');
        $nm->published = 1;
        $nm->save();

        return $nm;
    }

    protected function createNotificationsMessagesPassword(CoreConfig $config, ExtranetConfig $extranetConfig, NotificationModel $notification, GatewayModel $gateway): NotificationMessageModel
    {
        $nm = NotificationMessageModel::findOneById($extranetConfig->getSgNotificationPasswordMessage()) ?? new NotificationMessageModel();
        $nm->pid = $notification->id;
        $nm->gateway = $config->getSgNotificationGatewayEmail();
        $nm->gateway_type = 'email';
        $nm->tstamp = time();
        $nm->title = $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.notificationGatewayMessagePasswordTitle', [], 'contao_default');
        $nm->published = 1;
        $nm->save();

        return $nm;
    }

    protected function createNotificationsMessagesSubscription(CoreConfig $config, ExtranetConfig $extranetConfig, ?NotificationModel $notification, GatewayModel $gateway): ?NotificationMessageModel
    {
        $nm = NotificationMessageModel::findOneById($extranetConfig->getSgNotificationSubscriptionMessage()) ?? new NotificationMessageModel();

        if (!$extranetConfig->getSgCanSubscribe()) {
            if (null !== $nm) {
                $nm->delete();
            }

            return null;
        }
        $nm->pid = $notification->id;
        $nm->gateway = $config->getSgNotificationGatewayEmail();
        $nm->gateway_type = 'email';
        $nm->tstamp = time();
        $nm->title = $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.notificationGatewayMessageSubscriptionTitle', [], 'contao_default');
        $nm->published = 1;
        $nm->save();

        return $nm;
    }

    protected function createNotificationsMessages(array $notifications): array
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        $extranetConfig = $config->getSgExtranet();

        $gateway = GatewayModel::findOneById($config->getSgNotificationGatewayEmail());

        return [
            'changeData' => $this->createNotificationsMessagesChangeData($config, $extranetConfig, $notifications['changeData'], $gateway),
            'password' => $this->createNotificationsMessagesPassword($config, $extranetConfig, $notifications['password'], $gateway),
            'subscription' => $this->createNotificationsMessagesSubscription($config, $extranetConfig, $notifications['subscription'], $gateway),
        ];
    }

    protected function createNotificationsMessagesLanguagesChangeData(CoreConfig $config, ExtranetConfig $extranetConfig, NotificationMessageModel $gatewayMessage): NotificationLanguageModel
    {
        $strText = file_get_contents(sprintf('%s/public/bundles/wemsmartgear/examples/extranet/%s/change_data.html', TL_ROOT, $this->language));

        $nl = NotificationLanguageModel::findOneById($extranetConfig->getSgNotificationChangeDataMessageLanguage()) ?? new NotificationLanguageModel();
        $nl->pid = $gatewayMessage->id;
        $nl->tstamp = time();
        $nl->language = 'fr';
        $nl->fallback = 1;
        $nl->recipients = '##member_email##';
        $nl->gateway_type = 'email';
        $nl->email_sender_name = $config->getSgWebsiteTitle();
        $nl->email_sender_address = $config->getSgOwnerEmail();
        $nl->email_subject = $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.notificationGatewayMessageLanguageChangeDataSubject', [$config->getSgWebsiteTitle()], 'contao_default');
        $nl->email_mode = 'textAndHtml';
        $nl->email_text = $this->htmlDecoder->htmlToPlainText($strText, false);
        $nl->email_html = $strText;
        $nl->save();

        return $nl;
    }

    protected function createNotificationsMessagesLanguagesPassword(CoreConfig $config, ExtranetConfig $extranetConfig, NotificationMessageModel $gatewayMessage): NotificationLanguageModel
    {
        $strText = file_get_contents(sprintf('%s/public/bundles/wemsmartgear/examples/extranet/%s/password.html', TL_ROOT, $this->language));

        $nl = NotificationLanguageModel::findOneById($extranetConfig->getSgNotificationPasswordMessageLanguage()) ?? new NotificationLanguageModel();
        $nl->pid = $gatewayMessage->id;
        $nl->tstamp = time();
        $nl->language = 'fr';
        $nl->fallback = 1;
        $nl->recipients = '##member_email##';
        $nl->gateway_type = 'email';
        $nl->email_sender_name = $config->getSgWebsiteTitle();
        $nl->email_sender_address = $config->getSgOwnerEmail();
        $nl->email_subject = $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.notificationGatewayMessageLanguagePasswordSubject', [$config->getSgWebsiteTitle()], 'contao_default');
        $nl->email_mode = 'textAndHtml';
        $nl->email_text = $this->htmlDecoder->htmlToPlainText($strText, false);
        $nl->email_html = $strText;
        $nl->save();

        return $nl;
    }

    protected function createNotificationsMessagesLanguagesSubscription(CoreConfig $config, ExtranetConfig $extranetConfig, ?NotificationMessageModel $gatewayMessage): ?NotificationLanguageModel
    {
        $strText = file_get_contents(sprintf('%s/public/bundles/wemsmartgear/examples/extranet/%s/subscription.html', TL_ROOT, $this->language));

        $nl = NotificationLanguageModel::findOneById($extranetConfig->getSgNotificationSubscriptionMessageLanguage()) ?? new NotificationLanguageModel();

        if (!$extranetConfig->getSgCanSubscribe()) {
            if (null !== $nl) {
                $nl->delete();
            }

            return null;
        }

        $nl->pid = $gatewayMessage->id;
        $nl->tstamp = time();
        $nl->language = 'fr';
        $nl->fallback = 1;
        $nl->recipients = '##member_email##';
        $nl->gateway_type = 'email';
        $nl->email_sender_name = $config->getSgWebsiteTitle();
        $nl->email_sender_address = $config->getSgOwnerEmail();
        $nl->email_subject = $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.notificationGatewayMessageLanguageSubscriptionSubject', [$config->getSgWebsiteTitle()], 'contao_default');
        $nl->email_mode = 'textAndHtml';
        $nl->email_text = $this->htmlDecoder->htmlToPlainText($strText, false);
        $nl->email_html = $strText;
        $nl->save();

        return $nl;
    }

    protected function createNotificationsMessagesLanguages(array $notificationMessages): array
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        $extranetConfig = $config->getSgExtranet();

        return [
            'changeData' => $this->createNotificationsMessagesLanguagesChangeData($config, $extranetConfig, $notificationMessages['changeData']),
            'password' => $this->createNotificationsMessagesLanguagesPassword($config, $extranetConfig, $notificationMessages['password']),
            'subscription' => $this->createNotificationsMessagesLanguagesSubscription($config, $extranetConfig, $notificationMessages['subscription']),
        ];
    }

    protected function updateModuleConfigurationAfterGenerations(array $pages, array $articles, array $modules, array $contents, array $members, array $memberGroups, array $notifications, array $notificationMessages, array $notificationMessagesLanguages): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var ExtranetConfig */
        $extranetConfig = $config->getSgExtranet();

        $extranetConfig
            ->setSgMemberExample((int) $members['example']->id)
            ->setSgMemberGroupMembers((int) $memberGroups['members']->id)
            ->setSgPageExtranet((int) $pages['extranet']->id)
            ->setSgPage401((int) $pages['error401']->id)
            ->setSgPage403((int) $pages['error403']->id)
            ->setSgPageContent((int) $pages['content']->id)
            ->setSgPageData((int) $pages['data']->id)
            ->setSgPageDataConfirm((int) $pages['dataConfirm']->id)
            ->setSgPagePassword((int) $pages['password']->id)
            ->setSgPagePasswordConfirm((int) $pages['passwordConfirm']->id)
            ->setSgPagePasswordValidate((int) $pages['passwordValidate']->id)
            ->setSgPageLogout((int) $pages['logout']->id)
            ->setSgPageSubscribe(
                null === $pages['subscribe']
                ? $pages['subscribe']
                : (int) $pages['subscribe']->id
            )
            ->setSgPageSubscribeConfirm(
                null === $pages['subscribeConfirm']
                ? $pages['subscribeConfirm']
                : (int) $pages['subscribeConfirm']->id
            )
            ->setSgPageSubscribeValidate(
                null === $pages['subscribeValidate']
                ? $pages['subscribeValidate']
                : (int) $pages['subscribeValidate']->id
            )
            ->setSgPageUnsubscribeConfirm(
                null === $pages['unsubscribeConfirm']
                ? $pages['unsubscribeConfirm']
                : (int) $pages['unsubscribeConfirm']->id
            )
            ->setSgArticleExtranet((int) $articles['extranet']->id)
            ->setSgArticle401((int) $articles['error401']->id)
            ->setSgArticle403((int) $articles['error403']->id)
            ->setSgArticleContent((int) $articles['content']->id)
            ->setSgArticleData((int) $articles['data']->id)
            ->setSgArticleDataConfirm((int) $articles['dataConfirm']->id)
            ->setSgArticlePassword((int) $articles['password']->id)
            ->setSgArticlePasswordConfirm((int) $articles['passwordConfirm']->id)
            ->setSgArticlePasswordValidate((int) $articles['passwordValidate']->id)
            ->setSgArticleLogout((int) $articles['logout']->id)
            ->setSgArticleSubscribe(
                null === $articles['subscribe']
                ? $articles['subscribe']
                : (int) $articles['subscribe']->id
            )
            ->setSgArticleSubscribeConfirm(
                null === $articles['subscribeConfirm']
                ? $articles['subscribeConfirm']
                : (int) $articles['subscribeConfirm']->id
            )
            ->setSgArticleSubscribeValidate(
                null === $articles['subscribeValidate']
                ? $articles['subscribeValidate']
                : (int) $articles['subscribeValidate']->id
            )
            ->setSgArticleUnsubscribeConfirm(
                null === $articles['unsubscribeConfirm']
                ? $articles['unsubscribeConfirm']
                : (int) $articles['unsubscribeConfirm']->id
            )
            ->setSgNotificationChangeData((int) $notifications['changeData']->id)
            ->setSgNotificationPassword((int) $notifications['password']->id)
            ->setSgNotificationSubscription(
                null === $notifications['subscription']
                ? $notifications['subscription']
                : (int) $notifications['subscription']->id
            )
            ->setSgNotificationChangeDataMessage((int) $notificationMessages['changeData']->id)
            ->setSgNotificationPasswordMessage((int) $notificationMessages['password']->id)
            ->setSgNotificationSubscriptionMessage(
                null === $notificationMessages['subscription']
                ? $notificationMessages['subscription']
                : (int) $notificationMessages['subscription']->id
            )
            ->setSgNotificationChangeDataMessageLanguage((int) $notificationMessagesLanguages['changeData']->id)
            ->setSgNotificationPasswordMessageLanguage((int) $notificationMessagesLanguages['password']->id)
            ->setSgNotificationSubscriptionMessageLanguage(
                null === $notificationMessagesLanguages['subscription']
                ? $notificationMessagesLanguages['subscription']
                : (int) $notificationMessagesLanguages['subscription']->id
            )
            ->setSgModuleLogin((int) $modules['login']->id)
            ->setSgModuleLogout((int) $modules['logout']->id)
            ->setSgModuleData((int) $modules['data']->id)
            ->setSgModulePassword((int) $modules['password']->id)
            ->setSgModuleNav((int) $modules['nav']->id)
            ->setSgModuleSubscribe(
                null === $modules['subscribe']
                ? $modules['subscribe']
                : (int) $modules['subscribe']->id
            )
            ->setSgModuleCloseAccount(
                null === $modules['closeAccount']
                ? $modules['closeAccount']
                : (int) $modules['closeAccount']->id
            )
            ->setSgContentArticleExtranetHeadline((int) $contents['extranet']['headline']->id)
            ->setSgContentArticleExtranetModuleLoginGuests((int) $contents['extranet']['moduleLoginGuests']->id)
            ->setSgContentArticleExtranetGridStartA((int) $contents['extranet']['gridStartA']->id)
            ->setSgContentArticleExtranetGridStartB((int) $contents['extranet']['gridStartB']->id)
            ->setSgContentArticleExtranetModuleLoginLogged((int) $contents['extranet']['moduleLoginLogged']->id)
            ->setSgContentArticleExtranetModuleNav((int) $contents['extranet']['moduleNav']->id)
            ->setSgContentArticleExtranetGridStopA((int) $contents['extranet']['gridStopA']->id)
            ->setSgContentArticleExtranetText((int) $contents['extranet']['text']->id)
            ->setSgContentArticleExtranetGridStopB((int) $contents['extranet']['gridStopB']->id)
            ->setSgContentArticle401Headline((int) $contents['error401']['headline']->id)
            ->setSgContentArticle401Text((int) $contents['error401']['text']->id)
            ->setSgContentArticle401ModuleLoginGuests((int) $contents['error401']['moduleLoginGuests']->id)
            ->setSgContentArticle403Headline((int) $contents['error403']['headline']->id)
            ->setSgContentArticle403Text((int) $contents['error403']['text']->id)
            ->setSgContentArticle403Hyperlink((int) $contents['error403']['hyperlink']->id)
            ->setSgContentArticleContentHeadline((int) $contents['content']['headline']->id)
            ->setSgContentArticleContentText((int) $contents['content']['text']->id)
            ->setSgContentArticleDataHeadline((int) $contents['data']['headline']->id)
            ->setSgContentArticleDataModuleData((int) $contents['data']['moduleData']->id)
            ->setSgContentArticleDataHeadlineCloseAccount(
                null === $contents['data']['headlineCloseAccount']
                ? $contents['data']['headlineCloseAccount']
                : (int) $contents['data']['headlineCloseAccount']->id
            )
            ->setSgContentArticleDataTextCloseAccount(
                null === $contents['data']['textCloseAccount']
                ? $contents['data']['textCloseAccount']
                : (int) $contents['data']['textCloseAccount']->id
            )
            ->setSgContentArticleDataModuleCloseAccount(
                null === $contents['data']['moduleCloseAccount']
                ? $contents['data']['moduleCloseAccount']
                : (int) $contents['data']['moduleCloseAccount']->id
            )
            ->setSgContentArticleDataConfirmHeadline((int) $contents['dataConfirm']['headline']->id)
            ->setSgContentArticleDataConfirmText((int) $contents['dataConfirm']['text']->id)
            ->setSgContentArticleDataConfirmHyperlink((int) $contents['dataConfirm']['hyperlink']->id)
            ->setSgContentArticlePasswordHeadline((int) $contents['password']['headline']->id)
            ->setSgContentArticlePasswordModulePassword((int) $contents['password']['modulePassword']->id)
            ->setSgContentArticlePasswordConfirmHeadline((int) $contents['passwordConfirm']['headline']->id)
            ->setSgContentArticlePasswordConfirmText((int) $contents['passwordConfirm']['text']->id)
            ->setSgContentArticlePasswordValidateHeadline((int) $contents['passwordValidate']['headline']->id)
            ->setSgContentArticlePasswordValidateModulePassword((int) $contents['passwordValidate']['modulePassword']->id)
            ->setSgContentArticleLogoutModuleLogout((int) $contents['logout']['moduleLogout']->id)
            ->setSgContentArticleSubscribeHeadline(
                null === $contents['subscribe']['headline']
                ? $contents['subscribe']['headline']
                : (int) $contents['subscribe']['headline']->id
            )
            ->setSgContentArticleSubscribeModuleSubscribe(
                null === $contents['subscribe']['moduleSubscribe']
                ? $contents['subscribe']['moduleSubscribe']
                : (int) $contents['subscribe']['moduleSubscribe']->id
            )
            ->setSgContentArticleSubscribeConfirmHeadline(
                null === $contents['subscribeConfirm']['headline']
                ? $contents['subscribeConfirm']['headline']
                : (int) $contents['subscribeConfirm']['headline']->id
            )
            ->setSgContentArticleSubscribeConfirmText(
                null === $contents['subscribeConfirm']['text']
                ? $contents['subscribeConfirm']['text']
                : (int) $contents['subscribeConfirm']['text']->id
            )
            ->setSgContentArticleSubscribeValidateHeadline(
                null === $contents['subscribeValidate']['headline']
                ? $contents['subscribeValidate']['headline']
                : (int) $contents['subscribeValidate']['headline']->id
            )
            ->setSgContentArticleSubscribeValidateText(
                null === $contents['subscribeValidate']['text']
                ? $contents['subscribeValidate']['text']
                : (int) $contents['subscribeValidate']['text']->id
            )
            ->setSgContentArticleSubscribeValidateModuleLoginGuests(
                null === $contents['subscribeValidate']['moduleLoginGuests']
                ? $contents['subscribeValidate']['moduleLoginGuests']
                : (int) $contents['subscribeValidate']['moduleLoginGuests']->id
            )
            ->setSgContentArticleUnsubscribeHeadline(
                null === $contents['unsubscribe']['headline']
                ? $contents['unsubscribe']['headline']
                : (int) $contents['unsubscribe']['headline']->id
            )
            ->setSgContentArticleUnsubscribeText(
                null === $contents['unsubscribe']['text']
                ? $contents['unsubscribe']['text']
                : (int) $contents['unsubscribe']['text']->id
            )
            ->setSgContentArticleUnsubscribeHyperlink(
                null === $contents['unsubscribe']['hyperlink']
                ? $contents['unsubscribe']['hyperlink']
                : (int) $contents['unsubscribe']['hyperlink']->id
            )
        ;

        $config->setSgExtranet($extranetConfig);

        $this->configurationManager->save($config);
    }

    protected function updateUserGroups(array $modules): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var ExtranetConfig */
        $extranetConfig = $config->getSgExtranet();

        $modulesTypes = [];
        foreach ($modules as $module) {
            if (null !== $module && null !== $module->type) {
                $modulesTypes[] = $module->type;
            }
        }

        $this->updateUserGroup(UserGroupModel::findOneById($config->getSgUserGroupWebmasters()), $extranetConfig, $modulesTypes);
        $this->updateUserGroup(UserGroupModel::findOneById($config->getSgUserGroupAdministrators()), $extranetConfig, $modulesTypes);
    }

    protected function updateUserGroup(UserGroupModel $objUserGroup, ExtranetConfig $extranetConfig, array $modules): void
    {
        $objFolder = FilesModel::findByPath($extranetConfig->getSgExtranetFolder());
        if (!$objFolder) {
            throw new Exception('Unable to find the folder');
        }

        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);
        $userGroupManipulator
            ->addAllowedModules(array_values($modules))
            ->addAllowedModules(['member', 'mgroup'])
            ->addAllowedFilemounts([$objFolder->uuid])
        ;

        $objUserGroup = $userGroupManipulator->getUserGroup();
        // $objUserGroup->extranetp = serialize(['create', 'delete']);
        $objUserGroup->save();
    }
}
