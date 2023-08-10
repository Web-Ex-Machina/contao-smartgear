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

namespace WEM\SmartgearBundle\Backend;

use Contao\ArticleModel;
use Contao\BackendTemplate;
use Contao\CalendarModel;
use Contao\Config;
use Contao\ContentModel;
use Contao\Environment;
use Contao\FaqCategoryModel;
use Contao\FormFieldModel;
use Contao\FormModel;
use Contao\Input;
use Contao\LayoutModel;
use Contao\MemberGroupModel;
use Contao\Message;
use Contao\ModuleModel;
use Contao\NewsArchiveModel;
use Contao\PageModel;
use Contao\RequestToken;
use Contao\System;
use Contao\ThemeModel;
use Contao\UserGroupModel;
use Contao\UserModel;
use Exception;
use NotificationCenter\Model\Gateway as NcGatewayModel;
use NotificationCenter\Model\Language as NcLanguageModel;
use NotificationCenter\Model\Message as NcMessageModel;
use NotificationCenter\Model\Notification as NcNotificationModel;
use WEM\SmartgearBundle\Backup\BackupManager;
use WEM\SmartgearBundle\Classes\Command\Util as CommandUtil;
use WEM\SmartgearBundle\Classes\StringUtil;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Config\Component\Blog\Blog as BlogConfig;
use WEM\SmartgearBundle\Config\Component\Blog\Preset as BlogPresetConfig;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Config\Component\Events\Events as EventsConfig;
use WEM\SmartgearBundle\Config\Component\Faq\Faq as FaqConfig;
use WEM\SmartgearBundle\Config\Component\FormContact\FormContact as FormContactConfig;
use WEM\SmartgearBundle\Config\Module\Extranet\Extranet as ExtranetConfig;
use WEM\SmartgearBundle\Config\Module\FormDataManager\FormDataManager as FormDataManagerConfig;
use WEM\SmartgearBundle\Exceptions\Backup\ManagerException;
use WEM\SmartgearBundle\Exceptions\File\NotFound as FileNotFoundException;
use WEM\SmartgearBundle\Model\Member;
use WEM\SmartgearBundle\Override\Controller;
use WEM\SmartgearBundle\Update\UpdateManager;

/**
 * Back end module "smartgear".
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Smartgear extends \Contao\BackendModule
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'be_wem_sg_install';

    /**
     * Logs.
     *
     * @var array
     */
    protected $arrLogs = [];

    /**
     * Module basepath.
     *
     * @var string
     */
    protected $strBasePath = 'bundles/wemsmartgear';

    /** @var array */
    protected $modules = ['module' => ['extranet', 'form_data_manager'], 'component' => ['core', 'blog', 'events', 'faq', 'form_contact']];

    /** @var BackupManager */
    protected $backupManager;
    /** @var UpdateManager */
    protected $updateManager;
    /** @var CommandUtil */
    protected $commandUtil;

    public function __construct($dc = null)
    {
        parent::__construct($dc);
        $this->backupManager = System::getContainer()->get('smartgear.backup.backup_manager');
        $this->updateManager = System::getContainer()->get('smartgear.update.update_manager');
        $this->coreConfigurationManager = System::getContainer()->get('smartgear.config.manager.core');
        $this->commandUtil = System::getContainer()->get('smartgear.classes.command.util');
        $this->objSession = System::getContainer()->get('session'); // Init session
    }

    /**
     * Process AJAX actions.
     *
     * @param [String] $strAction - Ajax action wanted
     *
     * @return string - Ajax response, as String or JSON
     */
    public function processAjaxRequest($strAction)
    {
        // Catch AJAX Requests
        if (Input::post('TL_WEM_AJAX') && 'be_smartgear' === Input::post('wem_module')) {
            try {
                switch (Input::post('action')) {
                    case 'executeCmd':
                        if (!Input::post('cmd')) {
                            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['AJAX']['COMMAND']['messageCmdNotSpecified']);
                        }

                        try {
                            $arrResponse['status'] = 'success';
                            $arrResponse['msg'] = sprintf($GLOBALS['TL_LANG']['WEMSG']['AJAX']['COMMAND']['messageSuccess'], Input::post('cmd'));
                            $arrResponse['output'] = $this->commandUtil->executeCmd(Input::post('cmd'));
                            // } catch (ProcessFailedException $e) {
                        } catch (Exception $e) {
                            throw $e;
                        }
                        break;
                    case 'executeCmdPhp':
                        if (!Input::post('cmd')) {
                            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['AJAX']['COMMAND']['messageCmdNotSpecified']);
                        }

                        try {
                            $arrResponse['status'] = 'success';
                            $arrResponse['msg'] = sprintf($GLOBALS['TL_LANG']['WEMSG']['AJAX']['COMMAND']['messageSuccess'], Input::post('cmd'));
                            $arrResponse['output'] = $this->commandUtil->executeCmdPHP(Input::post('cmd'));
                            // } catch (ProcessFailedException $e) {
                        } catch (Exception $e) {
                            throw $e;
                        }
                        break;
                    case 'executeCmdLive':
                        if (!Input::post('cmd')) {
                            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['AJAX']['COMMAND']['messageCmdNotSpecified']);
                        }

                        $arrResponse['status'] = 'success';
                        $arrResponse['msg'] = sprintf($GLOBALS['TL_LANG']['WEMSG']['AJAX']['COMMAND']['messageSuccess'], Input::post('cmd'));
                        $res = $this->commandUtil->executeCmdLive(Input::post('cmd'));
                        $arrResponse['output'] = $res;
                        // exit();
                    break;

                    default:
                        // Check if we get all the params we need first
                        if (!Input::post('type') || !Input::post('module') || !Input::post('action')) {
                            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['AJAX']['SUBBLOCK']['messageParameterMissing']);
                        }
                        $objBlock = System::getContainer()->get('smartgear.backend.'.Input::post('type').'.'.Input::post('module').'.block');
                        if ('parse' === Input::post('action')) {
                            echo $objBlock->processAjaxRequest();
                            exit();
                        }
                        $arrResponse = $objBlock->processAjaxRequest();
                        $arrResponse['logs'] = $objBlock->getLogs();
                }
            } catch (Exception $e) {
                $arrResponse = ['status' => 'error', 'msg' => $e->getMessage(), 'trace' => $e->getTrace()];
            }

            // Add Request Token to JSON answer and return
            $arrResponse['rt'] = RequestToken::get();
            echo json_encode($arrResponse);
            exit;
        }
    }

    /**
     * Generate the module.
     *
     * @throws Exception
     */
    protected function compile(): void
    {
        // Add WEM styles to template
        $GLOBALS['TL_CSS'][] = $this->strBasePath.'/backend/wemsg.css';
        try {
            $coreConfig = $this->coreConfigurationManager->load();
        } catch (FileNotFoundException $e) {
            $coreConfig = $this->coreConfigurationManager->new();
            $save = $this->coreConfigurationManager->save($coreConfig);
        }

        if ('backupmanager' === Input::get('key')) {
            $this->getBackupManager();

            return;
        }

        if ('updatemanager' === Input::get('key')) {
            $this->getUpdateManager();

            return;
        }

        if ('configurationmanager' === Input::get('key')) {
            $this->getConfigurationManager();

            return;
        }
        // Catch Modal Calls
        if ('modal' === Input::get('act')) {
            // Catch Errors
            if (!Input::get('type')) {
                throw new Exception($GLOBALS['TL_LANG']['WEMSG']['AJAX']['SUBBLOCK']['messageParameterTypeMissing']);
            }
            if (!Input::get('module')) {
                throw new Exception($GLOBALS['TL_LANG']['WEMSG']['AJAX']['SUBBLOCK']['messageParameterModuleMissing']);
            }
            if (!Input::get('function')) {
                throw new Exception($GLOBALS['TL_LANG']['WEMSG']['AJAX']['SUBBLOCK']['messageParameterFunctionMissing']);
            }

            // Load the good block
            $objModule = Util::findAndCreateObject(Input::get('type'), Input::get('module'));
            $this->Template = $objModule->{Input::get('function')}();

            return;
        }

        // If there is nothing setup, trigger Smartgear Install
        if (!$coreConfig->getSgInstallComplete()) {
            $coreBlock = System::getContainer()->get('smartgear.backend.component.core.block');
            $arrBlocks[$coreBlock->getType()][] = $coreBlock->parse();
            $this->getConfigurationManagerButton();
        } else {
            // Retrieve number of updates to play if session key is undefined
            // @todo : find a way to update this value after an update by the Contao-Manager
            if ($this->objSession->get('wem_sg_update_to_play_number')) {
                $this->Template->update_to_play_number = $this->objSession->get('wem_sg_update_to_play_number');
            } else {
                $listResults = $this->updateManager->list();
                $this->Template->update_to_play_number = $listResults->getNumbersOfUpdatesToPlay();
                $this->objSession->set('wem_sg_update_to_play_number', $this->Template->update_to_play_number);
            }

            // Load buttons
            $this->getBackupManagerButton();
            $this->getUpdateManagerButton();
            $this->getConfigurationManagerButton();

            // Parse Smartgear components
            foreach ($this->modules as $type => $blocks) {
                foreach ($blocks as $block) {
                    $objModule = $this->getContainer()->get('smartgear.backend.'.$type.'.'.$block.'.block');
                    $arrBlocks[$type][] = $objModule->parse();
                }
            }
        }
        // Send blocks to template
        $this->Template->blocks = $arrBlocks;

        // Send msc data to template
        $this->Template->request = Environment::get('request');
        $this->Template->token = RequestToken::get();
        $this->Template->websiteTitle = Config::get('websiteTitle');
        $this->Template->version = $this->coreConfigurationManager->load()->getSgVersion();
    }

    /**
     * Backup manager behaviour.
     */
    protected function getBackupManager(): void
    {
        $this->Template = new BackendTemplate('be_wem_sg_backupmanager');
        if ('new' === Input::get('act')) {
            try {
                set_time_limit(0);
                $start = microtime(true);
                $result = $this->backupManager->newFromUI();
                $end = microtime(true);

                $this->objSession->set('wem_sg_backup_create_result', $result);

                // Add Message
                Message::addConfirmation(sprintf($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['BACKUPMANAGER']['messageNewBackUpDone'], $result->getBackup()->getFile()->basename, ($end - $start)));
            } catch (ManagerException $e) {
                Message::addError($e->getMessage());
            }
            // And redirect
            Controller::redirect(str_replace('&act=new', '', Environment::get('request')));
        } elseif ('restore' === Input::get('act')) {
            try {
                set_time_limit(0);
                $start = microtime(true);
                $result = $this->backupManager->restore(Input::get('backup'));
                $end = microtime(true);

                $this->objSession->set('wem_sg_backup_restore_result', $result);

                // Add Message
                Message::addConfirmation(sprintf($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['BACKUPMANAGER']['messageRestoreBackUpDone'], $result->getBackup()->getFile()->basename, ($end - $start)));
            } catch (ManagerException $e) {
                Message::addError($e->getMessage());
            }
            // And redirect
            Controller::redirect(str_replace('&act=restore&backup='.Input::get('backup'), '', Environment::get('request')));
        } elseif ('delete' === Input::get('act')) {
            try {
                if ($this->backupManager->delete(Input::get('backup'))) {
                    // Add Message
                    Message::addConfirmation(sprintf($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['BACKUPMANAGER']['messageDeleteBackUpSuccess'], Input::get('backup')));
                } else {
                    // Add Message
                    Message::addError(sprintf($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['BACKUPMANAGER']['messageDeleteBackUpError'], Input::get('backup')));
                }
            } catch (ManagerException $e) {
                Message::addError(sprintf($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['BACKUPMANAGER']['messageDeleteBackUpError'], Input::get('backup')));
            }

            // And redirect
            Controller::redirect(str_replace('&act=delete&backup='.Input::get('backup'), '', Environment::get('request')));
        } elseif ('download' === Input::get('act')) {
            $objFile = $this->backupManager->get(Input::get('backup'));
            $objFile->sendToBrowser();
        }

        // Retrieve eventual logs
        if ($this->objSession->get('wem_sg_backup_restore_result')) {
            $this->Template->restore_result = $this->objSession->get('wem_sg_backup_restore_result');
            $this->objSession->set('wem_sg_backup_restore_result', '');
        }
        if ($this->objSession->get('wem_sg_backup_create_result')) {
            $this->Template->create_result = $this->objSession->get('wem_sg_backup_create_result');
            $this->objSession->set('wem_sg_backup_create_result', '');
        }

        // Retrieve backups
        $page = Input::get('page') ?? 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $listResults = $this->backupManager->list(
            $limit,
            $offset,
            Input::get('before'),
            Input::get('after'),
        );
        if (!$listResults) {
            $this->Template->empty = true;
        } else {
            $this->Template->empty = false;
            $this->Template->backups = $listResults;
        }

        $objPagination = new \Contao\Pagination($listResults->getTotal(), $listResults->getLimit());
        $this->Template->pagination = $objPagination->generate("\n  ");

        // Back button
        $this->getBackButton(str_replace('&key=backupmanager', '', Environment::get('request')));

        // New backup button
        $this->Template->newBackUpButtonHref = $this->addToUrl('&act=new');
        $this->Template->newBackUpButtonTitle = StringUtil::specialchars($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['BACKUPMANAGER']['newBackUpBTTitle']);
        $this->Template->newBackUpButtonButton = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['BACKUPMANAGER']['newBackUpBT'];
    }

    /**
     * Update manager behaviour.
     */
    protected function getUpdateManager(): void
    {
        $this->Template = new BackendTemplate('be_wem_sg_updatemanager');
        if ('play' === Input::get('act')) {
            try {
                set_time_limit(0);
                $result = $this->updateManager->update();
                $this->objSession->set('wem_sg_update_update_result', $result);

                // Add Message
                Message::addConfirmation($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['UPDATEMANAGER']['messagePlayUpdatesDone']);
            } catch (Exception $e) {
                Message::addError($e->getMessage());
            }

            // And redirect
            Controller::redirect(str_replace('&act=play', '', Environment::get('request')));
        }

        // Retrieve eventual logs
        if ($this->objSession->get('wem_sg_update_update_result')) {
            $this->Template->update_result = $this->objSession->get('wem_sg_update_update_result');
            $this->objSession->set('wem_sg_update_update_result', '');
        }

        // Retrieve updates
        $listResults = $this->updateManager->list();
        $this->objSession->set('wem_sg_update_to_play_number', $listResults->getNumbersOfUpdatesToPlay());
        if (!$listResults) {
            $this->Template->empty = true;
        } else {
            $this->Template->empty = false;
            $this->Template->updates = $listResults;
        }

        // Back button
        $this->getBackButton(str_replace('&key=updatemanager', '', Environment::get('request')));

        // play updates button
        $this->Template->playUpdatesButtonHref = $this->addToUrl('&act=play');
        $this->Template->playUpdatesButtonTitle = StringUtil::specialchars($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['UPDATEMANAGER']['playUpdatesBTTitle']);
        $this->Template->playUpdatesButtonButton = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['UPDATEMANAGER']['playUpdatesBT'];
    }

    /**
     * Configuration manager behaviour.
     */
    protected function getConfigurationManager(): void
    {
        $this->Template = new BackendTemplate('be_wem_sg_configurationmanager');
        if ('save' === Input::post('act')) {
            try {
                $this->saveConfigurationManagerForm();

                // Add Message
                Message::addConfirmation($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['CONFIGURATIONMANAGER']['messageSaveDone']);
            } catch (Exception $e) {
                Message::addError($e->getMessage());
            }
        }

        // Retrieve eventual logs
        // if ($this->objSession->get('wem_sg_configuration_update_result')) {
        //     $this->Template->update_result = $this->objSession->get('wem_sg_configuration_update_result');
        //     $this->objSession->set('wem_sg_configuration_update_result', '');
        // }

        $this->fillConfigurationManagerForm();

        // Back button
        $this->getBackButton(str_replace('&key=configurationmanager', '', Environment::get('request')));

        // play updates button
        $this->Template->saveButtonTitle = StringUtil::specialchars($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['CONFIGURATIONMANAGER']['saveBTTitle']);
        $this->Template->saveButtonButton = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['CONFIGURATIONMANAGER']['saveBT'];

        $this->Template->token = RequestToken::get();
    }

    protected function getBackButton($strHref = ''): void
    {
        // Back button
        $this->Template->backButtonHref = $strHref ?: Environment::get('request');
        $this->Template->backButtonTitle = StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']);
        $this->Template->backButtonButton = $GLOBALS['TL_LANG']['MSC']['backBT'];
    }

    protected function getBackupManagerButton(): void
    {
        // Backup manager button
        $this->Template->backupManagerBtnHref = $this->addToUrl('&key=backupmanager');
        $this->Template->backupManagerBtnTitle = StringUtil::specialchars($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['BACKUPMANAGER']['backupManagerBTTitle']);
        $this->Template->backupManagerBtnButton = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['BACKUPMANAGER']['backupManagerBT'];
    }

    protected function getUpdateManagerButton(): void
    {
        // Backup manager button
        $this->Template->updateManagerBtnHref = $this->addToUrl('&key=updatemanager');
        $this->Template->updateManagerBtnTitle = StringUtil::specialchars($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['UPDATEMANAGER']['updateManagerBTTitle']);
        $this->Template->updateManagerBtnButton = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['UPDATEMANAGER']['updateManagerBT'];
    }

    protected function getConfigurationManagerButton(): void
    {
        // Backup manager button
        $this->Template->configurationManagerBtnHref = $this->addToUrl('&key=configurationmanager');
        $this->Template->configurationManagerBtnTitle = StringUtil::specialchars($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['CONFIGURATIONMANAGER']['configurationManagerBTTitle']);
        $this->Template->configurationManagerBtnButton = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['CONFIGURATIONMANAGER']['configurationManagerBT'];
    }

    private function saveConfigurationManagerForm(): void
    {
        /** @var CoreConfig */
        $coreConfig = $this->coreConfigurationManager->load();

        if (Input::post('core')) {
            $coreConfig
                ->setSgInstallComplete((bool) Input::post('core')['installComplete'])
                ->setSgVersion(Input::post('core')['version'])
                ->setSgFramwayPath(Input::post('core')['framwayPath'])
                // ->setSgFramwayThemes(Input::post('core')['framwayThemes'])
                ->setSgGoogleFonts(Input::post('core')['googleFonts'] ? explode(',', Input::post('core')['googleFonts']) : [])
                // ->setSgSelectedModules(Input::post('core')['selectedModules'])
                ->setSgAnalyticsGoogleId(Input::post('core')['analyticsGoogleId'])
                ->setSgMode(Input::post('core')['mode'])
                ->setSgAnalyticsMatomoHost(Input::post('core')['analyticsMatomoHost'])
                ->setSgAnalyticsMatomoId(Input::post('core')['analyticsMatomoId'])
                ->setSgOwnerName(Input::post('core')['ownerName'])
                ->setSgOwnerDomain(Input::post('core')['ownerDomain'])
                ->setSgOwnerHost(Input::post('core')['ownerHost'])
                ->setSgOwnerLogo(Input::post('core')['ownerLogo'])
                ->setSgOwnerStatus(Input::post('core')['ownerStatus'])
                ->setSgOwnerStreet(Input::post('core')['ownerStreet'])
                ->setSgOwnerPostal(Input::post('core')['ownerPostal'])
                ->setSgOwnerCity(Input::post('core')['ownerCity'])
                ->setSgOwnerRegion(Input::post('core')['ownerRegion'])
                ->setSgOwnerCountry(Input::post('core')['ownerCountry'])
                ->setSgOwnerSiret(Input::post('core')['ownerSiret'])
                ->setSgOwnerDpoName(Input::post('core')['ownerDpoName'])
                ->setSgOwnerDpoEmail(Input::post('core')['ownerDpoEmail'])
                ->setSgTheme(Input::post('core')['theme'] ? (int) Input::post('core')['theme'] : null)
                ->setSgLayoutFullwidth(Input::post('core')['layoutFullwidth'] ? (int) Input::post('core')['layoutFullwidth'] : null)
                ->setSgLayoutStandard(Input::post('core')['layoutStandard'] ? (int) Input::post('core')['layoutStandard'] : null)
                ->setSgPageRoot(Input::post('core')['pageRoot'] ? (int) Input::post('core')['pageRoot'] : null)
                ->setSgPageHome(Input::post('core')['pageHome'] ? (int) Input::post('core')['pageHome'] : null)
                ->setSgPage404(Input::post('core')['page404'] ? (int) Input::post('core')['page404'] : null)
                ->setSgPageLegalNotice(Input::post('core')['pageLegalNotice'] ? (int) Input::post('core')['pageLegalNotice'] : null)
                ->setSgPagePrivacyPolitics(Input::post('core')['pagePrivacyPolitics'] ? (int) Input::post('core')['pagePrivacyPolitics'] : null)
                ->setSgPageSitemap(Input::post('core')['pageSitemap'] ? (int) Input::post('core')['pageSitemap'] : null)
                ->setSgArticleHome(Input::post('core')['articleHome'] ? (int) Input::post('core')['articleHome'] : null)
                ->setSgArticle404(Input::post('core')['article404'] ? (int) Input::post('core')['article404'] : null)
                ->setSgArticleLegalNotice(Input::post('core')['articleLegalNotice'] ? (int) Input::post('core')['articleLegalNotice'] : null)
                ->setSgArticlePrivacyPolitics(Input::post('core')['articlePrivacyPolitics'] ? (int) Input::post('core')['articlePrivacyPolitics'] : null)
                ->setSgArticleSitemap(Input::post('core')['articleSitemap'] ? (int) Input::post('core')['articleSitemap'] : null)
                ->setSgContent404Headline(Input::post('core')['content404Headline'] ? (int) Input::post('core')['content404Headline'] : null)
                ->setSgContent404Sitemap(Input::post('core')['content404Sitemap'] ? (int) Input::post('core')['content404Sitemap'] : null)
                ->setSgContentLegalNotice(Input::post('core')['contentLegalNotice'] ? (int) Input::post('core')['contentLegalNotice'] : null)
                ->setSgContentPrivacyPolitics(Input::post('core')['contentPrivacyPolitics'] ? (int) Input::post('core')['contentPrivacyPolitics'] : null)
                ->setSgContentSitemapHeadline(Input::post('core')['contentSitemapHeadline'] ? (int) Input::post('core')['contentSitemapHeadline'] : null)
                ->setSgContentSitemap(Input::post('core')['contentSitemap'] ? (int) Input::post('core')['contentSitemap'] : null)
                ->setSgUserWebmaster(Input::post('core')['userWebmaster'] ? (int) Input::post('core')['userWebmaster'] : null)
                ->setSgUserGroupRedactors(Input::post('core')['userGroupRedactors'] ? (int) Input::post('core')['userGroupRedactors'] : null)
                ->setSgUserGroupAdministrators(Input::post('core')['userGroupAdministrators'] ? (int) Input::post('core')['userGroupAdministrators'] : null)
                ->setSgNotificationGatewayEmail(Input::post('core')['notificationGatewayEmail'] ? (int) Input::post('core')['notificationGatewayEmail'] : null)
                // ->setSgModules(Input::post('core')['modules'])
                ->setSgApiKey(Input::post('core')['apiKey'])
                ->setSgEncryptionKey(Input::post('core')['encryptionKey'])
                ->setSgAirtableApiKey(Input::post('core')['airtableApiKey'])
                ->setSgAirtableApiKeyForRead(Input::post('core')['airtableApiKeyForRead'])
                ->setSgAirtableApiKeyForWrite(Input::post('core')['airtableApiKeyForWrite'])
            ;
            $arrModules = [];
            foreach (Input::post('core')['modules'] ?? [] as $key => $moduleId) {
                $objModule = ModuleModel::findByPk($moduleId);
                $arrModules[] = [
                    'key' => $key,
                    'id' => $objModule ? $objModule->id : null,
                    'type' => $objModule ? $objModule->type : null,
                ];
            }
            $coreConfig->setSgModules($arrModules);
        }

        if (Input::post('blog')) {
            /** @var BlogConfig */
            $blogConfig = $coreConfig->getSgBlog();

            $blogConfig
                ->setSgInstallComplete(Input::post('blog')['installComplete'] ? true : false)
                ->setSgArchived((bool) Input::post('blog')['archived'] ? true : false)
                ->setSgArchivedAt((int) Input::post('blog')['archivedAt'])
                ->setSgArchivedMode(Input::post('blog')['archivedMode'])
                ->setSgMode(Input::post('blog')['mode'])
                ->setSgNewsArchive(Input::post('blog')['newsArchive'] ? (int) Input::post('blog')['newsArchive'] : null)
                ->setSgPage(Input::post('blog')['page'] ? (int) Input::post('blog')['page'] : null)
                ->setSgArticle(Input::post('blog')['article'] ? (int) Input::post('blog')['article'] : null)
                ->setSgContentList(Input::post('blog')['contentList'] ? (int) Input::post('blog')['contentList'] : null)
                ->setSgModuleReader(Input::post('blog')['moduleReader'] ? (int) Input::post('blog')['moduleReader'] : null)
                ->setSgModuleList(Input::post('blog')['moduleList'] ? (int) Input::post('blog')['moduleList'] : null)
            ;

            $arrBlogPresets = [];
            $arrBlogPresetsExisting = $blogConfig->getSgPresets();
            if (!empty($arrBlogPresetsExisting)) {
                foreach ($arrBlogPresetsExisting as $index => $preset) {
                    /** @var BlogPresetConfig */
                    $preset = $preset;
                    $preset
                        ->setSgNewsFolder(Input::post('blog')['presets'][$index]['newsFolder'] ?? BlogPresetConfig::DEFAULT_FOLDER_PATH)
                        ->setSgNewsArchiveTitle(Input::post('blog')['presets'][$index]['newsArchiveTitle'] ?? BlogPresetConfig::DEFAULT_ARCHIVE_TITLE)
                        ->setSgNewsListPerPage((int) Input::post('blog')['presets'][$index]['newsListPerPage'])
                        ->setSgPageTitle(Input::post('blog')['presets'][$index]['pageTitle'] ?? BlogPresetConfig::DEFAULT_PAGE_TITLE)
                    ;
                    $arrBlogPresets[] = $preset;
                }
            } else {
                foreach (Input::post('blog')['presets'] as $index => $presetConfig) {
                    $preset = new BlogPresetConfig();
                    $preset
                        ->setSgNewsFolder($presetConfig['newsFolder'] ?? BlogPresetConfig::DEFAULT_FOLDER_PATH)
                        ->setSgNewsArchiveTitle($presetConfig['newsArchiveTitle'] ?? BlogPresetConfig::DEFAULT_ARCHIVE_TITLE)
                        ->setSgNewsListPerPage((int) $presetConfig['newsListPerPage'])
                        ->setSgPageTitle($presetConfig['pageTitle'] ?? BlogPresetConfig::DEFAULT_PAGE_TITLE)
                    ;
                    $arrBlogPresets[] = $preset;
                }
            }

            $blogConfig->setSgPresets($arrBlogPresets);
            $blogConfig->setSgCurrentPresetIndex((int) Input::post('blog')['currentPresetIndex']); // wait for presets to be saved
            $coreConfig->setSgBlog($blogConfig);
        }

        if (Input::post('events')) {
            /** @var EventsConfig */
            $eventsConfig = $coreConfig->getSgEvents();

            $eventsConfig
                ->setSgInstallComplete((bool) Input::post('events')['installComplete'])
                ->setSgArchived((bool) Input::post('events')['archived'])
                ->setSgArchivedAt((int) Input::post('events')['archivedAt'])
                ->setSgArchivedMode(Input::post('events')['archivedMode'])
                ->setSgMode(Input::post('events')['mode'])
                ->setSgEventsFolder(Input::post('events')['eventsFolder'] ?? EventsConfig::DEFAULT_FOLDER_PATH)
                ->setSgPage(Input::post('events')['page'] ? (int) Input::post('events')['page'] : null)
                ->setSgArticle(Input::post('events')['article'] ? (int) Input::post('events')['article'] : null)
                ->setSgContentList(Input::post('events')['contentList'] ? (int) Input::post('events')['contentList'] : null)
                ->setSgCalendar(Input::post('events')['calendar'] ? (int) Input::post('events')['calendar'] : null)
                ->setSgModuleReader(Input::post('events')['moduleReader'] ? (int) Input::post('events')['moduleReader'] : null)
                ->setSgModuleList(Input::post('events')['moduleList'] ? (int) Input::post('events')['moduleList'] : null)
                ->setSgModuleCalendar(Input::post('events')['moduleCalendar'] ? (int) Input::post('events')['moduleCalendar'] : null)
                ->setSgCalendarTitle(Input::post('events')['calendarTitle'] ?? EventsConfig::DEFAULT_FEED_TITLE)
                ->setSgEventsListPerPage((int) Input::post('events')['eventsListPerPage'] ?? EventsConfig::DEFAULT_EVENTS_PER_PAGE)
                ->setSgPageTitle(Input::post('events')['pageTitle'] ?? EventsConfig::DEFAULT_PAGE_TITLE)
            ;

            $coreConfig->setSgEvents($eventsConfig);
        }

        if (Input::post('faq')) {
            /** @var FaqConfig */
            $faqConfig = $coreConfig->getSgFaq();

            $faqConfig
                ->setSgInstallComplete((bool) Input::post('faq')['installComplete'])
                ->setSgArchived((bool) Input::post('faq')['archived'])
                ->setSgArchivedAt((int) Input::post('faq')['archivedAt'])
                ->setSgArchivedMode(Input::post('faq')['archivedMode'])
                ->setSgFaqFolder(Input::post('faq')['faqFolder'] ?? EventsConfig::DEFAULT_FOLDER_PATH)
                ->setSgPage(Input::post('faq')['page'] ? (int) Input::post('faq')['page'] : null)
                ->setSgArticle(Input::post('faq')['article'] ? (int) Input::post('faq')['article'] : null)
                ->setSgContent(Input::post('faq')['content'] ? (int) Input::post('faq')['content'] : null)
                ->setSgFaqCategory(Input::post('faq')['faqCategory'] ? (int) Input::post('faq')['faqCategory'] : null)
                ->setSgModuleFaq(Input::post('faq')['moduleFaq'] ? (int) Input::post('faq')['moduleFaq'] : null)
                ->setSgFaqTitle(Input::post('faq')['faqTitle'] ?? EventsConfig::DEFAULT_FEED_TITLE)
                ->setSgPageTitle(Input::post('faq')['pageTitle'] ?? EventsConfig::DEFAULT_PAGE_TITLE)
            ;

            $coreConfig->setSgFaq($faqConfig);
        }

        if (Input::post('formContact')) {
            /** @var FormContactConfig */
            $fcConfig = $coreConfig->getSgFormContact();

            $fcConfig
                ->setSgInstallComplete((bool) Input::post('formContact')['installComplete'])
                ->setSgArchived((bool) Input::post('formContact')['archived'])
                ->setSgArchivedAt((int) Input::post('formContact')['archivedAt'])
                ->setSgArchivedMode(Input::post('formContact')['archivedMode'])

                ->setSgPageForm(Input::post('formContact')['pageForm'] ? (int) Input::post('formContact')['pageForm'] : null)
                ->setSgPageFormSent(Input::post('formContact')['pageFormSent'] ? (int) Input::post('formContact')['pageFormSent'] : null)
                ->setSgArticleForm(Input::post('formContact')['articleForm'] ? (int) Input::post('formContact')['articleForm'] : null)
                ->setSgArticleFormSent(Input::post('formContact')['articleFormSent'] ? (int) Input::post('formContact')['articleFormSent'] : null)
                ->setSgContentHeadlineArticleForm(Input::post('formContact')['contentHeadlineArticleForm'] ? (int) Input::post('formContact')['contentHeadlineArticleForm'] : null)
                ->setSgContentFormArticleForm(Input::post('formContact')['contentFormArticleForm'] ? (int) Input::post('formContact')['contentFormArticleForm'] : null)
                ->setSgContentHeadlineArticleFormSent(Input::post('formContact')['contentHeadlineArticleFormSent'] ? (int) Input::post('formContact')['contentHeadlineArticleFormSent'] : null)
                ->setSgContentTextArticleFormSent(Input::post('formContact')['contentTextArticleFormSent'] ? (int) Input::post('formContact')['contentTextArticleFormSent'] : null)

                ->setSgFormContact(Input::post('formContact')['formContact'] ? (int) Input::post('formContact')['formContact'] : null)
                ->setSgFieldName(Input::post('formContact')['fieldName'] ? (int) Input::post('formContact')['fieldName'] : null)
                ->setSgFieldEmail(Input::post('formContact')['fieldEmail'] ? (int) Input::post('formContact')['fieldEmail'] : null)
                ->setSgFieldMessage(Input::post('formContact')['fieldMessage'] ? (int) Input::post('formContact')['fieldMessage'] : null)
                ->setSgFieldConsentDataTreatment(Input::post('formContact')['fieldConsentDataTreatment'] ? (int) Input::post('formContact')['fieldConsentDataTreatment'] : null)
                ->setSgFieldConsentDataSave(Input::post('formContact')['fieldConsentDataSave'] ? (int) Input::post('formContact')['fieldConsentDataSave'] : null)
                ->setSgFieldCaptcha(Input::post('formContact')['fieldCaptcha'] ? (int) Input::post('formContact')['fieldCaptcha'] : null)
                ->setSgFieldSubmit(Input::post('formContact')['fieldSubmit'] ? (int) Input::post('formContact')['fieldSubmit'] : null)

                ->setSgNotificationMessageUser(Input::post('formContact')['notificationMessageUser'] ? (int) Input::post('formContact')['notificationMessageUser'] : null)
                ->setSgNotificationMessageAdmin(Input::post('formContact')['notificationMessageAdmin'] ? (int) Input::post('formContact')['notificationMessageAdmin'] : null)

                ->setSgNotificationMessageUserLanguage(Input::post('formContact')['notificationMessageUserLanguage'] ? (int) Input::post('formContact')['notificationMessageUserLanguage'] : null)
                ->setSgNotificationMessageAdminLanguage(Input::post('formContact')['notificationMessageAdminLanguage'] ? (int) Input::post('formContact')['notificationMessageAdminLanguage'] : null)

                ->setSgFormContactTitle(Input::post('formContact')['formContactTitle'] ?? FormContactConfig::DEFAULT_FEED_TITLE)
                ->setSgPageTitle(Input::post('formContact')['pageTitle'] ?? FormContactConfig::DEFAULT_PAGE_TITLE)

            ;

            $coreConfig->setSgFormContact($fcConfig);
        }

        if (Input::post('formDataManager')) {
            /** @var FormDataManagerConfig */
            $fdmConfig = $coreConfig->getSgFormDataManager();

            $fdmConfig
                ->setSgInstallComplete((bool) Input::post('formDataManager')['installComplete'])
                ->setSgArchived((bool) Input::post('formDataManager')['archived'])
                ->setSgArchivedAt((int) Input::post('formDataManager')['archivedAt'])
                ->setSgArchivedMode(Input::post('formDataManager')['archivedMode'])
            ;

            $coreConfig->setSgFormDataManager($fdmConfig);
        }

        if (Input::post('extranet')) {
            /** @var ExtranetConfig */
            $extranetConfig = $coreConfig->getSgExtranet();

            $extranetConfig
                ->setSgInstallComplete((bool) Input::post('extranet')['installComplete'])
                ->setSgArchived((bool) Input::post('extranet')['archived'])
                ->setSgArchivedAt((int) Input::post('extranet')['archivedAt'])
                ->setSgArchivedMode(Input::post('extranet')['archivedMode'])

                ->setSgExtranetFolder(Input::post('extranet')['extranetFolder'] ?? ExtranetConfig::DEFAULT_FOLDER_PATH)
                ->setSgCanSubscribe((bool) Input::post('extranet')['canSubscribe'])
                ->setSgMemberGroupMembersTitle(Input::post('extranet')['memberGroupMembersTitle'] ?? ExtranetConfig::DEFAULT_MEMBER_GROUP_MEMBERS_TITLE)
                ->setSgPageExtranetTitle(Input::post('extranet')['pageExtranetTitle'] ?? ExtranetConfig::DEFAULT_PAGE_EXTRANET_TITLE)

                ->setSgPageExtranet(Input::post('extranet')['pageExtranet'] ? (int) Input::post('extranet')['pageExtranet'] : null)
                ->setSgPage401(Input::post('extranet')['page401'] ? (int) Input::post('extranet')['page401'] : null)
                ->setSgPage403(Input::post('extranet')['page403'] ? (int) Input::post('extranet')['page403'] : null)
                ->setSgPageContent(Input::post('extranet')['pageContent'] ? (int) Input::post('extranet')['pageContent'] : null)
                ->setSgPageData(Input::post('extranet')['pageData'] ? (int) Input::post('extranet')['pageData'] : null)
                ->setSgPageDataConfirm(Input::post('extranet')['pageDataConfirm'] ? (int) Input::post('extranet')['pageDataConfirm'] : null)
                ->setSgPagePassword(Input::post('extranet')['pagePassword'] ? (int) Input::post('extranet')['pagePassword'] : null)
                ->setSgPagePasswordConfirm(Input::post('extranet')['pagePasswordConfirm'] ? (int) Input::post('extranet')['pagePasswordConfirm'] : null)
                ->setSgPagePasswordValidate(Input::post('extranet')['pagePasswordValidate'] ? (int) Input::post('extranet')['pagePasswordValidate'] : null)
                ->setSgPageLogout(Input::post('extranet')['pageLogout'] ? (int) Input::post('extranet')['pageLogout'] : null)
                ->setSgPageSubscribe(Input::post('extranet')['pageSubscribe'] ? (int) Input::post('extranet')['pageSubscribe'] : null)
                ->setSgPageSubscribeConfirm(Input::post('extranet')['pageSubscribeConfirm'] ? (int) Input::post('extranet')['pageSubscribeConfirm'] : null)
                ->setSgPageSubscribeValidate(Input::post('extranet')['pageSubscribeValidate'] ? (int) Input::post('extranet')['pageSubscribeValidate'] : null)
                ->setSgPageUnsubscribeConfirm(Input::post('extranet')['pageUnsubscribeConfirm'] ? (int) Input::post('extranet')['pageUnsubscribeConfirm'] : null)

                ->setSgArticleExtranet(Input::post('extranet')['articleExtranet'] ? (int) Input::post('extranet')['articleExtranet'] : null)
                ->setSgArticle401(Input::post('extranet')['article401'] ? (int) Input::post('extranet')['article401'] : null)
                ->setSgArticle403(Input::post('extranet')['article403'] ? (int) Input::post('extranet')['article403'] : null)
                ->setSgArticleContent(Input::post('extranet')['articleContent'] ? (int) Input::post('extranet')['articleContent'] : null)
                ->setSgArticleData(Input::post('extranet')['articleData'] ? (int) Input::post('extranet')['articleData'] : null)
                ->setSgArticleDataConfirm(Input::post('extranet')['articleDataConfirm'] ? (int) Input::post('extranet')['articleDataConfirm'] : null)
                ->setSgArticlePassword(Input::post('extranet')['articlePassword'] ? (int) Input::post('extranet')['articlePassword'] : null)
                ->setSgArticlePasswordConfirm(Input::post('extranet')['articlePasswordConfirm'] ? (int) Input::post('extranet')['articlePasswordConfirm'] : null)
                ->setSgArticlePasswordValidate(Input::post('extranet')['articlePasswordValidate'] ? (int) Input::post('extranet')['articlePasswordValidate'] : null)
                ->setSgArticleLogout(Input::post('extranet')['articleLogout'] ? (int) Input::post('extranet')['articleLogout'] : null)
                ->setSgArticleSubscribe(Input::post('extranet')['articleSubscribe'] ? (int) Input::post('extranet')['articleSubscribe'] : null)
                ->setSgArticleSubscribeConfirm(Input::post('extranet')['articleSubscribeConfirm'] ? (int) Input::post('extranet')['articleSubscribeConfirm'] : null)
                ->setSgArticleSubscribeValidate(Input::post('extranet')['articleSubscribeValidate'] ? (int) Input::post('extranet')['articleSubscribeValidate'] : null)
                ->setSgArticleUnsubscribeConfirm(Input::post('extranet')['articleUnsubscribeConfirm'] ? (int) Input::post('extranet')['articleUnsubscribeConfirm'] : null)

                ->setSgModuleLogin(Input::post('extranet')['moduleLogin'] ? (int) Input::post('extranet')['moduleLogin'] : null)
                ->setSgModuleLogout(Input::post('extranet')['moduleLogout'] ? (int) Input::post('extranet')['moduleLogout'] : null)
                ->setSgModuleData(Input::post('extranet')['moduleData'] ? (int) Input::post('extranet')['moduleData'] : null)
                ->setSgModulePassword(Input::post('extranet')['modulePassword'] ? (int) Input::post('extranet')['modulePassword'] : null)
                ->setSgModuleNav(Input::post('extranet')['moduleNav'] ? (int) Input::post('extranet')['moduleNav'] : null)
                ->setSgModuleSubscribe(Input::post('extranet')['moduleSubscribe'] ? (int) Input::post('extranet')['moduleSubscribe'] : null)
                ->setSgModuleCloseAccount(Input::post('extranet')['moduleCloseAccount'] ? (int) Input::post('extranet')['moduleCloseAccount'] : null)

                ->setSgNotificationChangeData(Input::post('extranet')['notificationChangeData'] ? (int) Input::post('extranet')['notificationChangeData'] : null)
                ->setSgNotificationPassword(Input::post('extranet')['notificationPassword'] ? (int) Input::post('extranet')['notificationPassword'] : null)
                ->setSgNotificationSubscription(Input::post('extranet')['notificationSubscription'] ? (int) Input::post('extranet')['notificationSubscription'] : null)

                ->setSgNotificationChangeDataMessage(Input::post('extranet')['notificationChangeDataMessage'] ? (int) Input::post('extranet')['notificationChangeDataMessage'] : null)
                ->setSgNotificationPasswordMessage(Input::post('extranet')['notificationPasswordMessage'] ? (int) Input::post('extranet')['notificationPasswordMessage'] : null)
                ->setSgNotificationSubscriptionMessage(Input::post('extranet')['notificationSubscriptionMessage'] ? (int) Input::post('extranet')['notificationSubscriptionMessage'] : null)

                ->setSgNotificationChangeDataMessageLanguage(Input::post('extranet')['notificationChangeDataMessageLanguage'] ? (int) Input::post('extranet')['notificationChangeDataMessageLanguage'] : null)
                ->setSgNotificationPasswordMessageLanguage(Input::post('extranet')['notificationPasswordMessageLanguage'] ? (int) Input::post('extranet')['notificationPasswordMessageLanguage'] : null)
                ->setSgNotificationSubscriptionMessageLanguage(Input::post('extranet')['notificationSubscriptionMessageLanguage'] ? (int) Input::post('extranet')['notificationSubscriptionMessageLanguage'] : null)

                ->setSgContentArticleExtranetHeadline(Input::post('extranet')['contentArticleExtranetHeadline'] ? (int) Input::post('extranet')['contentArticleExtranetHeadline'] : null)
                ->setSgContentArticleExtranetModuleLoginGuests(Input::post('extranet')['contentArticleExtranetModuleLoginGuests'] ? (int) Input::post('extranet')['contentArticleExtranetModuleLoginGuests'] : null)
                ->setSgContentArticleExtranetGridStartA(Input::post('extranet')['contentArticleExtranetGridStartA'] ? (int) Input::post('extranet')['contentArticleExtranetGridStartA'] : null)
                ->setSgContentArticleExtranetGridStartB(Input::post('extranet')['contentArticleExtranetGridStartB'] ? (int) Input::post('extranet')['contentArticleExtranetGridStartB'] : null)
                ->setSgContentArticleExtranetModuleLoginLogged(Input::post('extranet')['contentArticleExtranetModuleLoginLogged'] ? (int) Input::post('extranet')['contentArticleExtranetModuleLoginLogged'] : null)
                ->setSgContentArticleExtranetModuleNav(Input::post('extranet')['contentArticleExtranetModuleNav'] ? (int) Input::post('extranet')['contentArticleExtranetModuleNav'] : null)
                ->setSgContentArticleExtranetGridStopB(Input::post('extranet')['contentArticleExtranetGridStopB'] ? (int) Input::post('extranet')['contentArticleExtranetGridStopB'] : null)
                ->setSgContentArticleExtranetGridStopA(Input::post('extranet')['contentArticleExtranetGridStopA'] ? (int) Input::post('extranet')['contentArticleExtranetGridStopA'] : null)

                ->setSgContentArticle401Headline(Input::post('extranet')['contentArticle401Headline'] ? (int) Input::post('extranet')['contentArticle401Headline'] : null)
                ->setSgContentArticle401Text(Input::post('extranet')['contentArticle401Text'] ? (int) Input::post('extranet')['contentArticle401Text'] : null)
                ->setSgContentArticle401ModuleLoginGuests(Input::post('extranet')['contentArticle401ModuleLoginGuests'] ? (int) Input::post('extranet')['contentArticle401ModuleLoginGuests'] : null)

                ->setSgContentArticle403Headline(Input::post('extranet')['contentArticle403Headline'] ? (int) Input::post('extranet')['contentArticle403Headline'] : null)
                ->setSgContentArticle403Text(Input::post('extranet')['contentArticle403Text'] ? (int) Input::post('extranet')['contentArticle403Text'] : null)
                ->setSgContentArticle403Hyperlink(Input::post('extranet')['contentArticle403Hyperlink'] ? (int) Input::post('extranet')['contentArticle403Hyperlink'] : null)

                ->setSgContentArticleContentHeadline(Input::post('extranet')['contentArticleContentHeadline'] ? (int) Input::post('extranet')['contentArticleContentHeadline'] : null)
                ->setSgContentArticleContentText(Input::post('extranet')['contentArticleContentText'] ? (int) Input::post('extranet')['contentArticleContentText'] : null)

                ->setSgContentArticleDataHeadline(Input::post('extranet')['contentArticleDataHeadline'] ? (int) Input::post('extranet')['contentArticleDataHeadline'] : null)
                ->setSgContentArticleDataModuleData(Input::post('extranet')['contentArticleDataModuleData'] ? (int) Input::post('extranet')['contentArticleDataModuleData'] : null)
                ->setSgContentArticleDataHeadlineCloseAccount(Input::post('extranet')['contentArticleDataHeadlineCloseAccount'] ? (int) Input::post('extranet')['contentArticleDataHeadlineCloseAccount'] : null)
                ->setSgContentArticleDataTextCloseAccount(Input::post('extranet')['contentArticleDataTextCloseAccount'] ? (int) Input::post('extranet')['contentArticleDataTextCloseAccount'] : null)
                ->setSgContentArticleDataModuleCloseAccount(Input::post('extranet')['contentArticleDataModuleCloseAccount'] ? (int) Input::post('extranet')['contentArticleDataModuleCloseAccount'] : null)

                ->setSgContentArticleDataConfirmHeadline(Input::post('extranet')['contentArticleDataConfirmHeadline'] ? (int) Input::post('extranet')['contentArticleDataConfirmHeadline'] : null)
                ->setSgContentArticleDataConfirmText(Input::post('extranet')['contentArticleDataConfirmText'] ? (int) Input::post('extranet')['contentArticleDataConfirmText'] : null)
                ->setSgContentArticleDataConfirmHyperlink(Input::post('extranet')['contentArticleDataConfirmHyperlink'] ? (int) Input::post('extranet')['contentArticleDataConfirmHyperlink'] : null)

                ->setSgContentArticlePasswordHeadline(Input::post('extranet')['contentArticlePasswordHeadline'] ? (int) Input::post('extranet')['contentArticlePasswordHeadline'] : null)
                ->setSgContentArticlePasswordModulePassword(Input::post('extranet')['contentArticlePasswordModulePassword'] ? (int) Input::post('extranet')['contentArticlePasswordModulePassword'] : null)

                ->setSgContentArticlePasswordConfirmHeadline(Input::post('extranet')['contentArticlePasswordConfirmHeadline'] ? (int) Input::post('extranet')['contentArticlePasswordConfirmHeadline'] : null)
                ->setSgContentArticlePasswordConfirmText(Input::post('extranet')['contentArticlePasswordConfirmText'] ? (int) Input::post('extranet')['contentArticlePasswordConfirmText'] : null)

                ->setSgContentArticlePasswordValidateHeadline(Input::post('extranet')['contentArticlePasswordValidateHeadline'] ? (int) Input::post('extranet')['contentArticlePasswordValidateHeadline'] : null)
                ->setSgContentArticlePasswordValidateModulePassword(Input::post('extranet')['contentArticlePasswordValidateModulePassword'] ? (int) Input::post('extranet')['contentArticlePasswordValidateModulePassword'] : null)

                ->setSgContentArticleLogoutModuleLogout(Input::post('extranet')['contentArticleLogoutModuleLogout'] ? (int) Input::post('extranet')['contentArticleLogoutModuleLogout'] : null)

                ->setSgContentArticleSubscribeHeadline(Input::post('extranet')['contentArticleSubscribeHeadline'] ? (int) Input::post('extranet')['contentArticleSubscribeHeadline'] : null)
                ->setSgContentArticleSubscribeModuleSubscribe(Input::post('extranet')['contentArticleSubscribeModuleSubscribe'] ? (int) Input::post('extranet')['contentArticleSubscribeModuleSubscribe'] : null)

                ->setSgContentArticleSubscribeConfirmHeadline(Input::post('extranet')['contentArticleSubscribeConfirmHeadline'] ? (int) Input::post('extranet')['contentArticleSubscribeConfirmHeadline'] : null)
                ->setSgContentArticleSubscribeConfirmText(Input::post('extranet')['contentArticleSubscribeConfirmText'] ? (int) Input::post('extranet')['contentArticleSubscribeConfirmText'] : null)

                ->setSgContentArticleSubscribeValidateHeadline(Input::post('extranet')['contentArticleSubscribeValidateHeadline'] ? (int) Input::post('extranet')['contentArticleSubscribeValidateHeadline'] : null)
                ->setSgContentArticleSubscribeValidateText(Input::post('extranet')['contentArticleSubscribeValidateText'] ? (int) Input::post('extranet')['contentArticleSubscribeValidateText'] : null)
                ->setSgContentArticleSubscribeValidateModuleLoginGuests(Input::post('extranet')['contentArticleSubscribeValidateModuleLoginGuests'] ? (int) Input::post('extranet')['contentArticleSubscribeValidateModuleLoginGuests'] : null)

                ->setSgContentArticleUnsubscribeHeadline(Input::post('extranet')['contentArticleUnsubscribeHeadline'] ? (int) Input::post('extranet')['contentArticleUnsubscribeHeadline'] : null)
                ->setSgContentArticleUnsubscribeText(Input::post('extranet')['contentArticleUnsubscribeText'] ? (int) Input::post('extranet')['contentArticleUnsubscribeText'] : null)
                ->setSgContentArticleUnsubscribeHyperlink(Input::post('extranet')['contentArticleUnsubscribeHyperlink'] ? (int) Input::post('extranet')['contentArticleUnsubscribeHyperlink'] : null)

                ->setSgMemberExample(Input::post('extranet')['memberExample'] ? (int) Input::post('extranet')['memberExample'] : null)

                ->setSgMemberGroupMembers(Input::post('extranet')['memberGroupMembers'] ? (int) Input::post('extranet')['memberGroupMembers'] : null)
            ;

            $coreConfig->setSgExtranet($extranetConfig);
        }
        /**
         * Even if $coreConfig is a copy by value and not by reference
         * Reloading the configuration when doing a backup
         * do have an influence on $coreConfig ...
         * So let's clone it to avoid any trouble.
         */
        $coreConfig2 = clone $coreConfig;

        $this->coreConfigurationManager->createBackupFile();
        $this->coreConfigurationManager->save($coreConfig2);
    }

    private function fillConfigurationManagerForm(): void
    {
        $themes = ThemeModel::findAll(['order' => 'name ASC']);
        $arrThemes = [];
        $arrModules = [];
        $arrLayouts = [];
        $arrPages = [];
        $arrArticles = [];
        $arrContents = [];
        if ($themes) {
            while ($themes->next()) {
                $objTheme = $themes->current();
                $arrThemes[$objTheme->id] = [
                    'value' => (int) $objTheme->id,
                    'text' => $objTheme->name,
                    'selected' => false,
                ];

                $modules = ModuleModel::findBy('pid', $objTheme->id, ['order' => 'name ASC']);
                if ($modules) {
                    while ($modules->next()) {
                        $objModule = $modules->current();
                        // $arrLayouts[$objTheme->id]['options'][$objModule->id] = [
                        $arrModules[$objModule->id] = [
                            'value' => (int) $objModule->id,
                            'text' => $objTheme->name.' | '.$objModule->name.' ('.$objModule->type.')',
                            'selected' => false,
                        ];
                    }
                }

                // $arrLayouts[$objTheme->id] = [
                //     'text' => $objTheme->name,
                //     'options' => [],
                // ];

                $layouts = LayoutModel::findBy('pid', $objTheme->id, ['order' => 'name ASC']);
                if ($layouts) {
                    while ($layouts->next()) {
                        $objLayout = $layouts->current();
                        // $arrLayouts[$objTheme->id]['options'][$objLayout->id] = [
                        $arrLayouts[$objLayout->id] = [
                            'value' => (int) $objLayout->id,
                            'text' => $objTheme->name.' | '.$objLayout->name,
                            'selected' => false,
                        ];

                        // if (!$arrPages[$objTheme->id]) {
                        //     $arrPages[$objTheme->id] = [
                        //         'text' => $objTheme->name.' ('.$objLayout->name.')',
                        //         'options' => [],
                        //     ];
                        // }

                        $pages = PageModel::findBy('layout', $objLayout->id, ['order' => 'sorting ASC']);
                        if ($pages) {
                            while ($pages->next()) {
                                $objPage = $pages->current();
                                // $arrPages[$objTheme->id]['options'][$objPage->id] = [
                                $arrPages[$objPage->id] = [
                                    'value' => (int) $objPage->id,
                                    // 'text' => $objPage->sorting.' - '.$objPage->title.' ('.$objPage->type.')',
                                    'text' => $objTheme->name.' | '.$objPage->sorting.' - '.$objPage->title.' ('.$objPage->type.')',
                                    'selected' => false,
                                ];

                                // if (!$arrArticles[$objPage->id]) {
                                //     $arrArticles[$objPage->id] = [
                                //         'text' => $objPage->title.' ('.$objPage->title.')',
                                //         'options' => [],
                                //     ];
                                // }

                                $articles = ArticleModel::findBy('pid', $objPage->id, ['order' => 'sorting ASC']);
                                if ($articles) {
                                    while ($articles->next()) {
                                        $objArticle = $articles->current();
                                        // $arrArticles[$objPage->id]['options'][$objArticle->id] = [
                                        $arrArticles[$objArticle->id] = [
                                            'value' => (int) $objArticle->id,
                                            // 'text' => $objArticle->sorting.' - '.$objArticle->title.' ('.$objArticle->type.')',
                                            'text' => $objTheme->name.' | '.$objPage->sorting.' - '.$objPage->title.' ('.$objPage->type.') | '.$objArticle->sorting.' - '.$objArticle->title,
                                            'selected' => false,
                                        ];

                                        // if (!$arrContents[$objArticle->id]) {
                                        //     $arrContents[$objArticle->id] = [
                                        //         'text' => $objArticle->title.' ('.$objArticle->inColumn.')',
                                        //         'options' => [],
                                        //     ];
                                        // }

                                        $contents = ContentModel::findBy('pid', $objArticle->id, ['order' => 'sorting ASC']);
                                        if ($contents) {
                                            while ($contents->next()) {
                                                $objContent = $contents->current();
                                                // $arrContents[$objArticle->id]['options'][$objContent->id] = [
                                                $arrContents[$objContent->id] = [
                                                    'value' => (int) $objContent->id,
                                                    // 'text' => $objContent->sorting.' - '.$objContent->title.' ('.$objContent->type.')',
                                                    'text' => $objTheme->name.' | '.$objPage->sorting.' - '.$objPage->title.' ('.$objPage->type.') | '.$objArticle->sorting.' - '.$objArticle->title.' | '.$objContent->sorting.' - '.$objContent->title.' ('.$objContent->type.')',
                                                    'selected' => false,
                                                ];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        // $arrPages[0] = [
        //     'text' => 'No theme (No layout)',
        //     'options' => [],
        // ];
        $pages = PageModel::findBy('layout', 0, ['order' => 'sorting ASC']);
        if ($pages) {
            while ($pages->next()) {
                $objPage = $pages->current();
                // $arrPages[0]['options'][$pages->id] = [
                $arrPages[$pages->id] = [
                    'value' => (int) $objPage->id,
                    'text' => 'No theme | '.$objPage->sorting.' - '.$objPage->title.' ('.$objPage->type.')',
                    'selected' => false,
                ];
                // if (!$arrArticles[$objPage->id]) {
                //     $arrArticles[$objPage->id] = [
                //         'text' => $objPage->title.' ('.$objPage->title.')',
                //         'options' => [],
                //     ];
                // }

                $articles = ArticleModel::findBy('pid', $objPage->id, ['order' => 'sorting ASC']);
                if ($articles) {
                    while ($articles->next()) {
                        $objArticle = $articles->current();
                        // $arrArticles[$objPage->id]['options'][$objArticle->id] = [
                        $arrArticles[$objArticle->id] = [
                            'value' => (int) $objArticle->id,
                            // 'text' => $objArticle->sorting.' - '.$objArticle->title.' ('.$objArticle->type.')',
                            'text' => 'No theme | '.$objPage->sorting.' - '.$objPage->title.' ('.$objPage->type.') | '.$objArticle->sorting.' - '.$objArticle->title,
                            'selected' => false,
                        ];

                        // if (!$arrContents[$objArticle->id]) {
                        //     $arrContents[$objArticle->id] = [
                        //         'text' => $objArticle->title.' ('.$objArticle->inColumn.')',
                        //         'options' => [],
                        //     ];
                        // }

                        $contents = ContentModel::findBy('pid', $objArticle->id, ['order' => 'sorting ASC']);
                        if ($contents) {
                            while ($contents->next()) {
                                $objContent = $contents->current();
                                // $arrContents[$objArticle->id]['options'][$objContent->id] = [
                                $arrContents[$objContent->id] = [
                                    'value' => (int) $objContent->id,
                                    // 'text' => $objContent->sorting.' - '.$objContent->title.' ('.$objContent->type.')',
                                    'text' => 'No theme | '.$objPage->sorting.' - '.$objPage->title.' ('.$objPage->type.') | '.$objArticle->sorting.' - '.$objArticle->title.' | '.$objContent->sorting.' - '.$objContent->title.' ('.$objContent->type.')',
                                    'selected' => false,
                                ];
                            }
                        }
                    }
                }
            }
        }

        $arrUserGroups = [];
        $userGroups = UserGroupModel::findAll();
        if ($userGroups) {
            while ($userGroups->next()) {
                $arrUserGroups[$userGroups->id] = [
                    'value' => (int) $userGroups->id,
                    'text' => $userGroups->name,
                ];
            }
        }

        $arrUsers = [];
        $users = UserModel::findAll();
        if ($users) {
            while ($users->next()) {
                $arrUsers[$users->id] = [
                    'value' => (int) $users->id,
                    'text' => $users->name,
                ];
            }
        }

        $arrNcGateways = [];
        $gateways = NcGatewayModel::findAll();
        if ($gateways) {
            while ($gateways->next()) {
                $arrNcGateways[$gateways->id] = [
                    'value' => (int) $gateways->id,
                    'text' => $gateways->title.' ('.$gateways->type.')',
                ];
            }
        }

        $arrNcNotifications = [];
        $arrNcMessages = [];
        $arrNcLanguages = [];
        $notifications = NcNotificationModel::findAll();

        if ($notifications) {
            while ($notifications->next()) {
                $arrNcNotifications[$notifications->id] = [
                    'value' => (int) $notifications->id,
                    'text' => $notifications->title.' ('.$notifications->type.')',
                ];

                $messages = NcMessageModel::findBy('pid', $notifications->id);
                if ($messages) {
                    while ($messages->next()) {
                        $arrNcMessages[$messages->id] = [
                            'value' => (int) $messages->id,
                            'text' => $notifications->title.' ('.$notifications->type.') | '.$messages->title.' ('.$messages->gateway_type.')',
                        ];

                        $languages = NcLanguageModel::findBy('pid', $messages->id);
                        if ($languages) {
                            while ($languages->next()) {
                                $arrNcLanguages[$languages->id] = [
                                    'value' => (int) $languages->id,
                                    'text' => $notifications->title.' ('.$notifications->type.') | '.$messages->title.' ('.$messages->gateway_type.') | '.$languages->language,
                                ];
                            }
                        }
                    }
                }
            }
        }

        $empty = [['text' => '-', 'value' => null]];

        $this->Template->themes = $empty + $arrThemes;
        $this->Template->modules = $empty + $arrModules;
        $this->Template->layouts = $empty + $arrLayouts;
        $this->Template->pages = $empty + $arrPages;
        $this->Template->articles = $empty + $arrArticles;
        $this->Template->contents = $empty + $arrContents;
        $this->Template->users = $empty + $arrUsers;
        $this->Template->userGroups = $empty + $arrUserGroups;
        $this->Template->ncGateways = $empty + $arrNcGateways;
        $this->Template->ncNotifications = $empty + $arrNcNotifications;
        $this->Template->ncMessages = $empty + $arrNcMessages;
        $this->Template->ncLanguages = $empty + $arrNcLanguages;

        // core
        /** @var CoreConfig */
        $coreConfig = $this->coreConfigurationManager->load();

        $analyticsRaw = CoreConfig::ANALYTICS_SYSTEMS_ALLOWED;
        $analytics = [];
        foreach ($analyticsRaw as $mode) {
            $analytics[$mode] = [
                'text' => $mode,
                'value' => $mode,
                'selected' => false,
            ];
        }
        if ($analytics[$coreConfig->getSgAnalytics()]) {
            $analytics[$coreConfig->getSgAnalytics()]['selected'] = true;
        }

        $modesRaw = CoreConfig::MODES_ALLOWED;
        $modes = [];
        foreach ($modesRaw as $mode) {
            $modes[$mode] = [
                'text' => $mode,
                'value' => $mode,
                'selected' => false,
            ];
        }
        if ($modes[$coreConfig->getSgMode()]) {
            $modes[$coreConfig->getSgMode()]['selected'] = true;
        }

        $core = [
            'installComplete' => $coreConfig->getSgInstallComplete(),
            'version' => $coreConfig->getSgVersion(),
            'framwayPath' => $coreConfig->getSgFramwayPath() ?? CoreConfig::DEFAULT_FRAMWAY_PATH,
            // 'framwayThemes' => $coreConfig->getSgFramwayThemes(),
            'googleFonts' => implode(',', $coreConfig->getSgGoogleFonts()),
            // 'selectedModules' => $coreConfig->getSgSelectedModules(),
            'mode' => $modes,
            'analytics' => $analytics,
            'analyticsGoogleId' => $coreConfig->getSgAnalyticsGoogleId(),
            'analyticsMatomoHost' => $coreConfig->getSgAnalyticsMatomoHost() ?? CoreConfig::DEFAULT_ANALYTICS_SYSTEM_MATOMO_HOST,
            'analyticsMatomoId' => $coreConfig->getSgAnalyticsMatomoId(),
            'ownerName' => $coreConfig->getSgOwnerName(),
            'ownerDomain' => $coreConfig->getSgOwnerDomain(),
            'ownerHost' => $coreConfig->getSgOwnerHost() ?? CoreConfig::DEFAULT_OWNER_HOST,
            'ownerLogo' => $coreConfig->getSgOwnerLogo(),
            'ownerStatus' => $coreConfig->getSgOwnerStatus(),
            'ownerStreet' => $coreConfig->getSgOwnerStreet(),
            'ownerPostal' => $coreConfig->getSgOwnerPostal(),
            'ownerCity' => $coreConfig->getSgOwnerCity(),
            'ownerRegion' => $coreConfig->getSgOwnerRegion(),
            'ownerCountry' => $coreConfig->getSgOwnerCountry(),
            'ownerSiret' => $coreConfig->getSgOwnerSiret(),
            'ownerDpoName' => $coreConfig->getSgOwnerDpoName(),
            'ownerDpoEmail' => $coreConfig->getSgOwnerDpoEmail(),
            'theme' => $coreConfig->getSgTheme(),
            'layoutFullwidth' => $coreConfig->getSgLayoutFullwidth(),
            'layoutStandard' => $coreConfig->getSgLayoutStandard(),
            'pageRoot' => $coreConfig->getSgPageRoot(),
            'pageHome' => $coreConfig->getSgPageHome(),
            'page404' => $coreConfig->getSgPage404(),
            'pageLegalNotice' => $coreConfig->getSgPageLegalNotice(),
            'pagePrivacyPolitics' => $coreConfig->getSgPagePrivacyPolitics(),
            'pageSitemap' => $coreConfig->getSgPageSitemap(),
            'articleHome' => $coreConfig->getSgArticleHome(),
            'article404' => $coreConfig->getSgArticle404(),
            'articleLegalNotice' => $coreConfig->getSgArticleLegalNotice(),
            'articlePrivacyPolitics' => $coreConfig->getSgArticlePrivacyPolitics(),
            'articleSitemap' => $coreConfig->getSgArticleSitemap(),
            'content404Headline' => $coreConfig->getSgContent404Headline(),
            'content404Sitemap' => $coreConfig->getSgContent404Sitemap(),
            'contentLegalNotice' => $coreConfig->getSgContentLegalNotice(),
            'contentPrivacyPolitics' => $coreConfig->getSgContentPrivacyPolitics(),
            'contentSitemapHeadline' => $coreConfig->getSgContentSitemapHeadline(),
            'contentSitemap' => $coreConfig->getSgContentSitemap(),
            'userWebmaster' => $coreConfig->getSgUserWebmaster() ?? CoreConfig::DEFAULT_USER_USERNAME,
            'userGroupRedactors' => $coreConfig->getSgUserGroupRedactors(),
            'userGroupAdministrators' => $coreConfig->getSgUserGroupAdministrators() ?? CoreConfig::DEFAULT_USER_GROUP_ADMIN_NAME,
            'notificationGatewayEmail' => $coreConfig->getSgNotificationGatewayEmail(),
            // 'modules' => $coreConfig->getSgModules(),
            'apiKey' => $coreConfig->getSgApiKey() ?? StringUtil::generateKey(),
            'encryptionKey' => $coreConfig->getSgEncryptionKey() ?? StringUtil::generateKey(),
            'airtableApiKey' => $coreConfig->getSgAirtableApiKey(),
            'airtableApiKeyForRead' => $coreConfig->getSgAirtableApiKeyForRead(),
            'airtableApiKeyForWrite' => $coreConfig->getSgAirtableApiKeyForWrite(),
        ];
        foreach ($coreConfig->getSgModules() as $module) {
            $core['modules'][$module->key] = (int) $module->id;
        }

        $this->Template->core = $core;

        // blog
        /** @var BlogConfig */
        $blogConfig = $coreConfig->getSgBlog();
        $archivedModeRaw = BlogConfig::ARCHIVE_MODES_ALLOWED;
        $archivedMode = [];
        foreach ($archivedModeRaw as $mode) {
            $archivedMode[$mode] = [
                'text' => !empty($mode) ? $mode : 'N/A',
                'value' => $mode,
                'selected' => false,
            ];
        }
        if ($archivedMode[$blogConfig->getSgArchivedMode()]) {
            $archivedMode[$blogConfig->getSgArchivedMode()]['selected'] = true;
        }

        $modesRaw = BlogConfig::MODES_ALLOWED;
        $modes = [];
        foreach ($modesRaw as $mode) {
            $modes[$mode] = [
                'text' => $mode,
                'value' => $mode,
                'selected' => false,
            ];
        }
        if ($modes[$blogConfig->getSgMode()]) {
            $modes[$blogConfig->getSgMode()]['selected'] = true;
        }

        $newsArchives = NewsArchiveModel::findAll();
        $arrNewsArchives = [];
        if ($newsArchives) {
            while ($newsArchives->next()) {
                $objNA = $newsArchives->current();
                $arrNewsArchives[$objNA->id] = [
                    'text' => $objNA->title,
                    'value' => (int) $objNA->id,
                    'selected' => false,
                ];
            }
        }
        if ($arrNewsArchives[$blogConfig->getSgNewsArchive()]) {
            $arrNewsArchives[$blogConfig->getSgNewsArchive()]['selected'] = true;
        }

        $blog = [
            'installComplete' => $blogConfig->getSgInstallComplete(),
            'archived' => $blogConfig->getSgArchived(),
            'archivedAt' => $blogConfig->getSgArchivedAt(),
            'archivedMode' => $archivedMode,
            'mode' => $modes,
            'newsArchive' => $blogConfig->getSgNewsArchive(),
            'page' => $blogConfig->getSgPage(),
            'article' => $blogConfig->getSgArticle(),
            'contentList' => $blogConfig->getSgContentList(),
            'moduleReader' => $blogConfig->getSgModuleReader(),
            'moduleList' => $blogConfig->getSgModuleList(),
            'presets' => [],
            'currentPresetIndex' => $blogConfig->getSgCurrentPresetIndex(),
        ];

        $arrBlogPresets = [];
        foreach ($blogConfig->getSgPresets() as $index => $preset) {
            $blog['presets'][$index] = [
                'newsFolder' => $preset->getSgNewsFolder(),
                'newsArchiveTitle' => $preset->getSgNewsArchiveTitle(),
                'newsListPerPage' => $preset->getSgNewsListPerPage(),
                'pageTitle' => $preset->getSgPageTitle(),
            ];
            $arrBlogPresets[$index] = [
                'value' => $index,
                'text' => $index.' | '.$preset->getSgPageTitle().' | '.$preset->getSgNewsArchiveTitle().' | '.$preset->getSgNewsListPerPage().' | '.$preset->getSgNewsListPerPage(),
            ];
        }
        if (empty($arrBlogPresets)) {
            $blog['presets'][0] = [
                'newsFolder' => BlogPresetConfig::DEFAULT_FOLDER_PATH,
                'newsArchiveTitle' => BlogPresetConfig::DEFAULT_ARCHIVE_TITLE,
                'newsListPerPage' => BlogPresetConfig::DEFAULT_NEWS_PER_PAGE,
                'pageTitle' => BlogPresetConfig::DEFAULT_PAGE_TITLE,
            ];
            $arrBlogPresets[0] = [
                'value' => 0,
                'text' => '0 | Default',
            ];
        }

        $this->Template->blog = $blog;
        $this->Template->newsArchives = $empty + $arrNewsArchives;
        $this->Template->blogPresets = $arrBlogPresets;

        // events
        /** @var EventsConfig */
        $eventConfig = $coreConfig->getSgEvents();
        $archivedModeRaw = EventsConfig::ARCHIVE_MODES_ALLOWED;
        $archivedMode = [];
        foreach ($archivedModeRaw as $mode) {
            $archivedMode[$mode] = [
                'text' => !empty($mode) ? $mode : 'N/A',
                'value' => $mode,
                'selected' => false,
            ];
        }
        if ($archivedMode[$eventConfig->getSgArchivedMode()]) {
            $archivedMode[$eventConfig->getSgArchivedMode()]['selected'] = true;
        }

        $modesRaw = EventsConfig::MODES_ALLOWED;
        $modes = [];
        foreach ($modesRaw as $mode) {
            $modes[$mode] = [
                'text' => $mode,
                'value' => $mode,
                'selected' => false,
            ];
        }
        if ($modes[$eventConfig->getSgMode()]) {
            $modes[$eventConfig->getSgMode()]['selected'] = true;
        }

        $arrCalendars = [];
        $calendars = CalendarModel::findAll();
        if ($calendars) {
            while ($calendars->next()) {
                $objCalendar = $calendars->current();
                $arrCalendars[$objCalendar->id] = [
                    'value' => (int) $objCalendar->id,
                    'text' => $objCalendar->title,
                ];
            }
        }

        $events = [
            'installComplete' => $eventConfig->getSgInstallComplete(),
            'archived' => $eventConfig->getSgArchived(),
            'archivedAt' => $eventConfig->getSgArchivedAt(),
            'archivedMode' => $archivedMode,
            'mode' => $modes,

            'eventsFolder' => $eventConfig->getSgEventsFolder() ?? EventsConfig::DEFAULT_FOLDER_PATH,
            'page' => $eventConfig->getSgPage(),
            'article' => $eventConfig->getSgArticle(),
            'contentList' => $eventConfig->getSgContentList(),
            'calendar' => $eventConfig->getSgCalendar(),
            'moduleReader' => $eventConfig->getSgModuleReader(),
            'moduleList' => $eventConfig->getSgModuleList(),
            'moduleCalendar' => $eventConfig->getSgModuleCalendar(),

            'calendarTitle' => $eventConfig->getSgCalendarTitle() ?? EventsConfig::DEFAULT_FEED_TITLE,
            'eventsListPerPage' => $eventConfig->getSgEventsListPerPage() ?? EventsConfig::DEFAULT_EVENTS_PER_PAGE,
            'pageTitle' => $eventConfig->getSgPageTitle() ?? EventsConfig::DEFAULT_PAGE_TITLE,
        ];

        $this->Template->events = $events;
        $this->Template->calendars = $empty + $arrCalendars;

        // FAQ
        /** @var FaqConfig */
        $eventConfig = $coreConfig->getSgFaq();
        $archivedModeRaw = FaqConfig::ARCHIVE_MODES_ALLOWED;
        $archivedMode = [];
        foreach ($archivedModeRaw as $mode) {
            $archivedMode[$mode] = [
                'text' => !empty($mode) ? $mode : 'N/A',
                'value' => $mode,
                'selected' => false,
            ];
        }
        if ($archivedMode[$eventConfig->getSgArchivedMode()]) {
            $archivedMode[$eventConfig->getSgArchivedMode()]['selected'] = true;
        }

        $arrFaqCategories = [];
        $faqCategories = FaqCategoryModel::findAll();
        if ($faqCategories) {
            while ($faqCategories->next()) {
                $objFaqCategory = $faqCategories->current();
                $arrFaqCategories[$objFaqCategory->id] = [
                    'value' => (int) $objFaqCategory->id,
                    'text' => $objFaqCategory->title,
                ];
            }
        }

        $faq = [
            'installComplete' => $eventConfig->getSgInstallComplete(),
            'archived' => $eventConfig->getSgArchived(),
            'archivedAt' => $eventConfig->getSgArchivedAt(),
            'archivedMode' => $archivedMode,

            'faqFolder' => $eventConfig->getSgFaqFolder() ?? FaqConfig::DEFAULT_FOLDER_PATH,
            'page' => $eventConfig->getSgPage(),
            'article' => $eventConfig->getSgArticle(),
            'content' => $eventConfig->getSgContent(),
            'faqCategory' => $eventConfig->getSgFaqCategory(),
            'moduleFaq' => $eventConfig->getSgModuleFaq(),

            'faqTitle' => $eventConfig->getSgFaqTitle() ?? FaqConfig::DEFAULT_FEED_TITLE,
            'pageTitle' => $eventConfig->getSgPageTitle() ?? FaqConfig::DEFAULT_PAGE_TITLE,
        ];

        $this->Template->faq = $faq;
        $this->Template->faqCategories = $empty + $arrFaqCategories;

        // FormContact
        /** @var FormContactConfig */
        $fcConfig = $coreConfig->getSgFormContact();
        $archivedModeRaw = FormContactConfig::ARCHIVE_MODES_ALLOWED;
        $archivedMode = [];
        foreach ($archivedModeRaw as $mode) {
            $archivedMode[$mode] = [
                'text' => !empty($mode) ? $mode : 'N/A',
                'value' => $mode,
                'selected' => false,
            ];
        }
        if ($archivedMode[$fcConfig->getSgArchivedMode()]) {
            $archivedMode[$fcConfig->getSgArchivedMode()]['selected'] = true;
        }

        $arrForms = [];
        $arrFields = [];
        $forms = FormModel::findAll();
        if ($forms) {
            while ($forms->next()) {
                $objForm = $forms->current();
                $arrForms[$objForm->id] = [
                    'value' => (int) $objForm->id,
                    'text' => $objForm->title,
                ];
                $fields = FormFieldModel::findBy('pid', $objForm->id);
                if ($fields) {
                    while ($fields->next()) {
                        $objField = $fields->current();
                        $arrFields[$objField->id] = [
                            'value' => (int) $objField->id,
                            'text' => $objForm->title.' | '.$objField->name.' ('.$objField->type.')',
                        ];
                    }
                }
            }
        }

        $formContact = [
            'installComplete' => $fcConfig->getSgInstallComplete(),
            'archived' => $fcConfig->getSgArchived(),
            'archivedAt' => $fcConfig->getSgArchivedAt(),
            'archivedMode' => $archivedMode,

            'pageForm' => $fcConfig->getSgPageForm(),
            'pageFormSent' => $fcConfig->getSgPageFormSent(),
            'articleForm' => $fcConfig->getSgArticleForm(),
            'articleFormSent' => $fcConfig->getSgArticleFormSent(),
            'contentHeadlineArticleForm' => $fcConfig->getSgContentHeadlineArticleForm(),
            'contentFormArticleForm' => $fcConfig->getSgContentFormArticleForm(),
            'contentHeadlineArticleFormSent' => $fcConfig->getSgContentHeadlineArticleFormSent(),
            'contentTextArticleFormSent' => $fcConfig->getSgContentTextArticleFormSent(),

            'formContact' => $fcConfig->getSgFormContact(),
            'fieldName' => $fcConfig->getSgFieldName(),
            'fieldEmail' => $fcConfig->getSgFieldEmail(),
            'fieldMessage' => $fcConfig->getSgFieldMessage(),
            'fieldConsentDataTreatment' => $fcConfig->getSgFieldConsentDataTreatment(),
            'fieldConsentDataSave' => $fcConfig->getSgFieldConsentDataSave(),
            'fieldCaptcha' => $fcConfig->getSgFieldCaptcha(),
            'fieldSubmit' => $fcConfig->getSgFieldSubmit(),

            'notificationMessageUser' => $fcConfig->getSgNotificationMessageUser(),
            'notificationMessageAdmin' => $fcConfig->getSgNotificationMessageAdmin(),

            'notificationMessageUserLanguage' => $fcConfig->getSgNotificationMessageUserLanguage(),
            'notificationMessageAdminLanguage' => $fcConfig->getSgNotificationMessageAdminLanguage(),

            'formContactTitle' => $fcConfig->getSgFormContactTitle() ?? FormContactConfig::DEFAULT_FEED_TITLE,
            'pageTitle' => $fcConfig->getSgPageTitle() ?? FormContactConfig::DEFAULT_PAGE_TITLE,
        ];

        $this->Template->formContact = $formContact;
        $this->Template->forms = $empty + $arrForms;
        $this->Template->fields = $empty + $arrFields;

        // FormDataManager
        /** @var FormDataManagerConfig */
        $fdmConfig = $coreConfig->getSgFormDataManager();
        $archivedModeRaw = FormDataManagerConfig::ARCHIVE_MODES_ALLOWED;
        $archivedMode = [];
        foreach ($archivedModeRaw as $mode) {
            $archivedMode[$mode] = [
                'text' => !empty($mode) ? $mode : 'N/A',
                'value' => $mode,
                'selected' => false,
            ];
        }
        if ($archivedMode[$fdmConfig->getSgArchivedMode()]) {
            $archivedMode[$fdmConfig->getSgArchivedMode()]['selected'] = true;
        }

        $formDataManager = [
            'installComplete' => $fdmConfig->getSgInstallComplete(),
            'archived' => $fdmConfig->getSgArchived(),
            'archivedAt' => $fdmConfig->getSgArchivedAt(),
            'archivedMode' => $archivedMode,
        ];

        $this->Template->formDataManager = $formDataManager;

        // Extranet
        /** @var ExtranetConfig */
        $extranetConfig = $coreConfig->getSgExtranet();
        $archivedModeRaw = ExtranetConfig::ARCHIVE_MODES_ALLOWED;
        $archivedMode = [];
        foreach ($archivedModeRaw as $mode) {
            $archivedMode[$mode] = [
                'text' => !empty($mode) ? $mode : 'N/A',
                'value' => $mode,
                'selected' => false,
            ];
        }
        if ($archivedMode[$extranetConfig->getSgArchivedMode()]) {
            $archivedMode[$extranetConfig->getSgArchivedMode()]['selected'] = true;
        }

        $arrMembers = [];
        $members = Member::findAll();
        if ($members) {
            while ($members->next()) {
                $objMember = $members->current();
                $arrMembers[$objMember->id] = [
                    'value' => (int) $objMember->id,
                    'text' => $objMember->firstname.' '.$objMember->lastname.' ('.$objMember->email.')',
                ];
            }
        }

        $arrMemberGroups = [];
        $memberGroups = MemberGroupModel::findAll();
        if ($memberGroups) {
            while ($memberGroups->next()) {
                $objMemberGroup = $memberGroups->current();
                $arrMemberGroups[$objMemberGroup->id] = [
                    'value' => (int) $objMemberGroup->id,
                    'text' => $objMemberGroup->name,
                ];
            }
        }

        $extranet = [
            'installComplete' => $extranetConfig->getSgInstallComplete(),
            'archived' => $extranetConfig->getSgArchived(),
            'archivedAt' => $extranetConfig->getSgArchivedAt(),
            'archivedMode' => $archivedMode,

            'extranetFolder' => $extranetConfig->getSgExtranetFolder() ?? ExtranetConfig::DEFAULT_FOLDER_PATH,
            'canSubscribe' => $extranetConfig->getSgCanSubscribe() ?? ExtranetConfig::DEFAULT_CAN_SUBSCRIBE,
            'memberGroupMembersTitle' => $extranetConfig->getSgMemberGroupMembersTitle() ?? ExtranetConfig::DEFAULT_MEMBER_GROUP_MEMBERS_TITLE,
            'pageExtranetTitle' => $extranetConfig->getSgPageExtranetTitle() ?? ExtranetConfig::DEFAULT_PAGE_EXTRANET_TITLE,

            'pageExtranet' => $extranetConfig->getSgPageExtranet(),
            'page401' => $extranetConfig->getSgPage401(),
            'page403' => $extranetConfig->getSgPage403(),
            'pageContent' => $extranetConfig->getSgPageContent(),
            'pageData' => $extranetConfig->getSgPageData(),
            'pageDataConfirm' => $extranetConfig->getSgPageDataConfirm(),
            'pagePassword' => $extranetConfig->getSgPagePassword(),
            'pagePasswordConfirm' => $extranetConfig->getSgPagePasswordConfirm(),
            'pagePasswordValidate' => $extranetConfig->getSgPagePasswordValidate(),
            'pageLogout' => $extranetConfig->getSgPageLogout(),
            'pageSubscribe' => $extranetConfig->getSgPageSubscribe(),
            'pageSubscribeConfirm' => $extranetConfig->getSgPageSubscribeConfirm(),
            'pageSubscribeValidate' => $extranetConfig->getSgPageSubscribeValidate(),
            'pageUnsubscribeConfirm' => $extranetConfig->getSgPageUnsubscribeConfirm(),

            'articleExtranet' => $extranetConfig->getSgArticleExtranet(),
            'article401' => $extranetConfig->getSgArticle401(),
            'article403' => $extranetConfig->getSgArticle403(),
            'articleContent' => $extranetConfig->getSgArticleContent(),
            'articleData' => $extranetConfig->getSgArticleData(),
            'articleDataConfirm' => $extranetConfig->getSgArticleDataConfirm(),
            'articlePassword' => $extranetConfig->getSgArticlePassword(),
            'articlePasswordConfirm' => $extranetConfig->getSgArticlePasswordConfirm(),
            'articlePasswordValidate' => $extranetConfig->getSgArticlePasswordValidate(),
            'articleLogout' => $extranetConfig->getSgArticleLogout(),
            'articleSubscribe' => $extranetConfig->getSgArticleSubscribe(),
            'articleSubscribeConfirm' => $extranetConfig->getSgArticleSubscribeConfirm(),
            'articleSubscribeValidate' => $extranetConfig->getSgArticleSubscribeValidate(),
            'articleUnsubscribeConfirm' => $extranetConfig->getSgArticleUnsubscribeConfirm(),

            'moduleLogin' => $extranetConfig->getSgModuleLogin(),
            'moduleLogout' => $extranetConfig->getSgModuleLogout(),
            'moduleData' => $extranetConfig->getSgModuleData(),
            'modulePassword' => $extranetConfig->getSgModulePassword(),
            'moduleNav' => $extranetConfig->getSgModuleNav(),
            'moduleSubscribe' => $extranetConfig->getSgModuleSubscribe(),
            'moduleCloseAccount' => $extranetConfig->getSgModuleCloseAccount(),

            'notificationChangeData' => $extranetConfig->getSgNotificationChangeData(),
            'notificationPassword' => $extranetConfig->getSgNotificationPassword(),
            'notificationSubscription' => $extranetConfig->getSgNotificationSubscription(),

            'notificationChangeDataMessage' => $extranetConfig->getSgNotificationChangeDataMessage(),
            'notificationPasswordMessage' => $extranetConfig->getSgNotificationPasswordMessage(),
            'notificationSubscriptionMessage' => $extranetConfig->getSgNotificationSubscriptionMessage(),

            'notificationChangeDataMessageLanguage' => $extranetConfig->getSgNotificationChangeDataMessageLanguage(),
            'notificationPasswordMessageLanguage' => $extranetConfig->getSgNotificationPasswordMessageLanguage(),
            'notificationSubscriptionMessageLanguage' => $extranetConfig->getSgNotificationSubscriptionMessageLanguage(),

            'contentArticleExtranetHeadline' => $extranetConfig->getSgContentArticleExtranetHeadline(),
            'contentArticleExtranetModuleLoginGuests' => $extranetConfig->getSgContentArticleExtranetModuleLoginGuests(),
            'contentArticleExtranetGridStartA' => $extranetConfig->getSgContentArticleExtranetGridStartA(),
            'contentArticleExtranetGridStartB' => $extranetConfig->getSgContentArticleExtranetGridStartB(),
            'contentArticleExtranetModuleLoginLogged' => $extranetConfig->getSgContentArticleExtranetModuleLoginLogged(),
            'contentArticleExtranetModuleNav' => $extranetConfig->getSgContentArticleExtranetModuleNav(),
            'contentArticleExtranetGridStopB' => $extranetConfig->getSgContentArticleExtranetGridStopB(),
            'contentArticleExtranetGridStopA' => $extranetConfig->getSgContentArticleExtranetGridStopA(),

            'contentArticle401Headline' => $extranetConfig->getSgContentArticle401Headline(),
            'contentArticle401Text' => $extranetConfig->getSgContentArticle401Text(),
            'contentArticle401ModuleLoginGuests' => $extranetConfig->getSgContentArticle401ModuleLoginGuests(),

            'contentArticle403Headline' => $extranetConfig->getSgContentArticle403Headline(),
            'contentArticle403Text' => $extranetConfig->getSgContentArticle403Text(),
            'contentArticle403Hyperlink' => $extranetConfig->getSgContentArticle403Hyperlink(),

            'contentArticleContentHeadline' => $extranetConfig->getSgContentArticleContentHeadline(),
            'contentArticleContentText' => $extranetConfig->getSgContentArticleContentText(),

            'contentArticleDataHeadline' => $extranetConfig->getSgContentArticleDataHeadline(),
            'contentArticleDataModuleData' => $extranetConfig->getSgContentArticleDataModuleData(),
            'contentArticleDataHeadlineCloseAccount' => $extranetConfig->getSgContentArticleDataHeadlineCloseAccount(),
            'contentArticleDataTextCloseAccount' => $extranetConfig->getSgContentArticleDataTextCloseAccount(),
            'contentArticleDataModuleCloseAccount' => $extranetConfig->getSgContentArticleDataModuleCloseAccount(),

            'contentArticleDataConfirmHeadline' => $extranetConfig->getSgContentArticleDataConfirmHeadline(),
            'contentArticleDataConfirmText' => $extranetConfig->getSgContentArticleDataConfirmText(),
            'contentArticleDataConfirmHyperlink' => $extranetConfig->getSgContentArticleDataConfirmHyperlink(),

            'contentArticlePasswordHeadline' => $extranetConfig->getSgContentArticlePasswordHeadline(),
            'contentArticlePasswordModulePassword' => $extranetConfig->getSgContentArticlePasswordModulePassword(),

            'contentArticlePasswordConfirmHeadline' => $extranetConfig->getSgContentArticlePasswordConfirmHeadline(),
            'contentArticlePasswordConfirmText' => $extranetConfig->getSgContentArticlePasswordConfirmText(),

            'contentArticlePasswordValidateHeadline' => $extranetConfig->getSgContentArticlePasswordValidateHeadline(),
            'contentArticlePasswordValidateModulePassword' => $extranetConfig->getSgContentArticlePasswordValidateModulePassword(),

            'contentArticleLogoutModuleLogout' => $extranetConfig->getSgContentArticleLogoutModuleLogout(),

            'contentArticleSubscribeHeadline' => $extranetConfig->getSgContentArticleSubscribeHeadline(),
            'contentArticleSubscribeModuleSubscribe' => $extranetConfig->getSgContentArticleSubscribeModuleSubscribe(),

            'contentArticleSubscribeConfirmHeadline' => $extranetConfig->getSgContentArticleSubscribeConfirmHeadline(),
            'contentArticleSubscribeConfirmText' => $extranetConfig->getSgContentArticleSubscribeConfirmText(),

            'contentArticleSubscribeValidateHeadline' => $extranetConfig->getSgContentArticleSubscribeValidateHeadline(),
            'contentArticleSubscribeValidateText' => $extranetConfig->getSgContentArticleSubscribeValidateText(),
            'contentArticleSubscribeValidateModuleLoginGuests' => $extranetConfig->getSgContentArticleSubscribeValidateModuleLoginGuests(),

            'contentArticleUnsubscribeHeadline' => $extranetConfig->getSgContentArticleUnsubscribeHeadline(),
            'contentArticleUnsubscribeText' => $extranetConfig->getSgContentArticleUnsubscribeText(),
            'contentArticleUnsubscribeHyperlink' => $extranetConfig->getSgContentArticleUnsubscribeHyperlink(),

            'memberExample' => $extranetConfig->getSgMemberExample(),

            'memberGroupMembers' => $extranetConfig->getSgMemberGroupMembers(),
        ];

        $this->Template->extranet = $extranet;
        $this->Template->members = $empty + $arrMembers;
        $this->Template->memberGroups = $empty + $arrMemberGroups;
    }
}
