<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2023 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

namespace WEM\SmartgearBundle\Migrations\V1\Y0\Z0;

use Doctrine\DBAL\Connection;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\Migration\MigrationAbstract as BaseMigrationAbstract;
use WEM\SmartgearBundle\Classes\Migration\Result;
use WEM\SmartgearBundle\Classes\Version\Comparator as VersionComparator;
use WEM\SmartgearBundle\Classes\Version\Version;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Exceptions\File\NotFound as FileNotFoundException;
use WEM\SmartgearBundle\Model\Configuration\Configuration;

abstract class MigrationAbstract extends BaseMigrationAbstract
{
    /** @var Connection */
    protected $connection;
    /** @var CoreConfigurationManager */
    protected $coreConfigurationManager;
    /** @var VersionComparator */
    protected $versionComparator;
    protected $translation_key = 'WEMSG.MIGRATIONS';

    public function __construct(
        Connection $connection,
        TranslatorInterface $translator,
        CoreConfigurationManager $coreConfigurationManager,
        VersionComparator $versionComparator
    ) {
        parent::__construct($translator);
        $this->connection = $connection;
        $this->coreConfigurationManager = $coreConfigurationManager;
        $this->versionComparator = $versionComparator;
    }

    public function shouldRun(): Result
    {
        $result = new Result();
        $result->setStatus(Result::STATUS_SHOULD_RUN);
        $result = $this->shouldRunCheckConfiguration($result);

        return $this->shouldRunCheckVersion($result);
    }

    public function shouldRunWithoutCheckingVersion(): Result
    {
        $result = new Result();
        $result->setStatus(Result::STATUS_SHOULD_RUN);

        return $this->shouldRunCheckConfiguration($result);
    }

    public function do(): Result
    {
        $result = $this->shouldRun();
        if (Result::STATUS_SHOULD_RUN !== $result->getStatus()) {
            return $result;
        }

        return $result;
    }

    protected function shouldRunCheckConfiguration(Result $result): Result
    {
        try {
            /** @var CoreConfig */
            $config = $this->coreConfigurationManager->load();
        } catch (FileNotFoundException $e) {
            $result->setStatus(Result::STATUS_SKIPPED)
            ->addLog(
                $this->translator->trans($this->buildTranslationKeyLocal('WEMSG.MIGRATIONS.skippedBecauseSmartgearNotInstalled'), [], 'contao_default')
            )
            ;
        }

        return $result;
    }

    protected function shouldRunCheckVersion(Result $result): Result
    {
        try {
            /** @var CoreConfig */
            $config = $this->coreConfigurationManager->load();
        } catch (FileNotFoundException $e) {
            $result->setStatus(Result::STATUS_SKIPPED)
            ->addLog(
                $this->translator->trans($this->buildTranslationKeyLocal('WEMSG.MIGRATIONS.skippedBecauseSmartgearNotInstalled'), [], 'contao_default')
            )
            ;

            return $result;
        }

        $currentVersion = (new Version())->fromString($config->getSgVersion());
        $migrationVersion = $this->getVersion();
        switch ($this->versionComparator->compare($currentVersion, $migrationVersion)) {
            case VersionComparator::CURRENT_VERSION_HIGHER:
                $result->setStatus(Result::STATUS_SKIPPED)
                ->addLog(
                    $this->translator->trans('WEMSG.MIGRATIONS.VERSIONCOMPARATOR.currentVersionHigher', [$currentVersion, $migrationVersion], 'contao_default')
                )
                ;
            break;
            case VersionComparator::VERSIONS_EQUALS:
                $result->setStatus(Result::STATUS_SKIPPED)
                // $result->setStatus(Result::STATUS_SHOULD_RUN)
                ->addLog(
                    $this->translator->trans('WEMSG.MIGRATIONS.VERSIONCOMPARATOR.versionsEquals', [$currentVersion, $migrationVersion], 'contao_default')
                )
                ;
            break;
            case VersionComparator::CURRENT_VERSION_LOWER:
                $result->setStatus(Result::STATUS_SHOULD_RUN)
                ->addLog(
                    $this->translator->trans('WEMSG.MIGRATIONS.VERSIONCOMPARATOR.currentVersionLower', [$currentVersion, $migrationVersion], 'contao_default')
                )
                ->addLog($this->translator->trans('WEMSG.MIGRATIONS.shouldBeRun', [], 'contao_default'))
                ;
            break;
        }

        return $result;
    }

    protected function buildTranslationKeyLocal(string $property): string
    {
        return $this->translation_key.'.'.$property;
    }

    protected function updateConfigurationsVersion(string $version): void
    {
        $this->connection->executeStatement('UPDATE '.Configuration::getTable().' set version = ?', [$version]);
    }
}
