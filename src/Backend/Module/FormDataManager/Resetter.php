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
        // reset everything except what we wanted to keep
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FormDataManagerConfig */
        $formDataManagerConfig = $config->getSgFormDataManager();
        if (!$formDataManagerConfig) {
            return;
        }
        $this->resetUserGroupSettings();
        $archiveTimestamp = time();

        switch ($mode) {
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

        return $formDataManagerConfig;
    }

    protected function resetUserGroupSettings(): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FormDataManagerConfig */
        $formDataManagerConfig = $config->getSgFormDataManager();

        $this->resetUserGroup(UserGroupModel::findOneById($config->getSgUserGroupRedactors()), $formDataManagerConfig);
        $this->resetUserGroup(UserGroupModel::findOneById($config->getSgUserGroupAdministrators()), $formDataManagerConfig);
    }

    protected function resetUserGroup(UserGroupModel $objUserGroup, FormDataManagerConfig $formDataManagerConfig): void
    {
        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);

        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->save();
    }
}
