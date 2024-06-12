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

namespace WEM\SmartgearBundle\Backend\Component\Core\ConfigurationStep;

use Contao\Input;
use Exception;
use WEM\SmartgearBundle\Classes\Backend\ConfigurationStep;
use WEM\SmartgearBundle\Classes\Command\Util as CommandUtil;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\StringUtil;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Config\LocalConfig as LocalConfig;
use WEM\SmartgearBundle\Config\Manager\LocalConfig as LocalConfigManager;

class General extends ConfigurationStep
{
    /** @var ConfigurationManager */
    protected $configurationManager;
    /** @var LocalConfigManager */
    protected $localConfigManager;
    /** @var CommandUtil */
    protected $commandUtil;
    /** @var array */
    protected $foldersToCreate;

    public function __construct(
        string $module,
        string $type,
        ConfigurationManager $configurationManager,
        LocalConfigManager $localConfigManager,
        CommandUtil $commandUtil,
        array $foldersToCreate
    ) {
        parent::__construct($module, $type);
        $this->configurationManager = $configurationManager;
        $this->localConfigManager = $localConfigManager;
        $this->commandUtil = $commandUtil;
        $this->foldersToCreate = $foldersToCreate;
        $this->title = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['Title'];
        try {
            /** @var CoreConfig */
            $config = $this->configurationManager->load();
        } catch (Exception) {
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
        $this->addTextField('sgApiKey', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgApiKey'], !empty($config->getSgApiKey()) ? $config->getSgApiKey() : StringUtil::generateKey(), false, '', 'text', '', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgApiKeyHelp']);
        $this->addTextField('sgEncryptionKey', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgEncryptionKey'], !empty($config->getSgEncryptionKey()) ? $config->getSgEncryptionKey() : StringUtil::generateKey(), false, '', 'text', '', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgEncryptionKeyHelp']);
        $this->addTextField('sgAirtableApiKey', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgAirtableApiKey'], $config->getSgAirtableApiKey(), false, '', 'text', '', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgAirtableApiKey']);
        $this->addTextField('sgAirtableApiKeyForRead', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgAirtableApiKeyForRead'], $config->getSgAirtableApiKeyForRead(), false, '', 'text', '', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgAirtableApiKeyForRead']);
        $this->addTextField('sgAirtableApiKeyForWrite', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgAirtableApiKeyForWrite'], $config->getSgAirtableApiKeyForWrite(), false, '', 'text', '', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgAirtableApiKeyForWrite']);
    }

    public function isStepValid(): bool
    {
        // check if the step is correct
        if (empty(Input::post('sgWebsiteTitle'))) {
            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgWebsiteTitleEmpty']);
        }

        if (\in_array(Input::post('sgWebsiteTitle'), CoreConfig::FORBIDDEN_WEBSITE_TITLES, true)) {
            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgWebsiteTitleForbidden']);
        }

        if (empty(Input::post('sgOwnerEmail'))) {
            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgOwnerEmailEmpty']);
        }

        if (CoreConfig::ANALYTICS_SYSTEM_MATOMO === Input::post('sgAnalytics')) {
            if (empty(Input::post('sgAnalyticsMatomoId'))) {
                throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgAnalyticsMatomoIdEmpty']);
            }
            if (empty(Input::post('sgAnalyticsMatomoHost'))) {
                throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgAnalyticsMatomoHostEmpty']);
            }
        }

        if (CoreConfig::ANALYTICS_SYSTEM_GOOGLE === Input::post('sgAnalytics')) {
            if (empty(Input::post('sgAnalyticsGoogleId'))) {
                throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgAnalyticsGoogleIdEmpty']);
            }
        }

        if (empty(Input::post('sgApiKey'))) {
            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgApiKeyEmpty']);
        }

        if (empty(Input::post('sgEncryptionKey'))) {
            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgEncryptionKeyEmpty']);
        }

        if (empty(Input::post('sgAirtableApiKeyForRead')) && empty(Input::post('sgAirtableApiKey'))) {
            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgAirtableApiKeyForReadEmpty']);
        }

        if (empty(Input::post('sgAirtableApiKeyForWrite')) && empty(Input::post('sgAirtableApiKey'))) {
            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgAirtableApiKeyForWriteEmpty']);
        }

        if (empty(Input::post('sgAirtableApiKey')) && empty(Input::post('sgAirtableApiKeyForRead')) && empty(Input::post('sgAirtableApiKeyForWrite'))) {
            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['GENERAL']['sgAirtableApiKeyEmpty']);
        }

        return true;
    }

    public function do(): void
    {
        // do what is meant to be done in this step
        $this->updateModuleConfiguration();
        $this->updateLocalconfigConfiguration();
        $this->updateContaoConfiguration();
        $this->createPublicFolders();
        $this->commandUtil->executeCmdPHP('cache:clear');
    }

    protected function createPublicFolders(): void
    {
        foreach ($this->foldersToCreate as $folderToCreate) {
            $folder = new \Contao\Folder($folderToCreate);
            $folder->unprotect();
        }
    }

    protected function updateModuleConfiguration(): void
    {
        try {
            /** @var CoreConfig */
            $config = $this->configurationManager->load();
        } catch (Exception) {
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
        $config->setSgEncryptionKey(Input::post('sgEncryptionKey'));
        $config->setSgAirtableApiKey(Input::post('sgAirtableApiKey'));
        $config->setSgAirtableApiKeyForRead(Input::post('sgAirtableApiKeyForRead'));
        $config->setSgAirtableApiKeyForWrite(Input::post('sgAirtableApiKeyForWrite'));

        $this->configurationManager->save($config);
    }

    protected function updateLocalconfigConfiguration(): void
    {
        \Contao\Config::set('wem_pdm_encryption_key', Input::post('sgEncryptionKey'));
        \Contao\Config::persist('wem_pdm_encryption_key', Input::post('sgEncryptionKey'));

        // allow "onclick" on "<a>" tag
        $allowedAttributes = StringUtil::deserialize(\Contao\Config::get('allowedAttributes'), true);
        foreach ($allowedAttributes as $index => $allowedAttribute) {
            if ('a' === $allowedAttribute['key']
            && !str_contains((string) $allowedAttribute['value'], 'onclick')
            ) {
                $allowedAttributes[$index]['value'] .= ',onclick';
                \Contao\Config::set('allowedAttributes', serialize($allowedAttributes));
                \Contao\Config::persist('allowedAttributes', serialize($allowedAttributes));
                break;
            }
        }
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
        ->setFileusageSkipReplaceInsertTags(true) // Still needed on some installations
        ;

        $this->localConfigManager->save($config);
    }
}
