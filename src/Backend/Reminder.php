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
use Contao\DataContainer;
use Contao\FaqModel;
use Contao\Input;
use Contao\NewsModel;
use Contao\PageModel;
use Contao\CoreBundle\Csrf\ContaoCsrfTokenManager;
use Contao\System;
use DateInterval;
use DateTime;
use Exception;
use WEM\SmartgearBundle\Classes\Util;

class Reminder extends BackendModule
{

    protected $strTemplate = 'be_wem_sg_remindermanager';

    protected mixed $security;

    protected string $strId = 'remindermanager';

    public function __construct(
        protected readonly ContaoCsrfTokenManager $contaoCsrfTokenManager,
        DataContainer|null $dc = null)
    {
        parent::__construct($dc);
        $this->security = System::getContainer()->get('security.helper');
    }

    public function generate(): string
    {
        return parent::generate();
    }

    protected function compile(): void
    {
        if (Input::post('TL_AJAX') && Input::post('TL_WEM_AJAX') && $this->strId === Input::post('wem_module')) {
            $this->processAjaxRequest(Input::post('action'));
        }

        $this->dtNow = new DateTime();

        $arrItems = array_merge($this->getContents(), $this->getArticles(), $this->getPages(), $this->getNews(), $this->getFAQ());
        usort($arrItems, static fn($itemA, $itemB): bool => (int) $itemA['obsolete_since'] < (int) $itemB['obsolete_since']);
        $this->Template->arrItems = $arrItems;
        $this->Template->strId = $this->strId;
        $this->Template->token = $this->contaoCsrfTokenManager->getDefaultTokenValue();
    }

    public function processAjaxRequest($strAction): void
    {
        if (Input::post('TL_WEM_AJAX') && $this->strId === Input::post('wem_module')) {
            try {
                switch (Input::post('action')) {
                case 'resetReminder':
                    $model = \Contao\Model::getClassFromTable(Input::post('ptable'));
                    $objItem = $model::findById(Input::post('pid'));
                    if (!$objItem) {
                        throw new Exception('Not found');
                    }

                    $dti = new DateInterval($objItem->update_reminder_period);
                    $updateReminderDate = (new DateTime())
                        ->setTimestamp(time())
                        ->add($dti)
                    ;
                    $updateReminderDate->setTime((int) $updateReminderDate->format('H'), (int) $updateReminderDate->format('i'), 0);
                    $updateReminderDate = $updateReminderDate->getTimestamp();
                    $objItem->update_reminder_date = $updateReminderDate;
                    $objItem->save();
                    $arrResponse['status'] = 'success';
                    $arrResponse['msg'] = 'OK';

                    break;
                case 'disableReminder':
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

                    break;
            }
            } catch (Exception $e) {
                $arrResponse = ['status' => 'error', 'msg' => $e->getMessage(), 'trace' => $e->getTrace()];
            }

            // Add Request Token to JSON answer and return
            $arrResponse['rt'] = $this->contaoCsrfTokenManager->getDefaultTokenValue();
            echo json_encode($arrResponse);
            exit;
        }
    }

    protected function getContents(): array
    {
        $arrItems = [];

        $contents = ContentModel::findBy(['update_reminder = ?', 'update_reminder_date <= ?'], [1, time()], ['order' => 'update_reminder_date DESC']);
        if ($contents) {
            while ($contents->next()) {
                $objItem = $contents->current();
                $obsoleteSinceText = $this->calculateObsoleteSinceText((int) $objItem->update_reminder_date);
                $obsoleteSinceText = \strlen($obsoleteSinceText) > 0 ? $obsoleteSinceText : $GLOBALS['TL_LANG']['WEMSG']['REMINDERMANAGER']['LIST']['obsoleteSinceTextLessThanAMinute'];
                $arrItems[] = [
                    'ptable' => ContentModel::getTable(),
                    'pid' => $objItem->id,
                    'label' => $objItem->type,
                    'last_update' => $objItem->tstamp,
                    'obsolete_since' => $objItem->update_reminder_date,
                    'obsolete_since_text' => $obsoleteSinceText,
                    'period' => $objItem->update_reminder_period,
                    'actions' => [
                        'edit' => [
                            'class' => 'edit',
                            'icon' => 'system/themes/flexible/icons/edit.svg',
                            // 'label' => &$GLOBALS['TL_LANG']['WEMSG']['REMINDERMANAGER']['LIST']['actionEdit'],
                            'title' => &$GLOBALS['TL_LANG']['WEMSG']['REMINDERMANAGER']['LIST']['actionEditTitle'],
                            'href' => System::getContainer()->getParameter('contao.backend.route_prefix').'?do=article&table='.ContentModel::getTable().'&act=edit&id='.$objItem->id.'&rt='.$this->contaoCsrfTokenManager->getDefaultTokenValue(),
                        ],
                        'reset' => [
                            'class' => 'reset',
                            'icon' => 'system/themes/flexible/icons/sync.svg',
                            // 'label' => &$GLOBALS['TL_LANG']['WEMSG']['REMINDERMANAGER']['LIST']['actionReset'],
                            'title' => &$GLOBALS['TL_LANG']['WEMSG']['REMINDERMANAGER']['LIST']['actionResetTitle'],
                            'data' => [
                                'ptable' => ContentModel::getTable(),
                                'pid' => $objItem->id,
                            ],
                            // 'href' => System::getContainer()->getParameter('contao.backend.route_prefix').'?do=article&table='.ContentModel::getTable().'&act=edit&id='.$objItem->id.'&rt='.REQUEST_TOKEN,
                        ],
                        'disable' => [
                            'class' => 'disable',
                            'icon' => 'system/themes/flexible/icons/delete.svg',
                            // 'label' => &$GLOBALS['TL_LANG']['WEMSG']['REMINDERMANAGER']['LIST']['actionDisable'],
                            'title' => &$GLOBALS['TL_LANG']['WEMSG']['REMINDERMANAGER']['LIST']['actionDisableTitle'],
                            'data' => [
                                'ptable' => ContentModel::getTable(),
                                'pid' => $objItem->id,
                            ],
                            // 'href' => System::getContainer()->getParameter('contao.backend.route_prefix').'?do=article&table='.ContentModel::getTable().'&act=edit&id='.$objItem->id.'&rt='.REQUEST_TOKEN,
                        ],
                    ],
                ];
            }
        }

        return $arrItems;
    }

    protected function getArticles(): array
    {
        $arrItems = [];

        $contents = ArticleModel::findBy(['update_reminder = ?', 'update_reminder_date <= ?'], [1, time()], ['order' => 'update_reminder_date DESC']);
        if ($contents) {
            while ($contents->next()) {
                $objItem = $contents->current();
                $obsoleteSinceText = $this->calculateObsoleteSinceText((int) $objItem->update_reminder_date);
                $obsoleteSinceText = \strlen($obsoleteSinceText) > 0 ? $obsoleteSinceText : $GLOBALS['TL_LANG']['WEMSG']['REMINDERMANAGER']['LIST']['obsoleteSinceTextLessThanAMinute'];
                $arrItems[] = [
                    'ptable' => ArticleModel::getTable(),
                    'pid' => $objItem->id,
                    'label' => $objItem->title,
                    'last_update' => $objItem->tstamp,
                    'obsolete_since' => $objItem->update_reminder_date,
                    'obsolete_since_text' => $obsoleteSinceText,
                    'period' => $objItem->update_reminder_period,
                    'actions' => [
                        'edit' => [
                            'class' => 'edit',
                            'icon' => 'system/themes/flexible/icons/edit.svg',
                            // 'label' => &$GLOBALS['TL_LANG']['WEMSG']['REMINDERMANAGER']['LIST']['actionEdit'],
                            'title' => &$GLOBALS['TL_LANG']['WEMSG']['REMINDERMANAGER']['LIST']['actionEditTitle'],
                            'href' => System::getContainer()->getParameter('contao.backend.route_prefix').'?do=article&act=edit&id='.$objItem->id.'&rt='.$this->contaoCsrfTokenManager->getDefaultTokenValue(),
                        ],
                        'reset' => [
                            'class' => 'reset',
                            'icon' => 'system/themes/flexible/icons/sync.svg',
                            // 'label' => &$GLOBALS['TL_LANG']['WEMSG']['REMINDERMANAGER']['LIST']['actionReset'],
                            'title' => &$GLOBALS['TL_LANG']['WEMSG']['REMINDERMANAGER']['LIST']['actionResetTitle'],
                            'data' => [
                                'ptable' => ArticleModel::getTable(),
                                'pid' => $objItem->id,
                            ],
                        ],
                        'disable' => [
                            'class' => 'disable',
                            'icon' => 'system/themes/flexible/icons/delete.svg',
                            // 'label' => &$GLOBALS['TL_LANG']['WEMSG']['REMINDERMANAGER']['LIST']['actionDisable'],
                            'title' => &$GLOBALS['TL_LANG']['WEMSG']['REMINDERMANAGER']['LIST']['actionDisableTitle'],
                            'data' => [
                                'ptable' => ArticleModel::getTable(),
                                'pid' => $objItem->id,
                            ],
                        ],
                    ],
                ];
            }
        }

        return $arrItems;
    }

    protected function getPages(): array
    {
        $arrItems = [];

        $contents = PageModel::findBy(['update_reminder = ?', 'update_reminder_date <= ?'], [1, time()], ['order' => 'update_reminder_date DESC']);
        if ($contents) {
            while ($contents->next()) {
                $objItem = $contents->current();
                $obsoleteSinceText = $this->calculateObsoleteSinceText((int) $objItem->update_reminder_date);
                $obsoleteSinceText = \strlen($obsoleteSinceText) > 0 ? $obsoleteSinceText : $GLOBALS['TL_LANG']['WEMSG']['REMINDERMANAGER']['LIST']['obsoleteSinceTextLessThanAMinute'];
                $arrItems[] = [
                    'ptable' => PageModel::getTable(),
                    'pid' => $objItem->id,
                    'label' => $objItem->title,
                    'last_update' => $objItem->tstamp,
                    'obsolete_since' => $objItem->update_reminder_date,
                    'obsolete_since_text' => $obsoleteSinceText,
                    'period' => $objItem->update_reminder_period,
                    'actions' => [
                        'edit' => [
                            'class' => 'edit',
                            'icon' => 'system/themes/flexible/icons/edit.svg',
                            // 'label' => &$GLOBALS['TL_LANG']['WEMSG']['REMINDERMANAGER']['LIST']['actionEdit'],
                            'title' => &$GLOBALS['TL_LANG']['WEMSG']['REMINDERMANAGER']['LIST']['actionEditTitle'],
                            'href' => System::getContainer()->getParameter('contao.backend.route_prefix').'?do=page&act=edit&id='.$objItem->id.'&rt='.$this->contaoCsrfTokenManager->getDefaultTokenValue(),
                        ],
                        'reset' => [
                            'class' => 'reset',
                            'icon' => 'system/themes/flexible/icons/sync.svg',
                            // 'label' => &$GLOBALS['TL_LANG']['WEMSG']['REMINDERMANAGER']['LIST']['actionReset'],
                            'title' => &$GLOBALS['TL_LANG']['WEMSG']['REMINDERMANAGER']['LIST']['actionResetTitle'],
                            'data' => [
                                'ptable' => PageModel::getTable(),
                                'pid' => $objItem->id,
                            ],
                        ],
                        'disable' => [
                            'class' => 'disable',
                            'icon' => 'system/themes/flexible/icons/delete.svg',
                            // 'label' => &$GLOBALS['TL_LANG']['WEMSG']['REMINDERMANAGER']['LIST']['actionDisable'],
                            'title' => &$GLOBALS['TL_LANG']['WEMSG']['REMINDERMANAGER']['LIST']['actionDisableTitle'],
                            'data' => [
                                'ptable' => PageModel::getTable(),
                                'pid' => $objItem->id,
                            ],
                        ],
                    ],
                ];
            }
        }

        return $arrItems;
    }

    protected function getNews(): array
    {
        $arrItems = [];

        $contents = NewsModel::findBy(['update_reminder = ?', 'update_reminder_date <= ?'], [1, time()], ['order' => 'update_reminder_date DESC']);
        if ($contents) {
            while ($contents->next()) {
                $objItem = $contents->current();
                $obsoleteSinceText = $this->calculateObsoleteSinceText((int) $objItem->update_reminder_date);
                $obsoleteSinceText = \strlen($obsoleteSinceText) > 0 ? $obsoleteSinceText : $GLOBALS['TL_LANG']['WEMSG']['REMINDERMANAGER']['LIST']['obsoleteSinceTextLessThanAMinute'];
                $arrItems[] = [
                    'ptable' => NewsModel::getTable(),
                    'pid' => $objItem->id,
                    'label' => $objItem->headline,
                    'last_update' => $objItem->tstamp,
                    'obsolete_since' => $objItem->update_reminder_date,
                    'obsolete_since_text' => $obsoleteSinceText,
                    'period' => $objItem->update_reminder_period,
                    'actions' => [
                        'edit' => [
                            'class' => 'edit',
                            'icon' => 'system/themes/flexible/icons/edit.svg',
                            // 'label' => &$GLOBALS['TL_LANG']['WEMSG']['REMINDERMANAGER']['LIST']['actionEdit'],
                            'title' => &$GLOBALS['TL_LANG']['WEMSG']['REMINDERMANAGER']['LIST']['actionEditTitle'],
                            'href' => System::getContainer()->getParameter('contao.backend.route_prefix').'?do=news&table='.NewsModel::getTable().'&act=edit&id='.$objItem->id.'&rt='.$this->contaoCsrfTokenManager->getDefaultTokenValue(),
                        ],
                        'reset' => [
                            'class' => 'reset',
                            'icon' => 'system/themes/flexible/icons/sync.svg',
                            // 'label' => &$GLOBALS['TL_LANG']['WEMSG']['REMINDERMANAGER']['LIST']['actionReset'],
                            'title' => &$GLOBALS['TL_LANG']['WEMSG']['REMINDERMANAGER']['LIST']['actionResetTitle'],
                            'data' => [
                                'ptable' => NewsModel::getTable(),
                                'pid' => $objItem->id,
                            ],
                        ],
                        'disable' => [
                            'class' => 'disable',
                            'icon' => 'system/themes/flexible/icons/delete.svg',
                            // 'label' => &$GLOBALS['TL_LANG']['WEMSG']['REMINDERMANAGER']['LIST']['actionDisable'],
                            'title' => &$GLOBALS['TL_LANG']['WEMSG']['REMINDERMANAGER']['LIST']['actionDisableTitle'],
                            'data' => [
                                'ptable' => NewsModel::getTable(),
                                'pid' => $objItem->id,
                            ],
                        ],
                    ],
                ];
            }
        }

        return $arrItems;
    }

    protected function getFAQ(): array
    {
        $arrItems = [];

        $contents = FaqModel::findBy(['update_reminder = ?', 'update_reminder_date <= ?'], [1, time()], ['order' => 'update_reminder_date DESC']);
        if ($contents) {
            while ($contents->next()) {
                $objItem = $contents->current();
                $obsoleteSinceText = $this->calculateObsoleteSinceText((int) $objItem->update_reminder_date);
                $obsoleteSinceText = \strlen($obsoleteSinceText) > 0 ? $obsoleteSinceText : $GLOBALS['TL_LANG']['WEMSG']['REMINDERMANAGER']['LIST']['obsoleteSinceTextLessThanAMinute'];
                $arrItems[] = [
                    'ptable' => FaqModel::getTable(),
                    'pid' => $objItem->id,
                    'label' => $objItem->question,
                    'last_update' => $objItem->tstamp,
                    'obsolete_since' => $objItem->update_reminder_date,
                    'obsolete_since_text' => $obsoleteSinceText,
                    'period' => $objItem->update_reminder_period,
                    'actions' => [
                        'edit' => [
                            'class' => 'edit',
                            'icon' => 'system/themes/flexible/icons/edit.svg',
                            // 'label' => &$GLOBALS['TL_LANG']['WEMSG']['REMINDERMANAGER']['LIST']['actionEdit'],
                            'title' => &$GLOBALS['TL_LANG']['WEMSG']['REMINDERMANAGER']['LIST']['actionEditTitle'],
                            'href' => System::getContainer()->getParameter('contao.backend.route_prefix').'?do=faq&table='.FaqModel::getTable().'&act=edit&id='.$objItem->id.'&rt='.$this->contaoCsrfTokenManager->getDefaultTokenValue(),
                        ],
                        'reset' => [
                            'class' => 'reset',
                            'icon' => 'system/themes/flexible/icons/sync.svg',
                            // 'label' => &$GLOBALS['TL_LANG']['WEMSG']['REMINDERMANAGER']['LIST']['actionReset'],
                            'title' => &$GLOBALS['TL_LANG']['WEMSG']['REMINDERMANAGER']['LIST']['actionResetTitle'],
                            'data' => [
                                'ptable' => FaqModel::getTable(),
                                'pid' => $objItem->id,
                            ],
                        ],
                        'disable' => [
                            'class' => 'disable',
                            'icon' => 'system/themes/flexible/icons/delete.svg',
                            // 'label' => &$GLOBALS['TL_LANG']['WEMSG']['REMINDERMANAGER']['LIST']['actionDisable'],
                            'title' => &$GLOBALS['TL_LANG']['WEMSG']['REMINDERMANAGER']['LIST']['actionDisableTitle'],
                            'data' => [
                                'ptable' => FaqModel::getTable(),
                                'pid' => $objItem->id,
                            ],
                        ],
                    ],
                ];
            }
        }

        return $arrItems;
    }

    protected function calculateObsoleteSinceText(int $timestamp): string
    {
        $dtReminder = (new DateTime())->setTimestamp($timestamp);

        return Util::formatDateInterval($dtReminder->diff($this->dtNow));
    }
}
