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
use Contao\UserGroupModel;
use Contao\UserModel;
use Exception;
use InvalidArgumentException;
use WEM\SmartgearBundle\Classes\Analyzer\Htaccess as HtaccessAnalyzer;
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
    /** @var HtaccessAnalyzer */
    protected $htaccessAnalyzer;

    public function __construct(
        ConfigurationManager $configurationManager,
        string $module,
        string $type,
        ConfigurationEnvFileManager $configurationEnvFileManager,
        HtaccessAnalyzer $htaccessAnalyzer
    ) {
        parent::__construct($configurationManager, $module, $type);
        $this->configurationEnvFileManager = $configurationEnvFileManager;
        $this->htaccessAnalyzer = $htaccessAnalyzer;
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

        $this->htaccessAnalyzer->disableRedirectToWwwAndHttps();

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
        $envFilePath = '../.env';
        if (!file_exists($envFilePath)) {
            file_put_contents($envFilePath, '');
        }
        /** @var EnvFileConfig */
        $envConfig = $this->configurationEnvFileManager->load();
        $envConfig->setAPPENV(EnvFileConfig::MODE_DEV);
        $this->configurationEnvFileManager->save($envConfig);
        $rootPages = PageModel::findPublishedRootPages();
        foreach ($rootPages as $rootPage) {
            if (empty($rootPage->domain)) {
                $rootPage->domain = \Contao\Environment::get('base');
            }
            if (empty($rootPage->language)) {
                $rootPage->language = 'fr';
            }
            if (empty($rootPage->sitemapName)) {
                $rootPage->sitemapName = 'sitemap';
            }
            $rootPage->useSSL = 1;
            $rootPage->createSitemap = 1;
            $rootPage->includeCache = 1;
            $rootPage->clientCache = 84600;
            $rootPage->cache = 84600;
            $rootPage->includeChmod = 1;
            $rootPage->cuser = UserModel::findOneByUsername(CoreConfig::DEFAULT_USER_USERNAME)->id;
            $rootPage->cgroup = UserGroupModel::findOneByName(CoreConfig::DEFAULT_USER_GROUP_ADMIN_NAME)->id;
            $rootPage->chmod = CoreConfig::DEFAULT_ROOTPAGE_CHMOD;
            $rootPage->save();
        }

        $this->htaccessAnalyzer->enableRedirectToWwwAndHttps();
    }

    public function checkProdMode(): string
    {
        $objTemplate = $this->getFilledTemplate();
        $nbErrors = 0;
        $rootPages = PageModel::findPublishedRootPages();
        foreach ($rootPages as $rootPage) {
            if (empty($rootPage->domain)) {
                $this->addError(sprintf('Pas de domaine configuré sur la page racine "%s"', $rootPage->title));
            }
            if (empty($rootPage->language)) {
                $this->addError(sprintf('Pas de langue configuré sur la page racine "%s"', $rootPage->title));
            }
            if (empty($rootPage->sitemapName)) {
                $this->addError(sprintf('Pas de sitemap configuré sur la page racine "%s"', $rootPage->title));
            }
        }

        if (empty($GLOBALS['TL_CONFIG']['adminEmail'])) {
            $this->addError('Aucune adresse email administrateur configurée.');
        }

        if (!$this->htaccessAnalyzer->hasRedirectToWwwAndHttps()) {
            $this->addError('Aucune redirection https & www configurée.');
        }

        // $this->addInfo(print_r($GLOBALS['TL_CONFIG'], true));

        $this->actions = [];
        $this->actions[] = ['action' => 'prod_mode_check_cancel', 'label' => 'Annuler'];
        if (0 !== $nbErrors) {
            $this->actions[] = ['action' => 'prod_mode', 'label' => 'Forcer la mise en production', 'attributes' => 'onclick="if(!confirm(\'Voulez-vous vraiment forcer le passage en mode production de Contao ?\'))return false;Backend.getScrollOffset()"'];
        } else {
            $this->actions[] = ['action' => 'prod_mode', 'label' => 'Effectuer la mise en production'];
        }

        // $objTemplate->actions = Util::formatActions($this->actions);

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

        // $objTemplate->actions = Util::formatActions($this->actions);

        return $objTemplate;
    }
}
