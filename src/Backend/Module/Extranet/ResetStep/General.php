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

namespace WEM\SmartgearBundle\Backend\Module\Extranet\ResetStep;

use Contao\ExtranetCategoryModel;
use Contao\FilesModel;
use Contao\Input;
use Contao\PageModel;
use Contao\UserGroupModel;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Backend\AbstractStep;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\UserGroupModelUtil;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Config\Module\Extranet\Extranet as ExtranetConfig;

class General extends AbstractStep
{
    /** @var ConfigurationManager */
    protected $configurationManager;

    protected $strTemplate = 'be_wem_sg_install_block_reset_step_extranet_general';

    public function __construct(
        string $module,
        string $type,
        TranslatorInterface $translator,
        ConfigurationManager $configurationManager
    ) {
        parent::__construct($module, $type);
        $this->translator = $translator;
        $this->configurationManager = $configurationManager;

        $this->title = $this->translator->trans('WEMSG.EXTRANET.RESET.title', [], 'contao_default');

        $resetOptions = [
            [
                'value' => ExtranetConfig::ARCHIVE_MODE_ARCHIVE,
                'label' => $this->translator->trans('WEMSG.EXTRANET.RESET.deleteModeArchiveLabel', [], 'contao_default'),
            ],
            [
                'value' => ExtranetConfig::ARCHIVE_MODE_KEEP,
                'label' => $this->translator->trans('WEMSG.EXTRANET.RESET.deleteModeKeepLabel', [], 'contao_default'),
            ],
            [
                'value' => ExtranetConfig::ARCHIVE_MODE_DELETE,
                'label' => $this->translator->trans('WEMSG.EXTRANET.RESET.deleteModeDeleteLabel', [], 'contao_default'),
            ],
        ];

        $this->addSelectField('deleteMode', $this->translator->trans('WEMSG.EXTRANET.RESET.deleteModeLabel', [], 'contao_default'), $resetOptions, ExtranetConfig::ARCHIVE_MODE_ARCHIVE, true);
    }

    public function isStepValid(): bool
    {
        // check if the step is correct
        if (!\in_array(Input::post('deleteMode'), ExtranetConfig::ARCHIVE_MODES_ALLOWED, true)) {
            throw new \InvalidArgumentException($this->translator->trans('WEMSG.EXTRANET.RESET.deleteModeUnknown', [], 'contao_default'));
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
        /** @var ExtranetConfig */
        $extranetConfig = $config->getSgExtranet();
        $archiveTimestamp = time();

        switch ($deleteMode) {
            case ExtranetConfig::ARCHIVE_MODE_ARCHIVE:
                $objFolder = new \Contao\Folder($extranetConfig->getSgExtranetFolder());
                $objCalendar = ExtranetCategoryModel::findById($extranetConfig->getSgExtranetCategory());

                $objFolder->renameTo(sprintf('files/archives/events-%s', (string) $archiveTimestamp));
                $objCalendar->title = sprintf('%s (Archive-%s)', $objCalendar->title, (string) $archiveTimestamp);
                $objCalendar->save();

            break;
            case ExtranetConfig::ARCHIVE_MODE_KEEP:
            break;
            case ExtranetConfig::ARCHIVE_MODE_DELETE:
                $objFolder = new \Contao\Folder($extranetConfig->getSgExtranetFolder());
                $objCalendar = ExtranetCategoryModel::findById($extranetConfig->getSgExtranetCategory());

                $objFolder->delete();
                $objCalendar->delete();
            break;
            default:
                throw new \InvalidArgumentException($this->translator->trans('WEMSG.EXTRANET.RESET.deleteModeUnknown', [], 'contao_default'));
            break;
        }

        $objPage = PageModel::findById($extranetConfig->getSgPage());
        $objPage->published = false;
        $objPage->save();

        $extranetConfig->setSgArchived(true)
            ->setSgArchivedMode($deleteMode)
            ->setSgArchivedAt($archiveTimestamp)
        ;

        $config->setSgExtranet($extranetConfig);

        $this->configurationManager->save($config);
    }

    protected function resetUserGroupSettings(): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var ExtranetConfig */
        $extranetConfig = $config->getSgExtranet();

        $this->resetUserGroup(UserGroupModel::findOneById($config->getSgUserGroupWebmasters()), $extranetConfig);
        $this->resetUserGroup(UserGroupModel::findOneById($config->getSgUserGroupAdministrators()), $extranetConfig);
    }

    protected function resetUserGroup(UserGroupModel $objUserGroup, ExtranetConfig $extranetConfig): void
    {
        $objFolder = FilesModel::findByPath($extranetConfig->getSgExtranetFolder());
        if (!$objFolder) {
            throw new Exception('Unable to find the folder');
        }

        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);
        $userGroupManipulator
            ->removeAllowedModules(['extranet'])
            ->removeAllowedExtranet([$extranetConfig->getSgExtranetCategory()])
            ->removeAllowedFilemounts([$objFolder->uuid])
            ->removeAllowedFieldsByPrefixes(['tl_extranet::'])
        ;

        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->extranetp = null;
        $objUserGroup->save();
    }
}
