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

namespace WEM\SmartgearBundle\EventListener;

use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Dca\Manipulator as DCAManipulator;
use WEM\SmartgearBundle\Config\Framway as FramwayConfiguration;
use WEM\SmartgearBundle\Config\FramwayTheme as FramwayThemeConfiguration;
use WEM\SmartgearBundle\Config\Manager\Framway as ConfigurationManager;
use WEM\SmartgearBundle\Config\Manager\FramwayTheme as ConfigurationThemeManager;
use WEM\SmartgearBundle\DataContainer\Article as ArticleDCA;
use WEM\SmartgearBundle\DataContainer\Calendar as CalendarDCA;
use WEM\SmartgearBundle\DataContainer\CalendarEvents as CalendarEventsDCA;
use WEM\SmartgearBundle\DataContainer\Content as ContentDCA;
use WEM\SmartgearBundle\DataContainer\FaqCategory as FaqCategoryDCA;
use WEM\SmartgearBundle\DataContainer\Files as FilesDCA;
use WEM\SmartgearBundle\DataContainer\Form as FormDCA;
use WEM\SmartgearBundle\DataContainer\Layout as LayoutDCA;
use WEM\SmartgearBundle\DataContainer\Module as ModuleDCA;
use WEM\SmartgearBundle\DataContainer\NewsArchive as NewsArchiveDCA;
use WEM\SmartgearBundle\DataContainer\NotificationGateway as NotificationGatewayDCA;
use WEM\SmartgearBundle\DataContainer\NotificationMessage as NotificationMessageDCA;
use WEM\SmartgearBundle\DataContainer\NotificationNotification as NotificationNotificationDCA;
use WEM\SmartgearBundle\DataContainer\Page as PageDCA;
use WEM\SmartgearBundle\DataContainer\SocialNetwork as SocialNetworkDCA;
use WEM\SmartgearBundle\DataContainer\SocialNetworkCategory as SocialNetworkCategoryDCA;
use WEM\SmartgearBundle\DataContainer\Theme as ThemeDCA;
use WEM\SmartgearBundle\DataContainer\User as UserDCA;
use WEM\SmartgearBundle\DataContainer\UserGroup as UserGroupDCA;
use WEM\SmartgearBundle\Exceptions\File\NotFound as FileNotFoundException;

class LoadDataContainerListener
{
    /** @var TranslatorInterface */
    protected $translator;
    /** @var ConfigurationManager */
    protected $framwayConfigurationManager;
    /** @var ConfigurationThemeManager */
    protected $framwayThemeConfigurationManager;
    /** @var array */
    protected $listeners;

    public function __construct(
        TranslatorInterface $translator,
        ConfigurationManager $framwayConfigurationManager,
        ConfigurationThemeManager $framwayThemeConfigurationManager,
        array $listeners
    ) {
        $this->translator = $translator;
        $this->framwayConfigurationManager = $framwayConfigurationManager;
        $this->framwayThemeConfigurationManager = $framwayThemeConfigurationManager;
        $this->listeners = $listeners;
    }

    public function __invoke(string $table): void
    {
        $this->applySmartgearBehaviour($table);
        $this->applyListeners($table);
        $this->applyStyleManagerBehaviour($table);
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
                ;
            break;
            case 'tl_form':
                DCAManipulator::create($table)
                    ->addConfigOnloadCallback(FormDCA::class, 'checkPermission')
                    ->setListOperationsDeleteButtonCallback(FormDCA::class, 'deleteItem')
                ;
            break;
            case 'tl_layout':
                DCAManipulator::create($table)
                    ->addConfigOnloadCallback(LayoutDCA::class, 'checkPermission')
                    ->setListOperationsDeleteButtonCallback(LayoutDCA::class, 'deleteItem')
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
                    ->addConfigOnloadCallback(SocialNetworkCategoryDCA::class, 'checkPermission')
                    ->setListOperationsDeleteButtonCallback(SocialNetworkCategoryDCA::class, 'deleteItem')
                    ->setListOperationsEditheaderButtonCallback(SocialNetworkCategoryDCA::class, 'editHeader')
                ;
            break;
            case 'tl_sm_social_network':
                DCAManipulator::create($table)
                    ->addConfigOnloadCallback(SocialNetworkDCA::class, 'checkPermission')
                    ->setListOperationsDeleteButtonCallback(SocialNetworkDCA::class, 'deleteItem')
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
            $listener->__invoke($table);
        }
    }

    protected function applyStyleManagerBehaviour($table): void
    {
        // here add "explanation"/"reference" to styleManager fields ?
        if (\array_key_exists($table, $GLOBALS['TL_DCA'])
        && \array_key_exists('fields', $GLOBALS['TL_DCA'][$table])
        && \array_key_exists('styleManager', $GLOBALS['TL_DCA'][$table]['fields'])
        ) {
            try {
                /** @var FramwayConfiguration */
                $config = $this->framwayConfigurationManager->load();
                /** @var FramwayThemeConfiguration */
                $themeConfig = $this->framwayThemeConfigurationManager->load();
                $help = [];
                $help['meaningfulLabel'] = ['headspan', $this->translator->trans('WEMSG.FRAMWAY.COLORS.meaningfulLabel', [], 'contao_default')];
                $meaningfulColors = ['primary', 'secondary', 'success', 'info', 'warning', 'error'];
                foreach ($meaningfulColors as $name) {
                    $help[$name] = [
                        '<div style="width:15px;height:15px;border:1px dotted black;" class="bg-'.$name.'"></div>',
                        $this->translator->trans(sprintf('WEMSG.FRAMWAY.COLORS.%s', $name), [], 'contao_default'),
                    ];
                }

                $help['rawLabel'] = ['headspan', $this->translator->trans('WEMSG.FRAMWAY.COLORS.rawLabel', [], 'contao_default')];
                $colors = $themeConfig->getColors();
                foreach ($colors as $name => $hexa) {
                    $help[$name] = [
                        '<div style="width:15px;height:15px;border:1px dotted black;" class="bg-'.$name.'"></div>',
                        $this->translator->trans(sprintf('WEMSG.FRAMWAY.COLORS.%s', $name), [], 'contao_default'),
                    ];
                }

                $GLOBALS['TL_DCA'][$table]['fields']['styleManager']['reference'] = $help;
            } catch (FileNotFoundException $e) {
                //nothing
            }
        }
    }
}
