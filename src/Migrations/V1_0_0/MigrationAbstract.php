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

namespace WEM\SmartgearBundle\Migrations\V1_0_0;

use Doctrine\DBAL\Connection;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\Migration\MigrationAbstract as BaseMigrationAbstract;
use WEM\SmartgearBundle\Classes\Migration\Result;
use WEM\SmartgearBundle\Classes\Version\Comparator as VersionComparator;
use WEM\SmartgearBundle\Classes\Version\Version;

class MigrationAbstract extends BaseMigrationAbstract
{
    /** @var Connection */
    protected $connection;
    /** @var CoreConfigurationManager */
    protected $coreConfigurationManager;
    /** @var VersionComparator */
    protected $versionComparator;

    public function __construct(
        Connection $connection,
        CoreConfigurationManager $coreConfigurationManager,
        VersionComparator $versionComparator
    ) {
        $this->connection = $connection;
        $this->coreConfigurationManager = $coreConfigurationManager;
        $this->versionComparator = $versionComparator;
    }

    public function shouldRun(): Result
    {
        $result = new Result();

        $currentVersion = (new Version())->fromString($this->coreConfigurationManager->load()->getSgVersion());
        $migrationVersion = $this->getVersion();
        switch ($this->versionComparator->compare($currentVersion, $migrationVersion)) {
            case VersionComparator::CURRENT_VERSION_HIGHER:
                $result->setStatus(Result::STATUS_SKIPPED)
                ->addLog(sprintf('Current version is higher than the migration\'s one (v%s -> v%s)', $currentVersion, $migrationVersion))
                    ;
            break;
            case VersionComparator::VERSIONS_EQUALS:
                // $result->setStatus(Result::STATUS_SKIPPED)
                $result->setStatus(Result::STATUS_SHOULD_RUN)
                ->addLog(sprintf('Current version is equal to the migration\'s one (v%s <-> v%s)', $currentVersion, $migrationVersion))
                    ;
            break;
            case VersionComparator::CURRENT_VERSION_LOWER:
                $result->setStatus(Result::STATUS_SHOULD_RUN)
                ->addLog(sprintf('Current version is lower than the migration\'s one (v%s -> v%s)', $currentVersion, $migrationVersion))
                ->addLog('Migration should be run')
                    ;
            break;
        }

        return $result;
    }

    public function do(): Result
    {
        $result = $this->shouldRun();
        if (Result::STATUS_SHOULD_RUN !== $result->getStatus()) {
            return $result;
        }
    }
}
