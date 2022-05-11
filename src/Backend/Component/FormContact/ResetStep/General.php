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

namespace WEM\SmartgearBundle\Backend\Component\FormContact\ResetStep;

use Contao\FormFieldModel;
use Contao\FormModel;
use Contao\Input;
use Contao\PageModel;
use Contao\UserGroupModel;
use NotificationCenter\Model\Language as NotificationLanguageModel;
use NotificationCenter\Model\Message as NotificationMessageModel;
use NotificationCenter\Model\Notification as NotificationModel;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Backend\AbstractStep;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\UserGroupModelUtil;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Config\Component\FormContact\FormContact as FormContactConfig;

class General extends AbstractStep
{
    /** @var ConfigurationManager */
    protected $configurationManager;

    protected $strTemplate = 'be_wem_sg_install_block_reset_step_formcontact_general';

    public function __construct(
        string $module,
        string $type,
        TranslatorInterface $translator,
        ConfigurationManager $configurationManager
    ) {
        parent::__construct($module, $type);
        $this->translator = $translator;
        $this->configurationManager = $configurationManager;

        $this->title = $this->translator->trans('WEMSG.FORMCONTACT.RESET.title', [], 'contao_default');

        $resetOptions = [
            [
                'value' => FormContactConfig::ARCHIVE_MODE_ARCHIVE,
                'label' => $this->translator->trans('WEMSG.FORMCONTACT.RESET.deleteModeArchiveLabel', [], 'contao_default'),
            ],
            [
                'value' => FormContactConfig::ARCHIVE_MODE_KEEP,
                'label' => $this->translator->trans('WEMSG.FORMCONTACT.RESET.deleteModeKeepLabel', [], 'contao_default'),
            ],
            [
                'value' => FormContactConfig::ARCHIVE_MODE_DELETE,
                'label' => $this->translator->trans('WEMSG.FORMCONTACT.RESET.deleteModeDeleteLabel', [], 'contao_default'),
            ],
        ];

        $this->addSelectField('deleteMode', $this->translator->trans('WEMSG.FORMCONTACT.RESET.deleteModeLabel', [], 'contao_default'), $resetOptions, FormContactConfig::ARCHIVE_MODE_ARCHIVE, true);
    }

    public function isStepValid(): bool
    {
        // check if the step is correct
        if (!\in_array(Input::post('deleteMode'), FormContactConfig::ARCHIVE_MODES_ALLOWED, true)) {
            throw new \InvalidArgumentException($this->translator->trans('WEMSG.FORMCONTACT.RESET.deleteModeUnknown', [], 'contao_default'));
        }

        return true;
    }

    public function do(): void
    {
        // do what is meant to be done in this step
        $this->resetUserGroupSettings();
        $this->reset(Input::post('deleteMode'));
    }

    protected function reset(string $deleteMode): void
    {
        // reset everything except what we wanted to keep
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FormContactConfig */
        $formContactConfig = $config->getSgFormContact();
        $archiveTimestamp = time();

        switch ($deleteMode) {
            case FormContactConfig::ARCHIVE_MODE_ARCHIVE:
                $objFormContact = FormModel::findById($formContactConfig->getSgFormContact());
                $objFormContact->title = sprintf('%s (Archive-%s)', $objFormContact->title, (string) $archiveTimestamp);
                $objFormContact->save();

                $objNotification = NotificationModel::findOneById($formContactConfig->getSgNotification());
                $objNotification->title = sprintf('%s (Archive-%s)', $objNotification->title, (string) $archiveTimestamp);
                $objNotification->save();

                $objNotificationMessageUser = NotificationMessageModel::findOneById($formContactConfig->getSgNotificationMessageUser());
                $objNotificationMessageUser->title = sprintf('%s (Archive-%s)', $objNotificationMessageUser->title, (string) $archiveTimestamp);
                $objNotificationMessageUser->save();

                $objNotificationMessageAdmin = NotificationMessageModel::findOneById($formContactConfig->getSgNotificationMessageAdmin());
                $objNotificationMessageAdmin->title = sprintf('%s (Archive-%s)', $objNotificationMessageAdmin->title, (string) $archiveTimestamp);
                $objNotificationMessageAdmin->save();

            break;
            case FormContactConfig::ARCHIVE_MODE_KEEP:
            break;
            case FormContactConfig::ARCHIVE_MODE_DELETE:
                $objFormContact = FormModel::findById($formContactConfig->getSgFormContact());
                $objFormContact->delete();

                $objField = FormFieldModel::findByIdOrAlias($formContactConfig->getSgFieldName());
                $objField->delete();

                $objField = FormFieldModel::findByIdOrAlias($formContactConfig->getSgFieldEmail());
                $objField->delete();

                $objField = FormFieldModel::findByIdOrAlias($formContactConfig->getSgFieldMessage());
                $objField->delete();

                $objField = FormFieldModel::findByIdOrAlias($formContactConfig->getSgFieldCaptcha());
                $objField->delete();

                $objField = FormFieldModel::findByIdOrAlias($formContactConfig->getSgFieldSubmit());
                $objField->delete();

                $objNotification = NotificationModel::findOneById($formContactConfig->getSgNotification());
                $objNotification->delete();

                $objNotificationMessageUser = NotificationMessageModel::findOneById($formContactConfig->getSgNotificationMessageUser());
                $objNotificationMessageUser->delete();

                $objNotificationMessageUserLanguage = NotificationLanguageModel::findOneById($formContactConfig->getSgNotificationMessageUserLanguage());
                $objNotificationMessageUserLanguage->delete();

                $objNotificationMessageAdmin = NotificationMessageModel::findOneById($formContactConfig->getSgNotificationMessageAdmin());
                $objNotificationMessageAdmin->delete();

                $objNotificationMessageAdminLanguage = NotificationLanguageModel::findOneById($formContactConfig->getSgNotificationMessageAdminLanguage());
                $objNotificationMessageAdminLanguage->delete();

            break;
            default:
                throw new \InvalidArgumentException($this->translator->trans('WEMSG.FORMCONTACT.RESET.deleteModeUnknown', [], 'contao_default'));
            break;
        }

        $objPageForm = PageModel::findById($formContactConfig->getSgPageForm());
        $objPageForm->published = false;
        $objPageForm->save();

        $objPageFormSent = PageModel::findById($formContactConfig->getSgPageFormSent());
        $objPageFormSent->published = false;
        $objPageFormSent->save();

        $formContactConfig->setSgArchived(true)
            ->setSgArchivedMode($deleteMode)
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

        $objUserGroup = UserGroupModel::findOneById($config->getSgUserGroupWebmasters());
        $objUserGroup = $this->resetUserGroupAllowedModules($objUserGroup);
        $objUserGroup = $this->resetUserGroupAllowedForms($objUserGroup, $formContactConfig);
        $objUserGroup = $this->resetUserGroupAllowedFields($objUserGroup);
        $objUserGroup->save();

        $objUserGroup = UserGroupModel::findOneById($config->getSgUserGroupAdministrators());
        $objUserGroup = $this->resetUserGroupAllowedModules($objUserGroup);
        $objUserGroup = $this->resetUserGroupAllowedForms($objUserGroup, $formContactConfig);
        $objUserGroup = $this->resetUserGroupAllowedFields($objUserGroup);
        $objUserGroup->save();
    }

    protected function resetUserGroupAllowedModules(UserGroupModel $objUserGroup): UserGroupModel
    {
        return UserGroupModelUtil::removeAllowedModules($objUserGroup, ['form']);
    }

    protected function resetUserGroupAllowedForms(UserGroupModel $objUserGroup, FormContactConfig $formContactConfig): UserGroupModel
    {
        $objUserGroup = UserGroupModelUtil::removeAllowedForms($objUserGroup, [$formContactConfig->getSgFormContact()]);
        $objUserGroup->newp = null;

        return $objUserGroup;
    }

    protected function resetUserGroupAllowedFields(UserGroupModel $objUserGroup): UserGroupModel
    {
        return UserGroupModelUtil::removeAllowedFieldsByPrefixes($objUserGroup, ['tl_form::']);
    }
}
