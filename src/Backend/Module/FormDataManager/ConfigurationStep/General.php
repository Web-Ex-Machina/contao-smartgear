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

namespace WEM\SmartgearBundle\Backend\Module\FormDataManager\ConfigurationStep;

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
    /** @var TranslatorInterface */
    protected $translator;
    /** @var ConfigurationManager */
    protected $configurationManager;
    /** @var CommandUtil */
    protected $commandUtil;
    /** @var HtmlDecoder */
    protected $htmlDecoder;
    /** @var string */
    protected $language;

    public function __construct(
        string $module,
        string $type,
        TranslatorInterface $translator,
        ConfigurationManager $configurationManager,
        CommandUtil $commandUtil,
        HtmlDecoder $htmlDecoder
    ) {
        parent::__construct($module, $type);
        $this->translator = $translator;
        $this->configurationManager = $configurationManager;
        $this->commandUtil = $commandUtil;
        $this->htmlDecoder = $htmlDecoder;
        $this->language = \Contao\BackendUser::getInstance()->language;

        $this->title = $this->translator->trans('WEMSG.FDM.INSTALL_GENERAL.title', [], 'contao_default');
        /** @var FormDataManagerConfig */
        $config = $this->configurationManager->load()->getSgFormDataManager();
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

    protected function updateFormContactFormIfInstalled(): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FormContactConfig */
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
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FormDataManagerConfig */
        $formDataManagerConfig = $config->getSgFormDataManager();

        $formDataManagerConfig
            ->setSgInstallComplete(true)
        ;

        $config->setSgFormDataManager($formDataManagerConfig);

        $this->configurationManager->save($config);
    }

    protected function updateUserGroups(): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FormDataManagerConfig */
        $formDataManagerConfig = $config->getSgFormDataManager();
        $this->updateUserGroup(UserGroupModel::findOneById($config->getSgUserGroupRedactors()), $formDataManagerConfig);
        $this->updateUserGroup(UserGroupModel::findOneById($config->getSgUserGroupAdministrators()), $formDataManagerConfig);
    }

    protected function updateUserGroup(UserGroupModel $objUserGroup, FormDataManagerConfig $formDataManagerConfig): void
    {
        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);

        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->save();
    }
}
