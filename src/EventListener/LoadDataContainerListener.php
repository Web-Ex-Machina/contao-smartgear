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

namespace WEM\SmartgearBundle\EventListener;

use Contao\Input;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\Dca\Manipulator as DCAManipulator;
use WEM\SmartgearBundle\Classes\Utils\Configuration\ConfigurationUtil;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfiguration;
use WEM\SmartgearBundle\Config\Framway as FramwayConfiguration;
use WEM\SmartgearBundle\Config\FramwayTheme as FramwayThemeConfiguration;
use WEM\SmartgearBundle\Config\Manager\Framway as ConfigurationManager;
use WEM\SmartgearBundle\Config\Manager\FramwayCombined as ConfigurationCombinedManager;
use WEM\SmartgearBundle\DataContainer\Article as ArticleDCA;
use WEM\SmartgearBundle\DataContainer\Calendar as CalendarDCA;
use WEM\SmartgearBundle\DataContainer\CalendarEvents as CalendarEventsDCA;
use WEM\SmartgearBundle\DataContainer\Content as ContentDCA;
use WEM\SmartgearBundle\DataContainer\FaqCategory as FaqCategoryDCA;
use WEM\SmartgearBundle\DataContainer\Files as FilesDCA;
use WEM\SmartgearBundle\DataContainer\Form as FormDCA;
use WEM\SmartgearBundle\DataContainer\ImageSize as ImageSizeDCA;
use WEM\SmartgearBundle\DataContainer\Layout as LayoutDCA;
use WEM\SmartgearBundle\DataContainer\Member as MemberDCA;
use WEM\SmartgearBundle\DataContainer\MemberGroup as MemberGroupDCA;
use WEM\SmartgearBundle\DataContainer\Module as ModuleDCA;
use WEM\SmartgearBundle\DataContainer\NewsArchive as NewsArchiveDCA;
use WEM\SmartgearBundle\DataContainer\NotificationGateway as NotificationGatewayDCA;
use WEM\SmartgearBundle\DataContainer\NotificationLanguage as NotificationLanguageDCA;
use WEM\SmartgearBundle\DataContainer\NotificationMessage as NotificationMessageDCA;
use WEM\SmartgearBundle\DataContainer\NotificationNotification as NotificationNotificationDCA;
use WEM\SmartgearBundle\DataContainer\Page as PageDCA;
use WEM\SmartgearBundle\DataContainer\Theme as ThemeDCA;
use WEM\SmartgearBundle\DataContainer\User as UserDCA;
use WEM\SmartgearBundle\DataContainer\UserGroup as UserGroupDCA;
use WEM\SmartgearBundle\Exceptions\File\NotFound as FileNotFoundException;

class LoadDataContainerListener
{
    protected string $do;

    public function __construct(
        protected \Symfony\Contracts\Translation\TranslatorInterface $translator,
        protected CoreConfigurationManager $configurationManager,
        protected ConfigurationManager $framwayConfigurationManager,
        protected ConfigurationCombinedManager $framwayCombinedConfigurationManager,
        protected array $listeners
    ) {
        $this->do = Input::get('do') ?? ''; // always empty ?
    }

    public function __invoke($tables): void
    {
        if (!\is_array($tables)) {
            $tables = [$tables];
        }

        foreach ($tables as $table) {
            $this->applySmartgearBehaviour($table);
            $this->applyListeners($table);
            $this->applyStyleManagerBehaviour($table);
        }
    }

    protected function applySmartgearBehaviour($table): void
    {
        switch ($table) {
            case 'tl_article':
                DCAManipulator::create($table)
                    ->addConfigOnloadCallback(ArticleDCA::class, 'checkPermission')
                    ->setListOperationsDeleteButtonCallback(ArticleDCA::class, 'deleteItem')
                ;
            break;
            case 'tl_calendar':
                DCAManipulator::create($table)
                    ->addConfigOnloadCallback(CalendarDCA::class, 'checkPermission')
                    ->setListOperationsDeleteButtonCallback(CalendarDCA::class, 'deleteItem')
                ;
            break;
            case 'tl_calendar_events':
                DCAManipulator::create($table)
                    ->addConfigOnsubmitCallback(CalendarEventsDCA::class, 'fillCoordinates')
                ;
            break;
            case 'tl_content':
                DCAManipulator::create($table)
                    ->addConfigOnloadCallback(ContentDCA::class, 'checkPermission')
                    ->addConfigOnloadCallback(ContentDCA::class, 'showJsLibraryHint')
                    ->setListOperationsDeleteButtonCallback(ContentDCA::class, 'deleteItem')
                ;
            break;
            case 'tl_faq_category':
                DCAManipulator::create($table)
                    ->addConfigOnloadCallback(FaqCategoryDCA::class, 'checkPermission')
                    ->setListOperationsDeleteButtonCallback(FaqCategoryDCA::class, 'deleteItem')
                ;
            break;
            case 'tl_files':
                DCAManipulator::create($table)
                    ->addConfigOnloadCallback(FilesDCA::class, 'checkPermission')
                    ->setListOperationsDeleteButtonCallback(FilesDCA::class, 'deleteItem')
                    ->addConfigOnloadCallback(FilesDCA::class, 'uploadWarningMessage')
                ;
            break;
            case 'tl_form':
                DCAManipulator::create($table)
                    ->addConfigOnloadCallback(FormDCA::class, 'checkPermission')
                    ->setListOperationsDeleteButtonCallback(FormDCA::class, 'deleteItem')
                ;
            break;
            case 'tl_image_size':
                DCAManipulator::create($table)
                    ->addConfigOnloadCallback(ImageSizeDCA::class, 'checkPermission')
                    ->setListOperationsDeleteButtonCallback(ImageSizeDCA::class, 'deleteItem')
                ;
            break;
            case 'tl_layout':
                DCAManipulator::create($table)
                    ->addConfigOnloadCallback(LayoutDCA::class, 'checkPermission')
                    ->setListOperationsDeleteButtonCallback(LayoutDCA::class, 'deleteItem')
                ;
            break;
            case 'tl_member':
                DCAManipulator::create($table)
                    ->addConfigOnloadCallback(MemberDCA::class, 'checkPermission')
                    ->setListOperationsDeleteButtonCallback(MemberDCA::class, 'deleteItem')
                ;
                try {
                    /** @var CoreConfiguration */
                    $coreConfig = $this->configurationManager->load();
                } catch (Exception) {
                    $coreConfig = null;
                }

                if (!$coreConfig
                || $coreConfig->getSgUsePdmForMembers()
                ) {
                    DCAManipulator::create($table)
                        ->setDataContainer(\WEM\SmartgearBundle\Classes\Dca\Driver\DC_Table::class)
                        ->addConfigOnshowCallback('wem.personal_data_manager.dca.config.callback.show', '__invoke')
                        ->addConfigOndeleteCallback('wem.personal_data_manager.dca.config.callback.delete', '__invoke')
                        ->addConfigOnsubmitCallback('wem.personal_data_manager.dca.config.callback.submit', '__invoke')

                        ->addListLabelLabelCallback('wem.personal_data_manager.dca.listing.callback.list_label_label_for_list', '__invoke')
                        ->addListLabelGroupCallback('wem.personal_data_manager.dca.listing.callback.list_label_group', '__invoke')

                        ->addFieldLoadCallback('firstname', ['smartgear.classes.dca.field.callback.load.tl_member.firstname', '__invoke'])
                        ->addFieldLoadCallback('lastname', ['smartgear.classes.dca.field.callback.load.tl_member.lastname', '__invoke'])
                        ->addFieldLoadCallback('dateOfBirth', ['smartgear.classes.dca.field.callback.load.tl_member.dateOfBirth', '__invoke'])
                        ->addFieldLoadCallback('gender', ['smartgear.classes.dca.field.callback.load.tl_member.gender', '__invoke'])
                        ->addFieldLoadCallback('company', ['smartgear.classes.dca.field.callback.load.tl_member.company', '__invoke'])
                        ->addFieldLoadCallback('street', ['smartgear.classes.dca.field.callback.load.tl_member.street', '__invoke'])
                        ->addFieldLoadCallback('postal', ['smartgear.classes.dca.field.callback.load.tl_member.postal', '__invoke'])
                        ->addFieldLoadCallback('city', ['smartgear.classes.dca.field.callback.load.tl_member.city', '__invoke'])
                        ->addFieldLoadCallback('state', ['smartgear.classes.dca.field.callback.load.tl_member.state', '__invoke'])
                        ->addFieldLoadCallback('country', ['smartgear.classes.dca.field.callback.load.tl_member.country', '__invoke'])
                        ->addFieldLoadCallback('phone', ['smartgear.classes.dca.field.callback.load.tl_member.phone', '__invoke'])
                        ->addFieldLoadCallback('mobile', ['smartgear.classes.dca.field.callback.load.tl_member.mobile', '__invoke'])
                        ->addFieldLoadCallback('fax', ['smartgear.classes.dca.field.callback.load.tl_member.fax', '__invoke'])
                    ;
                }

            break;
            case 'tl_member_group':
                DCAManipulator::create($table)
                    ->addConfigOnloadCallback(MemberGroupDCA::class, 'checkPermission')
                    ->setListOperationsDeleteButtonCallback(MemberGroupDCA::class, 'deleteItem')
                ;
            break;
            case 'tl_module':
                DCAManipulator::create($table)
                    ->addConfigOnloadCallback(ModuleDCA::class, 'checkPermission')
                    ->setListOperationsDeleteButtonCallback(ModuleDCA::class, 'deleteItem')
                ;
            break;
            case 'tl_nc_gateway':
                DCAManipulator::create($table)
                    ->addConfigOnloadCallback(NotificationGatewayDCA::class, 'checkPermission')
                    ->setListOperationsDeleteButtonCallback(NotificationGatewayDCA::class, 'deleteItem')
                ;
            break;
            case 'tl_nc_language':
                DCAManipulator::create($table)
                    ->addConfigOnloadCallback(NotificationLanguageDCA::class, 'checkPermission')
                    ->setListOperationsDeleteButtonCallback(NotificationLanguageDCA::class, 'deleteItem')
                ;
            break;
            case 'tl_nc_message':
                DCAManipulator::create($table)
                    ->addConfigOnloadCallback(NotificationMessageDCA::class, 'checkPermission')
                    ->setListOperationsDeleteButtonCallback(NotificationMessageDCA::class, 'deleteItem')
                ;
            break;
            case 'tl_nc_notification':
                DCAManipulator::create($table)
                    ->addConfigOnloadCallback(NotificationNotificationDCA::class, 'checkPermission')
                    ->setListOperationsDeleteButtonCallback(NotificationNotificationDCA::class, 'deleteItem')
                ;
            break;
            case 'tl_news_archive':
                DCAManipulator::create($table)
                    ->addConfigOnloadCallback(NewsArchiveDCA::class, 'checkPermission')
                    ->setListOperationsDeleteButtonCallback(NewsArchiveDCA::class, 'deleteItem')
                ;
            break;
            case 'tl_page':
                DCAManipulator::create($table)
                    ->addConfigOnloadCallback(PageDCA::class, 'checkPermission')
                    ->setListOperationsDeleteButtonCallback(PageDCA::class, 'deleteItem')
                ;
            break;
            case 'tl_sm_social_network_category':
                DCAManipulator::create($table)
                    ->addConfigOnloadCallback('smartgear.data_container.social_network_category', 'checkPermission')
                    ->setListOperationsDeleteButtonCallback('smartgear.data_container.social_network_category', 'deleteItem')
                    ->setListOperationsEditheaderButtonCallback('smartgear.data_container.social_network_category', 'editHeader')
                ;
            break;
            case 'tl_sm_social_network':
                DCAManipulator::create($table)
                    ->addConfigOnloadCallback('smartgear.data_container.social_network', 'checkPermission')
                    ->setListOperationsDeleteButtonCallback('smartgear.data_container.social_network', 'deleteItem')
                ;
            break;
            case 'tl_theme':
                DCAManipulator::create($table)
                    ->addConfigOnloadCallback(ThemeDCA::class, 'checkPermission')
                    ->setListOperationsDeleteButtonCallback(ThemeDCA::class, 'deleteItem')
                ;
            break;
            case 'tl_user':
                DCAManipulator::create($table)
                    ->addConfigOnloadCallback(UserDCA::class, 'checkPermission')
                    ->setListOperationsDeleteButtonCallback(UserDCA::class, 'deleteItem')
                ;
            break;
            case 'tl_user_group':
                DCAManipulator::create($table)
                    ->addConfigOnloadCallback(UserGroupDCA::class, 'checkPermission')
                    ->setListOperationsDeleteButtonCallback(UserGroupDCA::class, 'deleteItem')
                ;
            break;
        }
    }

    protected function applyListeners($table): void
    {
        foreach ($this->listeners as $listener) {
            $listener->setDo($this->do);
            $listener->__invoke($table);
        }
    }

    protected function applyStyleManagerBehaviour($table): void
    {
        // @todo : Framway path depends on SG installs
        // How do we know wich to use ?
        // here add "explanation"/"reference" to styleManager fields ?
        if (\array_key_exists('TL_DCA', $GLOBALS)
        && \array_key_exists($table, $GLOBALS['TL_DCA'])
        && \array_key_exists('fields', $GLOBALS['TL_DCA'][$table])
        && \array_key_exists('styleManager', $GLOBALS['TL_DCA'][$table]['fields'])
        ) {
            // Input::get('id') available thanks to the help URL built in Widget/ComponentStyleSelect
            // Input::get('framway_path') available thanks to the help URL built in Widget/ComponentStyleSelect
            // $objConfiguration = ConfigurationUtil::findConfigurationForItem($table, (int) Input::get('id'));

            $help = ['framway_path' => [Input::get('framway_path'), Input::get('framway_path')]]; // will be deleted in be_help.html5
            try {
                /** @var FramwayConfiguration */
                $config = $this->framwayConfigurationManager->setConfigurationRootFilePath(Input::get('framway_path'))->load();
                $help['meaningfulLabel'] = ['headspan', $this->translator->trans('WEMSG.FRAMWAY.COLORS.meaningfulLabel', [], 'contao_default')];
                $meaningfulColors = ['primary', 'secondary', 'success', 'info', 'warning', 'error'];
                foreach ($meaningfulColors as $name) {
                    $help[$name] = [
                        '<div style="width:15px;height:15px;border:1px dotted black;" class="bg-'.$name.'"></div>',
                        $this->translator->trans(sprintf('WEMSG.FRAMWAY.COLORS.%s', $name), [], 'contao_default'),
                    ];
                }
            } catch (FileNotFoundException) {
                //nothing
            }

            try {
                /** @var FramwayThemeConfiguration */
                $themeConfig = $this->framwayCombinedConfigurationManager->setConfigurationRootFilePath(Input::get('framway_path'))->load();
                $help['rawLabel'] = ['headspan', $this->translator->trans('WEMSG.FRAMWAY.COLORS.rawLabel', [], 'contao_default')];
                $colors = $themeConfig->getColors();
                foreach (array_keys($colors) as $name) {
                    $help[$name] = [
                        '<div style="width:15px;height:15px;border:1px dotted black;" class="bg-'.$name.'"></div>',
                        $this->translator->trans(sprintf('WEMSG.FRAMWAY.COLORS.%s', $name), [], 'contao_default'),
                    ];
                }
            } catch (FileNotFoundException) {
                //nothing
            }

            $GLOBALS['TL_DCA'][$table]['fields']['styleManager']['reference'] = $help;
        }
    }
}
