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
use Contao\FilesModel;
use Contao\Input;
use Contao\MemberGroupModel;
use Contao\MemberModel;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\UserGroupModel;
use Exception;
use NotificationCenter\Model\Language as NotificationLanguageModel;
use NotificationCenter\Model\Message as NotificationMessageModel;
use NotificationCenter\Model\Notification as NotificationModel;
use Symfony\Contracts\Translation\TranslatorInterface;
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

    public function __construct(
        string $module,
        string $type,
        TranslatorInterface $translator,
        ConfigurationManager $configurationManager,
        CommandUtil $commandUtil
    ) {
        parent::__construct($module, $type);
        $this->translator = $translator;
        $this->configurationManager = $configurationManager;
        $this->commandUtil = $commandUtil;

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
        $modules = $this->createModules($pages);
        $contents = $this->createContents($pages, $articles, $modules);

        $notifications = $this->createNotificationGatewayNotifications();
        $notificationGatewayMessages = $this->createNotificationGatewayMessages($notifications);
        $notificationGatewayMessagesLanguages = $this->createNotificationGatewayMessagesLanguages($notificationGatewayMessages);

        $this->updateModuleConfigurationAfterGenerations($pages, $articles, $modules, $contents, $members, $memberGroups, $notifications, $notificationGatewayMessages, $notificationGatewayMessagesLanguages);
        $this->updateUserGroups($modules);

        $this->commandUtil->executeCmdPHP('cache:clear');
        $this->commandUtil->executeCmdPHP('contao:symlinks');
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
        $page = PageModel::findById($extranetConfig->getSgPagePasswordConfirm());

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
        $pages['error403'] = $this->createPageError401($rootPage, $config, $extranetConfig, (int) $pages['error401']->sorting);
        $pages['content'] = $this->createPageContent($pages['extranet'], $config, $extranetConfig, $groups);
        $pages['data'] = $this->createPageData($pages['extranet'], $config, $extranetConfig, $groups);
        $pages['dataConfirm'] = $this->createPageDataConfirm($pages['data'], $config, $extranetConfig, $groups);
        $pages['password'] = $this->createPagePassword($pages['extranet'], $config, $extranetConfig);
        $pages['passwordConfirm'] = $this->createPagePasswordConfirm($pages['password'], $config, $extranetConfig);
        $pages['passwordValidate'] = $this->createPagePasswordValidate($pages['password'], $config, $extranetConfig);
        $pages['logout'] = $this->createPageLogout($pages['extranet'], $config, $extranetConfig);

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

    protected function createArticles(array $pages): array
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var ExtranetConfig */
        $extranetConfig = $config->getSgExtranet();

        return [
            'extranet' => $this->createArticleExtranet($pages['extranet'], $extranetConfig),
            'error401' => $this->createArticleError401($pages['error401'], $extranetConfig),
            'error403' => $this->createArticleError401($pages['error403'], $extranetConfig),
            'content' => $this->createArticleContent($pages['content'], $extranetConfig),
            'data' => $this->createArticleData($pages['data'], $extranetConfig),
            'dataConfirm' => $this->createArticleDataConfirm($pages['dataConfirm'], $extranetConfig),
            'password' => $this->createArticlePassword($pages['password'], $extranetConfig),
            'passwordConfirm' => $this->createArticlePasswordConfirm($pages['passwordConfirm'], $extranetConfig),
            'passwordValidate' => $this->createArticlePasswordValidate($pages['passwordValidate'], $extranetConfig),
            'logout' => $this->createArticleLogout($pages['logout'], $extranetConfig),
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
        $module->name = '';
        $module->pid = $config->getSgTheme();
        $module->type = 'extranetpage';
        $module->numberOfItems = 0;
        $module->imgSize = serialize([0 => '480', 1 => '0', 2 => \Contao\Image\ResizeConfiguration::MODE_PROPORTIONAL]);
        $module->tstamp = time();
        $module->save();

        return $module;
    }

    protected function createModules(array $pages): array
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var ExtranetConfig */
        $extranetConfig = $config->getSgExtranet();

        return ['login' => $this->createModuleLogin($config, $extranetConfig)];
    }

    protected function createContentsArticleExtranet(ExtranetConfig $config, PageModel $page, ArticleModel $article, array $modules): array
    {
        $moduleLogin = ContentModel::findById($extranetConfig->getSgContent());
        $moduleLogin = Util::createContent($article, array_merge([
            'type' => 'module',
            'pid' => $article->id,
            'ptable' => 'tl_article',
            'module' => $modules['login']->id,
        ], ['id' => null !== $moduleLogin ? $moduleLogin->id : null]));

        $article->save();

        return [
            'login' => $moduleLogin,
        ];
    }

    protected function createContents(array $pages, array $articles, array $modules): array
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        $extranetConfig = $config->getSgExtranet();

        return [
            'extranet' => $this->createContentsArticleExtranet($extranetConfig, $pages['extranet'], $articles['extranet'], $modules),
        ];
    }

    protected function createMembers(array $groups): array
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        $extranetConfig = $config->getSgExtranet();

        $objUser = null !== $extranetConfig->getSgMemberExample()
                    ? MemberModel::findOneById($extranetConfig->getSgMemberExample()) ?? new MemberModel()
                    : new MemberModel();
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

    protected function createNotificationGatewayNotificationChangeData(CoreConfig $config, ExtranetConfig $extranetConfig): NotificationModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FormContactConfig */
        $formContactConfig = $config->getSgFormContact();

        $nc = NotificationModel::findOneById($formContactConfig->getSgNotification()) ?? new NotificationModel();
        $nc->tstamp = time();
        $nc->title = $formTitle;
        $nc->type = 'core_form';
        $nc->save();

        return $nc;
    }

    protected function createNotificationGatewayNotifications(): array
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        $extranetConfig = $config->getSgExtranet();

        return [
            'changeData' => $this->createNotificationGatewayNotificationChangeData($config, $extranetConfig),
        ];
    }

    protected function createNotificationGatewayMessagesChangeData(CoreConfig $config, ExtranetConfig $extranetConfig): NotificationMessageModel
    {
        $nm = NotificationMessageModel::findOneById($formContactConfig->getSgNotificationMessageUser()) ?? new NotificationMessageModel();
        $nm->pid = $gateway->id;
        $nm->gateway = $config->getSgNotificationGatewayEmail();
        $nm->gateway_type = 'email';
        $nm->tstamp = time();
        $nm->title = $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.titleNotificationGatewayMessageUser', [], 'contao_default');
        $nm->published = 1;
        $nm->save();

        return $nm;
    }

    protected function createNotificationGatewayMessages(array $notifications): array
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        $extranetConfig = $config->getSgExtranet();

        return [
            'changeData' => $this->createNotificationGatewayMessagesChangeData($config, $extranetConfig),
        ];
    }

    protected function createNotificationGatewayMessagesLanguagesChangeData(CoreConfig $config, ExtranetConfig $extranetConfig, NotificationMessageModel $gatewayMessage): NotificationModel
    {
        $strText = file_get_contents(sprintf('%s/public/bundles/wemsmartgear/examples/formContact/%s/user_form.html', TL_ROOT, $this->language));

        $nl = NotificationLanguageModel::findOneById($extranetConfig->getSgNotificationMessageUserLanguage()) ?? new NotificationLanguageModel();
        $nl->pid = $gatewayMessage->id;
        $nl->tstamp = time();
        $nl->language = 'fr';
        $nl->fallback = 1;
        $nl->recipients = '##form_email##';
        $nl->gateway_type = 'email';
        $nl->email_sender_name = $config->getSgWebsiteTitle();
        $nl->email_sender_address = $config->getSgOwnerEmail();
        $nl->email_subject = $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.subjectNotificationGatewayMessageLanguageUser', [$config->getSgWebsiteTitle(), $formTitle], 'contao_default');
        $nl->email_mode = 'textAndHtml';
        $nl->email_text = $this->htmlDecoder->htmlToPlainText($strText, false);
        $nl->email_html = $strText;
        $nl->save();

        return $nl;
    }

    protected function createNotificationGatewayMessagesLanguages(array $notificationMessages): array
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        $extranetConfig = $config->getSgExtranet();

        return [
            'changeData' => $this->createNotificationGatewayMessagesLanguagesChangeData($config, $extranetConfig, $notificationMessages['changeData']),
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

        $this->updateUserGroup(UserGroupModel::findOneById($config->getSgUserGroupWebmasters()), $extranetConfig, $modules);
        $this->updateUserGroup(UserGroupModel::findOneById($config->getSgUserGroupAdministrators()), $extranetConfig, $modules);
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
            ->addAllowedFilemounts([$objFolder->uuid])
        ;

        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->extranetp = serialize(['create', 'delete']);
        $objUserGroup->save();
    }
}
