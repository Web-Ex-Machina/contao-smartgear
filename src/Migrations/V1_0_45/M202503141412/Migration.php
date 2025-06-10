<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2025 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

namespace WEM\SmartgearBundle\Migrations\V1_0_45\M202503141412;

use Doctrine\DBAL\Connection;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\DirectoriesSynchronizer;
use WEM\SmartgearBundle\Classes\Migration\Result;
use WEM\SmartgearBundle\Classes\Version\Comparator as VersionComparator;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Migrations\V1_0_0\MigrationAbstract;

class Migration extends MigrationAbstract
{
    protected $name = 'Smargear update to v1.0.45';
    protected $description = 'Set Smartgear to version 1.0.45';
    protected $version = '1.0.45';
    protected $translation_key = 'WEMSG.MIGRATIONS.V1_0_45_M202503141412';
    /** @var DirectoriesSynchronizer */
    protected $templatesSmartgearSynchronizer;

    public function __construct(
        Connection $connection,
        TranslatorInterface $translator,
        CoreConfigurationManager $coreConfigurationManager,
        VersionComparator $versionComparator,
        DirectoriesSynchronizer $templatesSmartgearSynchronizer
    ) {
        parent::__construct($connection, $translator, $coreConfigurationManager, $versionComparator);
        $this->templatesSmartgearSynchronizer = $templatesSmartgearSynchronizer;
    }

    public function shouldRun(): Result
    {
        $result = parent::shouldRun();

        if (Result::STATUS_SHOULD_RUN !== $result->getStatus()) {
            return $result;
        }

        $schemaManager = $this->connection->getSchemaManager();

        if (!class_exists('\WEM\SmartgearBundle\Model\FormStorage')
        || !class_exists('\WEM\SmartgearBundle\Model\FormStorageData')
        || !class_exists('\WEM\ContaoFormDataManagerBundle\Model\FormStorage')
        || !class_exists('\WEM\ContaoFormDataManagerBundle\Model\FormStorageData')
        || $schemaManager->tablesExist(['tl_sm_form_storage', 'tl_sm_form_storage_data'])
        ) {
            return (new Result())->setStatus(Result::STATUS_SKIPPED);
        }

        $result
            ->addLog($this->translator->trans('WEMSG.MIGRATIONS.shouldBeRun', [], 'contao_default'))
        ;

        return $result;
    }

    public function do(): Result
    {
        $result = $this->shouldRun();
        if (Result::STATUS_SHOULD_RUN !== $result->getStatus()) {
            return $result;
        }
        try {
            /** @var CoreConfig */
            $coreConfig = $this->coreConfigurationManager->load();

            // copy templates needing to be updated
            $this->templatesSmartgearSynchronizer->synchronize(false);

            $coreConfig->setSgVersion($this->version);

            $logs = $this->migrateFormData();
            foreach ($logs as $log) {
                $result->addLog($log);
            }

            $this->coreConfigurationManager->save($coreConfig);

            $result
                ->setStatus(Result::STATUS_SUCCESS)
                ->addLog($this->translator->trans($this->buildTranslationKey('done'), [], 'contao_default'))
            ;
        } catch (\Exception $e) {
            $result
                ->setStatus(Result::STATUS_FAIL)
                ->addLog($e->getMessage())
            ;
        }

        return $result;
    }

    public function migrateFormData(): array
    {
        $logs = ['Starting migrating rows'];
        // LET FQDN PATH SO THE MIGRATION DO NOT CRASH WHEN THOSE MODELS GET REMOVED
        $formStorages = \WEM\SmartgearBundle\Model\FormStorage::findItems();

        if (!$formStorages) {
            $logs[] = 'No rows to migrate';
            $logs[] = 'End migrating rows';

            return $logs;
        }

        while ($formStorages->next()) {
            $objFS = $formStorages->current();

            $logs[] = 'Migrating FormStorage (old) id '.$objFS->id;

            $formStorageDatas = \WEM\SmartgearBundle\Model\FormStorage::findItems(['pid' => $objFS->id]);

            if (!$formStorageDatas) {
                continue;
            }

            $objFSNew = new \WEM\ContaoFormDataManagerBundle\Model\FormStorage();

            $data = $objFS->row();
            unset($data['id']);
            $objFSNew->setRow($data);
            $objFSNew->save();

            $logs[] = 'FormStorage (new) id '.$objFSNew->id;

            while ($formStorageDatas->next()) {
                $objFSD = $formStorageDatas->current();
                $logs[] = 'Migrating FormStorageData (old) id '.$objFSD->id;

                $objFSDNew = new \WEM\ContaoFormDataManagerBundle\Model\FormStorageData();

                $data2 = $objFSD->row();
                unset($data2['id']);
                $data2['pid'] = $objFSNew->id;
                $objFSDNew->setRow($data2);
                $objFSDNew->save();
                $logs[] = 'FormStorageData (new) id '.$objFSDNew->id;

                $logs[] = 'Deleting FormStorageData (old) id '.$objFS->id;
                $objFSD->delete();
            }

            $logs[] = 'Deleting FormStorage (old) id '.$objFS->id;
            $objFS->delete();

            $logs[] = 'End migrating rows';

            return $logs;
        }
    }
}
