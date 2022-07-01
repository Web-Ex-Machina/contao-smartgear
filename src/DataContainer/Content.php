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

namespace WEM\SmartgearBundle\DataContainer;

use Contao\Backend;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\Image;
use Contao\Input;
use Contao\System;
use tl_content;
use tl_content_calendar;
use tl_content_news;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;

// class Content extends \tl_content
class Content extends Backend
{
    /** @var CoreConfigurationManager */
    private $configManager;
    /** @var Backend */
    private $parent;

    public function __construct()
    {
        parent::__construct();
        $this->configManager = System::getContainer()->get('smartgear.config.manager.core');
        switch (Input::get('do')) {
            case 'news':
                $this->parent = new tl_content_news();
            break;
            case 'calendar':
                $this->parent = new tl_content_calendar();
            break;
            default:
                $this->parent = new tl_content();
        }
    }

    /**
     * Check permissions to edit table tl_content.
     *
     * @throws AccessDeniedException
     */
    public function checkPermission(): void
    {
        // parent::checkPermission();
        $this->parent->checkPermission();

        // Check current action
        switch (Input::get('act')) {
            case 'delete':
                if ($this->isItemUsedBySmartgear((int) Input::get('id'))) {
                    throw new AccessDeniedException('Not enough permissions to '.Input::get('act').' content ID '.Input::get('id').'.');
                }
            break;
        }
    }

    /**
     * Return the delete content button.
     *
     * @param array  $row
     * @param string $href
     * @param string $label
     * @param string $title
     * @param string $icon
     * @param string $attributes
     *
     * @return string
     */
    public function deleteItem($row, $href, $label, $title, $icon, $attributes)
    {
        if ($this->isItemUsedBySmartgear((int) $row['id'])) {
            return Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
        }

        // return parent::deleteElement($row, $href, $label, $title, $icon, $attributes);
        if (method_exists($this->parent, 'deleteElement')) {
            return $this->parent->deleteElement($row, $href, $label, $title, $icon, $attributes);
        }

        return (new tl_content())->deleteElement($row, $href, $label, $title, $icon, $attributes);
    }

    /**
     * Check if the content is being used by Smartgear.
     *
     * @param int $id content's ID
     */
    protected function isItemUsedBySmartgear(int $id): bool
    {
        try {
            /** @var CoreConfig */
            $config = $this->configManager->load();
            if ($config->getSgInstallComplete()
            && (
                $id === (int) $config->getSgContent404Headline()
                || $id === (int) $config->getSgContent404Sitemap()
                || $id === (int) $config->getSgContentLegalNotice()
                || $id === (int) $config->getSgContentPrivacyPolitics()
                || $id === (int) $config->getSgContentSitemap()
            )
            ) {
                return true;
            }
            $blogConfig = $config->getSgBlog();
            if ($blogConfig->getSgInstallComplete()
            && (
                $id === (int) $blogConfig->getSgContentHeadline()
                || $id === (int) $blogConfig->getSgContentList()
            )
            ) {
                return true;
            }
            $eventsConfig = $config->getSgEvents();
            if ($eventsConfig->getSgInstallComplete()
            && (
                $id === (int) $eventsConfig->getSgContentHeadline()
                || $id === (int) $eventsConfig->getSgContentList()
            )
            ) {
                return true;
            }
            $faqConfig = $config->getSgFaq();
            if ($faqConfig->getSgInstallComplete() && $id === (int) $faqConfig->getSgContent()) {
                return true;
            }
            $formContactConfig = $config->getSgFormContact();
            if ($formContactConfig->getSgInstallComplete()
            && (
                $id === (int) $formContactConfig->getSgContentHeadlineArticleForm()
                || $id === (int) $formContactConfig->getSgContentFormArticleForm()
                || $id === (int) $formContactConfig->getSgContentHeadlineArticleFormSent()
                || $id === (int) $formContactConfig->getSgContentTextArticleFormSent()
            )
            ) {
                return true;
            }
            $extranetConfig = $config->getSgExtranet();
            if ($extranetConfig->getSgInstallComplete()
            && (
                $id === (int) $extranetConfig->getSgContentArticleExtranetHeadline()
                || $id === (int) $extranetConfig->getSgContentArticleExtranetModuleLoginGuests()
                || $id === (int) $extranetConfig->getSgContentArticleExtranetGridStartA()
                || $id === (int) $extranetConfig->getSgContentArticleExtranetGridStartB()
                || $id === (int) $extranetConfig->getSgContentArticleExtranetModuleLoginLogged()
                || $id === (int) $extranetConfig->getSgContentArticleExtranetModuleNav()
                || $id === (int) $extranetConfig->getSgContentArticleExtranetGridStopB()
                || $id === (int) $extranetConfig->getSgContentArticleExtranetText()
                || $id === (int) $extranetConfig->getSgContentArticleExtranetGridStopA()
                || $id === (int) $extranetConfig->getSgContentArticle401Headline()
                || $id === (int) $extranetConfig->getSgContentArticle401Text()
                || $id === (int) $extranetConfig->getSgContentArticle401ModuleLoginGuests()
                || $id === (int) $extranetConfig->getSgContentArticle403Headline()
                || $id === (int) $extranetConfig->getSgContentArticle403Text()
                || $id === (int) $extranetConfig->getSgContentArticle403Hyperlink()
                || $id === (int) $extranetConfig->getSgContentArticleContentHeadline()
                || $id === (int) $extranetConfig->getSgContentArticleContentText()
                || $id === (int) $extranetConfig->getSgContentArticleDataHeadline()
                || $id === (int) $extranetConfig->getSgContentArticleDataModuleData()
                || $id === (int) $extranetConfig->getSgContentArticleDataHeadlineCloseAccount()
                || $id === (int) $extranetConfig->getSgContentArticleDataTextCloseAccount()
                || $id === (int) $extranetConfig->getSgContentArticleDataModuleCloseAccount()
                || $id === (int) $extranetConfig->getSgContentArticleDataConfirmHeadline()
                || $id === (int) $extranetConfig->getSgContentArticleDataConfirmText()
                || $id === (int) $extranetConfig->getSgContentArticleDataConfirmHyperlink()
                || $id === (int) $extranetConfig->getSgContentArticlePasswordHeadline()
                || $id === (int) $extranetConfig->getSgContentArticlePasswordModulePassword()
                || $id === (int) $extranetConfig->getSgContentArticlePasswordConfirmHeadline()
                || $id === (int) $extranetConfig->getSgContentArticlePasswordConfirmText()
                || $id === (int) $extranetConfig->getSgContentArticlePasswordValidateHeadline()
                || $id === (int) $extranetConfig->getSgContentArticlePasswordValidateModulePassword()
                || $id === (int) $extranetConfig->getSgContentArticleLogoutModuleLogout()
                || $id === (int) $extranetConfig->getSgContentArticleSubscribeHeadline()
                || $id === (int) $extranetConfig->getSgContentArticleSubscribeModuleSubscribe()
                || $id === (int) $extranetConfig->getSgContentArticleSubscribeConfirmHeadline()
                || $id === (int) $extranetConfig->getSgContentArticleSubscribeConfirmText()
                || $id === (int) $extranetConfig->getSgContentArticleSubscribeValidateHeadline()
                || $id === (int) $extranetConfig->getSgContentArticleSubscribeValidateText()
                || $id === (int) $extranetConfig->getSgContentArticleSubscribeValidateModuleLoginGuests()
                || $id === (int) $extranetConfig->getSgContentArticleUnsubscribeHeadline()
                || $id === (int) $extranetConfig->getSgContentArticleUnsubscribeText()
                || $id === (int) $extranetConfig->getSgContentArticleUnsubscribeHyperlink()
            )
            ) {
                return true;
            }
        } catch (\Exception $e) {
        }

        return false;
    }
}
