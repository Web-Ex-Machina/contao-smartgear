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

namespace WEM\SmartgearBundle\Api\Smartgear\V1;

use Contao\System;
use Exception;
use WEM\SmartgearBundle\Classes\Api\Security\ApiKey;
use WEM\SmartgearBundle\Classes\Api\Security\Token;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Model\Configuration\Configuration;

class Api
{
    /** @var ManagerJson */
    protected $coreConfigurationManager;
    /** @var ApiKey */
    protected $securityApiKey;
    /** @var Token */
    protected $securityToken;

    public function __construct(
        ManagerJson $coreConfigurationManager,
        ApiKey $securityApiKey,
        Token $securityToken
    ) {
        $this->coreConfigurationManager = $coreConfigurationManager;
        $this->securityApiKey = $securityApiKey;
        $this->securityToken = $securityToken;
    }

    public function token(): string
    {
        return json_encode(['token' => $this->securityToken->define()]);
    }

    public function version(): string
    {
        $sgVersion = null;
        $fwInstallPath = null;
        $fwPackageJSON = null;

        $objSession = System::getContainer()->get('session');
        switch ($objSession->get('configuration_source')) {
            case 'file':
                $fwInstallPath = 'assets/framway';
                /** @var CoreConfig */
                $config = $this->coreConfigurationManager->load();

                $sgVersion = $config->getSgVersion();
            break;
            case 'database':
                $objConfiguration = Configuration::findOneBy('id', $objSession->get('configuration_id'));
                if ($objConfiguration) {
                    $fwInstallPath = $objConfiguration->framway_path;
                    $sgVersion = $objConfiguration->version;
                }
            break;
        }

        $fwPackageJSON = $fwInstallPath ? $this->getFramwayPackageJson($fwInstallPath) : null;

        return json_encode([
            'smartgear' => [
                'installed' => $sgVersion,
                'package' => Util::getPackageVersion(),
            ],
            'php' => \PHP_VERSION,
            'contao' => Util::getCustomPackageVersion('contao/core-bundle'),
            'framway' => $fwPackageJSON ? $fwPackageJSON->version : null,
        ]);
    }

    protected function getFramwayPackageJson(string $path): ?\stdClass
    {
        try {
            $content = file_get_contents(sprintf('./%s/package.json', $path));

            return !empty($content) ? json_decode($content) : null;
        } catch (Exception $e) {
            // nothing
        }

        return null;
    }
}
