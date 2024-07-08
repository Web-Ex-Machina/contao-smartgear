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

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\Model;
use Contao\Model\Collection;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\PersonalDataManagerBundle\Model\PersonalData;
use WEM\SmartgearBundle\Model\FormStorage;
use WEM\SmartgearBundle\Model\FormStorageData;

#[AsHook('exportByPidAndPtableAndEmail','exportByPidAndPtableAndEmail',-1)]
class ExportListener
{
    public function __construct(protected TranslatorInterface $translator)
    {
    }

    public function exportByPidAndPtableAndEmail(int $pid, string $ptable, string $email, ?Collection $pdms): ?Collection
    {
        if ($ptable === FormStorage::getTable()) {
            $arrModels = $pdms instanceof Collection ? $pdms->getModels() : [];
            $formStorageData = FormStorageData::findBy('pid', $pid);
            if ($formStorageData) {
                while ($formStorageData->next()) {
                    $objPersonalData = PersonalData::findOneByPidAndPTableAndEmail((int) $formStorageData->id, FormStorageData::getTable(), $email);
                    if ($objPersonalData instanceof Model) {
                        $arrModels[] = $objPersonalData;
                    }
                }
            }
            
            $pdms = \count($arrModels) > 0 ? new Collection($arrModels, PersonalData::getTable()) : null;
        }

        return $pdms;
    }
}
