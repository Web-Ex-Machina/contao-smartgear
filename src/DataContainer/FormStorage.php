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

namespace WEM\SmartgearBundle\DataContainer;

use Contao\Config;
use Contao\Date;
use Contao\FormModel;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Model\FormStorageData;

class FormStorage
{
    /** @var TranslatorInterface */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function listItems(array $row): array
    {
        $objForm = FormModel::findById($row['pid']);
        $objFormStorageDataEmail = FormStorageData::findItems(['pid' => $row['id'], 'field_label' => 'Email'], 1);

        return [
            $objForm ? $objForm->title : $row['pid'],
            Date::parse(Config::get('datimFormat'), (int) $row['tstamp']),
            $this->translator->trans(sprintf('tl_sm_form_storage.status.%s', $row['status']), [], 'contao_default'),
            $objFormStorageDataEmail ? $objFormStorageDataEmail->value : 'NR',
        ];
    }
}
