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

namespace WEM\SmartgearBundle\Backend\Component\Blog\ConfigurationStep;

use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\Input;
use Contao\ModuleModel;
use Contao\NewsArchiveModel;
use Contao\PageModel;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Backend\ConfigurationStep;
use WEM\SmartgearBundle\Classes\Command\Util as CommandUtil;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Config\Component\Blog\Blog as BlogConfig;
use WEM\SmartgearBundle\Config\Component\Blog\Preset as BlogPresetConfig;
use WEM\SmartgearBundle\Config\Core as CoreConfig;

class General extends ConfigurationStep
{
    /** @var TranslatorInterface */
    protected $translator;
    /** @var ConfigurationManager */
    protected $configurationManager;
    /** @var CommandUtil */
    protected $commandUtil;

    protected $strTemplate = 'be_wem_sg_install_block_configuration_step_blog_general';

    public function __construct(
        string $module,
        string $type,
        TranslatorInterface $translator,
        ConfigurationManager $configurationManager,
        CommandUtil $commandUtil
    ) {
        parent::__construct($module, $type);
        $this->configurationManager = $configurationManager;
        $this->commandUtil = $commandUtil;
        $this->translator = $translator;
        $this->title = $this->translator->trans('WEMSG.BLOG.INSTALL.title', [], 'contao_default');
        /** @var BlogConfig */
        $config = $this->configurationManager->load()->getSgBlog();

        $sgNewsConfigOptions = [];

        foreach ($config->getSgPresets() as $index => $presetConfig) {
            $sgNewsConfigOptions[] = ['value' => $index, 'label' => $presetConfig->getSgNewsArchiveTitle()];
        }

        $this->addSelectField('newsConfig', $this->translator->trans('WEMSG.BLOG.INSTALL.newsConfig', [], 'contao_default'), $sgNewsConfigOptions, $config->getSgCurrentPresetIndex(), true);
        $this->addTextField('new_config', $this->translator->trans('WEMSG.BLOG.INSTALL.newPresetTitle', [], 'contao_default'), '', false);

        if (\count($config->getSgPresets()) > 0) {
            // $sgNewsArchiveConfig = $config->getSgPresets()[0];
            $sgNewsArchiveConfig = $config->getPresetByIndex($config->getSgCurrentPresetIndex());
            $this->addTextField('newsArchiveTitle', $this->translator->trans('WEMSG.BLOG.INSTALL.newsArchiveTitle', [], 'contao_default'), $sgNewsArchiveConfig->getSgNewsArchiveTitle(), true);

            $this->addTextField('newsListPerPage', $this->translator->trans('WEMSG.BLOG.INSTALL.newsListPerPage', [], 'contao_default'), (string) $sgNewsArchiveConfig->getSgNewsListPerPage(), false, '', 'number');

            $this->addTextField('pageTitle', $this->translator->trans('WEMSG.BLOG.INSTALL.pageTitle', [], 'contao_default'), $sgNewsArchiveConfig->getSgPageTitle(), true);

            $this->addSimpleFileTree('newsFolder', $this->translator->trans('WEMSG.BLOG.INSTALL.newsFolder', [], 'contao_default'), $sgNewsArchiveConfig->getSgNewsFolder(), true, false, '', $this->translator->trans('WEMSG.BLOG.INSTALL.newsFolderHelp', [], 'contao_default'), ['multiple' => false, 'isGallery' => false,
                'isDownloads' => false,
                'files' => false, ]);
        }
        $this->addCheckboxField('expertMode', $this->translator->trans('WEMSG.BLOG.INSTALL.expertMode', [], 'contao_default'), '1', BlogConfig::MODE_EXPERT === $config->getSgMode());
    }

    public function isStepValid(): bool
    {
        // check if the step is correct
        if (empty(Input::post('newsConfig'))) {
            throw new Exception($this->translator->trans('WEMSG.BLOG.INSTALL.newsConfigMissing', [], 'contao_default'));
        }
        if (empty(Input::post('newsArchiveTitle'))) {
            throw new Exception($this->translator->trans('WEMSG.BLOG.INSTALL.newsArchiveTitleMissing', [], 'contao_default'));
        }
        if (empty(Input::post('newsListPerPage'))) {
            throw new Exception($this->translator->trans('WEMSG.BLOG.INSTALL.newsListPerPageMissing', [], 'contao_default'));
        }
        if (0 > (int) Input::post('newsListPerPage')) {
            throw new Exception($this->translator->trans('WEMSG.BLOG.INSTALL.newsListPerPageTooLow', [], 'contao_default'));
        }
        if (empty(Input::post('pageTitle'))) {
            throw new Exception($this->translator->trans('WEMSG.BLOG.INSTALL.pageTitleMissing', [], 'contao_default'));
        }
        if (empty(Input::post('newsFolder'))) {
            throw new Exception($this->translator->trans('WEMSG.BLOG.INSTALL.newsFolderMissing', [], 'contao_default'));
        }

        return true;
    }

    public function do(): void
    {
        // do what is meant to be done in this step
        $this->updateModuleConfiguration();

        $page = $this->createPage();
        $article = $this->createArticle($page);
        $newsArchive = $this->createNewsArchive($page);
        $modules = $this->createModules($page, $newsArchive);
        $this->fillArticle($page, $article, $modules);

        $this->updateModuleConfigurationAfterGenerations($page, $newsArchive, $modules);
        $this->commandUtil->executeCmdPHP('cache:clear');
    }

    public function presetAdd()
    {
        if (empty(Input::post('new_config'))) {
            throw new \InvalidArgumentException($this->translator->trans('WEMSG.BLOG.INSTALL.fieldNewsPresetNameEmpty', [], 'contao_default'));
        }

        $newsConfigTitle = Input::post('new_config');

        if (!preg_match('/^([A-Za-z0-9-_]+)$/', $newsConfigTitle)) {
            throw new \InvalidArgumentException($this->translator->trans('WEMSG.BLOG.INSTALL.fieldNewsPresetNameIncorrectFormat', [], 'contao_default'));
        }

        // // here we add a new tl_news_archive
        // $objNewsArchive = new \Contao\NewsArchiveModel();
        // $objNewsArchive->title = $newsConfigTitle;
        // $objNewsArchive->save();

        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var BlogPresetConfig */
        $newsArchiveConfig = new BlogPresetConfig();
        $newsArchiveConfig->setSgNewsArchiveTitle($newsConfigTitle);
        $config->getSgBlog()->addOrUpdatePreset($newsArchiveConfig);

        return $this->configurationManager->save($config);
    }

    public function presetGet(int $id)
    {
        /* @var BlogPresetConfig */
        return $this->configurationManager->load()->getSgBlog()->getPresetByIndex($id);
    }

    protected function updateModuleConfiguration(): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var BlogConfig */
        $blogConfig = $config->getSgBlog();

        $blogConfig
            ->setSgMode(Input::post('expertMode') ? BlogConfig::MODE_EXPERT : BlogConfig::MODE_SIMPLE)
            ->setSgCurrentPresetIndex((int) Input::post('newsConfig'))
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

    protected function createPage(): PageModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var BlogConfig */
        $blogConfig = $config->getSgBlog();
        $presetConfig = $blogConfig->getCurrentPreset();

        $rootPage = PageModel::findById($config->getSgRootPage());

        $page = PageModel::findById($blogConfig->getSgPage());

        return Util::createPage($presetConfig->getSgPageTitle(), 0, array_merge([
            'pid' => $rootPage->id,
            'sorting' => ((int) $rootPage->sorting) + 128,
            'layout' => $rootPage->layout,
            'title' => $presetConfig->getSgPageTitle(),
            'robots' => 'index,follow',
            'type' => 'regular',
            'description' => $this->translator->trans('WEMSG.BLOG.INSTALL.pageDescription', [$presetConfig->getSgPageTitle(), $config->getSgWebsiteTitle()], 'contao_default'),
        ], null !== $page ? ['id' => $page->id] : []));
    }

    protected function createArticle(PageModel $page): ArticleModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var BlogConfig */
        $blogConfig = $config->getSgBlog();
        $presetConfig = $blogConfig->getCurrentPreset();

        $article = ArticleModel::findOneByPid($page->id) ?? new ArticleModel();
        $article->pid = $page->id;
        $article->sorting = 128;
        $article->title = $presetConfig->getSgPageTitle();
        $article->alias = $page->alias;
        $article->author = 1;
        $article->inColumn = 'main';

        $article->save();

        return $article;
    }

    protected function createNewsArchive(PageModel $page): NewsArchiveModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var BlogConfig */
        $blogConfig = $config->getSgBlog();
        $presetConfig = $blogConfig->getCurrentPreset();

        $newsArchive = NewsArchiveModel::findById($blogConfig->getSgNewsArchive()) ?? new NewsArchiveModel();
        $newsArchive->title = $presetConfig->getSgNewsArchiveTitle();
        $newsArchive->jumpTo = $page->id;
        $newsArchive->save();

        return $newsArchive;
    }

    protected function createModules(PageModel $page, NewsArchiveModel $newsArchive): array
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var BlogConfig */
        $blogConfig = $config->getSgBlog();
        $presetConfig = $blogConfig->getCurrentPreset();

        $moduleReader = ModuleModel::findById($blogConfig->getSgModuleReader()) ?? new ModuleModel();
        $moduleList = ModuleModel::findById($blogConfig->getSgModuleList()) ?? new ModuleModel();

        $moduleReader->name = $page->title.' - Reader';
        $moduleReader->type = 'newsreader';
        $moduleReader->news_archives = serialize([0 => $newsArchive->id]);
        $moduleReader->news_metaFields = serialize([0 => 'date']);
        $moduleReader->imgSize = 'a:3:{i:0;s:4:"1200";i:1;s:0:"";i:2;s:12:"proportional";}';
        $moduleReader->wem_sg_display_share_buttons = '1';
        $moduleReader->save();

        $moduleList->name = $page->title.' - List';
        $moduleList->type = 'newslist';
        $moduleList->news_archives = serialize([0 => $newsArchive->id]);
        $moduleList->numberOfItems = 0;
        $moduleList->news_readerModule = $moduleReader->id;
        $moduleList->news_order = 'order_date_desc';
        $moduleList->perPage = $presetConfig->getSgNewsListPerPage();
        $moduleList->imgSize = 'a:3:{i:0;s:3:"480";i:1;s:0:"";i:2;s:12:"proportional";}';

        $moduleList->wem_sg_number_of_characters = 200;
        $moduleList->save();

        return ['reader' => $moduleReader, 'list' => $moduleList];
    }

    protected function fillArticle(PageModel $page, ArticleModel $article, array $modules): void
    {
        $headline = ContentModel::findOneBy(['pid = ?', 'ptable = ?', 'type = ?'], [$article->id, 'tl_article', 'headline']) ?? new ContentModel();
        // $reader = ContentModel::findOneBy(['pid' => $article->id, 'ptable' => 'tl_article', 'type' => 'module', 'module' => $modules['reader']->id]) ?? new ContentModel();
        $reader = ContentModel::findOneBy(['pid = ?', 'ptable = ?', 'type = ?', 'module = ?'], [$article->id, 'tl_article', 'module', $modules['reader']->id]) ?? new ContentModel();
        // $list = ContentModel::findOneBy(['pid' => $article->id, 'ptable' => 'tl_article', 'type' => 'module', 'module' => $modules['list']->id]) ?? new ContentModel();
        $list = ContentModel::findOneBy(['pid = ?', 'ptable = ?', 'type = ?', 'module = ?'], [$article->id, 'tl_article', 'module', $modules['list']->id]) ?? new ContentModel();

        $headline->type = 'headline';
        $headline->pid = $article->id;
        $headline->ptable = 'tl_article';
        $headline->headline = serialize(['unit' => 'h1', 'value' => $page->title]);
        $headline->cssID = 'sep-bottom';
        $headline->save();

        $reader->type = 'module';
        $reader->pid = $article->id;
        $reader->ptable = 'tl_article';
        $reader->module = $modules['reader']->id;
        $reader->save();

        $list->type = 'module';
        $list->pid = $article->id;
        $list->ptable = 'tl_article';
        $list->module = $modules['list']->id;
        $list->save();
    }

    protected function updateModuleConfigurationAfterGenerations(PageModel $page, NewsArchiveModel $newsArchive, array $modules): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var BlogConfig */
        $blogConfig = $config->getSgBlog();

        $blogConfig
            ->setSgPage((int) $page->id)
            ->setSgNewsArchive((int) $newsArchive->id)
            ->setSgModuleReader((int) $modules['reader']->id)
            ->setSgModuleList((int) $modules['list']->id)
        ;

        $config->setSgBlog($blogConfig);

        $this->configurationManager->save($config);
    }
}
