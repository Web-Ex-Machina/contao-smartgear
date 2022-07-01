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

namespace WEM\SmartgearBundle\EventListener\PersonalDataManager;

use Contao\MemberGroupModel;
use Contao\Model;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\PersonalDataManagerBundle\Model\PersonalData;

class UiListener
{
    /** @var TranslatorInterface */
    protected $translator;

    public function __construct(
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
    }

    public function renderSingleItemTitle(int $pid, string $ptable, array $personalDatas, Model $originalModel, string $buffer): string
    {
        switch ($ptable) {
            case 'tl_member':
                $buffer = 'Member';
            break;
        }

        return $buffer;
    }

    public function renderSingleItemBodyOriginalModelSingle(int $pid, string $ptable, string $field, $value, array $personalDatas, Model $originalModel, string $buffer): string
    {
        switch ($ptable) {
            case 'tl_member':
                switch ($field) {
                    case 'id':
                    case 'tstamp':
                    case 'password':
                    case 'dateAdded':
                    case 'lastLogin':
                    case 'loginAttempts':
                    case 'locked':
                    case 'session':
                    case 'secret':
                    case 'backupCodes':
                    case 'trustedTokenVersion':
                    case 'currentLogin':
                        $buffer = '';
                    break;
                }
            break;
        }

        return $buffer;
    }

    public function renderSingleItemBodyOriginalModelSingleFieldValue(int $pid, string $ptable, string $field, $value, array $personalDatas, Model $originalModel, string $buffer): string
    {
        if (empty($buffer)) {
            return sprintf('<i>%s</i>', $this->translator->trans('WEM.SMARTGEAR.DEFAULT.NotFilled', [], 'contao_default'));
        }

        switch ($ptable) {
            case 'tl_member':
                switch ($field) {
                    case 'login':
                        $buffer = sprintf('<input type="checkbox" readonly %s />', true === (bool) $value ? 'checked' : '');
                    break;
                    case 'groups':
                        $groupIds = unserialize($value);
                        $buffer = '<ul>';
                        foreach ($groupIds as $groupId) {
                            $objGroup = MemberGroupModel::findById($groupId);
                            $buffer .= sprintf('<li>- %s</li>', null !== $objGroup ? $objGroup->name : $this->translator->trans('WEM.SMARTGEAR.DEFAULT.elementUnknown', [], 'contao_default'));
                        }
                        $buffer .= '<ul>';
                    break;
                }
            break;
        }

        return $buffer;
    }

    public function renderSingleItemBodyPersonalDataSingleFieldValue(int $pid, string $ptable, PersonalData $personalData, array $personalDatas, Model $originalModel, string $buffer): string
    {
        if (empty($buffer)) {
            return sprintf('<i>%s</i>', $this->translator->trans('WEM.SMARTGEAR.DEFAULT.NotFilled', [], 'contao_default'));
        }

        return $buffer;
    }
}
