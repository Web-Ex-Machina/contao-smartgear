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

namespace WEM\SmartgearBundle\Backend\Component\FormContact;

use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\FormFieldModel;
use Contao\FormModel;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\UserGroupModel;
use NotificationCenter\Model\Language as NotificationLanguageModel;
use NotificationCenter\Model\Message as NotificationMessageModel;
use NotificationCenter\Model\Notification as NotificationModel;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Backend\Resetter as BackendResetter;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\UserGroupModelUtil;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Config\Component\FormContact\FormContact as FormContactConfig;
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
        /** @var FormContactConfig */
        $formContactConfig = $config->getSgFormContact();
        $archiveTimestamp = time();

        switch ($mode) {
            case FormContactConfig::ARCHIVE_MODE_ARCHIVE:
                $objFormContact = FormModel::findById($formContactConfig->getSgFormContact());
                if ($objFormContact) {
                    $objFormContact->title = sprintf('%s (Archive-%s)', $objFormContact->title, (string) $archiveTimestamp);
                    $objFormContact->save();
                }

                $objNotification = NotificationModel::findOneById($formContactConfig->getSgNotification());
                if ($objNotification) {
                    $objNotification->title = sprintf('%s (Archive-%s)', $objNotification->title, (string) $archiveTimestamp);
                    $objNotification->save();
                }

                $objNotificationMessageUser = NotificationMessageModel::findOneById($formContactConfig->getSgNotificationMessageUser());
                if ($objNotificationMessageUser) {
                    $objNotificationMessageUser->title = sprintf('%s (Archive-%s)', $objNotificationMessageUser->title, (string) $archiveTimestamp);
                    $objNotificationMessageUser->save();
                }

                $objNotificationMessageAdmin = NotificationMessageModel::findOneById($formContactConfig->getSgNotificationMessageAdmin());
                if ($objNotificationMessageAdmin) {
                    $objNotificationMessageAdmin->title = sprintf('%s (Archive-%s)', $objNotificationMessageAdmin->title, (string) $archiveTimestamp);
                    $objNotificationMessageAdmin->save();
                }

                $objPageForm = PageModel::findById($formContactConfig->getSgPageForm());
                if ($objPageForm) {
                    $objPageForm->published = false;
                    $objPageForm->save();
                }

                $objPageFormSent = PageModel::findById($formContactConfig->getSgPageFormSent());
                if ($objPageFormSent) {
                    $objPageFormSent->published = false;
                    $objPageFormSent->save();
                }

                foreach ($formContactConfig->getContaoArticlesIds() as $id) {
                    $objArticle = ArticleModel::findByPk($id);
                    if ($objArticle) {
                        $objArticle->published = false;
                        $objArticle->title = sprintf('%s (Archive-%s)', $objArticle->title, (string) $archiveTimestamp);
                        $objArticle->save();
                    }
                }

                foreach ($formContactConfig->getContaoModulesIds() as $id) {
                    $objModule = ModuleModel::findByPk($id);
                    if ($objModule) {
                        $objModule->published = false;
                        $objModule->title = sprintf('%s (Archive-%s)', $objModule->title, (string) $archiveTimestamp);
                        $objModule->save();
                    }
                }
            break;
            case FormContactConfig::ARCHIVE_MODE_KEEP:
            break;
            case FormContactConfig::ARCHIVE_MODE_DELETE:
                $objFormContact = FormModel::findById($formContactConfig->getSgFormContact());
                if ($objFormContact) {
                    $objFormContact->delete();
                }

                $objField = FormFieldModel::findByIdOrAlias($formContactConfig->getSgFieldName());
                if ($objField) {
                    $objField->delete();
                }

                $objField = FormFieldModel::findByIdOrAlias($formContactConfig->getSgFieldEmail());
                if ($objField) {
                    $objField->delete();
                }

                $objField = FormFieldModel::findByIdOrAlias($formContactConfig->getSgFieldMessage());
                if ($objField) {
                    $objField->delete();
                }

                $objField = FormFieldModel::findByIdOrAlias($formContactConfig->getSgFieldCaptcha());
                if ($objField) {
                    $objField->delete();
                }

                $objField = FormFieldModel::findByIdOrAlias($formContactConfig->getSgFieldSubmit());
                if ($objField) {
                    $objField->delete();
                }

                $objNotification = NotificationModel::findOneById($formContactConfig->getSgNotification());
                if ($objNotification) {
                    $objNotification->delete();
                }

                $objNotificationMessageUser = NotificationMessageModel::findOneById($formContactConfig->getSgNotificationMessageUser());
                if ($objNotificationMessageUser) {
                    $objNotificationMessageUser->delete();
                }

                $objNotificationMessageUserLanguage = NotificationLanguageModel::findOneById($formContactConfig->getSgNotificationMessageUserLanguage());
                if ($objNotificationMessageUserLanguage) {
                    $objNotificationMessageUserLanguage->delete();
                }

                $objNotificationMessageAdmin = NotificationMessageModel::findOneById($formContactConfig->getSgNotificationMessageAdmin());
                if ($objNotificationMessageAdmin) {
                    $objNotificationMessageAdmin->delete();
                }

                $objNotificationMessageAdminLanguage = NotificationLanguageModel::findOneById($formContactConfig->getSgNotificationMessageAdminLanguage());
                if ($objNotificationMessageAdminLanguage) {
                    $objNotificationMessageAdminLanguage->delete();
                }

                $objPageForm = PageModel::findById($formContactConfig->getSgPageForm());
                if ($objPageForm) {
                    $objPageForm->delete();
                }

                $objPageFormSent = PageModel::findById($formContactConfig->getSgPageFormSent());
                if ($objPageFormSent) {
                    $objPageFormSent->delete();
                }

                foreach ($formContactConfig->getContaoArticlesIds() as $id) {
                    $objArticle = ArticleModel::findByPk($id);
                    if ($objArticle) {
                        $objArticle->delete();
                    }
                }

                foreach ($formContactConfig->getContaoContentsIds() as $id) {
                    $objContent = ContentModel::findByPk($id);
                    if ($objContent) {
                        $objContent->delete();
                    }
                }

                foreach ($formContactConfig->getContaoModulesIds() as $id) {
                    $objModule = ModuleModel::findByPk($id);
                    if ($objModule) {
                        $objModule->delete();
                    }
                }
            break;
            default:
                throw new \InvalidArgumentException($this->translator->trans('WEMSG.FORMCONTACT.RESET.deleteModeUnknown', [], 'contao_default'));
            break;
        }

        $formContactConfig->setSgArchived(true)
            ->setSgArchivedMode($mode)
            ->setSgArchivedAt($archiveTimestamp)
        ;

        $config->setSgFormContact($formContactConfig);

        $this->configurationManager->save($config);
    }

    protected function resetUserGroupSettings(): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FormContactConfig */
        $formContactConfig = $config->getSgFormContact();

        $this->resetUserGroup(UserGroupModel::findOneById($config->getSgUserGroupRedactors()), $formContactConfig);
        $this->resetUserGroup(UserGroupModel::findOneById($config->getSgUserGroupAdministrators()), $formContactConfig);
    }

    protected function resetUserGroup(UserGroupModel $objUserGroup, FormContactConfig $formContactConfig): void
    {
        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);
        $userGroupManipulator
            ->removeAllowedModules(['form'])
            ->removeAllowedForms([$formContactConfig->getSgFormContact()])
            ->removeAllowedFormFields(['text', 'textarea', 'captcha', 'submit'])
            ->removeAllowedFieldsByPrefixes(['tl_form::', 'tl_form_field::'])
            ->removeAllowedPagemounts($formContactConfig->getContaoPagesIds())
            ->removeAllowedModules(Module::getTypesByIds($formContactConfig->getContaoModulesIds()))
        ;
        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->formp = null;
        $objUserGroup->save();
    }
}
