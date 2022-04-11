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
use WEM\SmartgearBundle\Config\Component\Blog\Blog as BlogConfig;
use WEM\SmartgearBundle\Config\Component\Blog\NewsArchive as NewsArchiveConfig;
use WEM\SmartgearBundle\Config\Core as CoreConfig;
use WEM\SmartgearBundle\Config\LocalConfig as LocalConfig;

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
        $sgNewsConfigDefaultValue = \count($config->getSgNewsArchives()) > 0 ? $config->getSgNewsArchives()[0]->getSgNewsArchive() : null;

        foreach ($config->getSgNewsArchives() as $newsArchiveConfig) {
            $sgNewsConfigOptions[] = ['value' => $newsArchiveConfig->getSgNewsArchive(), 'label' => $newsArchiveConfig->getSgNewsArchiveTitle()];
        }

        $this->addSelectField('newsConfig', $this->translator->trans('WEMSG.BLOG.INSTALL.newsConfig', [], 'contao_default'), $sgNewsConfigOptions, $sgNewsConfigDefaultValue, true);
        $this->addTextField('new_config', $this->translator->trans('WEMSG.BLOG.INSTALL.newConfigTitle', [], 'contao_default'), '', false);
        if (\count($config->getSgNewsArchives()) > 0) {
            $sgNewsArchiveConfig = $config->getSgNewsArchives()[0];
            $this->addTextField('newsArchiveTitle', $this->translator->trans('WEMSG.BLOG.INSTALL.newsArchiveTitle', [], 'contao_default'), $sgNewsArchiveConfig->getSgNewsArchiveTitle(), true);

            // $this->addSelectField('sgAnalytics', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgAnalytics'], $sgAnalyticsOptions, $config->getSgAnalytics(), true);
            // $this->addSelectField('sgAnalytics', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgAnalytics'], $sgAnalyticsOptions, $config->getSgAnalytics(), true);

            $this->addTextField('newsListPerPage', $this->translator->trans('WEMSG.BLOG.INSTALL.newsListPerPage', [], 'contao_default'), (string) $sgNewsArchiveConfig->getSgNewsListPerPage(), false, '', 'number');
            $this->addTextField('pageTitle', $this->translator->trans('WEMSG.BLOG.INSTALL.pageTitle', [], 'contao_default'), $sgNewsArchiveConfig->getSgPageTitle(), true);
        }
        $this->addCheckboxField('expertMode', $this->translator->trans('WEMSG.BLOG.INSTALL.expertMode', [], 'contao_default'), '1', BlogConfig::MODE_EXPERT === $config->getSgMode());
    }

    public function isStepValid(): bool
    {
        // check if the step is correct
        if (empty(Input::post('sgWebsiteTitle'))) {
            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgWebsiteTitleMissing']);
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

        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var NewsArchiveConfig */
        $newsArchiveConfig = new NewsArchiveConfig();
        $newsArchiveConfig->setSgNewsArchiveTitle($newsConfigTitle);

        $config->getSgBlog()->addOrUpdateNewsArchive($newsArchiveConfig);

        return $this->configurationManager->save($config);
    }

    protected function updateModuleConfiguration(): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var BlogConfig */
        $blogConfig = $config->getSgBlog();

        $blogConfig->setSgWebsiteTitle(Input::post('sgWebsiteTitle'));
        $config->setSgBlog($blogConfig);

        $this->configurationManager->save($config);
    }

    protected function updateContaoConfiguration(): void
    {
        /** @var LocalConfig */
        $config = $this->localConfigManager->load();

        $config->setDateFormat('d/m/Y')
        ->setTimeFormat('H:i')
        ->setDatimFormat('d/m/Y à H:i')
        ->setTimeZone('Europe/Paris')
        ->setCharacterSet('utf-8')
        ->setUseAutoItem(1)
        ->setFolderUrl(1)
        ->setMaxResultsPerPage(500)
        ->setPrivacyAnonymizeIp(1)
        ->setPrivacyAnonymizeGA(1)
        ->setGdMaxImgWidth(5000)
        ->setGdMaxImgHeight(5000)
        ->setMaxFileSize(10000000)
        ->setUndoPeriod(7776000)
        ->setVersionPeriod(7776000)
        ->setLogPeriod(7776000)
        ->setAllowedTags('<script><iframe><a><abbr><acronym><address><area><article><aside><audio><b><bdi><bdo><big><blockquote><br><base><button><canvas><caption><cite><code><col><colgroup><data><datalist><dataset><dd><del><dfn><div><dl><dt><em><fieldset><figcaption><figure><footer><form><h1><h2><h3><h4><h5><h6><header><hgroup><hr><i><img><input><ins><kbd><keygen><label><legend><li><link><map><mark><menu><nav><object><ol><optgroup><option><output><p><param><picture><pre><q><s><samp><section><select><small><source><span><strong><style><sub><sup><table><tbody><td><textarea><tfoot><th><thead><time><tr><tt><u><ul><var><video><wbr>')
        ->setSgOwnerDomain(\Contao\Environment::get('base'))
        ->setSgOwnerHost(CoreConfig::DEFAULT_OWNER_HOST)
        ->setRejectLargeUploads(true)
        ->setFileusageSkipReplaceInsertTags(true)
        ->setFileusageSkipDatabase(true)
        ->setImageSizes([
            '_defaults' => [
                'formats' => [
                    'jpg' => ['jpg', 'jpeg'],
                    'png' => ['png'],
                    'gif' => ['gif'],
                ],
                'lazy_loading' => true,
                'resize_mode' => 'crop',
            ],
            '16-9' => [
                'width' => 1920,
                'height' => 1080,
                'densities' => '0.5x, 1x, 2x',
            ],
            '2-1' => [
                'width' => 1920,
                'height' => 960,
                'densities' => '2x',
            ],
            '1-2' => [
                'width' => 960,
                'height' => 1920,
                'densities' => '0.5x',
            ],
            '1-1' => [
                'width' => 1920,
                'height' => 1920,
                'densities' => '1x',
            ],
            '4-3' => [
                'width' => 1920,
                'height' => 1440,
                'densities' => '0.5x, 1x, 2x',
            ],
        ])
        ;

        $this->localConfigManager->save($config);
    }
}
