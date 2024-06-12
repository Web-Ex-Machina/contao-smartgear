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

namespace WEM\SmartgearBundle\Migrations\V1\Y0\Z0\M202205130814;

use Doctrine\DBAL\Connection;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\Migration\Result;
use WEM\SmartgearBundle\Classes\Version\Comparator as VersionComparator;
use WEM\SmartgearBundle\Migrations\V1\Y0\Z0\MigrationAbstract;
use WEM\SmartgearBundle\Model\SocialNetwork as SocialNetworkModel;
use WEM\SmartgearBundle\Model\SocialNetworkCategory as SocialNetworkCategoryModel;

class Migration extends MigrationAbstract
{
    protected $name = 'Configures social networks';
    protected $description = 'Configures social networks';
    protected $version = '1.0.0';
    protected $translation_key = 'WEMSG.MIGRATIONS.V1_0_0_M202205130814';

    public function __construct(
        Connection $connection,
        TranslatorInterface $translator,
        CoreConfigurationManager $coreConfigurationManager,
        VersionComparator $versionComparator
    ) {
        parent::__construct($connection, $translator, $coreConfigurationManager, $versionComparator);
    }

    public function shouldRun(): Result
    {
        $result = parent::shouldRunWithoutCheckingVersion();

        if (Result::STATUS_SHOULD_RUN !== $result->getStatus()) {
            return $result;
        }
        $schemaManager = $this->connection->getSchemaManager();
        if (!$schemaManager->tablesExist([SocialNetworkModel::getTable(), SocialNetworkCategoryModel::getTable()])) {
            $result
                ->setStatus(Result::STATUS_FAIL)
                ->addLog($this->translator->trans($this->buildTranslationKey('shouldRunSocialNetworkTablesMissing'), [], 'contao_default'))
            ;

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
            $objSocialNetworkCategory = $this->fillSocialNetworkCategory('WEMSG.SOCIALNETWORK.CATEGORY.social');
            $this->fillSocialNetwork('WEMSG.SOCIALNETWORK.NETWORK.facebook', (int) $objSocialNetworkCategory->id, 'fab fa-facebook');
            $this->fillSocialNetwork('WEMSG.SOCIALNETWORK.NETWORK.twitter', (int) $objSocialNetworkCategory->id, 'fab fa-twitter');
            $this->fillSocialNetwork('WEMSG.SOCIALNETWORK.NETWORK.linkedin', (int) $objSocialNetworkCategory->id, 'fab fa-linkedin');
            $this->fillSocialNetwork('WEMSG.SOCIALNETWORK.NETWORK.instagram', (int) $objSocialNetworkCategory->id, 'fab fa-instagram');
            $this->fillSocialNetwork('WEMSG.SOCIALNETWORK.NETWORK.tiktok', (int) $objSocialNetworkCategory->id, 'fab fa-tiktok');
            $this->fillSocialNetwork('WEMSG.SOCIALNETWORK.NETWORK.snapchat', (int) $objSocialNetworkCategory->id, 'fab fa-snapchat');

            $objSocialNetworkCategory = $this->fillSocialNetworkCategory('WEMSG.SOCIALNETWORK.CATEGORY.video');
            $this->fillSocialNetwork('WEMSG.SOCIALNETWORK.NETWORK.youtube', (int) $objSocialNetworkCategory->id, 'fab fa-youtube');
            $this->fillSocialNetwork('WEMSG.SOCIALNETWORK.NETWORK.twitch', (int) $objSocialNetworkCategory->id, 'fab fa-twitch');
            $this->fillSocialNetwork('WEMSG.SOCIALNETWORK.NETWORK.dailymotion', (int) $objSocialNetworkCategory->id, 'fab fa-dailymotion');

            $objSocialNetworkCategory = $this->fillSocialNetworkCategory('WEMSG.SOCIALNETWORK.CATEGORY.music');
            $this->fillSocialNetwork('WEMSG.SOCIALNETWORK.NETWORK.deezer', (int) $objSocialNetworkCategory->id, 'fab fa-deezer');
            $this->fillSocialNetwork('WEMSG.SOCIALNETWORK.NETWORK.spotify', (int) $objSocialNetworkCategory->id, 'fab fa-spotify');
            $this->fillSocialNetwork('WEMSG.SOCIALNETWORK.NETWORK.itunes', (int) $objSocialNetworkCategory->id, 'fab fa-itunes');
            $this->fillSocialNetwork('WEMSG.SOCIALNETWORK.NETWORK.soundcloud', (int) $objSocialNetworkCategory->id, 'fab fa-soundcloud');

            $objSocialNetworkCategory = $this->fillSocialNetworkCategory('WEMSG.SOCIALNETWORK.CATEGORY.communication');
            $this->fillSocialNetwork('WEMSG.SOCIALNETWORK.NETWORK.discord', (int) $objSocialNetworkCategory->id, 'fab fa-discord');
            $this->fillSocialNetwork('WEMSG.SOCIALNETWORK.NETWORK.slack', (int) $objSocialNetworkCategory->id, 'fab fa-slack');
            $this->fillSocialNetwork('WEMSG.SOCIALNETWORK.NETWORK.discourse', (int) $objSocialNetworkCategory->id, 'fab fa-discourse');
            $this->fillSocialNetwork('WEMSG.SOCIALNETWORK.NETWORK.mastodon', (int) $objSocialNetworkCategory->id, 'fab fa-mastodon');

            $objSocialNetworkCategory = $this->fillSocialNetworkCategory('WEMSG.SOCIALNETWORK.CATEGORY.technology');
            $this->fillSocialNetwork('WEMSG.SOCIALNETWORK.NETWORK.github', (int) $objSocialNetworkCategory->id, 'fab fa-github');
            $this->fillSocialNetwork('WEMSG.SOCIALNETWORK.NETWORK.gitlab', (int) $objSocialNetworkCategory->id, 'fab fa-gitlab');

            $result
                ->setStatus(Result::STATUS_SUCCESS)
                ->addLog($this->translator->trans($this->buildTranslationKey('doAddSocialNetworks'), [], 'contao_default'))
            ;
        } catch (\Exception $e) {
            $result
                ->setStatus(Result::STATUS_FAIL)
                ->addLog($e->getMessage())
            ;
        }

        return $result;
    }

    protected function fillSocialNetworkCategory(string $translationKeyName): SocialNetworkCategoryModel
    {
        $objSocialNetworkCategory = SocialNetworkCategoryModel::findOneByName($this->translator->trans($translationKeyName, [], 'contao_default')) ?? new SocialNetworkCategoryModel();

        $objSocialNetworkCategory->name = $this->translator->trans($translationKeyName, [], 'contao_default');
        $objSocialNetworkCategory->tstamp ??= time();
        $objSocialNetworkCategory->createdAt ??= time();
        $objSocialNetworkCategory->save();

        return $objSocialNetworkCategory;
    }

    protected function fillSocialNetwork(string $translationKeyName, int $categoryId, string $icon): SocialNetworkModel
    {
        $objSocialNetwork = SocialNetworkModel::findOneByName($this->translator->trans($translationKeyName, [], 'contao_default')) ?? new SocialNetworkModel();

        $objSocialNetwork->name = $this->translator->trans($translationKeyName, [], 'contao_default');
        $objSocialNetwork->pid = (string) $categoryId;
        $objSocialNetwork->icon = $icon;
        $objSocialNetwork->tstamp ??= time();
        $objSocialNetwork->createdAt ??= time();
        $objSocialNetwork->save();

        return $objSocialNetwork;
    }
}
