<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2024 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

namespace WEM\SmartgearBundle\Migrations\V1_0_35\M202407251417;

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
    protected $name = 'Smargear update to v1.0.35';
    protected $description = 'Set Smartgear to version 1.0.35';
    protected $version = '1.0.35';
    protected $translation_key = 'WEMSG.MIGRATIONS.V1_0_35_M202407251417';
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
}
