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

namespace WEM\SmartgearBundle\Migrations\V1\Y0\Z4\M202301110935;

use Contao\Model\Collection;
use Contao\PageModel;
use Doctrine\DBAL\Connection;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\DirectoriesSynchronizer;
use WEM\SmartgearBundle\Classes\Migration\Result;
use WEM\SmartgearBundle\Classes\Version\Comparator as VersionComparator;
use WEM\SmartgearBundle\Config\Component\Blog\Blog as BlogConfig;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Exceptions\File\NotFound;
use WEM\SmartgearBundle\Migrations\V1\Y0\Z0\MigrationAbstract;
use WEM\SmartgearBundle\Model\Article;
use WEM\SmartgearBundle\Model\Content;
use WEM\SmartgearBundle\Model\Module;

class Migration extends MigrationAbstract
{
    protected string $name = 'Smargear update to v1.0.4';

    protected string $description = 'Set Smartgear to version 1.0.4';

    protected string $version = '1.0.4';

    protected string $translation_key = 'WEMSG.MIGRATIONS.V1_0_4_M202301110935';

    public function __construct(
        Connection $connection,
        TranslatorInterface $translator,
        CoreConfigurationManager $coreConfigurationManager,
        VersionComparator $versionComparator,
        protected DirectoriesSynchronizer $templatesSmartgearSynchronizer
    ) {
        parent::__construct($connection, $translator, $coreConfigurationManager, $versionComparator);
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
            /** @var CoreConfig $config */
            // $coreConfig = $this->coreConfigurationManager->load();

            // copy templates needing to be updated
            $this->templatesSmartgearSynchronizer->synchronize(false);

            $this->updateElementsUsingRemovedTemplate();
            $this->updateBlogComponent();

            // $coreConfig->setSgVersion($this->version);

            // $this->coreConfigurationManager->save($coreConfig);

            $this->updateConfigurationsVersion($this->version);

            $result
                ->setStatus(Result::STATUS_SUCCESS)
                ->addLog($this->translator->trans($this->buildTranslationKey('done'), [], 'contao_default'))
            ;
        } catch (\Exception $exception) {
            $result
                ->setStatus(Result::STATUS_FAIL)
                ->addLog($exception->getMessage())
            ;
        }

        return $result;
    }

    protected function updateElementsUsingRemovedTemplate(): void
    {
        $oldTemplate = 'mod_newslist_without_filters';
        $newTemplate = 'mod_newslist_nofilters';

        $modules = Module::findItems(['customTpl' => $oldTemplate]);
        if ($modules instanceof Collection) {
            while ($modules->next()) {
                $module = $modules->current();
                $module->customTpl = $newTemplate;
                $module->save();
            }
        }

        $contents = Content::findItems(['customTpl' => $oldTemplate]);
        if ($contents instanceof Collection) {
            while ($contents->next()) {
                $content = $contents->current();
                $content->customTpl = $newTemplate;
                $content->save();
            }
        }

        $articles = Article::findItems(['customTpl' => $oldTemplate]);
        if ($articles instanceof Collection) {
            while ($articles->next()) {
                $article = $articles->current();
                $article->customTpl = $newTemplate;
                $article->save();
            }
        }
    }

    protected function updateBlogComponent(): void
    {
        // only if old SG install still valid
        try {
            $coreConfig = $this->coreConfigurationManager->load();
        } catch (NotFound) {
            return;
        }

        /** @var BlogConfig */
        $blogConfig = $coreConfig->getSgBlog();

        if (!$blogConfig->getSgInstallComplete()) {
            return;
        }

        $page = PageModel::findById($blogConfig->getSgPage());
        if ($page) {
            // remove headline from page
            $article = Article::findItems(['pid' => $page->id], 1);
            if ($article instanceof Collection) {
                $headline = Content::findItems(['pid' => $article->id, 'ptable' => 'tl_article', 'type' => 'headline'], 1);
                if ($headline instanceof Collection) {
                    $headline->delete();
                }
            }

            // set header in list module
            $module = Module::findItems(['id' => $blogConfig->getSgModuleList()], 1);
            if ($module instanceof Collection) {
                $module->headline = serialize(['unit' => 'h1', 'value' => $page->title]);
                $module->save();
            }
        }
    }
}
