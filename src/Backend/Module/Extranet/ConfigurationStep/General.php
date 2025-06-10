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

namespace WEM\SmartgearBundle\Backend\Module\Extranet\ConfigurationStep;

use Contao\ArticleModel;
use Contao\BackendUser;
use Contao\ContentModel;
use Contao\CoreBundle\String\HtmlDecoder;
use Contao\FilesModel;
use Contao\Folder;
use Contao\Input;
use Contao\MemberGroupModel;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\UserGroupModel;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\GridBundle\Classes\GridStartManipulator;
use WEM\SmartgearBundle\Classes\Backend\ConfigurationStep;
use WEM\SmartgearBundle\Classes\Command\Util as CommandUtil;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\UserGroupModelUtil;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Classes\Utils\ArticleUtil;
use WEM\SmartgearBundle\Classes\Utils\ContentUtil;
use WEM\SmartgearBundle\Classes\Utils\ModuleUtil;
use WEM\SmartgearBundle\Classes\Utils\PageUtil;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Config\Module\Extranet\Extranet as ExtranetConfig;
use WEM\SmartgearBundle\Model\Member as MemberModel;
use WEM\SmartgearBundle\Model\Module;
use WEM\SmartgearBundle\Model\NotificationCenter\Gateway as GatewayModel;
use WEM\SmartgearBundle\Model\NotificationCenter\Language as NotificationLanguageModel;
use WEM\SmartgearBundle\Model\NotificationCenter\Message as NotificationMessageModel;
use WEM\SmartgearBundle\Model\NotificationCenter\Notification as NotificationModel;

class General extends ConfigurationStep
{
    protected string $language;

    public function __construct(
        string $module,
        string $type,
        protected TranslatorInterface $translator,
        protected ConfigurationManager $configurationManager,
        protected CommandUtil $commandUtil,
        protected HtmlDecoder $htmlDecoder,
    ) {
        parent::__construct($module, $type);
        $this->language = BackendUser::getInstance()->language;

        $this->title = $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.title', [], 'contao_default');
        /** @var ExtranetConfig $config */
        $config = $this->configurationManager->load()->getSgExtranet();

        $this->addTextField('groupTitle', $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.groupTitle', [], 'contao_default'), $config->getSgMemberGroupMembersTitle(), true);

        $this->addTextField('pageTitle', $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.pageTitle', [], 'contao_default'), $config->getSgPageExtranetTitle(), true);

        $this->addCheckboxField('canSubscribe', $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.canSubscribe', [], 'contao_default'), '1', $config->getSgCanSubscribe());
    }

    /**
     * @throws Exception
     */
    public function isStepValid(): bool
    {
        // check if the step is correct
        if (Input::post('groupTitle') === null) {
            throw new Exception($this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.groupTitleMissing', [], 'contao_default'));
        }

        if (Input::post('pageTitle') === null) {
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
        if ((bool) Input::post('canSubscribe') === false) {
            $this->cleanSubscriptionRelatedEntities();
        }

        $this->updateModuleConfigurationAfterGenerations($pages, $articles, $modules, $contents, $members, $memberGroups, $notifications, $notificationGatewayMessages, $notificationGatewayMessagesLanguages);
        $this->updateUserGroups($modules);
        // $this->updateUserGroups();

        $this->commandUtil->executeCmdPHP('cache:clear');
        $this->commandUtil->executeCmdPHP('contao:symlinks');
    }

    public function updateUserGroups(array $modules): void
    // protected function updateUserGroups(): void
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $extranetConfig = $config->getSgExtranet();

        $modulesTypes = [];

        foreach ($modules as $module) {
            if ($module !== null && $module->type !== null) {
                $modulesTypes[] = $module->type;
            }
        }

        // $modules = ModuleModel::findby('id', $extranetConfig->getContaoModulesIds());
        // if ($modules) {
        //     while ($modules->next()) {
        //         $modulesTypes[] = $modules->type;
        //     }
        // }

        $this->updateUserGroup(UserGroupModel::findOneById($config->getSgUserGroupRedactors()), $extranetConfig, $modulesTypes);
        $this->updateUserGroup(UserGroupModel::findOneById($config->getSgUserGroupAdministrators()), $extranetConfig, $modulesTypes);
    }

    protected function cleanSubscriptionRelatedEntities(): void
    {
        // destruction, destruction, destruction !!!
    }

    protected function updateModuleConfiguration(string $groupTitle, string $pageTitle, bool $canSubscribe): void
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
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
        $objFolder = new Folder(ExtranetConfig::DEFAULT_FOLDER_PATH);
        $objFolder->unprotect();
    }

    protected function createPageExtranet(PageModel $rootPage, CoreConfig $config, ExtranetConfig $extranetConfig, string $title): PageModel
    {
        $page = PageModel::findById($extranetConfig->getSgPageExtranet());

        $page = PageUtil::createPage($extranetConfig->getSgPageExtranetTitle(), 0, array_merge([
            'pid' => $rootPage->id,
            'sorting' => PageUtil::getNextAvailablePageSortingByParentPage((int) $rootPage->id),
            'layout' => $rootPage->layout,
            'title' => $title,
            'type' => 'regular',
            'pageTitle' => $title,
            'robots' => 'index,follow',
            'description' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.pageExtranetDescription', [$extranetConfig->getSgPageExtranetTitle(), $config->getSgWebsiteTitle()], 'contao_default'),
            'published' => 1,
        ], $page !== null ? ['id' => $page->id, 'sorting' => $page->sorting] : []));

        $this->setExtranetConfigKey('setSgPageExtranet', (int) $page->id);

        return $page;
    }

    protected function createPageError401(PageModel $rootPage, CoreConfig $config, ExtranetConfig $extranetConfig): PageModel
    {
        $page = PageModel::findById($extranetConfig->getSgPage401());

        $page = PageUtil::createPage($this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.pageError401Title', [], 'contao_default'), 0, array_merge([
            'pid' => $rootPage->id,
            'sorting' => PageUtil::getNextAvailablePageSortingByParentPage((int) $rootPage->id),
            'layout' => $rootPage->layout,
            'type' => 'error_401',
            'robots' => 'noindex,nofollow',
            'published' => 1,
        ], $page !== null ? ['id' => $page->id, 'sorting' => $page->sorting] : []));

        $this->setExtranetConfigKey('setSgPage401', (int) $page->id);

        return $page;
    }

    protected function createPageError403(PageModel $rootPage, CoreConfig $config, ExtranetConfig $extranetConfig, int $sorting401): PageModel
    {
        $page = PageModel::findById($extranetConfig->getSgPage403());

        $page = PageUtil::createPage($this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.pageError403Title', [], 'contao_default'), 0, array_merge([
            'pid' => $rootPage->id,
            // 'sorting' => $sorting401 + 128,
            'sorting' => PageUtil::getNextAvailablePageSortingByParentPage((int) $rootPage->id),
            'layout' => $rootPage->layout,
            'type' => 'error_403',
            'robots' => 'noindex,nofollow',
            'published' => 1,
        ], $page !== null ? ['id' => $page->id, 'sorting' => $page->sorting] : []));

        $this->setExtranetConfigKey('setSgPage403', (int) $page->id);

        return $page;
    }

    protected function createPageContent(PageModel $rootPage, CoreConfig $config, ExtranetConfig $extranetConfig, array $groups): PageModel
    {
        $page = PageModel::findById($extranetConfig->getSgPageContent());

        $page = PageUtil::createPage($this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.pageContentTitle', [], 'contao_default'), 0, array_merge([
            'pid' => $rootPage->id,
            'sorting' => PageUtil::getNextAvailablePageSortingByParentPage((int) $rootPage->id),
            'layout' => $rootPage->layout,
            'type' => 'regular',
            'robots' => 'noindex,nofollow',
            'protected' => 1,
            'groups' => serialize([0 => $groups['members']->id]),
            'noSearch' => 1,
            'published' => 1,
            'sitemap' => 'map_never',
        ], $page !== null ? ['id' => $page->id, 'sorting' => $page->sorting] : []));

        $this->setExtranetConfigKey('setSgPageContent', (int) $page->id);

        return $page;
    }

    protected function createPageData(PageModel $rootPage, CoreConfig $config, ExtranetConfig $extranetConfig, array $groups): PageModel
    {
        $page = PageModel::findById($extranetConfig->getSgPageData());

        $page = PageUtil::createPage($this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.pageDataTitle', [], 'contao_default'), 0, array_merge([
            'pid' => $rootPage->id,
            'sorting' => PageUtil::getNextAvailablePageSortingByParentPage((int) $rootPage->id),
            'layout' => $rootPage->layout,
            'type' => 'regular',
            'robots' => 'noindex,nofollow',
            'protected' => 1,
            'groups' => serialize([0 => $groups['members']->id]),
            'noSearch' => 1,
            'published' => 1,
            'sitemap' => 'map_never',
        ], $page !== null ? ['id' => $page->id, 'sorting' => $page->sorting] : []));

        $this->setExtranetConfigKey('setSgPageData', (int) $page->id);

        return $page;
    }

    protected function createPageDataConfirm(PageModel $rootPage, CoreConfig $config, ExtranetConfig $extranetConfig, array $groups): PageModel
    {
        $page = PageModel::findById($extranetConfig->getSgPageDataConfirm());

        $page = PageUtil::createPage($this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.pageDataConfirmTitle', [], 'contao_default'), 0, array_merge([
            'pid' => $rootPage->id,
            'sorting' => PageUtil::getNextAvailablePageSortingByParentPage((int) $rootPage->id),
            'layout' => $rootPage->layout,
            'type' => 'regular',
            'robots' => 'noindex,nofollow',
            'protected' => 1,
            'groups' => serialize([0 => $groups['members']->id]),
            'noSearch' => 1,
            'published' => 1,
            'hide' => 1,
            'sitemap' => 'map_never',
        ], $page !== null ? ['id' => $page->id, 'sorting' => $page->sorting] : []));

        $this->setExtranetConfigKey('setSgPageDataConfirm', (int) $page->id);

        return $page;
    }

    protected function createPagePassword(PageModel $rootPage, CoreConfig $config, ExtranetConfig $extranetConfig): PageModel
    {
        $page = PageModel::findById($extranetConfig->getSgPagePassword());

        $page = PageUtil::createPage($this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.pagePasswordTitle', [], 'contao_default'), 0, array_merge([
            'pid' => $rootPage->id,
            'sorting' => PageUtil::getNextAvailablePageSortingByParentPage((int) $rootPage->id),
            'layout' => $rootPage->layout,
            'type' => 'regular',
            'robots' => 'noindex,nofollow',
            'noSearch' => 1,
            'published' => 1,
            'hide' => 1,
            'sitemap' => 'map_never',
        ], $page !== null ? ['id' => $page->id, 'sorting' => $page->sorting] : []));

        $this->setExtranetConfigKey('setSgPagePassword', (int) $page->id);

        return $page;
    }

    protected function createPagePasswordConfirm(PageModel $rootPage, CoreConfig $config, ExtranetConfig $extranetConfig): PageModel
    {
        $page = PageModel::findById($extranetConfig->getSgPagePasswordConfirm());

        $page = PageUtil::createPage($this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.pagePasswordConfirmTitle', [], 'contao_default'), 0, array_merge([
            'pid' => $rootPage->id,
            'sorting' => PageUtil::getNextAvailablePageSortingByParentPage((int) $rootPage->id),
            'layout' => $rootPage->layout,
            'type' => 'regular',
            'robots' => 'noindex,nofollow',
            'noSearch' => 1,
            'published' => 1,
            'hide' => 1,
            'sitemap' => 'map_never',
        ], $page !== null ? ['id' => $page->id, 'sorting' => $page->sorting] : []));

        $this->setExtranetConfigKey('setSgPagePasswordConfirm', (int) $page->id);

        return $page;
    }

    protected function createPagePasswordValidate(PageModel $rootPage, CoreConfig $config, ExtranetConfig $extranetConfig): PageModel
    {
        $page = PageModel::findById($extranetConfig->getSgPagePasswordValidate());

        $page = PageUtil::createPage($this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.pagePasswordValidateTitle', [], 'contao_default'), 0, array_merge([
            'pid' => $rootPage->id,
            'sorting' => PageUtil::getNextAvailablePageSortingByParentPage((int) $rootPage->id),
            'layout' => $rootPage->layout,
            'type' => 'regular',
            'robots' => 'noindex,nofollow',
            'noSearch' => 1,
            'published' => 1,
            'hide' => 1,
            'sitemap' => 'map_never',
        ], $page !== null ? ['id' => $page->id, 'sorting' => $page->sorting] : []));

        $this->setExtranetConfigKey('setSgPagePasswordValidate', (int) $page->id);

        return $page;
    }

    protected function createPageLogout(PageModel $rootPage, CoreConfig $config, ExtranetConfig $extranetConfig): PageModel
    {
        $page = PageModel::findById($extranetConfig->getSgPageLogout());

        $page = PageUtil::createPage($this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.pageLogoutTitle', [], 'contao_default'), 0, array_merge([
            'pid' => $rootPage->id,
            'sorting' => PageUtil::getNextAvailablePageSortingByParentPage((int) $rootPage->id),
            'layout' => $rootPage->layout,
            'type' => 'regular',
            'robots' => 'noindex,nofollow',
            'noSearch' => 1,
            'published' => 1,
            'hide' => 1,
            'sitemap' => 'map_never',
        ], $page !== null ? ['id' => $page->id, 'sorting' => $page->sorting] : []));

        $this->setExtranetConfigKey('setSgPageLogout', (int) $page->id);

        return $page;
    }

    protected function createPageSubscribe(PageModel $rootPage, CoreConfig $config, ExtranetConfig $extranetConfig): ?PageModel
    {
        $page = PageModel::findById($extranetConfig->getSgPageSubscribe());

        if (! $extranetConfig->getSgCanSubscribe()) {
            if ($page !== null) {
                $page->delete();
            }

            $this->setExtranetConfigKey('setSgPageSubscribe', null);

            return null;
        }

        $page = PageUtil::createPage($this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.pageSubscribeTitle', [], 'contao_default'), 0, array_merge([
            'pid' => $rootPage->id,
            'sorting' => PageUtil::getNextAvailablePageSortingByParentPage((int) $rootPage->id),
            'layout' => $rootPage->layout,
            'type' => 'regular',
            'robots' => 'noindex,nofollow',
            'noSearch' => 1,
            'published' => 1,
            'hide' => 1,
            'guests' => 1,
        ], $page !== null ? ['id' => $page->id, 'sorting' => $page->sorting] : []));

        $this->setExtranetConfigKey('setSgPageSubscribe', (int) $page->id);

        return $page;
    }

    protected function createPageSubscribeConfirm(?PageModel $rootPage, CoreConfig $config, ExtranetConfig $extranetConfig): ?PageModel
    {
        $page = PageModel::findById($extranetConfig->getSgPageSubscribeConfirm());

        if (! $extranetConfig->getSgCanSubscribe()) {
            if ($page !== null) {
                $page->delete();
            }

            $this->setExtranetConfigKey('setSgPageSubscribeConfirm', null);

            return null;
        }

        $page = PageUtil::createPage($this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.pageSubscribeConfirmTitle', [], 'contao_default'), 0, array_merge([
            'pid' => $rootPage->id,
            'sorting' => PageUtil::getNextAvailablePageSortingByParentPage((int) $rootPage->id),
            'layout' => $rootPage->layout,
            'type' => 'regular',
            'robots' => 'noindex,nofollow',
            'noSearch' => 1,
            'published' => 1,
            'hide' => 1,
            'guests' => 1,
            'sitemap' => 'map_never',
        ], $page !== null ? ['id' => $page->id, 'sorting' => $page->sorting] : []));

        $this->setExtranetConfigKey('setSgPageSubscribeConfirm', (int) $page->id);

        return $page;
    }

    protected function createPageSubscribeValidate(?PageModel $rootPage, CoreConfig $config, ExtranetConfig $extranetConfig): ?PageModel
    {
        $page = PageModel::findById($extranetConfig->getSgPageSubscribeValidate());

        if (! $extranetConfig->getSgCanSubscribe()) {
            if ($page !== null) {
                $page->delete();
            }

            $this->setExtranetConfigKey('setSgPageSubscribeValidate', null);

            return null;
        }

        $page = PageUtil::createPage($this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.pageSubscribeValidateTitle', [], 'contao_default'), 0, array_merge([
            'pid' => $rootPage->id,
            'sorting' => PageUtil::getNextAvailablePageSortingByParentPage((int) $rootPage->id),
            'layout' => $rootPage->layout,
            'type' => 'regular',
            'robots' => 'noindex,nofollow',
            'noSearch' => 1,
            'published' => 1,
            'hide' => 1,
            'guests' => 1,
            'sitemap' => 'map_never',
        ], $page !== null ? ['id' => $page->id, 'sorting' => $page->sorting] : []));

        $this->setExtranetConfigKey('setSgPageSubscribeValidate', (int) $page->id);

        return $page;
    }

    protected function createPageUnsubscribeConfirm(?PageModel $rootPage, CoreConfig $config, ExtranetConfig $extranetConfig): ?PageModel
    {
        $page = PageModel::findById($extranetConfig->getSgPageUnsubscribeConfirm());

        if (! $extranetConfig->getSgCanSubscribe()) {
            if ($page !== null) {
                $page->delete();
            }

            $this->setExtranetConfigKey('setSgPageUnsubscribeConfirm', null);

            return null;
        }

        $page = PageUtil::createPage($this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.pageUnsubscribeTitle', [], 'contao_default'), 0, array_merge([
            'pid' => $rootPage->id,
            'sorting' => PageUtil::getNextAvailablePageSortingByParentPage((int) $rootPage->id),
            'layout' => $rootPage->layout,
            'type' => 'regular',
            'robots' => 'noindex,nofollow',
            'noSearch' => 1,
            'published' => 1,
            'hide' => 1,
        ], $page !== null ? ['id' => $page->id, 'sorting' => $page->sorting] : []));

        $this->setExtranetConfigKey('setSgPageUnsubscribeConfirm', (int) $page->id);

        return $page;
    }

    protected function createPages(string $pageTitle, array $groups): array
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
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

        $article = ArticleUtil::createArticle($page, array_merge([
            'title' => $extranetConfig->getSgPageExtranetTitle(),
        ], $article !== null ? ['id' => $article->id] : []));

        $this->setExtranetConfigKey('setSgArticleExtranet', (int) $article->id);

        return $article;
    }

    protected function createArticleError401(PageModel $page, ExtranetConfig $extranetConfig): ArticleModel
    {
        $article = ArticleModel::findById($extranetConfig->getSgArticle401());

        $article = ArticleUtil::createArticle($page, array_merge([
            'title' => $page->title,
        ], $article !== null ? ['id' => $article->id] : []));

        $this->setExtranetConfigKey('setSgArticle401', (int) $article->id);

        return $article;
    }

    protected function createArticleError403(PageModel $page, ExtranetConfig $extranetConfig): ArticleModel
    {
        $article = ArticleModel::findById($extranetConfig->getSgArticle403());

        $article = ArticleUtil::createArticle($page, array_merge([
            'title' => $page->title,
        ], $article !== null ? ['id' => $article->id] : []));

        $this->setExtranetConfigKey('setSgArticle403', (int) $article->id);

        return $article;
    }

    protected function createArticleContent(PageModel $page, ExtranetConfig $extranetConfig): ArticleModel
    {
        $article = ArticleModel::findById($extranetConfig->getSgArticleContent());

        $article = ArticleUtil::createArticle($page, array_merge([
            'title' => $page->title,
        ], $article !== null ? ['id' => $article->id] : []));

        $this->setExtranetConfigKey('setSgArticleContent', (int) $article->id);

        return $article;
    }

    protected function createArticleData(PageModel $page, ExtranetConfig $extranetConfig): ArticleModel
    {
        $article = ArticleModel::findById($extranetConfig->getSgArticleData());

        $article = ArticleUtil::createArticle($page, array_merge([
            'title' => $page->title,
        ], $article !== null ? ['id' => $article->id] : []));

        $this->setExtranetConfigKey('setSgArticleData', (int) $article->id);

        return $article;
    }

    protected function createArticleDataConfirm(PageModel $page, ExtranetConfig $extranetConfig): ArticleModel
    {
        $article = ArticleModel::findById($extranetConfig->getSgArticleDataConfirm());

        $article = ArticleUtil::createArticle($page, array_merge([
            'title' => $page->title,
        ], $article !== null ? ['id' => $article->id] : []));

        $this->setExtranetConfigKey('setSgArticleDataConfirm', (int) $article->id);

        return $article;
    }

    protected function createArticlePassword(PageModel $page, ExtranetConfig $extranetConfig): ArticleModel
    {
        $article = ArticleModel::findById($extranetConfig->getSgArticlePassword());

        $article = ArticleUtil::createArticle($page, array_merge([
            'title' => $page->title,
        ], $article !== null ? ['id' => $article->id] : []));

        $this->setExtranetConfigKey('setSgArticlePassword', (int) $article->id);

        return $article;
    }

    protected function createArticlePasswordConfirm(PageModel $page, ExtranetConfig $extranetConfig): ArticleModel
    {
        $article = ArticleModel::findById($extranetConfig->getSgArticlePasswordConfirm());

        $article = ArticleUtil::createArticle($page, array_merge([
            'title' => $page->title,
        ], $article !== null ? ['id' => $article->id] : []));

        $this->setExtranetConfigKey('setSgArticlePasswordConfirm', (int) $article->id);

        return $article;
    }

    protected function createArticlePasswordValidate(PageModel $page, ExtranetConfig $extranetConfig): ArticleModel
    {
        $article = ArticleModel::findById($extranetConfig->getSgArticlePasswordValidate());

        $article = ArticleUtil::createArticle($page, array_merge([
            'title' => $page->title,
        ], $article !== null ? ['id' => $article->id] : []));

        $this->setExtranetConfigKey('setSgArticlePasswordValidate', (int) $article->id);

        return $article;
    }

    protected function createArticleLogout(PageModel $page, ExtranetConfig $extranetConfig): ArticleModel
    {
        $article = ArticleModel::findById($extranetConfig->getSgArticleLogout());

        $article = ArticleUtil::createArticle($page, array_merge([
            'title' => $page->title,
        ], $article !== null ? ['id' => $article->id] : []));

        $this->setExtranetConfigKey('setSgArticleLogout', (int) $article->id);

        return $article;
    }

    protected function createArticleSubscribe(?PageModel $page, ExtranetConfig $extranetConfig): ?ArticleModel
    {
        $article = ArticleModel::findById($extranetConfig->getSgArticleSubscribe());

        if (! $extranetConfig->getSgCanSubscribe()) {
            if ($article !== null) {
                $article->delete();
            }

            $this->setExtranetConfigKey('setSgArticleSubscribe', null);

            return null;
        }

        $article = ArticleUtil::createArticle($page, array_merge([
            'title' => $page->title,
        ], $article !== null ? ['id' => $article->id] : []));

        $this->setExtranetConfigKey('setSgArticleSubscribe', (int) $article->id);

        return $article;
    }

    protected function createArticleSubscribeConfirm(?PageModel $page, ExtranetConfig $extranetConfig): ?ArticleModel
    {
        $article = ArticleModel::findById($extranetConfig->getSgArticleSubscribeConfirm());

        if (! $extranetConfig->getSgCanSubscribe()) {
            if ($article !== null) {
                $article->delete();
            }

            $this->setExtranetConfigKey('setSgArticleSubscribeConfirm', null);

            return null;
        }

        $article = ArticleUtil::createArticle($page, array_merge([
            'title' => $page->title,
        ], $article !== null ? ['id' => $article->id] : []));

        $this->setExtranetConfigKey('setSgArticleSubscribeConfirm', (int) $article->id);

        return $article;
    }

    protected function createArticleSubscribeValidate(?PageModel $page, ExtranetConfig $extranetConfig): ?ArticleModel
    {
        $article = ArticleModel::findById($extranetConfig->getSgArticleSubscribeValidate());

        if (! $extranetConfig->getSgCanSubscribe()) {
            if ($article !== null) {
                $article->delete();
            }

            $this->setExtranetConfigKey('setSgArticleSubscribeValidate', null);

            return null;
        }

        $article = ArticleUtil::createArticle($page, array_merge([
            'title' => $page->title,
        ], $article !== null ? ['id' => $article->id] : []));

        $this->setExtranetConfigKey('setSgArticleSubscribeValidate', (int) $article->id);

        return $article;
    }

    protected function createArticlUnsubscribeConfirm(?PageModel $page, ExtranetConfig $extranetConfig): ?ArticleModel
    {
        $article = ArticleModel::findById($extranetConfig->getSgArticleUnsubscribeConfirm());

        if (! $extranetConfig->getSgCanSubscribe()) {
            if ($article !== null) {
                $article->delete();
            }

            $this->setExtranetConfigKey('setSgArticleUnsubscribeConfirm', null);

            return null;
        }

        $article = ArticleUtil::createArticle($page, array_merge([
            'title' => $page->title,
        ], $article !== null ? ['id' => $article->id] : []));

        $this->setExtranetConfigKey('setSgArticleUnsubscribeConfirm', (int) $article->id);

        return $article;
    }

    protected function createArticles(array $pages): array
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

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

    protected function createModuleLogin(CoreConfig $config, ExtranetConfig $extranetConfig, PageModel $pagePwdLost, PageModel $pageSubscribe): ModuleModel
    {
        $module = new ModuleModel();

        if ($extranetConfig->getSgModuleLogin() !== null) {
            $moduleListOld = ModuleModel::findById($extranetConfig->getSgModuleLogin());
            if ($moduleListOld) {
                $moduleListOld->delete();
            }

            // $module->id = $extranetConfig->getSgModuleLogin();
        }

        $module = ModuleUtil::createModule((int) $config->getSgTheme(), array_merge(
            [
                'name' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.moduleLoginName', [], 'contao_default'),
                'pid' => $config->getSgTheme(),
                'type' => 'login',
                'autologin' => 1,
                'redirectBack' => 1,
                'wem_sg_login_pwd_lost_jumpTo' => $pagePwdLost->id,
            ],
            $extranetConfig->getSgCanSubscribe() ? ['wem_sg_login_register_jumpTo' => $pageSubscribe->id] : [],
            $extranetConfig->getSgModuleLogin() !== null ? ['id' => $extranetConfig->getSgModuleLogin()] : [],
        ));

        $this->setExtranetConfigKey('setSgModuleLogin', (int) $module->id);

        return $module;
    }

    protected function createModuleLogout(CoreConfig $config, ExtranetConfig $extranetConfig, PageModel $page): ModuleModel
    {
        $module = new ModuleModel();

        if ($extranetConfig->getSgModuleLogout() !== null) {
            $moduleListOld = ModuleModel::findById($extranetConfig->getSgModuleLogout());
            if ($moduleListOld) {
                $moduleListOld->delete();
            }
        }

        $module = ModuleUtil::createModule((int) $config->getSgTheme(), array_merge(
            [
                'name' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.moduleLogoutName', [], 'contao_default'),
                'pid' => $config->getSgTheme(),
                'type' => 'logout',
                'jumpTo' => $page->id,
                'tstamp' => time(),
            ],
            $extranetConfig->getSgModuleLogout() !== null ? ['id' => $extranetConfig->getSgModuleLogout()] : [],
        ));

        $this->setExtranetConfigKey('setSgModuleLogout', (int) $module->id);

        return $module;
    }

    protected function createModuleData(CoreConfig $config, ExtranetConfig $extranetConfig, PageModel $page, NotificationModel $notification): ModuleModel
    {
        $module = new ModuleModel();

        if ($extranetConfig->getSgModuleData() !== null) {
            $moduleListOld = ModuleModel::findById($extranetConfig->getSgModuleData());
            if ($moduleListOld) {
                $moduleListOld->delete();
            }
        }

        $module = ModuleUtil::createModule((int) $config->getSgTheme(), array_merge(
            [
                'name' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.moduleDataName', [], 'contao_default'),
                'pid' => $config->getSgTheme(),
                'type' => 'personalData',
                'jumpTo' => $page->id,
                'editable' => serialize(['firstname', 'lastname', 'email', 'username', 'password']),
                'nc_notification' => $notification->id,
                'tstamp' => time(),
            ],
            $extranetConfig->getSgModuleData() !== null ? ['id' => $extranetConfig->getSgModuleData()] : [],
        ));

        $this->setExtranetConfigKey('setSgModuleData', (int) $module->id);

        return $module;
    }

    protected function createModulePassword(CoreConfig $config, ExtranetConfig $extranetConfig, PageModel $pageConfirm, PageModel $pageValidate, NotificationModel $notification): ModuleModel
    {
        $module = new ModuleModel();

        if ($extranetConfig->getSgModulePassword() !== null) {
            $moduleListOld = ModuleModel::findById($extranetConfig->getSgModulePassword());
            if ($moduleListOld) {
                $moduleListOld->delete();
            }
        }

        $module = ModuleUtil::createModule((int) $config->getSgTheme(), array_merge(
            [
                'name' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.modulePasswordName', [], 'contao_default'),
                'pid' => $config->getSgTheme(),
                'type' => 'lostPasswordNotificationCenter',
                'reg_skipName' => 1,
                'jumpTo' => $pageConfirm->id,
                'reg_jumpTo' => $pageValidate->id,
                'nc_notification' => $notification->id,
                'tstamp' => time(),
            ],
            $extranetConfig->getSgModulePassword() !== null ? ['id' => $extranetConfig->getSgModulePassword()] : [],
        ));

        $this->setExtranetConfigKey('setSgModulePassword', (int) $module->id);

        return $module;
    }

    protected function createModuleNav(CoreConfig $config, ExtranetConfig $extranetConfig, PageModel $page): ModuleModel
    {
        $module = new ModuleModel();

        if ($extranetConfig->getSgModuleNav() !== null) {
            $moduleListOld = ModuleModel::findById($extranetConfig->getSgModuleNav());
            if ($moduleListOld) {
                $moduleListOld->delete();
            }
        }

        $module = ModuleUtil::createModule((int) $config->getSgTheme(), array_merge(
            [
                'name' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.moduleNavName', [], 'contao_default'),
                'pid' => $config->getSgTheme(),
                'type' => 'navigation',
                'levelOffset' => 1,
                'defineRoot' => 1,
                'rootPage' => $page->id,
                'tstamp' => time(),
            ],
            $extranetConfig->getSgModuleNav() !== null ? ['id' => $extranetConfig->getSgModuleNav()] : [],
        ));

        $this->setExtranetConfigKey('setSgModuleNav', (int) $module->id);

        return $module;
    }

    protected function createModuleSubscribe(CoreConfig $config, ExtranetConfig $extranetConfig, ?PageModel $pageConfirm, ?PageModel $pageValidate, ?NotificationModel $notification, MemberGroupModel $group): ?ModuleModel
    {
        $module = new ModuleModel();

        if ($extranetConfig->getSgModuleSubscribe() !== null) {
            $moduleListOld = ModuleModel::findById($extranetConfig->getSgModuleSubscribe());
            if ($moduleListOld) {
                $moduleListOld->delete();
            }
        }

        if (! $extranetConfig->getSgCanSubscribe()) {
            $this->setExtranetConfigKey('setSgModuleSubscribe', null);

            return null;
        }

        $module = ModuleUtil::createModule((int) $config->getSgTheme(), array_merge(
            [
                'name' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.moduleSubscribeName', [], 'contao_default'),
                'pid' => $config->getSgTheme(),
                'type' => 'registration',
                'jumpTo' => $pageConfirm->id,
                'reg_jumpTo' => $pageValidate->id,
                'nc_notification' => $notification->id,
                'reg_allowLogin' => 1,
                'editable' => serialize(['firstname', 'lastname', 'email', 'username', 'password']),
                'reg_groups' => serialize([$group->id]),
                'tstamp' => time(),
            ],
            $extranetConfig->getSgModuleSubscribe() !== null ? ['id' => $extranetConfig->getSgModuleSubscribe()] : [],
        ));

        $this->setExtranetConfigKey('setSgModuleSubscribe', (int) $module->id);

        return $module;
    }

    protected function createModuleCloseAccount(CoreConfig $config, ExtranetConfig $extranetConfig, ?PageModel $page): ?ModuleModel
    {
        $module = new ModuleModel();

        if ($extranetConfig->getSgModuleCloseAccount() !== null) {
            $moduleListOld = ModuleModel::findById($extranetConfig->getSgModuleCloseAccount());
            if ($moduleListOld) {
                $moduleListOld->delete();
            }
        }

        if (! $extranetConfig->getSgCanSubscribe()) {
            $this->setExtranetConfigKey('setSgModuleCloseAccount', null);

            return null;
        }

        $module = ModuleUtil::createModule((int) $config->getSgTheme(), array_merge(
            [
                'name' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.moduleCloseAccountName', [], 'contao_default'),
                'pid' => $config->getSgTheme(),
                'type' => 'closeAccount',
                'jumpTo' => $page->id,
                'reg_close' => 'close_delete',
                'tstamp' => time(),
            ],
            $extranetConfig->getSgModuleCloseAccount() !== null ? ['id' => $extranetConfig->getSgModuleCloseAccount()] : [],
        ));

        $this->setExtranetConfigKey('setSgModuleCloseAccount', (int) $module->id);

        return $module;
    }

    protected function createModules(array $pages, array $notifications, array $groups): array
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $extranetConfig = $config->getSgExtranet();

        return [
            'login' => $this->createModuleLogin($config, $extranetConfig, $pages['password'], $pages['subscribe']),
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
        $gridStopA = ContentModel::findById($extranetConfig->getSgContentArticleExtranetGridStopA());

        $headline = ContentUtil::createContent($article, array_merge([
            'type' => 'headline',
            'headline' => serialize(['unit' => 'h1', 'value' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleExtranetHeadline', [], 'contao_default')]),
            'cssID' => ',sep-bottom',
            'sorting' => 128,
        ], $headline !== null ? ['id' => $headline->id] : []));

        $this->setExtranetConfigKey('setSgContentArticleExtranetHeadline', (int) $headline->id);

        $moduleLoginGuests = ContentUtil::createContent($article, ['type' => 'module', 'module' => $modules['login']->id, 'guests' => 1, 'sorting' => 256, 'id' => $moduleLoginGuests !== null ? $moduleLoginGuests->id : null]);

        $this->setExtranetConfigKey('setSgContentArticleExtranetModuleLoginGuests', (int) $moduleLoginGuests->id);

        $gridStartA = ContentUtil::createContent($article, ['type' => 'grid-start', 'protected' => 1, 'groups' => serialize([$group->id]), 'sorting' => 384, 'id' => $gridStartA !== null ? $gridStartA->id : null]);

        $this->setExtranetConfigKey('setSgContentArticleExtranetGridStartA', (int) $gridStartA->id);

        $gridStartB = ContentUtil::createContent($article, ['type' => 'grid-start', 'protected' => 1, 'groups' => serialize([$group->id]), 'sorting' => 512, 'id' => $gridStartB !== null ? $gridStartB->id : null]);

        $this->setExtranetConfigKey('setSgContentArticleExtranetGridStartB', (int) $gridStartB->id);

        $moduleLoginLogged = ContentUtil::createContent($article, ['type' => 'module', 'module' => $modules['login']->id, 'protected' => 1, 'groups' => serialize([$group->id]), 'sorting' => 640, 'id' => $moduleLoginLogged !== null ? $moduleLoginLogged->id : null]);

        $this->setExtranetConfigKey('setSgContentArticleExtranetModuleLoginLogged', (int) $moduleLoginLogged->id);

        $moduleNav = ContentUtil::createContent($article, ['type' => 'module', 'module' => $modules['nav']->id, 'protected' => 1, 'groups' => serialize([$group->id]), 'sorting' => 768, 'id' => $moduleNav !== null ? $moduleNav->id : null]);

        $this->setExtranetConfigKey('setSgContentArticleExtranetModuleNav', (int) $moduleNav->id);

        $gridStopB = ContentUtil::createContent($article, ['type' => 'grid-stop', 'sorting' => 896, 'id' => $gridStopB !== null ? $gridStopB->id : null]);

        $this->setExtranetConfigKey('setSgContentArticleExtranetGridStopB', (int) $gridStopB->id);

        $gridStopA = ContentUtil::createContent($article, ['type' => 'grid-stop', 'sorting' => 1152, 'id' => $gridStopA !== null ? $gridStopA->id : null]);

        $this->setExtranetConfigKey('setSgContentArticleExtranetGridStopA', (int) $gridStopA->id);

        /** @todo : update $gridStartA to apply some style to its fifth element (aka Leeloo) */
        $gsm = GridStartManipulator::create($gridStartA);
        $gsm
            ->recalculateElementsForAllGridSharingTheSamePidAndPtable()
            ->setGridColsAll(3)
            ->setGridColsSm(1)
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
            'gridStopB' => $gridStopB,
        ];
    }

    protected function createContentsArticle401(ExtranetConfig $extranetConfig, PageModel $page, ArticleModel $article, array $modules): array
    {
        $headline = ContentModel::findById($extranetConfig->getSgContentArticle401Headline());
        $text = ContentModel::findById($extranetConfig->getSgContentArticle401Text());
        $moduleLoginGuests = ContentModel::findById($extranetConfig->getSgContentArticle401ModuleLoginGuests());

        $headline = ContentUtil::createContent($article, array_merge([
            'type' => 'headline',
            'headline' => serialize(['unit' => 'h1', 'value' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticle401Headline', [], 'contao_default')]),
            'cssID' => ',sep-bottom',
        ], $headline !== null ? ['id' => $headline->id] : []));

        $this->setExtranetConfigKey('setSgContentArticle401Headline', (int) $headline->id);

        $text = ContentUtil::createContent($article, array_merge([
            'type' => 'text',
            'text' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticle401Text', [], 'contao_default'),
            'cssID' => ',sep-bottom',
        ], $text !== null ? ['id' => $text->id] : []));

        $this->setExtranetConfigKey('setSgContentArticle401Text', (int) $text->id);

        $moduleLoginGuests = ContentUtil::createContent($article, ['type' => 'module', 'module' => $modules['login']->id, 'guests' => 1, 'id' => $moduleLoginGuests !== null ? $moduleLoginGuests->id : null]);

        $this->setExtranetConfigKey('setSgContentArticle401ModuleLoginGuests', (int) $moduleLoginGuests->id);

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

        $headline = ContentUtil::createContent($article, array_merge([
            'type' => 'headline',
            'headline' => serialize(['unit' => 'h1', 'value' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticle403Headline', [], 'contao_default')]),
            'cssID' => ',sep-bottom',
        ], $headline !== null ? ['id' => $headline->id] : []));

        $this->setExtranetConfigKey('setSgContentArticle403Headline', (int) $headline->id);

        $text = ContentUtil::createContent($article, array_merge([
            'type' => 'text',
            'text' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticle403Text', [], 'contao_default'),
            'cssID' => ',sep-bottom',
        ], $text !== null ? ['id' => $text->id] : []));

        $this->setExtranetConfigKey('setSgContentArticle403Text', (int) $text->id);

        $hyperlink = ContentUtil::createContent($article, ['type' => 'hyperlink', 'url' => sprintf('{{link_url::%s}}', $pageExtranet->id), 'linkTitle' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticle403Hyperlink', [], 'contao_default'), 'titleText' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticle403Hyperlink', [], 'contao_default'), 'id' => $hyperlink !== null ? $hyperlink->id : null]);

        $this->setExtranetConfigKey('setSgContentArticle403Hyperlink', (int) $hyperlink->id);

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

        $headline = ContentUtil::createContent($article, array_merge([
            'type' => 'headline',
            'headline' => serialize(['unit' => 'h1', 'value' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleContentHeadline', [], 'contao_default')]),
            'cssID' => ',sep-bottom',
        ], $headline !== null ? ['id' => $headline->id] : []));

        $this->setExtranetConfigKey('setSgContentArticleContentHeadline', (int) $headline->id);

        $text = ContentUtil::createContent($article, array_merge([
            'type' => 'text',
            'text' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleContentText', [], 'contao_default'),
            'cssID' => ',sep-bottom',
        ], $text !== null ? ['id' => $text->id] : []));

        $this->setExtranetConfigKey('setSgContentArticleContentText', (int) $text->id);

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

        $headline = ContentUtil::createContent($article, array_merge([
            'type' => 'headline',
            'headline' => serialize(['unit' => 'h1', 'value' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleDataHeadline', [], 'contao_default')]),
            'cssID' => ',sep-bottom',
        ], $headline !== null ? ['id' => $headline->id] : []));

        $this->setExtranetConfigKey('setSgContentArticleDataHeadline', (int) $headline->id);

        $moduleData = ContentUtil::createContent($article, ['type' => 'module', 'module' => $modules['data']->id, 'id' => $moduleData !== null ? $moduleData->id : null]);

        $this->setExtranetConfigKey('setSgContentArticleDataModuleData', (int) $moduleData->id);

        if ($extranetConfig->getSgCanSubscribe()) {
            $headlineCloseAccount = ContentUtil::createContent($article, array_merge([
                'type' => 'headline',
                'headline' => serialize(['unit' => 'h1', 'value' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleDataHeadlineCloseAccount', [], 'contao_default')]),
                'cssID' => ',sep-bottom',
            ], $headlineCloseAccount !== null ? ['id' => $headlineCloseAccount->id] : []));

            $this->setExtranetConfigKey('setSgContentArticleDataHeadlineCloseAccount', (int) $headlineCloseAccount->id);

            $textCloseAccount = ContentUtil::createContent($article, array_merge([
                'type' => 'text',
                'text' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleDataTextCloseAccount', [], 'contao_default'),
                'cssID' => ',sep-bottom',
            ], $textCloseAccount !== null ? ['id' => $textCloseAccount->id] : []));

            $this->setExtranetConfigKey('setSgContentArticleDataTextCloseAccount', (int) $textCloseAccount->id);

            $moduleCloseAccount = ContentUtil::createContent($article, ['type' => 'module', 'module' => $modules['closeAccount']->id, 'id' => $moduleCloseAccount !== null ? $moduleCloseAccount->id : null]);

            $this->setExtranetConfigKey('setSgContentArticleDataModuleCloseAccount', (int) $moduleCloseAccount->id);
        } else {
            if ($headlineCloseAccount !== null) {
                $headlineCloseAccount->delete();
                $headlineCloseAccount = null;

                $this->setExtranetConfigKey('setSgContentArticleDataHeadlineCloseAccount', null);
            }

            if ($textCloseAccount !== null) {
                $textCloseAccount->delete();
                $textCloseAccount = null;

                $this->setExtranetConfigKey('setSgContentArticleDataTextCloseAccount', null);
            }

            if ($moduleCloseAccount !== null) {
                $moduleCloseAccount->delete();
                $moduleCloseAccount = null;

                $this->setExtranetConfigKey('setSgContentArticleDataModuleCloseAccount', null);
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

        $headline = ContentUtil::createContent($article, array_merge([
            'type' => 'headline',
            'headline' => serialize(['unit' => 'h1', 'value' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleDataConfirmHeadline', [], 'contao_default')]),
            'cssID' => ',sep-bottom',
        ], $headline !== null ? ['id' => $headline->id] : []));

        $this->setExtranetConfigKey('setSgContentArticleDataConfirmHeadline', (int) $headline->id);

        $text = ContentUtil::createContent($article, array_merge([
            'type' => 'text',
            'text' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleDataConfirmText', [], 'contao_default'),
            'cssID' => ',sep-bottom',
        ], $text !== null ? ['id' => $text->id] : []));

        $this->setExtranetConfigKey('setSgContentArticleDataConfirmText', (int) $text->id);

        $hyperlink = ContentUtil::createContent($article, ['type' => 'hyperlink', 'url' => sprintf('{{link_url::%s}}', $pageExtranet->id), 'linkTitle' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleDataConfirmHyperlink', [], 'contao_default'), 'titleText' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleDataConfirmHyperlink', [], 'contao_default'), 'id' => $hyperlink !== null ? $hyperlink->id : null]);

        $this->setExtranetConfigKey('setSgContentArticleDataConfirmHyperlink', (int) $hyperlink->id);

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

        $headline = ContentUtil::createContent($article, array_merge([
            'type' => 'headline',
            'headline' => serialize(['unit' => 'h1', 'value' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticlePasswordHeadline', [], 'contao_default')]),
            'cssID' => ',sep-bottom',
        ], $headline !== null ? ['id' => $headline->id] : []));

        $this->setExtranetConfigKey('setSgContentArticlePasswordHeadline', (int) $headline->id);

        $modulePassword = ContentUtil::createContent($article, ['type' => 'module', 'module' => $modules['password']->id, 'id' => $modulePassword !== null ? $modulePassword->id : null]);

        $this->setExtranetConfigKey('setSgContentArticlePasswordModulePassword', (int) $modulePassword->id);

        return [
            'headline' => $headline,
            'modulePassword' => $modulePassword,
        ];
    }

    protected function createContentsArticlePasswordConfirm(ExtranetConfig $extranetConfig, PageModel $page, ArticleModel $article): array
    {
        $headline = ContentModel::findById($extranetConfig->getSgContentArticlePasswordConfirmHeadline());
        $text = ContentModel::findById($extranetConfig->getSgContentArticlePasswordConfirmText());

        $headline = ContentUtil::createContent($article, array_merge([
            'type' => 'headline',
            'headline' => serialize(['unit' => 'h1', 'value' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticlePasswordConfirmHeadline', [], 'contao_default')]),
            'cssID' => ',sep-bottom',
        ], $headline !== null ? ['id' => $headline->id] : []));

        $this->setExtranetConfigKey('setSgContentArticlePasswordConfirmHeadline', (int) $headline->id);

        $text = ContentUtil::createContent($article, array_merge([
            'type' => 'text',
            'text' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticlePasswordConfirmText', [], 'contao_default'),
            'cssID' => ',sep-bottom',
        ], $text !== null ? ['id' => $text->id] : []));

        $this->setExtranetConfigKey('setSgContentArticlePasswordConfirmText', (int) $text->id);

        return [
            'headline' => $headline,
            'text' => $text,
        ];
    }

    protected function createContentsArticlePasswordValidate(ExtranetConfig $extranetConfig, PageModel $page, ArticleModel $article, array $modules): array
    {
        $headline = ContentModel::findById($extranetConfig->getSgContentArticlePasswordValidateHeadline());
        $modulePassword = ContentModel::findById($extranetConfig->getSgContentArticlePasswordValidateModulePassword());

        $headline = ContentUtil::createContent($article, array_merge([
            'type' => 'headline',
            'headline' => serialize(['unit' => 'h1', 'value' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticlePasswordValidateHeadline', [], 'contao_default')]),
            'cssID' => ',sep-bottom',
        ], $headline !== null ? ['id' => $headline->id] : []));

        $this->setExtranetConfigKey('setSgContentArticlePasswordValidateHeadline', (int) $headline->id);

        $modulePassword = ContentUtil::createContent($article, ['type' => 'module', 'module' => $modules['password']->id, 'id' => $modulePassword !== null ? $modulePassword->id : null]);

        $this->setExtranetConfigKey('setSgContentArticlePasswordValidateModulePassword', (int) $modulePassword->id);

        return [
            'headline' => $headline,
            'modulePassword' => $modulePassword,
        ];
    }

    protected function createContentsArticleLogout(ExtranetConfig $extranetConfig, PageModel $page, ArticleModel $article, array $modules): array
    {
        $moduleLogout = ContentModel::findById($extranetConfig->getSgContentArticleLogoutModuleLogout());

        $moduleLogout = ContentUtil::createContent($article, ['type' => 'module', 'module' => $modules['logout']->id, 'id' => $moduleLogout !== null ? $moduleLogout->id : null]);

        $this->setExtranetConfigKey('setSgContentArticleLogoutModuleLogout', (int) $moduleLogout->id);

        return [
            'moduleLogout' => $moduleLogout,
        ];
    }

    protected function createContentsArticleSubscribe(ExtranetConfig $extranetConfig, ?PageModel $page, ?ArticleModel $article, array $modules): array
    {
        $headline = ContentModel::findById($extranetConfig->getSgContentArticleSubscribeHeadline());
        $moduleSubscribe = ContentModel::findById($extranetConfig->getSgContentArticleSubscribeModuleSubscribe());

        if ($extranetConfig->getSgCanSubscribe()) {
            $headline = ContentUtil::createContent($article, array_merge([
                'type' => 'headline',
                'headline' => serialize(['unit' => 'h1', 'value' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleSubscribeHeadline', [], 'contao_default')]),
                'cssID' => ',sep-bottom',
            ], $headline !== null ? ['id' => $headline->id] : []));

            $this->setExtranetConfigKey('setSgContentArticleSubscribeHeadline', (int) $headline->id);

            $moduleSubscribe = ContentUtil::createContent($article, ['type' => 'module', 'module' => $modules['subscribe']->id, 'id' => $moduleSubscribe !== null ? $moduleSubscribe->id : null]);

            $this->setExtranetConfigKey('setSgContentArticleSubscribeModuleSubscribe', (int) $moduleSubscribe->id);
        } else {
            if ($headline !== null) {
                $headline->delete();
                $headline = null;

                $this->setExtranetConfigKey('setSgContentArticleSubscribeHeadline', null);
            }

            if ($moduleSubscribe !== null) {
                $moduleSubscribe->delete();
                $moduleSubscribe = null;

                $this->setExtranetConfigKey('setSgContentArticleSubscribeModuleSubscribe', null);
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
            $headline = ContentUtil::createContent($article, array_merge([
                'type' => 'headline',
                'headline' => serialize(['unit' => 'h1', 'value' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleSubscribeConfirmHeadline', [], 'contao_default')]),
                'cssID' => ',sep-bottom',
            ], $headline !== null ? ['id' => $headline->id] : []));

            $this->setExtranetConfigKey('setSgContentArticleSubscribeConfirmHeadline', (int) $headline->id);

            $text = ContentUtil::createContent($article, array_merge([
                'type' => 'text',
                'text' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleSubscribeConfirmText', [], 'contao_default'),
                'cssID' => ',sep-bottom',
            ], $text !== null ? ['id' => $text->id] : []));

            $this->setExtranetConfigKey('setSgContentArticleSubscribeConfirmText', (int) $text->id);
        } else {
            if ($headline !== null) {
                $headline->delete();
                $headline = null;

                $this->setExtranetConfigKey('setSgContentArticleSubscribeConfirmHeadline', null);
            }

            if ($text !== null) {
                $text->delete();
                $text = null;

                $this->setExtranetConfigKey('setSgContentArticleSubscribeConfirmText', null);
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
            $headline = ContentUtil::createContent($article, array_merge([
                'type' => 'headline',
                'headline' => serialize(['unit' => 'h1', 'value' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleSubscribeValidateHeadline', [], 'contao_default')]),
                'cssID' => ',sep-bottom',
            ], $headline !== null ? ['id' => $headline->id] : []));

            $this->setExtranetConfigKey('setSgContentArticleSubscribeValidateHeadline', (int) $headline->id);

            $text = ContentUtil::createContent($article, array_merge([
                'type' => 'text',
                'text' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleSubscribeValidateText', [], 'contao_default'),
                'cssID' => ',sep-bottom',
            ], $text !== null ? ['id' => $text->id] : []));

            $this->setExtranetConfigKey('setSgContentArticleSubscribeValidateText', (int) $text->id);

            $moduleLoginGuests = ContentUtil::createContent($article, ['type' => 'module', 'module' => $modules['login']->id, 'guests' => 1, 'id' => $moduleLoginGuests !== null ? $moduleLoginGuests->id : null]);

            $this->setExtranetConfigKey('setSgContentArticleSubscribeValidateModuleLoginGuests', (int) $moduleLoginGuests->id);
        } else {
            if ($headline !== null) {
                $headline->delete();
                $headline = null;

                $this->setExtranetConfigKey('setSgContentArticleSubscribeValidateHeadline', null);
            }

            if ($text !== null) {
                $text->delete();
                $text = null;

                $this->setExtranetConfigKey('setSgContentArticleSubscribeValidateText', null);
            }

            if ($moduleLoginGuests !== null) {
                $moduleLoginGuests->delete();
                $moduleLoginGuests = null;

                $this->setExtranetConfigKey('setSgContentArticleSubscribeValidateModuleLoginGuests', null);
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
            $headline = ContentUtil::createContent($article, array_merge([
                'type' => 'headline',
                'headline' => serialize(['unit' => 'h1', 'value' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleUnsubscribeHeadline', [], 'contao_default')]),
                'cssID' => ',sep-bottom',
            ], $headline !== null ? ['id' => $headline->id] : []));

            $this->setExtranetConfigKey('setSgContentArticleUnsubscribeHeadline', (int) $headline->id);

            $text = ContentUtil::createContent($article, array_merge([
                'type' => 'text',
                'text' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleUnsubscribeText', [], 'contao_default'),
                'cssID' => ',sep-bottom',
            ], $text !== null ? ['id' => $text->id] : []));

            $this->setExtranetConfigKey('setSgContentArticleUnsubscribeText', (int) $text->id);

            $hyperlink = ContentUtil::createContent($article, ['type' => 'hyperlink', 'url' => sprintf('{{link_url::%s}}', $pageExtranet->id), 'linkTitle' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleUnsubscribeHyperlink', [], 'contao_default'), 'titleText' => $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.contentHeadlineArticleUnsubscribeHyperlink', [], 'contao_default'), 'id' => $hyperlink !== null ? $hyperlink->id : null]);

            $this->setExtranetConfigKey('setSgContentArticleUnsubscribeHyperlink', (int) $hyperlink->id);
        } else {
            if ($headline !== null) {
                $headline->delete();
                $headline = null;

                $this->setExtranetConfigKey('setSgContentArticleUnsubscribeHeadline', null);
            }

            if ($text !== null) {
                $text->delete();
                $text = null;

                $this->setExtranetConfigKey('setSgContentArticleUnsubscribeText', null);
            }

            if ($hyperlink !== null) {
                $hyperlink->delete();
                $hyperlink = null;

                $this->setExtranetConfigKey('setSgContentArticleUnsubscribeHyperlink', null);
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
        /** @var CoreConfig $config */
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
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $extranetConfig = $config->getSgExtranet();

        $objUser = $extranetConfig->getSgMemberExample() !== null
                    ? MemberModel::findOneById($extranetConfig->getSgMemberExample()) ?? new MemberModel()
                    : MemberModel::findOneByUsername('test@webexmachina.fr') ?? new MemberModel();
        $objUser->tstamp = time();
        $objUser->dateAdded = time();
        $objUser->firstname = 'John';
        $objUser->lastname = 'Doe';
        $objUser->email = 'test@webexmachina.fr';
        $objUser->login = 1;
        $objUser->groups = serialize([0 => $groups['members']->id]);
        $objUser->username = 'test@webexmachina.fr';
        $objUser->password = password_hash('12345678', PASSWORD_DEFAULT);
        $objUser->save();

        $this->setExtranetConfigKey('setSgMemberExample', (int) $objUser->id);

        return ['example' => $objUser];
    }

    protected function createMemberGroups(string $groupTitle): array
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $extranetConfig = $config->getSgExtranet();

        if ($extranetConfig->getSgMemberGroupMembers() !== null) {
            $objUserGroup = MemberGroupModel::findOneById($extranetConfig->getSgMemberGroupMembers()) ?? new MemberGroupModel();
        } else {
            $objUserGroup = new MemberGroupModel();
        }

        $objUserGroup->tstamp = time();
        $objUserGroup->name = $groupTitle;
        $objUserGroup->save();

        $this->setExtranetConfigKey('setSgMemberGroupMembers', (int) $objUserGroup->id);

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

        $this->setExtranetConfigKey('setSgNotificationChangeData', (int) $nc->id);

        return $nc;
    }

    protected function createNotificationPassword(CoreConfig $config, ExtranetConfig $extranetConfig): NotificationModel
    {
        $nc = NotificationModel::findOneById($extranetConfig->getSgNotificationPassword()) ?? new NotificationModel();
        $nc->tstamp = time();
        $nc->title = $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.notificationPasswordTitle', [], 'contao_default');
        $nc->type = 'member_password';
        $nc->save();

        $this->setExtranetConfigKey('setSgNotificationPassword', (int) $nc->id);

        return $nc;
    }

    protected function createNotificationSubscription(CoreConfig $config, ExtranetConfig $extranetConfig): ?NotificationModel
    {
        $nc = NotificationModel::findOneById($extranetConfig->getSgNotificationSubscription()) ?? new NotificationModel();

        if (! $extranetConfig->getSgCanSubscribe()) {
            if ($nc !== null) {
                $nc->delete();
            }

            $this->setExtranetConfigKey('setSgNotificationSubscription', null);

            return null;
        }

        $nc->tstamp = time();
        $nc->title = $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.notificationSubscriptionTitle', [], 'contao_default');
        $nc->type = 'member_password';
        $nc->save();

        $this->setExtranetConfigKey('setSgNotificationSubscription', (int) $nc->id);

        return $nc;
    }

    protected function createNotifications(): array
    {
        /** @var CoreConfig $config */
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

        $this->setExtranetConfigKey('setSgNotificationChangeDataMessage', (int) $nm->id);

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

        $this->setExtranetConfigKey('setSgNotificationPasswordMessage', (int) $nm->id);

        return $nm;
    }

    protected function createNotificationsMessagesSubscription(CoreConfig $config, ExtranetConfig $extranetConfig, ?NotificationModel $notification, GatewayModel $gateway): ?NotificationMessageModel
    {
        $nm = NotificationMessageModel::findOneById($extranetConfig->getSgNotificationSubscriptionMessage()) ?? new NotificationMessageModel();

        if (! $extranetConfig->getSgCanSubscribe()) {
            if ($nm !== null) {
                $nm->delete();
            }

            $this->setExtranetConfigKey('setSgNotificationSubscriptionMessage', null);

            return null;
        }

        $nm->pid = $notification->id;
        $nm->gateway = $config->getSgNotificationGatewayEmail();
        $nm->gateway_type = 'email';
        $nm->tstamp = time();
        $nm->title = $this->translator->trans('WEMSG.EXTRANET.INSTALL_GENERAL.notificationGatewayMessageSubscriptionTitle', [], 'contao_default');
        $nm->published = 1;
        $nm->save();

        $this->setExtranetConfigKey('setSgNotificationSubscriptionMessage', (int) $nm->id);

        return $nm;
    }

    protected function createNotificationsMessages(array $notifications): array
    {
        /** @var CoreConfig $config */
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
        $strText = Util::getLocalizedTemplateContent('{public_or_web}/bundles/wemsmartgear/examples/extranet/{lang}/change_data.html', $this->language, '{public_or_web}/bundles/wemsmartgear/examples/extranet/fr/change_data.html');

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

        $this->setExtranetConfigKey('setSgNotificationChangeDataMessageLanguage', (int) $nl->id);

        return $nl;
    }

    protected function createNotificationsMessagesLanguagesPassword(CoreConfig $config, ExtranetConfig $extranetConfig, NotificationMessageModel $gatewayMessage): NotificationLanguageModel
    {
        $strText = Util::getLocalizedTemplateContent('{public_or_web}/bundles/wemsmartgear/examples/extranet/{lang}/password.html', $this->language, '{public_or_web}/bundles/wemsmartgear/examples/extranet/fr/password.html');

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

        $this->setExtranetConfigKey('setSgNotificationPasswordMessageLanguage', (int) $nl->id);

        return $nl;
    }

    protected function createNotificationsMessagesLanguagesSubscription(CoreConfig $config, ExtranetConfig $extranetConfig, ?NotificationMessageModel $gatewayMessage): ?NotificationLanguageModel
    {
        $strText = Util::getLocalizedTemplateContent('{public_or_web}/bundles/wemsmartgear/examples/extranet/{lang}/subscription.html', $this->language, '{public_or_web}/bundles/wemsmartgear/examples/extranet/fr/subscription.html');

        $nl = NotificationLanguageModel::findOneById($extranetConfig->getSgNotificationSubscriptionMessageLanguage()) ?? new NotificationLanguageModel();

        if (! $extranetConfig->getSgCanSubscribe()) {
            if ($nl !== null) {
                $nl->delete();
            }

            $this->setExtranetConfigKey('setSgNotificationSubscriptionMessageLanguage', null);

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

        $this->setExtranetConfigKey('setSgNotificationSubscriptionMessageLanguage', (int) $nl->id);

        return $nl;
    }

    protected function createNotificationsMessagesLanguages(array $notificationMessages): array
    {
        /** @var CoreConfig $config */
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
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
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
                $pages['subscribe'] === null
                ? $pages['subscribe']
                : (int) $pages['subscribe']->id,
            )
            ->setSgPageSubscribeConfirm(
                $pages['subscribeConfirm'] === null
                ? $pages['subscribeConfirm']
                : (int) $pages['subscribeConfirm']->id,
            )
            ->setSgPageSubscribeValidate(
                $pages['subscribeValidate'] === null
                ? $pages['subscribeValidate']
                : (int) $pages['subscribeValidate']->id,
            )
            ->setSgPageUnsubscribeConfirm(
                $pages['unsubscribeConfirm'] === null
                ? $pages['unsubscribeConfirm']
                : (int) $pages['unsubscribeConfirm']->id,
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
                $articles['subscribe'] === null
                ? $articles['subscribe']
                : (int) $articles['subscribe']->id,
            )
            ->setSgArticleSubscribeConfirm(
                $articles['subscribeConfirm'] === null
                ? $articles['subscribeConfirm']
                : (int) $articles['subscribeConfirm']->id,
            )
            ->setSgArticleSubscribeValidate(
                $articles['subscribeValidate'] === null
                ? $articles['subscribeValidate']
                : (int) $articles['subscribeValidate']->id,
            )
            ->setSgArticleUnsubscribeConfirm(
                $articles['unsubscribeConfirm'] === null
                ? $articles['unsubscribeConfirm']
                : (int) $articles['unsubscribeConfirm']->id,
            )
            ->setSgNotificationChangeData((int) $notifications['changeData']->id)
            ->setSgNotificationPassword((int) $notifications['password']->id)
            ->setSgNotificationSubscription(
                $notifications['subscription'] === null
                ? $notifications['subscription']
                : (int) $notifications['subscription']->id,
            )
            ->setSgNotificationChangeDataMessage((int) $notificationMessages['changeData']->id)
            ->setSgNotificationPasswordMessage((int) $notificationMessages['password']->id)
            ->setSgNotificationSubscriptionMessage(
                $notificationMessages['subscription'] === null
                ? $notificationMessages['subscription']
                : (int) $notificationMessages['subscription']->id,
            )
            ->setSgNotificationChangeDataMessageLanguage((int) $notificationMessagesLanguages['changeData']->id)
            ->setSgNotificationPasswordMessageLanguage((int) $notificationMessagesLanguages['password']->id)
            ->setSgNotificationSubscriptionMessageLanguage(
                $notificationMessagesLanguages['subscription'] === null
                ? $notificationMessagesLanguages['subscription']
                : (int) $notificationMessagesLanguages['subscription']->id,
            )
            ->setSgModuleLogin((int) $modules['login']->id)
            ->setSgModuleLogout((int) $modules['logout']->id)
            ->setSgModuleData((int) $modules['data']->id)
            ->setSgModulePassword((int) $modules['password']->id)
            ->setSgModuleNav((int) $modules['nav']->id)
            ->setSgModuleSubscribe(
                $modules['subscribe'] === null
                ? $modules['subscribe']
                : (int) $modules['subscribe']->id,
            )
            ->setSgModuleCloseAccount(
                $modules['closeAccount'] === null
                ? $modules['closeAccount']
                : (int) $modules['closeAccount']->id,
            )
            ->setSgContentArticleExtranetHeadline((int) $contents['extranet']['headline']->id)
            ->setSgContentArticleExtranetModuleLoginGuests((int) $contents['extranet']['moduleLoginGuests']->id)
            ->setSgContentArticleExtranetGridStartA((int) $contents['extranet']['gridStartA']->id)
            ->setSgContentArticleExtranetGridStartB((int) $contents['extranet']['gridStartB']->id)
            ->setSgContentArticleExtranetModuleLoginLogged((int) $contents['extranet']['moduleLoginLogged']->id)
            ->setSgContentArticleExtranetModuleNav((int) $contents['extranet']['moduleNav']->id)
            ->setSgContentArticleExtranetGridStopA((int) $contents['extranet']['gridStopA']->id)
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
                $contents['data']['headlineCloseAccount'] === null
                ? $contents['data']['headlineCloseAccount']
                : (int) $contents['data']['headlineCloseAccount']->id,
            )
            ->setSgContentArticleDataTextCloseAccount(
                $contents['data']['textCloseAccount'] === null
                ? $contents['data']['textCloseAccount']
                : (int) $contents['data']['textCloseAccount']->id,
            )
            ->setSgContentArticleDataModuleCloseAccount(
                $contents['data']['moduleCloseAccount'] === null
                ? $contents['data']['moduleCloseAccount']
                : (int) $contents['data']['moduleCloseAccount']->id,
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
                $contents['subscribe']['headline'] === null
                ? $contents['subscribe']['headline']
                : (int) $contents['subscribe']['headline']->id,
            )
            ->setSgContentArticleSubscribeModuleSubscribe(
                $contents['subscribe']['moduleSubscribe'] === null
                ? $contents['subscribe']['moduleSubscribe']
                : (int) $contents['subscribe']['moduleSubscribe']->id,
            )
            ->setSgContentArticleSubscribeConfirmHeadline(
                $contents['subscribeConfirm']['headline'] === null
                ? $contents['subscribeConfirm']['headline']
                : (int) $contents['subscribeConfirm']['headline']->id,
            )
            ->setSgContentArticleSubscribeConfirmText(
                $contents['subscribeConfirm']['text'] === null
                ? $contents['subscribeConfirm']['text']
                : (int) $contents['subscribeConfirm']['text']->id,
            )
            ->setSgContentArticleSubscribeValidateHeadline(
                $contents['subscribeValidate']['headline'] === null
                ? $contents['subscribeValidate']['headline']
                : (int) $contents['subscribeValidate']['headline']->id,
            )
            ->setSgContentArticleSubscribeValidateText(
                $contents['subscribeValidate']['text'] === null
                ? $contents['subscribeValidate']['text']
                : (int) $contents['subscribeValidate']['text']->id,
            )
            ->setSgContentArticleSubscribeValidateModuleLoginGuests(
                $contents['subscribeValidate']['moduleLoginGuests'] === null
                ? $contents['subscribeValidate']['moduleLoginGuests']
                : (int) $contents['subscribeValidate']['moduleLoginGuests']->id,
            )
            ->setSgContentArticleUnsubscribeHeadline(
                $contents['unsubscribe']['headline'] === null
                ? $contents['unsubscribe']['headline']
                : (int) $contents['unsubscribe']['headline']->id,
            )
            ->setSgContentArticleUnsubscribeText(
                $contents['unsubscribe']['text'] === null
                ? $contents['unsubscribe']['text']
                : (int) $contents['unsubscribe']['text']->id,
            )
            ->setSgContentArticleUnsubscribeHyperlink(
                $contents['unsubscribe']['hyperlink'] === null
                ? $contents['unsubscribe']['hyperlink']
                : (int) $contents['unsubscribe']['hyperlink']->id,
            )
        ;

        $config->setSgExtranet($extranetConfig);

        $this->configurationManager->save($config);
    }

    protected function updateUserGroup(UserGroupModel $objUserGroup, ExtranetConfig $extranetConfig, array $modules): void
    {
        $objFolder = FilesModel::findByPath($extranetConfig->getSgExtranetFolder());
        if (! $objFolder) {
            throw new Exception('Unable to find the "' . $extranetConfig->getSgExtranetFolder() . '" folder');
        }

        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);
        $userGroupManipulator
            ->addAllowedModules(array_values($modules))
            ->addAllowedModules(['member', 'mgroup'])
            ->addAllowedFilemounts([$objFolder->uuid])
            ->addAllowedPagemounts($extranetConfig->getContaoPagesIds())
            ->addAllowedModules(Module::getTypesByIds($extranetConfig->getContaoModulesIds()))
        ;

        $objUserGroup = $userGroupManipulator->getUserGroup();
        // $objUserGroup->extranetp = serialize(['create', 'delete']);
        $objUserGroup->save();
    }

    private function setExtranetConfigKey(string $key, ?int $value): void
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $extranetConfig = $config->getSgExtranet();

        $extranetConfig->{$key}($value);

        $config->setSgExtranet($extranetConfig);

        $this->configurationManager->save($config);
    }
}
