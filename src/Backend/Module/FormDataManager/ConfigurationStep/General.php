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

namespace WEM\SmartgearBundle\Backend\Module\FormDataManager\ConfigurationStep;

use Contao\BackendUser;
use Contao\CoreBundle\String\HtmlDecoder;
use Contao\FormFieldModel;
use Contao\FormModel;
use Contao\UserGroupModel;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Backend\ConfigurationStep;
use WEM\SmartgearBundle\Classes\Command\Util as CommandUtil;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\UserGroupModelUtil;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Config\Component\FormContact\FormContact as FormContactConfig;
use WEM\SmartgearBundle\Config\Module\FormDataManager\FormDataManager as FormDataManagerConfig;

class General extends ConfigurationStep
{






    public function __construct(
        protected string               $language,
        string                         $module,
        string                         $type,
        protected TranslatorInterface  $translator,
        protected ConfigurationManager $configurationManager,
        protected CommandUtil          $commandUtil,
        protected HtmlDecoder          $htmlDecoder
    ) {
        parent::__construct($module, $type);
        $this->language = BackendUser::getInstance()->language;

        $this->title = $this->translator->trans('WEMSG.FDM.INSTALL_GENERAL.title', [], 'contao_default');
        $this->configurationManager->load()->getSgFormDataManager();
    }

    public function isStepValid(): bool
    {
        return true;
    }

    public function do(): void
    {
        $this->updateModuleConfigurationAfterGenerations();
        $this->updateFormContactFormIfInstalled();
        $this->updateUserGroups();

        $this->commandUtil->executeCmdPHP('cache:clear');
        $this->commandUtil->executeCmdPHP('contao:symlinks');
    }

    public function updateUserGroups(): void
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $formDataManagerConfig = $config->getSgFormDataManager();
        $this->updateUserGroup(UserGroupModel::findOneById($config->getSgUserGroupRedactors()), $formDataManagerConfig);
        $this->updateUserGroup(UserGroupModel::findOneById($config->getSgUserGroupAdministrators()), $formDataManagerConfig);
    }

    protected function updateFormContactFormIfInstalled(): void
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $formContactConfig = $config->getSgFormContact();

        if ($formContactConfig->getSgInstallComplete()) {
            $objForm = FormModel::findById($formContactConfig->getSgFormContact());
            if ($objForm) {
                $objForm->storeViaFormDataManager = true;
                $objForm->save();
            }

            $objFormFieldName = FormFieldModel::findById($formContactConfig->getSgFieldName());
            if ($objFormFieldName) {
                $objFormFieldName->contains_personal_data = true;
                $objFormFieldName->save();
            }

            $objFormFieldMessage = FormFieldModel::findById($formContactConfig->getSgFieldMessage());
            if ($objFormFieldMessage) {
                $objFormFieldMessage->contains_personal_data = true;
                $objFormFieldMessage->save();
            }

            $objConsentDataSave = FormFieldModel::findById($formContactConfig->getSgFieldConsentDataSave());
            if ($objConsentDataSave) {
                $objConsentDataSave->invisible = false;
                $objConsentDataSave->save();
            }
        }
    }

    protected function updateModuleConfigurationAfterGenerations(): void
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $formDataManagerConfig = $config->getSgFormDataManager();

        $formDataManagerConfig
            ->setSgInstallComplete(true)
            ->setSgArchived(false)
            ->setSgArchivedMode(FormDataManagerConfig::ARCHIVE_MODE_EMPTY)
            ->setSgArchivedAt(0)
        ;

        $config->setSgFormDataManager($formDataManagerConfig);

        $this->configurationManager->save($config);
    }

    protected function updateUserGroup(UserGroupModel $objUserGroup, FormDataManagerConfig $formDataManagerConfig): void
    {
        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);
        $userGroupManipulator
            ->addAllowedModules(['wem_sg_form_data_manager'])
        ;
        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->save();
    }
}
