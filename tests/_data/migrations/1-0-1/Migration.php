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

use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\Migration\MigrationInterface;
use WEM\SmartgearBundle\Classes\Migration\Result;
use WEM\SmartgearBundle\Classes\Version\Comparator as VersionComparator;
use WEM\SmartgearBundle\Classes\Version\Version;

class Migration implements MigrationInterface
{
    /** @var CoreConfigurationManager */
    protected $coreConfigurationManager;
    /** @var VersionComparator */
    protected $versionComparator;
    /** @var Version */
    protected $version;

    public function __construct(
        CoreConfigurationManager $coreConfigurationManager,
        VersionComparator $versionComparator
    ) {
        $this->coreConfigurationManager = $coreConfigurationManager;
        $this->versionComparator = $versionComparator;
        $this->version = (new Version())->fromString('1.0.1');
    }

    public function getName(): string
    {
        return '1.0.1 - Migration test';
    }

    public function getVersion(): Version
    {
        return $this->version;
    }

    public function getDescription(): string
    {
        return 'This migration doesn\'t do anything apart from testing if it should run or not';
    }

    public function shouldRun(): Result
    {
        $result = new Result();

        $currentVersion = (new Version())->fromString($this->coreConfigurationManager->load()->getSgVersion());

        switch ($this->versionComparator->compare($currentVersion, $this->version)) {
            case VersionComparator::CURRENT_VERSION_HIGHER:
                $result->setStatus(Result::STATUS_SKIPPED)
                ->addLog(sprintf('Current version is higher than the migration\'s one (v%s -> v%s)', $currentVersion, $this->version))
                    ;
            break;
            case VersionComparator::VERSIONS_EQUALS:
                $result->setStatus(Result::STATUS_SKIPPED)
                ->addLog(sprintf('Current version is equal to the migration\'s one (v%s <-> v%s)', $currentVersion, $this->version))
                    ;
            break;
            case VersionComparator::CURRENT_VERSION_LOWER:
                $result->setStatus(Result::STATUS_SHOULD_RUN)
                ->addLog(sprintf('Current version is lower than the migration\'s one (v%s -> v%s)', $currentVersion, $this->version))
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

        $result->addLog('Doing what needs to be done ...');
        $result->setStatus(Result::STATUS_SUCCESS);
        $result->addLog('All was alright !');

        return $result;
    }
}
