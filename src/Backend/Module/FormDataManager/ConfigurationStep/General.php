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
use Contao\UserGroupModel;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Backend\ConfigurationStep;
use WEM\SmartgearBundle\Classes\Command\Util as CommandUtil;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\UserGroupModelUtil;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
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
        $this->updateUserGroups();

        $this->commandUtil->executeCmdPHP('cache:clear');
        $this->commandUtil->executeCmdPHP('contao:symlinks');
    }

    protected function updateModuleConfigurationAfterGenerations(): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FormDataManagerConfig */
        $extranetConfig = $config->getSgFormDataManager();

        $extranetConfig
            ->setSgInstallComplete(true)
        ;

        $config->setSgFormDataManager($extranetConfig);

        $this->configurationManager->save($config);
    }

    protected function updateUserGroups(): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FormDataManagerConfig */
        $extranetConfig = $config->getSgFormDataManager();
        $this->updateUserGroup(UserGroupModel::findOneById($config->getSgUserGroupWebmasters()), $extranetConfig);
        $this->updateUserGroup(UserGroupModel::findOneById($config->getSgUserGroupAdministrators()), $extranetConfig);
    }

    protected function updateUserGroup(UserGroupModel $objUserGroup, FormDataManagerConfig $extranetConfig): void
    {
        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);

        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->save();
    }
}
