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

namespace WEM\SmartgearBundle\DataContainer\Configuration;

use Contao\DataContainer;
use Exception;
use WEM\SmartgearBundle\Classes\StringUtil;
use WEM\SmartgearBundle\Classes\Utils\Configuration\ConfigurationUtil;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\DataContainer\Core;
use WEM\SmartgearBundle\Model\Configuration\Configuration as ConfigurationModel;

class Configuration extends Core
{
    public function __construct()
    {
        parent::__construct();
    }

    public function onsubmitCallback(DataContainer $dc): void
    {
        $objItem = ConfigurationModel::findOneById($dc->activeRecord->id);
        if (empty($objItem->version)) {
            $objItem->version = CoreConfig::DEFAULT_VERSION;
            $objItem->save();
        }

        ConfigurationUtil::createEverythingFromConfiguration($objItem);
    }

    public function ondeleteCallback(DataContainer $dc): void
    {
        $objItem = ConfigurationModel::findOneById($dc->activeRecord->id);
        ConfigurationUtil::deleteEverythingFromConfiguration($objItem);
    }

    public function fieldGoogleFontsOnsaveCallback($value, DataContainer $dc): string
    {
        $valueFormatted = StringUtil::deserialize($value, true);

        return implode(',', $valueFormatted);
    }

    public function fieldGoogleFontsOnloadCallback($value, DataContainer $dc): string
    {
        return serialize(explode(',', (string) $value));
    }

    public function apiKeySaveCallback($value, DataContainer $dc)
    {
        $encryptionService = \Contao\System::getContainer()->get('plenta.encryption');

        return $encryptionService->encrypt($value);
    }

    public function apiKeyLoadCallback($value, DataContainer $dc)
    {
        $encryptionService = \Contao\System::getContainer()->get('plenta.encryption');

        return $encryptionService->decrypt($value);
    }

    public function versionLoadCallback($value, DataContainer $dc)
    {
        if ($dc->id && $value) {
            return $value;
        }

        $coreConfigurationManager = \Contao\System::getContainer()->get('smartgear.config.manager.core');

        try {
            $coreConfig = $coreConfigurationManager->load();
        } catch (Exception) {
            return CoreConfig::DEFAULT_VERSION;
        }

        return $coreConfig->getSgVersion();
    }
}
