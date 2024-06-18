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

namespace WEM\SmartgearBundle\Migrations;

use Doctrine\DBAL\Connection;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\Migration\Result;
use WEM\SmartgearBundle\Classes\Version\Comparator as VersionComparator;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Migrations\V1\Y0\Z0\MigrationAbstract;

class PlaceholderMigration extends MigrationAbstract
{
    protected string $name;

    protected string $description;

    protected string $version;

    protected string $translation_key = 'WEMSG.MIGRATIONS.PLACEHOLDER';

    public function __construct(
        Connection $connection,
        TranslatorInterface $translator,
        CoreConfigurationManager $coreConfigurationManager,
        VersionComparator $versionComparator,
        int $x,
        int $y,
        int $z
    ) {
        $this->name = sprintf('Smargear update to v%s.%s.%s', $x, $y, $z);
        $this->description = sprintf('Set Smartgear to version %s.%s.%s', $x, $y, $z);
        $this->version = sprintf('%s.%s.%s', $x, $y, $z);
        parent::__construct($connection, $translator, $coreConfigurationManager, $versionComparator);
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
            /** @var CoreConfig $config */
            // $coreConfig = $this->coreConfigurationManager->load();

            // $coreConfig->setSgVersion($this->version);

            // $this->coreConfigurationManager->save($coreConfig);

            $this->updateConfigurationsVersion($this->version);

            $result
                ->setStatus(Result::STATUS_SUCCESS)
                ->addLog($this->translator->trans($this->buildTranslationKey('done'), [$this->version], 'contao_default'))
            ;
        } catch (\Exception $exception) {
            $result
                ->setStatus(Result::STATUS_FAIL)
                ->addLog($exception->getMessage())
            ;
        }

        return $result;
    }

    public function getTranslatedName(): string
    {
        return $this->translator->trans($this->buildTranslationKey('name'), [$this->version], 'contao_default');
    }

    public function getTranslatedDescription(): string
    {
        return $this->translator->trans($this->buildTranslationKey('description'), [$this->version], 'contao_default');
    }
}
