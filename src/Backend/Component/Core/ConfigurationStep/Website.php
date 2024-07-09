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

use Contao\ArticleModel;
use Contao\BackendUser;
use Contao\ContentModel;
use Contao\CoreBundle\String\HtmlDecoder;
use Contao\File;
use Contao\Files;
use Contao\FilesModel;
use Contao\Folder;
use Contao\FrontendTemplate;
use Contao\Input;
use Contao\LayoutModel;
use Contao\Model\Collection;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\ThemeModel;
use Contao\UserGroupModel;
use Contao\UserModel;
use Exception;
use WEM\SmartgearBundle\Model\NotificationCenter\Gateway;
use WEM\SmartgearBundle\Model\NotificationCenter\Language as NotificationLanguageModel;
use WEM\SmartgearBundle\Model\NotificationCenter\Message as NotificationMessageModel;
use WEM\SmartgearBundle\Model\NotificationCenter\Notification as NotificationModel;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Backend\ConfigurationStep;
use WEM\SmartgearBundle\Classes\Command\Util as CommandUtil;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\Migration\Result as MigrationResult;
use WEM\SmartgearBundle\Classes\StringUtil;
use WEM\SmartgearBundle\Classes\UserGroupModelUtil;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Classes\Utils\ArticleUtil;
use WEM\SmartgearBundle\Classes\Utils\ContentUtil;
use WEM\SmartgearBundle\Classes\Utils\ImageSizeUtil;
use WEM\SmartgearBundle\Classes\Utils\LayoutUtil;
use WEM\SmartgearBundle\Classes\Utils\ModuleUtil;
use WEM\SmartgearBundle\Classes\Utils\Notification\NcNotificationMessageLanguageUtil;
use WEM\SmartgearBundle\Classes\Utils\Notification\NcNotificationMessageUtil;
use WEM\SmartgearBundle\Classes\Utils\Notification\NcNotificationUtil;
use WEM\SmartgearBundle\Classes\Utils\PageUtil;
use WEM\SmartgearBundle\Classes\Utils\UserGroupUtil;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Model\Module;
use WEM\SmartgearBundle\Security\SmartgearPermissions;
use WEM\SmartgearBundle\Update\UpdateManager;
use WEM\UtilsBundle\Classes\CountriesUtil;
use WEM\UtilsBundle\Classes\Files as WEMFiles;
use WEM\UtilsBundle\Classes\StringUtil as WEMStringUtil;

class Website extends ConfigurationStep
{

    protected array $userGroupWebmasterOldPermissions = [];

    protected string $language;

    protected string $strTemplate = 'be_wem_sg_install_block_configuration_step_core_website';

    public function __construct(
        string                                          $module,
        string                                          $type,
        protected TranslatorInterface                   $translator,
        protected ConfigurationManager                  $configurationManager,
        protected UpdateManager                         $updateManager,
        protected CommandUtil                           $commandUtil,
        protected array                                 $userGroupUpdaters,
        protected HtmlDecoder                           $htmlDecoder,
        protected NcNotificationUtil                    $notificationUtil,
        protected NcNotificationMessageUtil             $notificationMessageUtil,
        protected NcNotificationMessageLanguageUtil     $notificationMessageLanguageUtil
    ) {
        parent::__construct($module, $type);
        $this->language = BackendUser::getInstance()->language;
        $this->title = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['Title'];
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $countries = [];
        foreach (CountriesUtil::getCountries() as $longName) {
            $countries[] = ['value' => $longName, 'label' => $longName];
        }

        $this->addFileField('sgWebsiteLogo', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgWebsiteLogo'], empty($config->getSgOwnerLogo()));
        $this->addTextField('sgOwnerName', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerName'], empty($config->getSgOwnerName()) ? $config->getSgWebsiteTitle() : $config->getSgOwnerName(), true);
        $this->addTextField('sgOwnerStatus', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerStatus'], $config->getSgOwnerStatus(), true);
        $this->addTextField('sgOwnerSiret', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerSiret'], $config->getSgOwnerSiret(), true);
        $this->addTextField('sgOwnerStreet', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerStreet'], $config->getSgOwnerStreet(), true);
        $this->addTextField('sgOwnerPostal', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerPostal'], $config->getSgOwnerPostal(), true);
        $this->addTextField('sgOwnerCity', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerCity'], $config->getSgOwnerCity(), true);
        $this->addTextField('sgOwnerRegion', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerRegion'], $config->getSgOwnerRegion(), false);
        $this->addSelectField('sgOwnerCountry', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerCountry'], $countries, empty($config->getSgOwnerCountry()) ? 'France' : $config->getSgOwnerCountry(), true);
        $this->addTextField('sgOwnerEmail', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerEmail'], $config->getSgOwnerEmail(), true);
        $this->addTextField('sgOwnerDomain', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerDomain'], empty($config->getSgOwnerDomain()) ? \Contao\Environment::get('base') : $config->getSgOwnerDomain(), true);
        $this->addTextField('sgOwnerHost', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerHost'], $config->getSgOwnerHost(), true);
        $this->addTextField('sgOwnerDpoName', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerDpoName'], $config->getSgOwnerDpoName(), true);
        $this->addTextField('sgOwnerDpoEmail', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerDpoEmail'], $config->getSgOwnerDpoEmail(), true);
        $this->addTextField('sgGoogleFonts', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgGoogleFonts'], implode(',', $config->getSgGoogleFonts()), false, '', 'text', '', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgGoogleFontsHelp']);
        $this->addCheckboxField('doBackup', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['doBackup'], '1', true, false, '', '', $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['doBackupHelp']);
    }

    /**
     * @throws Exception
     */
    public function getFilledTemplate(): FrontendTemplate
    {
        // to render the step
        $objTemplate = parent::getFilledTemplate();
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        if (!empty($config->getSgOwnerLogo())) {
            $objFileLogo = new File($config->getSgOwnerLogo());
            $objTemplate->logo = $objFileLogo->exists() ? WEMFiles::imageToBase64($objFileLogo) : '';
        } else {
            $objTemplate->logo = '';
        }

        // And return the template, parsed.
        return $objTemplate;
    }

    /**
     * @throws Exception
     */
    public function isStepValid(): bool
    {
        // check if the step is correct
        if (empty(Input::post('sgOwnerName'))) {
            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerNameEmpty']);
        }

        if (empty(Input::post('sgOwnerStatus'))) {
            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerStatusEmpty']);
        }

        if (empty(Input::post('sgOwnerSiret'))) {
            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerSiretEmpty']);
        }

        if (empty(Input::post('sgOwnerStreet'))) {
            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerStreetEmpty']);
        }

        if (empty(Input::post('sgOwnerPostal'))) {
            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerPostalEmpty']);
        }

        if (empty(Input::post('sgOwnerCity'))) {
            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerCityEmpty']);
        }

        if (empty(Input::post('sgOwnerCountry'))) {
            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerCountryEmpty']);
        }

        if (empty(Input::post('sgOwnerEmail'))) {
            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerEmailEmpty']);
        }

        if (empty(Input::post('sgOwnerDomain'))) {
            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerDomainEmpty']);
        }

        if (empty(Input::post('sgOwnerHost'))) {
            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerHostEmpty']);
        }

        if (empty(Input::post('sgOwnerDpoName'))) {
            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerDpoNameEmpty']);
        }

        if (empty(Input::post('sgOwnerDpoEmail'))) {
            throw new Exception($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['sgOwnerDpoEmailEmpty']);
        }

        return true;
    }

    /**
     * @throws Exception
     */
    public function do(): void
    {
        // do what is meant to be done in this step
        $this->updateModuleConfiguration();
        $this->createClientFilesFolders();
        $this->createClientTemplatesFolder();
        if ($_FILES !== []) {
            $objFileLogo = $this->uploadLogo();
            $this->updateModuleConfigurationLogo($objFileLogo);
        }

        $themeId = $this->createTheme();
        $this->updateModuleConfigurationTheme($themeId);

        $imageSizes = $this->createImageSizes($themeId);
        $this->updateModuleConfigurationImageSizes($imageSizes);

        $modules = $this->createModules($themeId);

        $layouts = $this->createLayouts($themeId, $modules);
        $this->updateModuleConfigurationLayouts($layouts);

        $groups = $this->createUserGroups();
        $users = $this->createUsers($groups);
        $this->updateModuleConfigurationUserAndGroups($users, $groups);

        $pages = $this->createPages($layouts, $groups, $users, $modules);
        $this->updateModuleConfigurationPages($pages);

        $articles = $this->createArticles($pages);
        $this->updateModuleConfigurationArticles($articles);

        $this->updateLayouts($layouts, $pages);

        $contents = $this->createContents($pages, $articles, $modules);
        $this->updateModuleConfigurationContents($contents);

        $modules = array_merge($this->createModules2($themeId, $pages), $modules);
        $this->updateModuleConfigurationModules($modules);

        $notificationGateways = $this->createNotificationGateways();
        $this->updateModuleConfigurationNotificationGateways($notificationGateways);

        $notificationSupport = $this->createNotificationSupportGatewayNotification();
        $this->updateModuleConfigurationNotificationSupportGatewayNotification($notificationSupport);

        $notificationSupportGatewayMessages = $this->createNotificationSupportGatewayMessages($notificationSupport);
        $this->updateModuleConfigurationNotificationSupportGatewayMessages($notificationSupportGatewayMessages);

        $notificationSupportGatewayMessagesLanguages = $this->createNotificationSupportGatewayMessagesLanguages($notificationSupportGatewayMessages);
        $this->updateModuleConfigurationNotificationSupportGatewayMessagesLanguages($notificationSupportGatewayMessagesLanguages);

        $this->updateUserGroups();

        $this->commandUtil->executeCmdPHP('cache:clear');
        $this->commandUtil->executeCmdPHP('contao:symlinks');

        $this->launchMigrations((bool) Input::post('doBackupHelp'));
    }

    protected function launchMigrations(bool $doBackup): void
    {
        $updateResult = $this->updateManager->update($doBackup);
        if ($updateResult->isSuccess()) {
            $this->addConfirm($this->translator->trans('WEMSG.UPDATEMANAGER.RESULT.success', [], 'contao_default'), $this->module);
        } else {
            $this->addError($this->translator->trans('WEMSG.UPDATEMANAGER.RESULT.fail', [], 'contao_default'), $this->module);
        }

        foreach ($updateResult->getResults() as $singleMigrationResult) {
            switch ($singleMigrationResult->getResult()->getStatus()) {
                case MigrationResult::STATUS_NOT_EXCUTED_YET:
                    $this->addInfo(sprintf('%s : %s', $singleMigrationResult->getName(), $this->translator->trans('WEMSG.UPDATEMANAGER.SINGLEMIGRATIONRESULT.statusNotexecutedyet', [], 'contao_default')), $this->module);
                break;
                case MigrationResult::STATUS_SHOULD_RUN:
                    $this->addInfo(sprintf('%s : %s', $singleMigrationResult->getName(), $this->translator->trans('WEMSG.UPDATEMANAGER.SINGLEMIGRATIONRESULT.statusShouldrun', [], 'contao_default')), $this->module);
                break;
                case MigrationResult::STATUS_SKIPPED:
                    $this->addInfo(sprintf('%s : %s', $singleMigrationResult->getName(), $this->translator->trans('WEMSG.UPDATEMANAGER.SINGLEMIGRATIONRESULT.statusSkipped', [], 'contao_default')), $this->module);
                break;
                case MigrationResult::STATUS_FAIL:
                    $this->addError(sprintf('%s : %s', $singleMigrationResult->getName(), $this->translator->trans('WEMSG.UPDATEMANAGER.SINGLEMIGRATIONRESULT.statusFail', [], 'contao_default')), $this->module);
                break;
                case MigrationResult::STATUS_SUCCESS:
                    $this->addConfirm(sprintf('%s : %s', $singleMigrationResult->getName(), $this->translator->trans('WEMSG.UPDATEMANAGER.SINGLEMIGRATIONRESULT.statusSuccess', [], 'contao_default')), $this->module);
                break;
            }
        }
    }

    protected function createClientFilesFolders(): void
    {
        $clientFilesFolder = new Folder(CoreConfig::DEFAULT_CLIENT_FILES_FOLDER);
        $clientFilesFolder->unprotect();

        $clientLogosFolder = new Folder(CoreConfig::DEFAULT_CLIENT_LOGOS_FOLDER);
        $clientLogosFolder->unprotect();
    }

    /**
     * @throws Exception
     */
    protected function createClientTemplatesFolder(): void
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        new Folder('templates'.\DIRECTORY_SEPARATOR.WEMStringUtil::generateAlias($config->getSgWebsiteTitle()));
    }

    protected function createTheme(): int
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        // Create the Smartgear main theme
        $objTheme = ThemeModel::findOneById($config->getSgTheme()) ?? new ThemeModel();
        $objTheme->tstamp = time();
        $objTheme->name = 'Smartgear '.$config->getSgWebsiteTitle();
        $objTheme->author = 'Web ex Machina';
        $objTheme->templates = sprintf('templates/%s', WEMStringUtil::generateAlias($config->getSgWebsiteTitle()));
        $objTheme->save();

        $this->setConfigKey('setSgTheme', (int) $objTheme->id);

        return (int) $objTheme->id;
    }

    protected function createImageSizes(int $themeId): array
    {
        $this->configurationManager->load();
        $registeredImageSizes = $this->getConfigImageSizesAsFormattedArray();
        $imageSizes = [];

        // $obj16_9 = \array_key_exists('16:9', $registeredImageSizes)
        //                     ? ImageSizeModel::findOneById($registeredImageSizes['16:9']) ?? new ImageSizeModel()
        //                     : new ImageSizeModel()
        //                     ;
        // $obj16_9->pid = $themeId;
        // $obj16_9->tstamp = time();
        // $obj16_9->name = '16:9';
        // $obj16_9->width = '1920';
        // $obj16_9->height = '1080';
        // $obj16_9->densities = '0.5x, 1x, 2x';
        // $obj16_9->resizeMode = 'crop';
        // $obj16_9->lazyLoading = 1;
        // $obj16_9->save();
        $obj16_9 = ImageSizeUtil::createImageSize_16_9(
            $themeId,
            \array_key_exists('16:9', $registeredImageSizes) ? ['id' => $registeredImageSizes['16:9']] : []
        );
        $imageSizes[$obj16_9->name] = $obj16_9;

        $this->setConfigImageSizeKey($obj16_9->name, (int) $obj16_9->id);
        // $obj2_1->pid = $themeId;
        // $obj2_1->tstamp = time();
        // $obj2_1->name = '2:1';
        // $obj2_1->width = '1920';
        // $obj2_1->height = '960';
        // $obj2_1->densities = '2x';
        // $obj2_1->resizeMode = 'crop';
        // $obj2_1->lazyLoading = 1;
        // $obj2_1->save();
        $obj2_1 = ImageSizeUtil::createImageSize_2_1(
            $themeId,
            \array_key_exists('2:1', $registeredImageSizes) ? ['id' => $registeredImageSizes['2:1']] : []
        );
        $imageSizes[$obj2_1->name] = $obj2_1;

        $this->setConfigImageSizeKey($obj2_1->name, (int) $obj2_1->id);

        // $obj1_2 = \array_key_exists('1:2', $registeredImageSizes)
        //                     ? ImageSizeModel::findOneById($registeredImageSizes['1:2']) ?? new ImageSizeModel()
        //                     : new ImageSizeModel()
        //                     ;
        // $obj1_2->pid = $themeId;
        // $obj1_2->tstamp = time();
        // $obj1_2->name = '1:2';
        // $obj1_2->width = '960';
        // $obj1_2->height = '1920';
        // $obj1_2->densities = '0.5x';
        // $obj1_2->resizeMode = 'crop';
        // $obj1_2->lazyLoading = 1;
        // $obj1_2->save();
        $obj1_2 = ImageSizeUtil::createImageSize_1_2(
            $themeId,
            \array_key_exists('1:2', $registeredImageSizes) ? ['id' => $registeredImageSizes['1:2']] : []
        );
        $imageSizes[$obj1_2->name] = $obj1_2;

        $this->setConfigImageSizeKey($obj1_2->name, (int) $obj1_2->id);

        // $obj1_1 = \array_key_exists('1:1', $registeredImageSizes)
        //                     ? ImageSizeModel::findOneById($registeredImageSizes['1:1']) ?? new ImageSizeModel()
        //                     : new ImageSizeModel()
        //                     ;
        // $obj1_1->pid = $themeId;
        // $obj1_1->tstamp = time();
        // $obj1_1->name = '1:1';
        // $obj1_1->width = '1920';
        // $obj1_1->height = '1920';
        // $obj1_1->densities = '1x';
        // $obj1_1->resizeMode = 'crop';
        // $obj1_1->lazyLoading = 1;
        // $obj1_1->save();
        $obj1_1 = ImageSizeUtil::createImageSize_1_1(
            $themeId,
            \array_key_exists('1:1', $registeredImageSizes) ? ['id' => $registeredImageSizes['1:1']] : []
        );
        $imageSizes[$obj1_1->name] = $obj1_1;

        $this->setConfigImageSizeKey($obj1_1->name, (int) $obj1_1->id);

        // $obj4_3 = \array_key_exists('4:3', $registeredImageSizes)
        //                     ? ImageSizeModel::findOneById($registeredImageSizes['4:3']) ?? new ImageSizeModel()
        //                     : new ImageSizeModel()
        //                     ;
        // $obj4_3->pid = $themeId;
        // $obj4_3->tstamp = time();
        // $obj4_3->name = '4:3';
        // $obj4_3->width = '1920';
        // $obj4_3->height = '1440';
        // $obj4_3->densities = '0.5x, 1x, 2x';
        // $obj4_3->resizeMode = 'crop';
        // $obj4_3->lazyLoading = 1;
        // $obj4_3->save();
        $obj4_3 = ImageSizeUtil::createImageSize_4_3(
            $themeId,
            \array_key_exists('4:3', $registeredImageSizes) ? ['id' => $registeredImageSizes['4:3']] : []
        );
        $imageSizes[$obj4_3->name] = $obj4_3;

        $this->setConfigImageSizeKey($obj4_3->name, (int) $obj4_3->id);

        return $imageSizes;
    }

    protected function createModules(int $themeId): array
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $registeredModules = $this->getConfigModulesAsFormattedArray();
        $modules = [];
        // Navigation
        $objNavMain = ModuleUtil::createModuleNav($themeId, \array_key_exists('navigation', $registeredModules) ? ['id' => $registeredModules['navigation']] : []);
        $modules[$objNavMain->type] = $objNavMain;

        $this->setConfigModuleKey($objNavMain->type, (int) $objNavMain->id);

        // Header
        $objHeaderModule = ModuleUtil::createModuleWemSgHeader($themeId, (int) $objNavMain->id, array_merge([
            // $objHeaderModule = ModuleUtil::createModule((int) $themeId, array_merge([
            // 'pid' => $themeId,
            // 'tstamp' => time(),
            // 'type' => 'wem_sg_header',
            // 'name' => 'HEADER',
            // 'imgSize' => 'a:3:{i:0;s:0:"";i:1;s:3:"100";i:2;s:12:"proportional";}',
            // 'wem_sg_header_sticky' => 1,
            // 'wem_sg_header_nav_module' => $objNavMain->id,
            'wem_sg_header_alt' => 'Logo '.$config->getSgWebsiteTitle(),
            // 'wem_sg_header_search_parameter' => 'keywords',
            // 'wem_sg_header_nav_position' => 'right',
            // 'wem_sg_header_panel_position' => 'right',
        ],
        (!empty($config->getSgOwnerLogo()) && $objFileModel = FilesModel::findByPath($config->getSgOwnerLogo())) ? ['singleSRC' => $objFileModel->uuid] : [],
        \array_key_exists('wem_sg_header', $registeredModules) ? ['id' => $registeredModules['wem_sg_header']] : []
        ));
        $modules[$objHeaderModule->type] = $objHeaderModule;

        $this->setConfigModuleKey($objHeaderModule->type, (int) $objHeaderModule->id);

        // Breadcrumb
        $objBreadcrumbModule = ModuleUtil::createModuleBreadcrumb($themeId, array_merge([
            // $objBreadcrumbModule = ModuleUtil::createModule((int) $themeId, array_merge([
            //     'pid' => $themeId,
            //     'tstamp' => time(),
            //     'type' => 'breadcrumb',
            //     'name' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['ModuleBreadcrumbName'],
            //     'wem_sg_breadcrumb_auto_placement' => 1,
            //     'wem_sg_breadcrumb_auto_placement_after_content_elements' => serialize(['rsce_hero', 'rsce_heroStart']),
            //     'wem_sg_breadcrumb_auto_placement_after_modules' => serialize(['rsce_hero', 'rsce_heroStart']),
        ],
        \array_key_exists('breadcrumb', $registeredModules) ? ['id' => $registeredModules['breadcrumb']] : []
        ));

        $modules[$objBreadcrumbModule->type] = $objBreadcrumbModule;

        $this->setConfigModuleKey($objBreadcrumbModule->type, (int) $objBreadcrumbModule->id);

        // Footer
        $objFooterModule = ModuleUtil::createModule($themeId, array_merge([
            'pid' => $themeId,
            'tstamp' => time(),
            'type' => 'html',
            'name' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['ModuleFooterName'],
            'html' => file_get_contents(Util::getPublicOrWebDirectory().'/bundles/wemsmartgear/examples/footer_1.html'),
        ],
        \array_key_exists('wem_sg_footer', $registeredModules) ? ['id' => $registeredModules['wem_sg_footer']] : []
        ));

        $modules['wem_sg_footer'] = $objFooterModule;

        $this->setConfigModuleKey('wem_sg_footer', (int) $objFooterModule->id);

        // Sitemap
        $objSitemapModule = ModuleUtil::createModuleSitemap($themeId, array_merge([
            // $objSitemapModule = ModuleUtil::createModule((int) $themeId, array_merge([
            //     'pid' => $themeId,
            //     'tstamp' => time(),
            //     'type' => 'sitemap',
            //     'name' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['ModuleSitemapName'],
        ],
        \array_key_exists('sitemap', $registeredModules) ? ['id' => $registeredModules['sitemap']] : []
        ));

        $modules[$objSitemapModule->type] = $objSitemapModule;

        $this->setConfigModuleKey($objSitemapModule->type, (int) $objSitemapModule->id);

        // Social link
        $objSocialLinkModule = ModuleUtil::createModuleWemSgSocialLink($themeId, array_merge([
            // $objSocialLinkModule = ModuleUtil::createModule((int) $themeId, array_merge([
            //     'pid' => $themeId,
            //     'tstamp' => time(),
            //     'type' => 'wem_sg_social_link',
            //     'name' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['ModuleSocialLinkName'],
        ],
        \array_key_exists('wem_sg_social_link', $registeredModules) ? ['id' => $registeredModules['wem_sg_social_link']] : []
        ));

        $modules[$objSocialLinkModule->type] = $objSocialLinkModule;

        $this->setConfigModuleKey($objSocialLinkModule->type, (int) $objSocialLinkModule->id);

        // Social Link Categories
        $objSocialLinkCategoriesModule = ModuleUtil::createModuleWemSgSocialLinkConfigCategories($themeId, array_merge([
            // $objSocialLinkCategoriesModule = ModuleUtil::createModule((int) $themeId, array_merge([
            //     'pid' => $themeId,
            //     'tstamp' => time(),
            //     'type' => 'wem_sg_social_link_config_categories',
            //     'name' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['ModuleSocialLinkConfigCategoriesName'],
        ],
        \array_key_exists('wem_sg_social_link_config_categories', $registeredModules) ? ['id' => $registeredModules['wem_sg_social_link_config_categories']] : []
        ));

        $modules[$objSocialLinkCategoriesModule->type] = $objSocialLinkCategoriesModule;

        $this->setConfigModuleKey($objSocialLinkCategoriesModule->type, (int) $objSocialLinkCategoriesModule->id);

        // Personal Data Manager
        $objPDMModule = ModuleUtil::createModuleWemPersonalDataManager($themeId, array_merge([
            // $objPDMModule = ModuleUtil::createModule((int) $themeId, array_merge([
            //     'pid' => $themeId,
            //     'tstamp' => time(),
            //     'type' => 'wem_personaldatamanager',
            //     'name' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['modulePersonalDataManagerName'],
        ],
        \array_key_exists('wem_personaldatamanager', $registeredModules) ? ['id' => $registeredModules['wem_personaldatamanager']] : []
        ));

        $modules[$objPDMModule->type] = $objPDMModule;

        $this->setConfigModuleKey($objPDMModule->type, (int) $objPDMModule->id);

        return $modules;
    }

    protected function updateLayouts(array $layouts, array $pages): void
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $objLayout = LayoutModel::findOneById($config->getSgLayoutStandard());
        $objLayout->head = str_replace('{{config.core.page.privacy.url}}', $pages['privacy_politics']->getAbsoluteUrl(), $objLayout->head);
        $objLayout->save();

        $objLayout = LayoutModel::findOneById($config->getSgLayoutFullwidth());

        $objLayout->head = str_replace('{{config.core.page.privacy.url}}', $pages['privacy_politics']->getAbsoluteUrl(), $objLayout->head);
        $objLayout->save();
    }

    protected function createLayouts(int $themeId, array $modules): array
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $layouts = [];

        $arrLayoutModulesDefault = LayoutUtil::buildDefaultModulesConfiguration((int) $modules['wem_sg_header']->id, (int) $modules['breadcrumb']->id, (int) $modules['wem_sg_footer']->id);
        // $arrLayoutModulesDefault = [
        //     ['mod' => $modules['wem_sg_header']->id, 'col' => 'header', 'enable' => '1'],
        //     ['mod' => $modules['breadcrumb']->id, 'col' => 'main', 'enable' => '1'],
        //     ['mod' => 0, 'col' => 'main', 'enable' => '1'],
        //     ['mod' => $modules['wem_sg_footer']->id, 'col' => 'footer', 'enable' => '1'],
        // ];
        // $script = file_get_contents(Util::getPublicOrWebDirectory().'/bundles/wemsmartgear/examples/code_javascript_personnalise_1.js');
        // if (\count($config->getSgGoogleFonts()) > 0) {
        //     $script = str_replace('{{config.googleFonts}}', "'".implode("','", $config->getSgGoogleFonts())."'", $script);
        // } else {
        //     $script = preg_replace('/\/\/ -- GFONT(.*)\/\/ -- \/GFONT/s', '', $script);
        // }

        // $script = str_replace('{{config.framway.path}}', $config->getSgFramwayPath(), $script);
        // switch ($config->getSgAnalytics()) {
        //     case CoreConfig::ANALYTICS_SYSTEM_NONE:
        //         $script = preg_replace('/\/\/ -- GTAG(.*)\/\/ -- \/GTAG/s', '', $script);
        //         $script = preg_replace('/\/\/ -- MATOMO(.*)\/\/ -- \/MATOMO/s', '', $script);
        //     break;
        //     case CoreConfig::ANALYTICS_SYSTEM_GOOGLE:
        //         $script = str_replace('{{config.analytics.google.id}}', $config->getSgAnalyticsGoogleId(), $script);
        //         $script = preg_replace('/\/\/ -- MATOMO(.*)\/\/ -- \/MATOMO/s', '', $script);
        //     break;
        //     case CoreConfig::ANALYTICS_SYSTEM_MATOMO:
        //         $script = str_replace('{{config.analytics.matomo.host}}', $config->getSgAnalyticsMatomoHost(), $script);
        //         $script = str_replace('{{config.analytics.matomo.id}}', $config->getSgAnalyticsMatomoId(), $script);
        //         $script = preg_replace('/\/\/ -- GTAG(.*)\/\/ -- \/GTAG/s', '', $script);
        //     break;
        // }

        // $head = file_get_contents(Util::getPublicOrWebDirectory().'/bundles/wemsmartgear/examples/balises_supplementaires_1.js');
        // $head = str_replace('{{config.framway.path}}', $config->getSgFramwayPath(), $head);

        $objLayout = null !== $config->getSgLayoutStandard()
            ? LayoutModel::findOneById($config->getSgLayoutStandard()) ?? new LayoutModel()
            : new LayoutModel();
        $arrLayoutModules = LayoutUtil::reorderLayoutModules(LayoutUtil::mergeLayoutsModules(StringUtil::deserialize($objLayout->modules ?? []), $arrLayoutModulesDefault), $modules);
        // $objLayout->pid = $themeId;
        // $objLayout->name = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['LayoutStandardName'];
        // $objLayout->rows = '3rw';
        // $objLayout->cols = '1cl';
        // $objLayout->loadingOrder = 'external_first';
        // $objLayout->combineScripts = 1;
        // $objLayout->viewport = 'width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=0';
        // $objLayout->modules = serialize($arrLayoutModules);
        // $objLayout->template = 'fe_page';
        // $objLayout->webfonts = $config->getSgGoogleFonts();
        // $objLayout->head = $head;
        // $objLayout->script = $script;
        // $objLayout->framework = serialize([]);
        // $objLayout->tstamp = time();
        // $objLayout->save();

        $objLayout = LayoutUtil::createLayoutStandard(
            $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['LayoutStandardName'],
            $themeId,
            array_merge(
            [
                'webfonts' => implode("','", $config->getSgGoogleFonts()),
                'modules' => serialize($arrLayoutModules),
                // 'modules_raw' => $modules,
                'replace' => [
                    'head' => [
                        '{{config.framway.path}}' => $config->getSgFramwayPath(),
                    ],
                    'script' => [
                        '{{config.googleFonts}}' => $config->getSgGoogleFonts(),
                        '{{config.framway.path}}' => $config->getSgFramwayPath(),
                        '{{config.analytics.system}}' => $config->getSgAnalytics(),
                        '{{config.analytics.google.id}}' => $config->getSgAnalyticsGoogleId(),
                        '{{config.analytics.matomo.host}}' => $config->getSgAnalyticsMatomoHost(),
                        '{{config.analytics.matomo.id}}' => $config->getSgAnalyticsMatomoId(),
                    ],
                ],
            ],
            $config->getSgLayoutStandard() ? ['id' => $config->getSgLayoutStandard()] : []
            )
        );

        $layouts['standard'] = $objLayout;

        $this->setConfigKey('setSgLayoutStandard', (int) $objLayout->id);

        $objLayout = null !== $config->getSgLayoutFullwidth()
            ? LayoutModel::findOneById($config->getSgLayoutFullwidth()) ?? new LayoutModel()
            : new LayoutModel();
        $arrLayoutModules = LayoutUtil::reorderLayoutModules(LayoutUtil::mergeLayoutsModules(StringUtil::deserialize($objLayout->modules ?? []), $arrLayoutModulesDefault), $modules);
        // $objLayout->pid = $themeId;
        // $objLayout->name = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['LayoutStandardFullwidthName'];
        // $objLayout->rows = '3rw';
        // $objLayout->cols = '1cl';
        // $objLayout->loadingOrder = 'external_first';
        // $objLayout->combineScripts = 1;
        // $objLayout->viewport = 'width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=0';
        // $objLayout->modules = serialize($arrLayoutModulesDefault);
        // $objLayout->template = 'fe_page_full';
        // $objLayout->webfonts = $config->getSgGoogleFonts();
        // $objLayout->head = $head;
        // $objLayout->script = $script;
        // $objLayout->framework = serialize([]);
        // $objLayout->tstamp = time();
        // $objLayout->save();

        $objLayout = LayoutUtil::createLayoutFullpage(
            $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['LayoutStandardFullwidthName'],
            $themeId,
            array_merge([
                'webfonts' => implode("','", $config->getSgGoogleFonts()),
                'modules' => serialize($arrLayoutModules),
                // 'modules_raw' => $modules,
                'replace' => [
                    'head' => [
                        '{{config.framway.path}}' => $config->getSgFramwayPath(),
                    ],
                    'script' => [
                        '{{config.googleFonts}}' => $config->getSgGoogleFonts(),
                        '{{config.framway.path}}' => $config->getSgFramwayPath(),
                        '{{config.analytics.system}}' => $config->getSgAnalytics(),
                        '{{config.analytics.google.id}}' => $config->getSgAnalyticsGoogleId(),
                        '{{config.analytics.matomo.host}}' => $config->getSgAnalyticsMatomoHost(),
                        '{{config.analytics.matomo.id}}' => $config->getSgAnalyticsMatomoId(),
                    ],
                ],
            ],
            $config->getSgLayoutStandard() ? ['id' => $config->getSgLayoutStandard()] : []
            )
        );

        $layouts['fullwidth'] = $objLayout;

        $this->setConfigKey('setSgLayoutFullwidth', (int) $objLayout->id);

        return $layouts;
    }

    protected function createUserGroups(): array
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $objFolderClientFiles = FilesModel::findByPath(CoreConfig::DEFAULT_CLIENT_FILES_FOLDER);
        $objFolderClientLogos = FilesModel::findByPath(CoreConfig::DEFAULT_CLIENT_LOGOS_FOLDER);

        $userGroups = [];
        if (null !== $config->getSgUserGroupAdministrators()) {
            $objUserGroup = UserGroupModel::findOneById($config->getSgUserGroupAdministrators()) ?? new UserGroupModel();
        } else {
            $objUserGroup = new UserGroupModel();
        }

        $objUserGroup->tstamp = time();
        $objUserGroup->name = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['UsergroupAdministratorsName'];
        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);
        $userGroupManipulator
            ->addAllowedModules(['page', 'article', 'form', 'files', 'nc_notifications', 'user', 'log', 'maintenance', 'wem_sg_social_link', 'wem_sg_social_link_config_categories', 'wem_sg_dashboard'])
            ->addAllowedFields(UserGroupUtil::getCorePermissions())
            ->addAllowedImageSizes(['proportional'])
            ->addAllowedFilemounts([$objFolderClientFiles->uuid, $objFolderClientLogos->uuid])
            ->addAllowedFileOperationPermissions(['f1', 'f2', 'f3', 'f4'])
            ->addAllowedElements([
                'headline',
                'text',
                'html',
                'table',
                'rsce_listIcons',
                'rsce_quote',
                'accordionStart',
                'accordionStop',
                'hyperlink',
                'image',
                'player',
                'youtube',
                'vimeo',
                'downloads',
                'module',
                // 'rsce_timeline',
                'grid-start',
                'grid-stop',
                'rsce_accordion',
                'rsce_counter',
                'rsce_hero',
                'rsce_heroStart',
                'rsce_heroStop',
                'rsce_ratings',
                'rsce_priceCards',
                'rsce_slider',
                'rsce_tabs',
                // 'rsce_testimonials',
                'rsce_pdfViewer',
                'rsce_blockCard',
                'form',
                'gallery',
            ])
            ->removeAllowedElements(['rsce_timeline', 'rsce_testimonials'])
            ->addAllowedFormFields(array_keys($GLOBALS['TL_FFL']))
            ->addAllowedFieldsByTables(['tl_form', 'tl_form_field'])
            ->addAllowedFormPermissions(['create', 'delete'])
        ;
        $objUserGroup->modules = serialize(['page', 'article', 'form', 'files', 'nc_notifications', 'user', 'log', 'maintenance', 'wem_sg_social_link', 'wem_sg_social_link_config_categories', 'wem_sg_dashboard']);
        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->save();
        $userGroups['administrators'] = $objUserGroup;

        $this->setConfigKey('setSgUserGroupAdministrators', (int) $objUserGroup->id);

        if (null !== $config->getSgUserGroupAdministrators()) {
            $objUserGroup = UserGroupModel::findOneById($config->getSgUserGroupRedactors()) ?? new UserGroupModel();
            $this->userGroupWebmasterOldPermissions = null !== $objUserGroup->smartgear_permissions
            ? unserialize($objUserGroup->smartgear_permissions)
            : [];
        } else {
            $objUserGroup = new UserGroupModel();
        }

        $objUserGroup->tstamp = time();
        $objUserGroup->name = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['UsergroupRedactorsName'];
        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);
        $userGroupManipulator
            ->addAllowedModules(['article', 'files', 'form', 'wem_sg_social_link', 'wem_sg_dashboard'])
            ->addAllowedFields(UserGroupUtil::getCorePermissions())
            ->addAllowedImageSizes(['proportional'])
            ->addAllowedFilemounts([$objFolderClientFiles->uuid, $objFolderClientLogos->uuid])
            ->addAllowedFileOperationPermissions(['f1', 'f2', 'f3', 'f4'])
            ->addAllowedElements([
                'headline',
                'text',
                'html',
                'table',
                'rsce_listIcons',
                'rsce_quote',
                'accordionStart',
                'accordionStop',
                'hyperlink',
                'image',
                'player',
                'youtube',
                'vimeo',
                'downloads',
                'module',
                // 'rsce_timeline',
                'grid-start',
                'grid-stop',
                'rsce_accordion',
                'rsce_counter',
                'rsce_hero',
                'rsce_heroStart',
                'rsce_heroStop',
                'rsce_ratings',
                'rsce_priceCards',
                'rsce_slider',
                'rsce_tabs',
                // 'rsce_testimonials',
                'rsce_pdfViewer',
                'rsce_blockCard',
                'form',
                'gallery',
            ])
            ->removeAllowedElements(['rsce_timeline', 'rsce_testimonials'])
            ->addAllowedFormFields(array_keys($GLOBALS['TL_FFL']))
            ->addAllowedFieldsByTables(['tl_form', 'tl_form_field'])
            ->addAllowedFormPermissions(['create', 'delete'])
        ;
        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->save();
        $userGroups['redactors'] = $objUserGroup;

        $this->setConfigKey('setSgUserGroupRedactors', (int) $objUserGroup->id);

        return $userGroups;
    }

    protected function createUsers(array $groups): array
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $objUser = null !== $config->getSgUserWebmaster()
                    ? UserModel::findOneById($config->getSgUserWebmaster()) ?? new UserModel()
                    : new UserModel();
        $objUser->tstamp = time();
        $objUser->dateAdded = time();
        $objUser->username = CoreConfig::DEFAULT_USER_USERNAME;
        $objUser->name = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['UserWebmasterName'];
        $objUser->email = $config->getSgOwnerEmail();
        $objUser->language = 'fr';
        $objUser->backendTheme = 'flexible';
        $objUser->fullscreen = 1;
        $objUser->showHelp = 1;
        $objUser->thumbnails = 1;
        $objUser->useRTE = 1;
        $objUser->useCE = 1;
        $objUser->uploader = 'DropZone';
        // $objUser->password = \Contao\Encryption::hash('webmaster');
        $objUser->password = password_hash('webmaster', \PASSWORD_DEFAULT);
        $objUser->pwChange = 1;
        $objUser->groups = serialize([0 => $groups['redactors']->id]);
        $objUser->inherit = 'group';
        $objUser->save();

        $this->setConfigKey('setSgUserWebmaster', (int) $objUser->id);

        return ['webmaster' => $objUser];
    }

    protected function createPageRoot(array $layouts, array $groups, array $users): PageModel
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        if (null !== $config->getSgPageRoot()) {
            $page = PageModel::findById($config->getSgPageRoot());
        } else {
            $page = PageModel::findOneBy('title', $config->getSgwebsiteTitle());
        }

        $page = PageUtil::createPageRoot($config->getSgwebsiteTitle(), $config->getSgOwnerEmail(), $layouts['standard']->id, 'fr', array_merge([
            // $page = PageUtil::createPage($config->getSgwebsiteTitle(), 0, array_merge([
            // 'sorting' => 128,
            // 'type' => 'root',
            // 'language' => 'fr',
            // 'fallback' => 1,
            // 'adminEmail' => $config->getSgOwnerEmail(),
            // 'createSitemap' => 1,
            // 'sitemapName' => 'sitemap',
            // 'useSSL' => 1,
            // 'includeLayout' => 1,
            // 'layout' => $layouts['standard']->id,
            // 'includeChmod' => 1,
            'cuser' => $users['webmaster']->id,
            'cgroup' => $groups['administrators']->id,
            // 'chmod' => CoreConfig::DEFAULT_ROOTPAGE_CHMOD,
            // 'robotsTxt' => SG_ROBOTSTXT_CONTENT_FULL,
        ], null !== $page ? ['id' => $page->id] : []));

        $this->setConfigKey('setSgPageRoot', (int) $page->id);

        return $page;
    }

    protected function createPageHome(PageModel $rootPage): PageModel
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $page = PageModel::findOneById($config->getSgPageHome());
        $page = PageUtil::createPageHome($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PageHomeTitle'], $rootPage->id, array_merge([
            // $page = PageUtil::createPage($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PageHomeTitle'], $rootPage->id, array_merge([
            //     'sorting' => PageUtil::getNextAvailablePageSortingByParentPage((int) $rootPage->id),
            //     'alias' => 'index',
            //     'sitemap' => 'map_default',
            //     'hide' => 1,
        ], null !== $page ? ['id' => $page->id, 'sorting' => $page->sorting] : []));

        $this->setConfigKey('setSgPageHome', (int) $page->id);

        return $page;
    }

    protected function createPage404(array $modules, PageModel $rootPage): PageModel
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $page = PageModel::findOneById($config->getSgPage404());
        $page = PageUtil::createPage404($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['Page404Title'], $rootPage->id, array_merge([
            // $page = PageUtil::createPage($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['Page404Title'], $rootPage->id, array_merge([
            //     'sorting' => PageUtil::getNextAvailablePageSortingByParentPage((int) $rootPage->id),
            //     'sitemap' => 'map_default',
            //     'hide' => 1,
            //     'type' => 'error_404',
        ], null !== $page ? ['id' => $page->id, 'sorting' => $page->sorting] : []));

        $this->setConfigKey('setSgPage404', (int) $page->id);

        return $page;
    }

    protected function createPageLegalNotice(PageModel $rootPage): PageModel
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $page = PageModel::findOneById($config->getSgPageLegalNotice());
        $page = PageUtil::createPage($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PageLegalNoticeTitle'], $rootPage->id, array_merge([
            'sorting' => PageUtil::getNextAvailablePageSortingByParentPage((int) $rootPage->id),
            'sitemap' => 'map_default',
            'description' => sprintf($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PageLegalNoticeDescription'], $config->getSgWebsiteTitle()),
            'hide' => 1,
        ], null !== $page ? ['id' => $page->id, 'sorting' => $page->sorting] : []));

        $this->setConfigKey('setSgPageLegalNotice', (int) $page->id);

        return $page;
    }

    protected function createPagePrivacyPolitics(PageModel $rootPage): PageModel
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $page = PageModel::findOneById($config->getSgPagePrivacyPolitics());
        $page = PageUtil::createPage($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PagePrivacyPoliticsTitle'], $rootPage->id, array_merge([
            'sorting' => PageUtil::getNextAvailablePageSortingByParentPage((int) $rootPage->id),
            'sitemap' => 'map_default',
            'description' => sprintf($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PagePrivacyPoliticsDescription'], $config->getSgWebsiteTitle()),
            'hide' => 1,
        ], null !== $page ? ['id' => $page->id, 'sorting' => $page->sorting] : []));

        $this->setConfigKey('setSgPagePrivacyPolitics', (int) $page->id);

        return $page;
    }

    protected function createPageSitemap(array $modules, PageModel $rootPage): PageModel
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $page = PageModel::findOneById($config->getSgPageSitemap());
        $page = PageUtil::createPageSitemap($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PageSitemapTitle'], $rootPage->id, array_merge([
            // $page = PageUtil::createPage($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PageSitemapTitle'], $rootPage->id, array_merge([
            // 'sorting' => PageUtil::getNextAvailablePageSortingByParentPage((int) $rootPage->id),
            // 'sitemap' => 'map_default',
            'description' => sprintf($GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PageSitemapDescription'], $config->getSgWebsiteTitle()),
            // 'hide' => 1,
        ], null !== $page ? ['id' => $page->id, 'sorting' => $page->sorting] : []));

        $this->setConfigKey('setSgPageSitemap', (int) $page->id);

        return $page;
    }

    protected function createPages(array $layouts, array $groups, array $users, array $modules): array
    {
        $pages = [];
        $pages['root'] = $this->createPageRoot($layouts, $groups, $users);
        $pages['home'] = $this->createPageHome($pages['root']);
        $pages['404'] = $this->createPage404($modules, $pages['root']);
        $pages['legal_notice'] = $this->createPageLegalNotice($pages['root']);
        $pages['privacy_politics'] = $this->createPagePrivacyPolitics($pages['root']);
        $pages['sitemap'] = $this->createPageSitemap($modules, $pages['root']);

        return $pages;
    }

    protected function createArticleHome(PageModel $page): ArticleModel
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $article = ArticleModel::findOneById($config->getSgArticleHome()) ?? ArticleUtil::createArticle($page);

        $this->setConfigKey('setSgArticleHome', (int) $article->id);

        return $article;
    }

    protected function createArticle404(PageModel $page): ArticleModel
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $article = ArticleModel::findOneById($config->getSgArticle404()) ?? ArticleUtil::createArticle($page);

        $this->setConfigKey('setSgArticle404', (int) $article->id);

        return $article;
    }

    protected function createArticleLegalNotice(PageModel $page): ArticleModel
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $article = ArticleModel::findOneById($config->getSgArticleLegalNotice()) ?? ArticleUtil::createArticle($page);

        $this->setConfigKey('setSgArticleLegalNotice', (int) $article->id);

        return $article;
    }

    protected function createArticlePrivacyPolitics(PageModel $page): ArticleModel
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $article = ArticleModel::findOneById($config->getSgArticlePrivacyPolitics()) ?? ArticleUtil::createArticle($page);

        $this->setConfigKey('setSgArticlePrivacyPolitics', (int) $article->id);

        return $article;
    }

    protected function createArticleSitemap(PageModel $page): ArticleModel
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $article = ArticleModel::findOneById($config->getSgArticleSitemap()) ?? ArticleUtil::createArticle($page);

        $this->setConfigKey('setSgArticleSitemap', (int) $article->id);

        return $article;
    }

    protected function createArticles(array $pages): array
    {
        return [
            'home' => $this->createArticleHome($pages['home']),
            '404' => $this->createArticle404($pages['404']),
            'legal_notice' => $this->createArticleLegalNotice($pages['legal_notice']),
            'privacy_politics' => $this->createArticlePrivacyPolitics($pages['privacy_politics']),
            'sitemap' => $this->createArticleSitemap($pages['sitemap'])
        ];
    }

    protected function createContent404(ArticleModel $article, array $modules): array
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $content = ContentModel::findById($config->getSgContent404Headline());
        $contents['headline'] = ContentUtil::createContent($article, ['headline' => serialize(['unit' => 'h1', 'value' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['Page404Headline']]), 'text' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['Page404Text'], 'id' => null !== $content ? $content->id : null]);

        $this->setConfigKey('setSgContent404Headline', (int) $content->id);

        $content = ContentModel::findById($config->getSgContent404Sitemap());
        $contents['sitemap'] = ContentUtil::createContent($article, ['type' => 'module', 'module' => $modules['sitemap']->id, 'id' => null !== $content ? $content->id : null]);

        $this->setConfigKey('setSgContent404Sitemap', (int) $content->id);

        return $contents;
    }

    protected function createContentLegalNotice(ArticleModel $article, array $modules): ContentModel
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $content = ContentModel::findById($config->getSgContentLegalNotice());

        $strText = file_get_contents(Util::getPublicOrWebDirectory().'/bundles/wemsmartgear/examples/fr/legal-notices_1.html');
        $strHtml = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PageLegalNoticeTextDefault'];
        if ($strText) {
            /**
             * 1: URL du site entière
             * 2: URL du site sans https://
             * 3: Nom de l'entreprise
             * 4: Statut de l'entreprise
             * 5: Siret de l'entreprise
             * 6: Adresse du siège de l'entreprise
             * 7: Adresse mail de l'entreprise
             * 8: Nom & Adresse de l'hébergeur.
             */
            $strHtml = sprintf(
                    $strText,
                    $config->getSgOwnerDomain() ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled'],
                    str_replace('https://', '', $config->getSgOwnerDomain()) ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled'],
                    $config->getSgOwnerName() ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled'],
                    $config->getSgOwnerStatus() ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled'],
                    $config->getSgOwnerSIRET() ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled'],
                    $config->getSgOwnerStreet().' '.$config->getSgOwnerPostal().' '.$config->getSgOwnerCity().' '.$config->getSgOwnerRegion().' '.$config->getSgOwnerCountry() ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled'],
                    $config->getSgOwnerEmail() ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled'],
                    $config->getSgOwnerHost() ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled']
                );
        }

        $objContent = ContentUtil::createContent($article, ['headline' => serialize(['unit' => 'h1', 'value' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PageLegalNoticeHeadline']]), 'text' => $strHtml, 'id' => null !== $content ? $content->id : null]);

        $this->setConfigKey('setSgContentLegalNotice', (int) $objContent->id);

        return $objContent;
    }

    protected function createContentPrivacyPolitics(PageModel $page, ArticleModel $article, array $modules): ContentModel
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $content = ContentModel::findById($config->getSgContentPrivacyPolitics());

        $strText = file_get_contents(Util::getPublicOrWebDirectory().'/bundles/wemsmartgear/examples/privacy-politics/fr/privacy_1.html');
        $strHtml = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PagePrivacyPoliticsTextDefault'];
        if ($strText) {
            /**
             * 1: Nom de la boite
             * 2: Adresse
             * 3: SIRET
             * 4: URL de la page confidentialité
             * 5: Date
             * 6: Contact email.
             */
            $strHtml = sprintf(
                $strText,
                $config->getSgOwnerName() ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled'],
                $config->getSgOwnerStreet().' '.$config->getSgOwnerPostal().' '.$config->getSgOwnerCity().' '.$config->getSgOwnerRegion().' '.$config->getSgOwnerCountry() ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled'],
                $config->getSgOwnerSIRET() ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled'],
                $page->getAbsoluteUrl(),
                date('d/m/Y'),
                $config->getSgOwnerEmail() ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled']
            );
        }

        $objContent = ContentUtil::createContent($article, ['text' => $strHtml, 'id' => null !== $content ? $content->id : null]);

        $this->setConfigKey('setSgContentPrivacyPolitics', (int) $objContent->id);

        return $objContent;
    }

    protected function createContentSitemap(ArticleModel $article, array $modules): array
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $contents = [];

        $content = ContentModel::findById($config->getSgContentSitemapHeadline());
        $contents['headline'] = ContentUtil::createContent($article, ['headline' => serialize(['unit' => 'h1', 'value' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PageSitemapHeadline']]), 'id' => null !== $content ? $content->id : null]);

        $this->setConfigKey('setSgContentSitemapHeadline', (int) $contents['headline']->id);

        $content = ContentModel::findById($config->getSgContentSitemap());

        $contents['module'] = ContentUtil::createContent($article, ['type' => 'module', 'module' => $modules['sitemap']->id, 'id' => null !== $content ? $content->id : null]);

        $this->setConfigKey('setSgContentSitemap', (int) $contents['module']->id);

        return $contents;
    }

    protected function createContents(array $pages, array $articles, array $modules): array
    {
        return [
            '404' => $this->createContent404($articles['404'], $modules),
            'legal_notice' => $this->createContentLegalNotice($articles['legal_notice'], $modules),
            'privacy_politics' => $this->createContentPrivacyPolitics($pages['privacy_politics'], $articles['privacy_politics'], $modules),
            'sitemap' => $this->createContentSitemap($articles['sitemap'], $modules)
        ];
    }

    protected function createModules2(int $themeId, array $pages): array
    {
        $registeredModules = $this->getConfigModulesAsFormattedArray();
        $modules = [];

        // Custom Nav
        $objCustomNavModule = ModuleUtil::createModuleFooterNav($themeId,
            [$pages['legal_notice']->id, $pages['privacy_politics']->id, $pages['sitemap']->id],
            array_merge([
                // $objCustomNavModule = ModuleUtil::createModule((int) $themeId, array_merge([
                //     'pid' => $themeId,
                //     'tstamp' => time(),
                //     'type' => 'customnav',
                //     'name' => $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['ModuleFooterNavName'],
                //     'pages' => [$pages['legal_notice']->id, $pages['privacy_politics']->id, $pages['sitemap']->id],
                //     'navigationTpl' => 'nav_default',
            ],
        \array_key_exists('customnav', $registeredModules) ? ['id' => $registeredModules['customnav']] : []
        ));

        $modules[$objCustomNavModule->type] = $objCustomNavModule;

        $this->setConfigModuleKey($objCustomNavModule->type, (int) $objCustomNavModule->id);

        // Footer - add content
        $objFooterModule = \array_key_exists('wem_sg_footer', $registeredModules)
                            ? ModuleModel::findOneById($registeredModules['wem_sg_footer']) ?? new ModuleModel()
                            : new ModuleModel()
                            ;
        $html = file_get_contents(Util::getPublicOrWebDirectory().'/bundles/wemsmartgear/examples/footer_1.html');
        $html = str_replace('link::plan-du-site', 'link::'.$pages['sitemap']->id, $html);
        $html = str_replace('link::mentions-legales', 'link::'.$pages['legal_notice']->id, $html);
        $html = str_replace('link::confidentialite', 'link::'.$pages['privacy_politics']->id, $html);
        $objFooterModule->html = $html;

        $objFooterModule->save();
        $modules['wem_sg_footer'] = $objFooterModule;

        $this->setConfigModuleKey('wem_sg_footer', (int) $objFooterModule->id);

        return $modules;
    }

    protected function createNotificationGateways(): array
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $nc = [];
        $objGateway = null !== $config->getSgNotificationGatewayEmail()
            ? Gateway::findOneById($config->getSgNotificationGatewayEmail()) ?? new Gateway()
            : new Gateway();
        $objGateway->tstamp = time();
        $objGateway->title = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['NotificationGatewayEmailSmartgearTitle'];
        $objGateway->type = 'email';
        $objGateway->save();

        $this->setConfigKey('setSgNotificationGatewayEmail', (int) $objGateway->id);

        $nc['email'] = $objGateway;

        return $nc;
    }

    protected function createNotificationSupportGatewayNotification(): NotificationModel
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        // $nc = NotificationModel::findOneById($config->getSgNotificationSupport()) ?? new NotificationModel();
        // $nc->tstamp = time();
        // $nc->title = $this->translator->trans('WEMSG.INSTALL.WEBSITE.titleNotificationSupportGatewayNotification', [], 'contao_default');
        // $nc->type = 'ticket_creation';
        // $nc->save();
        $nc = $this->notificationUtil->createSupportFormNotification($config->getSgNotificationSupport() ? ['id' => $config->getSgNotificationSupport()] : []);

        $this->setConfigKey('setSgNotificationSupport', (int) $nc->id);

        return $nc;
    }

    protected function createNotificationSupportGatewayMessagesUser(NotificationModel $gateway): NotificationMessageModel
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        // $nm = NotificationMessageModel::findOneById($config->getSgNotificationSupportMessageUser()) ?? new NotificationMessageModel();
        // $nm->pid = $gateway->id;
        // $nm->gateway = $config->getSgNotificationGatewayEmail();
        // $nm->gateway_type = 'email';
        // $nm->tstamp = time();
        // $nm->title = $this->translator->trans('WEMSG.INSTALL.WEBSITE.titleNotificationSupportGatewayMessageUser', [], 'contao_default');
        // $nm->published = 1;
        // $nm->save();
        // TODO : Notification bad usage fonction
        $nm = $this->notificationMessageUtil->createSupportFormNotificationMessageUser((int) $config->getSgNotificationGatewayEmail(), 'email', (int) $gateway->id, $config->getSgNotificationSupportMessageUser() ? ['id' => $config->getSgNotificationSupportMessageUser()] : []);

        $this->setConfigKey('setSgNotificationSupportMessageUser', (int) $nm->id);

        return $nm;
    }

    protected function createNotificationSupportGatewayMessagesAdmin(NotificationModel $gateway): NotificationMessageModel
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        // $nm = NotificationMessageModel::findOneById($config->getSgNotificationSupportMessageAdmin()) ?? new NotificationMessageModel();
        // $nm->pid = $gateway->id;
        // $nm->gateway = $config->getSgNotificationGatewayEmail();
        // $nm->gateway_type = 'email';
        // $nm->tstamp = time();
        // $nm->title = $this->translator->trans('WEMSG.INSTALL.WEBSITE.titleNotificationSupportGatewayMessageAdmin', [], 'contao_default');
        // $nm->published = 1;
        // $nm->save();
        // TODO : Notification bad usage fonction
        $nm = $this->notificationMessageUtil->createSupportFormNotificationMessageAdmin((int) $config->getSgNotificationGatewayEmail(), 'email', (int) $gateway->id, $config->getSgNotificationSupportMessageAdmin() ? ['id' => $config->getSgNotificationSupportMessageAdmin()] : []);

        $this->setConfigKey('setSgNotificationSupportMessageAdmin', (int) $nm->id);

        return $nm;
    }

    protected function createNotificationSupportGatewayMessages(NotificationModel $gateway): array
    {
        return [
            'user' => $this->createNotificationSupportGatewayMessagesUser($gateway),
            'admin' => $this->createNotificationSupportGatewayMessagesAdmin($gateway),
        ];
    }

    protected function createNotificationSupportGatewayMessagesLanguagesUser(NotificationMessageModel $gatewayMessage): NotificationLanguageModel
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        // $strText = file_get_contents(sprintf('%s/bundles/wemsmartgear/examples/dashboard/%s/ticket_mail_user.html', Util::getPublicOrWebDirectory(), $this->language));

        // $nl = NotificationLanguageModel::findOneById($config->getSgNotificationSupportMessageUserLanguage()) ?? new NotificationLanguageModel();
        // $nl->pid = $gatewayMessage->id;
        // $nl->tstamp = time();
        // $nl->language = $this->language;
        // $nl->fallback = 1;
        // $nl->recipients = '##sg_owner_email##';
        // $nl->gateway_type = 'email';
        // $nl->email_sender_name = $config->getSgWebsiteTitle();
        // $nl->email_sender_address = '##sg_owner_email##';
        // $nl->email_subject = $this->translator->trans('WEMSG.INSTALL.WEBSITE.subjectNotificationSupportGatewayMessageLanguageUser', [$config->getSgWebsiteTitle()], 'contao_default');
        // $nl->email_mode = 'textAndHtml';
        // $nl->email_text = $this->htmlDecoder->htmlToPlainText($strText, false);
        // $nl->email_html = $strText;
        // $nl->attachment_tokens = '##ticket_file##';
        // $nl->save();

        // TODO : Notification bad usage fonction
        $nl = $this->notificationMessageLanguageUtil->createSupportFormNotificationMessageUserLanguage((int) $gatewayMessage->id, $this->language, true, $config->getSgNotificationSupportMessageUserLanguage() ? ['id' => $config->getSgNotificationSupportMessageUserLanguage()] : []);

        $this->setConfigKey('setSgNotificationSupportMessageUserLanguage', (int) $nl->id);

        return $nl;
    }

    protected function createNotificationSupportGatewayMessagesLanguagesAdmin(NotificationMessageModel $gatewayMessage): NotificationLanguageModel
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        // $strText = file_get_contents(sprintf('%s/bundles/wemsmartgear/examples/dashboard/%s/ticket_mail_admin.html', Util::getPublicOrWebDirectory(), $this->language));

        // $nl = NotificationLanguageModel::findOneById($config->getSgNotificationSupportMessageAdminLanguage()) ?? new NotificationLanguageModel();
        // $nl->pid = $gatewayMessage->id;
        // $nl->tstamp = time();
        // $nl->language = $this->language;
        // $nl->fallback = 1;
        // $nl->recipients = '##support_email##';
        // $nl->gateway_type = 'email';
        // $nl->email_sender_name = $config->getSgWebsiteTitle();
        // $nl->email_sender_address = '##sg_owner_email##';
        // $nl->email_subject = $this->translator->trans('WEMSG.INSTALL.WEBSITE.subjectNotificationSupportGatewayMessageLanguageUser', [$config->getSgWebsiteTitle()], 'contao_default');
        // $nl->email_mode = 'textAndHtml';
        // $nl->email_text = $this->htmlDecoder->htmlToPlainText($strText, false);
        // $nl->email_html = $strText;
        // $nl->email_replyTo = '##sg_owner_email##';
        // $nl->attachment_tokens = '##ticket_file##';
        // $nl->save();

        // TODO : Notification bad usage fonction
        $nl = $this->notificationMessageLanguageUtil->createSupportFormNotificationMessageAdminLanguage((int) $gatewayMessage->id, $this->language, true, $config->getSgNotificationSupportMessageAdminLanguage() ? ['id' => $config->getSgNotificationSupportMessageAdminLanguage()] : []);

        $this->setConfigKey('setSgNotificationSupportMessageAdminLanguage', (int) $nl->id);

        return $nl;
    }

    protected function createNotificationSupportGatewayMessagesLanguages(array $gatewayMessages): array
    {
        return [
            'user' => $this->createNotificationSupportGatewayMessagesLanguagesUser($gatewayMessages['user']),
            'admin' => $this->createNotificationSupportGatewayMessagesLanguagesAdmin($gatewayMessages['admin']),
        ];
    }

    /**
     * @throws Exception
     */
    protected function uploadLogo(): File
    {
        $fm = Files::getInstance();
        new Folder(CoreConfig::DEFAULT_CLIENT_LOGOS_FOLDER);
        if (!$fm->move_uploaded_file($_FILES['sgWebsiteLogo']['tmp_name'], CoreConfig::DEFAULT_CLIENT_LOGOS_FOLDER.\DIRECTORY_SEPARATOR.$_FILES['sgWebsiteLogo']['name'].'_tmp')) {
            throw new Exception(sprintf('Unable to upload logo to "%s".', CoreConfig::DEFAULT_CLIENT_LOGOS_FOLDER.\DIRECTORY_SEPARATOR.$_FILES['sgWebsiteLogo']['name'].'_tmp'));
        }

        $objFile = new File(CoreConfig::DEFAULT_CLIENT_LOGOS_FOLDER.\DIRECTORY_SEPARATOR.$_FILES['sgWebsiteLogo']['name'].'_tmp');
        $objFile->renameTo(CoreConfig::DEFAULT_CLIENT_LOGOS_FOLDER.\DIRECTORY_SEPARATOR.$_FILES['sgWebsiteLogo']['name']);

        return $objFile;
    }

    protected function updateModuleConfiguration(): void
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $fonts = [];
        if (!empty(Input::post('sgGoogleFonts'))) {
            $fonts = explode(',', Input::post('sgGoogleFonts'));
            foreach ($fonts as $key => $value) {
                $fonts[$key] = trim($value);
            }
        }

        $config->setSgOwnerName(Input::post('sgOwnerName'));
        $config->setSgOwnerStatus(Input::post('sgOwnerStatus'));
        $config->setSgOwnerSiret(Input::post('sgOwnerSiret'));
        $config->setSgOwnerStreet(Input::post('sgOwnerStreet'));
        $config->setSgOwnerPostal(Input::post('sgOwnerPostal'));
        $config->setSgOwnerCity(Input::post('sgOwnerCity'));
        $config->setSgOwnerRegion(Input::post('sgOwnerRegion'));
        $config->setSgOwnerCountry(Input::post('sgOwnerCountry'));
        $config->setSgOwnerEmail(Input::post('sgOwnerEmail'));
        $config->setSgOwnerDomain(Input::post('sgOwnerDomain'));
        $config->setSgOwnerHost(Input::post('sgOwnerHost'));
        $config->setSgOwnerDpoName(Input::post('sgOwnerDpoName'));
        $config->setSgOwnerDpoEmail(Input::post('sgOwnerDpoEmail'));
        $config->setSgGoogleFonts($fonts);

        $this->configurationManager->save($config);
    }

    protected function updateModuleConfigurationLogo(File $objFileLogo): void
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $config->setSgOwnerLogo($objFileLogo->path);

        $this->configurationManager->save($config);
    }

    protected function updateModuleConfigurationTheme(int $themeId): void
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $config->setSgTheme($themeId);

        $this->configurationManager->save($config);
    }

    protected function updateModuleConfigurationModules(array $modules): void
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $formattedModules = [];
        foreach ($modules as $key => $objModule) {
            $formattedModules[] = ['key' => $key, 'type' => $objModule->type, 'id' => $objModule->id];
        }

        $config->setSgModules($formattedModules);

        $this->configurationManager->save($config);
    }

    protected function updateModuleConfigurationImageSizes(array $imageSizes): void
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $formattedImageSizes = [];
        foreach ($imageSizes as $key => $objImageSize) {
            $formattedImageSizes[] = ['key' => $key, 'type' => $objImageSize->name, 'id' => $objImageSize->id];
        }

        $config->setSgImageSizes($formattedImageSizes);

        $this->configurationManager->save($config);
    }

    protected function updateModuleConfigurationUserAndGroups(array $users, array $groups): void
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $config->setSgUserWebmaster((int) $users['webmaster']->id);
        $config->setSgUserGroupRedactors((int) $groups['redactors']->id);
        $config->setSgUserGroupAdministrators((int) $groups['administrators']->id);

        $this->configurationManager->save($config);
    }

    protected function updateModuleConfigurationPages(array $pages): void
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $config->setSgPageRoot((int) $pages['root']->id);
        $config->setSgPageHome((int) $pages['home']->id);
        $config->setSgPage404((int) $pages['404']->id);
        $config->setSgPageLegalNotice((int) $pages['legal_notice']->id);
        $config->setSgPagePrivacyPolitics((int) $pages['privacy_politics']->id);
        $config->setSgPageSitemap((int) $pages['sitemap']->id);

        $this->configurationManager->save($config);
    }

    protected function updateModuleConfigurationArticles(array $articles): void
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $config->setSgArticleHome((int) $articles['home']->id);
        $config->setSgArticle404((int) $articles['404']->id);
        $config->setSgArticleLegalNotice((int) $articles['legal_notice']->id);
        $config->setSgArticlePrivacyPolitics((int) $articles['privacy_politics']->id);
        $config->setSgArticleSitemap((int) $articles['sitemap']->id);

        $this->configurationManager->save($config);
    }

    protected function updateModuleConfigurationContents(array $contents): void
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $config->setSgContent404Headline((int) $contents['404']['headline']->id);
        $config->setSgContent404Sitemap((int) $contents['404']['sitemap']->id);
        $config->setSgContentLegalNotice((int) $contents['legal_notice']->id);
        $config->setSgContentPrivacyPolitics((int) $contents['privacy_politics']->id);
        $config->setSgContentSitemapHeadline((int) $contents['sitemap']['headline']->id);
        $config->setSgContentSitemap((int) $contents['sitemap']['module']->id);

        $this->configurationManager->save($config);
    }

    protected function updateModuleConfigurationLayouts(array $layouts): void
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $config->setSgLayoutStandard((int) $layouts['standard']->id);
        $config->setSgLayoutFullwidth((int) $layouts['fullwidth']->id);

        $this->configurationManager->save($config);
    }

    protected function updateModuleConfigurationNotificationGateways(array $nc): void
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $config->setSgNotificationGatewayEmail((int) $nc['email']->id);

        $this->configurationManager->save($config);
    }

    protected function updateModuleConfigurationNotificationSupportGatewayNotification($notificationSupport): void
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $config->setSgNotificationSupport((int) $notificationSupport->id);

        $this->configurationManager->save($config);
    }

    protected function updateModuleConfigurationNotificationSupportGatewayMessages(array $notificationSupportMessages): void
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $config->setSgNotificationSupportMessageUser((int) $notificationSupportMessages['user']->id);
        $config->setSgNotificationSupportMessageAdmin((int) $notificationSupportMessages['admin']->id);

        $this->configurationManager->save($config);
    }

    protected function updateModuleConfigurationNotificationSupportGatewayMessagesLanguages(array $notificationSupportMessagesLanguages): void
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $config->setSgNotificationSupportMessageUserLanguage((int) $notificationSupportMessagesLanguages['user']->id);
        $config->setSgNotificationSupportMessageAdminLanguage((int) $notificationSupportMessagesLanguages['admin']->id);

        $this->configurationManager->save($config);
    }

    protected function getConfigModulesAsFormattedArray(): array
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $registeredModules = [];
        $registeredModulesRaw = $config->getSgModules();
        foreach ($registeredModulesRaw as $registeredModuleRaw) {
            $registeredModules[$registeredModuleRaw->key] = (int) $registeredModuleRaw->id;
        }

        return $registeredModules;
    }

    protected function getConfigImageSizesAsFormattedArray(): array
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $registeredImageSizes = [];
        $registeredImageSizesRaw = $config->getSgImageSizes();
        foreach ($registeredImageSizesRaw as $registeredImageSizeRaw) {
            $registeredImageSizes[$registeredImageSizeRaw->key] = (int) $registeredImageSizeRaw->id;
        }

        return $registeredImageSizes;
    }

    /**
     * @throws Exception
     */
    protected function updateUserGroups(): void
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $this->updateUserGroup(UserGroupModel::findOneById($config->getSgUserGroupRedactors()), $config);
        $this->updateUserGroup(UserGroupModel::findOneById($config->getSgUserGroupAdministrators()), $config);

        foreach ($this->userGroupUpdaters as $submoduleStep) {
            $submodule = $submoduleStep->getModule();
            $submoduleConfig = $config->getSubmoduleConfig($submodule);
            if ($submoduleConfig->getSgInstallComplete()) {
                if ('blog' === $submodule) {
                    $submoduleStep->updateUserGroups(\in_array(SmartgearPermissions::BLOG_EXPERT, $this->userGroupWebmasterOldPermissions, true));
                } elseif ('events' === $submodule) {
                    $submoduleStep->updateUserGroups(\in_array(SmartgearPermissions::EVENTS_EXPERT, $this->userGroupWebmasterOldPermissions, true));
                } elseif ('extranet' === $submodule) {
                    $objModules = Module::findItems(['id' => $submoduleConfig->getContaoModulesIds()]);
                    $modules = [];
                    if ($objModules instanceof Collection) {
                        while ($objModules->next()) {
                            $modules[] = $objModules->current();
                        }
                    }

                    $submoduleStep->updateUserGroups($modules);
                } else {
                    $submoduleStep->updateUserGroups();
                }
            }
        }
    }

    protected function updateUserGroup(UserGroupModel $objUserGroup, CoreConfig $config): void
    {
        $objFolderClient = FilesModel::findByPath(CoreConfig::DEFAULT_CLIENT_FILES_FOLDER);
        if (!$objFolderClient) {
            throw new Exception('Unable to find the "'.CoreConfig::DEFAULT_CLIENT_FILES_FOLDER.'" folder');
        }

        $objFolderLogos = FilesModel::findByPath(CoreConfig::DEFAULT_CLIENT_LOGOS_FOLDER);
        if (!$objFolderLogos) {
            throw new Exception('Unable to find the "'.CoreConfig::DEFAULT_CLIENT_LOGOS_FOLDER.'" folder');
        }

        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);
        $userGroupManipulator
            ->addAllowedFilemounts($config->getContaoFoldersIds())
            ->addAllowedPagemounts($config->getContaoPagesIds())
            ->addAllowedImageSizes($config->getContaoImageSizesIds())
            // ->addAllowedModules(Module::getTypesByIds($config->getContaoModulesIds()))
        ;

        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->save();
    }

    private function setConfigKey(string $key, int $value): void
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $config->{$key}($value);

        $this->configurationManager->save($config);
    }

    private function setConfigModuleKey(string $moduleType, int $moduleId): void
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $modules = $config->getSgModules();

        $formattedModules = [];
        foreach ($modules as $module) {
            if ($module->type === $moduleType) {
                $formattedModules[] = ['key' => $module->key, 'type' => $moduleType, 'id' => $moduleId];
            } else {
                $formattedModules[] = ['key' => $module->key, 'type' => $module->type, 'id' => $module->id];
            }
        }

        $config->setSgModules($formattedModules);

        $this->configurationManager->save($config);
    }

    private function setConfigImageSizeKey(string $imageSizeName, int $imageSizeId): void
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $imageSizes = $config->getSgImageSizes();

        $formattedImageSizes = [];
        foreach ($imageSizes as $imageSize) {
            if ($imageSize->name === $imageSizeName) {
                $formattedImageSizes[] = ['key' => $imageSize->name, 'name' => $imageSizeName, 'id' => $imageSizeId];
            } else {
                $formattedImageSizes[] = ['key' => $imageSize->name, 'name' => $imageSize->name, 'id' => $imageSize->id];
            }
        }

        $config->setSgImageSizes($formattedImageSizes);

        $this->configurationManager->save($config);
    }
}
