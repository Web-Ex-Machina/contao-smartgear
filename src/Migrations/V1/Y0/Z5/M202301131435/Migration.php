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

namespace WEM\SmartgearBundle\Migrations\V1\Y0\Z5\M202301131435;

use Doctrine\DBAL\Connection;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\DirectoriesSynchronizer;
use WEM\SmartgearBundle\Classes\Migration\Result;
use WEM\SmartgearBundle\Classes\Version\Comparator as VersionComparator;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Migrations\V1\Y0\Z0\MigrationAbstract;
use WEM\SmartgearBundle\Model\PageVisit;

class Migration extends MigrationAbstract
{
    protected $name = 'Smargear update to v1.0.5';
    protected $description = 'Set Smartgear to version 1.0.5';
    protected $version = '1.0.5';
    protected $translation_key = 'WEMSG.MIGRATIONS.V1_0_5_M202301131435';
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
            /* @var CoreConfig */
            // $coreConfig = $this->coreConfigurationManager->load();

            // copy templates needing to be updated
            $this->templatesSmartgearSynchronizer->synchronize(false);

            // $coreConfig->setSgVersion($this->version);

            // $this->coreConfigurationManager->save($coreConfig);

            $this->updateConfigurationsVersion($this->version);

            $sqlFillEmptyRefererBase = sprintf('UPDATE %1$s
                SET referer_base =
                IF(
                    0 = POSITION("?" IN %1$s.referer)
                    ,%1$s.referer
                    , SUBSTRING(%1$s.referer,1,POSITION("?" IN %1$s.referer)-1)
                )
                WHERE referer_base = "" OR referer_base IS NULL
                ', PageVisit::getTable()
            );
            $this->connection->executeQuery($sqlFillEmptyRefererBase);

            $sqlFillEmptyPageUrlBase = sprintf('UPDATE %1$s
                SET page_url_base =
                IF(
                    0 = POSITION("?" IN %1$s.page_url)
                    ,%1$s.page_url
                    ,CONCAT(
                        SUBSTRING(%1$s.page_url,1,POSITION("?" IN %1$s.page_url)-1)
                    )
                )
                WHERE page_url_base = "" OR page_url_base IS NULL
                ', PageVisit::getTable()
            );

            $this->connection->executeQuery($sqlFillEmptyPageUrlBase);

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
