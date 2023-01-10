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
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
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
            /** @var CoreConfig */
            $config = $this->configurationManager->load();
        } catch (NotFound $e) {
            return;
        }
        $this->Template->title = $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTINTERNAL.title', [], 'contao_default');
        $links = [];

        // pages
        $links['pages'] = [
            'href' => System::getContainer()->get('router')->generate('contao_backend', ['do' => 'page']),
            'text' => $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTINTERNAL.linkPageText', [], 'contao_default'),
            'title' => $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTINTERNAL.linkPageTitle', [], 'contao_default'),
            'icon' => 'manager.gif',
        ];

        // articles
        $links['articles'] = [
            'href' => System::getContainer()->get('router')->generate('contao_backend', ['do' => 'article']),
            'text' => $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTINTERNAL.linkArticleText', [], 'contao_default'),
            'title' => $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTINTERNAL.linkArticleTitle', [], 'contao_default'),
            'icon' => 'manager.gif',
        ];

        // news
        if ($config->getSgInstallComplete() && $config->getSgBlog()->getSgInstallComplete()) {
            $links['news'] = [
                'href' => System::getContainer()->get('router')->generate('contao_backend', ['do' => 'news']),
                'text' => $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTINTERNAL.linkNewsText', [], 'contao_default'),
                'title' => $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTINTERNAL.linkNewsTitle', [], 'contao_default'),
                'icon' => 'manager.gif',
            ];
        }

        // files
        $links['files'] = [
            'href' => System::getContainer()->get('router')->generate('contao_backend', ['do' => 'files']),
            'text' => $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTINTERNAL.linkFilesText', [], 'contao_default'),
            'title' => $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTINTERNAL.linkFilesTitle', [], 'contao_default'),
            'icon' => 'manager.gif',
        ];

        // events
        if ($config->getSgInstallComplete() && $config->getSgEvents()->getSgInstallComplete()) {
            $links['events'] = [
                'href' => System::getContainer()->get('router')->generate('contao_backend', ['do' => 'calendar']),
                'text' => $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTINTERNAL.linkCalendarText', [], 'contao_default'),
                'title' => $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTINTERNAL.linkCalendarTitle', [], 'contao_default'),
                'icon' => 'manager.gif',
            ];
        }

        // FAQ
        if ($config->getSgInstallComplete() && $config->getSgFaq()->getSgInstallComplete()) {
            $links['faq'] = [
                'href' => System::getContainer()->get('router')->generate('contao_backend', ['do' => 'faq']),
                'text' => $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTINTERNAL.linkFaqText', [], 'contao_default'),
                'title' => $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTINTERNAL.linkFaqTitle', [], 'contao_default'),
                'icon' => 'manager.gif',
            ];
        }

        // contacts
        if ($config->getSgInstallComplete() && $config->getSgFaq()->getSgInstallComplete()) {
            $links['contacts'] = [
                'href' => System::getContainer()->get('router')->generate('contao_backend', ['do' => 'wem_sg_form_data_manager']),
                'text' => $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTINTERNAL.linkContactsText', [], 'contao_default'),
                'title' => $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTINTERNAL.linkContactsTitle', [], 'contao_default'),
                'icon' => 'manager.gif',
            ];
        }
        // extranet
        if ($config->getSgInstallComplete() && $config->getSgFaq()->getSgInstallComplete()) {
            $links['extranet'] = [
                'href' => System::getContainer()->get('router')->generate('contao_backend', ['do' => 'member']),
                'text' => $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTINTERNAL.linkMemberText', [], 'contao_default'),
                'title' => $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTINTERNAL.linkMemberTitle', [], 'contao_default'),
                'icon' => 'manager.gif',
            ];
        }

        $this->Template->links = $links;
    }
}
