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

namespace WEM\SmartgearBundle\Backend\Component\FormContact\EventListener;

use WEM\SmartgearBundle\Classes\Backend\Component\EventListener\ReplaceInsertTagsListener as AbstractReplaceInsertTagsListener;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Config\Component\FormContact\FormContact as FormContactConfig;

class ReplaceInsertTagsListener extends AbstractReplaceInsertTagsListener
{
    /** @var CoreConfigurationManager */
    protected $coreConfigurationManager;

    public function __construct(
        CoreConfigurationManager $coreConfigurationManager
    ) {
        $this->coreConfigurationManager = $coreConfigurationManager;
    }

    /**
     * Handles Smartgear insert tags.
     *
     * @see https://docs.contao.org/dev/reference/hooks/replaceInsertTags/
     *
     * @param string $insertTag   the unknown insert tag
     * @param bool   $useCache    indicates if we are supposed to cache
     * @param string $cachedValue the cached replacement for this insert tag (if there is any)
     * @param array  $flags       an array of flags used with this insert tag
     * @param array  $tags        contains the result of spliting the page’s content in order to replace the insert tags
     * @param array  $cache       the cached replacements of insert tags found on the page so far
     * @param int    $_rit        counter used while iterating over the parts in $tags
     * @param int    $_cnt        number of elements in $tags
     *
     * @return string|false if the tags isn't managed by this class, return false
     */
    public function onReplaceInsertTags(
        string $insertTag,
        bool $useCache,
        string $cachedValue,
        array $flags,
        array $tags,
        array $cache,
        int $_rit,
        int $_cnt
    ) {
        $elements = explode('::', $insertTag);
        $key = strtolower($elements[0]);
        if ('sg' === $key && 'formContact' === substr($elements[1], 0, 11)) {
            /** @var CoreConfig */
            $config = $this->coreConfigurationManager->load();
            /** @var FormContactConfig */
            $formContactConfig = $config->getSgFormContact();

            if (!$formContactConfig->getSgInstallComplete()) {
                return static::NOT_HANDLED;
            }

            switch ($elements[1]) {
                case 'formContact_installComplete':
                    return $formContactConfig->getSgInstallComplete() ? '1' : '0';
                break;
                case 'formContact_formContactTitle':
                    return $formContactConfig->getSgFormContactTitle();
                break;
                case 'formContact_pageTitle':
                    return $formContactConfig->getSgPageTitle();
                break;
                case 'formContact_pageForm':
                    return $formContactConfig->getSgPageForm();
                break;
                case 'formContact_pageFormSent':
                    return $formContactConfig->getSgPageFormSent();
                break;
                case 'formContact_articleForm':
                    return $formContactConfig->getSgArticleForm();
                break;
                case 'formContact_articleFormSent':
                    return $formContactConfig->getSgArticleFormSent();
                break;
                case 'formContact_contentHeadlineArticleForm':
                    return $formContactConfig->getSgContentHeadlineArticleForm();
                break;
                case 'formContact_contentFormArticleForm':
                    return $formContactConfig->getSgContentFormArticleForm();
                break;
                case 'formContact_contentHeadlineArticleFormSent':
                    return $formContactConfig->getSgContentHeadlineArticleFormSent();
                break;
                case 'formContact_contentTextArticleFormSent':
                    return $formContactConfig->getSgContentTextArticleFormSent();
                break;
                case 'formContact_formContact':
                    return $formContactConfig->getSgFormContact();
                break;
                case 'formContact_fieldName':
                    return $formContactConfig->getSgFieldName();
                break;
                case 'formContact_fieldEmail':
                    return $formContactConfig->getSgFieldEmail();
                break;
                case 'formContact_fieldMessage':
                    return $formContactConfig->getSgFieldMessage();
                break;
                case 'formContact_fieldConsentDataTreatment':
                    return $formContactConfig->getSgFieldConsentDataTreatment();
                break;
                case 'formContact_fieldConsentDataSave':
                    return $formContactConfig->getSgFieldConsentDataSave();
                break;
                case 'formContact_fieldCaptcha':
                    return $formContactConfig->getSgFieldCaptcha();
                break;
                case 'formContact_fieldSubmit':
                    return $formContactConfig->getSgFieldSubmit();
                break;
                case 'formContact_notification':
                    return $formContactConfig->getSgNotification();
                break;
                case 'formContact_notificationMessageUser':
                    return $formContactConfig->getSgNotificationMessageUser();
                break;
                case 'formContact_notificationMessageAdmin':
                    return $formContactConfig->getSgNotificationMessageAdmin();
                break;
                case 'formContact_notificationMessageUserLanguage':
                    return $formContactConfig->getSgNotificationMessageUserLanguage();
                break;
                case 'formContact_notificationMessageAdminLanguage':
                    return $formContactConfig->getSgNotificationMessageAdminLanguage();
                break;
                case 'formContact_archived':
                    return $formContactConfig->getSgArchived() ? '1' : '0';
                break;
                case 'formContact_archivedAt':
                    return $formContactConfig->getSgArchivedAt();
                break;
                case 'formContact_archivedMode':
                    return $formContactConfig->getSgArchivedMode();
                break;
                default:
                return static::NOT_HANDLED;
            }
        }

        return static::NOT_HANDLED;
    }
}
