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

namespace WEM\SmartgearBundle\Backend\Module\Core\ConfigurationStep;

use Contao\Input;
use Exception;
use WEM\SmartgearBundle\Classes\Backend\ConfigurationStep;
use WEM\SmartgearBundle\Classes\Command\Util as CommandUtil;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Config\Core as CoreConfig;
use WEM\SmartgearBundle\Config\LocalConfig as LocalConfig;
use WEM\SmartgearBundle\Config\Manager\LocalConfig as LocalConfigManager;

class General extends ConfigurationStep
{
    /** @var ConfigurationManager */
    protected $configurationManager;
    /** @var CommandUtil */
    protected $commandUtil;

    public function __construct(
        string $module,
        string $type,
        ConfigurationManager $configurationManager,
        LocalConfigManager $localConfigManager,
        CommandUtil $commandUtil
    ) {
        parent::__construct($module, $type);
        $this->configurationManager = $configurationManager;
        $this->localConfigManager = $localConfigManager;
        $this->commandUtil = $commandUtil;
        $this->title = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['Title'];
        try {
            /** @var CoreConfig */
            $config = $this->configurationManager->load();
        } catch (Exception $e) {
            /** @var CoreConfig */
            $config = $this->configurationManager->new();
        }

        $this->addTextField('sgWebsiteTitle', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgWebsiteTitle'], $config->getSgWebsiteTitle(), true);
        $this->addTextField('sgOwnerEmail', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgOwnerEmail'], $config->getSgOwnerEmail(), true);

        $sgAnalyticsOptions = [
            [
                'label' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgAnalyticsLabelNone'],
                'value' => CoreConfig::ANALYTICS_SYSTEM_NONE,
            ],
            [
                'label' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgAnalyticsLabelMatomo'],
                'value' => CoreConfig::ANALYTICS_SYSTEM_MATOMO,
            ],
            [
                'label' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgAnalyticsLabelGoogle'],
                'value' => CoreConfig::ANALYTICS_SYSTEM_GOOGLE,
            ],
        ];

        $this->addSelectField('sgAnalytics', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgAnalytics'], $sgAnalyticsOptions, $config->getSgAnalytics(), true);
        $this->addTextField('sgAnalyticsGoogleId', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgAnalyticsGoogleId'], $config->getSgAnalyticsGoogleId(), false);
        $this->addTextField('sgAnalyticsMatomoId', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgAnalyticsMatomoId'], $config->getSgAnalyticsMatomoId(), false);
        $this->addTextField('sgAnalyticsMatomoHost', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgAnalyticsMatomoHost'], $config->getSgAnalyticsMatomoHost(), false);
        $this->addTextField('sgApiKey', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgApiKey'], $config->getSgApiKey(), false);
    }

    public function isStepValid(): bool
    {
        // check if the step is correct
        if (empty(Input::post('sgWebsiteTitle'))) {
            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgWebsiteTitleMissing']);
        }

        if (empty(Input::post('sgOwnerEmail'))) {
            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgOwnerEmailMissing']);
        }

        if (CoreConfig::ANALYTICS_SYSTEM_MATOMO === Input::post('sgAnalytics')) {
            if (empty(Input::post('sgAnalyticsMatomoId'))) {
                throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgAnalyticsMatomoIdMissing']);
            }
            if (empty(Input::post('sgAnalyticsMatomoHost'))) {
                throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgAnalyticsMatomoHostMissing']);
            }
        }

        if (CoreConfig::ANALYTICS_SYSTEM_GOOGLE === Input::post('sgAnalytics')) {
            if (empty(Input::post('sgAnalyticsGoogleId'))) {
                throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgAnalyticsGoogleIdMissing']);
            }
        }

        if (empty(Input::post('sgApiKey'))) {
            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgApiKeyMissing']);
        }

        return true;
    }

    public function do(): void
    {
        // do what is meant to be done in this step
        $this->updateModuleConfiguration();
        $this->updateContaoConfiguration();
        $this->commandUtil->executeCmdPHP('cache:clear');
    }

    protected function updateModuleConfiguration(): void
    {
        try {
            /** @var CoreConfig */
            $config = $this->configurationManager->load();
        } catch (Exception $e) {
            /** @var CoreConfig */
            $config = $this->configurationManager->new();
        }
        $config->setSgWebsiteTitle(Input::post('sgWebsiteTitle'));
        $config->setSgOwnerEmail(Input::post('sgOwnerEmail'));
        $config->setSgAnalytics(Input::post('sgAnalytics'));
        $config->setSgAnalyticsGoogleId(Input::post('sgAnalyticsGoogleId'));
        $config->setSgAnalyticsMatomoId(Input::post('sgAnalyticsMatomoId'));
        $config->setSgAnalyticsMatomoHost(Input::post('sgAnalyticsMatomoHost'));
        $config->setSgApiKey(Input::post('sgApiKey'));

        $this->configurationManager->save($config);
    }

    protected function updateContaoConfiguration(): void
    {
        /** @var LocalConfig */
        $config = $this->localConfigManager->load();

        $config->setDateFormat('d/m/Y')
        ->setTimeFormat('H:i')
        ->setDatimFormat('d/m/Y à H:i')
        ->setTimeZone('Europe/Paris')
        ->setCharacterSet('utf-8')
        ->setUseAutoItem(1)
        ->setFolderUrl(1)
        ->setMaxResultsPerPage(500)
        ->setPrivacyAnonymizeIp(1)
        ->setPrivacyAnonymizeGA(1)
        ->setGdMaxImgWidth(5000)
        ->setGdMaxImgHeight(5000)
        ->setMaxFileSize(10000000)
        ->setUndoPeriod(7776000)
        ->setVersionPeriod(7776000)
        ->setLogPeriod(7776000)
        ->setAllowedTags('<script><iframe><a><abbr><acronym><address><area><article><aside><audio><b><bdi><bdo><big><blockquote><br><base><button><canvas><caption><cite><code><col><colgroup><data><datalist><dataset><dd><del><dfn><div><dl><dt><em><fieldset><figcaption><figure><footer><form><h1><h2><h3><h4><h5><h6><header><hgroup><hr><i><img><input><ins><kbd><keygen><label><legend><li><link><map><mark><menu><nav><object><ol><optgroup><option><output><p><param><picture><pre><q><s><samp><section><select><small><source><span><strong><style><sub><sup><table><tbody><td><textarea><tfoot><th><thead><time><tr><tt><u><ul><var><video><wbr>')
        ->setSgOwnerDomain(\Contao\Environment::get('base'))
        ->setSgOwnerHost(CoreConfig::DEFAULT_OWNER_HOST)
        ->setRejectLargeUploads(true)
        ->setImageSizes([
            '_defaults' => [
                'formats' => [
                    'jpg' => ['jpg', 'jpeg'],
                    'png' => ['png'],
                    'gif' => ['gif'],
                ],
                'lazy_loading' => true,
                'resize_mode' => 'crop',
            ],
            '16-9' => [
                'width' => 1920,
                'height' => 1080,
                'densities' => '0.5x, 1x, 2x',
            ],
            '2-1' => [
                'width' => 1920,
                'height' => 960,
                'densities' => '2x',
            ],
            '1-2' => [
                'width' => 960,
                'height' => 1920,
                'densities' => '0.5x',
            ],
            '1-1' => [
                'width' => 1920,
                'height' => 1920,
                'densities' => '1x',
            ],
            '4-3' => [
                'width' => 1920,
                'height' => 1440,
                'densities' => '0.5x, 1x, 2x',
            ],
        ])
        ;

        $this->localConfigManager->save($config);
    }
}
