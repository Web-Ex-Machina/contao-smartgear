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

namespace WEM\SmartgearBundle\Migrations\V1\Y0\Z21\M202310121049;

use Doctrine\DBAL\Connection;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Analyzer\Htaccess;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\Migration\Result;
use WEM\SmartgearBundle\Classes\Version\Comparator as VersionComparator;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Migrations\V1\Y0\Z0\MigrationAbstract;

class Migration extends MigrationAbstract
{
    protected $name = 'Smargear update to v1.0.21';
    protected $description = 'Set Smartgear to version 1.0.21';
    protected $version = '1.0.21';
    protected $translation_key = 'WEMSG.MIGRATIONS.V1_0_21_M202310121049';
    /** @var Htaccess */
    protected $htaccessAnalyzer;

    public function __construct(
        Connection $connection,
        TranslatorInterface $translator,
        CoreConfigurationManager $coreConfigurationManager,
        VersionComparator $versionComparator,
        Htaccess $htaccessAnalyzer
    ) {
        parent::__construct($connection, $translator, $coreConfigurationManager, $versionComparator);
        $this->htaccessAnalyzer = $htaccessAnalyzer;
    }

    public function shouldRun(): Result
    {
        $result = parent::shouldRun();

        if (Result::STATUS_SHOULD_RUN !== $result->getStatus()) {
            if (!$this->htaccessAnalyzer->hasRedirectToWwwAndHttps_OLD()) {
                return $result;
            }
        }

        $result
            ->setStatus(Result::STATUS_SHOULD_RUN)
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

            // if old system in place
            // disable old system
            // enable new one
            if ($this->htaccessAnalyzer->hasRedirectToWwwAndHttps_OLD()) {
                $this->htaccessAnalyzer->disableRedirectToWwwAndHttps_OLD();
                $this->htaccessAnalyzer->enableRedirectToWwwAndHttps();
            }

            // $this->coreConfigurationManager->save($coreConfig);

            $this->updateConfigurationsVersion($this->version);

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
