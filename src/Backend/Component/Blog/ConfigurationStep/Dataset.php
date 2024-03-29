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

use Contao\Input;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Backend\ConfigurationStep;
use WEM\SmartgearBundle\Classes\Command\Util as CommandUtil;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Config\Component\Blog\Blog as BlogConfig;

class Dataset extends ConfigurationStep
{
    /** @var TranslatorInterface */
    protected $translator;
    /** @var ConfigurationManager */
    protected $configurationManager;
    /** @var CommandUtil */
    protected $commandUtil;
    /** @var string */
    protected $sourceDirectory;

    // protected $strTemplate = 'be_wem_sg_install_block_configuration_step_blog_general';

    public function __construct(
        string $module,
        string $type,
        TranslatorInterface $translator,
        ConfigurationManager $configurationManager,
        CommandUtil $commandUtil,
        string $sourceDirectory
    ) {
        parent::__construct($module, $type);
        $this->configurationManager = $configurationManager;
        $this->commandUtil = $commandUtil;
        $this->translator = $translator;
        $this->sourceDirectory = str_replace('[public_or_web]', Util::getPublicOrWebDirectory(true), $sourceDirectory);

        $this->title = $this->translator->trans('WEMSG.BLOG.INSTALL_DATASET.title', [], 'contao_default');
        /** @var BlogConfig */
        $config = $this->configurationManager->load()->getSgBlog();

        $datasetOptions = [];

        $datasetOptions[] = ['value' => 'none', 'label' => $this->translator->trans('WEMSG.BLOG.INSTALL_DATASET.datasetOptionNone', [], 'contao_default')];
        $datasetOptions[] = ['value' => 'A', 'label' => $this->translator->trans('WEMSG.BLOG.INSTALL_DATASET.datasetOptionA', [], 'contao_default')];
        $datasetOptions[] = ['value' => 'B', 'label' => $this->translator->trans('WEMSG.BLOG.INSTALL_DATASET.datasetOptionB', [], 'contao_default')];

        $this->addSelectField('dataset', $this->translator->trans('WEMSG.BLOG.INSTALL_DATASET.dataset', [], 'contao_default'), $datasetOptions, 'none', true, false, '', 'select', $this->translator->trans('WEMSG.BLOG.INSTALL_DATASET.datasetHelp', [], 'contao_default'));
    }

    public function isStepValid(): bool
    {
        // check if the step is correct
        if (null === Input::post('dataset', null)) {
            throw new Exception($this->translator->trans('WEMSG.BLOG.INSTALL_DATASET.datasetMissing', [], 'contao_default'));
        }

        return true;
    }

    public function do(): void
    {
        // do what is meant to be done in this step
        $this->cleanDatasets();
        $this->installDataset(Input::post('dataset', null));
        $this->commandUtil->executeCmdPHP('cache:clear');
    }

    protected function installDataset(string $dataset): void
    {
        switch ($dataset) {
            case 'none':
                // nothing
            break;
            case 'A':
                $this->installDatasetA();
            break;
            case 'B':
                $this->installDatasetB();
            break;
        }
    }

    protected function installDatasetA(): void
    {
        $coreConfig = $this->configurationManager->load();
        $blogConfig = $coreConfig->getSgBlog();
        $filesDirectory = $blogConfig->getCurrentPreset()->getSgNewsFolder();
        $newsArchiveId = $blogConfig->getSgNewsArchive();
        $fileNamesToCopy = ['fileA.jpg', 'fileB.jpg', 'fileC.jpg'];
        $authorId = $coreConfig->getSgUserWebmaster();
        $this->copyFiles($fileNamesToCopy);

        $this->createOrUpdateNews($newsArchiveId, 'News Test A', 'news-test-a', $authorId, strtotime('-1 year'), strtotime('-1 year'), $this->getLoremIpsum(140), $filesDirectory.\DIRECTORY_SEPARATOR.'fileA.jpg', true);
        $this->createOrUpdateNews($newsArchiveId, 'News Test B', 'news-test-b', $authorId, strtotime('-1 week'), strtotime('-1 week'), $this->getLoremIpsum(240), $filesDirectory.\DIRECTORY_SEPARATOR.'fileB.jpg', true);
        $this->createOrUpdateNews($newsArchiveId, 'Actualité C', 'actualité-c', $authorId, strtotime('-1 day'), strtotime('-1 day'), $this->getLoremIpsum(80), $filesDirectory.\DIRECTORY_SEPARATOR.'fileC.jpg', true);
    }

    protected function installDatasetB(): void
    {
        $coreConfig = $this->configurationManager->load();
        $blogConfig = $coreConfig->getSgBlog();
        $filesDirectory = $blogConfig->getCurrentPreset()->getSgNewsFolder();
        $newsArchiveId = $blogConfig->getSgNewsArchive();
        $fileNamesToCopy = ['fileA.jpg', 'fileB.jpg', 'fileC.jpg', 'fileD.jpg', 'fileE.jpg', 'fileF.jpg', 'fileG.jpg', 'fileH.jpg', 'fileI.jpg', 'fileJ.jpg', 'fileK.jpg', 'fileL.jpg', 'fileM.jpg', 'fileN.jpg'];
        $authorId = $coreConfig->getSgUserWebmaster();

        $this->copyFiles($fileNamesToCopy);

        $this->createOrUpdateNews($newsArchiveId, 'News Test A', 'news-test-a', $authorId, strtotime('-1 year'), strtotime('-1 year'), $this->getLoremIpsum(140), $filesDirectory.\DIRECTORY_SEPARATOR.'fileA.jpg', true);
        $this->createOrUpdateNews($newsArchiveId, 'News Test B', 'news-test-b', $authorId, strtotime('-340 days'), strtotime('-340 days'), $this->getLoremIpsum(240), $filesDirectory.\DIRECTORY_SEPARATOR.'fileB.jpg', true);
        $this->createOrUpdateNews($newsArchiveId, 'Actualité C', 'actualité-c', $authorId, strtotime('-300 days'), strtotime('-300 days'), $this->getLoremIpsum(80), $filesDirectory.\DIRECTORY_SEPARATOR.'fileC.jpg', true);
        $this->createOrUpdateNews($newsArchiveId, 'Actualité D', 'actualité-d', $authorId, strtotime('-300 days'), strtotime('-300 days +2 hours'), $this->getLoremIpsum(160), '', true);
        $this->createOrUpdateNews($newsArchiveId, 'Actualité E', 'actualité-e', $authorId, strtotime('-280 days'), strtotime('-280 days'), $this->getLoremIpsum(380), $filesDirectory.\DIRECTORY_SEPARATOR.'fileD.jpg', true);
        $this->createOrUpdateNews($newsArchiveId, 'Actualité F', 'actualité-f', $authorId, strtotime('-270 days'), strtotime('-270 days'), $this->getLoremIpsum(120), '', true);
        $this->createOrUpdateNews($newsArchiveId, 'Actualité G', 'actualité-g', $authorId, strtotime('-240 days'), strtotime('-240 days'), $this->getLoremIpsum(80), '', true);
        $this->createOrUpdateNews($newsArchiveId, 'Actualité H', 'actualité-h', $authorId, strtotime('-180 days'), strtotime('-180 days'), $this->getLoremIpsum(160), $filesDirectory.\DIRECTORY_SEPARATOR.'fileE.jpg', true);
        $this->createOrUpdateNews($newsArchiveId, 'Actualité I', 'actualité-i', $authorId, strtotime('-160 days'), strtotime('-160 days'), $this->getLoremIpsum(340), $filesDirectory.\DIRECTORY_SEPARATOR.'fileF.jpg', true);
        $this->createOrUpdateNews($newsArchiveId, 'Actualité J', 'actualité-j', $authorId, strtotime('-140 days'), strtotime('-140 days'), $this->getLoremIpsum(80), $filesDirectory.\DIRECTORY_SEPARATOR.'fileG.jpg', true);
        $this->createOrUpdateNews($newsArchiveId, 'Actualité K', 'actualité-k', $authorId, strtotime('-135 days'), strtotime('-135 days'), $this->getLoremIpsum(160), $filesDirectory.\DIRECTORY_SEPARATOR.'fileH.jpg', true);
        $this->createOrUpdateNews($newsArchiveId, 'Actualité L', 'actualité-l', $authorId, strtotime('-120 days'), strtotime('-120 days'), $this->getLoremIpsum(360), $filesDirectory.\DIRECTORY_SEPARATOR.'fileI.jpg', true);
        $this->createOrUpdateNews($newsArchiveId, 'Actualité M', 'actualité-m', $authorId, strtotime('-90 days'), strtotime('-90 days'), $this->getLoremIpsum(80), $filesDirectory.\DIRECTORY_SEPARATOR.'fileJ.jpg', true);
        $this->createOrUpdateNews($newsArchiveId, 'Actualité N', 'actualité-n', $authorId, strtotime('-85 days'), strtotime('-85 days'), $this->getLoremIpsum(160), $filesDirectory.\DIRECTORY_SEPARATOR.'fileK.jpg', true);
        $this->createOrUpdateNews($newsArchiveId, 'Actualité O', 'actualité-o', $authorId, strtotime('-70 days'), strtotime('-70 days'), $this->getLoremIpsum(600), $filesDirectory.\DIRECTORY_SEPARATOR.'fileL.jpg', true);
        $this->createOrUpdateNews($newsArchiveId, 'Actualité P', 'actualité-p', $authorId, strtotime('-60 days'), strtotime('-60 days'), $this->getLoremIpsum(320), $filesDirectory.\DIRECTORY_SEPARATOR.'fileM.jpg', true);
        $this->createOrUpdateNews($newsArchiveId, 'Actualité Q', 'actualité-q', $authorId, strtotime('-30 days'), strtotime('-30 days'), $this->getLoremIpsum(80), '', true);
        $this->createOrUpdateNews($newsArchiveId, 'Actualité R', 'actualité-r', $authorId, strtotime('-15 days'), strtotime('-15 days'), $this->getLoremIpsum(120), $filesDirectory.\DIRECTORY_SEPARATOR.'fileN.jpg', true);
    }

    protected function cleanDatasets(): void
    {
        $blogConfig = $this->configurationManager->load()->getSgBlog();
        $directory = $blogConfig->getCurrentPreset()->getSgNewsFolder();
        $newsArchiveId = $blogConfig->getSgNewsArchive();
        $fileNamesToDelete = ['fileA.jpg', 'fileB.jpg', 'fileC.jpg', 'fileD.jpg', 'fileE.jpg', 'fileF.jpg', 'fileG.jpg', 'fileH.jpg', 'fileI.jpg', 'fileJ.jpg', 'fileK.jpg', 'fileL.jpg', 'fileM.jpg', 'fileN.jpg'];
        foreach ($fileNamesToDelete as $filenameToDelete) {
            $objFile = new \Contao\File($directory.\DIRECTORY_SEPARATOR.$filenameToDelete);
            if ($objFile->exists()) {
                $objFile->delete();
            }
        }

        $newsAliasesToDelete = ['news-test-a', 'news-test-b', 'actualité-c'];
        foreach ($newsAliasesToDelete as $newsAliasToDelete) {
            $objNews = \Contao\NewsModel::findOneByAlias($newsAliasToDelete);
            if ($objNews) {
                $objNews->delete();
            }
        }
    }

    protected function copyFiles(array $filenames): void
    {
        $blogConfig = $this->configurationManager->load()->getSgBlog();
        $destinationDirectory = $blogConfig->getCurrentPreset()->getSgNewsFolder();
        foreach ($filenames as $filenameToCopy) {
            $objFile = new \Contao\File($this->sourceDirectory.\DIRECTORY_SEPARATOR.$filenameToCopy);
            if (!$objFile->copyTo($destinationDirectory.\DIRECTORY_SEPARATOR.$filenameToCopy)) {
                throw new Exception($this->translator->trans('WEMSG.DIRECTORIESSYNCHRONIZER.error', [$this->sourceDirectory.\DIRECTORY_SEPARATOR.$filenameToCopy, $destinationDirectory.\DIRECTORY_SEPARATOR.$filenameToCopy], 'contao_default'));
            }
        }
    }

    protected function createOrUpdateNews(int $pid, string $title, string $alias, int $author, $date, $time, string $teaser, string $fileSRC, bool $published): void
    {
        $singleSRC = $fileSRC;
        if (!empty($fileSRC)) {
            $objFile = \Contao\FilesModel::findByPath($fileSRC);
            if ($objFile) {
                $singleSRC = $objFile->uuid;
            } else {
                $singleSRC = null;
            }
        }
        $objNews = \Contao\NewsModel::findOneByAlias($alias) ?? new \Contao\NewsModel();
        $objNews->pid = $pid;
        $objNews->title = $title;
        $objNews->headline = $title;
        $objNews->alias = $alias;
        $objNews->author = $author;
        $objNews->date = $date;
        $objNews->time = $time;
        $objNews->teaser = $teaser;
        $objNews->addImage = !empty($singleSRC);
        $objNews->singleSRC = $singleSRC;
        $objNews->published = $published;
        $objNews->tstamp = time();
        $objNews->save();
    }

    protected function getLoremIpsum(int $length)
    {
        return substr('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam at semper sapien. Vivamus ac consequat ligula. Suspendisse dapibus nisi laoreet, porta nisl eget, ornare neque. Aliquam eu ex molestie, rhoncus tortor sed, pellentesque nisi. Donec auctor venenatis sapien, fermentum consequat lorem placerat sit amet. Maecenas ac placerat tellus. Nulla nunc mi, tempus non mollis vitae, venenatis sed purus. Sed eu velit imperdiet, cursus libero et, porttitor risus. Suspendisse potenti. Vestibulum eget nisl lectus. Vestibulum eu interdum tellus, nec rhoncus augue. Ut orci justo, feugiat ut nunc tristique, faucibus consequat quam. Fusce dignissim sagittis lectus, non placerat odio porttitor vitae. Curabitur suscipit erat et dolor hendrerit commodo. Interdum et malesuada fames ac ante ipsum primis in faucibus. Nunc a elit condimentum, semper felis ut, mattis justo.', 0, $length);
    }
}
