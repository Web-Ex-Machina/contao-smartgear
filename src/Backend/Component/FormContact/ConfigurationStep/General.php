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

namespace WEM\SmartgearBundle\Backend\Component\FormContact\ConfigurationStep;

use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\FilesModel;
use Contao\FormContactCategoryModel;
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
use WEM\SmartgearBundle\Config\Component\FormContact\FormContact as FormContactConfig;

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

        $this->title = $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.title', [], 'contao_default');
        /** @var FormContactConfig */
        $config = $this->configurationManager->load()->getSgFormContact();

        $this->addTextField('formContactTitle', $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.formContactTitle', [], 'contao_default'), $config->getSgFormContactTitle(), true);

        $this->addTextField('pageTitle', $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.pageTitle', [], 'contao_default'), $config->getSgPageTitle(), true);
    }

    public function isStepValid(): bool
    {
        // check if the step is correct
        if (null === Input::post('formContactTitle', null)) {
            throw new Exception($this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.calendarTitleMissing', [], 'contao_default'));
        }
        if (null === Input::post('pageTitle', null)) {
            throw new Exception($this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.pageTitleMissing', [], 'contao_default'));
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
        $formContactCategory = $this->createFormContactCategory($page);
        $modules = $this->createModules($page, $formContactCategory);
        $contents = $this->fillArticle($page, $article, $modules);
        $this->updateModuleConfigurationAfterGenerations($page, $article, $formContactCategory, $modules, $contents);
        $this->updateUserGroups();
        $this->commandUtil->executeCmdPHP('cache:clear');
        $this->commandUtil->executeCmdPHP('contao:symlinks');
    }

    protected function updateModuleConfiguration(): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FormContactConfig */
        $formContactConfig = $config->getSgFormContact();

        $formContactConfig
            ->setSgFormContactTitle(Input::post('formContactTitle'))
            ->setSgPageTitle(Input::post('pageTitle'))
            ->setSgFormContactFolder(FormContactConfig::DEFAULT_FOLDER_PATH)
            ->setSgArchived(false)
            ->setSgArchivedMode(FormContactConfig::ARCHIVE_MODE_EMPTY)
            ->setSgArchivedAt(0)
        ;
        $config->setSgFormContact($formContactConfig);

        $this->configurationManager->save($config);
    }

    protected function createFolder(): void
    {
        $objFolder = new \Contao\Folder(FormContactConfig::DEFAULT_FOLDER_PATH);
        $objFolder->unprotect();
    }

    protected function createPage(): PageModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FormContactConfig */
        $formContactConfig = $config->getSgFormContact();

        $rootPage = PageModel::findById($config->getSgPageRoot());

        $page = PageModel::findById($formContactConfig->getSgPage());

        return Util::createPage($formContactConfig->getSgPageTitle(), 0, array_merge([
            'pid' => $rootPage->id,
            'sorting' => ((int) $rootPage->sorting) + 128,
            'layout' => $rootPage->layout,
            'title' => $formContactConfig->getSgPageTitle(),
            'robots' => 'index,follow',
            'type' => 'regular',
            'published' => 1,
        ], null !== $page ? ['id' => $page->id] : []));
    }

    protected function createArticle(PageModel $page): ArticleModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FormContactConfig */
        $formContactConfig = $config->getSgFormContact();

        $article = ArticleModel::findById($formContactConfig->getSgArticle());

        return Util::createArticle($page, array_merge([
            'title' => $formContactConfig->getSgPageTitle(),
        ], null !== $article ? ['id' => $article->id] : []));
    }

    protected function createFormContactCategory(PageModel $page): FormContactCategoryModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FormContactConfig */
        $formContactConfig = $config->getSgFormContact();

        $objUserGroupAdministrators = UserGroupModel::findOneById($config->getSgUserGroupAdministrators());
        $objUserGroupWebmasters = UserGroupModel::findOneById($config->getSgUserGroupWebmasters());

        $formContactCategory = FormContactCategoryModel::findById($formContactConfig->getSgFormContactCategory()) ?? new FormContactCategoryModel();
        $formContactCategory->title = $formContactConfig->getSgFormContactTitle();
        $formContactCategory->jumpTo = $page->id;
        $formContactCategory->groups = serialize([$objUserGroupAdministrators->id, $objUserGroupWebmasters->id]);
        $formContactCategory->tstamp = time();
        $formContactCategory->save();

        return $formContactCategory;
    }

    protected function createModules(PageModel $page, FormContactCategoryModel $formContactCategory): array
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FormContactConfig */
        $formContactConfig = $config->getSgFormContact();

        $moduleFormContact = new ModuleModel();

        if (null !== $formContactConfig->getSgModuleFormContact()) {
            $moduleListOld = ModuleModel::findById($formContactConfig->getSgModuleFormContact());
            if ($moduleListOld) {
                $moduleListOld->delete();
            }
            $moduleFormContact->id = $formContactConfig->getSgModuleFormContact();
        }
        $moduleFormContact->name = $page->title.' - Reader';
        $moduleFormContact->pid = $config->getSgTheme();
        $moduleFormContact->type = 'formContactpage';
        $moduleFormContact->formContact_categories = serialize([$formContactCategory->id]);
        $moduleFormContact->numberOfItems = 0;
        $moduleFormContact->imgSize = serialize([0 => '480', 1 => '0', 2 => \Contao\Image\ResizeConfiguration::MODE_PROPORTIONAL]);
        $moduleFormContact->tstamp = time();
        $moduleFormContact->save();

        return ['formContact' => $moduleFormContact];
    }

    protected function fillArticle(PageModel $page, ArticleModel $article, array $modules): array
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        $formContactConfig = $config->getSgFormContact();

        $formContact = ContentModel::findById($formContactConfig->getSgContent());
        $formContact = Util::createContent($article, array_merge([
            'type' => 'module',
            'pid' => $article->id,
            'ptable' => 'tl_article',
            'module' => $modules['formContact']->id,
        ], ['id' => null !== $formContact ? $formContact->id : null]));

        $article->save();

        return ['formContact' => $formContact];
    }

    protected function updateModuleConfigurationAfterGenerations(PageModel $page, ArticleModel $article, FormContactCategoryModel $formContactCategory, array $modules, array $contents): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FormContactConfig */
        $formContactConfig = $config->getSgFormContact();

        $formContactConfig
            ->setSgPage((int) $page->id)
            ->setSgArticle((int) $article->id)
            ->setSgContent((int) $contents['formContact']->id)
            ->setSgFormContactCategory((int) $formContactCategory->id)
            ->setSgModuleFormContact((int) $modules['formContact']->id)
        ;

        $config->setSgFormContact($formContactConfig);

        $this->configurationManager->save($config);
    }

    protected function updateUserGroups(): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FormContactConfig */
        $formContactConfig = $config->getSgFormContact();

        // retrieve the webmaster's group and update the permissions

        $objUserGroup = UserGroupModel::findOneById($config->getSgUserGroupWebmasters());
        $objUserGroup = $this->updateUserGroupAllowedModules($objUserGroup);
        $objUserGroup = $this->updateUserGroupAllowedFormContact($objUserGroup, $formContactConfig);
        $objUserGroup = $this->updateUserGroupAllowedDirectory($objUserGroup, $formContactConfig);
        $objUserGroup = $this->updateUserGroupAllowedFields($objUserGroup);
        $objUserGroup->save();

        $objUserGroup = UserGroupModel::findOneById($config->getSgUserGroupAdministrators());
        $objUserGroup = $this->updateUserGroupAllowedModules($objUserGroup);
        $objUserGroup = $this->updateUserGroupAllowedFormContact($objUserGroup, $formContactConfig);
        $objUserGroup = $this->updateUserGroupAllowedDirectory($objUserGroup, $formContactConfig);
        $objUserGroup = $this->updateUserGroupAllowedFields($objUserGroup);
        $objUserGroup->save();
    }

    protected function updateUserGroupAllowedModules(UserGroupModel $objUserGroup): UserGroupModel
    {
        return UserGroupModelUtil::addAllowedModules($objUserGroup, ['formContact']);
    }

    protected function updateUserGroupAllowedFormContact(UserGroupModel $objUserGroup, FormContactConfig $formContactConfig): UserGroupModel
    {
        $objUserGroup = UserGroupModelUtil::addAllowedFormContact($objUserGroup, [$formContactConfig->getSgFormContactCategory()]);
        $objUserGroup->formContactp = serialize(['create', 'delete']);

        return $objUserGroup;
    }

    protected function updateUserGroupAllowedDirectory(UserGroupModel $objUserGroup, FormContactConfig $formContactConfig): UserGroupModel
    {
        // add allowed directory
        $objFolder = FilesModel::findByPath($formContactConfig->getSgFormContactFolder());
        if (!$objFolder) {
            throw new Exception('Unable to find the folder');
        }

        return UserGroupModelUtil::addAllowedFilemounts($objUserGroup, [$objFolder->uuid]);
    }

    protected function updateUserGroupAllowedFields(UserGroupModel $objUserGroup): UserGroupModel
    {
        return UserGroupModelUtil::addAllowedFieldsByTables($objUserGroup, ['tl_formContact']);
    }
}
