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

namespace WEM\SmartgearBundle\Backend\Component\Faq;

use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\FaqCategoryModel;
use Contao\FaqModel;
use Contao\FilesModel;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\UserGroupModel;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Backend\Resetter as BackendResetter;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\UserGroupModelUtil;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Config\Component\Faq\Faq as FaqConfig;
use WEM\SmartgearBundle\Model\Module;

class Resetter extends BackendResetter
{
    /** @var string */
    protected $module = '';
    /** @var string */
    protected $type = '';
    /** @var ConfigurationManager */
    protected $configurationManager;
    /** @var TranslatorInterface */
    protected $translator;

    /**
     * Generic array of logs.
     *
     * @var array
     */
    protected $logs = [];

    public function __construct(
        ConfigurationManager $configurationManager,
        TranslatorInterface $translator,
        string $module,
        string $type
    ) {
        parent::__construct($configurationManager, $translator, $module, $type);
    }

    public function reset(string $mode): void
    {
        $this->resetUserGroupSettings();
        // reset everything except what we wanted to keep
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FaqConfig */
        $faqConfig = $config->getSgFaq();
        $archiveTimestamp = time();

        switch ($mode) {
            case FaqConfig::ARCHIVE_MODE_ARCHIVE:
                $objFolder = new \Contao\Folder($faqConfig->getSgFaqFolder());
                if ($objFolder) {
                    $objFolder->renameTo(sprintf('files/archives/events-%s', (string) $archiveTimestamp));
                }

                $objFaqCategory = FaqCategoryModel::findById($faqConfig->getSgFaqCategory());
                if ($objFaqCategory) {
                    $objFaqCategory->title = sprintf('%s (Archive-%s)', $objFaqCategory->title, (string) $archiveTimestamp);
                    $objFaqCategory->save();
                }

                $objPage = PageModel::findById($faqConfig->getSgPage());
                if ($objPage) {
                    $objPage->published = false;
                    $objPage->save();
                }

                foreach ($faqConfig->getContaoArticlesIds() as $id) {
                    $objArticle = ArticleModel::findByPk($id);
                    if ($objArticle) {
                        $objArticle->published = false;
                        $objArticle->title = sprintf('%s (Archive-%s)', $objArticle->title, (string) $archiveTimestamp);
                        $objArticle->save();
                    }
                }

                foreach ($faqConfig->getContaoModulesIds() as $id) {
                    $objModule = ModuleModel::findByPk($id);
                    if ($objModule) {
                        $objModule->published = false;
                        $objModule->title = sprintf('%s (Archive-%s)', $objModule->title, (string) $archiveTimestamp);
                        $objModule->save();
                    }
                }
            break;
            case FaqConfig::ARCHIVE_MODE_KEEP:
            break;
            case FaqConfig::ARCHIVE_MODE_DELETE:
                $objFolder = new \Contao\Folder($faqConfig->getSgFaqFolder());
                if ($objFolder) {
                    $objFolder->delete();
                }

                $faqs = FaqModel::findByPid($faqConfig->getSgFaqCategory());
                if ($faqs) {
                    while ($faqs->next()) {
                        $faqs->delete();
                    }
                }

                $objFaqCategory = FaqCategoryModel::findById($faqConfig->getSgFaqCategory());
                if ($objFaqCategory) {
                    $objFaqCategory->delete();
                }

                $objPage = PageModel::findById($faqConfig->getSgPage());
                if ($objPage) {
                    $objPage->delete();
                }

                foreach ($faqConfig->getContaoArticlesIds() as $id) {
                    $objArticle = ArticleModel::findByPk($id);
                    if ($objArticle) {
                        $objArticle->delete();
                    }
                }

                foreach ($faqConfig->getContaoContentsIds() as $id) {
                    $objContent = ContentModel::findByPk($id);
                    if ($objContent) {
                        $objContent->delete();
                    }
                }

                foreach ($faqConfig->getContaoModulesIds() as $id) {
                    $objModule = ModuleModel::findByPk($id);
                    if ($objModule) {
                        $objModule->delete();
                    }
                }
            break;
            default:
                throw new \InvalidArgumentException($this->translator->trans('WEMSG.FAQ.RESET.deleteModeUnknown', [], 'contao_default'));
            break;
        }

        $faqConfig->setSgArchived(true)
            ->setSgArchivedMode($mode)
            ->setSgArchivedAt($archiveTimestamp)
        ;

        $config->setSgFaq($faqConfig);

        $this->configurationManager->save($config);
    }

    protected function resetUserGroupSettings(): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FaqConfig */
        $faqConfig = $config->getSgFaq();

        $this->resetUserGroup(UserGroupModel::findOneById($config->getSgUserGroupRedactors()), $faqConfig);
        $this->resetUserGroup(UserGroupModel::findOneById($config->getSgUserGroupAdministrators()), $faqConfig);
    }

    protected function resetUserGroup(UserGroupModel $objUserGroup, FaqConfig $faqConfig): void
    {
        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);
        $userGroupManipulator
            ->removeAllowedModules(['faq'])
            ->removeAllowedFaq([$faqConfig->getSgFaqCategory()])
            ->removeAllowedFieldsByPrefixes(['tl_faq::'])
            ->removeAllowedPagemounts($faqConfig->getContaoPagesIds())
            ->removeAllowedModules(Module::getTypesByIds($faqConfig->getContaoModulesIds()))
        ;

        $objFolder = FilesModel::findByPath($faqConfig->getSgFaqFolder());
        if ($objFolder) {
            $userGroupManipulator->removeAllowedFilemounts([$objFolder->uuid]);
        }

        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->faqp = null;
        $objUserGroup->save();
    }
}
