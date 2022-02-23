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

namespace WEM\SmartgearBundle\Backend\Module\Core;

use Contao\FrontendTemplate;
use Contao\Input;
use Contao\PageModel;
use Contao\RequestToken;
use Exception;
use InvalidArgumentException;
use WEM\SmartgearBundle\Classes\Backend\Dashboard as BackendDashboard;
use WEM\SmartgearBundle\Classes\Config\Manager as ConfigurationManager;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Config\Core as CoreConfig;
use WEM\SmartgearBundle\Config\EnvFile as EnvFileConfig;
use WEM\SmartgearBundle\Config\Manager\EnvFile as ConfigurationEnvFileManager;

class Dashboard extends BackendDashboard
{
    /** @var string */
    protected $strTemplate = 'be_wem_sg_block_core_dashboard';

    /** @var ConfigurationEnvFileManager */
    protected $configurationEnvFileManager;

    public function __construct(
        ConfigurationManager $configurationManager,
        string $module,
        string $type,
        ConfigurationEnvFileManager $configurationEnvFileManager
    ) {
        parent::__construct($configurationManager, $module, $type);
        $this->configurationEnvFileManager = $configurationEnvFileManager;
    }

    public function processAjaxRequest(): void
    {
        try {
            if (empty(Input::post('action'))) {
                throw new InvalidArgumentException('No action specified');
            }
            switch (Input::post('action')) {
                default:
                    parent::processAjaxRequest();
                break;
            }
        } catch (Exception $e) {
            $arrResponse = ['status' => 'error', 'msg' => $e->getMessage(), 'trace' => $e->getTrace()];
        }

        // Add Request Token to JSON answer and return
        $arrResponse['rt'] = RequestToken::get();
        echo json_encode($arrResponse);
        exit;
    }

    public function enableDevMode(): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        $config->setSgMode(CoreConfig::MODE_DEV);
        $this->configurationManager->save($config);

        $rootPages = PageModel::findPublishedRootPages();
        foreach ($rootPages as $rootPage) {
            $rootPage->robotsTxt = 'Disallow /';
            $rootPage->includeCache = '';
            $rootPage->save();
        }
        $envFilePath = '../.env';
        if (!file_exists($envFilePath)) {
            file_put_contents($envFilePath, '');
        }
        /** @var EnvFileConfig */
        $envConfig = $this->configurationEnvFileManager->load();
        $envConfig->setAPPENV(EnvFileConfig::MODE_DEV);
        $this->configurationEnvFileManager->save($envConfig);
    }

    public function enableProdMode(): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        $config->setSgMode(CoreConfig::MODE_PROD);
        $this->configurationManager->save($config);
    }

    public function checkProdMode(): string
    {
        $this->addError('Il manque des trucs pour passer en prod ...');
        $objTemplate = $this->getFilledTemplate();
        $this->actions = [];
        $this->actions[] = ['action' => 'prod_mode_check_cancel', 'label' => 'Annuler'];
        if (true) {
            $this->actions[] = ['action' => 'mode_prod', 'label' => 'Forcer la mise en production', 'attributes' => 'onclick="if(!confirm(\'Voulez-vous vraiment forcer le passage en mode production de Contao ?\'))return false;Backend.getScrollOffset()"'];
        } else {
            $this->actions[] = ['action' => 'mode_prod', 'label' => 'Effectuer la mise en production'];
        }

        $objTemplate->actions = Util::formatActions($this->actions);

        return $objTemplate->parse();
    }

    protected function getFilledTemplate(): FrontendTemplate
    {
        $objTemplate = parent::getFilledTemplate();
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        if (CoreConfig::MODE_DEV === $config->getSgMode()) {
            $this->actions[] = ['action' => 'prod_mode_check', 'label' => 'Mode production'];
        } else {
            $this->actions[] = ['action' => 'dev_mode', 'label' => 'Mode développement'];
        }

        $this->actions[] = ['action' => 'configure', 'label' => 'Configuration'];

        $objTemplate->actions = Util::formatActions($this->actions);

        return $objTemplate;
    }
}
