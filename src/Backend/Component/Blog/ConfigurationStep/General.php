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

namespace WEM\SmartgearBundle\Backend\Component\Blog\ConfigurationStep;

use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\FilesModel;
use Contao\Folder;
use Contao\Input;
use Contao\ModuleModel;
use Contao\NewsArchiveModel;
use Contao\PageModel;
use Contao\UserGroupModel;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Backend\ConfigurationStep;
use WEM\SmartgearBundle\Classes\Command\Util as CommandUtil;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\UserGroupModelUtil;
use WEM\SmartgearBundle\Classes\Utils\ArticleUtil;
use WEM\SmartgearBundle\Classes\Utils\ContentUtil;
use WEM\SmartgearBundle\Classes\Utils\ModuleUtil;
use WEM\SmartgearBundle\Classes\Utils\NewsArchiveUtil;
use WEM\SmartgearBundle\Classes\Utils\PageUtil;
use WEM\SmartgearBundle\Config\Component\Blog\Blog as BlogConfig;
use WEM\SmartgearBundle\Config\Component\Blog\Preset as BlogPresetConfig;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Model\Module;
use WEM\SmartgearBundle\Security\SmartgearPermissions;

class General extends ConfigurationStep
{

    protected string $strTemplate = 'be_wem_sg_install_block_configuration_step_blog_general';

    public function __construct(
        string                         $module,
        string                         $type,
        protected TranslatorInterface  $translator,
        protected ConfigurationManager $configurationManager,
        protected CommandUtil          $commandUtil
    ) {
        parent::__construct($module, $type);

        $this->title = $this->translator->trans('WEMSG.BLOG.INSTALL_GENERAL.title', [], 'contao_default');
        /** @var BlogConfig $config */
        $config = $this->configurationManager->load()->getSgBlog();

        $sgNewsConfigOptions = [];

        foreach ($config->getSgPresets() as $index => $presetConfig) {
            $sgNewsConfigOptions[] = ['value' => $index, 'label' => $presetConfig->getSgNewsArchiveTitle()];
        }

        $this->addSelectField('newsConfig', $this->translator->trans('WEMSG.BLOG.INSTALL_GENERAL.newsConfig', [], 'contao_default'), $sgNewsConfigOptions, $config->getSgCurrentPresetIndex(), true);
        $this->addTextField('new_config', $this->translator->trans('WEMSG.BLOG.INSTALL_GENERAL.newPresetTitle', [], 'contao_default'), '', false);

        $sgNewsArchiveConfig = \count($config->getSgPresets()) > 0 ? $config->getCurrentPreset() : null;

        $this->addTextField('newsArchiveTitle', $this->translator->trans('WEMSG.BLOG.INSTALL_GENERAL.newsArchiveTitle', [], 'contao_default'), null === $sgNewsArchiveConfig ? null : $sgNewsArchiveConfig->getSgNewsArchiveTitle(), true);

        $this->addTextField('newsListPerPage', $this->translator->trans('WEMSG.BLOG.INSTALL_GENERAL.newsListPerPage', [], 'contao_default'), null === $sgNewsArchiveConfig ? null : (string) $sgNewsArchiveConfig->getSgNewsListPerPage(), false, '', 'number');

        $this->addTextField('pageTitle', $this->translator->trans('WEMSG.BLOG.INSTALL_GENERAL.pageTitle', [], 'contao_default'), null === $sgNewsArchiveConfig ? null : $sgNewsArchiveConfig->getSgPageTitle(), true);

        $this->addSimpleFileTree('newsFolder', $this->translator->trans('WEMSG.BLOG.INSTALL_GENERAL.newsFolder', [], 'contao_default'), null === $sgNewsArchiveConfig ? null : $sgNewsArchiveConfig->getSgNewsFolder(), true, false, '', $this->translator->trans('WEMSG.BLOG.INSTALL_GENERAL.newsFolderHelp', [], 'contao_default'), ['multiple' => false, 'isGallery' => false,
            'isDownloads' => false,
            'files' => false, ]);

        $this->addCheckboxField('expertMode', $this->translator->trans('WEMSG.BLOG.INSTALL_GENERAL.expertMode', [], 'contao_default'), '1', BlogConfig::MODE_EXPERT === $config->getSgMode());
    }

    /**
     * @throws Exception
     */
    public function isStepValid(): bool
    {
        // check if the step is correct
        if (null === Input::post('newsConfig', null)) {
            throw new Exception($this->translator->trans('WEMSG.BLOG.INSTALL_GENERAL.newsConfigMissing', [], 'contao_default'));
        }

        if (null === Input::post('newsArchiveTitle', null)) {
            throw new Exception($this->translator->trans('WEMSG.BLOG.INSTALL_GENERAL.newsArchiveTitleMissing', [], 'contao_default'));
        }

        if (null === Input::post('newsListPerPage', null)) {
            throw new Exception($this->translator->trans('WEMSG.BLOG.INSTALL_GENERAL.newsListPerPageMissing', [], 'contao_default'));
        }

        if (0 > (int) Input::post('newsListPerPage')) {
            throw new Exception($this->translator->trans('WEMSG.BLOG.INSTALL_GENERAL.newsListPerPageTooLow', [], 'contao_default'));
        }

        if (null === Input::post('pageTitle', null)) {
            throw new Exception($this->translator->trans('WEMSG.BLOG.INSTALL_GENERAL.pageTitleMissing', [], 'contao_default'));
        }

        if (null === Input::post('newsFolder', null)) {
            throw new Exception($this->translator->trans('WEMSG.BLOG.INSTALL_GENERAL.newsFolderMissing', [], 'contao_default'));
        }

        return true;
    }

    public function do(): void
    {
        // do what is meant to be done in this step
        $this->updateModuleConfiguration();

        $this->createFolder();
        $page = $this->createPage();
        $article = $this->createArticle($page);
        $newsArchive = $this->createNewsArchive($page);
        $modules = $this->createModules($page, $newsArchive);
        $contents = $this->fillArticle($page, $article, $modules);

        $this->updateModuleConfigurationAfterGenerations($page, $article, $newsArchive, $modules, $contents);
        $this->updateUserGroups((bool) Input::post('expertMode', false));
        $this->commandUtil->executeCmdPHP('cache:clear');
        $this->commandUtil->executeCmdPHP('contao:symlinks');
    }

    public function presetAdd()
    {
        if (empty(Input::post('new_config'))) {
            throw new \InvalidArgumentException($this->translator->trans('WEMSG.BLOG.INSTALL_GENERAL.fieldNewsPresetNameEmpty', [], 'contao_default'));
        }

        $newsConfigTitle = Input::post('new_config');

        if (!preg_match('/^([A-Za-z0-9-_]+)$/', $newsConfigTitle)) {
            throw new \InvalidArgumentException($this->translator->trans('WEMSG.BLOG.INSTALL_GENERAL.fieldNewsPresetNameIncorrectFormat', [], 'contao_default'));
        }

        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $presetConfig = new BlogPresetConfig();
        $presetConfig->setSgNewsArchiveTitle($newsConfigTitle);
        $config->getSgBlog()->addOrUpdatePreset($presetConfig);

        $this->configurationManager->save($config);

        return $config->getSgBlog()->getPresetIndex($presetConfig);
    }

    public function presetGet(int $id): BlogPresetConfig
    {
        /* @var BlogPresetConfig */
        return $this->configurationManager->load()->getSgBlog()->getPresetByIndex($id);
    }

    public function updateUserGroups(bool $expertMode): void
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $blogConfig = $config->getSgBlog();

        $this->updateUserGroup(UserGroupModel::findOneById($config->getSgUserGroupRedactors()), $expertMode, $blogConfig);
        $this->updateUserGroup(UserGroupModel::findOneById($config->getSgUserGroupAdministrators()), true, $blogConfig);
    }

    protected function updateModuleConfiguration(): void
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $blogConfig = $config->getSgBlog();

        $blogConfig
            ->setSgMode(Input::post('expertMode') ? BlogConfig::MODE_EXPERT : BlogConfig::MODE_SIMPLE)
            ->setSgCurrentPresetIndex((int) Input::post('newsConfig'))
            ->setSgArchived(false)
            ->setSgArchivedMode(BlogConfig::ARCHIVE_MODE_EMPTY)
            ->setSgArchivedAt(0)
        ;

        $newsArchiveConfig = $blogConfig
            ->getPresetByIndex((int) Input::post('newsConfig'))
            ->setSgNewsArchiveTitle(Input::post('newsArchiveTitle'))
            ->setSgNewsListPerPage((int) Input::post('newsListPerPage'))
            ->setSgPageTitle(Input::post('pageTitle'))
            ->setSgNewsFolder(Input::post('newsFolder'))
        ;
        $blogConfig->addOrUpdatePreset($newsArchiveConfig, (int) Input::post('newsConfig'));
        $config->setSgBlog($blogConfig);

        $this->configurationManager->save($config);
    }

    /**
     * @throws Exception
     */
    protected function createFolder(): void
    {
        $objFolder = new Folder(Input::post('newsFolder', null));
        $objFolder->unprotect();
    }

    protected function createPage(): PageModel
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $blogConfig = $config->getSgBlog();
        $presetConfig = $blogConfig->getCurrentPreset();

        $rootPage = PageModel::findById($config->getSgPageRoot());

        $page = PageModel::findById($blogConfig->getSgPage());

        $page = PageUtil::createPageBlog($presetConfig->getSgPageTitle(), (int) $rootPage->id, array_merge([
            // $page = PageUtil::createPage($presetConfig->getSgPageTitle(), 0, array_merge([
            //     'pid' => $rootPage->id,
            //     'sorting' => PageUtil::getNextAvailablePageSortingByParentPage((int) $rootPage->id),
            //     'layout' => $rootPage->layout,
            // 'title' => $presetConfig->getSgPageTitle(),
            // 'robots' => 'index,follow',
            // 'type' => 'regular',
            // 'published' => 1,
            'description' => $this->translator->trans('WEMSG.BLOG.INSTALL_GENERAL.pageDescription', [$presetConfig->getSgPageTitle(), $config->getSgWebsiteTitle()], 'contao_default'),
        ], null !== $page ? ['id' => $page->id, 'sorting' => $page->sorting] : []));

        $this->setBlogConfigKey('setSgPage', (int) $page->id);

        return $page;
    }

    protected function createArticle(PageModel $page): ArticleModel
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $blogConfig = $config->getSgBlog();
        $presetConfig = $blogConfig->getCurrentPreset();

        $article = ArticleModel::findById($blogConfig->getSgArticle());

        $article = ArticleUtil::createArticle($page, array_merge([
            'title' => $presetConfig->getSgPageTitle(),
        ], null !== $article ? ['id' => $article->id] : []));

        $this->setBlogConfigKey('setSgArticle', (int) $article->id);

        return $article;
    }

    protected function createNewsArchive(PageModel $page): NewsArchiveModel
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $blogConfig = $config->getSgBlog();
        $presetConfig = $blogConfig->getCurrentPreset();

        $objUserGroupAdministrators = UserGroupModel::findOneById($config->getSgUserGroupAdministrators());
        $objUserGroupRedactors = UserGroupModel::findOneById($config->getSgUserGroupRedactors());

        $newsArchive = NewsArchiveUtil::createNewsArchive($presetConfig->getSgNewsArchiveTitle(), (int) $page->id, array_merge(
            $blogConfig->getSgNewsArchive() ? ['id' => $blogConfig->getSgNewsArchive()] : [],
            ['groups' => serialize([$objUserGroupAdministrators->id, $objUserGroupRedactors->id])]
        ));
        // $newsArchive = NewsArchiveModel::findById($blogConfig->getSgNewsArchive()) ?? new NewsArchiveModel();
        // $newsArchive->title = $presetConfig->getSgNewsArchiveTitle();
        // $newsArchive->jumpTo = $page->id;
        // $newsArchive->groups = serialize([$objUserGroupAdministrators->id, $objUserGroupRedactors->id]);
        // $newsArchive->tstamp = time();
        // $newsArchive->save();

        $this->setBlogConfigKey('setSgNewsArchive', (int) $newsArchive->id);

        return $newsArchive;
    }

    protected function createModules(PageModel $page, NewsArchiveModel $newsArchive): array
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $blogConfig = $config->getSgBlog();
        $blogConfig->getCurrentPreset();

        $moduleReader = new ModuleModel();
        $moduleList = new ModuleModel();

        if (null !== $blogConfig->getSgModuleReader()) {
            $moduleReaderOld = ModuleModel::findById($blogConfig->getSgModuleReader());
            if ($moduleReaderOld) {
                $moduleReaderOld->delete();
            }

            // $moduleReader->id = $blogConfig->getSgModuleReader();
        }

        $moduleReader = ModuleUtil::createModuleBlogReader((int) $config->getSgTheme(), (int) $newsArchive->id, array_merge([
            // $moduleReader = ModuleUtil::createModule((int) $config->getSgTheme(), array_merge([
            //     'name' => $page->title.' - Reader',
            //     'pid' => $config->getSgTheme(),
            //     'type' => 'newsreader',
            //     'news_archives' => serialize([$newsArchive->id]),
            //     'news_metaFields' => serialize(['date', 'author']),
            //     'imgSize' => serialize([0 => '1200', 1 => '0', 2 => \Contao\Image\ResizeConfiguration::MODE_PROPORTIONAL]),
            //     'news_template' => 'news_full',
            //     'wem_sg_display_share_buttons' => '1',
        ], null !== $blogConfig->getSgModuleReader() ? ['id' => $blogConfig->getSgModuleReader()] : []));

        $this->setBlogConfigKey('setSgModuleReader', (int) $moduleReader->id);

        if (null !== $blogConfig->getSgModuleList()) {
            $moduleListOld = ModuleModel::findById($blogConfig->getSgModuleList());
            if ($moduleListOld) {
                $moduleListOld->delete();
            }

            // $moduleList->id = $blogConfig->getSgModuleList();
        }

        $moduleList = ModuleUtil::createModuleBlogList((int) $config->getSgTheme(), (int) $newsArchive->id, (int) $moduleReader->id, array_merge([
            // $moduleList = ModuleUtil::createModule((int) $config->getSgTheme(), array_merge([
            //     'name' => $page->title.' - List',
            //     'headline' => serialize(['value' => $page->title, 'unit' => 'h1']),
            //     'type' => 'newslist',
            //     'news_archives' => serialize([$newsArchive->id]),
            //     'numberOfItems' => 0,
            //     'news_readerModule' => $moduleReader->id,
            //     'news_order' => 'order_date_desc',
            //     'perPage' => $presetConfig->getSgNewsListPerPage(),
            //     'imgSize' => serialize([0 => '480', 1 => '0', 2 => \Contao\Image\ResizeConfiguration::MODE_PROPORTIONAL]),
            //     'news_featured' => 'all_items',
            //     'news_template' => 'news_latest',
            //     'skipFirst' => 0,
            //     'news_metaFields' => serialize(['date', 'author']),
            //     'tstamp' => time(),
            //     'wem_sg_number_of_characters' => 200,
        ], null !== $blogConfig->getSgModuleList() ? ['id' => $blogConfig->getSgModuleList()] : []));

        $this->setBlogConfigKey('setSgModuleList', (int) $moduleList->id);

        return ['reader' => $moduleReader, 'list' => $moduleList];
    }

    protected function fillArticle(PageModel $page, ArticleModel $article, array $modules): array
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $blogConfig = $config->getSgBlog();

        $list = ContentModel::findById($blogConfig->getSgContentList());
        $list = ContentUtil::createContent($article, ['type' => 'module', 'pid' => $article->id, 'ptable' => 'tl_article', 'module' => $modules['list']->id, 'id' => null !== $list ? $list->id : null]);

        $article->save();

        $this->setBlogConfigKey('setSgContentList', (int) $list->id);

        return ['list' => $list];
    }

    protected function updateModuleConfigurationAfterGenerations(PageModel $page, ArticleModel $article, NewsArchiveModel $newsArchive, array $modules, array $contents): void
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $blogConfig = $config->getSgBlog();

        $blogConfig
            ->setSgPage((int) $page->id)
            ->setSgArticle((int) $article->id)
            ->setSgContentList((int) $contents['list']->id)
            ->setSgNewsArchive((int) $newsArchive->id)
            ->setSgModuleReader((int) $modules['reader']->id)
            ->setSgModuleList((int) $modules['list']->id)
        ;

        $config->setSgBlog($blogConfig);

        $this->configurationManager->save($config);
    }

    /**
     * @throws Exception
     */
    protected function updateUserGroup(UserGroupModel $objUserGroup, bool $expertMode, BlogConfig $blogConfig): void
    {
        $objFolder = FilesModel::findByPath($blogConfig->getCurrentPreset()->getSgNewsFolder());
        if (!$objFolder) {
            throw new Exception('Unable to find the "'.$blogConfig->getCurrentPreset()->getSgNewsFolder().'" folder');
        }

        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);
        $userGroupManipulator
            ->addAllowedModules(['news'])
            ->addAllowedNewsArchive([$blogConfig->getSgNewsArchive()])
            ->addAllowedFilemounts([$objFolder->uuid])
            ->addAllowedFieldsByTables(['tl_news'])
            ->addAllowedPagemounts($blogConfig->getContaoPagesIds())
            // ->addAllowedModules(Module::getTypesByIds($blogConfig->getContaoModulesIds()))
        ;
        if ($expertMode) {
            $userGroupManipulator
                ->addSmartgearPermissions([SmartgearPermissions::BLOG_EXPERT])
            ;
        }

        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->newp = serialize(['create', 'delete']);
        $objUserGroup->save();
    }

    private function setBlogConfigKey(string $key, int $value): void
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $blogConfig = $config->getSgBlog();

        $blogConfig->{$key}($value);

        $config->setSgBlog($blogConfig);

        $this->configurationManager->save($config);
    }
}
