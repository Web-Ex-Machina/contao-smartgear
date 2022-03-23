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

namespace WEM\SmartgearBundle\Migrations\V1_0_0\M202203231730;

use Doctrine\DBAL\Connection;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\Migration\Result;
use WEM\SmartgearBundle\Classes\Version\Comparator as VersionComparator;
use WEM\SmartgearBundle\Migrations\V1_0_0\MigrationAbstract;

class Migration extends MigrationAbstract
{
    protected static $name = 'Configures CSS classes';
    protected static $description = 'Configures CSS classes available for contents';
    protected static $version = '1.0.0';

    public function __construct(
        Connection $connection,
        CoreConfigurationManager $coreConfigurationManager,
        VersionComparator $versionComparator
    ) {
        parent::__construct($connection, $coreConfigurationManager, $versionComparator);
    }

    public function shouldRun(): Result
    {
        $result = parent::shouldRun();

        if (Result::STATUS_SHOULD_RUN !== $result->getStatus()) {
            return $result;
        }

        $schemaManager = $this->connection->getSchemaManager();
        if (!$schemaManager->tablesExist(['tl_style_manager'])) {
            $result
                ->setStatus(Result::STATUS_FAIL)
                ->addLog('Cannot execute this migration : Style Manager package seems absent.')
            ;

            return $result;
        }
        $result
            ->addLog('Prerequisites met.')
        ;

        return $result;
    }

    public function do(): Result
    {
        $result = $this->shouldRun();
        if (Result::STATUS_SHOULD_RUN !== $result->getStatus()) {
            return $result;
        }

        return (new Result())
            ->setStatus(Result::STATUS_SKIPPED)
            ->addLog('Step 1 : preparing')
            ->addLog('Step 2 : doing')
            ->addLog('Step 3 : doing again')
            ->addLog('Step 4 : done !')
            ->addLog('Step 5 : enjoy \o/')
        ;
    }
}
