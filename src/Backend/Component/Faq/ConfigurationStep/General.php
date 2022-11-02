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

namespace WEM\SmartgearBundle\Backend\Component\Faq\ConfigurationStep;

use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\FaqCategoryModel;
use Contao\FilesModel;
use Contao\Input;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\UserGroupModel;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Backend\ConfigurationStep;
use WEM\SmartgearBundle\Classes\Command\Util as CommandUtil;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\UserGroupModelUtil;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Config\Component\Faq\Faq as FaqConfig;
use WEM\SmartgearBundle\Model\Module;

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

        $this->title = $this->translator->trans('WEMSG.FAQ.INSTALL_GENERAL.title', [], 'contao_default');
        /** @var FaqConfig */
        $config = $this->configurationManager->load()->getSgFaq();

        $this->addTextField('faqTitle', $this->translator->trans('WEMSG.FAQ.INSTALL_GENERAL.faqTitle', [], 'contao_default'), $config->getSgFaqTitle(), true);

        $this->addTextField('pageTitle', $this->translator->trans('WEMSG.FAQ.INSTALL_GENERAL.pageTitle', [], 'contao_default'), $config->getSgPageTitle(), true);
    }

    public function isStepValid(): bool
    {
        // check if the step is correct
        if (null === Input::post('faqTitle', null)) {
            throw new Exception($this->translator->trans('WEMSG.FAQ.INSTALL_GENERAL.faqTitleMissing', [], 'contao_default'));
        }
        if (null === Input::post('pageTitle', null)) {
            throw new Exception($this->translator->trans('WEMSG.FAQ.INSTALL_GENERAL.pageTitleMissing', [], 'contao_default'));
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
        $faqCategory = $this->createFaqCategory($page);
        $modules = $this->createModules($page, $faqCategory);
        $contents = $this->fillArticle($page, $article, $modules);
        $this->updateModuleConfigurationAfterGenerations($page, $article, $faqCategory, $modules, $contents);
        $this->updateUserGroups();
        $this->commandUtil->executeCmdPHP('cache:clear');
        $this->commandUtil->executeCmdPHP('contao:symlinks');
    }

    public function updateUserGroups(): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FaqConfig */
        $faqConfig = $config->getSgFaq();

        $this->updateUserGroup(UserGroupModel::findOneById($config->getSgUserGroupRedactors()), $faqConfig);
        $this->updateUserGroup(UserGroupModel::findOneById($config->getSgUserGroupAdministrators()), $faqConfig);
    }

    protected function updateModuleConfiguration(): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FaqConfig */
        $faqConfig = $config->getSgFaq();

        $faqConfig
            ->setSgFaqTitle(Input::post('faqTitle'))
            ->setSgPageTitle(Input::post('pageTitle'))
            ->setSgFaqFolder(FaqConfig::DEFAULT_FOLDER_PATH)
            ->setSgArchived(false)
            ->setSgArchivedMode(FaqConfig::ARCHIVE_MODE_EMPTY)
            ->setSgArchivedAt(0)
        ;
        $config->setSgFaq($faqConfig);

        $this->configurationManager->save($config);
    }

    protected function createFolder(): void
    {
        $objFolder = new \Contao\Folder(FaqConfig::DEFAULT_FOLDER_PATH);
        $objFolder->unprotect();
    }

    protected function createPage(): PageModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FaqConfig */
        $faqConfig = $config->getSgFaq();

        $rootPage = PageModel::findById($config->getSgPageRoot());

        $page = PageModel::findById($faqConfig->getSgPage());

        return Util::createPage($faqConfig->getSgPageTitle(), 0, array_merge([
            'pid' => $rootPage->id,
            'sorting' => ((int) $rootPage->sorting) + 128,
            'layout' => $rootPage->layout,
            'title' => $faqConfig->getSgPageTitle(),
            'robots' => 'index,follow',
            'type' => 'regular',
            'published' => 1,
        ], null !== $page ? ['id' => $page->id] : []));
    }

    protected function createArticle(PageModel $page): ArticleModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FaqConfig */
        $faqConfig = $config->getSgFaq();

        $article = ArticleModel::findById($faqConfig->getSgArticle());

        return Util::createArticle($page, array_merge([
            'title' => $faqConfig->getSgPageTitle(),
        ], null !== $article ? ['id' => $article->id] : []));
    }

    protected function createFaqCategory(PageModel $page): FaqCategoryModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FaqConfig */
        $faqConfig = $config->getSgFaq();

        $objUserGroupAdministrators = UserGroupModel::findOneById($config->getSgUserGroupAdministrators());
        $objUserGroupRedactors = UserGroupModel::findOneById($config->getSgUserGroupRedactors());

        $faqCategory = FaqCategoryModel::findById($faqConfig->getSgFaqCategory()) ?? new FaqCategoryModel();
        $faqCategory->title = $faqConfig->getSgFaqTitle();
        $faqCategory->jumpTo = $page->id;
        $faqCategory->groups = serialize([$objUserGroupAdministrators->id, $objUserGroupRedactors->id]);
        $faqCategory->tstamp = time();
        $faqCategory->save();

        return $faqCategory;
    }

    protected function createModules(PageModel $page, FaqCategoryModel $faqCategory): array
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FaqConfig */
        $faqConfig = $config->getSgFaq();

        $moduleFaq = new ModuleModel();

        if (null !== $faqConfig->getSgModuleFaq()) {
            $moduleListOld = ModuleModel::findById($faqConfig->getSgModuleFaq());
            if ($moduleListOld) {
                $moduleListOld->delete();
            }
            $moduleFaq->id = $faqConfig->getSgModuleFaq();
        }
        $moduleFaq->name = $page->title.' - Reader';
        $moduleFaq->pid = $config->getSgTheme();
        $moduleFaq->type = 'faqpage';
        $moduleFaq->faq_categories = serialize([$faqCategory->id]);
        $moduleFaq->numberOfItems = 0;
        $moduleFaq->imgSize = serialize([0 => '480', 1 => '0', 2 => \Contao\Image\ResizeConfiguration::MODE_PROPORTIONAL]);
        $moduleFaq->tstamp = time();
        $moduleFaq->save();

        return ['faq' => $moduleFaq];
    }

    protected function fillArticle(PageModel $page, ArticleModel $article, array $modules): array
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        $faqConfig = $config->getSgFaq();

        $faq = ContentModel::findById($faqConfig->getSgContent());
        $faq = Util::createContent($article, array_merge([
            'type' => 'module',
            'pid' => $article->id,
            'ptable' => 'tl_article',
            'module' => $modules['faq']->id,
        ], ['id' => null !== $faq ? $faq->id : null]));

        $article->save();

        return ['faq' => $faq];
    }

    protected function updateModuleConfigurationAfterGenerations(PageModel $page, ArticleModel $article, FaqCategoryModel $faqCategory, array $modules, array $contents): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FaqConfig */
        $faqConfig = $config->getSgFaq();

        $faqConfig
            ->setSgPage((int) $page->id)
            ->setSgArticle((int) $article->id)
            ->setSgContent((int) $contents['faq']->id)
            ->setSgFaqCategory((int) $faqCategory->id)
            ->setSgModuleFaq((int) $modules['faq']->id)
        ;

        $config->setSgFaq($faqConfig);

        $this->configurationManager->save($config);
    }

    protected function updateUserGroup(UserGroupModel $objUserGroup, FaqConfig $faqConfig): void
    {
        $objFolder = FilesModel::findByPath($faqConfig->getSgFaqFolder());
        if (!$objFolder) {
            throw new Exception('Unable to find the folder');
        }

        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);
        $userGroupManipulator
            ->addAllowedModules(['faq'])
            ->addAllowedFaq([$faqConfig->getSgFaqCategory()])
            ->addAllowedFilemounts([$objFolder->uuid])
            ->addAllowedFieldsByTables(['tl_faq'])
            ->addAllowedPagemounts($faqConfig->getContaoPagesIds())
            // ->addAllowedModules(Module::getTypesByIds($faqConfig->getContaoModulesIds()))
        ;

        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->faqp = serialize(['create', 'delete']);
        $objUserGroup->save();
    }
}
