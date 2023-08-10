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
use Contao\BackendModule;
use Contao\ContentModel;
use Contao\FaqModel;
use Contao\Input;
use Contao\NewsModel;
use Contao\PageModel;
use Contao\RequestToken;
use Contao\System;
use DateInterval;
use DateTime;
use Exception;

class Reminder extends BackendModule
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'be_wem_sg_remindermanager';
    protected $strId = 'remindermanager';

    public function __construct($dc = null)
    {
        parent::__construct($dc);
        $this->security = System::getContainer()->get('security.helper');
    }

    public function generate(): string
    {
        return parent::generate();
    }

    public function compile(): void
    {
        if(Input::post('TL_AJAX') && $this->strId === Input::post('wem_module')){
            $this->processAjaxRequest(Input::post('action'));
        }
        $arrItems = array_merge($this->getContents(),$this->getArticles(),$this->getPages(),$this->getNews(),$this->getFAQ());
        usort($arrItems,function($itemA,$itemB){
            return (int) $itemA['obsolete_since'] < (int) $itemB['obsolete_since'];
        });
        $this->Template->arrItems = $arrItems;
        $this->Template->strId = $this->strId;
        $this->Template->token = REQUEST_TOKEN;
    }

    public function processAjaxRequest($strAction){
        try {
            switch (Input::post('action')) {
                case 'resetReminder':
                    try {
                        $model = \Contao\Model::getClassFromTable(Input::post('ptable'));
                        $objItem = $model::findById(Input::post('pid'));
                        if (!$objItem) {
                            throw new Exception('Not found');
                        }

                        $dti = new DateInterval($objItem->update_reminder_period);
                        $updateReminderDate = (new DateTime())
                            ->setTimestamp((int) time())
                            ->add($dti)
                        ;
                        $updateReminderDate->setTime((int) $updateReminderDate->format('H'), (int) $updateReminderDate->format('i'), 0);
                        $updateReminderDate = $updateReminderDate->getTimestamp();

                        $objItem->update_reminder_date = $updateReminderDate;
                        $objItem->save();

                        $arrResponse['status'] = 'success';
                        $arrResponse['msg'] = 'OK';
                    } catch (Exception $e) {
                        throw $e;
                    }
                    break;
                case 'disableReminder':
                    try {
                        $model = \Contao\Model::getClassFromTable(Input::post('ptable'));
                        $objItem = $model::findById(Input::post('pid'));
                        if (!$objItem) {
                            throw new Exception('Not found');
                        }

                        $objItem->update_reminder = 0;
                        $objItem->update_reminder_date = 0;
                        $objItem->save();

                        $arrResponse['status'] = 'success';
                        $arrResponse['msg'] = 'OK';
                    } catch (Exception $e) {
                        throw $e;
                    }
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

    protected function getContents():array {
        $arrItems = [];

        $contents = ContentModel::findBy(['update_reminder = ?', 'update_reminder_date <= ?'], [1, time()],['order'=>'update_reminder_date DESC']);
        if ($contents) {
            while ($contents->next()) {
                $objItem = $contents->current();
                $arrItems[] = [
                    'ptable' => ContentModel::getTable(),
                    'pid' => $objItem->id,
                    'label' => $objItem->type,
                    'last_update' => $objItem->tstamp,
                    'obsolete_since' => $objItem->update_reminder_date,
                    'period' => $objItem->update_reminder_period,
                    'actions' => [
                        'edit' => [
                            'class' => 'edit',
                            'icon' => 'system/themes/flexible/icons/edit.svg',
                            'label' => 'Edit',
                            'title' => 'Edit item',
                            'href' => System::getContainer()->getParameter('contao.backend.route_prefix').'?do=article&table='.ContentModel::getTable().'&act=edit&id='.$objItem->id.'&rt='.RequestToken::get(),
                        ],
                        'reset' => [
                            'class' => 'reset',
                            'icon' => 'system/themes/flexible/icons/sync.svg',
                            'label' => 'Reset',
                            'title' => 'Reset item update reminder',
                            'data'=>[
                                'ptable'=>ContentModel::getTable(),
                                'pid'=>$objItem->id
                            ]
                            // 'href' => System::getContainer()->getParameter('contao.backend.route_prefix').'?do=article&table='.ContentModel::getTable().'&act=edit&id='.$objItem->id.'&rt='.REQUEST_TOKEN,
                        ],
                        'disable' => [
                            'class' => 'disable',
                            'icon' => 'system/themes/flexible/icons/delete.svg',
                            'label' => 'Disable',
                            'title' => 'Disable item update reminder',
                            'data'=>[
                                'ptable'=>ContentModel::getTable(),
                                'pid'=>$objItem->id
                            ]
                            // 'href' => System::getContainer()->getParameter('contao.backend.route_prefix').'?do=article&table='.ContentModel::getTable().'&act=edit&id='.$objItem->id.'&rt='.REQUEST_TOKEN,
                        ],
                    ],
                ];
            }
        }

        return $arrItems;
    }

    protected function getArticles():array {
        $arrItems = [];

        $contents = ArticleModel::findBy(['update_reminder = ?', 'update_reminder_date <= ?'], [1, time()],['order'=>'update_reminder_date DESC']);
        if ($contents) {
            while ($contents->next()) {
                $objItem = $contents->current();
                $arrItems[] = [
                    'ptable' => ArticleModel::getTable(),
                    'pid' => $objItem->id,
                    'label' => $objItem->title,
                    'last_update' => $objItem->tstamp,
                    'obsolete_since' => $objItem->update_reminder_date,
                    'period' => $objItem->update_reminder_period,
                    'actions' => [
                        'edit' => [
                            'class' => 'edit',
                            'icon' => 'system/themes/flexible/icons/edit.svg',
                            'label' => 'Edit',
                            'title' => 'Edit item',
                            'href' => System::getContainer()->getParameter('contao.backend.route_prefix').'?do=article&act=edit&id='.$objItem->id.'&rt='.RequestToken::get(),
                        ],
                        'reset' => [
                            'class' => 'reset',
                            'icon' => 'system/themes/flexible/icons/sync.svg',
                            'label' => 'Reset',
                            'title' => 'Reset item update reminder',
                            'data'=>[
                                'ptable'=>ArticleModel::getTable(),
                                'pid'=>$objItem->id
                            ]
                        ],
                        'disable' => [
                            'class' => 'disable',
                            'icon' => 'system/themes/flexible/icons/delete.svg',
                            'label' => 'Disable',
                            'title' => 'Disable item update reminder',
                            'data'=>[
                                'ptable'=>ArticleModel::getTable(),
                                'pid'=>$objItem->id
                            ]
                        ],
                    ],
                ];
            }
        }

        return $arrItems;
    }

    protected function getPages():array {
        $arrItems = [];

        $contents = PageModel::findBy(['update_reminder = ?', 'update_reminder_date <= ?'], [1, time()],['order'=>'update_reminder_date DESC']);
        if ($contents) {
            while ($contents->next()) {
                $objItem = $contents->current();
                $arrItems[] = [
                    'ptable' => PageModel::getTable(),
                    'pid' => $objItem->id,
                    'label' => $objItem->title,
                    'last_update' => $objItem->tstamp,
                    'obsolete_since' => $objItem->update_reminder_date,
                    'period' => $objItem->update_reminder_period,
                    'actions' => [
                        'edit' => [
                            'class' => 'edit',
                            'icon' => 'system/themes/flexible/icons/edit.svg',
                            'label' => 'Edit',
                            'title' => 'Edit item',
                            'href' => System::getContainer()->getParameter('contao.backend.route_prefix').'?do=page&act=edit&id='.$objItem->id.'&rt='.RequestToken::get(),
                        ],
                        'reset' => [
                            'class' => 'reset',
                            'icon' => 'system/themes/flexible/icons/sync.svg',
                            'label' => 'Reset',
                            'title' => 'Reset item update reminder',
                            'data'=>[
                                'ptable'=>PageModel::getTable(),
                                'pid'=>$objItem->id
                            ]
                        ],
                        'disable' => [
                            'class' => 'disable',
                            'icon' => 'system/themes/flexible/icons/delete.svg',
                            'label' => 'Disable',
                            'title' => 'Disable item update reminder',
                            'data'=>[
                                'ptable'=>PageModel::getTable(),
                                'pid'=>$objItem->id
                            ]
                        ],
                    ],
                ];
            }
        }

        return $arrItems;
    }

    protected function getNews():array {
        $arrItems = [];

        $contents = NewsModel::findBy(['update_reminder = ?', 'update_reminder_date <= ?'], [1, time()],['order'=>'update_reminder_date DESC']);
        if ($contents) {
            while ($contents->next()) {
                $objItem = $contents->current();
                $arrItems[] = [
                    'ptable' => NewsModel::getTable(),
                    'pid' => $objItem->id,
                    'label' => $objItem->headline,
                    'last_update' => $objItem->tstamp,
                    'obsolete_since' => $objItem->update_reminder_date,
                    'period' => $objItem->update_reminder_period,
                    'actions' => [
                        'edit' => [
                            'class' => 'edit',
                            'icon' => 'system/themes/flexible/icons/edit.svg',
                            'label' => 'Edit',
                            'title' => 'Edit item',
                            'href' => System::getContainer()->getParameter('contao.backend.route_prefix').'?do=news&table='.NewsModel::getTable().'&act=edit&id='.$objItem->id.'&rt='.RequestToken::get(),
                        ],
                        'reset' => [
                            'class' => 'reset',
                            'icon' => 'system/themes/flexible/icons/sync.svg',
                            'label' => 'Reset',
                            'title' => 'Reset item update reminder',
                            'data'=>[
                                'ptable'=>NewsModel::getTable(),
                                'pid'=>$objItem->id
                            ]
                        ],
                        'disable' => [
                            'class' => 'disable',
                            'icon' => 'system/themes/flexible/icons/delete.svg',
                            'label' => 'Disable',
                            'title' => 'Disable item update reminder',
                            'data'=>[
                                'ptable'=>NewsModel::getTable(),
                                'pid'=>$objItem->id
                            ]
                        ],
                    ],
                ];
            }
        }

        return $arrItems;
    }

    protected function getFAQ():array {
        $arrItems = [];

        $contents = FaqModel::findBy(['update_reminder = ?', 'update_reminder_date <= ?'], [1, time()],['order'=>'update_reminder_date DESC']);
        if ($contents) {
            while ($contents->next()) {
                $objItem = $contents->current();
                $arrItems[] = [
                    'ptable' => FaqModel::getTable(),
                    'pid' => $objItem->id,
                    'label' => $objItem->question,
                    'last_update' => $objItem->tstamp,
                    'obsolete_since' => $objItem->update_reminder_date,
                    'period' => $objItem->update_reminder_period,
                    'actions' => [
                        'edit' => [
                            'class' => 'edit',
                            'icon' => 'system/themes/flexible/icons/edit.svg',
                            'label' => 'Edit',
                            'title' => 'Edit item',
                            'href' => System::getContainer()->getParameter('contao.backend.route_prefix').'?do=faq&table='.FaqModel::getTable().'&act=edit&id='.$objItem->id.'&rt='.RequestToken::get(),
                        ],
                        'reset' => [
                            'class' => 'reset',
                            'icon' => 'system/themes/flexible/icons/sync.svg',
                            'label' => 'Reset',
                            'title' => 'Reset item update reminder',
                            'data'=>[
                                'ptable'=>FaqModel::getTable(),
                                'pid'=>$objItem->id
                            ]
                        ],
                        'disable' => [
                            'class' => 'disable',
                            'icon' => 'system/themes/flexible/icons/delete.svg',
                            'label' => 'Disable',
                            'title' => 'Disable item update reminder',
                            'data'=>[
                                'ptable'=>FaqModel::getTable(),
                                'pid'=>$objItem->id
                            ]
                        ],
                    ],
                ];
            }
        }

        return $arrItems;
    }
}
