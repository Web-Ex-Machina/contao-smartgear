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

namespace WEM\SmartgearBundle\Backend\Module\FormDataManager;

use Contao\FormFieldModel;
use Contao\FormModel;
use Contao\UserGroupModel;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Backend\Resetter as BackendResetter;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\UserGroupModelUtil;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Config\Module\FormDataManager\FormDataManager as FormDataManagerConfig;
use WEM\SmartgearBundle\Model\FormStorage;
use WEM\SmartgearBundle\Model\FormStorageData;

class Resetter extends BackendResetter
{
    protected string $module = '';

    protected string $type = '';

    protected ConfigurationManager $configurationManager;

    protected TranslatorInterface $translator;

    /**
     * Generic array of logs.
     */
    protected array $logs = [];

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
        // reset everything except what we wanted to keep
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $formDataManagerConfig = $config->getSgFormDataManager();
        if (!$formDataManagerConfig) {
            return;
        }

        $this->resetUserGroupSettings();
        $archiveTimestamp = time();

        match ($mode) {
            FormDataManagerConfig::ARCHIVE_MODE_DELETE => $this->archiveModeDelete($formDataManagerConfig),
            default => throw new \InvalidArgumentException($this->translator->trans('WEMSG.FDM.RESET.deleteModeUnknown', [], 'contao_default')),
        };

        $formDataManagerConfig
            ->setSgInstallComplete(false)
            ->setSgArchived(true)
            ->setSgArchivedMode($mode)
            ->setSgArchivedAt($archiveTimestamp)
        ;

        $config->setSgFormDataManager($formDataManagerConfig);

        $this->configurationManager->save($config);
    }

    protected function archiveModeArchive(FormDataManagerConfig $formDataManagerConfig, int $archiveTimestamp): FormDataManagerConfig
    {
        return $formDataManagerConfig;
    }

    protected function archiveModeDelete(FormDataManagerConfig $formDataManagerConfig): FormDataManagerConfig
    {
        FormStorageData::deleteAll();
        FormStorage::deleteAll();

        $this->formContactUpdate();

        return $formDataManagerConfig;
    }

    protected function formContactUpdate(): void
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $formContactConfig = $config->getSgFormContact();

        if ($formContactConfig->getSgInstallComplete()) {
            $objForm = FormModel::findById($formContactConfig->getSgFormContact());
            if ($objForm) {
                $objForm->storeViaFormDataManager = 0;
                $objForm->save();
            }

            $objFormFieldName = FormFieldModel::findById($formContactConfig->getSgFieldName());
            if ($objFormFieldName) {
                $objFormFieldName->contains_personal_data = 0;
                $objFormFieldName->save();
            }

            $objFormFieldMessage = FormFieldModel::findById($formContactConfig->getSgFieldMessage());
            if ($objFormFieldMessage) {
                $objFormFieldMessage->contains_personal_data = 0;
                $objFormFieldMessage->save();
            }

            $objConsentDataSave = FormFieldModel::findById($formContactConfig->getSgFieldConsentDataSave());
            if ($objConsentDataSave) {
                $objConsentDataSave->invisible = 1;
                $objConsentDataSave->save();
            }
        }
    }

    protected function resetUserGroupSettings(): void
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $formDataManagerConfig = $config->getSgFormDataManager();

        $objGroupRedactors = UserGroupModel::findOneById($config->getSgUserGroupRedactors());
        if ($objGroupRedactors) {
            $this->resetUserGroup($objGroupRedactors, $formDataManagerConfig);
        }

        $objGroupAdministrators = UserGroupModel::findOneById($config->getSgUserGroupAdministrators());
        if ($objGroupAdministrators) {
            $this->resetUserGroup($objGroupAdministrators, $formDataManagerConfig);
        }
    }

    protected function resetUserGroup(UserGroupModel $objUserGroup, FormDataManagerConfig $formDataManagerConfig): void
    {
        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);

        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->save();
    }
}
