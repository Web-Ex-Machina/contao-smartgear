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
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Analyzer\Htaccess as HtaccessAnalyzer;
use WEM\SmartgearBundle\Classes\Backend\Dashboard as BackendDashboard;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
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
        TranslatorInterface $translator,
        string $module,
        string $type,
        ConfigurationEnvFileManager $configurationEnvFileManager,
        HtaccessAnalyzer $htaccessAnalyzer
    ) {
        parent::__construct($configurationManager, $translator, $module, $type);
        $this->configurationEnvFileManager = $configurationEnvFileManager;
        $this->htaccessAnalyzer = $htaccessAnalyzer;
    }

    public function processAjaxRequest(): void
    {
        try {
            if (empty(Input::post('action'))) {
                throw new InvalidArgumentException($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['AjaxNoActionSpecified']);
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
        $envConfig->setAPPENV(EnvFileConfig::MODE_PROD);
        $this->configurationEnvFileManager->save($envConfig);
        $rootPages = PageModel::findPublishedRootPages();
        foreach ($rootPages as $rootPage) {
            if (empty($rootPage->dns)) {
                $rootPage->dns = \Contao\Environment::get('base');
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
            $rootPage->cuser = UserModel::findOneByUsername($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['UserWebmasterName'])->id;
            $rootPage->cgroup = UserGroupModel::findOneByName($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['UsergroupAdministratorsName'])->id;
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
            if (empty($rootPage->dns)) {
                $this->addError(sprintf($GLOBALS['TL_LANG']['WEMSG']['CORE']['DASHBOARD']['checkProdModeRootpageDomainMissing'], $rootPage->title));
            }
            if (empty($rootPage->language)) {
                $this->addError(sprintf($GLOBALS['TL_LANG']['WEMSG']['CORE']['DASHBOARD']['checkProdModeRootpageLanguageMissing'], $rootPage->title));
            }
            if (empty($rootPage->sitemapName)) {
                $this->addError(sprintf($GLOBALS['TL_LANG']['WEMSG']['CORE']['DASHBOARD']['checkProdModeRootpageSitemapMissing'], $rootPage->title));
            }
        }

        if (empty($GLOBALS['TL_CONFIG']['adminEmail'])) {
            $this->addError($GLOBALS['TL_LANG']['WEMSG']['CORE']['DASHBOARD']['checkProdModeAdminEmailMissing']);
        }

        if (!$this->htaccessAnalyzer->hasRedirectToWwwAndHttps()) {
            $this->addError($GLOBALS['TL_LANG']['WEMSG']['CORE']['DASHBOARD']['checkProdModeHtaccessRedirectMissing']);
        }

        // $this->addInfo(print_r($GLOBALS['TL_CONFIG'], true));

        $this->actions = [];
        $this->actions[] = ['action' => 'prod_mode_check_cancel', 'label' => $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['Cancel']];
        if (0 !== $nbErrors) {
            $this->actions[] = ['action' => 'prod_mode', 'label' => $GLOBALS['TL_LANG']['WEMSG']['CORE']['DASHBOARD']['checkProdModeButtonForceProdModeLabel'], 'attributes' => 'onclick="if(!confirm(\''.$GLOBALS['TL_LANG']['WEMSG']['CORE']['DASHBOARD']['checkProdModeButtonForceProdModeConfirm'].'\'))return false;Backend.getScrollOffset()"'];
        } else {
            $this->actions[] = ['action' => 'prod_mode', 'label' => $GLOBALS['TL_LANG']['WEMSG']['CORE']['DASHBOARD']['checkProdModeButtonProdModeLabel']];
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
            $this->actions[] = ['action' => 'prod_mode_check', 'label' => $GLOBALS['TL_LANG']['WEMSG']['CORE']['DASHBOARD']['buttonProdModeLabel']];
        } else {
            $this->actions[] = ['action' => 'dev_mode', 'label' => $GLOBALS['TL_LANG']['WEMSG']['CORE']['DASHBOARD']['buttonDevModeLabel']];
        }

        $this->actions[] = ['action' => 'configure', 'label' => $GLOBALS['TL_LANG']['WEMSG']['CORE']['DASHBOARD']['buttonConfigurationLabel']];
        $this->actions[] = ['action' => 'reset_mode', 'label' => $GLOBALS['TL_LANG']['WEMSG']['CORE']['DASHBOARD']['buttonResetLabel']];

        $objTemplate->version = $config->getSgVersion();
        $objTemplate->mode = $config->getSgMode();

        return $objTemplate;
    }
}
