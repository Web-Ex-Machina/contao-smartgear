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

namespace WEM\SmartgearBundle\Backend\Module\FormDataManager\ResetStep;

use Contao\Input;
use Contao\UserGroupModel;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Backend\AbstractStep;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\UserGroupModelUtil;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Config\Module\FormDataManager\FormDataManager as FormDataManagerConfig;
use WEM\SmartgearBundle\Model\FormStorage;

class General extends AbstractStep
{
    /** @var ConfigurationManager */
    protected $configurationManager;

    protected $strTemplate = 'be_wem_sg_install_block_reset_step_fdm_general';

    public function __construct(
        string $module,
        string $type,
        TranslatorInterface $translator,
        ConfigurationManager $configurationManager
    ) {
        parent::__construct($module, $type);
        $this->translator = $translator;
        $this->configurationManager = $configurationManager;

        $this->title = $this->translator->trans('WEMSG.FDM.RESET.title', [], 'contao_default');

        $resetOptions = [
            [
                'value' => FormDataManagerConfig::ARCHIVE_MODE_DELETE,
                'label' => $this->translator->trans('WEMSG.FDM.RESET.deleteModeDeleteLabel', [], 'contao_default'),
            ],
        ];

        $this->addSelectField('deleteMode', $this->translator->trans('WEMSG.FDM.RESET.deleteModeLabel', [], 'contao_default'), $resetOptions, FormDataManagerConfig::ARCHIVE_MODE_DELETE, true);
    }

    public function isStepValid(): bool
    {
        // check if the step is correct
        if (!\in_array(Input::post('deleteMode'), FormDataManagerConfig::ARCHIVE_MODES_ALLOWED, true)) {
            throw new \InvalidArgumentException($this->translator->trans('WEMSG.FDM.RESET.deleteModeUnknown', [], 'contao_default'));
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
        /** @var FormDataManagerConfig */
        $formDataManagerConfig = $config->getSgFormDataManager();
        $archiveTimestamp = time();

        switch ($deleteMode) {
            case FormDataManagerConfig::ARCHIVE_MODE_DELETE:
                $this->archiveModeDelete($formDataManagerConfig);
            break;
            default:
                throw new \InvalidArgumentException($this->translator->trans('WEMSG.FDM.RESET.deleteModeUnknown', [], 'contao_default'));
            break;
        }

        $formDataManagerConfig
            ->setSgInstallComplete(false)
            ->setSgArchived(true)
            ->setSgArchivedMode($deleteMode)
            ->setSgArchivedAt($archiveTimestamp)
        ;

        $config->setSgFormDataManager($formDataManagerConfig);

        $this->configurationManager->save($config);
    }

    protected function archiveModeDelete(FormDataManagerConfig $formDataManagerConfig): FormDataManagerConfig
    {
        FormStorage::deleteAll();

        return $formDataManagerConfig;
    }

    protected function resetUserGroupSettings(): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FormDataManagerConfig */
        $formDataManagerConfig = $config->getSgFormDataManager();

        $this->resetUserGroup(UserGroupModel::findOneById($config->getSgUserGroupWebmasters()), $formDataManagerConfig);
        $this->resetUserGroup(UserGroupModel::findOneById($config->getSgUserGroupAdministrators()), $formDataManagerConfig);
    }

    protected function resetUserGroup(UserGroupModel $objUserGroup, FormDataManagerConfig $formDataManagerConfig): void
    {
        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);

        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->save();
    }
}
