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

namespace WEM\SmartgearBundle\Backend\Module\FormDataManager;

use Contao\Config;
use Contao\CoreBundle\Controller\BackendController as ControllerBackendController;
use Contao\Date;
use Contao\Input;
use Contao\System;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Model\FormStorage;
use WEM\SmartgearBundle\Model\FormStorageData;
use WEM\UtilsBundle\Classes\StringUtil;

class BackendController extends ControllerBackendController
{
    protected $module;
    protected $type;
    protected $translator;
    protected $configurationManager;

    public function __construct(
        string $module,
        string $type,
        TranslatorInterface $translator,
        ConfigurationManager $configurationManager
    ) {
        $this->module = $module;
        $this->type = $type;
        $this->translator = $translator;
        $this->configurationManager = $configurationManager;
        System::loadLanguageFile(FormStorage::getTable());
        System::loadLanguageFile(FormStorageData::getTable());
    }

    public function exportSingle(): void
    {
        $rows = FormStorage::findItems(['id' => Input::get('id')], 0, 0, ['order' => 'createdAt DESC']);

        (new Response(mb_convert_encoding(StringUtil::decodeEntities($this->export($rows)), 'UTF-16LE', 'UTF-8'), Response::HTTP_OK, [
            'Content-Type' => 'text/csv; charset=utf-16le',
            'Content-Disposition' => 'attachment;filename='.sprintf(
                'contact_%s_%s_%s.csv',
                $rows->first()->getRelated('pid')->title,
                $rows->current()->getSender(),
                Date::parse(Config::get('datimFormat'), (int) $rows->first()->createdAt),
            ),
        ]
        ))->send();
        exit();
    }

    public function exportAll(): void
    {
        if (!empty(Input::get('id'))) {
            $this->exportAllFromForm();
        }
        $rows = FormStorage::findItems([], 0, 0, ['order' => 'createdAt DESC']);

        (new Response(mb_convert_encoding(StringUtil::decodeEntities($this->export($rows)), 'UTF-16LE', 'UTF-8'), Response::HTTP_OK, [
            'Content-Type' => 'text/csv; charset=utf-16le',
            'Content-Disposition' => 'attachment;filename=contacts.csv',
        ]
        ))->send();
        exit();
    }

    public function exportAllFromForm(): void
    {
        $rows = FormStorage::findItems(['pid' => Input::get('id')], 0, 0, ['order' => 'createdAt DESC']);

        (new Response(mb_convert_encoding(StringUtil::decodeEntities($this->export($rows)), 'UTF-16LE', 'UTF-8'), Response::HTTP_OK, [
            'Content-Type' => 'text/csv; charset=utf-16le',
            'Content-Disposition' => 'attachment;filename='.sprintf(
                'contacts_%s.csv',
                $rows->first()->getRelated('pid')->title
            ),
        ]
        ))->send();
        exit();
    }

    protected function export($rows): string
    {
        $headers = $this->buildHeaders($rows);
        $csvRows = $this->buildRows($rows, $headers);

        return $this->formatForCsv($headers, $csvRows);
    }

    protected function formatForCsv(array $headers, array $rows): string
    {
        $csv = [implode(';', $headers)];

        foreach ($rows as $row) {
            $csv[] = implode(';', $row);
        }

        return implode("\n", $csv);
    }

    protected function buildHeaders($rows): array
    {
        $headers = [
            'form' => $this->translator->trans('tl_sm_form_storage.pid.0', [], 'contao_default'),
            'submission_date' => $this->translator->trans('tl_sm_form_storage.createdAt.0', [], 'contao_default'),
            'status' => $this->translator->trans('tl_sm_form_storage.status.0', [], 'contao_default'),
            'sender' => $this->translator->trans('tl_sm_form_storage.sender.0', [], 'contao_default'),
            'completion_percentage' => $this->translator->trans('tl_sm_form_storage.completion_percentage.0', [], 'contao_default'),
            'delay_to_first_interaction' => $this->translator->trans('tl_sm_form_storage.delay_to_first_interaction.0', [], 'contao_default'),
            'delay_to_submission' => $this->translator->trans('tl_sm_form_storage.delay_to_submission.0', [], 'contao_default'),
        ];
        $rows->reset();
        while ($rows->next()) {
            // find datas and create appropriate columns
            $formStorageDatas = FormStorageData::findItems(['pid' => $rows->id]);
            if ($formStorageDatas) {
                while ($formStorageDatas->next()) {
                    $headers[$formStorageDatas->field_name] = $formStorageDatas->field_name;
                }
            }
        }

        return $headers;
    }

    protected function buildRows($rows, array $headers): array
    {
        $csvRows = [];
        $rows->reset();
        while ($rows->next()) {
            $csvRows[] = $this->buildRow($rows->current(), $headers);
        }

        return $csvRows;
    }

    protected function buildRow(FormStorage $objFormStorage, array $headers): array
    {
        $formStorageDatas = FormStorageData::findItems(['pid' => $objFormStorage->id]);

        $headersKeyToKeep = ['form',
            'submission_date',
            'status',
            'sender',
            'completion_percentage',
            'delay_to_first_interaction',
            'delay_to_submission',
        ];

        $headers['form'] = $objFormStorage->getRelated('pid')->title;
        $headers['submission_date'] = Date::parse(Config::get('datimFormat'), (int) $objFormStorage->createdAt);
        $headers['status'] = $this->translator->trans(sprintf('tl_sm_form_storage.status.%s', $objFormStorage->status), [], 'contao_default');
        $headers['sender'] = $objFormStorage->getSender();
        $headers['completion_percentage'] = $objFormStorage->completion_percentage;
        $headers['delay_to_first_interaction'] = $objFormStorage->delay_to_first_interaction;
        $headers['delay_to_submission'] = $objFormStorage->delay_to_submission;

        if ($formStorageDatas) {
            while ($formStorageDatas->next()) {
                $headers[$formStorageDatas->field_name] = '"'.StringUtil::decodeEntities($formStorageDatas->current()->getValueAsString()).'"';
                $headersKeyToKeep[] = $formStorageDatas->field_name;
            }
        }

        foreach ($headers as $key => $value) {
            if (!\in_array($key, $headersKeyToKeep, true)) {
                $headers[$key] = $this->translator->trans('WEMSG.FDM.EXPORT.fieldNotPresentInForm', [], 'contao_default');
            }
        }

        return $headers;
    }
}
