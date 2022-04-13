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

use Contao\DataContainer;
use Contao\Input;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Backend\ConfigurationStep;
use WEM\SmartgearBundle\Classes\Command\Util as CommandUtil;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Config\Component\Blog\Blog as BlogConfig;
use WEM\SmartgearBundle\Config\Component\Blog\NewsArchive as NewsArchiveConfig;
use WEM\SmartgearBundle\Config\Core as CoreConfig;

class General extends ConfigurationStep
{
    /** @var TranslatorInterface */
    protected $translator;
    /** @var ConfigurationManager */
    protected $configurationManager;
    /** @var CommandUtil */
    protected $commandUtil;
    /** @var DataContainer */
    // protected $dataContainer;

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
        $sgNewsConfigDefaultValue = \count($config->getSgNewsArchives()) > 0 ? $config->getSgNewsArchives()[0]->getSgNewsArchive() : null;

        foreach ($config->getSgNewsArchives() as $newsArchiveConfig) {
            $sgNewsConfigOptions[] = ['value' => $newsArchiveConfig->getSgNewsArchive(), 'label' => $newsArchiveConfig->getSgNewsArchiveTitle()];
        }

        $this->addSelectField('newsConfig', $this->translator->trans('WEMSG.BLOG.INSTALL.newsConfig', [], 'contao_default'), $sgNewsConfigOptions, $sgNewsConfigDefaultValue, true);
        $this->addTextField('new_config', $this->translator->trans('WEMSG.BLOG.INSTALL.newConfigTitle', [], 'contao_default'), '', false);
        if (\count($config->getSgNewsArchives()) > 0) {
            $sgNewsArchiveConfig = $config->getSgNewsArchives()[0];
            $this->addTextField('newsArchiveTitle', $this->translator->trans('WEMSG.BLOG.INSTALL.newsArchiveTitle', [], 'contao_default'), $sgNewsArchiveConfig->getSgNewsArchiveTitle(), true);

            $this->addTextField('newsListPerPage', $this->translator->trans('WEMSG.BLOG.INSTALL.newsListPerPage', [], 'contao_default'), (string) $sgNewsArchiveConfig->getSgNewsListPerPage(), false, '', 'number');

            $this->addTextField('pageTitle', $this->translator->trans('WEMSG.BLOG.INSTALL.pageTitle', [], 'contao_default'), $sgNewsArchiveConfig->getSgPageTitle(), true);

            $this->addSimpleFileTree('newsFolder', $this->translator->trans('WEMSG.BLOG.INSTALL.newsFolder', [], 'contao_default'), $sgNewsArchiveConfig->getSgNewsFolder(), true, false, '', 'ffgddfg', ['multiple' => false, 'isGallery' => false,
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
        $this->commandUtil->executeCmdPHP('cache:clear');
    }

    public function newsConfigAdd()
    {
        if (empty(Input::post('new_config'))) {
            throw new \InvalidArgumentException($this->translator->trans('WEMSG.BLOG.INSTALL.fieldNewsConfigNameEmpty', [], 'contao_default'));
        }

        $newsConfigTitle = Input::post('new_config');

        if (!preg_match('/^([A-Za-z0-9-_]+)$/', $newsConfigTitle)) {
            throw new \InvalidArgumentException($this->translator->trans('WEMSG.BLOG.INSTALL.fieldNewsConfigNameIncorrectFormat', [], 'contao_default'));
        }

        // here we add a new tl_news_archive
        $objNewsArchive = new \Contao\NewsArchiveModel();
        $objNewsArchive->title = $newsConfigTitle;
        $objNewsArchive->save();

        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var NewsArchiveConfig */
        $newsArchiveConfig = new NewsArchiveConfig();
        $newsArchiveConfig->setSgNewsArchiveTitle($newsConfigTitle);
        $newsArchiveConfig->setSgNewsArchive((int) $objNewsArchive->id);
        $config->getSgBlog()->addOrUpdateNewsArchive($newsArchiveConfig);

        return $this->configurationManager->save($config);
    }

    public function newsConfigGet(int $id)
    {
        /* @var NewsArchiveConfig */
        return $this->configurationManager->load()->getSgBlog()->getNewsArchiveById($id);
    }

    protected function updateModuleConfiguration(): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var BlogConfig */
        $blogConfig = $config->getSgBlog();

        $blogConfig
            ->setSgMode(Input::post('expertMode') ? BlogConfig::MODE_EXPERT : BlogConfig::MODE_SIMPLE)
        ;

        $newsArchiveConfig = $blogConfig
            ->getNewsArchiveById((int) Input::post('newsConfig'))
            ->setSgNewsArchiveTitle(Input::post('newsArchiveTitle'))
            ->setSgNewsListPerPage((int) Input::post('newsListPerPage'))
            ->setSgPageTitle(Input::post('pageTitle'))
            ->setSgNewsFolder(Input::post('newsFolder'))
        ;
        $blogConfig->addOrUpdateNewsArchive($newsArchiveConfig);
        $config->setSgBlog($blogConfig);

        $this->configurationManager->save($config);
    }
}
