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

namespace WEM\SmartgearBundle\Backend\Dashboard;

use Contao\BackendModule;
use Contao\System;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Exceptions\File\NotFound;

class ShortcutInternal extends BackendModule
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'be_wem_sg_dashboard_shortcut_internal';
    protected $strId = 'wem_sg_dashboard_shortcut_internal';
    /** @var TranslatorInterface */
    protected $translator;
    /** @var ConfigurationManager */
    protected $configurationManager;

    /**
     * Initialize the object.
     */
    public function __construct(
        TranslatorInterface $translator,
        configurationManager $configurationManager
    ) {
        parent::__construct();
        $this->translator = $translator;
        $this->configurationManager = $configurationManager;
    }

    public function generate(): string
    {
        return parent::generate();
    }

    public function compile(): void
    {
        try {
            $config = $this->configurationManager->load();
        } catch (NotFound $e) {
            return;
        }
        $this->Template->title = $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTINTERNAL.title', [], 'contao_default');
        // pages
        $this->Template->modPageUrl = System::getContainer()->get('router')->generate('contao_backend', ['do' => 'page']);

        $this->Template->linkPageText = $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTINTERNAL.linkPageText', [], 'contao_default');
        $this->Template->linkPageTitle = $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTINTERNAL.linkPageTitle', [], 'contao_default');

        // articles
        $this->Template->modArticleUrl = System::getContainer()->get('router')->generate('contao_backend', ['do' => 'article']);
        $this->Template->linkArticleText = $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTINTERNAL.linkArticleText', [], 'contao_default');
        $this->Template->linkArticleTitle = $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTINTERNAL.linkArticleTitle', [], 'contao_default');

        // news
        $this->Template->modNewsUrl = System::getContainer()->get('router')->generate('contao_backend', ['do' => 'news']);
        $this->Template->linkNewsText = $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTINTERNAL.linkNewsText', [], 'contao_default');
        $this->Template->linkNewsTitle = $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTINTERNAL.linkNewsTitle', [], 'contao_default');
        $this->Template->isNewsInstalled = $config->getSgInstallComplete() && $config->getSgBlog()->getSgInstallComplete();
        $this->Template->msgBlogNotInstalled = $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTINTERNAL.msgBlogNotInstalled', [], 'contao_default');

        // files
        $this->Template->modFilesUrl = System::getContainer()->get('router')->generate('contao_backend', ['do' => 'files']);
        $this->Template->linkFilesText = $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTINTERNAL.linkFilesText', [], 'contao_default');
        $this->Template->linkFilesTitle = $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTINTERNAL.linkFilesTitle', [], 'contao_default');
    }
}
