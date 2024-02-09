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

use Contao\Config;
use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\DataContainer;
use Contao\Date;
use Contao\Input;
use Contao\LayoutModel;
use Contao\Message;
use Contao\ModuleModel;
use WEM\SmartgearBundle\Classes\Dca\Manipulator as DCAManipulator;
use WEM\SmartgearBundle\Classes\StringUtil;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Classes\Utils\Configuration\ConfigurationItemUtil;
use WEM\SmartgearBundle\DataContainer\Core;
use WEM\SmartgearBundle\Model\Configuration\Configuration as ConfigurationModel;
use WEM\SmartgearBundle\Model\Configuration\ConfigurationItem as ConfigurationItemModel;

class ConfigurationItem extends Core
{
    public function __construct()
    {
        parent::__construct();
    }

    // public function listItems(array $row, string $label, DataContainer $dc, array $labels): array
    public function listItems(array $row, string $label, DataContainer $dc): string
    {
        $objItem = ConfigurationItemModel::findItems(['id' => $row['id']], 1);

        $arrData = [];

        switch ($objItem->type) {
            case ConfigurationItemModel::TYPE_PAGE_LEGAL_NOTICE:
            case ConfigurationItemModel::TYPE_PAGE_PRIVACY_POLITICS:
                if ($objItem->contao_page) {
                    $objPage = $objItem->getRelated('contao_page');
                    $arrData['contao_page'] = $objPage ? $objPage->title : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_page'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                break;
            case ConfigurationItemModel::TYPE_PAGE_SITEMAP:
                if ($objItem->contao_page) {
                    $objPage = $objItem->getRelated('contao_page');
                    $arrData['contao_page'] = $objPage ? $objPage->title : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_page'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }

                if ($objItem->contao_module) {
                    $objModule = $objItem->getRelated('contao_module');
                    $arrData['contao_module'] = $objModule ? $objModule->name : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_module'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                break;
            case ConfigurationItemModel::TYPE_USER_GROUP_ADMINISTRATORS:
            case ConfigurationItemModel::TYPE_USER_GROUP_REDACTORS:
                if ($objItem->contao_user_group) {
                    $objUserGroup = $objItem->getRelated('contao_user_group');
                    $arrData['contao_user_group'] = $objUserGroup ? $objUserGroup->name : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_user_group'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                break;
            case ConfigurationItemModel::TYPE_MODULE_WEM_SG_HEADER:
                if ($objItem->contao_module) {
                    $objModule = $objItem->getRelated('contao_module');
                    $arrData['contao_module'] = $objModule ? $objModule->name : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_module'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                break;
            case ConfigurationItemModel::TYPE_MODULE_WEM_SG_FOOTER:
                if ($objItem->contao_module) {
                    $objModule = $objItem->getRelated('contao_module');
                    $arrData['contao_module'] = $objModule ? $objModule->name : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_module'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                break;
            case ConfigurationItemModel::TYPE_MODULE_BREADCRUMB:
                if ($objItem->contao_module) {
                    $objModule = $objItem->getRelated('contao_module');
                    $arrData['contao_module'] = $objModule ? $objModule->name : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_module'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                break;
            case ConfigurationItemModel::TYPE_MODULE_WEM_SG_SOCIAL_NETWORKS:
                if ($objItem->contao_module) {
                    $objModule = $objItem->getRelated('contao_module');
                    $arrData['contao_module'] = $objModule ? $objModule->name : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_module'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                break;
            case ConfigurationItemModel::TYPE_MIXED_SITEMAP:
                if ($objItem->contao_page) {
                    $objPage = $objItem->getRelated('contao_page');
                    $arrData['contao_page'] = $objPage ? $objPage->title : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_page'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                if ($objItem->contao_module) {
                    $objModule = $objItem->getRelated('contao_module');
                    $arrData['contao_module'] = $objModule ? $objModule->name : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_module'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                break;
            case ConfigurationItemModel::TYPE_MIXED_FAQ:
                if ($objItem->contao_page) {
                    $objPage = $objItem->getRelated('contao_page');
                    $arrData['contao_page'] = $objPage ? $objPage->title : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_page'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                if ($objItem->contao_module) {
                    $objModule = $objItem->getRelated('contao_module');
                    $arrData['contao_module'] = $objModule ? $objModule->name : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_module'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                if ($objItem->contao_faq_category) {
                    $objFaqCategory = $objItem->getRelated('contao_faq_category');
                    $arrData['contao_faq_category'] = $objFaqCategory ? $objFaqCategory->title : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_faq_category'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                break;
            case ConfigurationItemModel::TYPE_MIXED_EVENTS:
                if ($objItem->contao_page) {
                    $objPage = $objItem->getRelated('contao_page');
                    $arrData['contao_page'] = $objPage ? $objPage->title : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_page'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                if ($objItem->contao_module_list) {
                    $objModule = $objItem->getRelated('contao_module_list');
                    $arrData['contao_module_list'] = $objModule ? $objModule->name : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_module_list'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                if ($objItem->contao_module_reader) {
                    $objModule = $objItem->getRelated('contao_module_reader');
                    $arrData['contao_module_reader'] = $objModule ? $objModule->name : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_module_reader'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                if ($objItem->contao_module_calendar) {
                    $objModule = $objItem->getRelated('contao_module_calendar');
                    $arrData['contao_module_calendar'] = $objModule ? $objModule->name : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_module_calendar'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                if ($objItem->contao_calendar) {
                    $objCalendar = $objItem->getRelated('contao_calendar');
                    $arrData['contao_calendar'] = $objCalendar ? $objCalendar->title : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_calendar'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                break;
            case ConfigurationItemModel::TYPE_MIXED_BLOG:
                if ($objItem->contao_page) {
                    $objPage = $objItem->getRelated('contao_page');
                    $arrData['contao_page'] = $objPage ? $objPage->title : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_page'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                if ($objItem->contao_module_list) {
                    $objModule = $objItem->getRelated('contao_module_list');
                    $arrData['contao_module_list'] = $objModule ? $objModule->name : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_module_list'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                if ($objItem->contao_module_reader) {
                    $objModule = $objItem->getRelated('contao_module_reader');
                    $arrData['contao_module_reader'] = $objModule ? $objModule->name : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_module_reader'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                if ($objItem->contao_news_archive) {
                    $objNewsArchive = $objItem->getRelated('contao_news_archive');
                    $arrData['contao_news_archive'] = $objNewsArchive ? $objNewsArchive->title : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_news_archive'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                break;
            case ConfigurationItemModel::TYPE_MIXED_FORM_CONTACT:
                if ($objItem->contao_page_form) {
                    $objPage = $objItem->getRelated('contao_page_form');
                    $arrData['contao_page_form'] = $objPage ? $objPage->title : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_page_form'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                if ($objItem->contao_page_form_sent) {
                    $objPage = $objItem->getRelated('contao_page_form_sent');
                    $arrData['contao_page_form_sent'] = $objPage ? $objPage->title : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_page_form_sent'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                if ($objItem->contao_form) {
                    $objForm = $objItem->getRelated('contao_form');
                    $arrData['contao_form'] = $objForm ? $objForm->title : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_form'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                if ($objItem->contao_notification) {
                    $objNotification = $objItem->getRelated('contao_notification');
                    $arrData['contao_notification'] = $objNotification ? $objNotification->title : $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                } else {
                    $arrData['contao_notification'] = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['NotFilled'];
                }
                break;
        }

        $labels = [];
        foreach ($arrData as $property => $value) {
            $labels[] = '<strong>'.$GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()][$property][0].' :</strong> '.$value;
        }

        return implode('<br />', $labels);
    }

    public function onloadCallback(DataContainer $dc): void
    {
        if ('edit' === Input::get('act')) {
            // check contents linked tstamp against the configuration item one
            $this->checkLinkedContaoContentUpdated($dc);
            $this->checkRequiredConditions($dc);
        }
    }

    public function onsubmitCallback(DataContainer $dc): void
    {
        // only do that if it is a real save, not a reload
        if ('auto' === Input::post('SUBMIT_TYPE')) {
            return;
        }

        $objItem = ConfigurationItemModel::findOneById($dc->activeRecord->id);
        $objItem->refresh(); // otherwise $dc->activeRecord is the only version with updated values

        $arrUpdates = [
            'update_page' => (bool) Input::post('update_page'),
            'update_user_group' => (bool) Input::post('update_user_group'),
            'update_module' => (bool) Input::post('update_module'),
            'update_faq_category' => (bool) Input::post('update_faq_category'),
            'update_module_list' => (bool) Input::post('update_module_list'),
            'update_module_reader' => (bool) Input::post('update_module_reader'),
            'update_module_calendar' => (bool) Input::post('update_module_calendar'),
            'update_calendar' => (bool) Input::post('update_calendar'),
            'update_news_archive' => (bool) Input::post('update_news_archive'),
            'update_page_form' => (bool) Input::post('update_page_form'),
            'update_page_form_sent' => (bool) Input::post('update_page_form_sent'),
            'update_form' => (bool) Input::post('update_form'),
            'update_notification' => (bool) Input::post('update_notification'),
        ];
        ConfigurationItemUtil::createEverythingFromConfigurationItem($objItem, $arrUpdates, (int) $dc->activeRecord->tstamp);
    }

    public function ondeleteCallback(DataContainer $dc): void
    {
        $objItem = ConfigurationItemModel::findOneById($dc->activeRecord->id);
        ConfigurationItemUtil::deleteEverythingFromConfigurationItem($objItem);
    }

    public function contentTemplateOptionsCallback(DataContainer $dc): array
    {
        $arrOptions = [];

        switch ($dc->activeRecord->type) {
            case ConfigurationItemModel::TYPE_PAGE_LEGAL_NOTICE:
                $arrOptions = Util::getFileListByLanguages(Util::getPublicOrWebDirectory().'/bundles/wemsmartgear/examples/legal-notice');
                break;
            case ConfigurationItemModel::TYPE_PAGE_PRIVACY_POLITICS:
                $arrOptions = Util::getFileListByLanguages(Util::getPublicOrWebDirectory().'/bundles/wemsmartgear/examples/privacy-politics');
                break;
            case ConfigurationItemModel::TYPE_MODULE_WEM_SG_FOOTER:
                $arrOptions = Util::getFileListByLanguages(Util::getPublicOrWebDirectory().'/bundles/wemsmartgear/examples/footer');
                break;
        }

        return ['-'] + $arrOptions;
    }

    public function contaoModuleOptionsCallback(DataContainer $dc): array
    {
        $arrOptions = [];

        $objItem = ConfigurationItemModel::findOneById($dc->activeRecord->id);
        /** @var ConfigurationModel */
        $objConfiguration = $objItem->getRelated('pid');

        $modules = ModuleModel::findByPid($objConfiguration->contao_theme);
        if ($modules) {
            while ($modules->next()) {
                $arrOptions[$modules->type][$modules->id] = $modules->name;
            }
        }

        return $arrOptions;
    }

    public function contaoLayoutToUpdateOptionsCallback(DataContainer $dc): array
    {
        $arrOptions = [];

        $objItem = ConfigurationItemModel::findOneById($dc->activeRecord->id);
        /** @var ConfigurationModel */
        $objConfiguration = $objItem->getRelated('pid');

        switch ($dc->activeRecord->type) {
            case ConfigurationItemModel::TYPE_MODULE_WEM_SG_HEADER:
            case ConfigurationItemModel::TYPE_MODULE_WEM_SG_FOOTER:
            case ConfigurationItemModel::TYPE_MODULE_BREADCRUMB:
                $layouts = LayoutModel::findByPid($objConfiguration->contao_theme);
                if ($layouts) {
                    while ($layouts->next()) {
                        $arrOptions[$layouts->id] = $layouts->name;
                    }
                }
                // $arrOptions = Util::getFileListByLanguages(Util::getPublicOrWebDirectory().'/bundles/wemsmartgear/examples/legal-notice');
                break;
        }

        return $arrOptions;
    }

    public function checkRequiredConditions(DataContainer $dc): void
    {
        if (!$dc->id) {
            return;
        }
        $objItem = ConfigurationItemModel::findOneById($dc->id);
        $objConfiguration = $objItem->getRelated('pid');
        switch ($objItem->type) {
            case ConfigurationItemModel::TYPE_MIXED_FORM_CONTACT:
                if (empty($objConfiguration->email_gateway)) {
                    Message::addError('La configuration ne stipule aucune passerelle email : il ne sera pas possible de créer la notification si besoin.');
                }
            break;
        }
    }

    public function checkLinkedContaoContentUpdated(DataContainer $dc): void
    {
        if (!$dc->id) {
            return;
        }
        $objItem = ConfigurationItemModel::findOneById($dc->id);

        $dcaManipulator = DCAManipulator::create(ConfigurationItemModel::getTable());
        $addFields = false;

        if ($objItem->contao_page) {
            $objPage = $objItem->getRelated('contao_page');
            if ($objPage
            && (int) $objPage->tstamp > (int) $objItem->tstamp
            ) {
                Message::addInfo(sprintf($GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['contao_page_updated_outside_sg_configuration'], $objPage->title, $objPage->id, Date::parse(Config::get('datimFormat'), (int) $objItem->tstamp)));
            }
            if (0 !== (int) $objItem->tstamp) {
                $dcaManipulator
                    ->addField('update_page', [
                        'label' => &$GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['update_page'],
                        'inputType' => 'checkbox',
                        'save_callback' => [function ($val) {return ''; }], // so Contao does not try to save this fake field
                        'eval' => ['doNotSaveEmpty' => true], // so Contao does not try to save this fake field
                    ])
                ;
                $addFields = true;
            }
        }

        if ($objItem->contao_page_form) {
            $objPage = $objItem->getRelated('contao_page_form');
            if ($objPage
            && (int) $objPage->tstamp > (int) $objItem->tstamp
            ) {
                Message::addInfo(sprintf($GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['contao_page_form_updated_outside_sg_configuration'], $objPage->title, $objPage->id, Date::parse(Config::get('datimFormat'), (int) $objItem->tstamp)));
            }
            if (0 !== (int) $objItem->tstamp) {
                $dcaManipulator
                    ->addField('update_page_form', [
                        'label' => &$GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['update_page_form'],
                        'inputType' => 'checkbox',
                        'save_callback' => [function ($val) {return ''; }], // so Contao does not try to save this fake field
                        'eval' => ['doNotSaveEmpty' => true], // so Contao does not try to save this fake field
                    ])
                ;
                $addFields = true;
            }
        }

        if ($objItem->contao_page_form_sent) {
            $objPage = $objItem->getRelated('contao_page_form_sent');
            if ($objPage
            && (int) $objPage->tstamp > (int) $objItem->tstamp
            ) {
                Message::addInfo(sprintf($GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['contao_page_form_sent_updated_outside_sg_configuration'], $objPage->title, $objPage->id, Date::parse(Config::get('datimFormat'), (int) $objItem->tstamp)));
            }
            if (0 !== (int) $objItem->tstamp) {
                $dcaManipulator
                    ->addField('update_page_form_sent', [
                        'label' => &$GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['update_page_form_sent'],
                        'inputType' => 'checkbox',
                        'save_callback' => [function ($val) {return ''; }], // so Contao does not try to save this fake field
                        'eval' => ['doNotSaveEmpty' => true], // so Contao does not try to save this fake field
                    ])
                ;
                $addFields = true;
            }
        }

        if ($objItem->contao_module) {
            $objModule = $objItem->getRelated('contao_module');
            if ($objModule
            && (int) $objModule->tstamp > (int) $objItem->tstamp
            ) {
                Message::addInfo(sprintf($GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['contao_module_updated_outside_sg_configuration'], $objModule->name, $objModule->id, Date::parse(Config::get('datimFormat'), (int) $objItem->tstamp)));
            }
            if (0 !== (int) $objItem->tstamp) {
                $dcaManipulator
                    ->addField('update_module', [
                        'label' => &$GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['update_module'],
                        'inputType' => 'checkbox',
                        'save_callback' => [function ($val) {return ''; }], // so Contao does not try to save this fake field
                        'eval' => ['doNotSaveEmpty' => true], // so Contao does not try to save this fake field
                    ])
                ;
            }
            $addFields = true;
        }

        if ($objItem->contao_module_list) {
            $objModule = $objItem->getRelated('contao_module_list');
            if ($objModule
            && (int) $objModule->tstamp > (int) $objItem->tstamp
            ) {
                Message::addInfo(sprintf($GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['contao_module_list_updated_outside_sg_configuration'], $objModule->name, $objModule->id, Date::parse(Config::get('datimFormat'), (int) $objItem->tstamp)));
            }
            if (0 !== (int) $objItem->tstamp) {
                $dcaManipulator
                    ->addField('update_module_list', [
                        'label' => &$GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['update_module_list'],
                        'inputType' => 'checkbox',
                        'save_callback' => [function ($val) {return ''; }], // so Contao does not try to save this fake field
                        'eval' => ['doNotSaveEmpty' => true], // so Contao does not try to save this fake field
                    ])
                ;
            }
            $addFields = true;
        }

        if ($objItem->contao_module_reader) {
            $objModule = $objItem->getRelated('contao_module_reader');
            if ($objModule
            && (int) $objModule->tstamp > (int) $objItem->tstamp
            ) {
                Message::addInfo(sprintf($GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['contao_module_reader_updated_outside_sg_configuration'], $objModule->name, $objModule->id, Date::parse(Config::get('datimFormat'), (int) $objItem->tstamp)));
            }
            if (0 !== (int) $objItem->tstamp) {
                $dcaManipulator
                    ->addField('update_module_reader', [
                        'label' => &$GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['update_module_reader'],
                        'inputType' => 'checkbox',
                        'save_callback' => [function ($val) {return ''; }], // so Contao does not try to save this fake field
                        'eval' => ['doNotSaveEmpty' => true], // so Contao does not try to save this fake field
                    ])
                ;
            }
            $addFields = true;
        }

        if ($objItem->contao_module_calendar) {
            $objModule = $objItem->getRelated('contao_module_calendar');
            if ($objModule
            && (int) $objModule->tstamp > (int) $objItem->tstamp
            ) {
                Message::addInfo(sprintf($GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['contao_module_calendar_updated_outside_sg_configuration'], $objModule->name, $objModule->id, Date::parse(Config::get('datimFormat'), (int) $objItem->tstamp)));
            }
            if (0 !== (int) $objItem->tstamp) {
                $dcaManipulator
                    ->addField('update_module_calendar', [
                        'label' => &$GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['update_module_calendar'],
                        'inputType' => 'checkbox',
                        'save_callback' => [function ($val) {return ''; }], // so Contao does not try to save this fake field
                        'eval' => ['doNotSaveEmpty' => true], // so Contao does not try to save this fake field
                    ])
                ;
            }
            $addFields = true;
        }

        if ($objItem->contao_user_group) {
            $objUserGroup = $objItem->getRelated('contao_user_group');
            if ($objUserGroup
            && (int) $objUserGroup->tstamp > (int) $objItem->tstamp
            ) {
                Message::addInfo(sprintf($GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['contao_user_group_updated_outside_sg_configuration'], $objUserGroup->name, $objUserGroup->id, Date::parse(Config::get('datimFormat'), (int) $objItem->tstamp)));
            }
            if (0 !== (int) $objItem->tstamp) {
                $dcaManipulator
                    ->addField('update_user_group', [
                        'label' => &$GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['update_user_group'],
                        'inputType' => 'checkbox',
                        'save_callback' => [function ($val) {return ''; }], // so Contao does not try to save this fake field
                        'eval' => ['doNotSaveEmpty' => true], // so Contao does not try to save this fake field
                    ])
                ;
                $addFields = true;
            }
        }

        if ($objItem->contao_faq_category) {
            $objFaqCat = $objItem->getRelated('contao_faq_category');
            if ($objFaqCat
            && (int) $objFaqCat->tstamp > (int) $objItem->tstamp
            ) {
                Message::addInfo(sprintf($GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['contao_faq_category_updated_outside_sg_configuration'], $objFaqCat->name, $objFaqCat->id, Date::parse(Config::get('datimFormat'), (int) $objItem->tstamp)));
            }
            if (0 !== (int) $objItem->tstamp) {
                $dcaManipulator
                    ->addField('update_faq_category', [
                        'label' => &$GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['update_faq_category'],
                        'inputType' => 'checkbox',
                        'save_callback' => [function ($val) {return ''; }], // so Contao does not try to save this fake field
                        'eval' => ['doNotSaveEmpty' => true], // so Contao does not try to save this fake field
                    ])
                ;
                $addFields = true;
            }
        }

        if ($objItem->contao_calendar) {
            $objCal = $objItem->getRelated('contao_calendar');
            if ($objCal
            && (int) $objCal->tstamp > (int) $objItem->tstamp
            ) {
                Message::addInfo(sprintf($GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['contao_calendar_updated_outside_sg_configuration'], $objCal->name, $objCal->id, Date::parse(Config::get('datimFormat'), (int) $objItem->tstamp)));
            }
            if (0 !== (int) $objItem->tstamp) {
                $dcaManipulator
                    ->addField('update_calendar', [
                        'label' => &$GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['update_calendar'],
                        'inputType' => 'checkbox',
                        'save_callback' => [function ($val) {return ''; }], // so Contao does not try to save this fake field
                        'eval' => ['doNotSaveEmpty' => true], // so Contao does not try to save this fake field
                    ])
                ;
                $addFields = true;
            }
        }

        if ($objItem->contao_news_archive) {
            $objNewsArch = $objItem->getRelated('contao_news_archive');
            if ($objNewsArch
            && (int) $objNewsArch->tstamp > (int) $objItem->tstamp
            ) {
                Message::addInfo(sprintf($GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['contao_news_archive_updated_outside_sg_configuration'], $objNewsArch->title, $objNewsArch->id, Date::parse(Config::get('datimFormat'), (int) $objItem->tstamp)));
            }
            if (0 !== (int) $objItem->tstamp) {
                $dcaManipulator
                    ->addField('update_news_archive', [
                        'label' => &$GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['update_news_archive'],
                        'inputType' => 'checkbox',
                        'save_callback' => [function ($val) {return ''; }], // so Contao does not try to save this fake field
                        'eval' => ['doNotSaveEmpty' => true], // so Contao does not try to save this fake field
                    ])
                ;
                $addFields = true;
            }
        }

        if ($objItem->contao_form) {
            $objForm = $objItem->getRelated('contao_form');
            if ($objForm
            && (int) $objForm->tstamp > (int) $objItem->tstamp
            ) {
                Message::addInfo(sprintf($GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['contao_form_updated_outside_sg_configuration'], $objForm->title, $objForm->id, Date::parse(Config::get('datimFormat'), (int) $objItem->tstamp)));
            }
            if (0 !== (int) $objItem->tstamp) {
                $dcaManipulator
                    ->addField('update_form', [
                        'label' => &$GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['update_form'],
                        'inputType' => 'checkbox',
                        'save_callback' => [function ($val) {return ''; }], // so Contao does not try to save this fake field
                        'eval' => ['doNotSaveEmpty' => true], // so Contao does not try to save this fake field
                    ])
                ;
                $addFields = true;
            }
        }

        if ($objItem->contao_notification) {
            $objNcNotif = $objItem->getRelated('contao_notification');
            if ($objNcNotif
            && (int) $objNcNotif->tstamp > (int) $objItem->tstamp
            ) {
                Message::addInfo(sprintf($GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['contao_notification_updated_outside_sg_configuration'], $objNcNotif->title, $objNcNotif->id, Date::parse(Config::get('datimFormat'), (int) $objItem->tstamp)));
            }
            if (0 !== (int) $objItem->tstamp) {
                $dcaManipulator
                    ->addField('update_notification', [
                        'label' => &$GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['update_notification'],
                        'inputType' => 'checkbox',
                        'save_callback' => [function ($val) {return ''; }], // so Contao does not try to save this fake field
                        'eval' => ['doNotSaveEmpty' => true], // so Contao does not try to save this fake field
                    ])
                ;
                $addFields = true;
            }
        }

        if (\in_array($objItem->type, array_merge(ConfigurationItemModel::TYPES_MIXED, ConfigurationItemModel::TYPES_PAGE, ConfigurationItemModel::TYPES_MODULE), true)
        && 0 < ConfigurationItemModel::countItems(['pid' => $objItem->pid, 'type' => ConfigurationItemModel::TYPES_USER_GROUP])
        ) {
            $dcaManipulator
                ->addField('update_user_group_permission', [
                    'label' => &$GLOBALS['TL_LANG'][ConfigurationItemModel::getTable()]['update_user_group_permission'],
                    'inputType' => 'checkbox',
                    'save_callback' => [function ($val) {return ''; }], // so Contao does not try to save this fake field
                    'eval' => ['doNotSaveEmpty' => true], // so Contao does not try to save this fake field
                ])
            ;
            $addFields = true;
        }

        $paletteManipulator = PaletteManipulator::create();
        if ($addFields) {
            $paletteManipulator->addLegend('update_legend');
            if ($dcaManipulator->hasField('update_page')) {
                $paletteManipulator->addField('update_page', 'update_legend');
            }
            if ($dcaManipulator->hasField('update_page_form')) {
                $paletteManipulator->addField('update_page_form', 'update_legend');
            }
            if ($dcaManipulator->hasField('update_page_form_sent')) {
                $paletteManipulator->addField('update_page_form_sent', 'update_legend');
            }
            if ($dcaManipulator->hasField('update_module')) {
                $paletteManipulator->addField('update_module', 'update_legend');
            }
            if ($dcaManipulator->hasField('update_module_list')) {
                $paletteManipulator->addField('update_module_list', 'update_legend');
            }
            if ($dcaManipulator->hasField('update_module_reader')) {
                $paletteManipulator->addField('update_module_reader', 'update_legend');
            }
            if ($dcaManipulator->hasField('update_module_calendar')) {
                $paletteManipulator->addField('update_module_calendar', 'update_legend');
            }
            if ($dcaManipulator->hasField('update_user_group')) {
                $paletteManipulator->addField('update_user_group', 'update_legend');
            }
            if ($dcaManipulator->hasField('update_faq_category')) {
                $paletteManipulator->addField('update_faq_category', 'update_legend');
            }
            if ($dcaManipulator->hasField('update_calendar')) {
                $paletteManipulator->addField('update_calendar', 'update_legend');
            }
            if ($dcaManipulator->hasField('update_news_archive')) {
                $paletteManipulator->addField('update_news_archive', 'update_legend');
            }
            if ($dcaManipulator->hasField('update_form')) {
                $paletteManipulator->addField('update_form', 'update_legend');
            }
            if ($dcaManipulator->hasField('update_notification')) {
                $paletteManipulator->addField('update_notification', 'update_legend');
            }
            if ($dcaManipulator->hasField('update_user_group_permission')) {
                $paletteManipulator->addField('update_user_group_permission', 'update_legend');
            }
            $paletteManipulator->applyToPalette('default', ConfigurationItemModel::getTable());
        }
    }

    // public function contaoLayoutToUpdateLoadCallback($value, DataContainer $dc)
    // {
    //     $arrOptions = [];

    //     $objItem = ConfigurationItemModel::findOneById($dc->activeRecord->id);
    //     /** @var ConfigurationModel */
    //     $objConfiguration = $objItem->getRelated('pid');
    //     $layouts = LayoutModel::findByPid($objConfiguration->contao_theme);
    //     if (!$layouts) {
    //         return [];
    //     }

    //     $arrLayouts = $layouts->fetchAll();

    //     switch ($dc->activeRecord->type) {
    //         case ConfigurationItemModel::TYPE_MODULE_WEM_SG_HEADER:
    //             foreach ($arrLayouts as $layout) {
    //                 $arrModules = StringUtil::deserialize($layout['modules'], true);
    //                 foreach ($arrModules as $moduleLayout) {
    //                     if('header' === $moduleLayout['col']){
    //                         $objModule = ModuleModel::findById($moduleLayout['mod']);
    //                     }
    //                 }
    //             }
    //         break;
    //         case ConfigurationItemModel::TYPE_MODULE_WEM_SG_FOOTER:
    //         break;
    //         case ConfigurationItemModel::TYPE_MODULE_BREADCRUMB:
    //         break;
    //     }

    //     return $arrOptions;
    // }
}
