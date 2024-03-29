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

namespace WEM\SmartgearBundle\Migrations\V1_0_4\M202301110935;

use Contao\PageModel;
use Doctrine\DBAL\Connection;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\DirectoriesSynchronizer;
use WEM\SmartgearBundle\Classes\Migration\Result;
use WEM\SmartgearBundle\Classes\Version\Comparator as VersionComparator;
use WEM\SmartgearBundle\Config\Component\Blog\Blog as BlogConfig;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Migrations\V1_0_0\MigrationAbstract;
use WEM\SmartgearBundle\Model\Article;
use WEM\SmartgearBundle\Model\Content;
use WEM\SmartgearBundle\Model\Module;

class Migration extends MigrationAbstract
{
    protected $name = 'Smargear update to v1.0.4';
    protected $description = 'Set Smartgear to version 1.0.4';
    protected $version = '1.0.4';
    protected $translation_key = 'WEMSG.MIGRATIONS.V1_0_4_M202301110935';
    /** @var DirectoriesSynchronizer */
    protected $templatesSmartgearSynchronizer;

    public function __construct(
        Connection $connection,
        TranslatorInterface $translator,
        CoreConfigurationManager $coreConfigurationManager,
        VersionComparator $versionComparator,
        DirectoriesSynchronizer $templatesSmartgearSynchronizer
    ) {
        parent::__construct($connection, $translator, $coreConfigurationManager, $versionComparator);
        $this->templatesSmartgearSynchronizer = $templatesSmartgearSynchronizer;
    }

    public function shouldRun(): Result
    {
        $result = parent::shouldRun();

        if (Result::STATUS_SHOULD_RUN !== $result->getStatus()) {
            return $result;
        }

        $result
            ->addLog($this->translator->trans('WEMSG.MIGRATIONS.shouldBeRun', [], 'contao_default'))
        ;

        return $result;
    }

    public function do(): Result
    {
        $result = $this->shouldRun();
        if (Result::STATUS_SHOULD_RUN !== $result->getStatus()) {
            return $result;
        }
        try {
            /** @var CoreConfig */
            $coreConfig = $this->coreConfigurationManager->load();

            // copy templates needing to be updated
            $this->templatesSmartgearSynchronizer->synchronize(false);

            $this->updateElementsUsingRemovedTemplate();
            $this->updateBlogComponent();

            $coreConfig->setSgVersion($this->version);

            $this->coreConfigurationManager->save($coreConfig);

            $result
                ->setStatus(Result::STATUS_SUCCESS)
                ->addLog($this->translator->trans($this->buildTranslationKey('done'), [], 'contao_default'))
            ;
        } catch (\Exception $e) {
            $result
                ->setStatus(Result::STATUS_FAIL)
                ->addLog($e->getMessage())
            ;
        }

        return $result;
    }

    protected function updateElementsUsingRemovedTemplate(): void
    {
        $oldTemplate = 'mod_newslist_without_filters';
        $newTemplate = 'mod_newslist_nofilters';

        $modules = Module::findItems(['customTpl' => $oldTemplate]);
        if ($modules) {
            while ($modules->next()) {
                $module = $modules->current();
                $module->customTpl = $newTemplate;
                $module->save();
            }
        }

        $contents = Content::findItems(['customTpl' => $oldTemplate]);
        if ($contents) {
            while ($contents->next()) {
                $content = $contents->current();
                $content->customTpl = $newTemplate;
                $content->save();
            }
        }

        $articles = Article::findItems(['customTpl' => $oldTemplate]);
        if ($articles) {
            while ($articles->next()) {
                $article = $articles->current();
                $article->customTpl = $newTemplate;
                $article->save();
            }
        }
    }

    protected function updateBlogComponent(): void
    {
        /** @var CoreConfig */
        $coreConfig = $this->coreConfigurationManager->load();

        /** @var BlogConfig */
        $blogConfig = $coreConfig->getSgBlog();

        if (!$blogConfig->getSgInstallComplete()) {
            return;
        }

        $page = PageModel::findById($blogConfig->getSgPage());
        if ($page) {
            // remove headline from page
            $article = Article::findItems(['pid' => $page->id], 1);
            if ($article) {
                $headline = Content::findItems(['pid' => $article->id, 'ptable' => 'tl_article', 'type' => 'headline'], 1);
                if ($headline) {
                    $headline->delete();
                }
            }

            // set header in list module
            $module = Module::findItems(['id' => $blogConfig->getSgModuleList()], 1);
            if ($module) {
                $module->headline = serialize(['unit' => 'h1', 'value' => $page->title]);
                $module->save();
            }
        }
    }
}
